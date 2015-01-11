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
	var oDiv=document.getElementById('graphic');
	var aBtn=document.getElementById('btn').getElementsByTagName('li');
	var oUl=document.getElementById('bigimg');
	var aLi=oUl.getElementsByTagName('li');
	var prev=document.getElementById('prev').getElementsByTagName('a')[0];
	var next=document.getElementById('next').getElementsByTagName('a')[0];
	var timer=null;
	var iNow=0;
	var i=0;
	oUl.style.width=aLi[0].offsetWidth*aLi.length+'px';
	oDiv.onmouseover=function()
	{
		clearInterval(timer);	
	}
	oDiv.onmouseout=function()
	{
		timer=setInterval(function()
		{
			iNow++;
			if(iNow==aBtn.length)
			{
				iNow=0;
			};
			tab();	
		},2000);
	};
	for(i=0;i<aBtn.length;i++)
	{
		aBtn[i].index=i;
		aBtn[i].onmouseover=function()
		{
			iNow=this.index
			tab();
		}
	};
	function tab()
	{
		for(i=0;i<aBtn.length;i++)
		{
			aBtn[i].className='';	
		};
		aBtn[iNow].className='active';
		//oUl.style.left=-aLi[0].offsetWidth*iNow+'px';
		startMove(oUl,{left: -aLi[0].offsetWidth*iNow});
	};
	prev.onclick=function()
	{
		iNow--;
		if(iNow==-1)
		{
			iNow=aBtn.length-1;
		};	
		tab();
	};
	next.onclick=function()
	{
		iNow++;
		if(iNow==aBtn.length)
		{
			iNow=0;
		};
		tab();
	};
	clearInterval(timer);
	timer=setInterval(function()
	{
		iNow++;
		if(iNow==aBtn.length)
		{
			iNow=0;
		};
		tab();	
	},2000);
});	