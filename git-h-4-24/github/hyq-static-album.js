;(function($){
	$.fn.HYQStaticAlbum = function(params){
		var s = this,$s = $(this);
    var pre = $s.find(".go-prev");
    var next = $s.find(".go-next");

    
    $(".photo-wrapper").mouseover();
    $(".photo-wrapper").mouseover();
    $s.find(".thumb-item img").hide();
      var busyFlag = false;
        
        //显示大图
        var showBigImage = function(src){
          $s.find(".photo-wrapper img").remove();
          //在载入
          var img = $("<img>").attr({
            "src":src}).hide();
          $(".photo-wrapper").append(img);

          //根据图片载入情况来操作
          img.each(function(){

            if(this.complete) $(this).load();
             
          });

          
          img.load(function(){
            $(img).centerInBox();
            $(img).fadeIn();
            //busyFlag = false;

            $(".photo-wrapper").mouseenter(function(){
                pre.show();
                next.show();
            }).mouseleave(function(){
                pre.hide();
                next.hide();
            });    
            pre.mouseenter(function(){$(".photo-wrapper").mouseenter();});
            next.mouseenter(function(){$(".photo-wrapper").mouseenter();});
           // $(img).draggable();
          });

        }
 

        pre.click(function(event){
           var activeItem = $s.find(".thumb-item.selected");
           console.log("+++++++++++++++++++");
           console.log(activeItem)
           if(activeItem.length==0) return;
           console.log(activeItem.prev());
           if(activeItem.prev().length>0){
              //转到前一个图
              activeItem.removeClass("selected");
              activeItem.prev().addClass("selected");
              showBigImage(activeItem.prev().find("img").attr('bigsrc'));

           }else{
            //找到最后一个图
              var items = $s.find(".thumb-item");
              var lastItem=items[items.length-1];
              var lastImg=$(lastItem).find("img").attr("bigsrc");
              activeItem.removeClass('selected');
              $(lastItem).addClass("selected");
              showBigImage(lastImg);

           }
        });
        next.click(function(event){
           var activeItem = $s.find(".thumb-item.selected");
           if(activeItem.length==0) return;
           if(activeItem.next().hasClass("thumb-item")){
               
              activeItem.removeClass("selected");
              activeItem.next().addClass("selected");
              showBigImage(activeItem.next().find("img").attr('bigsrc'));
           }else{
            //找到最后一个图
              var items = $s.find(".thumb-item");
              var firstItem=items[0];
              var firstImg=$(firstItem).find("img").attr("bigsrc");
              activeItem.removeClass('selected');
              $(firstItem).addClass("selected");
              showBigImage(firstImg);

           }
        });

        if($s.find(".thumb-wrapper .thumb-item").length>0){
            var ibox = $($s.find(".thumb-wrapper .thumb-item")[0]).find(".image-box");
            if(ibox.length>0){
              ibox.parent().addClass("selected"); 
              showBigImage(ibox.find('img').attr('bigsrc'));
            }
        }
        $s.find(".thumb-item").click(function(){
          $s.find(".thumb-item").removeClass("selected");

          if($(this).find('img').length<0) return;
           var bigSrc = $(this).find("img").attr("bigsrc");
           $(this).addClass("selected");
           showBigImage(bigSrc);
        });

        $s.find(".thumb-item img").each(function(i,e){
            if(this.complete){
               $(e).centerInBox();
               $(e).fadeIn();
            }
        });
      
        $s.find(".thumb-item img").show();
        console.log("static ========================= ");
        console.log(pre);
        console.log(next);
        pre.hide();
        next.hide();
        // pre.css({
        //   "left":"9px"
        // }); 

        // next.css({
        //   "right":"9px"
        // });

        return  $.extend($(this),params);
	};

	$(document).ready(function(){
		var ab = $(".hyq-album-static-viewport");
		if(ab.length>0){
			$.each(ab,function(i,e){
				$(e).HYQStaticAlbum();
			});	
		}

	});

}(jQuery));
