;(function($){

	$.fn.HYQGotop = function(){
		var sel = this;
 		 sel.isActive = true;
 		 var flag = 1;
		var gt = $(".go-top");
		var footer = $(".hyq-footer-wrapper");
		var scroll_up,bottom_up;
		function A(){gt.css({"position":"fixed","margin-left": (980/2)+10+"px","bottom":bottom_up });}
		function B(){gt.css({"position":"fixed","margin-left": (980/2)+10+"px",	"bottom": "20px" });}
		B();
		$(window).scroll(function(){
                scroll_up=$(document).height()-$(window).height()-355;
			if($(window).scrollTop()>100&&$(sel).is(":hidden")){
				$(sel).fadeIn();
			}
			if($(window).scrollTop()<100&& !$(sel).is(":hidden")){
				$(sel).fadeOut();
			}
			if($(document).scrollTop()>=($(document).height()-$(window).height()-355)){

				bottom_up=$(document).scrollTop()-scroll_up+'px';
				A();
			}else{
				B();
			}
			 
		});
	}
	$(document).ready(function(){
		var gt = $("<a href='#' class=go-top></a>").click(function(){
			$('html,body').scrollTop(0);
			return false;
		});
			gt.hide();
			$("body").append(gt);
			$(gt).HYQGotop();
	});
})(jQuery);