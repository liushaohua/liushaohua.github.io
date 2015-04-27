;(function($){
	$.widget("ui.HYQAlbumDatastore",{
		options:{
			albumData:null,
			deleteApi:null,
			delRequstFieldName:'photo_id_arr',
		},
		_E:{
			deleted_in_server:"hyq/album/data/deleted_in_server",//在服务器中删除照片成功
			deleted:"hyq/album/data/deleted",//在Datastore中删除照片成功
			deletee:"hyq/album/data/deletee",//删除照片的通知（应该发自AlbumManager）
			load:"hyq/album/data/load",//载入数据
			 
			updated:"hyq/album/data/updated"//更新数据状态成功 
		},

		//判断每个图片位是否都为空
		isAllNull:function(){
			var res = null;
			$.each(this.options.albumData,function(i,e){
				res = res || e;
			});

			return (res==null)?true:false;
		},
		//传入ID获得大图路径
		getPhotoById:function(pid){
			// 外界发到Album DataStore 请求通过ID获得大图
			var pt = null;
			$.each(this.options.albumData,function(i,e){
				 if(e!=null){
				 	if(e.id==pid){
				 		 pt=e.photo;
					}
				 }
			});
			return pt;
		},
		//私有方法 删除传进来的 id数组 对应的照片
		_delete:function(photoIdArr){
			var s  = this,$s = $(this.element),
			    param = JSON.stringify(photoIdArr);
			$.get(this.options.deleteApi+"&"+this.options.delRequstFieldName+"="+param,function(data){
				try{
					var back = eval('('+data+')');
					if(back.code == "1000"){
						$s.trigger({type:s._E.deleted_in_server,code:"1000",deleted:back.data});
						return;
					}
					if(back.code=="1054"){
						$s.trigger({type:s._E.deleted,code:"1054",deleted:back.data})
					}
				}
				catch(e){
					//发送删除请求失败
					$s.trigger({type:s._E.deleted,code:"0",deleted:null});
				}
			});
		},
		//对外的公共方法 删输入的ID数组对应的照片
		deleteByIds:function(photoIdArr){
			this._delete(photoIdArr);
		},
		setAlbum:function(_album){
			this.album = this.options.albumData = _album ;
			$(this.element).trigger({type:this._E.load,album:this.album});
		},

		//更新六张图的某一张
		setItem:function(item,pos,trigger){
			this.options.albumData[pos] = item;
			$(this.element).trigger({type:"hyq/album/data/updated",dataStore:this,trigger:trigger,changeIndex:pos});
		},

		//清空指定图片ID的项目
		_clearItemById:function(id){
			var s = this,$s = $(this.element);
			for(var i=0;i<s.options.albumData.length;i++){
				if(s.options.albumData[i]!=null){

					if(s.options.albumData[i].id==id){
						s.options.albumData[i]=null;
					}
				}
			}
		},
		//把已经删除的项目设置为null
		_setDeletedItems:function(deleteds){
			var s = this,$s = $(this.element);
			if(deleteds!=undefined && deleteds.length>0){
				$.each(deleteds,function(i,e){
					s._clearItemById(e);

				});
			}
		},
		//构造函数
		_create:function(){
			var s = this,$s = $(this.element);
			if(!this.options.albumData)
				throw "params error:No rawdata for red albumn";
			if(!this.options.deleteApi)
				throw "params error:You must specify a delete api";
			 
			$s.trigger({type:this._E.load,album:this.album});
			$s.on(this._E.deletee,function(event){
				 //AlbumStore 收到外界传来的删除记录的信息
				 s.deleteByIds(event.deletee);
			});

			//从服务器上删除返回
			$s.on(s._E.deleted_in_server,function(event){
				s._setDeletedItems(event.deleted);
				//通知相关的WIDGET删除成功变传给他们删除成功的id
				$s.trigger({type:s._E.deleted,code:"1000",deleted:event.deleted});
			});

		}
	});

})(jQuery);