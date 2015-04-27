//加载公用模块
require('../global/global.js');
//初始化header
require('../../common/header/nav_search/nav_search.js');
require('../../common/header/user/user.js');
//初始化lazy
require('../../common/ui/lazyload/lazyload.js').lazyload();

$(document).ready(function(){                                                                                            
	$('.icon_img').each(function(){                                                                                        
		$(this).error(function(){                                                                                          
			$(this).attr('src',"http://icon.hotyq.com/v2/default/150x150.png");                                               
		});                                                                                                                
	});

	(function () {
		var $btnI = $('.reds-l-bot-slbtn i'),
			$btnBox = $('.reds-l-bot-slbtn');
		$('.reds-l-bot-wrap ul').each(function (i,e) {
			var $this = $(this),
				$wrap = $this.parent('.reds-l-bot-wrap'),
				ulLen = $wrap.find('ul').length;
			$wrap.width(ulLen * $this.width());
			$(e).find('li').each(function () {
				var $this = $(this);
				if (($this.index()+1) %2 == 0) {
					$this.find('a').addClass('reds-l-bot-active');
				}else {
					$this.find('a').removeClass('reds-l-bot-active');
				}
			}); 
		});
		
		$btnI.click(function () {
		  var $this = $(this),
			  $bot = $this.parents('.reds-l-bot'),
			  $botW = $bot.width(),
			  $wrap = $bot.find('.reds-l-bot-wrap'),
			  oLeft = $botW * -$this.index(); 
		  $wrap.animate({'left': oLeft + 'px'});
		  $this.addClass('reds-l-bot-slbtn-active').siblings().removeClass('reds-l-bot-slbtn-active');
		  $this.parent('.reds-l-bot-slbtn')[0].sIndex = $this.index();
		});
		
		$btnBox.each(function (i,e) {
			var $e = $(e);
			e.sIndex = 0;
			toMove($e);
		});
		
		$('.reds-l-bot').each(function (i,e) {
			var $e = $(e),
				$btn = $e.find('.reds-l-bot-slbtn');
			if ($btn[0]) {
				$e.hover(function () {
					clearInterval($btn[0].timer); 
				},function () {
					toMove($btn);
				});
			}	
		});
		
		function toMove($e) {
			var $i = $e.find('i'),
				iLen = $i.length,
				e = $e[0];
			clearInterval(e.timer);	
			e.timer = setInterval(function () {
				++e.sIndex;
				if (e.sIndex == iLen) {
					e.sIndex = 0;
				}
				$i.eq(e.sIndex).click();
			},3000);
		}
	})();
});      
