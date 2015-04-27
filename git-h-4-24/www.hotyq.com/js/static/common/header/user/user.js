$(document).ready(function(){
	if(typeof($.cookie('hyq_user_info')) == 'undefined' ){
		//未登录
		$('#yet_login_box').fadeIn();
		$('#has_login_box').css('display','none');
	}else{
		//已登录
		$('#yet_login_box').css('display','none');
		$('#has_login_box').fadeIn(1000);
		var cookie_info = $.cookie('hyq_user_info').split('|');
		//console.log(cookie_info);
		if(cookie_info[6] != ''){
			$('#login_user_head_icon').prop('src',cookie_info[6]);
		}else{
			//缺省头像
		}
		if(cookie_info[2] != ''){
			$('#login_user_nickname').text(cookie_info[2]).attr('alt',cookie_info[2]).attr('title',cookie_info[2]);
		}else{
			//缺省昵称  放id
			$('#login_user_nickname').text('用户'+cookie_info[0]);
		}
		$('#login_user_level').text(cookie_info[3]);
		if(cookie_info[4] <= 90){
			$('#login_user_percent').attr('percent',cookie_info[4]).attr('alt','完善个人资料').attr('title','完善个人资料');
		}else{
			$('#login_user_percent').attr('percent',cookie_info[4]).attr('alt','更新个人资料').attr('title','更新个人资料');
		}
		$('#login_user_percent').attr('percent',cookie_info[4]);
		//console.log(cookie_info);
		if(cookie_info[1] == 'user'){
			$('#my_hyq').prop('href','/home/user');
			$('#my_home').prop('href','/user/'+cookie_info[0]);
		}else if(cookie_info[1] == 'org'){
			$('#hyq_percent_pie').prop('href','/home/org/set');
			$('#my_hyq').prop('href','/home/org');
			$('#my_home').prop('href','/org/'+cookie_info[0]);
		}
		
		var action  = 'get_msg';
		$.ajax({
			 url:'/home/ajax/ajax_home_message.php',
			data:{'action':action},
			type:'post',
			success:function(data){
				  $.each(data,function(i,v){
					  if(i == 'message'){
						  if(v == 0){
							$('#msg_num').text(v);
						  }else{
							$('#msg_num').text(v).parent().attr('alt','未读私信('+v+')').attr('title','未读私信('+v+')');
						  }
					  }else if (i == 'collect') {
						if(v == 0){
						   $('#collect_num').text(v);
						}else{
						   $('#collect_num').text(v).parent().attr('alt','收藏('+v+')').attr('title','收藏('+v+')');
						}
					  }else if(i == 'reply_invite'){
						if(v == 0){
							$('#invite_num').text(v);
						}else{
							$('#invite_num').text(v).parent().attr('alt','新邀约('+v+')').attr('title','新邀约('+v+')');
						}
					  }                               
				  });
			},
			dataType:'json'
		});
	}
});