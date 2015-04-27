;(function($) {
	$.widget("ui._HYQAlbumViewportItem",{
		options:{
			albumStore:null,
			albumData:null,
			holder:null,
			pos:0,
			manager:null
		},
		imgBox:null,
		noImgBox:null,
		setViewFace:function(hasImage){
			if(hasImage){
				this.imgBox.show();
				this.noImgBox.hide();
			}else{
				this.imgBox.find('img').remove();
				this.imgBox.hide();
				this.noImgBox.show();
			}
		},

		setThumb:function(){
			var s = this;
			if(s.options.albumData!=null){
				s.setViewFace(true);
				s.imgBox.find('img').remove();
 
				var img  = $("<img>").attr({
					'src':s.options.albumData.thumbnail,
					'photoId':s.options.albumData.id
				})
					.appendTo(s.imgBox);
					
				img.each(function(){
						 if(this.complete) $(this).load();
				});

				$(img).load(function(){

					$(img).centerInBox()
				});

					
			}else{
				s.setViewFace(false);
			}
		},
		_create:function(){
			var s = this,$s = $(this.element);
			//s.imgBox = $s.find(".image-box");
			s.imgBox = $("<div>").addClass("image-box").appendTo($s);
			//s.noImgBox = $s.find(".no-image-box");
			s.noImgBox = $("<div>").addClass("no-image-box").appendTo($s);
			if(this.options.albumData){
				s.setThumb();
			}else{
				s.setViewFace(false);
			}

			s.imgBox.click(function(){
				if(s.options.albumData!=null){
					$s.addClass('selected');
					$s.trigger({type:"hyq/viewport/item/statechange",trigger:s});
				}
			});

			$s.parent().find('.thumb-item').on("hyq/viewport/item/statechange",function(event){
				if(event.trigger!=s){
					$s.removeClass('selected');
				}
			});

			$(s.options.albumStore.element).on("hyq/album/data/updated",function(event){
				//console.log("VIEWPORT 收到来自Album Datastore");
				 
				if(event.changeIndex == s.options.pos){
					s.options.albumData = s.options.albumStore.options.albumData[event.changeIndex];
					// console.log("##############################")
					// console.log(s.options.albumData);
					// console.log("##############################")
					s.setThumb();
				}
			});

			$(s.options.albumStore.element).on("hyq/album/data/deleted",function(event){
				// console.log("Album viewport 收到来自 album datastore删除完毕的信息");
				// console.log(s.options.albumData);
				if(event.deleted==null || event.deleted.length <=0) return;
				$.each(event.deleted,function(i,e){

					if(s.options.albumData!=null && e==s.options.albumData.id){
					 
						s.options.albumData = null;
						s.setThumb();

					}
				});
 
			}); 

			//收到需要显示照片的请求
			$(s.options.manager.element).on("hyq/album_manager/show_photo",function(event){	
				$s.find("img").centerInBox();
				if(typeof(s.options.albumData)==undefined || s.options.albumData == null) return;
				//console.log(s.options.albumData);
				if(event.photoId==s.options.albumData.id){
					$s.addClass('selected');
				}else{
					$s.removeClass('selected');
				}

			});
		}
	});

	$.widget("ui.HYQAlbumViewport", {
		options : {
			albumStore : null,
			initPhotoId : null,
			activePos:null,
			manager:null
		},
		//是否活动，用于判断当点击串口外区域是否关闭窗口
		_isActive:false,
		showImgClass:"hyq-album-viewport-shown-image",
		prevBtn : null,
		nextBtn : null,
		itemNodes : [],
		itemNodeInstances:[],
		photoBox : null,
		 
		showPhoto:function(url){

		 	var s = this,$s = $(this.element);
		 	$s.find("."+s.showImgClass).remove();
		 	var img = $("<img>").attr({
		 		"src":url,
		 		"class":s.showImgClass
		 	}).hide()

		 	.appendTo(s.photoBox);
		 	img.load(function(event){
		 		 
		 		$(this).centerInBox().bringToFront()
		 		.fadeIn('fast');
		 		//$(img).draggable();
					 		$(".photo-wrapper").mouseenter(function(event){
					 		//if(s.prevBtn.is(":hidden")){
					 			//s.prevBtn.fadeIn('fast');
					 			s.prevBtn.show();
					 		//}
					 		//if(s.nextBtn.is(":hidden")){
					 			//s.nextBtn.fadeIn('fast');	
					 			s.nextBtn.show();	
					 		//}
					 		event.stopPropagation();
						 	});

						 	s.prevBtn.mouseenter(function(){
						 		$(".photo-wrapper").mouseenter()
						 	});
						 	s.nextBtn.mouseenter(function(){
						 		$(".photo-wrapper").mouseenter()
						 	});

						 	$(".photo-wrapper").mouseleave(function(event){
						 		//if(!s.prevBtn.is(":hidden")){
						 			//s.prevBtn.fadeOut('fast');
						 			s.prevBtn.hide();
						 		//}
						 		//if(!s.nextBtn.is(":hidden")){
						 			//s.nextBtn.fadeOut('fast');	
						 			s.nextBtn.hide();	
						 		//}
						 		event.stopPropagation();
						 	});

		 	})
		 	.each(function(event){
		 		if(this.complete){
		 			$(this).load();
		 		}
		 	});

		 	

		 	

		},

		_create : function() {
			
			var s = this, $s = $(this.element);
			$s.focus();
			
			$s.find(".viewport-inner").prev().html("按Esc键关闭, 按键盘 &larr; 和 &rarr; 进行左右翻页");
			$s.find(".photo-wrapper img").remove();
			s.itemNodes = $s.find('.thumb-item');
			s.photoBox = $s.find(".photo-wrapper");
			s.prevBtn = $s.find(".go-prev"); 
			s.nextBtn = $s.find(".go-next");
			s.prevBtn.hide();
			s.nextBtn.hide();

			// s.prevBtn.mouseover(function(event){
			// 	event.stopPropagation();
			// });
			// s.nextBtn.mouseover(function(event){
			// 	event.stopPropagation();
			// });


			$s.attr({
				"tabindex":"1"
			})
			 
			var albums = s.options.albumStore.options.albumData;
			 
			s.photoBox.find('img').centerInBox();
			$.each(s.itemNodes,function(i,e){
				var n =  $(e)._HYQAlbumViewportItem({
				 	albumStore:s.options.albumStore,
				 	albumData:albums[i],
				 	holder:s,
				 	pos:i,
				 	manager:s.options.manager
				 });

				s.itemNodes.push(n);
				var nn = $(e).data("ui-_HYQAlbumViewportItem")
				$(n).on("hyq/viewport/item/statechange",function(event){
					if(nn.options.albumData!=null){
						s.showPhoto(nn.options.albumData.photo);
					}
				})
				s.itemNodeInstances.push(nn);
			});

			$(s.options.manager.element).on("hyq/album_manager/show_photo",function(event){	
				// console.log("想要显示某张图片");
				// console.log(event);
				s.showPhoto(s.options.albumStore.getPhotoById(event.photoId));
			});

			$(document).on("keydown",function(event) {

				if($s.is(":hidden")) return;
				event = event || window.event;
				var kcode = event.keyCode || event.which || event.charCode;
				// console.log("###########");
				// console.log(kcode);
				if (kcode == 27) {
					event.preventDefault();
					//console.log(kcode);
					$($s.parent()).trigger("hyq/modal/close");
				}

				if (kcode == 37 || kcode==39) {
					event.preventDefault();
					//console.log("上一张下一张");
					//上一张
					 var activeItem = $s.find(".thumb-item.selected");
					 
					 if(activeItem.length==0)return;//如果没有当前选定图，则不做任何操作直接返回。
					 	
					 if(kcode==37 && activeItem.prev().hasClass("thumb-item") && activeItem.prev().find(".image-box img").length>0){
					 	s.showPhoto(s.options.albumStore.getPhotoById(activeItem.prev().find(".image-box img").attr("photoId")));
					 	activeItem.removeClass("selected");
					 	activeItem.prev().addClass("selected");
					 }
					 else if(kcode==37 && activeItem.prev().find(".image-box img").length==0){
					 	var thumbItems = $s.find(".thumb-item");
		 			 	var lastItem =null,lastImgUrl = null ;
		 			 	$.each(thumbItems,function(i,item){
		 			 		if($(item).find(".image-box img").length>0){
		 			 			lastItem = $(item);
		 			 			lastImgUrl = s.options.albumStore.getPhotoById($(item).find(".image-box img").attr("photoId"));
		 			 		}
		 			 	});
		 			 	//找到最后一张
		 			 	if(lastItem!=null && lastImgUrl!=null){
		 			 		lastItem.addClass("selected");
		 			 		activeItem.removeClass("selected");
		 			 		s.showPhoto(lastImgUrl);
		 			 	}

					 }

					 if(kcode==39 && activeItem.next().hasClass("thumb-item") && activeItem.next().find(".image-box img").length>0){
					 	s.showPhoto(s.options.albumStore.getPhotoById(activeItem.next().find(".image-box img").attr("photoId")));
					 	activeItem.removeClass("selected");
					 	activeItem.next().addClass("selected");
					 }
					 else if(kcode==39 && activeItem.next().find(".image-box img").length==0){
					 	 var first = $s.find(".thumb-item")[0];
		 			 	 if($(first).find(".image-box img")){
		 			 	 	var url = s.options.albumStore.getPhotoById($(first).find(".image-box img").attr("photoId"));
		 			 	 	activeItem.removeClass("selected");
		 			 	 	$(first).addClass("selected");	
		 			 	 	s.showPhoto(url);
		 			 	 }
						
					 }
				}

			});
 
	 		//上一张下一张的箭头
	 		//上一个按钮


	 		s.prevBtn.click(function(){
	 			console.log("CLICK PREV ");
	 			 var activeItem = $s.find(".thumb-item.selected");
	 			 if(activeItem.length>0 && activeItem.prev().hasClass("thumb-item") && activeItem.prev().find(".image-box img").length>0){
	 			 	s.showPhoto(s.options.albumStore.getPhotoById(activeItem.prev().find(".image-box img").attr("photoId")));
	 			 	activeItem.removeClass("selected");
					activeItem.prev().addClass("selected");
	 			 }else{
	 			 	var thumbItems = $s.find(".thumb-item");
	 			 	var lastItem =null,lastImgUrl = null ;
	 			 	$.each(thumbItems,function(i,item){
	 			 		if($(item).find(".image-box img").length>0){
	 			 			lastItem = $(item);
	 			 			lastImgUrl = s.options.albumStore.getPhotoById($(item).find(".image-box img").attr("photoId"));
	 			 		}
	 			 	});
	 			 	//找到最后一张
	 			 	if(lastItem!=null && lastImgUrl!=null){
	 			 		lastItem.addClass("selected");
	 			 		activeItem.removeClass("selected");
	 			 		s.showPhoto(lastImgUrl);
	 			 	}
	 			 }
	 		});

	 		//下一个按钮
	 		s.nextBtn.click(function(){
	 			console.log("CLICK NEXT ");
	 			 var activeItem = $s.find(".thumb-item.selected");

	 			 if(activeItem.length>0 && activeItem.next().hasClass("thumb-item") && activeItem.next().find(".image-box img").length>0){
	 			 	s.showPhoto(s.options.albumStore.getPhotoById(activeItem.next().find(".image-box img").attr("photoId")));
					activeItem.removeClass("selected");
					activeItem.next().addClass("selected");
	 			 }else{
	 			 	//
	 			 	 var first = $s.find(".thumb-item")[0];
	 			 	 if($(first).find(".image-box img")){
	 			 	 	var url = s.options.albumStore.getPhotoById($(first).find(".image-box img").attr("photoId"));
	 			 	 	activeItem.removeClass("selected");
	 			 	 	$(first).addClass("selected");	
	 			 	 	s.showPhoto(url);
	 			 	 }
					
	 			 }
	 		});

	 		$s.parent().on("hyq/modal/shown",function(){
	 			s._isActive = true;
	 		});
	 		$s.parent().on("hyq/modal/hidden",function(){
	 			s._isActive = false;
	 		})

	 		 //点击旁边的时候隐藏
           $("html").click(function(event){
           	console.log("Document was clicked");
           	if(s._isActive){
           		$s.parent().trigger("hyq/modal/close");
           	}
           });
           
           $s.click(function(event){
           		event.stopPropagation();
           });

		 
		}
	});

})(jQuery); 