<?php
	header("content-type: text/html; charset=utf-8");
	require('../includes/common_home_inc.php');
	session_start();
	
	$message = new message;
	$user = new user;
	$user_msg_total = new user_msg_total;
	//当前用户的id
	$user_id = $user_info['id'];
	
	//联系人列表
	$contacts_list = $message -> get_user_contacts_list_by_user($user_id);
	//var_dump($contacts_list);

	if(!$contacts_list){
		$message_list = null;
		$smarty -> assign('friend_id', null);
		$smarty -> assign('list', null);
	}else{
		//侧边栏的最近联系人列表
		foreach($contacts_list as $k => $v){
			if(!is_array($v)){			//判断是否是序列化数据
				$v = unserialize($v);
			}
			
			if($v['user'] == $user_id){
				$friend_id = $v['friend'];
				$friend_info = $user -> get_userinfo($friend_id, $flash);
				if($friend_info){
					$friend_name = $friend_info['nickname'];
					$v['friend_name'] = $friend_name;
					$friend_face = $friend_info['icon_server_url'].$friend_info['icon_path_url'];
					$v['friend_face'] = $friend_face;
					$v['friend_level'] = $friend_info['level'];
					$list[$k] = $v;	  //当前用户接收的私信
				}
				
			}
		}
		//联系人排序
		foreach($list as $kk=>$vv){
			$time_order1[] = $vv['last_time'];
			$num_order1[] = $vv['unread_num'];
		}
		
		array_multisort($num_order1,SORT_DESC,$time_order1,SORT_DESC,$list);
		$smarty -> assign('list', $list);
		
	
		//默认显示最新的一个联系人的私信记录
		$fresh_friend_info = $list[0];
		//var_dump($fresh_friend_info);
		$friend = $fresh_friend_info['friend'];
		
		$friend_info = $user -> get_userinfo($friend, $flash);
		$my_info = $user -> get_userinfo($user_id, $flash);
		
		
		$friend_face = $friend_info['icon_server_url'].$friend_info['icon_path_url'];
		$friend_name = $friend_info['nickname'];
		$my_face = $my_info['icon_server_url'].$my_info['icon_path_url'];
		
		$unread_num = $fresh_friend_info['unread_num'];
		//获取私信记录信息
		$message_list = $message -> get_message_list_by_user_friend($user_id, $friend, 0, -1);
		
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
			//未读私信 置已读  未读数不为零时执行操作
			//if($unread_num != 0){
			$status_code = $message -> update_unread_message_by_user_friend($user_id, $friend);
				/* if($status_code != 1000){		//私信状态更新成功
				 echo $status_code;
				exit('状态更新失败');
				} */
			//}
			
			//获取未读私信总数 并更新
			$unread_mes_num = $message -> get_unread_message_num($user_id);
			$mes_unread_num['message'] = $unread_mes_num;
			$result = $user_msg_total -> update_user_msg_total($user_id,$mes_unread_num);
			
		}
		$smarty -> assign('friend_id', $friend);
		$smarty -> assign('my_face', $my_face);
		$smarty -> assign('friend_face', $friend_face);
		$smarty -> assign('friend_name', $friend_name);
	}
	///$smarty -> display("home/message.html");
	
	//exit;
	
	
	$smarty -> assign('detail_list', $message_list);
	$smarty -> display("home/message.html");


	