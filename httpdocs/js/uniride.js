/**
 **************************************************
 * UNIRIDE
 * uniride.js
 *
 * @author:Projektteam uniride.de
 * @version:1.0
 * @updated:2013-05-13
 **************************************************
**/

$(function(){
	
	/* Scrollen BEGIN */
	$('section').waypoint({ offset: 100 });
	
	var scrollElement = 'html, body';
	$('html, body').each(function () {
		var initScrollTop = $(this).attr('scrollTop');
		$(this).attr('scrollTop', initScrollTop + 1);
		if ($(this).attr('scrollTop') == initScrollTop + 1) {
			scrollElement = this.nodeName.toLowerCase();
			$(this).attr('scrollTop', initScrollTop);
			return false;
		}    
	})
	
	$("a.scrollTop").click(function(event) {
		event.preventDefault();	
		var $this = $(this),
		target = this.hash,
		$target = $(target);
		$(scrollElement).stop().animate({
			'scrollTop': $target.offset().top
		}, 500, 'swing', function() {
			//window.location.hash = target;
		});
	});
	
	

	/* Scrollen END */
	
	
});