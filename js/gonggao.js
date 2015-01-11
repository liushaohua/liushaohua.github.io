// JavaScript Document
function myAddEvent(a, b, c) {
    if (a.attachEvent) {
        a.attachEvent('on' + b, c)
    } 
	else {
        a.addEventListener(b, c, false)
    }
}
myAddEvent(window, 'load',
function() {
	var oDiv=document.getElementById('div1');
	var aDiv=document.getElementById('div2').getElementsByTagName('div');
	var i=0;
	
	var lastDivX=0;
	var lastDivY=0;
	
	var iSpeedX=0;
	var iSpeedY=0;
	
	var timer=null;
	
	for(i=0;i<aDiv.length;i++)
	{
		aDiv[i].style.filter="alpha(opacity:"+(10-i)*7+")";
		aDiv[i].style.opacity=(10-i)*7/100;
	};
	function oTan(){
		timer=setInterval(function(){
				iSpeedY+=3;
				
				var l=oDiv.offsetLeft+iSpeedX;
				var t=oDiv.offsetTop+iSpeedY;
				if(t>=document.documentElement.clientHeight-oDiv.offsetHeight+scrollY())
				{
					t=document.documentElement.clientHeight-oDiv.offsetHeight+scrollY();
					iSpeedY*=-0.7;
					iSpeedX*=0.7;
				}

				else if(t<=0)
				{
					t=0;
					iSpeedY*=-0.7;
					iSpeedX*=0.7;
				}
				
				if(l>=document.documentElement.clientWidth-oDiv.offsetWidth)
				{
					l=document.documentElement.clientWidth-oDiv.offsetWidth;
					iSpeedX*=-0.7;
				}
				else if(l<=0)
				{
					l=0;
					iSpeedX*=-0.7;
				}
				
				if(Math.abs(iSpeedX)<1)
				{
					iSpeedX=0;
				}
				
				
				oDiv.style.left=l+'px';
				oDiv.style.top=t+'px';
				
				for(i=aDiv.length-1;i>0;i--)
				{
					aDiv[i].style.left=aDiv[i-1].style.left;
					aDiv[i].style.top=aDiv[i-1].style.top;
				};
				
				aDiv[0].style.left=oDiv.offsetLeft+'px';
				aDiv[0].style.top=oDiv.offsetTop+'px';
			}, 30);	
	};
	oTan();
	oDiv.onmousedown=function(ev)
	{
		var oEvent=ev||event;
		clearInterval(timer);
		var disX=oEvent.clientX-oDiv.offsetLeft;
		var disY=oEvent.clientY-oDiv.offsetTop;
		document.onmousemove=function(ev)
		{
			var oEvent=ev||event;
			var l=oEvent.clientX-disX;
			var t=oEvent.clientY-disY;
			
			if(l<0)
			{
				l=0;
			}else if(l>document.documentElement.clientWidth-oDiv.offsetWidth)
			{
				l=document.documentElement.clientWidth-oDiv.offsetWidth;
			};
			
			if(t<0)
			{
				t=0;
			}else if(t>document.documentElement.clientHeight-oDiv.offsetHeight+scrollY())
			{
				t=document.documentElement.clientHeight-oDiv.offsetHeight+scrollY();
			};
			
			oDiv.style.left=l+'px';
			oDiv.style.top=t+'px';
			
			iSpeedX=l-lastDivX;
			iSpeedY=t-lastDivY;
			
			lastDivX=l;
			lastDivY=t;
			
			for(i=aDiv.length-1;i>0;i--)
			{
				aDiv[i].style.left=aDiv[i-1].style.left;
				aDiv[i].style.top=aDiv[i-1].style.top;
			};
			
			aDiv[0].style.left=oDiv.offsetLeft+'px';
			aDiv[0].style.top=oDiv.offsetTop+'px';
			
		};
		document.onmouseup=function()
		{
			document.onmousemove=null;
			document.onmouseup=null;
			
			oTan();
		}	
	}
});	
function scrollY(){
	return document.documentElement.scrollTop || document.body.scrollTop;
}