(function () {
  "use strict";

  if (window.RHLibrary && window.RHLibrary.version) {
    return;
  }

  var CLOSE_TRIGGER_SELECTOR = '.js-close, [data-function="close"], .close';

  function resolveExplicitTarget(trigger) {
    var targetSelector = trigger.getAttribute("data-close-target");
    if (!targetSelector) return null;

    if (targetSelector === "self") {
      return trigger;
    }

    var closestMatch = trigger.closest(targetSelector);
    if (closestMatch) return closestMatch;

    return document.querySelector(targetSelector);
  }

  function resolveCloseTarget(trigger) {
    var explicitTarget = resolveExplicitTarget(trigger);
    if (explicitTarget) return explicitTarget;

    return (
      trigger.closest("[data-close-container]") ||
      trigger.closest(".notification") ||
      trigger.closest(".legal-notice") ||
      trigger.closest(".notice") ||
      trigger.closest(".message") ||
      trigger.closest(".error-message") ||
      null
    );
  }

  function closeElement(target, trigger) {
    if (!target) return false;

    target.setAttribute("hidden", "hidden");
    target.style.display = "none";
    target.setAttribute("aria-hidden", "true");

    var detail = { trigger: trigger || null };
    target.dispatchEvent(new CustomEvent("rh:closed", { bubbles: true, detail: detail }));
    return true;
  }

  function handleCloseTrigger(trigger, nativeEvent) {
    if (!trigger) return false;

    var target = resolveCloseTarget(trigger);
    if (!target) return false;

    if (nativeEvent) {
      nativeEvent.preventDefault();
    }
    return closeElement(target, trigger);
  }

  document.addEventListener("click", function (event) {
    var trigger = event.target.closest(CLOSE_TRIGGER_SELECTOR);
    if (!trigger) return;

    handleCloseTrigger(trigger, event);
  });

  window.RHLibrary = {
    version: "1.0.0",
    closeElement: closeElement,
    resolveCloseTarget: resolveCloseTarget,
    handleCloseTrigger: handleCloseTrigger,
  };
})();
