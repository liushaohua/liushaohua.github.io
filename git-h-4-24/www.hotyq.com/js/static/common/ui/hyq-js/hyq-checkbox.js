;(function($){
	$.fn.HYQCheckbox = function(params){

	var s = this;  
	var $s = $(s);

	s.cbox = s.next();
	$s.on("disable",function(event){
			$s.prop("disabled",true);	
	});
	$s.on("enable",function(event){
			$s.prop("disabled",false);	
	});


	s.cbox.click(function(event){
	 	//不可用不触发点击事件
		if(s.prop('disabled')==true) {
			 
			event.stopPropagation();
			return;
		}

		if(s.prop("checked")==true){
			s.prop("checked",false);

		}else{
			s.prop("checked",true);
			 
		}
		$s.trigger({type:"onchange",text:$s.next().next().html(),value:$s.val()});
		$s.trigger({type:"hyq/checkbox/click",hyqCheckbox:s});

		event.stopPropagation();
	});

	return  $.extend($(this),params);
}
}(jQuery));