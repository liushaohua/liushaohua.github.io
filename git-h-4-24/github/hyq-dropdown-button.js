;(function($){
	$.fn.HYQDropdownButton = function(){
	
	var $this = this;
	$this.fakeClass="__XX_FAKE_CLASS__HYQ_DROP_DOWN_BUTTON__";
	$this.popMenu = null;

	$this.setMenuState = function(menuState){
		if(!menuState){
			$this.removeClass('menuActive');
		}else{
			$this.addClass('menuActive');
		}
	}

	$this.menuOnIdle = false;
	$(document).click(function(evt) {
		try{
			$this.popMenu.fadeOut(function(){
						$this.popMenu.remove();
						$this.popMenu = null;
						$this.setMenuState(false);
			});
		}catch(e){

		}
	});
	
	var autoHide = function(){
		setTimeout(function() {
			try{
				if($this.menuOnIdle && $this.popMenu.is(':hidden')==false){
					$this.popMenu.fadeOut(function(){
						$this.popMenu.remove();
						$this.popMenu = null;
						$this.setMenuState(false);
					});
				}
			}
			catch(e){

			}
		}, 2000);
	}

	//  
	
	$this.showMenu = function(){
		//如果还没有显示
			var button = $this.find('input');
			$this.popMenu = $($this.find('dl').prop('outerHTML')).addClass($this.fakeClass);
			$("body").append($this.popMenu);
			if($this.popMenu){
				$this.popMenu.mouseover(function(){
					$this.menuOnIdle = false;

				}).mouseout(function(){
					$this.menuOnIdle = true;
					autoHide();
				});
			}

 
			$this.popMenu.css({

				"left":button.offset().left+button.width()/2-$this.popMenu.width()/2+'px',
				"top":button.offset().top+button.height()+6+'px'
			});
			$this.popMenu.fadeIn(200,function(){
				$this.setMenuState(true);
			});
	};
	 
	

	$this.find('input[type=button]').click(function(){
		var tmpLayers = $("."+$this.fakeClass);
		if(tmpLayers.length>0){tmpLayers.fadeOut(200,function(){

			tmpLayers.remove();
			$this.showMenu();
			

		});}else{
			$this.showMenu();
		}
		
		return false;

	});

	$this.on("hyq.btns.menu.showed",function(){
		autoHide();
	});
};
}(jQuery));