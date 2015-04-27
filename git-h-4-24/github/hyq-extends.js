;(function($){
$.fn.extend({
	bringToFront:function(){
		var $s = $(this);
		 $s.css({"position":"absolute","z-index":$.hyqZindex($s.parent())});
		 return this;
	},
  	centerInBox:function(){
  		var $s = $(this);
		$s.css({"position":"absolute"});
		var newl = Math.round(($s.parent().width()-$s.width())/2);
		var newt = Math.round(($s.parent().height()-$s.height())/2); 
		 
		$s.css({
			"left":newl+"px",
			"top":newt+"px"
		})
		 
		return this;

	},

  	scalingInBox:function(zoom){
  		var $s = $(this);
  		var p = $s.parent();

  		var oW = $s.width(),oH = $s.height();
  		var oL = $s.position().left,oT = $s.position().top;
  		$s.width(oW*zoom);$s.height(oH*zoom);
  		$s.css({"left":oL*zoom+"px","top":oL*zoom+"px"});
  		return this;
  } 
});
})(jQuery);