// 412|图片大小超出限制
// 406|图片类型不符合
// 403|图片移动失败！
// 407|图片宽度不够！
// 405|图片高度不够！
// 500|服务器端出错
// 200|{image_url}

;(function($){

//
$.extend({
	_HYQ_AVATAR_W:207,
	_HYQ_CROPPER_W:277,
	_HYQ_AVATAR2_W:77,
	_HYQ_AVATAR_4X3_W:127,
	_HYQ_AVATAR_4X3_H:77,
	_HYQ_ZOOM_IN_RANGE:1.97, 


	_HYQ_IMGCROPPER_TOKEN:"hyq/imgcropper/ver.0.1/20141124AM0256",
	 /***** 头像截取器独有的方法*/

  	_HYQ_UPDATE_AVATAR:function(imgCropper){
  		if(imgCropper.token!=$._HYQ_IMGCROPPER_TOKEN) return;
  		var c = imgCropper; if(!c) return;
  		c.avatar.find("img").remove();

  		$("<img>").attr('src',c._img.attr('src')).appendTo(c.avatar)
  		.one('load',function(){
			var rate = 150/280;

			var bW = c.imgDock.width();
			var bH = c.imgDock.height();
			 
			var oLeft = c.imgDock.position().left;
			var oTop = c.imgDock.position().top;
			 
			var targetImg = c.avatar.find('img');
			targetImg.attr("src",c._img.attr('src'));
			targetImg.one('load',function(){
			 	 
				var newH= bH*rate; newW= bW*rate;
				var pd = 35;
				var nLeft = Math.round((oLeft-pd)*rate);
				var nTop  =  Math.round((oTop-pd)*rate);
				targetImg.css({
					"left":nLeft+"px",
					"top":nTop+"px"
				});
				  
				targetImg.width(newW);targetImg.height(newH);
				targetImg.centerInBox();

			}).each(function(){
				if(this.complete)$(this).load();
			});
  		}).each(function(){
  			if(this.complete)$(this).load();
  		});



 	 },
 	 _HYQ_UPDATE_4x3:function(imgCropper){
		if(imgCropper.token!=$._HYQ_IMGCROPPER_TOKEN) return;
 

		var c = imgCropper; if(!c) return;
  		c.avatar4x3.find("img").remove();

  		$("<img>").attr('src',c._img.attr('src')).appendTo(c.avatar4x3)
  		.one("load",function(){
			var rate = 150/280;

			var bW = c.imgDock.width();
			var bH = c.imgDock.height();
			var oLeft = c.imgDock.position().left;
			var oTop = c.imgDock.position().top;

			var targetImg = c.avatar4x3.find('img');
			targetImg.attr("src",c._img.attr('src'));
			targetImg.one("load",function(){
			 
				var newH= bH*rate; newW= bW*rate;
				var pd = 35;
				var nLeft = Math.round((oLeft-pd)*rate);
				var nTop  =  Math.round((oTop-pd)*rate);
				targetImg.css({
					"left":nLeft+"px",
					"top":nTop+"px"
				});
				 
				targetImg.width(newW);targetImg.height(newH);
				targetImg.centerInBox();

			}).each(function(){
	  			if(this.complete){
	  				$(this).load();
	  			}
  			});
  		}).each(function(){
  			if(this.complete){
  				$(this).load();
  			}
  		});;

			 
 	 },
 	 _HYQ_FIT_IMAGE:function(imgCropper){//优化图片尺寸
  		if(imgCropper.token!=$._HYQ_IMGCROPPER_TOKEN) return;
  		var c = imgCropper; if(!c) return;
  		
  		var $i = c._img;
		var a = $i.width();
		var b = $i.height(),r=0;

		var _D  =$._HYQ_CROPPER_W;

		var na,nb,rate;
		 
		if(a<b){
			rate = _D/a;
			na = _D;
			nb = Math.round(rate*b);//*rate;
			 
		}else if(a==b){
			 rate = _D/a;
			 na=nb=_D;
		}else{
			rate = _D/b;
			nb=_D;
		 	na =Math.round(a*nb/b);
		}
		 
		$i.width(na),$i.height(nb) 
		c.imgDock.width($i.width());
		c.imgDock.height($i.height());
		$(c.imgDock).centerInBox();
		$._HYQ_UPDATE_AVATAR(c);
		$._HYQ_UPDATE_4x3(c);
  	}

});

$.widget("ui.HYQImgUploder",{
	options:{
		api:"upload.php",fileField:"image",filter:['png','jpg','jpeg'],cropper:null,
		defaultAvatar:"images/default_M_11.jpg",
		defaultAvatar4x3:"images/default_M_43.jpg"
	},

	cropper:null,
	_ifrm:null,
	_form:null,
	_file:null,

	stopUploading:function(){
		//console.log(this._ifrm.stop);
		if(typeof(this._ifrm.stop)=='function'){
		//	console.log("停止上传");
			this._ifrm.attr("src",'about:blank');
		}
	},
	//处理上传超时
	_timeoutInstance:null,
	uploadingFlag:false,
	_timeout:30000,//超时时间

	$s:null,
	showFileField:function(eventType){
		this._file.parent().show();
	 	this._file.click();
	},
	_E:{
		uploading:"hyq/imguploader/uploading",
		uploaded:"hyq/imguploader/uploaded",
		showFileField:"hyq/imguploader/showFileField",
	},
	_create:function(){
		var s = this;
		var $s = s.$s = $(this.element);
		$s.hide();
		s._ifrm = $('<iframe>').attr({"id":"frameFile","name":"frameFile"}).appendTo($s);
		s._form = $('<form>').attr({'enctype':'multipart/form-data','method':'post',"action":s.options.api,"target":s._ifrm.attr('name')}).appendTo($s);
		s._file = $('<input>').attr({'type':'file','name':s.options.fileField}).appendTo(s._form);
		 s._file.on('change',function(){
		 	//如果文件名无效则不做处理
		 	//if(s._file.val()=="" || typeof(s._file.val())==undefined) return;			//刘少华去掉
		 	s._form.submit();
		 	s.uploadingFlag = true;
		 	s._file.val('');
		 	//开始做超时计时
		 	s._timeoutInstance = setTimeout(function(){
		 		 
		 		s.stopUploading();
		 		$s.trigger("hyq/imguploader/timeout");
		 		s.uploadingFlag = false;
		 	}, s._timeout);
		 	$s.trigger(s._E.uploading);
		 });

		 s._ifrm.load(function(event){
		 	s._file.val('');
		 	//s._form.reset();
		 	clearTimeout(s._timeoutInstance);
		 	var back = s._ifrm.contents().find('body').html();
		 	if($.trim(back)=="") return;
		 	s.options.cropper._imageUploaded(back);
		 });

		 this._file.parent().parent().show().css('height','0');
	}
});


$.widget("ui.HYQImageCropper",{
	options:{
		api:'api.php',
		uploadApi:"",
		width:1200,
		height:900,
		type:1,
		cropApi:""

	},

	token:$._HYQ_IMGCROPPER_TOKEN,
	slider   : null,//滑动条
	cropper  : null,//裁剪窗口
	imgDock  : null,//图片拖拽代理
	imgMsk   : null,//图片拖拽代理蒙版
	sBtn     : null,//滑动条按钮

	avatar   : null,
	avatar4x3: null,

	inviteMsg : null,//上传规则提示
	inviteBtn : null,//上传邀请按钮
	
	cropMasks : null,//裁剪遮罩
	busyMask  : null,//忙碌遮罩
	
	//按钮
	resetBtn  : null,
	saveBtn   : null,
	changeBtn : null,

	//当前图片
	_img      : null,//当前图片
	_imgUrl	  : "",
	_rawSize  : {w:0,h:0},
	_niceSize :{w:0,h:0},
	_imgDockPos   : null,
	_scaleRatio : 1,

	_uploader  : null,
	_oTime : null,

	

	//处理绑定事件
	__uploadActionBinder:null,
	__uploadTrigger :function(event){
		var newTime = new Date().getTime();
		if (this._oTime) {
			if (newTime - this._oTime > 1000) {
				event.data.showFileField();
			}
			this._oTime = new Date().getTime();
		} else {
			event.data.showFileField();
			this._oTime = new Date().getTime();
		}	
	},

	_E        : {
		//网络相关事件
		imgload:"hyq/imgcropper/imgload",//图片在客户端载入完毕
		imgready:"hyq/imgcropper/imgready",//上传合法，图片准备就绪
		uploading:"hyq/imgcropper/uploading",//上传开始
		uploaded:"hyq/imgcropper/uploaded",//上传结束
		upload_err:"hyq/imgcropper/uploaderr",//上传出错
		//本地事件:图片大小发生变化
		imgscaling:"hyq/imagecropper/scaling",
		uiactived:"hyq/imagecropper/uiactived",
		uimuted:"hyq/imagecropper/uimuted",
		reset:"hyq/imagecropper/reset"
	},


	__activeUi:function(){
		if(this.cropMasks.is(":hidden")){
		//按钮显示出来的时候通知外界，窗口尺寸有改变
			this.$s.trigger(this._E.uiactived);
		}
		this.inviteBtn.hide();this.inviteMsg.hide();
		this.saveBtn.parent().show();
		this.cropMasks.show();
		this.$s.find('.slider-box').show();
		
		$(document).on('click','input[name="resetBtn"]',function () { console.log(5532);
			$('#img-cropper-dialog').add('.hyq-model-overlay').fadeOut();
		});
	},
	_moveImage:function(image,dOffset){
		$(image).css({
			"left":($(image).position().left-dOffset.left)+"px",
			"top":($(image).position().top-dOffset.top)+"px"
		});
	},
	_stopUploading:function(){
		// this.inviteBtn.show();
		// this.inviteMsg.show();
		// this.changeBtn.parent().hide();
		// this.cropMasks.hide();
		// this.$s.find('.slider-box').hide();
		this.busyMask.hide();
		this._uploader.uploadingFlag = false;
		this._uploader.stopUploading();
		// this.__uploadActionBinder = this.cropper.bind('click',this._uploader,this.__uploadTrigger);
	},
	_mutedUi:function(){
		this.inviteBtn.show();this.inviteMsg.show();
		this.changeBtn.parent().hide();
		this.cropMasks.hide();
		this.$s.find('.slider-box').hide();
		this.busyMask.hide();
		this.cropper.find('img').remove();
		this.avatar.find('img').attr('src',this.options.defaultAvatar).width('auto').height('auto').centerInBox();
		this.avatar4x3.find('img').attr('src',this.options.defaultAvatar4x3).width('auto').height('auto').centerInBox();
		this.__uploadActionBinder = this.cropper.bind('click',this._uploader,this.__uploadTrigger);
		
	},
	_scaleImage:function(node,scaling,zoom,s){
		 
		if(scaling>$._HYQ_ZOOM_IN_RANGE||scaling<=1) return;
		var newW = s._niceSize.w*scaling*zoom;
		var newH = s._niceSize.h*scaling*zoom;
		var dW,dH;
		if(scaling>s._scaleRatio){
		 	//图片在放大
		 	dW = newW-node.width();
		 	dH = newH-node.height();
		 	node.css({
		 		"left":node.position().left-dW/2,
		 		"top":node.position().top-dH/2
		 	});
		}else{
			//图片在缩小
			dW = node.width()-newW;
			dH = node.height()-newH;
			node.css({
				"left":node.position().left+dW/2,
				"top":node.position().top+dH/2
			});
		};

		if(node.find('img')){
			node.find('img').width(newW);
			node.find('img').height(newH);
		}
		node.width(newW);
		node.height(newH);

		s._scaleRatio= scaling;

	},
	_imageMoving:function(event,ui,s){
		//console.log("Image Moving");
		//console.log(ui);
	},

	_imageMoveStart:function(event,ui,s){
		//console.log(ui);
		s._imgDockPos = s.imgDock.position();
	},
	_imageMoved:function(event,ui,s){
		if(!s._img)return;
		//var rate = 152/280;
		var rate = 152/280;
		var curPos = s.imgDock.position();
		var dPos = {left:(s._imgDockPos.left-curPos.left)*rate,top:(s._imgDockPos.top-curPos.top)*rate};
		s._moveImage(s.avatar.find('img'),dPos);
		s._moveImage(s.avatar4x3.find('img'),dPos);

		 
	},

	_zoomDragStart:function(event,ui,s){
		//console.log(event);
	},
	_zoomDragging:function(event,ui,s){
		// console.log(event);
		// console.log(ui.position.left);
		if(ui.position.left>=308) return;
		var ratio =Math.round((1+ui.position.left/320)*100)/100;
		var zoom = Math.round(150*100/280)/100;
		s._scaleImage(s.imgDock,ratio,1,s);

		s._scaleImage(s.avatar.find('img'),ratio,zoom,s);
		s._scaleImage(s.avatar4x3.find('img'),ratio,zoom,s);
	},
	_zoomDragStop:function(event,ui,s){
		//console.log(event);
	},
	__handleBackData:function(data){
		console.log(data);
		var code =  data.split("|")[0];
		var s = this;
		s.busyMask.hide();
		if(code!="200"){
			s.$s.trigger({type:"hyq/imgcropper/upload-error",hyqData:data});
		}
				switch(code){
					case '200':
						s._imageReady(data.split("|")[1]);
						return;
					case '500':
						s.$s.trigger({type:'hyq/imgcropper/error-message',hyqMessage:"服务器端错误，请稍后再试。"});
						return;
					case '403':
						s.$s.trigger({type:'hyq/imgcropper/error-message',hyqMessage:"无法上传图片，请稍后再试"});
						return;
					case '405':
						s.$s.trigger({type:'hyq/imgcropper/error-message',hyqMessage:"请上传长度大于600像素，高度大于600像素的图片"});
						return;
					case '407':
						s.$s.trigger({type:'hyq/imgcropper/error-message',hyqMessage:"请上传长度大于600像素，高度大于600像素的图片"});
						return;	
					case '406':
						s.$s.trigger({type:'hyq/imgcropper/error-message',hyqMessage:"图片类型不符合规范，必须是jepg,jpg,png格式的图片。"});
						break;
					case '412':
						s.$s.trigger({type:'hyq/imgcropper/error-message',hyqMessage:"图片尺寸超过限制，请上传小于5M的合法图片。"});
						return;
					default:
						s.$s.trigger({type:'hyq/imgcropper/error-message',hyqMessage:"发生未知错误，可能是请求无法访问，请稍后再试。"});
						return;

		}
	},

	_imageUploaded:function(data){//图片上传完毕
		this.__handleBackData(data);
	},
	_showErrs:function(){//显示错误

	},
	cropWithExistImage:function(url){
		this._imageReady(url);
	},
	_imageReady:function(url){//图片准备就绪
		var s = this;
		s._imgUrl = url;
		s.sBtn.css( {"left":"0px" });
		this.__uploadActionBinder.unbind('click',this.__uploadTrigger);
		this.imgDock.find('img').remove(); 	
		this._img = $("<img>")
		.appendTo(s.imgDock) 
		s._img.attr("src",url)
		.one('load',function(){
			s.__activeUi();
			//一定要在FIT之前获取
			s._rawSize = {w:s._img.width(),h:s._img.height()};

			$._HYQ_FIT_IMAGE(s);
			//触发图片就绪事件
			s.$s.trigger({type:s._E.imgready,image:url});
			s.imgDock.draggable({stop:function(event,ui){
				s._imageMoved(event,ui,s);
			},drag:function(event,ui){
				s._imageMoving(event,ui,s);
			},start:function(event,ui){
				s._imageMoveStart(event,ui,s);
			}});

			s._niceSize.w = s.imgDock.width();
			s._niceSize.h = s.imgDock.height();

		}).each(function(){
			if(this.complete)$(this).load();
			s.busyMask.hide();
		});
	},

	_saveAction:function(){
		var s =this;
		s.busyMask.show();
		//多的图片相对坐标

		var rate =280/150;
		var s = this;
		var avt = s.avatar.find('img');
		var avt2 = s.avatar4x3.find('img');
		var data ={zoom :0,h:s.options.height,w:s.options.width,x1:0,y1:0,x2:0,y2:0,src:$(s._img).attr("src")};

		if(avt&&avt2){
			if(avt.position().top<0){
			 	data.x1 = -Math.round(100*rate*avt.position().left)/100;
			 }else{
			 	data.x1 = Math.round(100*rate*avt.position().left)/100;
			 }

			 if(avt.position().top<0){
			 	data.y1 = -Math.round(100*rate*avt.position().top)/100;
			 }else{
			 	data.y1 = Math.round(100*rate*avt.position().top)/100;
			 }
 			/*
			 if(avt.position().top<0){
			 	data.x2 = -Math.round(100*rate*avt2.position().left)/100;
			 }else{
			 	data.x2 = Math.round(100*rate*avt2.position().left)/100;
			 }

			 if(avt.position().top<0){
			 	data.y2 = -Math.round(100*rate*avt2.position().top)/100;
			 }else{
			 	data.y2 = Math.round(100*rate*avt2.position().top)/100;
			 }
			 */

			 //判断是否有空白边
			 var flgX1  = 1,flgX2=1,flgY1=1,flgY2=1;

			 if(avt.position().left>0)flgX1=-1;
			 if(avt2.position().left>0)flgX2=-1;
			 if(avt.position().top>0)flgY1=-1;
			 if(avt2.position().top>0)flgY2=-1;

			 //因为需要兼容900x900的尺寸要求，在这里做一个比例转换
			data.zoom = Math.round(900/280*100*rate*avt.width()/s._rawSize.w)/100;
			data.x1 = data.x1*900/280*flgX1;
			data.x2 = data.x2*900/280*flgX2;
			data.y1 = data.y1*900/280*flgY1;
			data.y2 = data.y2*900/280*flgY2;

			//尺寸1040*585
			if(s.options.type == 2){
				console.debug(s.cropper.find("img"),s.cropper.find("img").width(),s._rawSize.w,s.options.width,s.cropper.width()-70);
				data.zoom = s.cropper.find("img").width()/s._rawSize.w*(s.options.width/(s.cropper.width()-70));
				console.debug(data.zoom);
				
				data.x1 = 0-(s.cropper.find("[img-dock]").position().left-35);
				data.y1 = 0-(s.cropper.find("[img-dock]").position().top-35);
				
				
				data.x1 = data.x1*s.options.width/(s.cropper.width()-70);
				data.y1 = data.y1*s.options.height/(s.cropper.height()-70);
			}
			


			$.ajaxSetup ({
				cache: false
			});
			$.post(s.options.cropApi,data)
			.success(function(d){
				console.log(d,'99999999999909033');
				s.busyMask.hide();
				var res = $.trim(d).split('|');
				if((res[0])=='500'){
					s.$s.trigger({type:"hyq/imgcropper/error-message",hyqMessage:"服务器错误，请联系网站管理员。"});
					return;
				}else if((res[0])=='406'){
					s.$s.trigger({type:"hyq/imgcropper/error-message",hyqMessage:"文件出错，或者格式不支持，请重新上传。"});
					return;
				}else if(res[0]=="200"){
					s.$s.trigger({type:'hyq/imgcropper/success',images:[res[1],res[2]]});
				}else{
					s.$s.trigger({type:"hyq/imgcropper/error-message",hyqMessage:"发生未知错误，请稍后再试"});
				}
				
			}).error(function(){
				s.$s.trigger({type:'hyq/imgcropper/error-message',hyqMessage:"请求发送错误，请检查你的网络连接。"});
				s.busyMask.hide();
			});
		}

	},

	_create:function(){
		var s = this;
		var $s =	s.$s = $(this.element);
		$s.find('.slider').slider();
		s.slider	= $s.find('.slider').data("ui-slider");
		s.cropper 	= $s.find(".cropper");//裁剪窗口
		s.imgDock	= $s.find('[img-dock]');//图片拖拽代理
		s.imgMsk   	= $s.find('[img-mask]');//图片拖拽代理蒙版
		s.sBtn    	= $s.find('.slide-button');//滑动条按钮

		s.inviteMsg = $s.find('.invite-message');//上传规则提示
		s.inviteBtn = $s.find('.invite-btn');//上传邀请按钮
	
		s.cropMasks = $s.find(".mask");//裁剪遮罩
		s.busyMask  = $s.find('.busy-mask');//忙碌遮罩
	
	//按钮
		s.resetBtn  = $s.find('[name=resetBtn]');
		s.saveBtn   = $s.find('[name=saveBtn]');
		s.changeBtn = $s.find('[name=changeBtn]'); 

		s.avatar 	= $s.find('.preview-a');
		s.avatar4x3 = $s.find('.preview-b');

		s.options.defaultAvatar = s.avatar.find('img').attr('src');
		s.options.defaultAvatar4x3 = s.avatar4x3.find('img').attr('src');


		$s.find('div[img-cropper-uploder]').HYQImgUploder({cropper:s,api:s.options.uploadApi});
		s._uploader = $s.find('div[img-cropper-uploder]').data("ui-HYQImgUploder");
		s.__uploadActionBinder = s.cropper.bind('click',s._uploader,s.__uploadTrigger);
		s.changeBtn.click(s._uploader,s.__uploadTrigger);
		 
		s.sBtn.draggable({ axis: "x" ,containment: "parent" ,start:function(event,ui){
			s._zoomDragStart(event,ui,s);
		},stop:function(event,ui){
			s._zoomDragStop(event,ui,s);
		},drag:function(event,ui){
			s._zoomDragging(event,ui,s);
		}});

		s.resetBtn.click(function(){
			if(s._imgUrl){
				s._imageReady(s._imgUrl);
			}
		});
		s.saveBtn.click(function(){
			s._saveAction();
		});

		s.cropper.on("mouseover",function(evt){
			s.cropper.css({'background-color':'#4d4d4d',"cursor":'pointer'});
			s.cropper.find('.invite-message').css({"color":"#fff"});
		});
		s.cropper.on('mouseout',function(evt){
			s.cropper.css({'background-color':'#d7d7d7'});
			s.cropper.find('.invite-message').css({'color':'#4a4a4a'});
		});
		$s.on(s._E.uimuted,function(){
			s._mutedUi();
		})
 		$(s._uploader.element).on("hyq/imguploader/uploading",function(){
 			s.busyMask.show();
 		});
 
 		//点加减号
 		var sbtn = $s.find(".slide-button");

 		//点减号
 		$s.find(".zout").click(function(event){
 			var dx = sbtn.position().left-5;
 			console.log(dx);
 			if(dx>=308)dx=308;
 			if(dx<0)dx=0;
 			
 			sbtn.css({
 				left:dx+"px"
 			});

	 		var ratio =Math.round((1+sbtn.position().left/320)*100)/100;
			var zoom = Math.round(150*100/280)/100;
			s._scaleImage(s.imgDock,ratio,1,s);

			s._scaleImage(s.avatar.find('img'),ratio,zoom,s);
			s._scaleImage(s.avatar4x3.find('img'),ratio,zoom,s);

 		});

 		//点加号
 		$s.find(".zin").click(function(event){
 			var dx = sbtn.position().left+5;
 			if(dx>=308)dx=308;
 			if(dx<0)dx=0;
 			//console.log(dx);
 			sbtn.css({
 				left:dx+"px"
 			});

 			var ratio =Math.round((1+sbtn.position().left/320)*100)/100;
			var zoom = Math.round(150*100/280)/100;
			s._scaleImage(s.imgDock,ratio,1,s);

			s._scaleImage(s.avatar.find('img'),ratio,zoom,s);
			s._scaleImage(s.avatar4x3.find('img'),ratio,zoom,s);

 		});

 		//上传超时
 		s._uploader.element.on("hyq/imguploader/timeout",function(){
 			//console.log("上传超时###");
 			s._uploader.uploadingFlag = false;
 			s._mutedUi();
 			s.$s.trigger({type:"hyq/imgcropper/error-message",hyqMessage:"文件上传超时，有可能是网络问题或者您上传的文件过大，请重试。"});
 		});

	}

});

$("document").ready(function(){
	$("#img-cropper-dialog").on("hyq/modal/hidden",function(){
		

		var img_cropper = $("#img-cropper-dialog .hyq-image-cropper").data("ui-HYQImageCropper");
		//console.log("====");
		//console.log(img_cropper._uploader.uploadingFlag);
		if(img_cropper._uploader.uploadingFlag){
			//console.log("暂停正在上传中的文件");
			img_cropper._stopUploading();	
		}
		
	})
});

})(jQuery);
