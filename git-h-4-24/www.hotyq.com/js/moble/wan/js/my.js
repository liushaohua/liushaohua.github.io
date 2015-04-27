var my={
	    //css3动画时间递增函数
        diz:function  (objs,startTime,dizTime) {
			$(objs).each(function  (index,obj) {
				obj.style.webkitAnimationDelay=startTime+index*dizTime+'s';
			})
        },
         touchup:function (obj,fun){
				var touchY,startY
		   obj.addEventListener('touchstart',function  (event) {
                touchY=0;
    //阻止网页默认动作（即网页滚动）
    //event.preventDefault();
    var touch = event.touches[0];
    startY = touch.pageY;
		   },false);  
		   obj.addEventListener('touchmove',function  (event) {
			   //阻止网页默认动作（即网页滚动）
    event.preventDefault();
    var touch = event.touches[0];
    touchY=touch.pageY-startY;
   //document.title=touchY
		   },false);  
		   obj.addEventListener('touchend',function  (event) {
			    event.preventDefault();

    var touch = event.touches[0];
	         if (touchY<-$(window).height()/6) {
				 fun()
	         }
		   },false);  
		
		},
		//obj是对象单个
		touchstart:function(obj,calls){
		    obj.addEventListener('touchstart',function  (event) {
			     event.preventDefault();
				 var touch = event.touches[0];
				 calls();
			},false)
		},
				touchdw:function (obj,fun){
				var touchY,startY
		   obj.addEventListener('touchstart',function  (event) {
                touchY=0;
    //阻止网页默认动作（即网页滚动）
    //event.preventDefault();
    var touch = event.touches[0];
    startY = touch.pageY;
		   },false);  
		   obj.addEventListener('touchmove',function  (event) {
			   //阻止网页默认动作（即网页滚动）
    event.preventDefault();
    var touch = event.touches[0];
    touchY=touch.pageY-startY;
   //document.title=touchY
		   },false);  
		   obj.addEventListener('touchend',function  (event) {
			    event.preventDefault();

    var touch = event.touches[0];
	         if (touchY>$(window).height()/6) {
				 fun()
	         }
		   },false);  
		
		},
		//obj是对象单个
		
		touchstart:function(obj,calls){
		    obj.addEventListener('touchstart',function  (event) {
			     //event.preventDefault();
				 var touch = event.touches[0];
				 calls();
			},false)
		},
		touchend:function(obj,calls){
		    obj.addEventListener('touchend',function  (event) {
			     //event.preventDefault();
				 var touch = event.touches[0];
				 calls();
			},false)
		}
}
