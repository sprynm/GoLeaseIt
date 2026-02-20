$(document).ready(function() {
	// Generic close button behavior
	// Links with the class "close" will close parent element.
	$('.close').click(function() {
		$(this).parent().fadeTo(400, 0, function() {
			$(this).slideUp(400);
		});
		return false;
	});

});