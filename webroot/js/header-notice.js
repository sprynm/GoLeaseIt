$(document).ready(function () {
	$("#close-notice").click(function () {
		$("#hdr-notice").hide();
		$.cookie("noticestate", "closed", { path: "/" });
	});

	if ($.cookie("noticestate") == "closed") {
		$("#hdr-notice").hide();
	} else {
		$("#hdr-notice").css("display", "block");
	}
});
