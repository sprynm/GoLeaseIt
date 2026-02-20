$(function() {
	$("table.sortable").sortable({
		distance: 20,
		items: 'tr.sortable_row',
		update: function(event, ui) {
			var rows = $(this).find('tbody').children('tr.sortable_row');
			rows.each(function(index) {
				$(this).find('div.rank-input').children('input').attr('value', index);
			});
		}
	});
});