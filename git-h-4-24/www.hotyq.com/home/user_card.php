<?php
	$PAGE_TYPE = "user_card";
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');
	$base = new base();
	$message = new message;
	
	$uid = $user_info["id"];
	//读取角色
	$rolelist= $base -> get_parent_role_list();
	//读取省份
	$plist = $base -> get_province_list();
	$STATE = $COMMON_CONFIG["STATE"];
	//进入当前页面的时候 将红眼圈加入其好友
	//联系人列表
	$contacts_list = $message -> get_user_contacts_list_by_user($uid);
	if(!$contacts_list){
		$dt = date('Y-m-d H:i:s',time());					//私信发送时间
		$state_code = $message -> add_e_message_by_user($hotyq_id =1, $friend = $uid, $insert_id = 1, $dt);
		/* 错误处理 */
	}
	$smarty -> assign('rolelist',$rolelist);
	$smarty -> assign('STATE',$STATE);
	$smarty -> assign('provincelist',$plist);

	$smarty -> display("home/user_card.html");
?>