;(function($){
	$.fn.HYQNiceRadio = function(){
		var s = this,$s = $(this);
	  
	 $s.box = $s.next();
		 var handler = function(){
		 	if($s.prop('disabled')==true) return;

			if($s.prop("type")=="checkbox"){
			 	if($s.prop("checked")){
					$s.prop("checked",false);
				}else{
					$s.prop("checked",true);
				}
			}else if(!$s.prop("checked")){
				$s.prop("checked",true);
			}
		 };

		$s.box.click(function(){
			handler();
		});

		$s.box.next().click(function(){
			handler();
		});

	}	
}(jQuery));