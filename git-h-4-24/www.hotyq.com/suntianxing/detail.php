<?php
	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_inc_test.php');
	require_once('./message.class.php');
	require_once('./message_redis.class.php');
	require_once('./redis.class.php');
	//session_start();
	$user = new user;
	$user_cookie = $user -> get_cookie_user_info();
	//var_dump($cookie);
	
	
	$message = new message;
	$user = $user_cookie['userid'];
	//$user = 1103;
	$friend = clear_gpq($_REQUEST['friend']);	
	$unread_num = clear_gpq($_REQUEST['num']);	
	
	$message_list = $message -> get_message_list_by_user_friend($user, $friend, 0, 9);
	//缓存版
	if($message_list == 1325){				//测试后 替换if(){}else{}
		echo $message_list;
		exit('私信记录查询失败!');
	}elseif($message_list == 1351){
		echo $message_list;
		exit('存缓存失败');
	}else{
		foreach($message_list as $k => $v){
			if(!is_array($v)){
				$v = unserialize($v);
			}
			$mid = $v['mid'];
			$message_content = $message -> get_message_info($mid);
			if($message_content == 1324){
				echo $message_content;
				exit;
			}elseif($message_content == 1361){
				echo $message_content;			//缓存获取失败
				exit;
			}else{
				$v['index'] = $k;
				$message_list[$k] = $v + $message_content;
			}
		}
		$message_list = array_reverse($message_list,TRUE);
		if($unread_num != 0){
			$status_code = $message -> update_unread_message_by_user_friend($user, $friend);
			if($status_code != 1000){		//私信状态更新成功
				echo $status_code;
				exit('状态更新失败');
			}
		}
	}

	$smarty -> assign('uid', $user);
	$smarty -> assign('friend', $friend);	
	$smarty -> assign('detail_list', $message_list);	
	$smarty -> display("suntianxing/detail.html");


	