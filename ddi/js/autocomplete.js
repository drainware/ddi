$(document).ready(function() {

  $.getJSON('?module=group&action=getGroups', function(data) {
     var groups = data;
     $("#group_input").autocomplete({
		  	source: groups
	   });
  });

		
});