;(function($){
	$.extend({
		_HYQEVT_:{
			RR:{
				BABY_CLICKED:"hyq/rr/baby/click",
				//BABY_STATE_CHANGED:"hyq/rr/baby/state/changed",
				TAGS_REDUCED:"hyq/rr/baby/reduced",
				TAGS_INCREASED:"hyq/rr/tags/increased",
				TAGS_FULL:"hyq/rr/tags/full",
				TAGS_RENDERING:"hyq/rr/tags/rendering"
			}
		},
		 _HYQ_MAX_LEN:function(field,maxlimit)
        {
             var str = field.value.replace(/[^\x00-\xff]/g,"**").length;
             
             var tempString=field.value;
             var tt="";
             if(str > maxlimit)
             {
                 for(var i=0;i<maxlimit;i++)
                 {
                    //if(tt.replace(/[^\x00-\xff]/g,"**").length < maxlimit)
                    if(tt.length < maxlimit)
                    tt = tempString.substr(0,i+1);
                    else
                    break;
                 }
                 //if(tt.replace(/[^\x00-\xff]/g,"**").length > maxlimit)
                 if(tt.length > maxlimit)
                 //tt=tt.substr(0,tt.length-2);

                 field.value = tt;
               }
               else
               {
                     ;
               }
        } 
	});
//*********************  
//统管整个角色管理器 ================================================ HYQRoleManager 
var _HYQ_RR_MAX_ROLE = 5;
var _HYQ_RR_MAX_CUSTOM_ROLE = 1;
var _HYQ_RR_ROLETYPE_SYS = 0;
var _HYQ_RR_ROLETYPE_CUSTOM = 1;
var _HYQ_FAKE_CUSTOM_TAG = "CUSTOM_TAG";
$.widget("ui.HYQRoleManager",{
	options:{
		model:null,

	},
	tagsBox:null,
	tagsBoxInstance:null,
	maker:null,
	msgBox:null,
	tags:[],//临时存储的保存数据
	tags_end:[],//最终渲染的数据
	showErrorMessage:function(msg){
		var newMsg = "<label class='Rd'>"+msg+"</label>";
		this.showMessage(newMsg);
	},
	showMessage:function(msg){
		$('.rr-flash-notice').html(msg).fadeIn().delay(3000).fadeOut();
	},
	getAjaxModel:function(){
		//将临时tags 放到最终中tags_end中    然后进行渲染---
		this.tags_end = this.tags.slice(0);
		var ts = this.tags_end;
		//console.log(this.tags)
		var aj =  {
			userRoles:[],
			userCustomRoles:[]
		}
		$.each(ts,function(i,e){
			if(e.id!=_HYQ_FAKE_CUSTOM_TAG){
				aj.userRoles.push(e.id)
			}else{
				aj.userCustomRoles.push(e.name)
			}
		});

		//console.log(aj)
		return aj;
	},
	_getModelById:function(id){
		if(this.options.model.roles==null) return null;
		if(this.options.model.roles.length==0) return null;
		 
		var model = null;
		for(var key in this.options.model.roles){
			 
			if(key==id){
				model =  this.options.model.roles[id];
			}
			if(this.options.model.roles[key].child){
				 
				 $.each(this.options.model.roles[key].child,function(i,e){

				 		if(e.id==id){
				 			model = e;
				 			 
				 		}
				 			
				 })
			}
		} 
		 //console.log('通过id获取model详情000--wangyifan')
		 //console.log(model)
		 return model;
		
	},
	render:function(data){
		console.log('data初始化的数据')
		console.log(data)
		this.renderin(data.userRoles,data.userCustomRoles);
	},
	renderCheck : function (data) {
		var Roles = data.userRoles;
			setTimeout(function () {
				BoxLen = $('.rr-children-box').length;
				for (var i = 0,len = Roles.length; i < len; i++) {
					for (var j = 0,jLen = BoxLen; j < jLen; j++) {console.log('aa');
						var cCheck = $('.rr-children-inner').eq(j).find('.hyq-nice-checkbox');
						for (var w = 0,wLen = cCheck.length; w < wLen; w++) {
							if (cCheck.eq(w).val() == +Roles[i]) {
								if (cCheck.eq(w).attr('parent_id') != 0) {
									cCheck.eq(w).parent().siblings().find('input').each(function (i) {
										var $this = $(this);
										if ($this.attr('parent_id') == 0) {
											$this.prop('disabled','disabled');
										}
									});	
								} else {
									cCheck.eq(w).parent().siblings().find('input').prop('disabled','disabled');
								}
								
								break;
							}
						}
					}
					
					
				}

			},100);
	},
	renderin:function(userRoles,userCustomRoles){
		var s = this,$s = $(this.element);  
		if(userRoles==null||userCustomRoles==null) return;

		try{
			var tags = [];
			for(var i=0;i<userRoles.length;i++){
				var model = s._getModelById(userRoles[i]);

				  if(model){
				  	tags.push(model);
				  }
			}
			 
			for(var j=0;j<userCustomRoles.length;j++){
				 if(userCustomRoles[j]){
				 	var model = {
				 		id:_HYQ_FAKE_CUSTOM_TAG+((j==0)?"":j),
				 		name:userCustomRoles[j],
				 		parent_id:0
				 	}
				 	tags.push(model);
				 }
			}
			s.tags = tags;
			s.tags_end = s.tags.slice(0);

			//通知要渲染
			$s.trigger({
				type:$._HYQEVT_.RR.TAGS_RENDERING,
				tags:tags
			});
		}catch(e){
			//console.log(e);
			throw "初始化红角色数据出错";
		}

	},
	addTagByModel:function(model){
		var s = this,$s = $(this.element);
		var isAdded = false;
		if(s.tags.length>0){
			//判断是否存在  存在就不加了
			$.each(s.tags,function(i,item){
				if(item.id==model.id){
					isAdded = true;
				}
			});	
		}
		if(!isAdded){
			//该id tags里不存在  加
			s.tags.push(model);
		}
		console.log(isAdded)
		return isAdded;
	},
	ifHasCustomTag:function(){
		var s = this;
		if(!this.tags) return false;
		if(this.tags.length<=0) return false;

		for(var i=0;i<this.tags.length;i++){
			if(s.tags[i].id==_HYQ_FAKE_CUSTOM_TAG){
				return true;
			}
		}
		return false;
	},
	isDaddyHasChildren:function(dadyId){
		var babes =$s.find("input[parent_id="+parent_id+"]");
		return babes.length;
	},
	anyBabyChecked:function(parent_id){
		var babes = $s.find("input[parent_id="+parent_id+"]:checked");
		if(babes==null) return false;
		if(babes.length<=0) return false
			else return true;

	},
	isDadayChecked:function(id){
		var dady = $.find("input[name=rroleItem]:checked");
		if(dady==null || dady.length==0) return false;

		else{
			$.each(dady,function(i,e){
				if($(e).val()==id) return true;
			});
		}
		return false;
	},
	prinTagsId:function(){
		var arr = [];
		$.each(this.tags,function(i,e){
			arr.push(e.id);
		});
		var str  =arr.join("-");
		return str;
	},
	removeTagByModel:function(model){
		var s = this,$s = $(this.element);
		var removeIndex =-1;
		$.each(s.tags,function(i,item){
			if(item.id==model.id){
				//找到需要移除的ID对象的下标
				removeIndex = i;
			}
			
		});
		if(removeIndex>=0){
			delete s.tags.splice(removeIndex,1);
			return true;
		}else{
			return false;	
		}
		
	},
	_create:function(){
		var s = this,$s = $(this.element);
		s.maker = $s.find(".rr-maker");
		s.maker.HYQRoleMaker({
			model:s.options.model,
			manager:s
		});

		s.tagsBox = $(".rr-tag-box").HYQRoleTagsBox({
			maker:s.maker.data('ui-HYQRoleMaker'),
			model:s.options.model,
			manager:s
		});
		s.tagsBoxInstance = s.tagsBox.data("ui-HYQRoleTagsBox");

		$s.on($._HYQEVT_.RR.TAGS_INCREASED,function(event){
			if(s.tags.length>=5)$s.trigger({type:$._HYQEVT_.RR.TAGS_FULL,tags:s.tags});	
		})

		$s.on($._HYQEVT_.RR.BABY_CLICKED,function(event){
			console.log('222222click')
			if(event.checked){
				//先判断tags的个数
				if(s.tags.length<5){
					s.addTagByModel(event.model);
					$s.trigger({type:$._HYQEVT_.RR.TAGS_INCREASED,tags:s.tags,model:event.model});	
				  	
				}
			}
			//取消选中
			else{
				//尝试删除
				var reduced = s.removeTagByModel(event.model);
				if(reduced){
					$s.trigger({type:$._HYQEVT_.RR.TAGS_REDUCED,reduced:event.model,tags:s.tags});
				}
				console.log("删除后为"+s.prinTagsId());
			}
		});
	}
});
// ============================================================== HYQRoleTagsBox
$.widget("ui.HYQRoleTagsBox",{
	options:{
		maker:null,
		model:null,
		manager:null
	},
	emptyNotice:null,
	fullNotice:null,
	spliter:null,
	tagsBox:null,
	tagsWrapper:null,
	removeAll:function(){
		this.tagsBox.find("span.selected-role").remove();
	},
	removeTagByModel:function(model){
		var s = this,$s = $(this.element);
		//remove element trigger event
		var reduced = s.options.manager.removeTagByModel(model);
		if(reduced){

			$(s.options.manager.element).trigger({type:$._HYQEVT_.RR.TAGS_REDUCED,reduced:model,tags:s.options.manager.tags});
			return true;
		}
		return false;
	},
	
// <span class="selected-role inline-block"><span class="role-value"></span> &nbsp;&nbsp;<a class="close-btn-tiny"></a></span>
	createRoleTag:function(model){
		var s = this,$s = $(this.element);
		var tag = $("<span>").addClass("selected-role inline-block")
		.attr({"_id":model.id,"custom-role":""})
		.html("<span class='role-value'>"+model.name+"</span>&nbsp;&nbsp;<a class='close-btn-tiny'></a>")
		.appendTo(s.tagsBox).fadeIn(function(event){
			 $(this).find(".close-btn-tiny").click(function(){
			 		if(s.removeTagByModel(model)==true){
			 			$(this).parent().fadeOut(function(event){
			 				$(this).remove();
			 			})
			 		}

			 });

		}); 
	},

	_create:function(){
		var s = this,$s = $(this.element);
		s.emptyNotice = $s.find('.rr-empty-notice');
		s.hasNotice = $s.find('.rr-has-notice');
		s.fullNotice = $s.find('.rr-full-notice');
		// s.spliter = $s.find('.gray-spliter');
		s.tagsBox = $s.find(".rr-role-value-box");
		s.tagsWrapper = s.tagsBox.parent();
		//标签减少时
		$(s.options.manager.element).on($._HYQEVT_.RR.TAGS_REDUCED,function(event){
			if(event.tags.length <=0){
				s.hasNotice.fadeOut(function(){
					s.emptyNotice.fadeIn()	
				});
				
			}else{
				s.emptyNotice.fadeOut(function(){
					s.hasNotice.fadeIn();
				});
			}
			//开删
			var tags = s.tagsBox.find(".selected-role[_id="+event.reduced.id+"]");
			if(tags)tags.remove();
			//if()
		});
		//标签增加时
		$(s.options.manager.element).on($._HYQEVT_.RR.TAGS_INCREASED,function(event){
			if(event.tags.length <=0){
				s.hasNotice.fadeOut(function(){
					s.emptyNotice.fadeIn()	
				});
				
			}else{
				s.emptyNotice.fadeOut(function(){
					s.hasNotice.fadeIn();
					s.tagsWrapper.fadeIn();
				});
				
			}
			s.createRoleTag(event.model);



		});

		//当有渲染数据的通知的时候
		$(s.options.manager.element).on($._HYQEVT_.RR.TAGS_RENDERING,function(event){
			console.log("//当有渲染数据的通知的时候111");
			console.log(event.tags)
				for(var i=0;i<event.tags.length;i++){
					if(event.tags[i].id==_HYQ_FAKE_CUSTOM_TAG){
						$(s.options.manager.element).trigger({
							type:$._HYQEVT_.RR.TAGS_INCREASED,
							tags:event.tags,
							model:event.tags[i]
						});
					}
				}
		});
 


	}
});

// RR MAKER 从这里开始 ============================================= HYQRollMaker
var ARR_R = "ic-chevron-rt-white";
var ARR_D = "ic-chevron-sm";
$.widget("ui.HYQRoleMaker",{
	options:{
		model:null,
		manager:null
	},
	//跟外界通讯的事件
	soles:null,
	_E:{

	},
	selectBtn:null,
	selector:null,
	selectBtnArrow:null,
	dadBox:null,
	customRRMaker:null,
	items:[],
	itemInstances:[],
	_toggleSelectBtnArr:function(){
		var s = this;
		if(s.selectBtnArrow.hasClass(ARR_R)){
			s.selectBtnArrow.removeClass(ARR_R).addClass(ARR_D);
			s.selectBtn.css({"color":"#efefef","background":"#666"});
			s.selector.css({"visibility":"visible"});
			s.selector.show();

		}else{
			s.selectBtnArrow.addClass(ARR_R).removeClass(ARR_D);
			s.selectBtn.css({"color":"#fff","background":"#808080"});
			s.selector.css({"visibility":"hidden"});
			s.selector.hide();
		}
	},
	
	_create:function(){
		 
		var s = this,$s = $(this.element);
		s.selectBtn = $s.find(".rr-select-btn");
		s.dadBox = $s.find(".rr-list-inner");
		s.selector = $s.find('.rr-selector');
		s.selectBtnArrow = $s.find(".rr-select-btn > i");

		s.customRRMaker = $s.find("[name=rr-custom-role-maker]");

		$s.find("*").click(function(e){
			if($(e.target||e.srcElement).hasClass("hyq-ic")){
				return;
			}else{
				e.stopPropagation();
			}
			
		});
		$('html').click(function(e){
			s.selectBtnArrow.addClass(ARR_R).removeClass(ARR_D);
			s.selectBtn.css({"color":"#fff","background":"#808080"});
			s.selector.css({"visibility":"hidden"});
			s.selector.hide();
			e.stopPropagation();
		});
		s.selectBtn.click(function(event){
			s._toggleSelectBtnArr();	
			return false;
			event.stopPropagation();
		});

		s.roles = s.options.model.roles;
		var order = 0;
		for(var key in s.roles){
			//创建一个下拉列表项
			var item = $("<div>")
			.html(s.roles[key].name)
			.addClass('rr-list-item')
			.appendTo(s.dadBox);
			
			s.items.push(item);
			item.RRDadItem({
				order:order,
				model:s.roles[key],
				dadBox:s.dadBox,
				maker:this,
				manager:s.options.manager
			});
			var itemInstance = item.data("ui-HYQRoleMaker");
			s.itemInstances.push(itemInstance);
			order++;
		};
		//单击事件 
		$('#role_save_btn').on('click',function(){
			
				if(s.options.manager.tags>=5){
						return;	
					}
				if(s.customRRMaker.val()!=""){
					//创建标签 
					var cModel = {
						id:_HYQ_FAKE_CUSTOM_TAG,
						name:s.customRRMaker.val(),
						parent_id:0
					};
					if(!s.options.manager.addTagByModel(cModel)){
						// console.log('回车执行了222-keypress-wangyifan')
						$(s.options.manager.element).trigger({
							type:$._HYQEVT_.RR.TAGS_INCREASED,
							tags:s.options.manager.tags,
							model:cModel
						});
						if(s.options.manager.tags.length>=5){
							 $(s.options.manager.element).trigger({type:$._HYQEVT_.RR.TAGS_FULL,tags:s.options.manager.tags});	
						}
						s.customRRMaker.val("");
					}
				}
			
		}) ;
		//处理自定义标签输入框
		s.customRRMaker.keypress(function(event){
			//不能超过6个字
			//if($._HYQ_GET_LEN(s.customRRMaker.val())>12) return false;
			if(event.keyCode==13){
				//console.log('回车执行了111-keypress-wangyifan')
				if(s.options.manager.tags>=5){
						return;	
					}
				if(s.customRRMaker.val()!=""){
					//创建标签 
					var cModel = {
						id:_HYQ_FAKE_CUSTOM_TAG,
						name:s.customRRMaker.val(),
						parent_id:0
					};
					if(!s.options.manager.addTagByModel(cModel)){
						//console.log('回车执行了222-keypress-wangyifan')
						$(s.options.manager.element).trigger({
							type:$._HYQEVT_.RR.TAGS_INCREASED,
							tags:s.options.manager.tags,
							model:cModel
						});
						//
						if(s.options.manager.tags.length>=5){
							 $(s.options.manager.element).trigger({type:$._HYQEVT_.RR.TAGS_FULL,tags:s.options.manager.tags});	
						}
						s.customRRMaker.val("");
					}
				}
			}
		}) ;

		// //为IE而搞
		// document.getElementsByName('rr-custom-role-maker')[0].onpropertychange=function(event){
		// 	var that = event.target||event.srcElement;
		// 	console.log(event);
			 
		// };

		// document.getElementsByName('rr-custom-role-maker')[0].oninput= function(event){
		// 	console.log(event);
		// 	var that = event.target||event.srcElement;
			 
		// }

		//标签增加
		$(s.options.manager.element).on($._HYQEVT_.RR.TAGS_INCREASED,function(){
			//如果有自定以标签 则隐藏输入框
			if(s.options.manager.ifHasCustomTag()){
				s.customRRMaker.hide();
				$('#role_save_btn').hide();
			}else{
				s.customRRMaker.val("");
				s.customRRMaker.show();
				$('#role_save_btn').show();
			}
		});

		//标签减少
		$(s.options.manager.element).on($._HYQEVT_.RR.TAGS_REDUCED,function(){
			//如果有自定以标签 则隐藏输入框
			if(s.options.manager.ifHasCustomTag()){
				s.customRRMaker.hide();
				$('#role_save_btn').hide();
			}else{
				s.customRRMaker.val("");
				s.customRRMaker.show();
				$('#role_save_btn').show();
			}
		});

		//标签满
		$(s.options.manager.element).on($._HYQEVT_.RR.TAGS_FULL,function(){
			s.customRRMaker.val("");
				s.customRRMaker.hide();
				$('#role_save_btn').hide();//加号按钮
		});



	}
});
//父亲容器
$.widget("ui.RRDadItem",{
	options:{
		model:null,
		dadBox:null,
		maker:null,
		manager:null,
		order:null
	},
	holder:null,
	model:null,
	dadBox:null,
	childrenBox:null,
	childrenBoxInstance:null,
	makeChildren:function(){
		var s = this,$s =$(this.element);
		
		s.childrenBox = $("<div>")
			.appendTo(this.dadBox)
			.addClass('rr-children-box');
		s.childrenBox.css({
			"position":"absolute",
			"left":$s.position().left,
			"top":s.options.order*41+"px",
			"width":"180px",
			"height":$s.height()+"px",
			"background-color":"transparent"
		});

		 s.childrenBox.RRChildrenItem({
				model:this.model,
				holderNode:$(this.element),
				holder:this,
				dadBox:this.dadBox,
				manager:s.options.manager
			});
		 s.childrenBox.hide();
		 s.childrenBoxInstance = $(s.childrenBox).data("ui-RRChildrenItem");
	},
	_create:function(){
		var s = this,$s = $(this.element);
		console.log($(this.element));
		s.dadBox = s.options.dadBox;
		s.model = s.options.model;
		s.holder = s.options.maker;
		s.makeChildren();

		$s.mouseover(function(){
			$s.show();
			//$s.css("border","1px solid #ff3300");
			s.childrenBoxInstance.adjustBabyBoxHieght();
			$(s.options.manager.element).trigger({type:"hyq/rrole/dad/show",dad:s});
		});

		// $s.mouseout(function(){
		// 	$s.css("border","1px solid #ff3300");
		// });
		$(s.options.manager.element).on("hyq/rrole/dad/show",function(event){
			if(event.dad!=s){
				if(s.childrenBox!=null)
					s.childrenBox.hide();
			}else{
				 s.childrenBox.show();
			}
		});
	}
});

$.widget("ui.RRChildrenItem",{
	options:{
		model:null,
		holderNode:null,
		holder:null,
		dadBox:null,
		manager:null
	},
	model:null,
	holderNode:null,
	holder:null,
	dadBox:null,
	wrapperBox:null,
	babyBox:null,
	adjustBabyBoxHieght:function(){
		var s = this,$s = $(this.element);
		s.wrapperBox.css({
			"position":"absolute",
			"left":s.holderNode.width()-50+"px",
			"border":"#ff3300",
			"height":s.dadBox.height()-1+"px",
			"width":"480px",
			"display":"block",
			"background-color":"#fff",
			"scroll-x":"hidden",
			"scroll-y":"auto",
			"border":"1px solid #ff3300",
			//"top":"-"+$s.position().top-1+"px"

		});
	},
	_create:function(){
		var s = this,$s = $(this.element)
		s.holderNode = s.options.holderNode;
		s.holder = s.options.holder;
		s.dadBox = s.options.dadBox;
		s.model = this.options.model;

		var bbox = s.wrapperBox= $("<div>").css({
			"position":"absolute",
			"left":s.holderNode.width()-50+"px",
			"border":"#ff3300",
			"height":(s.dadBox.height()-1)+"px",
			"width":"480px",
			"display":"block",
			"background-color":"#fff",
			"scroll-x":"hidden",
			"scroll-y":"auto",
			"border":"1px solid #ff3300",
			// "top":"-"+$s.position().top-1+"px",
			"top":"-"+s.holder.options.order*41-1+"px",
			"z-index":"35"

		}).addClass('rr-children-inner').appendTo($s);

		 $("<div>").css({
			"position":"absolute",
			//"height":s.holderNode.height()+"px",
			"left":"158px",
			"width":"2px",
			"display":"block",
			"height":"40px",
			"background-color":"#fff",
			"z-index":"40"

		})
		 .addClass("white-sticky")
		 .appendTo($s);

		 s.babyBox = $("<div>").addClass("rr-baby-box").css({
		 	"height":bbox.height()
		 }).appendTo(bbox);

		 $("<div>").addClass("rr-baby-item rr-daddy").appendTo(s.babyBox).RRBaby({
		 	model:s.model,
		 	holder:s,
		 	isDad:true,
		 	manager:s.options.manager
		 });
		 if(s.model.child!=null){
		 	$.each(s.model.child,function(i,e){
		 		$("<div>").addClass('rr-baby-item rr-baby').appendTo(s.babyBox).RRBaby({
		 			model:e,
				 	holder:s,
				 	isDad:false,
				 	manager:s.options.manager
		 		});
		 	});
		 }
		 
		  

	}
});

$.widget("ui.RRBaby",{
	options:{
		model:null,
		holder:null,
		isDad:null,
		manager:null

	},
	model:null,
	hyqCheckbox:null,
	_create:function(){
		var s =this,$s = $(this.element);
		this.model = this.options.model;
		 	s.isDad = s.options.isDad;
		 	var input  = s.inputBox = $("<input>").appendTo($s);
			input.addClass("hyq-nice-checkbox")
			.attr({"type":"checkbox","name":"rroleItem","parent_id":this.model.parent_id})
			.val(this.model.id);
			var span = $("<span>").addClass("hyq-checkbox").insertAfter(input);
			var label = $("<label>").html(s.model.name).insertAfter(span);
			 setTimeout(function(){
			 	input.HYQCheckbox();
			 },100);
			 input.on("hyq/checkbox/click",function(){
			  	$(s.options.manager.element).trigger({
			  		type:$._HYQEVT_.RR.BABY_CLICKED,
			  		parent_id:s.model.parent_id,
			  		id:s.model.id,
			  		model:s.options.model,
			  		wrapper:$s.parent(),
			  		holder:s,
			  		roleType:_HYQ_RR_ROLETYPE_SYS,
			  		inputNode:s.inputBox,
			  		checked:input.prop('checked')
				});
			 });
		 	//Baby被点了 处理要不要显示的问题
		 	$(s.options.manager.element).on($._HYQEVT_.RR.BABY_CLICKED,function(event){
		 		//被选中，要判断需要限制什么
		 		 if(event.checked){
		 		 	//是爹
		 		 	if(event.parent_id==0){
		 		 		//把所有爹是event.id的都disabled
		 		 		if(s.options.model.parent_id==event.id){
		 		 			s.inputBox.prop('disabled',true);
		 		 		}
		 		 	}
		 		 	//是崽子
		 		 	else{
		 		 		//把所有爹是event.id的都disabled
		 		 		if(s.options.model.id==event.model.parent_id){
		 		 			s.inputBox.prop('disabled',true);
		 		 		}
		 		 	}
		 		 }
		 		 //被取消选中，要判断要取消什么限制
		 		 else{
		 		 	//是爹
		 		 	if(event.parent_id==0){
		 		 		//把所有爹是event.id的都取消disabled
		 		 		if(s.options.model.parent_id==event.id){
		 		 			s.inputBox.prop('disabled',false);
		 		 		}
		 		 	}
		 		 	//是崽子
		 		 	else{

						//如果所有崽子都取消选择了，把所有爹是event.id的都取消disabled
						//先判断所有相关的崽子是不是都取消选择了
						var babs = event.wrapper.find('.rr-baby input:checked').length;
						//然后再决定要不要给REDUCE爹解禁
		 		 		if(s.options.model.id==event.model.parent_id && babs==0){
		 		 			s.inputBox.prop('disabled',false);
		 		 		}

		 		 	}
		 		 }
		 		 
		 	});

			//当标签满额时 进行自我检查 如果是未选中且又是可选的 赶紧不让选
			$(s.options.manager.element).on($._HYQEVT_.RR.TAGS_FULL,function(event){
				 if(s.inputBox.prop("disabled")==false && s.inputBox.prop("checked")==false){
				 	s.inputBox.prop("disabled",true);
				 }
				
			});

			// //当标签减少时 进行自我检查，判断需要给那些标签解禁 让崽子们自己判断自己要怎么显示
			$(s.options.manager.element).on($._HYQEVT_.RR.TAGS_REDUCED,function(event){
				//标签被删除
				//取消选定直接相关的TAG
				if(event.reduced.id==s.options.model.id && s.inputBox.prop("checked")==true){
					s.inputBox.prop("checked",false);
				}


				 if(s.isDad && s.inputBox.prop("disabled")==true){
				 	 if($(s.options.manager.element).find(".rr-baby input[parent_id="+s.options.model.id+"]:checked").length<=0){
							s.inputBox.prop("disabled",false);
							$(s.options.manager.element).find(".rr-baby input[parent_id="+s.options.model.id+"]").prop("disabled",false);
					}
				 } 

				 if(!s.isDad&&s.inputBox.prop("disabled")==true){
				 	 
				 	//兄弟们有至少一个被选中了么 如果有 我直接解禁 如果没有 我和爹一起解禁
				 	if($(s.options.manager.element).find(".rr-baby input[parent_id="+s.options.model.parent_id+"]:checked").length>0 &&
				 		$(s.options.manager.element).find(".rr-daddy input[value="+s.options.model.parent_id+"]:checked").length<=0){
						s.inputBox.prop("disabled",false);
					 
						}

				 }

				  
				 if(s.inputBox.parent().parent().find('input:checked').length<=0){
				 	s.inputBox.prop("disabled",false);
				 }

				  
			});
			
			$(s.options.manager.element).on($._HYQEVT_.RR.TAGS_INCREASED,function(event){
				if(s.inputBox.prop("checked")==true){
					s.inputBox.prop("disabled",false);
				}
			});

			$(s.options.manager.element).on($._HYQEVT_.RR.TAGS_RENDERING,function(event){
				for(var i=0;i<event.tags.length;i++){
					if(event.tags[i].id==s.options.model.id){
						s.inputBox.prop("checked",true);
						 
						$(s.options.manager.element).trigger({
							type:$._HYQEVT_.RR.TAGS_INCREASED,
							tags:event.tags,
							model:s.options.model
						});
					}
				}
			});


	}
});
 
	 
})(jQuery);