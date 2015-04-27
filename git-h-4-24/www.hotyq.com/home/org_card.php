<?php
	$PAGE_TYPE = "org_card";
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');
	$base = new base();
	$message = new message;
	$uid = $user_info["id"];
	//获取企业成立时间年份
	for($i = $COMMON_CONFIG["CREATE_YEAR"]["RANGE"]['min'];$i <=date('Y');$i++){
		$create_year[] = $i;
	}
	$smarty -> assign('create_year',$create_year);	
	//读取省份
	$plist = $base -> get_province_list();
	//读取机构类型
	$tlist = $base -> get_org_type_list();
	//机构
	$state_list = $COMMON_CONFIG["STATE"];
	$smarty -> assign('state_list',$state_list);
	//进入当前页面的时候 将红眼圈加入其好友
	//联系人列表
	$contacts_list = $message -> get_user_contacts_list_by_user($uid);
	if(!$contacts_list){
		$dt = date('Y-m-d H:i:s',time());					//私信发送时间
		$state_code = $message -> add_e_message_by_user($hotyq_id =1, $friend = $uid, $insert_id = 1, $dt);
		/* 错误处理 */
	}
	$smarty -> assign('provincelist',$plist);
	$smarty -> assign('tlist',$tlist);
	//$smarty -> assign('str_date',$str_date);
	$smarty -> display("home/org_card.html");