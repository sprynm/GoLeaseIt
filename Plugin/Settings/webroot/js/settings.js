$(function(){
  //replace settingKeys with data attributes in the corresponding input
  $(".settingKey").each(function(){
    $self = $(this);
    $($self.data("for")).attr("title", $self.data("key"));
    $self.remove();
  });
});