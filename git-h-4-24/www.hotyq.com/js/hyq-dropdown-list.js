
;(function($){
	$.fn.HYQDropdownList = function(params){
		//如果是下拉多选框 就忽略
		if($(this).hasClass('hyq-checkbox-list')) return;
	var $this = this;
	var s = this,$s = $(this);
	var list = this.find(".hyq-dropdown-list");
	var btn = this.find('.hyq-dropdown-btn');
	var box = this.find('.hyq-dropdown');
	var fakeInput =this.find('.hyq-dropdown .dropdown-value');
	var hiddenInput = this.find('input');
	var currentValue = $.trim(hiddenInput.val());
	var placeholder = $.trim($(this).attr('placeholder'));

	var clickEvtHandler = function(event){//console.log('dian');
		var target = $(event.target||event.srcElement);
		if(target.attr("disabled")!=undefined){
			//console.log("AA")
			event.stopPropagation();
			return;	
		} 
		
		var input = $s.find("input");
		
		//update value and active item
		//console.log(input.val()+'ss');
		input.val(target.attr('value'));
		input.change();
		$s.find(".hyq-dropdown-list").find('li').removeClass("active");
		fakeInput.addClass('selected');
		target.addClass('active');
		fakeInput.html(target.html());
		fakeInput.attr({"title":fakeInput.html()});
		$s.trigger({
			type:"change",
			value:input.val(),
			text:fakeInput.html()
		})

	};
	

	//赋值
	if(currentValue!=""){
		
		$.each(list.find('li'),function(i,item){
			//console.log(item)
			var itemVal = $.trim($(item).attr('value'));
			//console.log(itemVal);

			if(currentValue==itemVal){

				$(item).addClass("active");
				fakeInput.addClass('selected');
				fakeInput.html($(item).html());
			}else{
				$(item).removeClass("active");
				

			}


		});
	}else{
		 
		fakeInput.removeClass("selected");

	}

//dianji kongbai xiaoshi
	$(document).click(function(evt) {
		$('.hyq-dropdown-list').hide();
	});
	
	var onControlClick = function(ev){
		//console.debug(111);
		if(hiddenInput.prop('disabled')==true) return;
		//console.debug(hiddenInput.prop('pause'));
		if(hiddenInput.prop('pause')) {
			hiddenInput.removeProp('pause');
			return;
		}
		//console.debug(222);
		$('.hyq-dropdown-list').hide();
		if(list.find('ul li').length <=0)return false;
	
		list.show();
		return false;
	}



	box.add(btn).click(function (ev) {
		$('.hyq-dropdown-list').each(function () {
			$this =$(this);
			if ($this[0] != list[0]) {
				$this.hide();
			}
		});
		
		list.show();
		if (!list[0].showTop) {
			var top = list.find('li[showTop="active"]').position() ? list.find('li[showTop="active"]').position().top : 0;
			list.scrollTop(top);
			list[0].showTop = true;
		}
		ev.stopPropagation();
	});
	//box.click(onControlClick);
	
	//添加节点
	this.on("addItem",function(event){

		var li = $("<li>").attr({"value":event.value}).html(event.name);
		if(event.isDisabled==true){
			li.attr({'disabled':"disabled"});
		}
		li.appendTo(list.find("ul"));
		li.click(function(event){clickEvtHandler(event);});
	})

	//li click event handler

	$s.on("clear",function(){
		  
		list.find("ul li").remove();
		hiddenInput.val("");
		fakeInput.removeClass("selected");
		fakeInput.html($(this).attr("placeholder")?$(this).attr("placeholder"):"");
		$s.trigger("cleared");
		//console.log('清除了');
	});

	//重置
/*	$s.on("reset",function(){
		list.find("ul li").removeClass("active").removeAttr("disabled");
		fakeInput.removeClass('selected').html($s.attr("placeholder"));
		hiddenInput.val("");
		console.log('重置了');
	});
*/	$s.find(".hyq-dropdown-list li").click(function(event){clickEvtHandler(event);});
	
	//设置DISABLED
	$s.on("set/disabled/values",function(event){
		var disabled = event.disabledVals;
		var items = list.find("ul li");
		if(disabled.length>0 && items.length>0){
			$.each(items,function(i,e){
				$.each(disabled,function(j,n){
					if(n.toString()==$(e).attr("value")){
						$(e).attr({"disabled":"disabled"});
						if($(e).hasClass("active")){
							$(e).removeClass("active");	
							fakeInput.removeClass('selected').html($s.attr("placeholder"));
							hiddenInput.val("");
						}
						
					}
				});
			});
		}
		
	});
	//
	$s.on("set/disabled/value",function(event){
		var disabled = event.disabledVal;
		var items = list.find("ul li");
		if(items.length>0 && disabled){
			$.each(items,function(i,e){
				if($(e).attr("value")==disabled.toString()){
					$(e).attr({"disabled":"disabled"});
					if($(e).hasClass("active")){
							$(e).removeClass("active");
							fakeInput.removeClass('selected').html($s.attr("placeholder"));
							hiddenInput.val("");
					}
				}
			});
		}
	});

	//取消多个disabled
	$s.on("relieve/disabled/values",function(event){
		var relieves = event.relieveVals;
		var items = list.find("ul li");
		if(relieves.length>0 && items.length>0){
			$.each(items,function(i,e){
				$.each(relieves,function(j,n){
					if(n.toString()==$(e).attr("value")){
						$(e).removeAttr("disabled");
					}
				});
			});
		}
	});

	//取消单个disabled
	$s.on("relieve/disabled/value",function(event){
		var relieve = event.relieveVal;

		var items = list.find("ul li");
		if(items.length>0 && relieve){
			$.each(items,function(i,e){
				if($(e).attr("value")==relieve.toString()){
					$(e).removeAttr("disabled");
				}
			});
		}
	});
	$s.trigger("reset");

	return  $.extend($(this),params);
	}
	
	

}(jQuery));
