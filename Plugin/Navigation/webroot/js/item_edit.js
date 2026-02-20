$(document).ready(function() {
	$(".nav-tabs").tabs();

	$('ul.nav-link-list li a').click(function(event) {
		event.preventDefault();
		var id = $(this).attr('id');
		var info = id.split('-');
		$('#NavigationMenuItemForeignModel').attr('value', info[0]);
		$('#NavigationMenuItemForeignKey').attr('value', info[1]);
		$('#NavigationMenuItemForeignPlugin').attr('value', info[2]);
		$('#NavigationMenuItemUrl').attr('value', null);
		$('#NavigationMenuItemCustomLink').attr('value', null);
		$('#links-to-text').html($(this).attr('title'));
		

	}); 
	
	$('#NavigationMenuItemCustomLink').keyup(function(event) {
		event.preventDefault();
		$('#NavigationMenuItemUrl').attr('value', $('#NavigationMenuItemCustomLink').attr('value'));
		$('#NavigationMenuItemForeignModel').attr('value', null);
		$('#NavigationMenuItemForeignKey').attr('value', null);
		$('#NavigationMenuItemForeignPlugin').attr('value', null);
		$('#links-to-text').html($('#NavigationMenuItemCustomLink').attr('value'));
		
	});
	
	var model = $('#NavigationMenuItemForeignModel').attr('value');
	var key = $('#NavigationMenuItemForeignKey').attr('value');
	var plugin = $('#NavigationMenuItemForeignPlugin').attr('value');
	if (model && key && plugin) { 
		var id = '#' + model + '-' + key + '-' + plugin;
		var existing = $(id);
		var title = existing.attr('title');
		$('#links-to-text').html(title);
	} else {
		var url = $('#NavigationMenuItemUrl').attr('value');
		if (url) {
			$('#links-to-text').html(url);
			$('#NavigationMenuItemCustomLink').attr('value', url);			  
		}
	}
});