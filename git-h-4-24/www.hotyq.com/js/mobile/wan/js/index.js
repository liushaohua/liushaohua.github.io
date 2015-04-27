$(function(){
  document.addEventListener('touchmove',function(event){
	            event.preventDefault();
				},false);
				my.diz('.hot-people img',5,0.2)
					my.diz('.zhao-people img',12,0.3)
					my.diz('.pagesix span',1,0.3);
				var numns=$('.page').length;
				var numns,ts,tt;
				
  $('.page').each(function  (index,obj) {
	  obj.num=index;
	  my.touchup(obj,function(){
		  //alert(obj.num)
		  $('.page').css({zIndex:2})
	      $(obj).siblings('li').hide();
		  if (obj.num>=numns-1) {
			  numms=0;
			 // alert(numns)
		  }else{
		      numms=obj.num+1;
			   //alert(numms)
		  };
		  if (numms==0) {
			  $('.pageones').hide();
		  }
		  //var nunn=this.index
		  ts=setTimeout(function (){
			  $('.page').eq(numms).show().css({zIndex:10});
		  },60);
		   clearTimeout(tt)
		  
	  });
	  my.touchdw(obj,function(){
		  //alert(obj.num)
		  $('.page').css({zIndex:2})
	      $(obj).siblings('li').hide();
		  if (obj.num<=0) {
			  numms=numns-1;
			  //alert(numns)
		  }else{
		      numms=obj.num-1;
			   //alert(numms)
		  }
		  if (numms==0) {
			  $('.pageones').hide();
		  }
		  //var nunn=this.index
		tt=setTimeout(function (){
			  $('.page').eq(numms).show().css({zIndex:10});
		  },60);
		  clearTimeout(ts)
	  })
  })
  $('.pages').find('*').each(function  (index1,obj1) {
			  $(obj1).addClass($(obj1).attr('cl'))
  })

  my.touchstart($('.divs-one').get(0),function  () {
      $('.pageones').show();
  })
 // pagesix
	  my.touchend($('.pagesix a').get(0),function(){
         window.location.href='http://www.hotyq.com/'
		//alert($(this).attr('href'))
   })
	   


   //loading页面
  $('audio')[0].pause();
      var numt=0;
	  var img_le=$('img').length;
	for (var t=0; t<$('img').length; t++) {
		var imge=new Image();
		imge.src=$('img')[t].src;
		imge.onload=function  () {
			numt++;
			$('#loading i').html(parseInt(100*numt/img_le)+'%')
			if (numt>=($('img').length-2)) {
				$('#loading').addClass('dizhi-go');
				$('audio')[0].play();
				$('.page').eq(0).show()
					
			}
		}
	}
   //loading页面


   //music
       var nn=0;
	  var flaa=true;
$('#yin img')[0].addEventListener('touchstart',touchStarty,false);
function touchStarty () {
	flaa=false;
	event.preventDefault();
	nn++;
	if (nn%2) {
		$('#yin img')[0].style.webkitTransform='translateX(-30px)';
		$('audio')[0].pause();
		saa=0;

	}else {
		$('#yin img')[0].style.webkitTransform='translateX(0px)';
		$('audio')[0].play();
	}
	
	
   
}
document.body.addEventListener('touchmove', function (event) {
	if (flaa) {
	  flaa=false;
    $('audio')[0].play();
	}
}, false);
   //music
})