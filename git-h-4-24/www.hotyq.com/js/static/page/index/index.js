//加载placeholder
require('../../common/ui/placeholder/hyq-placeholder.js');
//加载modal
require('../../common/ui/modal/hyq-modal.js');
//加载tip
require('../../common/ui/tip/hyq-tip.js');
//初始化main
require('../../common/ui/main/main.js');
//加载公用模块
require('../global/global.js');
//初始化lazy
require('../../common/ui/lazyload/lazyload.js').lazyload();
//初始化header
require('../../common/header/nav_search/nav_search.js');
require('../../common/header/user/user.js');
//红人说切换
var Class = {
	create : function() {
	  return function() {
		this.initialize.apply(this, arguments);
	  }
	}
  }
  Object.extend = function(destination, source) {
	for (var property in source) {
	  destination[property] = source[property];
	}
	return destination;
  }
  var TransformView = Class.create();
  TransformView.prototype = {
	//容器对象,滑动对象,切换参数,切换数量
	initialize : function(container, slider, parameter, count, options) {
	  if (parameter <= 0 || count <= 0)
		return;
	  var oContainer = document.getElementById(container), oSlider = document.getElementById(slider), oThis = this;
	  this.Index = 0;
	  //当前索引
	  this._timer = null;
	  //定时器
	  this._slider = oSlider;
	  //滑动对象
	  this._parameter = parameter;
	  //切换参数
	  this._count = count || 0;
	  //切换数量
	  this._target = 0;
	  //目标参数
	  this.SetOptions(options);
	  this.Up = !!this.options.Up;
	  this.Step = Math.abs(this.options.Step);
	  this.Time = Math.abs(this.options.Time);
	  this.Auto = !!this.options.Auto;
	  this.Pause = Math.abs(this.options.Pause);
	  this.onStart = this.options.onStart;
	  this.onFinish = this.options.onFinish;
	  oContainer.style.overflow = "hidden";
	  oContainer.style.position = "relative";
	  oSlider.style.position = "absolute";
	  oSlider.style.top = oSlider.style.left = 0;
	},
	//设置默认属性
	SetOptions : function(options) {
	  this.options = {//默认值
		Up : true, //是否向上(否则向左)
		Step : 5, //滑动变化率
		Time : 30, //滑动延时
		Auto : true, //是否自动转换
		Pause : 3000, //停顿时间(Auto为true时有效)
		onStart : function() {
		}, //开始转换时执行
		onFinish : function() {
		}//完成转换时执行
	  };
	  Object.extend(this.options, options || {});
	},
	//开始切换设置
	Start : function() {
	  if (this.Index < 0) {
		this.Index = this._count - 1;
	  } else if (this.Index >= this._count) {
		this.Index = 0;
	  }
	  this._target = -1 * this._parameter * this.Index;
	  this.onStart();
	  this.Move();
	},
	//移动
	Move : function() {
	  clearTimeout(this._timer);
	  var oThis = this, style = this.Up ? "top" : "left", iNow = parseInt(this._slider.style[style]) || 0, iStep = this.GetStep(this._target, iNow);
	  if (iStep != 0) {
		this._slider.style[style] = (iNow + iStep) + "px";
		this._timer = setTimeout(function() {
		  oThis.Move();
		}, this.Time);
	  } else {
		this._slider.style[style] = this._target + "px";
		this.onFinish();
		if (this.Auto) {
		  this._timer = setTimeout(function() {
			oThis.Index++;
			oThis.Start();
		  }, this.Pause);
		}
	  }
	},
	//获取步长
	GetStep : function(iTarget, iNow){
	  var iStep = (iTarget - iNow) / this.Step;
	  if (iStep == 0)
		return 0;
	  if (Math.abs(iStep) < 1)
		return (iStep > 0 ? 1 : -1);
	  return iStep;
	},
	//停止
	Stop : function(iTarget, iNow) {
	  clearTimeout(this._timer);
	  this._slider.style[this.Up ? "top" : "left"] = this._target + "px";
	}
  };

  $(window).load(function() {
	$(".down-expend-btn,.down-foldup-btn,.read-more-info-zk-1").click(function() {
	  $(".down-expend-btn,.down-foldup-btn").toggle();
	  if ($("#reds-details").is(":hidden")) {
		$("#reds-details").fadeIn();
	  } else {
		$("#reds-details").fadeOut();
	  }
	});

	$(".more-filter-zk-js,.more-filter-zk-js-1").click(function() {
	  $(".more-filter-zk-js,.more-filter-zk-js-1").hide();
	  $(this).parent().parent().find(".info-detail-filter-js").fadeIn();
	});

	$(".more-filter-sq-js").click(function() {
	  $(this).parent().hide();
	  $(this).parent().parent().find(".more-filter-zk-js,.more-filter-zk-js-1").fadeIn();
	});

	var objs = $("#idNum2").find("li");
	//console.log(objs)
	var tv = new TransformView("idTransformView2", "idSlider2", 980, 3, {
	  onStart : function() {
		$.each(objs, function(i, item) {
		  if(tv.Index == i){
			$(item).addClass("on");
		  }
		  else{
			$(item).removeClass("on");
		  }
		})
	  }, //按钮样式
	  Up : false
	});

	tv.Start();

	$.each(objs, function(i, item) {

	  $(item).on("mouseover", function() {
		//console.log(11);
		$(item).addClass("on");
		tv.Auto = false;
		tv.Index = i;
		tv.Start();
	  });
	  $(item).on("mouseout", function() {
		//console.log(22);
		$(item).removeClass("on");
		tv.Auto = true;
		tv.Start();
	  });
	})
   $('.reds-say-txt').each(function(){
	  $(this).on("mouseover", function() {
		tv.Auto = false;
		tv.Start();
	  });
	  $(this).on("mouseout", function() {
		tv.Auto = true;
		tv.Start();
	  });
   });
});

