$(function(){
  var addRecipientRow = function (){
    var $recipients = $(".email-form-recipients");
    var $recipient = $('<div class="email-form-recipient">');
    var highestRank = 0;
    var highestIndex = 0;
    //determine rank and index to use for this item
    $recipients.find('.email-form-recipient').each(function (key, el){
      var thisRank = $(el).find('.rank input').val();
      
      var thisIndex = $(el).find('.rank input').attr("name").match(/data\[[^\]]+\]\[(\d+)\].*/);
      if (thisIndex.length > 0 && parseInt(thisIndex[1]) >= highestIndex ) {
        highestIndex = parseInt(thisIndex[1]) + 1;
      }
      if (thisRank >= highestRank) {
        highestRank = thisRank + 1;
      }
    });
    $recipient.append('<div class="recipient-actions" style="text-align: right;"><a href="#" class="delete button"><img src="/img/icons/cross.png"></a></div>');
    $recipient.append('<div class="rank"><input name="data[EmailFormRecipient]['+highestIndex+'][rank]" type="hidden" value="'+highestRank+'"></div>');
    var name = "data[EmailFormRecipient]["+highestIndex+"][name]";
    $recipient.append('<div class="input text"><label for="'+name+'">Option Value</label><input name="'+name+'" type="text"></div>');
    name = "data[EmailFormRecipient]["+highestIndex+"][email_address]";
    $recipient.append('<div class="input text"><label for="'+name+'">Email Address</label><input name="'+name+'" type="text"></div>');
    name = "data[EmailFormRecipient]["+highestIndex+"][subject_template]";
    $templates = $('<div class="custom-templates">');
    $templates.append("<h3 class=\"toggle\">Custom Templates</h3>");
    $templatesInner = $("<div style=\"display: none;\">");
    $templatesInner.append('<div class="input text"><label for="'+name+'">Subject Line Format</label><input name="'+name+'" type="text"></div>');
    name = "data[EmailFormRecipient]["+highestIndex+"][content_template]";
    $templatesInner.append('<div class="input text"><label for="'+name+'">Content Format</label><textarea name="'+name+'" /></div>');
    name = "data[EmailFormRecipient]["+highestIndex+"][auto_response_subject_template]";
    $templatesInner.append('<div class="input text"><label for="'+name+'">Auto Response Subject Line Format</label><input name="'+name+'" type="text"></div>');
    name = "data[EmailFormRecipient]["+highestIndex+"][auto_response_content_template]";
    $templatesInner.append('<div class="input text"><label for="'+name+'">Auto Response Content Format</label><textarea name="'+name+'" /></div>');
    $templates.append($templatesInner);
    $recipient.append($templates);
    $recipients.append($recipient);
    
  };

  $(".add-new-recipient").on('click', function (e){
    e.preventDefault();
    e.stopPropagation();
    
    addRecipientRow();
    
    return false;
  });
  
  $("#EmailFormUseRecipientList").on('change input', function(){
    if ( $(this).is(":checked") ){
      $(".email-form-recipients-container").show();
      
      if (!$(".email-form-recipients .email-form-recipient").length) {
        addRecipientRow();
      }
      
    } else {
      $(".email-form-recipients-container").hide();
    }
  });
  
  $("#EmailFormUseRecipientList").change();
  $(".email-form-recipients .custom-templates .toggle").siblings().hide();
  
  $(".email-form-recipients").on('click', '.custom-templates .toggle', function(e){
    e.preventDefault();
    e.stopPropagation();
    
    if ($(this).hasClass('open')) {
      $(this).removeClass('open');
      $(this).siblings().slideUp();
    } else {
      $(this).addClass('open');
      $(this).siblings().slideDown();
    }
    
    return false;
  });
  
  
  $(".email-form-recipients").sortable({
		distance: 20,
		items: '.email-form-recipient',
		update: function(event, ui) {
			var fields = $(".email-form-recipient");
			fields.each(function(index) {
				$(this).find('.rank').children('input').attr('value', index);
			});
		}
	});
  
  $(".email-form-recipients").on('click', '.recipient-actions .delete', function (e){
    var $recipient = $(this).closest(".email-form-recipient");
    var $id = $recipient.find(".id");
    if ($id.length) {
      var id = $id.val();
      $.ajax(<?php echo json_encode( Router::url(array('plugin'=>'email_forms', 'controller'=>'email_form_recipients', 'action'=>'delete', 'admin'=>true)) ); ?> + "/" + id, {
        success: function() {
          $recipient.remove();
        }
      });      
    } else {
      $recipient.remove();
    }
    
    e.preventDefault();
    e.stopPropagation();
    return false;
  });
  
  $("form").on('input change', '[name^="data[EmailFormRecipient]["][name$="][displayed_fields][]"]', function (){
    var $el = $(this);
    var $container = $el.closest('.input');
    console.log($el.prop('checked'), $el.val());
    if ($el.prop('checked')) {
      if ($el.val() == 'All') {
        //deselect the other boxes
        $container.find('[value!="All"]').prop('checked', false);
      } else {
        //deselect the all box
        $container.find('[value="All"]').prop('checked', false);
      }
    } else if ($container.find('input:checked').length == 0){
      $container.find('[value="All"]').prop('checked', true);
    }
  });
  
});