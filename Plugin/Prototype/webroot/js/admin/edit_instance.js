$(function (){
  //auto continue image regeneration
  if ($('#regenerate_link').length) {
    window.location = $('#regenerate_link').attr('href');
  }
  
 /**
  * basic slide toggling for settings that are dependent on others
  */
  
  //toggle category settings
  $('#PrototypeInstanceUseCategories').on('input change', function (){
    if ($(this).prop('checked') == true) {
      $('#CategoryOptions').slideDown();
    } else {
      $('#CategoryOptions').slideUp();
    }
  });
  
  if ($('#PrototypeInstanceUseCategories').prop('checked') == true) {
    $('#CategoryOptions').show();
  } else {
    $('#CategoryOptions').hide();
  }
  
  //paginate item summary toggle
  $('#PrototypeInstanceItemSummaryPagination').on('input change', function (){
    if ($(this).prop('checked') == true) {
      $('#ItemsPerPageSetting').slideDown();
    } else {
      $('#ItemsPerPageSetting').slideUp();
    }
  });
  
  if ($('#PrototypeInstanceItemSummaryPagination').prop('checked') == true) {
    $('#ItemsPerPageSetting').show();
  } else {
    $('#ItemsPerPageSetting').hide();
  }
  
  
  //toggle featured item settings
  $('#PrototypeInstanceUseFeaturedItems').on('input change', function (){
    if ($(this).prop('checked') == true) {
      $('#FeatureSettings').slideDown();
    } else {
      $('#FeatureSettings').slideUp();
    }
  });
  
  if ($('#PrototypeInstanceUseFeaturedItems').prop('checked') == true) {
    $('#FeatureSettings').show();
  } else {
    $('#FeatureSettings').hide();
  }
  
});