//广告明暗效果
$(function() {
  $(".floorMain").find("a").each(function() {
	$(this).hover(function() {
	
		$(this).siblings().find(".mask").stop();
		$(this).siblings().find(".mask").fadeTo("fast",0.4);
		},
		function() {
			$(this).siblings().find(".mask").stop();
			$(this).siblings().find(".mask").fadeTo("fast",0);
		});
	});
});

$(document).ready(function() {
	$(".down-expend-btn,.down-foldup-btn,.read-more-info-zk-1").click(function() {
	  $(".down-expend-btn,.down-foldup-btn").toggle();
	  if ($("#reds-details").is(":hidden")) {
		$("#reds-details").fadeIn();
	  } else {
		$("#reds-details").fadeOut();
	  }
	});

	$(".more-filter-zk-js,.more-filter-zk-js-1").click(function(){
		$(".more-filter-zk-js,.more-filter-zk-js-1").hide();
		$(this).parent().parent().find(".info-detail-filter-js").fadeIn();  
	});

	$(".more-filter-sq-js").click(function(){
		$(this).parent().hide();
		$(this).parent().parent().find(".more-filter-zk-js,.more-filter-zk-js-1").fadeIn();
	});
});

//daohang left
$(function  () {
	$('.leftNav>li').hover(function  () {
		$(this).css({background:'#fff'}).addClass('be').children('h3').css({color:'#ff3300'}).next().css({color:'#ff3300'}).next().show();
	},function  () {
		$(this).css({background:'none'}).removeClass('be').children('h3').css({color:'#fff'}).next().css({color:'#fff'}).next().hide();
	});
	$('.navSecond').each(function  (index,obj) {
		$(obj).css({top:-40-38*index})
	})
	$('ul.leftNav>li').addClass('bee');
	$('.leftNav').hover(function(){	 
			$('.hd').hide();
			$('.arrow-con').hide();
				 },function(){
			$('.hd').show();
			$('.arrow-con').show();
	 })
});
$('.banner-href').mousemove(function(){
	//console.log($('.fullSlide .hd ul li').length);
	var arcs=$('.fullSlide .hd ul li');
	//console.log(arcs.index($('.fullSlide .hd ul li.on').get(0)));
	var arcs_now=arcs.index($('.fullSlide .hd ul li.on').get(0));
	if ($('.fullSlide .bd ul li').eq(arcs_now).children('a').length!==0){
	  	$('.banner-href a').show().attr('href',$('.fullSlide .bd ul li').eq(arcs_now).children('a').eq(0).attr('href'));
	}else{
		$('.banner-href a').hide();
	};
	
});
//tab
(function () {
	var cLeft = 0,
		timer,
		$bar = $('.hyq-tabs-bar'),
		$tab = $('.hyq-tab');
	$tab.click(function () {
		var $this = $(this),
			cBox = $('.hyq-tab-pane').eq($this.attr('index')),
			cImg = cBox.find('img');
		$this.addClass('active').siblings().removeClass('active');
		cBox.show().siblings().hide();
		cLeft = $this.position().left;
		$bar.width($this.width());
		if (!$this.attr('lazyEnd')) {	
			cImg.each(function (i,e) {
				var $this = $(this);
				$this.fadeOut(0).attr('src',$this.attr('tab-lazy')).fadeIn('slow');
			});
			$this.attr('lazyEnd','true');
		}	
	});
	
	function move(PageX) {
		clearInterval(timer);
		timer = setTimeout(function () {
			$('.hyq-tabs-bar').animate({'left':PageX},"fast");
		},500)
	}
	
	$tab.hover(function () {
		clearInterval(timer);
		$bar.stop().animate({'left':$(this).position().left},"fast");
	},function () {
		move(cLeft)
	});
})();
