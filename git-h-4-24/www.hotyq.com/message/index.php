<?php
	header("content-type: text/html; charset=utf-8");
	require('../includes/common_inc.php');
	session_start();
	
	$privatemessage = new Message;
	//$user = $_SESSION['uid'];
	$user = 1314;
	
	//未读私信总数统计
	/*
	$unread_num = $privatemessage -> get_unread_message_num($myid);
	if($unread_num == 1325){
		echo $unread_num;
	}else{
		$num = $unread_num['num'];
		$smarty -> assign('unread_num', $num);
	}
	//私信总数统计
	$mes_num = $privatemessage -> get_message_num($myid);
	if($mes_num == 1326){
		echo $mes_num;
	}else{
		$all_num = $mes_num['num'];
		$smarty -> assign('mes_num', $all_num);
	}
	*/
	
	
	//己方收到的所有未读消息  勿删
	/*
	$unread_mes_index = $privatemessage -> get_lastly_unread_mes_list($user);
	if($unread_mes_index == 1333){
		echo $unread_mes_index;
		exit;
	}else{
		$smarty -> assign('unread_list', $unread_mes_index);
	}
	*/
	
	
	/* 新版私信首页(完整) start */
	//重构与我相关的私信列表   测试
	$contacts_list = $privatemessage -> get_user_contacts_list_by_user($user);
	if($contacts_list == 1323){
		echo $contacts_list;
		exit;
	}else{
		//新 需求  私信首页 显示最后一条消息
		foreach($contacts_list as $k => $v){
			if($v['user'] == $user){
				$receive_message_list[$k] = $v;	  //当前用户接收的私信
			}
		}
		foreach($receive_message_list as $k=>$v){
			$last_mid = $v['last_mid'];
			$mes_content = $privatemessage -> get_message_info($last_mid);
			if($mes_content == 1324){
				echo $mes_content;
				exit;
			}else{
				$receive_message_list[$k]['mes_content'] = $mes_content['content'];
				$receive_message_list[$k]['mes_type'] = $mes_content['type'];
			}
		}

		//侧边栏的最近联系人列表
		foreach($contacts_list as $k => $v){
			if($v['user'] == $user){
				$list[$k] = $v;	  //当前用户接收的私信
			}
		}
		
		$smarty -> assign('message_list', $receive_message_list);
		$smarty -> assign('list', $list);
		
	}
	$smarty -> display("message/index.html");


	