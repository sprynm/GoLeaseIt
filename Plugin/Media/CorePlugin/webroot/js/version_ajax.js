// Handle the "Add Another Image" link and load another file form field.
$(document).ready(function() {
	$("a.add_new_version").click(function(event) {
    event.preventDefault();

    var rel = $('#'+$(this).attr("rel")).attr("rel").split("|");
    var tableId = $(this).attr("rel");
    var group = rel[0];
    var alias = rel[1];
    var model = rel[2];
    var key = rel[3];
    var table = $('#'+tableId);
    //var count = table.children("tbody").children("tr").length;
    
    var count = parseInt(table.find('tbody tr').last().find('input[name$="][id]"]').attr('name').match(/data\[[^\]]+\]\[(\d+)\]\[id\]/)[1] || 0) + 1;
    var url = '<?php echo Router::url(array('admin' => true, 'plugin' => 'media', 'controller' => 'attachment_versions', 'action' => 'add_version')); ?>/count:'+count+'/group:'+group+'/model:'+model+'/alias:'+alias;
    if (key) {
      url = url + '/foreign_key:'+key;
    }

		$.get(
			url,
			null,
			function(responseText) {
			    table.children("tbody").append(responseText);
			},
			"html"
		);
		return false;
	});
	
});