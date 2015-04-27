$(function(){
	$('#publish_recruit_btn').click(function(){
		//登录
		if( typeof($.cookie('hyq_user_info')) != 'undefined' ){
			//alert('登录了');
			if( $.cookie('hyq_user_info').split('|')[1] == 'mobile' ){
				top.location.href = "/home/recruit/set";
			}else{
				//alert('邮箱用户');
				//检验该用户是否验证手机
				var action = 'mobile_is_checked';
				//var uid = $.cookie('hyq_user_info').split('|')[0];
				$.post('/home/ajax/ajax_home_account.php',{ action:action},function(data){
					//alert(data);
					//console.log(data);
					if(data == 0){
						//alert('手机未验证');
						//触发弹出框 弹出事件
						$('#valid-mobile-modal').HYQModal();
						$('#valid-mobile-modal').trigger('hyq/modal/show');
					}else{
						//alert('data不等于0')
						top.location.href = "/home/recruit/set";
					}
				});
			}
		}else{
			//alert('没登陆');
			//跳转到登录页
			top.location.href = "/account/login";
		}
	});
	if( typeof($.cookie('hyq_user_info')) != 'undefined' ){
		$.post('/home/ajax/ajax_home_userprofile.php',{ action:'get_userinfo'},function(data){
			$(document).trigger("hyq/userstate/changed");
		});	
	}	
});

function clearString(s){ 
		var pattern = new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）&;|{}【】‘；：”“'。，、？]"); 
		var rs = ""; 
		for (var i = 0; i < s.length; i++) { 
			rs = rs+s.substr(i, 1).replace(pattern, ''); 
		} 
		return rs;  
	}
 	$('.search-btn').click(function(){
		if($("#search_q").val().trim()){
			var rs = clearString($("#search_q").val());
			$("#search_q").val(rs);
			$('.search-box').submit();
		}
 	});
	
    setTimeout(function () {
		$('#search_q').keypress(function(event){
			if(event.keyCode == "13"){
				if($("#search_q").val().trim()){
					var rs = clearString($("#search_q").val());
					$("#search_q").val(rs);
					$('.search-box').submit();
				}else {
					return false;
				}
			}
		});	
	},500);		
