(function () {
  "use strict";

  function isEmailLikeField(field) {
    if (!(field instanceof HTMLInputElement)) return false;
    return field.type === "email";
  }

  function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value || "").trim());
  }

  function getInvalidMessage(field) {
    var configured = field.getAttribute("data-invalid-message");
    var validity = field.validity || {};

    if ((validity.typeMismatch || validity.patternMismatch) && isEmailLikeField(field)) {
      return "Please enter a valid email address.";
    }

    if (field.validationMessage && field.validationMessage.trim() !== "") {
      return field.validationMessage.trim();
    }

    if (configured && configured.trim() !== "") {
      return configured.trim();
    }
    return "Please check this field.";
  }

  function ensureErrorNode(wrapper) {
    if (!wrapper) return null;
    var error = wrapper.querySelector(".error-message");
    if (error) return error;

    error = document.createElement("div");
    error.className = "error-message";
    error.style.display = "none";
    wrapper.appendChild(error);
    return error;
  }

  function applyDynamicValidity(field) {
    if (!(field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement)) return;

    if (field instanceof HTMLInputElement && isEmailLikeField(field)) {
      var value = String(field.value || "").trim();
      if (value !== "" && !isValidEmail(value)) {
        field.setCustomValidity("Please enter a valid email address.");
      } else {
        field.setCustomValidity("");
      }
      return;
    }

    if (field instanceof HTMLInputElement) {
      field.setCustomValidity("");
    }
  }

  function closestInputWrapper(el) {
    return el ? el.closest(".input") : null;
  }

  function setErrorVisible(input, isVisible) {
    if (!input) return;
    var wrapper = closestInputWrapper(input);
    if (!wrapper) return;

    var error = ensureErrorNode(wrapper);
    if (!error) return;

    if (isVisible) {
      error.textContent = getInvalidMessage(input);
    }

    error.style.display = isVisible ? "block" : "none";
  }

  function markInitialInvalidFields() {
    document.querySelectorAll(".error-message").forEach(function (error) {
      var wrapper = error.parentElement;
      if (!wrapper) return;

      var input = wrapper.querySelector("input, textarea");
      if (!input) return;

      input.classList.add("invalid");
    });
  }

  function handleFieldBlur(event) {
    var field = event.target;
    if (!(field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement)) {
      return;
    }

    applyDynamicValidity(field);

    if (field.checkValidity()) {
      field.classList.remove("invalid");
      setErrorVisible(field, false);
    } else {
      field.classList.add("invalid");
      setErrorVisible(field, true);
    }
  }

  function handleRequiredRadioChange(event) {
    var field = event.target;
    if (!(field instanceof HTMLInputElement) || field.type !== "radio") {
      return;
    }
    if (!field.matches(".radios.required input[type=radio]")) {
      return;
    }

    var fieldset = field.closest("fieldset");
    if (!fieldset || !fieldset.parentElement) return;

    var error = fieldset.parentElement.querySelector(".error-message");
    if (error) {
      error.style.display = "none";
    }
  }

  function handleRequiredCheckboxChange(event) {
    var field = event.target;
    if (!(field instanceof HTMLInputElement) || field.type !== "checkbox") {
      return;
    }
    if (!field.matches(".checkboxes.required input[type=checkbox]")) {
      return;
    }

    var checkboxes = field.closest(".checkboxes");
    if (!checkboxes) return;

    var checkedCount = checkboxes.querySelectorAll("input:checked").length;
    var container = field.parentElement;
    if (!container || !container.parentElement) return;

    var error = container.parentElement.querySelector(".error-message");
    if (error) {
      error.style.display = checkedCount > 0 ? "none" : "block";
    }
  }

  function toggleRecipientFields(recipientField) {
    if (!recipientField) return;

    var form = recipientField.closest("form");
    if (!form) return;

    var idField = form.querySelector('input[name="data[EmailFormSubmission][email_form_id]"]');
    var formId = idField ? idField.value : "";
    var selected =
      recipientField.querySelector(":checked") || recipientField.querySelector("option:checked");
    var selectedValue = selected ? selected.value : "";
    var mapName = "EMAIL_FORM_" + formId + "_RECIPIENT_FIELDS";
    var fieldMap = window[mapName];

    var emailFields = form.querySelectorAll('[name^="data[EmailFormSubmission]["]');

    if (fieldMap && fieldMap[selectedValue]) {
      var displayedFields = fieldMap[selectedValue];

      emailFields.forEach(function (input) {
        var wrapper = closestInputWrapper(input);
        if (wrapper) wrapper.style.display = "none";
      });

      displayedFields.forEach(function (key) {
        var selector = '[name^="data[EmailFormSubmission][' + key + ']"]';
        form.querySelectorAll(selector).forEach(function (input) {
          var wrapper = closestInputWrapper(input);
          if (wrapper) wrapper.style.display = "";
        });
      });

      var recipientWrapper = closestInputWrapper(recipientField);
      if (recipientWrapper) recipientWrapper.style.display = "";
    } else {
      emailFields.forEach(function (input) {
        var wrapper = closestInputWrapper(input);
        if (wrapper) wrapper.style.display = "";
      });
    }
  }

  function bindRecipientHandlers() {
    document.addEventListener("input", function (event) {
      var el = event.target;
      if (!(el instanceof HTMLInputElement || el instanceof HTMLSelectElement)) return;
      if (!el.matches('.input [name="data[EmailFormSubmission][recipient]"]')) return;
      toggleRecipientFields(el);
    });

    document.addEventListener("change", function (event) {
      var el = event.target;
      if (!(el instanceof HTMLInputElement || el instanceof HTMLSelectElement)) return;
      if (!el.matches('.input [name="data[EmailFormSubmission][recipient]"]')) return;
      toggleRecipientFields(el);
    });

    document
      .querySelectorAll('form .input [name="data[EmailFormSubmission][recipient]"]')
      .forEach(function (recipientField) {
        toggleRecipientFields(recipientField);
      });
  }

  function validateEmailFormSubmit(event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (!form.querySelector('[name^="data[EmailFormSubmission]["]')) return;

    var fields = form.querySelectorAll("input, textarea, select");
    var firstInvalid = null;

    fields.forEach(function (field) {
      if (!(field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement)) return;
      if (field.disabled || field.closest('[style*="display: none"]')) return;

      applyDynamicValidity(field);

      if (field.checkValidity()) {
        field.classList.remove("invalid");
        setErrorVisible(field, false);
      } else {
        field.classList.add("invalid");
        setErrorVisible(field, true);
        if (!firstInvalid) firstInvalid = field;
      }
    });

    if (firstInvalid) {
      event.preventDefault();
      firstInvalid.focus();
    }
  }

  function bindLiveValidation() {
    document.addEventListener("input", function (event) {
      var field = event.target;
      if (!(field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement)) return;

      applyDynamicValidity(field);
      if (field.checkValidity()) {
        field.classList.remove("invalid");
        setErrorVisible(field, false);
      }
    });
  }

  function init() {
    document.addEventListener("blur", handleFieldBlur, true);
    document.addEventListener("change", handleRequiredRadioChange);
    document.addEventListener("change", handleRequiredCheckboxChange);
    document.addEventListener("submit", validateEmailFormSubmit, true);

    markInitialInvalidFields();
    bindRecipientHandlers();
    bindLiveValidation();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
