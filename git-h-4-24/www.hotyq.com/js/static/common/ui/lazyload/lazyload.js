"use strict";
require('../../lib/jquery/jquery.js');

function lazyload(){
	var lazyImgArr = [],
		$window = $(window),
		$d = $(document),
		$clientHeight = $window.height(),
		temp = -1;
	
	function imgChange() {
		var $imgElements = $('img[data-lazy]'),
			$Scroll = $d.scrollTop();

		if (temp < $Scroll) {
			$.each($imgElements,function (i,e) { 
				var $e = $(e),
					$ImgTop = $e.offset().top;
				if($ImgTop - $Scroll <= $clientHeight) {
					$e.attr('src',$e.attr('data-lazy'));
					$e.fadeOut(0).fadeIn(500,function () {
						$e.attr('alt',$e.attr('data-title'));
						$e.attr('title',$e.attr('data-title'));
					});
					$e.removeAttr('data-lazy');
				}
			});
		}
		temp = $Scroll;
	}
	imgChange();
	$window.scroll(imgChange);
	$window.resize(imgChange);
}
exports.lazyload = lazyload;