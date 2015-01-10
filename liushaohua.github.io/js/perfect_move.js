function startMove(obj, json, fn)
{
	clearInterval(obj.timer);
	obj.timer=setInterval(function (){
		doMove(obj, json, fn);
	}, 30);
}

function getStyle(obj, attr)
{
	if(obj.currentStyle)
	{
		return obj.currentStyle[attr];
	}
	else
	{
		return getComputedStyle(obj, false)[attr];
	}
}

function doMove(obj, json, fn)
{
	var attr='';
	var 已经到了=true;
	
	for(attr in json)
	{
		var iCur=0;
		
		if(attr=='opacity')
		{
			iCur=parseInt(parseFloat(getStyle(obj, 'opacity'))*100);
		}
		else
		{
			iCur=parseInt(getStyle(obj, attr));
		}
		
		if(iCur!=json[attr])	//发现了一个值，还没到
		{
			已经到了=false;
		}
		
		var iSpeed=(json[attr]-iCur)/8;
		iSpeed=iSpeed>0?Math.ceil(iSpeed):Math.floor(iSpeed);
		
		if(attr=='opacity')
		{
			obj.style.filter="alpha(opacity:"+(iCur+iSpeed)+")";
			obj.style.opacity=(iCur+iSpeed)/100;
		}
		else
		{
			obj.style[attr]=iCur+iSpeed+'px';
		}
	}
	
	if(已经到了)
	{
		clearInterval(obj.timer);
		if(fn)
		{
			fn();
		}
	}
}