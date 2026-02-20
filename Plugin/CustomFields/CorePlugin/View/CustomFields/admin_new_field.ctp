<?php 


echo $this->CustomField->adminField(array(
	'alias' => $alias,
	'field' => $field,
	'count' => $count,
	'model' => $model,
	'group' => $group
));
?>

<!-- relephant code  -->

<script type="text/javascript">
var allInputs = $(document).find('input[name$="[name]"]');
allInputs.each(function() {
	$(this).after("<label class='name-error'></label>");
});
$('.admin-submit').after('<p class="name-warning"></p>');

$('.admin-submit').on('mouseenter', function() {
	var allInputs = $(document).find('input[name$="[name]"]');
	var count = 0;

	allInputs.each(function() {
			if ($(this).val() == '') {
				$(this).css('background-color', 'red');
				$(this).parent().find('.name-error').text("This field is required.");
				count ++;
            }
			
			
        });
	
	if(count > 0) {
		$(this).find('input[type="submit"]').each( function() {
            $(this).attr('disabled','disabled');
        });
		$('.name-warning').text('Name fields must be filled or new form elements won\'t save. Check other tabs for fields marked in red.');
	} else {
		$(this).find('input[type="submit"]').each( function() {
            $(this).removeAttr('disabled');
        });
		allInputs.each(function() {
			$(this).css('background-color', 'white');	
			$(this).parent().find('name-error').text('');
		});
		$('.name-warning').text('');
		$('.error').remove();
	
	}
	
});

$('input[name$="[name]"]').on('keydown', function() {
	$(this).css('background-color', 'white');	
});

</script>
<!-- relephant code ends --> 