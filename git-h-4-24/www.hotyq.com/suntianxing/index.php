<?php
	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_inc_test.php');
	require_once('./message.class.php');
	require_once('./message_redis.class.php');
	require_once('./redis.class.php');
	//session_start();
	$message = new message;
	$user = new user;
	$cookie = $user -> get_cookie_user_info();
	$user_cookie = $user -> get_cookie_user_info();
	$user = $user_cookie['userid'];
	$usertype = $user_cookie['user_type'];
	

	
	
	//未读私信总数统计
	
	$unread_num = $message -> get_unread_message_num($user);
	if(!$unread_num){
		$smarty -> assign('unread_num', null);
		//exit;
	}else{
		$smarty -> assign('unread_num', $unread_num);
	}
	
	/* 新版私信首页(完整) start */
	//重构与我相关的私信列表   测试
	$contacts_list = $message -> get_user_contacts_list_by_user($user);
	//var_dump($contacts_list);

	if(!$contacts_list){ 
		$smarty -> assign('message_list', null);
		$smarty -> assign('list', null);
	}else{
		//新 需求  私信首页 显示最后一条消息
		foreach($contacts_list as $k => $v){
			if(!is_array($v)){			//判断是否是序列化数据
				$v = unserialize($v);
			}
			//取出当前用户的联系人数据
			if($v['user'] == $user){
				$receive_message_list[$k] = $v;	  //当前用户接收的私信
			}
		}
		foreach($receive_message_list as $k=>$v){
			$last_mid = $v['last_mid'];
			$time_order[] = $v['last_time'];
			$num_order[] = $v['unread_num'];
			$mes_content = $message -> get_message_info($last_mid);
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
			if(!is_array($v)){			//判断是否是序列化数据
				$v = unserialize($v);
			}
			
			if($v['user'] == $user){
				$list[$k] = $v;	  //当前用户接收的私信
			}
		}
		//联系人排序
		foreach($list as $kk=>$vv){
			$time_order1[] = $vv['last_time'];
			$num_order1[] = $vv['unread_num'];
		}
		
		array_multisort($num_order,SORT_DESC,$time_order,SORT_DESC,$receive_message_list);
		$smarty -> assign('message_list', $receive_message_list);
		
		array_multisort($num_order1,SORT_DESC,$time_order1,SORT_DESC,$list);
		$smarty -> assign('list', $list);
	}
	
	
	$smarty -> assign('uid', $user);
	$smarty -> display("suntianxing/index.html");


	