	//$(document).on("hyq/template/all/load",function(){//发布的时候把这行注释掉
 
                      
	//init round button with dropdown menu
	$('.hyq-round-button-with-dropdown').each(function(i,item){
 		$(item).HYQDropdownButton();
 	});

	//初始化Placeholder
	$('input,textarea').placeholder();
		
	//初始化自定义下拉列表控件
	$.each($('.hyq-dropdown-box'),function(i,item){
		$(item).HYQDropdownList();
	});
 	
 	//初始化自定义多选控件
 	$('input[type="checkbox"]').each(function(i,itm){
    //console.log(i)
 		$(itm).HYQCheckbox();
 	});

 	//初始化漂亮的多选控件
 	$("input[class^='hyq-nice-selectable']").each(function(i,itm){
 		$(itm).HYQNiceRadio();
 	});
  	
  	
  		//初始化弹出窗口的按钮
      // $(".hyq-modal").each(function(){})
	$(".hyq-modal").each(function(i,item){
      $(item).HYQModal();
  });
       
	 
  		 
       
	var modals = $("[data-toggle=hyq-modal]");
  	modals.each(function(i,item){
  		 
  		var id = $(item).attr("data-target");
  		if($(id)){
  			$(item).click(function(){
  				  $(id).trigger({type:'hyq/modal/show',id:id});
  			});	
  		}
  		
  	}); 

      var slidedowns = $("[data-toggle=hyq-slidedown-content]");
    slidedowns.each(function(i,item){
       
      var id = $(item).attr("data-target");
       
      if($(id)){
        
        $(item).click(function(){
            $(id).slideDown();
            if($(item).attr('data-trigger-action')=="hide"){
              $(item).hide();
            }
        }); 
      }
      
    }); 
	 
   var galleryUploders = $(".album-uploader")
  	
  


	//});//发布的时候把这行注释掉
 

 	
