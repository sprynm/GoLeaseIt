$(document).ready(function() {
	// Adding a new field
	
	
  $('a.add-new-field').live("click", function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		var linkClass = $(this).attr('class').split(' ');
		linkClass = linkClass[1];
		
		var wrapper = $(this).prevAll('div.custom-fields-wrapper');
		
		var count = wrapper.find('div.custom-field-form').length;
		
    $.ajax({
      type: 'POST',
      url: url,
      data: {
        "count": count,
        "alias": linkClass
      },
      success: function(data) {
        wrapper.append(data);
      }
    });
	});
	
	$('a.Option').live("click", function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		var linkClass = $(this).attr('class').split(' ');
		linkClass = linkClass[1];
		
		var wrapper = $('.field-group');
		
		var count = wrapper.find('tr').length;
		
    $.ajax({
			type: 'POST',
      url: url,
			data: {
				"count": count,
				"alias": linkClass
			},
      success: function(data) {
				wrapper.append(data);
      }
    });
	});

	// Sorting fields
	$("div.custom-fields-wrapper").sortable({
		distance: 20,
		items: 'div.custom-field-form',
		update: function(event, ui) {
			var fields = $(this).children('div.custom-field-form');
			fields.each(function(index) {
				$(this).find('div.rank-input').children('input').attr('value', index);
			});
		}
	});
  
  //disable Validate and Placeholder options for some field types
  $(".custom-fields-wrapper").on('input change', 'select[name$="[type]"]', function (){
    var $fieldContainer = $(this).closest('.custom-field-form');
    $fieldContainer.find('.image-versions-link').remove();
    if (['checkbox', 'select', 'radio', 'document', 'image', 'readonly'].indexOf($(this).val()) != -1) {
      $fieldContainer.find('[name$="[validate]"]').prop('disabled', true).closest('.input').addClass('disabled');
      $fieldContainer.find('[name$="[placeholder]"]').prop('disabled', true).closest('.input').addClass('disabled');
    } else {
      $fieldContainer.find('[name$="[validate]"]').prop('disabled', false).closest('.input').removeClass('disabled');
      $fieldContainer.find('[name$="[placeholder]"]').prop('disabled', false).closest('.input').removeClass('disabled');
    }
    //show the image versions link for this field if the type is set to image and the foreign key is 0 (for default custom fields)
    if ($(this).val() == 'image' && $fieldContainer.find('[name$="[foreign_key]"]').val()==0) {
      $(this).after('<div style="display: inline-block; margin: 0 10px;" class="image-versions-link"><a href="<?php echo Router::url(array('plugin'=>'media', 'controller'=>'attachment_versions', 'action'=>'edit', 'admin'=>true, 'CustomField')); ?>/'+$fieldContainer.find('[name$="[id]"]').val()+'">Image Versions</a></div>');
    }
  });
  
  $('.custom-fields-wrapper select[name$="[type]"]').change();
});

