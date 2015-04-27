;(function($){

	$.extend({
		getUrlParameter:function(sParam){
			    var sPageURL = window.location.search.substring(1);
			    var sURLVariables = sPageURL.split('&');
			    for (var i = 0; i < sURLVariables.length; i++) 
			    {
			        var sParameterName = sURLVariables[i].split('=');
			        if (sParameterName[0] == sParam) 
			        {
			            return sParameterName[1];
			        }
			    }
			}


	});
	//用户面板
	$.fn.HYQUserPanel = function(sParam){
		var s = this,$s = $(this);

		var avt = $s.find("#login_user_head_icon");
		var level = $s.find("#login_user_level");
		var name = $s.find("#login_user_nickname");

		$(document).on("hyq/userstate/changed",function(){
			avt.attr("src",$.cookie('hyq_user_info').split('|')[6]);
			level.html($.cookie('hyq_user_info').split('|')[3]);
			name.html($.cookie('hyq_user_info').split('|')[2]);

		});

	}

	$(document).ready(function () {
		 $(".user-panel").HYQUserPanel();
	})
	//百分比条
	$.fn.HYQPercentBar=function(){
		var s = this,$s = $(this);
		try{
			var value = parseInt($s.attr('value'));
			if(value>100) value=100;
			if(value<0) value=0;
			var vbar = $s.find(".hyq-percentbar-value");
			var size = $s.width();
			if(vbar.length!=0 && size>1){
				var valWidth = Math.round(size*value/100)+"px";
				if(valWidth>size)valWidth ==size;

				vbar.css({"width":valWidth,"height":$s.height()>10?$s.height():1+"px","display":"block","background-color":"#ff3300"});

				if(value<15){
					$s.append(value+"%");
				}else{
					vbar.html(value+"%");
				}
			}

		}catch(e){
			console.log(e);
		};

		$(document).on("hyq/userstate/changed",function(){
			
		});

	};

	//按钮式下拉菜单
	$.fn.HYQDropdownMenuBtn = function(){
		var s = this,$s = $(this);
		var m = $s.find('ul');
		$s.click(function(event){
			if(!m.is(":hidden")){
				m.hide();
				$(".hyq-dropdown-menu-btn ul").hide();
				event.stopPropagation();
				return;
			}else{
				$(".hyq-dropdown-menu-btn ul").hide();	
				m.show();
			}
			event.stopPropagation();		 
		});

		$s.find("ul li").click(function(event){
			$s.find("ul").hide();
			 event.stopPropagation();
		});
		$(document).click(function(){
			$(".hyq-dropdown-menu-btn ul").hide();
		});
		 
	};

	//	
	$(document).ready(function(){
		var bars = $(".hyq-percentbar");
		if(bars.length>0){
			$.each(bars,function(i,e){
				$(e).HYQPercentBar();
			});
		}

		var dropdownMenus = $(".hyq-dropdown-menu-btn");
		if(dropdownMenus.length >0 ){
			$.each(dropdownMenus,function(i,e){
				$(e).HYQDropdownMenuBtn();
			});
		}

	});
})(jQuery);