// Handle the "Add Another Image" link and load another file form field.
$(document).ready(function() {
	$("a.add_new_photo").click(function() {
	    //var assocAlias = $(this).attr("rel");
	    var rel = $(this).attr("rel").split("|");
	    var assocAlias = rel[0];
	    var model = rel[1];
	    var group = rel[2];
        var table = $(this).parent().children("table");
        var trCount = table.children("tbody").children("tr").length;
        var divCount = $(this).parent().children("div.new").length;
        var count = trCount + divCount;
        var url = '<?php echo Router::url(array('admin' => true, 'plugin' => 'media', 'controller' => 'attachments', 'action' => 'add_file')); ?>/count:'+count+'/model:'+model+'/assocAlias:'+assocAlias+'/group:'+group;
		
		var ajax_load = '<img id="load_image" src="/media/img/loading.gif" alt="Loading..." title="Loading..." />';

		var last = $(this).parent().children("div.new").last();
		$(last).after(ajax_load);

		$.get(
			url,
			null,
			function(responseText) {
				$("#load_image").replaceWith(responseText);
			},
			"html"
		);
		return false;
	});
	
});