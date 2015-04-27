;(function($){
	var _HYQ_BUSY_INTERVAL = null;
	$.fn.HYQBusyAnimate = function(){
		 
	 	var s = this,$s = $(this);
	 	  s.wrapper  = $s.find('.hyq-busy-animate');
		s.busyFlag = false;
	 	var g1 = "#808080",g2 = "#ff3300";
	 	$s.css({
	 		"position":"absolute",
	 		"left":"0px",
	 		"top":"0px",
	 		"bottom":"0px",
	 		"right":"0px",
	 		"background":"#ccc"

	 	});
	 	s.isBusy = function(){
	 		return this.busyFlag;
	 	}

	 	s.wrapper .css({
	 		"width":"64px",
	 		"height":"16px",
	 		"display":"block",
	 		"position":"absolute",
	 		"left":"50%",
	 		"top":"50%",
	 		"background":"transparent",
	 		"margin-top":"-6px",
	 		"margin-left":"-32px"
	 		 
	 	});
	 	var blocks = $s.find("span").css({
	 		"float":"left",
	 		"background":g1,
	 		"width":"5px",
	 		"height":"12px",
	 		"border-radius":"2px",
	 		"margin":"2px"
	 	});

	 	s.current =0;
	  	s.intv = null;

	 	s.busying = function(){
	 		blocks.each(function(i,e){
	 			$(e).css({"background":g1});

	 		});
	 		s.current = s.current>=blocks.length?0:s.current+1;
	 		   
	 		$(blocks[s.current]).css({"background":g2});

	 	};
	 	 
	 	s._E={hide :"hyq/busyanimate/hide",show :"hyq/busyanimate/show"};

	 	s.on(s._E.hide,function(){
			s.hide();
	 	})

	 	s.on(s._E.show,function(){
	 		s.show();
	 	})
	 	s.active=function(){
	 	 
		 		_HYQ_BUSY_INTERVAL = setInterval(function(){
		 			s.busying();	 			
		 		} , 100); 		
		 		s.busyFlag = true;
		 		$s.trigger(s._E.show);

		 	 return this;
	 	}
	 	s.mute =function(){
	 		clearInterval(_HYQ_BUSY_INTERVAL);	
	 		$s.trigger(s._E.hide);
	 		s.busyFlag =false;
	 		return this;
	 	};
	 	 	 
	 	 
	 	$s.hover(function(){return false;});
	 	return this;
	}
	$(function(){
		var hba = $('.hyq-busy-animate');
		if(hba){
			$.each(hba,function(i,e){
				$(e).HYQBusyAnimate();
				
			});
		}
	});
}(jQuery));