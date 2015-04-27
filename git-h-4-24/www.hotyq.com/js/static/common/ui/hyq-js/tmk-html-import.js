;(function($){
	
	//tmk-html-import
	$.fn.TmkHtmlImport =function(order,total){
 		var s = this;
		var $s = $(this);
		s.order = order;
		s.total = total;
		s.file = $s.attr("file")+"?t="+Date.parse(new Date());
		 
		$(document).on("hyq/template/load",function(event,data){

			if(data==s.order){
				 
				$.get(s.file,function(result){
					 
					var node = $(result);
					$s.before(node);

					$(document).trigger("hyq/template/load",s.order+1);
					 if(s.order+1==s.total){
					 	$(document).trigger("hyq/template/all/load");
					 }
				});
			}
		});
		

	}

	$(document).ready(function(){
		var imports = $("tmk-html-import");
		$.each(imports,function(i,itm){
			$(itm).TmkHtmlImport(i,imports.length);
			if(i+1==imports.length){
				$(document).trigger("hyq/template/load",0);
			}

		});
	});
})(jQuery);