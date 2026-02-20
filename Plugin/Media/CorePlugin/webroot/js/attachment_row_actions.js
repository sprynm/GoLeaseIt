$(document).ready( function() {
	$('.attachmentDelete').click(function(event) {
		var basename = $('#' + event.currentTarget.id).closest('a').text();
		console.log(basename);
    
    if (!$("#deleteConfirm").length) {
      $('body').append($('<div style="display:none;" id="deleteConfirm" title="Delete this item?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>This item will be permanently deleted and cannot be recovered. Are you sure?</p></div>'));
    }
    
		$( "#deleteConfirm" ).dialog({
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
						deleteImage(event);
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});
	});
});

function deleteImage(event) {
	var picId = event.currentTarget.id.split('_');
	
	$.ajax({
		url: '/admin/media/attachments/delete/' + picId[1],
		success: function(data)  {
			$('#' + event.currentTarget.id).closest('table').parent().prepend(data);
			$('#' + event.currentTarget.id).closest('tr').remove();
		}
	}); 
}



