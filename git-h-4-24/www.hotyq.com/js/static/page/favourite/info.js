//加载公用模块
require('../global/global.js');
//初始化header
require('../../common/header/nav_search/nav_search.js');
require('../../common/header/user/user.js');
//初始化lazy
require('../../common/ui/lazyload/lazyload.js').lazyload();

$(function(){
	//var cookie_type = ''; //用户type用
	var user_id = $('#hide_userinfo_id').val();
	//alert(user_id);
	if( typeof($.cookie('hyq_user_info')) != 'undefined' ){
		//alert('登录了');
		if( $.cookie('hyq_user_info').split('|')[0] == user_id ){
			//alert($.cookie('hyq_user_info').split('|')[0]);
			$('#collect_btn').hide();
			$('#have_collect_btn').hide();
			$('#letter_btn').hide();
			$('#invite_btn').hide();
			$('#report_btn').hide();
			$('#edit_btn').show();
		}else{
			//$('#collect_btn').show();
			//$('#have_collect_btn').show();
			$('#letter_btn').show();
			$('#invite_btn').show();
			$('#report_btn').show();	
			$('#edit_btn').hide();
			//不是本人
			//alert('不是本人');
			var action = 'get_collected_list';
			var uid = $.cookie('hyq_user_info').split('|')[0];
			$.post("/home/ajax/ajax_home_recruit.php",{'action':'get_recruit_list_by_uid','uid':uid},function(data){
				//console.info(data);
				if (data == 1000) {
					$('#has_recruit').val('OK');
				}else{
					$('#has_recruit').val('');				
				}
			});						
			$.post("/home/ajax/ajax_home_collect.php",{ action:action,uid:uid},function(data){
				//alert(data);
				//获得该用户的所有收藏红人  再一一比对  针对该红人
				var collect_user_array = eval('('+data+')');
				var collect_user_arr = collect_user_array['user'];
				//alert(collect_user_array);
				//有一个红人  传递过来红人数组  遍历数组  看这个id在不在数组里面
				var i = 0;
				if(data.user != '[]'){
					for(; i < collect_user_arr.length;i++){
						if( user_id == collect_user_arr[i]){
							//替换该元素样式
							//alert('这个要替换');
							$('#have_collect_btn').show();
							$('#collect_btn').hide();
						}
					}
				}
			});
		}
	}else{
		//alert('没登陆');
	}
	///////////////////////////私信//////////////////////////////////////////
	//当发送私信的按钮被点中
	$("#letter_btn").click(function(){
		if( typeof($.cookie('hyq_user_info')) == 'undefined' || $.cookie('hyq_user_info').split('|')[0] < 1 ){
			console.log('没登陆');
			var url = window.location.href;
			location.href="/account/login?url="+url;				
		}
		
		//发送事件让发私信的窗口弹出，同事要把用户ID传过去
		var toUserId = $(this).attr('userId');	  
		$(document).trigger({
			type:"hyq/iframe/doc/sms",
			//fromUserId:toUserName,//这个ID由PHP判断
			toUserId:toUserId//这个ID由PHP判断
		});
	});
	/////////////////////私信////////////////////////////////////////////////
	/////////////////////邀约////////////////////////////////////////////////
	//当发送邀约的按钮被点击时
	$('#invite_btn').click(function(){
		if( typeof($.cookie('hyq_user_info')) == 'undefined' || $.cookie('hyq_user_info').split('|')[0] < 1 ){
			console.log('没登陆');
			var url = window.location.href;
			location.href="/account/login?url="+url;				
		}else{		
			var userType = $.cookie('hyq_user_info').split('|')[1];
			//这里要做个判断，判断当前登录的用户有没有招募
		   if($('#has_recruit').val() == 'OK'){
				var hasRecruit = true;//可以根据后台的情况判断这个值
			}else{
				var hasRecruit = false;//可以根据后台的情况判断这个值
			} 
			var toUserId = $(this).attr('userId');
			var toUserName = $(this).attr('to_uname');
			// alert(toUserId+toUserName);
			if(hasRecruit){
				//alert('应该弹出邀约框');
				//发送事件让发邀约的窗口弹出，同时要把用户ID传过去
				$(document).trigger({
					type:"hyq/doc/invite",
					userType:userType,    //当前用户的ID
					toUserName:toUserName,    
					toUserId:toUserId      //邀约用户的ID	
				});
			}else{
			   // alert('应该弹出没有邀约框');
				$(document).trigger("hyq/invite/no-recruit/alert");
			}
		}	
	});
});

$(document).ready(function(){
	var $ali = $('.bd div div'),
		$prev = $('.prev'),
		$next = $('.next'),
		timer,
		cIndex,
		aliLen = $ali.length;
	$(".list-item").click(function(){
		cIndex = $(this).index();
		$(".window-bg,.window-con").show();
		$('.window-con').show();
		fnMove(cIndex);
	});

	$ali.each(function (i,e) {
		var oImg = new Image();
		oImg.src = $(e).attr('data-src');
	});
  
	$("#btn-close").click(function(){
		$(".window-bg,.window-con").hide();
	});
	  
	$ali.mouseover(function() {
		clearInterval(timer);
		$prev.add($next).fadeIn();
	});
	
	$prev.add($next).mouseover(function () {
		clearInterval(timer);
	});
	
	
	$prev.add($next).add($ali).mouseout(function() {
		timer = setTimeout(function () {
			$prev.add($next).fadeOut();
		},500);
		
	});
	
	$prev.click(function () {
		--cIndex;
		if (cIndex == -1) {
			cIndex = aliLen-1;
		}
		fnMove(cIndex);
	});
	
	$next.click(function () {
		++cIndex;
		if (cIndex > aliLen-1) {
			cIndex = 0;
		}
		fnMove(cIndex);
	});
	function fnMove(index) {
		//$ali.eq(index).show().css({'background':$ali.eq(index).attr('_src')}).siblings().hide();
		$ali.eq(index).show().siblings().hide();
	}

	$(document).keydown(function (ev) {
		if (ev.keyCode == 27) {
			$('#btn-close').click();
		}
	});
});
