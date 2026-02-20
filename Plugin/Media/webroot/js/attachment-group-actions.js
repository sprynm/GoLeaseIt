var ATTACHMENT_ADDED;

$(function(){
  $('.attachment-controls.hidden').hide();
  //allow for switching between attachment layouts per group
  $(".attachment-group .view-as .grid").click(function (){
    $(this).closest(".attachment-group").find(".attachments").removeClass("list").addClass("grid");
    $(this).addClass("selected").siblings().removeClass("selected");
  });
  
  $(".attachment-group .view-as .list").click(function (){
    $(this).closest(".attachment-group").find(".attachments").removeClass("grid").addClass("list");
    $(this).addClass("selected").siblings().removeClass("selected");
  });
  
  //select all checkbox
  $(".attachment-group .select-all input").on("input change", function(){
    var $el = $(this);
    var checked = $el.is(":checked");
    var $attachmentGroup = $el.closest(".attachment-group");
    $attachmentGroup.find(".attachments .attachment").each(function (){
      $(this).find(".delete-controls input[type=checkbox]").prop("checked", checked);
    });
    
    if (!checked) {
      $attachmentGroup.find(".delete-selected").addClass("disabled");
    } else {
      $attachmentGroup.find(".delete-selected").removeClass("disabled");
    }
  });
  
  $(".attachment-group").on("change input", ".attachment .delete-controls input[type=checkbox]", function(){
    var $el = $(this);
    var $attachmentGroup = $el.closest(".attachment-group");
    var checked = $attachmentGroup.find(".attachment .delete-controls input[type=checkbox]:checked").length;
    
    //toggle state of delete selected button
    if (checked == 0) {
      $attachmentGroup.find(".delete-selected").addClass("disabled");
    } else {
      $attachmentGroup.find(".delete-selected").removeClass("disabled");
    }
    //toggle state of select all checkbox
    if (checked == $attachmentGroup.find(".attachment").length) {
      $attachmentGroup.find(".select-all input").prop("checked", true);
    } else {
      $attachmentGroup.find(".select-all input").prop("checked", false);
    }
  });
  
  var ajaxDeleteLink = <?php echo json_encode(Router::url(array('plugin'=>'media', 'controller'=>'attachments', 'action'=>'admin_ajax_delete', 'admin' => true ))); ?>;
  
  var ajaxDelete = function(ids, $attachmentGroup){
    if ($attachmentGroup.data("delete-link")) {
      ajaxDeleteLink = $attachmentGroup.data("delete-link");
    }
    //run the ajax request to delete the attachments
    $.ajax( ajaxDeleteLink, {
      'type': 'POST'
      , 'method': 'POST'
      , 'data': {
        'data': { 
          'id': ids
        }
      }
      , 'success': function(response, status, request){
        var data;
        if (response) {
          try {
            data = JSON.parse(response);
          } catch (e) {
            console.log("error parsing JSON", response);
            return false;
          }
                    
          if (data.notification) {
            //add the notification to the DOM
            var $notification = $(data.notification);
            if ($attachmentGroup.parent().closest(".input").length == 0) {
              $attachmentGroup.parent().prepend($notification);
            } else {
              $attachmentGroup.parent().closest(".input").before($notification);
            }
            
            $notification.find(".close").click(function(){
              $(this).parent().fadeTo(400, 0, function() {
                $(this).slideUp(400);
              });
              return false;
            });
          }
          
          if (data.id) {
            var ids = data.id;
            
            $(".attachment-group .attachment").each(function(){
              var $el = $(this);
              if ( ids.indexOf($el.find("input.id").val() + "") != -1 ){
                $attachments = $el.closest(".attachments");
                $el.remove();
                //recalculate attachment ranks
                $attachments.find(".attachment").each(function (key, el){
                  $(el).find(".rank-input").val(key + 1);
                  $(el).find(".rank").html(key + 1);
                });
                
                if ($attachments.find('.attachment').length == 0 ){
                  //set this attachment group to hidden and hide the controls
                  $attachments.addClass('empty').removeClass('grid').removeClass('list');
                  $attachments.siblings('.attachment-controls').addClass('hidden').hide();
                  $attachments.text("Nothing has been uploaded yet.");
                }
              }
            });
          }
        } 
      }
    });
  };
  
  //event for newly added attachment notifications
  $(".attachment-group").on("click", ".close", function (){
    $(this).parent().fadeTo(400, 0, function() {
      $(this).slideUp(400);
    });
    return false;
  });
  
  //delete selected button
  $(".attachment-group .delete-selected").on("click", function(){
    var ids = [];
    var $attachmentGroup = $(this).closest(".attachment-group");
    $(this).closest(".attachment-group").find(".attachment .delete-controls input[type=checkbox]:checked").each(function(){
      var $el = $(this).closest(".attachment");
      ids.push($el.find("input.id").val());
    });
    
    if (ids.length) {
      ajaxDelete(ids, $attachmentGroup);
    }
    
    return false;
  });
  
  $(".attachment-group .attachments").on("click", ".delete-attachment", function(){
    var id = $(this).closest(".attachment").find("input.id").val();
    var $attachmentGroup = $(this).closest(".attachment-group");
    ajaxDelete(id, $attachmentGroup);
    
    return false;
  });
  
  var recalculateRanks = function (el){
    var $el = $(el);
    if (!$el.is(".attachments")){
      $el = $el.closest(".attachments");
    }
    //recalculate attachment ranks
    $el.find(".attachment").each(function (key, el){          
      $(el).find(".rank-input").val(key + 1);
      $(el).find(".rank").html(key + 1);
    });
  };
  
  
  
  var $renameModal = $('<div>');
  $renameModal
    .append('<div class="modal-body">Rename <span class="old-name"></span> to<br><input type="text" class="new-name"><span class="extension"></span></div>');
  $('body').append($renameModal);
  
  var renameFile = function (){
    if ($renameModal.data("attachment")) {
      $renameModal.data("attachment").find("input.basename").val($renameModal.find("input").val() + "." + $renameModal.data("extension"));
    }
    $renameModal.dialog('close');
  }
  
  $renameModal.hide();
  $renameModal.dialog({
    'autoOpen':false
    , 'title': 'Rename Attachment:'
    , 'resizable': false
    , 'buttons': {
      'Rename File': renameFile
    }
  });
  
  $renameModal.find("input").keypress(function (e){
    if (e.which == 13) {
      renameFile();
      return false;
    }
  });
  
  //allow grid items/table rows to be dragged to reorder them
  var attachmentAdded = function (el) {
    var $el = $(el);
    
    //make sure this only happens once per element
    if ($el.data("tracked-load")) {
      return;
    } else {
      $el.data("tracked-load", true);
    }
    
    //if the attachment group has the empty class then we need to change that to grid and add the view controls
    if ($el.closest('.attachments').hasClass('empty')){
      $el.closest('.attachments').removeClass('empty').addClass('grid');
      $el.closest('.attachments').siblings('.attachment-controls').removeClass('hidden').show();
      $els = $el.closest('.attachments').find('.attachment');
      $el.closest('.attachments').empty().append($els);
      $el.closest('.attachments').siblings('.attachment-controls').find(".view-as .button.grid").addClass('selected');
      $el.closest('.attachments').siblings('.attachment-controls').find(".view-as .button.list").removeClass('selected');
      $el.closest('.attachments').siblings('.attachment-controls').find(".actions .select-all input").prop('checked',false);
      $el.closest('.attachments').siblings('.attachment-controls').find(".actions .delete-selected").addClass('disabled');
    }
    
    var dragging = false;
    
    $el.on("mousedown", function(e){
      dragging = true;
      $('body').addClass("dragging");
      $el.addClass("dragging");
    });
    
    $el.find("img").on('dragstart', function(e){
      e.preventDefault();
      e.stopPropagation();
      return false;
    });
    
    $el.siblings(".attachment").on("mousemove", function(e){
      if (dragging) {
        var currentRank = parseInt($el.find(".rank-input").val()) || 0;
        var $target = $(e.currentTarget);
        var targetRank = parseInt($target.find(".rank-input").val()) || 0;
        
        if (targetRank > currentRank) {
          $el.insertAfter($target);
        } else if (targetRank < currentRank) {
          $el.insertBefore($target);
        }
        
        recalculateRanks($el);
        e.preventDefault();
        e.stopPropagation();
        return false;
      }
    });
    
    $(window).on("mouseup", function(e){
      dragging = false;
      $('body').removeClass("dragging");
      $el.removeClass("dragging");
    });
    
    $el.find(".delete-controls input[type=checkbox]").change();
    
    recalculateRanks($el);
    
    if (typeof LAZY_LOADER != 'undefined' && LAZY_LOADER) {
      LAZY_LOADER.update();
    }
    
    var $attachment = $el;
    if ($attachment.find(".basename").length && $attachment.find(".basename").val() != "") {
      var $contextMenu = $('<menu type="context" id="AttachmentContextMenu'+$attachment.find('input.id').val()+'">');
      $contextMenu.append('<menuitem label="Rename Attachment" class="rename-attachment">');
      $contextMenu.find('.rename-attachment').on("click", function (e){
        e.preventDefault();
        e.stopPropagation();
        var currentFilename = $attachment.find("input.basename").val();
        var currentExtension = currentFilename.split(".").pop();
        $renameModal.data( "extension", currentExtension );
        $renameModal.data( "attachment", $attachment );
        //show a rename modal for this item
        $renameModal.find(".old-name").text($attachment.find("input.basename").val());
        $renameModal.find(".new-name").val(currentFilename.substr(0, currentFilename.length - currentExtension.length - 1));
        $renameModal.find(".extension").text("." + currentExtension);
        $renameModal.dialog('open');
        
        return false;
      });
      
      $attachment.append($contextMenu);
      $attachment.attr("contextmenu", 'AttachmentContextMenu'+$attachment.find('input.id').val());
    }
  };
  
  //set the global var so that uploadify can add the events
  ATTACHMENT_ADDED = attachmentAdded;
  
  $(".attachment-group .attachments .attachment").each(function(){
    attachmentAdded(this);
  });
});