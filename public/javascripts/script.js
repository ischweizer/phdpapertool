$(document).ready (function (){
	if ($('#sticky_navigation').length > 0) {
		$('#sticky_navigation').affix({
			offset: { top: $('#sticky_navigation').offset().top }
		});
	}	
});
