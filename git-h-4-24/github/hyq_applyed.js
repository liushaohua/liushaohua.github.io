$(function(){
	//登录  不是自己
	if( typeof($.cookie('hyq_user_info')) != 'undefined' || $.cookie('hyq_user_info').split('|')[0] > 0 ){
		//登录了
		if( $.cookie('hyq_user_info').split('|')[0] == recruit_uid ){
			//本人
			$('.have_apply_btn').hide();
			$('.yet_apply_btn').hide();
			$('#collect_btn').hide();
			$('#have_collect_btn').hide();
			$('#letter_btn').hide();
		}else{
			//不是本人
			var action = 'is_apply',
			    uid = $.cookie('hyq_user_info').split('|')[0];
			$.post("/home/ajax/ajax_home_apply.php",{ action:action,uid:uid},function(data){
				//获得该用户的所有报名  再一一比对  针对给招募  针对某个角色
				e_role_id_array = eval('('+data+')');
				if(data != '[]'){
					$('.recruit_role_apply_box').each(function(){
						var e_role_id = $(this).attr('eid');
						//alert(e_role_id);
						var i = 0;
						for(var i = 0,len = e_role_id_array.length; i < len;i++){
							if( e_role_id == e_role_id_array[i]){
								$(this).find('.have_apply_btn').show();
								$(this).find('.yet_apply_btn').hide();
							}
						}
					});
				}
			});
		}
	}else{
		//没登陆
	}
});