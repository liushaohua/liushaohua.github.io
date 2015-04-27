if(typeof($.cookie('hyq_user_info')) != 'undefined' && $.cookie('hyq_user_info').split('|')[0] > 0){
	var dynamic_arr = {user:[],org:[],recruit:[]};
	$('.collect_button').each(function(){
		dynamic_arr[$(this).attr('collect_type')].push($(this).attr('dynamic_id'));	
	});
	$.post("/home/ajax/ajax_home_collect.php",{'action':'get_collected_list'},function(data){
		var collected_list = eval('('+data+')');
		for(var eu in dynamic_arr['user']){
			if($.inArray(dynamic_arr['user'][eu],collected_list['user']) > -1){
				$(".collect_button[collect_type='user'][dynamic_id='"+dynamic_arr['user'][eu]+"']").html('<i class="hyq-ic ic-heart-k-wt"></i>已收藏').attr('class','profile-collected-a').attr('disabled','disabled');
			}
		}
		for(var eo in dynamic_arr['org']){
			if($.inArray(dynamic_arr['org'][eo],collected_list['org']) > -1){
				$(".collect_button[collect_type='org'][dynamic_id='"+dynamic_arr['org'][eo]+"']").val('已收藏').attr('disabled','disabled');
			}
		}
		for(var er in dynamic_arr['recruit']){
			if($.inArray(dynamic_arr['recruit'][er],collected_list['recruit']) > -1){
				$(".collect_button[collect_type='recruit'][dynamic_id='"+dynamic_arr['recruit'][er]+"']").html('<i class="hyq-ic ic-heart-k-wt"></i>已收藏').attr('class','profile-collected-a').attr('disabled','disabled');
			}
		}
	});
}		

$('.collect_button').each(function(){
	var main = $(this);	
	$(this).click(function(){
		if( typeof($.cookie('hyq_user_info')) == 'undefined' || $.cookie('hyq_user_info').split('|')[0] < 1 ){
			var url = window.location.href;
			location.href="/account/login?url="+url;
			return;
		}	
		if($.cookie('hyq_user_info').split('|')[2].length ==0){				
			location.href="/home/user/card";
			return;
		}				
		$.post("/home/ajax/ajax_home_collect.php",{'action':'add_collect','dynamic_id':$(this).attr('dynamic_id'),'collect_type':$(this).attr('collect_type')},function(data){
			var add_result = eval('('+data+')');
			main.html('<i class="hyq-ic ic-heart-k-wt"></i>已收藏');
			main.attr('class','profile-collected-a');
			main.attr("disabled",true);
			var tip = $("#tip-message").HYQTip();
			tip.showSuccess(add_result.desc);
		},'text');
	});
});