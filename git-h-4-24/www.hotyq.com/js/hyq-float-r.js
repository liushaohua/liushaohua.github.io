;$(document).ready(function(){
		function resizes(){
			if($(window).width()<1200){
				 $(".qrcode-toggle").stop().animate({right:'-122px'});
				 $(".arrow-toggle").fadeIn();
				 }else{
					 $(".qrcode-toggle").stop().animate({right:'10px'});
					 $(".arrow-toggle").fadeOut();
			}
		 };
		 resizes();
		 $(window).resize(resizes);
		 var numss=0;
		 $(".arrow-toggle-l,.arrow-toggle-r").click(function(){
			numss++;
			if(numss%2){
				$(".qrcode-toggle").stop().animate({right:'10px'});
				$(".arrow-toggle-l,.arrow-toggle-r").fadeToggle();
				}else
				{
				  $(".qrcode-toggle").stop().animate({right:'-122px'});
				  $(".arrow-toggle-l,.arrow-toggle-r").fadeToggle();
			 }			
		 })
	});