$(document).ready(function () {
	// Form error handling
	$("input, textarea").blur(function () {
		if ($(this).is(":valid")) {
			$(this).removeClass("invalid");
			$(this).siblings(".error-message").slideUp();
		} else if ($(this).is(":invalid")) {
			$(this).addClass("invalid");
			$(this).siblings(".error-message").slideDown();
		}
	});

	$(".error-message").siblings("input").addClass("invalid");
	$(".error-message").siblings("textarea").addClass("invalid");

	$(".radios.required input[type=radio]").change(function () {
		$(this).closest("fieldset").siblings(".error-message").slideUp();
	});

	$(".checkboxes.required input[type=checkbox]").change(function () {
		checked = $(this).closest(".checkboxes").find("input:checked").length;
		if (checked > 0) {
			$(this).parent("div").siblings(".error-message").slideUp();
		} else {
			$(this).parent("div").siblings(".error-message").slideDown();
		}
	});

	//handling email form custom recipients with specified fields
	$("form").on(
		"input change",
		'.input [name="data[EmailFormSubmission][recipient]"]',
		function () {
			var $el = $(this);
			var formId = $el
				.closest("form")
				.find('input[name="data[EmailFormSubmission][email_form_id]"]')
				.val();
			var checkedItem = $el.find(":selected, :checked").first().val();
			if (
				window["EMAIL_FORM_" + formId + "_RECIPIENT_FIELDS"] &&
				window["EMAIL_FORM_" + formId + "_RECIPIENT_FIELDS"][checkedItem]
			) {
				var displayedFields =
					window["EMAIL_FORM_" + formId + "_RECIPIENT_FIELDS"][checkedItem];

				//hide all inputs
				$el
					.closest("form")
					.find('[name^="data[EmailFormSubmission]["]')
					.parent(".input")
					.hide();

				for (var i = 0; i < displayedFields.length; i++) {
					//show the input with this name
					$el
						.closest("form")
						.find(
							'[name^="data[EmailFormSubmission][' + displayedFields[i] + ']"]'
						)
						.parent(".input")
						.show();
				}

				//show the recipient box
				$el.parent().show();
			} else {
				//show all inputs
				$el
					.closest("form")
					.find('[name^="data[EmailFormSubmission]["]')
					.parent(".input")
					.show();
			}
		}
	);

	$('form .input [name="data[EmailFormSubmission][recipient]"]').change();
});
