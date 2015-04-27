<?php
	header("content-type: text/html; charset=utf-8");
	require('../includes/common_inc.php');
	session_start();
	
	$privatemessage = new Message;
	//$myid = $_SESSION['uid'];
	$user = 1314;
	$friend = clear_gpq($_REQUEST['friend']);	
	
	$message_list = $privatemessage -> get_message_list_by_user_friend($user, $friend);
	if($message_list == 1325){
		echo $message_list;
		exit('私信记录查询失败!');
	}else{
		foreach($message_list as $k => $v){
			$mid = $v['mid'];
			$message_content = $privatemessage -> get_message_info($mid);
			if($message_content == 1324){
				echo $message_content;
				exit;
			}else{
				$message_list[$k] = $v + $message_content;
			}
		}
	}

	$smarty -> assign('friend', $friend);	
	$smarty -> assign('detail_list', $message_list);	
	$smarty -> display("message/detail.html");


	