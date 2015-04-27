;(function($){
 
CanvasRenderingContext2D.prototype.sector = function (x, y, radius, sDeg, eDeg) {

	this.save();
	this.translate(x, y);
	this.beginPath();
	this.arc(0,0,radius,sDeg, eDeg);
	this.save();
	this.rotate(eDeg);
	this.moveTo(radius,0);
	this.lineTo(0,0);
	this.restore();
	this.rotate(sDeg);
	this.lineTo(radius,0);
	this.closePath();
	this.restore();
	return this;
}

$.fn.HYQPctPie = function(){
	var sel = this ;
	var $sel = $(this);
	$sel.css({"cursor":"pointer","cursor":"*hand"});
	var attrs =  sel.hyqAttrs = {};
	attrs.R = $sel.attr("R");
	 
	attrs.foreColor = $sel.attr("fore-color");
	attrs.backColor = $sel.attr("back-color");
	attrs.fontColor = $sel.attr("font-color");
	attrs.bgColor = $sel.attr("bg-color");
	attrs.percent = $sel.attr("percent");
	attrs.fontSize = $sel.attr("font-size");
	var rd = attrs.R*2;
	$sel.css({
		"display":"block",
		"position":"relative",
		"overflow":"hidden",
		"width":(rd+2)+"px",
		"height":(rd+2)+"px",
		"background-color":attrs.bgColor
	});

	    
	$sel.append($("<canvas width="+(rd+2)+" height="+(rd+2)+"></canvas>"));
	$sel.append($("<label class='hyq-percent-pie-txt'>"+attrs.percent+"<small>%</small></label>"));

	var p = sel.percentNode = $sel.find('label');

	p.css({
		"position":"absolute",
		"display":"block",
		"text-align":"center",
		"overflow":"hidden",
		"color":attrs.fontColor,
		"font-family":"verdana",
		"left":"0px",
		"top":"0px",
		"margin":"0px",
		"padding":"0px",
		"font-weight":"bold",
		"font-size":attrs.fontSize,
		"line-height":rd+"px",
		"height":(rd+2)+"px",
		"width":(rd+2)+"px",
		"z-index":"100"
		//"background-color":"pink"
	});
	var samllFontSize = Math.round(parseInt(attrs.fontSize.replace("px"))*50/100)+"px";
	$sel.find('label small').css({"font-size":samllFontSize});

	var c =sel.canvas = $sel.find('canvas')[0];
	//console.log(c.getContext);

	$sel.append($());
	$sel.fadeOut(0).fadeIn(500);
 

	if (c.getContext) {
		var deg = Math.PI/180;
		var pnt = {x:rd/2+1,y:rd/2+1}
		//画底色

    	var ctx = c.getContext('2d');
    	ctx.beginPath();
    	ctx.arc(pnt.x,pnt.y, rd/2, 0, Math.PI*2, true);
    	ctx.fillStyle =attrs.backColor;
    	ctx.fill();

    	//画扇形
		ctx.beginPath();
		ctx.sector(pnt.x,pnt.y, rd/2, -90*deg, ((360*attrs.percent/100)-90)*deg);
    	ctx.fillStyle =attrs.foreColor;
    	ctx.fill();
    	//画内环
    	
    	ctx.beginPath();
    	ctx.arc(rd/2+1,rd/2+1, Math.round(rd/2/5*4), 0, Math.PI*2, true);
    	ctx.fillStyle = attrs.bgColor;
    	ctx.fill();


    	// ctx.stroke();		
		
	}
	
	$(document).on("hyq/userstate/changed",function(){
		if(typeof($.cookie('hyq_user_info')) == 'undefined' || $.cookie('hyq_user_info').split('|')[0] < 1 ) return;
		if (!c.getContext) return;

		//console.log("cookie改变重新画圈圈");
		var txt = $sel.find(".hyq-percent-pie-txt");
		var percent = parseInt($.cookie('hyq_user_info').split("|")[4]);
		var deg = Math.PI/180;
		var pnt = {x:rd/2+1,y:rd/2+1}
		//画底色

		txt.html(percent+"<small>%</small>");
    	var ctx = c.getContext('2d');
    	ctx.clearRect(0,0,rd+2,rd+2);
    	ctx.beginPath();
    	ctx.arc(pnt.x,pnt.y, rd/2, 0, Math.PI*2, true);
    	ctx.fillStyle =attrs.backColor;
    	ctx.fill();

    	//画扇形
		ctx.beginPath();
		ctx.sector(pnt.x,pnt.y, rd/2, -90*deg, ((360*percent/100)-90)*deg);
    	ctx.fillStyle =attrs.foreColor;
    	ctx.fill();
    	//画内环
    	
    	ctx.beginPath();
    	ctx.arc(rd/2+1,rd/2+1, Math.round(rd/2/5*4), 0, Math.PI*2, true);
    	ctx.fillStyle = attrs.bgColor;
    	ctx.fill();
	});




}
$(document).ready(function(){
	$.each($("[hyq-percent-pie]"),function(i,item){	
		setTimeout(function () {
			$(item).HYQPctPie();
		},1000);
	});
});

}(jQuery));

$(function(){
	$('#login_user_percent').click(function(){
		window.location.href=$('#hyq_percent_pie')[0].href;
	});
});