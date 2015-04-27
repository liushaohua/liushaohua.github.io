;(function($){
	//alert(222222222222);
	$.fn.HYQTitle = function(event){
		var s = this,$s  = $(s);
		$s.mouseover(function(event){
			console.log(event);
			var a =$("<div>").html($s.html()).appendTo($(document.body)).css({
				"position":"absolute",
				"z-inedex":$.hyqZindex()
			}).css({
				top:event.clientY,
				left:event.clientX
			});
		});

	}
	$.fn.HYQCheckboxList = function(params){
		var $s = $(this),s=this;
		$s.find(".dropdown-value").html($s.attr('placeholder'));
		var list = $s.find(".hyq-dropdown-list");
		var valueBox = $s.find(".dropdown-value");
		var btn = this.find('.hyq-dropdown-btn');
		var valueWrapper = this.find('.hyq-dropdown');

		//valueWrapper.HYQTitle();
		var hiddenInput = this.find('input');

		$.each(list.find("li"),function(i,e){
			var label = $(e).html();
			var input = $("<input>").attr({
				"type":"checkbox",
				"name":list.find('ul').attr('name'),
				"value":$(this).attr("value"),
				"hyq-checkbox-in-list":""

			}).addClass("hyq-nice-checkbox");
			var span = $("<span>").addClass("hyq-checkbox");
			var labelNode = $("<label>").html($.trim(label));
			$(e).html("");
			$(e).append(input).append(span).append(labelNode);
			 $(input).HYQCheckbox();
		});
		//执行change事件
		var performChangeEvent = function(){
			var checkedValueArr =[];
			var checkedTextArr = [];
			var valueArr = $s.find("input[type=checkbox]:checked");

			$.each(valueArr,function(i,e){
				checkedValueArr.push($(e).val());
				checkedTextArr.push($(e).next().next().html());
			});

			// console.log(checkedValueArr);
			// console.log(checkedTextArr);
			var valueForUi = checkedTextArr.join(" / ");
			if($.trim(valueForUi)=="") 
				$s.find(".dropdown-value").html($s.attr('placeholder')).removeClass('selected').attr({"title":$s.attr('placeholder')});
			else
				$s.find(".dropdown-value").html(valueForUi).addClass('selected').attr({"title":valueForUi});
			$s.trigger({
				type:"change",
				checkedValueArr:checkedValueArr,
				checkedTextArr:checkedTextArr
			});
		};

		var checkboxHandler = function(event){
			performChangeEvent();
 			event.stopPropagation();
		}

		$s.find("li,span.hyq-checkbox").bind("click",checkboxHandler);

		var onControlClick = function(event){
			// if(hiddenInput.prop('disabled')==true) return false;
			$('.hyq-dropdown-list').hide();
			if(list.find("ul li").length<=0) return false;
			list.toggle();
			event.stopPropagation();
		}

		list.hide();
		 
		//$(".hyq-nice-checkbox").on("onchange",function(event){
		

		$s.on("clear",function(event){
			list.find("ul li").remove();
			$s.trigger("removed");
			valueBox.removeClass('selected').html($s.attr("placeholder"));
		});
 
		$s.on("addItem",function(event){
			var li = $("<li>").attr({"value":event.id});
			var input = $("<input>").attr({
				"type":"checkbox",
				"name":event.checkboxName,
				"value":event.value,
				"hyq-checkbox-in-list":""
			}).addClass("hyq-nice-checkbox");

			var span = $("<span>").addClass("hyq-checkbox");
			var label = $("<label>").html($.trim(event.label));

			li.append(input).append(span).append(label).appendTo(list.find("ul"));

			input.HYQCheckbox();
			span.bind("click",checkboxHandler);
			$s.trigger("itemAdded");
		});

		$(document).click(function(){
			list.hide();
		});

		 
		btn.click(onControlClick);
		valueWrapper.click(onControlClick);
		return  $.extend($(this),params);
	}
	$(document).ready(function(){

		var lists = $(".hyq-checkbox-list");
		console.log(lists)
		if(lists.length>0){
			$.each(lists,function(i,e){
				$(e).HYQCheckboxList();
			});
		}
	});
}(jQuery));
// .hyq-checkbox-list
// <input type="checkbox" value="3" name="roles" class="hyq-nice-checkbox">
//                   <span class="hyq-checkbox"></span>
//                   <label>剪刀手</label>