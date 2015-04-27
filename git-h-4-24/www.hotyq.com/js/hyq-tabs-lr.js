;$(document).ready(function () {
		$('.hyq-tab').click(function () {
			//scroll the hyq-tab to current item position
			$(this).addClass('active').siblings().removeClass('active');
			//$('.hyq-tab').stop().animate({left:$(this).position()['left']}, {duration:500});
			
			//scroll the panel to the correct content
			$('.tabPanel').stop().animate({left:$(this).position()['left'] * (-3)-4}, {duration:500});
		});
		
	});