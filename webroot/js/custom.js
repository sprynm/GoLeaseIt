var LAZY_LOADER;

$(document).ready(function () {
	//close action for notifications
	$(".close").click(function () {
		$(this)
			.parent()
			.fadeTo(400, 0, function () {
				$(this).slideUp(400);
			});
		return false;
	});

	$("#responsive-menu-button").sidr({
		name: "sidr-main",
		source: "header nav",
		side: "right",
	});

	$("#responsive-menu-button").click(function () {
		var isOpened = $(this).attr("aria-expanded") === "true";

		if (isOpened) {
			$(this).attr("aria-expanded", "false");
			$(".sidr-class-menu_level_ > li > a").removeAttr("tabindex");
		} else {
			$(this).attr("aria-expanded", "true");
			$(".sidr-class-menu_level_ > li > a").attr("tabindex", 3);
		}
	});

	$(".sidr-class-menu_level_1").parent().addClass("has-sub-nav");
	$(".sidr-class-menu_level_1").hide();

	$(".sidr-class-menu_level_2").parent().addClass("has-sub-nav");
	$(".sidr-class-menu_level_2").hide();

	$(".has-sub-nav > a").click(function () {
		$(this).next("ul").slideToggle();
		$(this).parent().toggleClass("open");
		return false;
	});
});
