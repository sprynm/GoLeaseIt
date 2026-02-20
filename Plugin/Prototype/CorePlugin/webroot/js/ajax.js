// Handle the "Add Another Image" link and load another file form field.
$(document).ready(function() {
	$("a.add_new").click(function() {
		var idString = $(this).attr("id");
		var info = idString.split("-");
		var divs = $(".new_field_"+info[0]);
		var last = divs[divs.length-1];
		var count = $(last).attr("rel");
		var url = '/admin/prototype/prototype_instances/new_field/type:'+info[0]+'/id:'+info[1]+'/count:'+count;
		$.get(
			url,
			null,
			function(responseText) {
				$('h4#add_header').before(responseText);
			},
			"html"
		);
		return false;
	});
  
  
  $("a.toggle-featured").live("click", function(e) {
    e.preventDefault();
    
    var link = $(this);
    var id = link.attr('id');
    var url = link.attr('href');
    $.ajax({
      url: url,
      success: function(data)  {
        link.replaceWith(data);
      }
    });
  });
});
