// Loads a new custom field and adds to the table.
$(function() {
	$('a.add-new-meta').on('click', function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		var table = $(this).parents('table.sortable');
		var tbody = table.find('tbody');
		var rowCount = tbody.find('tr').length;
		$.post(url, { count: rowCount }, function(data) {
			tbody.append(data);
		});
	});
});

/**
 * Counts the characters of a textarea. Doesn't actually enforce the limit - just updates
 * an element with the current number of characters left (even if negative).
 */
function countChar(val, onload) {
	if (onload) {
		var len = val.val().length;
	} else {
		var len = val.value.length;
	}
	
	var maxLength = 156;

	var current = maxLength - len;
	$('#charNum').text(maxLength - len);
	if (current < 1) {
		$('#charNum').addClass('red');
	} else {
		$('#charNum').removeClass('red');
	}
};

$(document).ready(function() {
	var descElement = $("#meta_tag-tab div.input.textarea label:contains('description')");
	descElement.append(' (<strong><span id="charNum">156</span></strong> characters left)');
	var textarea = descElement.next('textarea');
	countChar(textarea, true);
	textarea.attr('onkeyup', 'countChar(this)');
});
