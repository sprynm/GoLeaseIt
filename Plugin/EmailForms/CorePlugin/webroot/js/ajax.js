// Loads a new custom field and adds to the table.
$(document).ready(function() {
	$('a.add-new-group').live("click", function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		var fieldCount = $('table.field-group').find('tbody').find('tr.sortable_row').length;
		var groupNum = $('div.form-group').length;
		$.ajax({
			type: 'POST',
			url: url,
			data: {
				"groupNum": groupNum,
				"fieldCount": fieldCount
			},
			success: function(data)  {
				$('a.add-new-group').before(data);
			}
		});
	});

	// For group/field reordering
	$('ol.nested-sortable-0').nestedSortable({
		disableNesting: 'no-nest',
		forcePlaceholderSize: true,
		handle: 'div',
		items: 'li',
		opacity: 0.6,
		placeholder: 'placeholder rounded-corners',
		tabSize: 25,
		tolerance: 'pointer',
		toleranceElement: '> div',
		protectRoot: true,
		maxLevels: 2
	});

	$('.disclose').on('click', function() {
		$(this).closest('li').toggleClass('collapsed').toggleClass('expanded');
	});

	$('#saveOrder').click(function(e){
		e.preventDefault();
		var clean = $('ol.nested-sortable-0').nestedSortable('toArray', {startDepthCount: 0});
		var sendData = [];
		for (var i = 0; i < clean.length; i++) {
			if (!clean[i]['item_id']) {
				continue;
			}
			var id = clean[i]['item_id'];
			var parentId = clean[i]['parent_id'];
			if (parentId == null) {
				parentId = -1;
			}
			var sendString = 'data['+i+'][parent_id]='+parentId+'&data['+i+'][id]='+id;
			sendData[i] = sendString;
		}
		var formId = $('#EmailFormId').val();
		$.ajax({
			type: 'POST',
			url: "/admin/email_forms/email_forms/reorder/" + formId,
			data: sendData.join('&'),
			beforeSend: function(xhr) {
				$('ol.nested-sortable-0').addClass('sortable-working');
				$('#saveOutput').html('');
			},
			success: function(data)  {
				$('ol.nested-sortable-0').removeClass('sortable-working');
                $('#saveOutput').html("Save Successful.");
				$('#tab-fields').html(data);
			}
		});
	});

});
