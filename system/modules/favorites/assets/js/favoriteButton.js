$(document).ready(function() {
	$('.flagfavorite').bind('click',function(e) {
		var link=$(this); 
		$.get($(this).attr('href'),{},function(result) {  
			link.after($(result));
			link.remove(); 
		});
		e.preventDefault();
		e.stopImmediatePropagation();
		return false;	
	});
});


