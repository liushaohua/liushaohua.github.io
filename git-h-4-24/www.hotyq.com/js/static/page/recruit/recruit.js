//初始化lazy 
require('../../common/ui/lazyload/lazyload.js').lazyload();

(function () {
	$('.tabsWrap').each(function (i,e) {
		var $this = $(e),
			scrollW = $this.find('.tabScroll').width(),
			$tabBox = $this.find('.tabBox'),
			boxW = $tabBox.width(),
			$tabUl = $tabBox.find('ul'),
			tabMoveW = 300,
			cUlW = 0;
			
			e.cLeft = 0;
			
		$this.find('.tabScroll li').each(function (i,e) {
			cUlW += $(e).outerWidth();
		});	
		
		$tabUl.width(cUlW + 5);

		if (cUlW > boxW) {
			$this.find('.tabArrow').show();
		}
		
		$('.tabArrow').on('click','.arrow-lr',function (ev) {
			var $e = $(this),
				bReady = false,
				$tabWrap = $e.parents('.tabsWrap'),
				$tabUl = $tabWrap.find('.tabScroll');
				
			var ev = ev || window.event;
			ev.preventDefault();
			ev.returnValue = false; 	
				
			if (bReady) { 
				return;
			}
			if(!$tabUl.is(":animated")){
				++$tabWrap[0].cLeft;	
				$e.next().attr('class','arrow-rr');
				$tabUl.animate({'left' : countLeft($tabWrap[0].cLeft,$e) + 'px'});
				$e.attr('class','arrow-l');
				bReady = true;

			}
			
		});
		
		function countLeft(iCount,$this,r) {
			var cUlW = r ? 2 : 0,
				iCount = Math.abs(iCount),
				cLi = $this.parent('.tabArrow').prev('.tabBox').find('li');	
				
			for (var i = 0,len = iCount * 2; i < len; i++) {
				cUlW += cLi.eq(i).outerWidth();
			}
			
			return cUlW;
		}
		
		$('.tabArrow').on('click','.arrow-rr',function () {
			var $e = $(this),
				bReady = false,
				$tabWrap = $e.parents('.tabsWrap'),
				$tabUl = $tabWrap.find('.tabScroll');
				
			if (bReady) { 
				return;
			}
			if(!$tabUl.is(":animated")){
				--$tabWrap[0].cLeft;
				$e.prev().attr('class','arrow-lr');
				$tabUl.animate({'left' : -countLeft($tabWrap[0].cLeft,$e,true) + 'px'});
				$e.attr('class','arrow-r')
				bReady = true;
			}
		});
	});
	
	$('.tabUnit').click(function () {
		var $this = $(this),
			index = $this.index(),
			$tabContent = $this.parents('.recruit-type');
		$this.addClass('active').siblings().removeClass('active');
		
		$tabContent.find('.hyq-tab-pane').fadeOut();
		$tabContent.find('.hyq-tab-pane').eq(index).fadeIn();
		
	});
}());
