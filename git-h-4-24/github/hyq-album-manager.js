;(function($){
	$.extend({
		_HYQ_TSTMP :function(){
			var timestamp1 = Date.parse(new Date());  
			var timestamp2 = (new Date()).valueOf();  
			var timestamp3 = new Date().getTime();  
			return timestamp1+"_"+timestamp2+"_"+timestamp3; 
		},
		_HYQ_GET_SIZE_OF:function(siz){

		}	

	});
	//事件定义
	var _ALBUM_UPLODER_IMG_READY="hyq/album/uploader/img_ready";//图片上传成功且返回了地址
	var _ALBUM_UPLODER_TIMEOUT="hyq/album/uploader/timeout";//上传超时
	var _ALBUM_UPLODER_UPLOADING="hyq/album/uploader/uploading";//开始上传
	var _ALBUM_UPLODER_UPLOAD_ERR="hyq/album/uploader/upload_err";//上传错误事件
	//图片上传器
	$.widget("ui._HYQAlbumUploader",{
		//处理上传超时的变量	
		_timeoutInstance:null,
		//上传的超时时间设置
		_timeout:30000,//超时时间
		_isUploading:false,
		//上传超时处理
		_timeoutHandler:function(s){
			s._ifrm.attr({"src":"about:blank"});
			s._isUploading = false;
			$(s.element).trigger({
				type:_ALBUM_UPLODER_TIMEOUT,
				albumItem:s.options.albumItem
			});
		},
		//参数
		options:{ 
		 	albumItem:null,
		 	uploadApi:null,
		 	fileFiledName:null,
		 	albumStore:null
		},
		//文件域
		_file:null,
		//隐藏的上传表单
		_form:null,
		//隐藏的IFRAME
		_ifrm:null,
		//罩在上传器上面的一层透明点击区域
		clickMask:null,
		//邀请按钮（+号按钮）
		inviteBtn:null,
		//进入上传模式
		uploadMode:function(){
			this.clickMask.show();
			this.inviteBtn.show();
		},
	 	//处理上传后返回的信息
		handleBack :function(data){
			//上传API返回的数据
			var $s = $(this.element);
			var s = this;
			var c = data.code;

			if(c=="1000"){
				$s.trigger({
					type:_ALBUM_UPLODER_IMG_READY,
					rawData:data,
					message:data.desc,
					_photo:data.data.photo,
					_thumbnail:data.data.thumbnail,
					_id:data.data.id,
					holder:s
				});
				return;
			}
			else if(c=="1041"||c=="1042"||c=="1043"||c=="1044"||c=="1014"||c=="1058"){
				s.uploadMode();
				s.options.albumItem.editMode();
				$s.trigger({type:_ALBUM_UPLODER_UPLOAD_ERR,rawData:data,message:data.desc,holder:s});
				return;
			}else{
				s.uploadMode();
				s.options.albumItem.editMode();
				$s.trigger({type:_ALBUM_UPLODER_UPLOAD_ERR,rawData:data,message:"发生未知错误！",holder:s});
				return;
			}
					
		},
		//构造函数
		 _create:function(){
		 	var s = this,$s = $(this.element);
			var uploadApi = this.options.uploadApi||null;
		 	if(!uploadApi)throw "Not enough parameters for hqy-album-uploader,please specify the upload api.";

		 	if(!this.options.albumItem) throw "params error: Please specify a holder album item for Uploader";
		 	var $el = $(this.element);

		 	this.clickMask = $el.find(".click-mask");
		 	this.inviteBtn =$el.find('.invite-btn');

		 	var frameName = "albumUploadFrame"+$._HYQ_TSTMP();
		 	this._ifrm = $('<iframe>').attr({"id":frameName,"name":frameName}).appendTo($el).hide();
		 	var formName = "albumUploadForm"+$._HYQ_TSTMP();
			this._form = $('<form>').attr({'enctype':'multipart/form-data',"name":formName,'method':'post',"action":uploadApi,"target":frameName}).appendTo($el).hide();

			var fileField = this.options.fileFiledName||"image";
			this._file = $('<input>').attr({'type':'file','name':fileField}).appendTo(this._form);

			this.clickMask.click(function(){
				$(this).parent().find('input').click();
			});
			//当文件域有所改变时，提交上传的隐藏表单
			s._file.on("change",function(){
				InputChange();
			});

			function InputChange () {
				s._form.submit();
				if (!/Trident/.test(navigator.userAgent)) {
					s._form.find('input').val('');
				} else {
					s._form.html(s._form.html());
					s._form.find('input').replaceWith('<input type="file" name="image">');
					s._form.find('input').on("change",function(){
					console.log("FILE CHANGED2222");
						InputChange();
					});
				}

				$('.profile-expend-btn').css('opacity',0);
				//开始做超时倒计时
				s._isUploading = true;
				s._timeoutInstance = setTimeout(function(){s._timeoutHandler(s);},s._timeout);
				//通知外界开始上传操作
				$s.trigger("hyq/album/uploader/uploading");
				$s.hide();
			}

			//当iframe 载入完成时（即上传有所返回时）
			this._ifrm.load(function(event){
				clearTimeout(s._timeoutInstance);
				var back = s._ifrm.contents().find('body').text();
				//console.log("上传iframe返回内容");
				if(back=="") return;

				try{

					var data =$.parseJSON(back);
					console.log('load-----',data)
				 	s.handleBack(data);
				}catch(e){
						//do nothing
						throw e;
						$s.show();
					$s.trigger({type:_ALBUM_UPLODER_UPLOAD_ERR,rawData:data,message:"JSON数据解析错误！",holder:s});
				}
			});
			//上传开始时
			$s.on(_ALBUM_UPLODER_UPLOADING,function(event){
				 
			});
			//上传成功时
			$s.on(_ALBUM_UPLODER_IMG_READY,function(event){
				s.uploadMode();
				$s.hide();

			});
		 }
		
});
/**
	_HYQAlbumItem ====================
*/
$.widget("ui._HYQAlbumItem",{
		options:{
		 	uploadApi:null,
		 	fileFiledName:null,
		 	albumStore:null,
		 	albumData:null,
		 	manager:null,
		 	pos:null//位置序号
		},
		//图片缩略图外层容器
		imgBox:null,
		//无图显示样式
		noImgBox:null,
		uploader:null,
		actionBtn:null,
		imgCheckBox:null,
		manager:null,
		_photoId:null,
		busyMsk:null,
		 
		busyMode:function(flag){
			var s = this,$s = $(this.element);
			if(flag){
				s.busyMsk.isBusy()?void(null):s.busyMsk.active();
				s.busyMsk.bringToFront();

			}else{
				s.busyMsk.mute();
			}
			 //console.log("BUSY MODE 结束");
			
		},
		editMode:function(){
		 	var s =this,$s=$(this.element);
		 	
			this.noImgBox.hide();
			$s.removeClass('active');
			if(s.imgBox.find("img").length==0){
				s.imgBox.hide();
				s.uploader.show();
				s.imgCheckBox.hide();
				s.actionBtn.parent().hide();
				s.busyMsk.mute();
			}else{
				s.imgBox.show(); 
				s.imgCheckBox.show();
				s.actionBtn.parent().show();
				s.busyMsk.mute();
				s.uploader.hide();
			}
			 
		},
		firstEditMode:function(){
			var s = this,$s=$(this.elment);
			s.noImgBox.hide();
			s.imgBox.hide();
			s.uploader.show()
			s.imgCheckBox.hide();
			s.actionBtn.parent().hide();
		},

		reviewMode:function(){
			var s =this,$s= $(this.element);
			$s.removeClass('selected')

			s.uploader.hide();
			if(s.imgBox.find('img').length<=0){
				s.imgBox.hide();
				s.noImgBox.show()
				s.imgCheckBox.hide();
				s.actionBtn.parent().hide();

			}else{
				s.imgBox.show(); 
				s.noImgBox.hide();
				s.imgCheckBox.hide();
				s.actionBtn.parent().hide();
			}
		},

		//更新图片
		updateImage:function(photo,id,isEditMode){
			var s = this,$s=$(this.element);
			s.imgBox.show();
			s.imgBox.find('img').remove();
			var img = $("<img>").attr({"src":photo,"photo-id":id}).appendTo(s.imgBox);

			if(isEditMode){
				s.imgCheckBox.show();
				s.actionBtn.parent().show();
				s.noImgBox.hide();
			}else{
				s.imgCheckBox.hide();
				s.actionBtn.parent().hide();
				s.noImgBox.hide();
			}
			img.each(function(){
				//if(this.complete)img.load();
			});

			$(img).load(function(){
				setTimeout(function () {
					$(img).centerInBox();
				},2000);
				
			});
		},
		initView:function(albumData,isEditMode){
			 
			if(albumData==null){
				$(this.element).removeClass('selected');
				this.imgBox.find('img').remove();
				this.noImgBox.show();
				this.uploader.hide();
				this.actionBtn.parent().hide();
				this.imgCheckBox.hide();
				if(isEditMode){
					this.uploader.show();
					this.noImgBox.hide();
				} 
				return;
			}else{
				 this.updateImage(albumData.thumbnail,albumData.id);
			}
		},
		sendToViewer:function(photoId){
			var s=this,$s=$(this.element);
			$s.trigger({type:s._E.send_to_viewer,photoId:photoId});		
		},
		sendToCropper:function(photoId){
			var s=this,$s=$(this.element);
			console.log("sendToCropper:");
			$s.trigger({type:s._E.send_to_imageCropper,photoId:photoId});		
		},
		removePhoto:function(photoIds){
			if(!photoIds)return;
			var s=this,$s=$(this.element);
			$.each(photoIds,function(i,e){
				if(e==s.photoId){
					s.imgBox.find('img').remove();
					s.photoId = null;
			 	 	s.editMode();
			 	 	s.showMessage("删除照片成功");
				}
			});

		},
		getPhotoId:function(){
			return this.options.albumData?this.options.albumData.id:null;
		},
		showUploadTimeout:function(){
			var s=this,$s=$(this.element);
			var layer = $("<div>").html("上传超时,请稍后再试").css({
				"position":"absolute",
				"left":"0px",
				"right":"0px",
				"top":"0px",
				"bottom":"0px",
				"text-align":"center",
				"background-color":"#F0F0E1",
				"color":"#222",
				"font-size":"14px",
				"line-height":$s.height()+"px"
			}).appendTo($s).hide().bringToFront().fadeIn('fast').delay(3000).fadeOut();
		},
		_E:{
			slect_state_change:"hyq/album/item/selectstate/change",
			send_to_viewer:"hyq/album/item/send_to_viewer",
			send_to_imageCropper:"hyq/album/item/send_to_image_cropper",
		},
		_create:function(){console.log('create------------');
			var s = this,$el = $(this.element);

			var uploadApi = this.options.uploadApi||null;

		 	if(!uploadApi)throw "Not enough parameters for hqy-album-uploader,please specify the upload api.";

		 	var fileField = this.options.fileFiledName||"image";

		 	var $el = $(s.element);

		 	s.imgBox = $el.find('.album-img-box');
		 	this.noImgBox = $el.find('.album-no-img-box');
		 	   
		 	s.actionBtn = $el.find('.album-action-btn em');
		 	s.imgCheckBox = $el.find(".album-item-checkbox");
		 	s.actionMenu = $el.find('.album-action-menu');
		 	s.actionMenu.hide();

		 	s.busyMsk = $el.find('.busy-mask').HYQBusyAnimate();
		 	s.busyMsk.mute();

		 	s.manager = this.options.manager;
		 	
		 	s.uploader = $el.find('.album-uploader')._HYQAlbumUploader({
		 		uploadApi:uploadApi,
		 		albumItem:s,
		 		albumStore:this.options.albumStore,
		 		fileFiledName:fileField
		 	});

		 	s.uploaderInstance = $el.find('.album-uploader').data('ui-_HYQAlbumUploader');

		 	s.uploader.on(_ALBUM_UPLODER_UPLOADING,function(event){
				//上传状态时不能退出编辑模式	
	  		 	s.busyMode(true);console.log(5333);
	  		});
	 
	  		s.uploader.on(_ALBUM_UPLODER_IMG_READY,function(event){
	  			//AlbumManager收到某个Uploader上传后传回来的信息
	  			s.manager.toggleBtnGroup(true);
	  			s.busyMsk.mute();
	  			//把新增的数据加到 Datastore
	  			var item = {
			        id : event._id,
			        thumbnail : event._thumbnail,
			        photo : event._photo
				}
				s.options.albumData = item;
				s.options.albumStore.setItem(item,s.options.pos,s);
	  			s.updateImage(event._thumbnail,event._id,(s.manager.element).hasClass('album-review')?false:true);
	  			s.manager.showMessage("上传照片成功！");
	  			s.manager.toggleBtnGroup(true);
	  		});

	  		s.imgCheckBox.click(function(){
	  			$el.hasClass("selected")?$el.removeClass('selected'):$el.addClass('selected');
	  			$el.trigger({type:s._E.slect_state_change});
	  		})
	  		s.actionBtn.click(function(event){
	  			$el.parent().find('.album-action-menu').hide();
	  			event.stopPropagation();	 
	  			s.actionMenu.show();
	  		});
	  		 
	  		$('html').click(function() {
				 s.actionMenu.hide();
			});

	  		//初始化视图
			s.initView(this.options.albumData,false);
 			s.imgBox.click(function(){
 				var target = s.imgBox.find('img');
 				if(target){
 					if(target.attr('photo-id')!=""){
 						photoId = target.attr('photo-id');
 						s.sendToViewer(photoId);
 						
 					}	
 				}
 			});

 			//接受上传失败的通知
 			s.uploader.on(_ALBUM_UPLODER_UPLOAD_ERR,function(event){
 				s.manager.showMessage("<label class='Rd'>"+event.message+"</label>");
 				s.busyMode(false);
 			});

 			//接收删除图片的通知
			$(s.options.albumStore.element).on("hyq/album/data/deleted",function(event){
					//AlbumItem接收到删除图片的通知
					s.manager.toggleBtnGroup(true);
					s.busyMsk.mute();
					if(s.getPhotoId()==null) return;
					var code = event.code; 
					var arr = event.deleted;//删除成功的ID

					$.each(arr,function(i,e){
						if(s.getPhotoId()==e){
							//开始清理界面
							s.options.albumData = null;
							s.initView(s.options.albumData,true);
						}
					});
			});

			$(s.options.albumStore.element).on("hyq/album/data/deletee",function(event){
				if(event.deletee ==null || event.deletee.length<=0) return;
				s.manager.toggleBtnGroup(false);
				$.each(event.deletee,function(i,e){
					
					if(e==s.getPhotoId()){
						console.log("[e:"+e+" === : === id:"+s.getPhotoId()+"]")
						console.log(s)
						s.busyMode(true);
					}
				});
			});

 			//弹出菜单 设为头像和删除
 			var menus = s.actionMenu.find(".album-action-menuitem");
 			 
 			$(menus[0]).click(function(){
 				var pid = s.getPhotoId();
 				if(pid!=null){
 					s.sendToCropper(pid);
 				}
 			});
 			$(menus[1]).click(function(){
 				var pid = s.getPhotoId();
 				if(pid!=null){
 					var arr = [];
 					arr.push(pid);
 					s.manager.deleteImages(arr);
 				}

 			});
 			//上传错误的处理
 			s.uploader.on(_ALBUM_UPLODER_UPLOAD_ERR,function(event){
            	s.manager.toggleBtnGroup(true);
            });
 			//照片数据源有数据被删除时的处理
 			$(s.options.albumStore.element).on("hyq/album/data/deleted",function(){
 				s.busyMode(false);
 			});

 			//上传超时的处理
 			s.uploader.on(_ALBUM_UPLODER_TIMEOUT,function(event){
				console.log("上传超时 ...");
				console.log(event);
				s.editMode();
				s.uploader.uploadMode();
				s.showUploadTimeout();
 			});

	  		return this;
		}
});
	 
	//Manager 
	$.widget("ui.HYQAlbumManager",{
		options:{
		 	uploadApi:null,
		 	fileFiledName:null,
		 	albumStore:null
		},
		_E:{
			show_photo:"hyq/album_manager/show_photo",
			cropper_avatar:"hyq/album_manager/cropper_avatar",
			delete_photos:"hyq/album_manager/delete_photos",
		},
		albumItems:[],
		albumItemInstances:[],
		editBtn:null,
		btnGroup:null,
		expendBtn:null,
		exitEditBtn:null,
		deleteBtn:null,
		messageBox:null,
		showMessage:function(message){
			$(this.messageBox).html(message).fadeIn().delay(3000).fadeOut();
		},
		editMode:function(){
			var s = this,$s = $(this.element);
			$s.addClass('album-edit').removeClass('album-first-edit').removeClass('album-review');
			$.each(s.albumItemInstances,function(i,e){
				 e.editMode();
			});
		},
		firstEditMode:function(){
			var s = this,$s = $(this.element);
			$s.removeClass('album-edit').addClass('album-first-edit').removeClass('album-review');
			$.each(s.albumItemInstances,function(i,e){
				e.editMode();
			});
			s.expendBtn.show();
		},
		reviewMode:function(){
			var s = this,$s = $(this.element);
			s.deleteBtn.hide();
			$s.removeClass('album-edit').removeClass('album-first-edit').addClass('album-review');
			$.each(s.albumItemInstances,function(i,e){
				e.reviewMode();
			});

		},
		updateSelectingState:function(){
			var s = this,$s = $(this.element);
			s.selectedItems=null;
			s.selectedItems =[];
			$s.find('.album-item').each(function(i,e){
				 
				if($(e).hasClass('selected')){
					try{
						s.selectedItems.push($(e).find(".album-img-box > img").attr('photo-id'));

					}catch(e){
						//do nothing
					}
				}
			});
			if(s.selectedItems.length>0){
				s.deleteBtn.show();
			}else{
				s.deleteBtn.hide();
			}
		},
		toggleBtnGroup:function(flag){
			flag?this.btnGroup.css({"visibility":"visible"}):this.btnGroup.css({"visibility":"hidden"});
			//看看有没有选定的照片
			var selectedItems = $(this.element).find(".album-wrapper li.selected");
			if(selectedItems.length>0){
				//尚有选定的照片 需要显示删除按钮
				this.deleteBtn.show();
			}else{
				//已经没有选定的照片 隐藏删除按钮
				this.deleteBtn.hide();
			}
 
			flag?this.exitEditBtn.show():this.exitEditBtn.show();
		},
		sendToViewer:function(photoId){

			var s = this,$s = $(this.element);
			$s.trigger({type:s._E.show_photo,photoId:photoId,holder:s});
		},
		sentToImgCropper:function(photoId){
			// console.log("传给截图器的ID");
			var s = this,$s = $(this.element);
			//在这里使用photoId获得图片地址，然后把把图片地址传给截图器
			var url = s.options.albumStore.getPhotoById(photoId);
			$s.trigger({type:s._E.cropper_avatar,imageUrl:url});
		},
		
		deleteImages:function(imageIds){			
			$(this.options.albumStore.element).trigger({type:"hyq/album/data/deletee",deletee:imageIds});
		} ,
		_create:function(){
			var s = this,$s = $(this.element);
			var uploadApi = this.options.uploadApi||null;
		 	if(!uploadApi)throw "params error:Not enough parameters for hqy-album-uploader,please specify the upload api.";
		 	if(!this.options.albumStore) throw "params error:no specified album data ";
		 	var params = s.options;
		 	params.manager = s;

			s.editBtn = $s.find(".profile-edit-btn");
			s.expendBtn = $s.find(".profile-expend-btn");
			s.exitEditBtn = $s.find(".exit-edit-btn");
			s.deleteBtn = $s.find(".delete-selected-photo-btn");
			s.messageBox = $s.find('.album-message-box');
			s.btnGroup = $s.find('.album-btn-box');
			var album = this.options.albumStore.options.albumData;
			 
			//修复对齐问题
			s.deleteBtn.css({"margin-right":"28px"}).hide();
		 	$s.find('.album-item').each(function(i,e){
			 	s.albumItems.push($(e));
			 	params.albumData = album[i];
			 	params.pos = i;
				 
				$(e)._HYQAlbumItem(params);

				s.albumItemInstances.push($(e).data("ui-_HYQAlbumItem"));
				$(e).on("hyq/album/item/selectstate/change",function(){
					s.updateSelectingState();
				});
				$(e).on("hyq/album/item/send_to_viewer",function(event){
					s.sendToViewer(event.photoId);
				});
				$(e).on("hyq/album/item/send_to_image_cropper",function(event){
					s.sentToImgCropper(event.photoId);
				});
			});

			//使用样式来控制页面刷新后的初始模式
			if($s.hasClass('album-first-edit')){
				s.firstEditMode();
			};

			if($s.hasClass('album-edit')){
				s.editMode();
			};

			if($s.hasClass('album-review')){
				s.reviewMode();
			};

			s.editBtn.click(function(){ s.editMode()});
				s.expendBtn.click(function(){ 
					 $(this).hide();
					s.editMode()
				});
				s.exitEditBtn.click(function(){
					if(s.options.albumStore.isAllNull()){
            				s.firstEditMode();
					}else{
						s.reviewMode();
					}
				});
            	s.deleteBtn.click(function(){
            	var ids=[];
            	$.each(s.albumItems,function(i,e){
            		 if(e.hasClass('selected')){
            		 	 
            		 	ids.push(s.albumItemInstances[i].getPhotoId());
            		 }
            	});
            	if(ids.length>0){
            		s.deleteImages(ids);
            	}

            });
            //删除返回结果后，在视图上显示状态
            $(this.options.albumStore.element).on("hyq/album/data/deleted",function(event){
            	// console.log("Datastore 删除图片后通知到 相册管理器的事件");
            	s.toggleBtnGroup(true);

            	if(event.code=="1000"){
            		s.showMessage('删除照片成功！');
					if(s.options.albumStore.isAllNull()){
						s.firstEditMode();
					}
            		$(document).trigger('hyq/userstate/changed');
            		return;
            	}
            	if(event.code=="1054"){
            		s.showMessage('<label class="Rdd">删除了部分照片，未能删除的请稍后再试。</label>');
            		return;
            	}
            	if(event.code=="0"){
            		s.showMessage('<label class="Rd">删除照片失败，请检查网络链接后再试。</label>');	
            		return;
            	}
            });
		}
	});
})(jQuery);