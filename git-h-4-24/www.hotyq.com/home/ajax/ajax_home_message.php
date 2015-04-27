<?php
	/* 
	*	私信  ajax处理  suntianxing	
	*
	*/
	
	session_start();
	header("Content-type:text/html;charset=utf-8");
	require_once('../../includes/common_home_inc.php');
	
	if(isset($_REQUEST['action'])){
		$action = clear_gpq($_REQUEST['action']);
	}else{
		echo 1099;//非法操作
		exit;
	}
	$message = new message();
	$user_msg_total = new user_msg_total();
	$userid = $user_info['id'];
	
	switch($action){
		case 'send_message':
			$friend = intval($_REQUEST['rec_id']);			//接收方id
			$content = htmlspecialchars(strip_tags(clear_gpq($_REQUEST['message'])));			//私信内容
			if(strlen($content) == 0){
				echo 'tooshort';
				exit;
			}else if(empty($friend) || !isset($friend)){
				echo 'wrong';
				exit;
			}else if(strlen($content) > 1024){
				echo 'tolong';
				exit;
			}
			//$message = new message();
			$state_code = $message -> add_message($userid, $friend, $content, $type=0);
			if($state_code == 1320){
				echo $state_code;			//私信发送失败
			}else if($state_code == 1321){
				echo $state_code;			//发送失败 联系人信息更新失败
			}else if($state_code == 1322){
				echo $state_code;			//发送失败 添加联系人失败
			}else if($state_code == 1360){
				echo $state_code;			//发送失败 私信缓存失败
			}else if($state_code == 1353){
				echo $state_code;			//发送失败 私信缓存失败
			}else if($state_code == 1354){
				echo $state_code;			//发送失败 私信缓存失败
			}else if($state_code == 1359){
				echo $state_code;			//发送失败 私信缓存失败
			}else if($state_code == 1358){
				echo $state_code;			//发送失败 私信缓存失败
			}else if($state_code == 1369){
				echo $state_code;			//发送失败 私信缓存失败
			}else{
				//更新接受方未读私信总数
				//$user_msg_total = new user_msg_total();
				$unread_mes_num = $message -> get_unread_message_num($friend);
				$unread_num['message'] = $unread_mes_num;
				$result = $user_msg_total -> update_user_msg_total($friend,$unread_num); 
				
				echo $state_code;			//私信发送成功  1000
			}
			break;
			
		case 'show_message':
			$friend = intval($_REQUEST['friend']);
			
			if(empty($friend) || !isset($friend)){
				echo 'wrong';
				exit;
			}
			//$message = new message();
			//全部加载  和 一次加载十条	
			//$type = clear_gpq($_REQUEST['type']);
				
			//if($type == 'ten'){
				//首次加载十条
				//$message_list = $message -> get_message_list_by_user_friend($userid, $friend, 0, 9);
			//}else{ 
				//一次加载全部
				$message_list = $message -> get_message_list_by_user_friend($userid, $friend, 0, -1);
			//}
			if($message_list == 1325){
				echo $message_list;
			}elseif($message_list == 1351){
				echo $message_list;
			}else{
				$user = new user();
				$friend_info = $user -> get_userinfo($friend);
				$my_info = $user -> get_userinfo($userid);
				
				$friend_face = $friend_info['icon_server_url'].$friend_info['icon_path_url'];
				$friend_name = $friend_info['nickname'];
				$my_face = $my_info['icon_server_url'].$my_info['icon_path_url'];
				//$unread_num = $fresh_friend_info['unread_num'];
				
				foreach($message_list as $k => $v){
					if(!is_array($v)){			//判断是否是序列化数据
						$v = unserialize($v);
					}
					
					$v['my_face'] = $my_face;
					$v['friend_face'] = $friend_face;
					$v['friend_name'] = $friend_name;
					
					$mid = $v['mid'];
					$message_content = $message -> get_message_info($mid);
					if($message_content == 1324){
						echo $message_content;
						exit;
					}else{
						$v['index'] = $k;
						$message_list[$k] = $v + $message_content;
					}
				}
				
				//当前用户 未读私信 置已读
				$status_code = $message -> update_unread_message_by_user_friend($userid, $friend);
				
				/* if($status_code != 1000){		//私信状态更新失败
					echo $status_code;
					exit;
				}   */
				
				
				//获取当前用户未读私信总数

				$unread_mes_num = $message -> get_unread_message_num($userid);
				$unread_num['message'] = $unread_mes_num;

				//$user_msg_total = new user_msg_total();
				$result = $user_msg_total -> update_user_msg_total($userid,$unread_num);
				
				
				$message_list = array_reverse($message_list);
				echo json_encode($message_list);
	
			}
			
			break;
		case 'more_message':
			$friend = clear_gpq($_REQUEST['friend']);	
			$start = clear_gpq($_REQUEST['up_index']);	
			$down_index = clear_gpq($_REQUEST['down_index']);	

			//$message = new message();
			$message_list = $message -> get_message_list_by_user_friend($userid, $friend, $start+1, $start+10);
			if($message_list == 1325){
				echo $message_list;
			}else if($message_list == 1351){
				echo $message_list;
			}else if($message_list == 1370){
				echo$message_list;
			}else{
				foreach($message_list as $k => $v){
					if(!is_array($v)){			//判断是否是序列化数据
						$v = unserialize($v);
					}
					$mid = $v['mid'];
					$message_content = $message -> get_message_info($mid);
					if($message_content == 1324){
						echo $message_content;
						exit;
					}else{
						$v['index'] = $k+$start+1;
						$message_list[$k] = $v + $message_content;
						
					}
				}
				//$message_list = array_reverse($message_list);
				echo json_encode($message_list);
			}
					
			break;
		case 'contacts_list':
			//实时更新联系人列表  包含所有的联系关系 比如 我发给对方的私信 他没回复的
			$message = new message();
			$contacts_list = $message -> get_user_contacts_list_by_user($userid);
			if($contacts_list == 1323){
				echo $contacts_list;
				exit;
			}else{
				//侧边栏的最近联系人列表
				foreach($contacts_list as $k => $v){
					if(!is_array($v)){			//判断是否是序列化数据
						$v = unserialize($v);
					}
					if($v['user'] == $userid){
						$list[$k] = $v;	  		//当前用户接收的私信
					}
				}
				//联系人排序
			foreach($list as $kk=>$vv){
				$time_order1[] = $vv['last_time'];
				$num_order1[] = $vv['unread_num'];
			}
			array_multisort($num_order1,SORT_DESC,$time_order1,SORT_DESC,$list);
			echo json_encode($list);
			}
			break;
			
		case 'read_message':								//未读私信列表  点击读取私信置已读
			$friend = clear_gpq($_REQUEST['friend']);

			//$message = new message();
			$status_code = $message -> update_unread_message_by_user_friend($userid, $friend);
			if($status_code == 1000){
				echo $status_code;
			}else{
				echo $status_code;
				exit;
			}
			break;
			
		case 'delete_mes':									//删除指定私信
			$user = clear_gpq($_REQUEST['fid']);	  		//发送者ID
			$mid = clear_gpq($_REQUEST['mid']);	  			//私信ID
			$friend = clear_gpq($_REQUEST['tid']);	  	//接收者ID
			$index = clear_gpq($_REQUEST['mes_index']);	  	//接收者ID
			//$message = new message();
			$state_code = $message -> hide_message($user, $friend, $mid, $index);
			if($state_code == 1000){
				echo $state_code;
			}else{
				echo $state_code;
				exit;
			}
			break;
			
		case 'read_all':									//设置所有未读私信 为已读
			//$message = new message();
			$state_code = $message -> update_unread_message_by_user($userid);				//更新私信的状态
			if($state_code == 1000){
				echo $state_code;  							//全部置已读成功
			}else{											//全部置已读失败
				echo $state_code;
				exit;
			}
			break;
		case 'get_msg':
			//$user_msg_total = new user_msg_total();
			$result = $user_msg_total -> get_user_msg_total_info($userid);
			if($result){
				echo json_encode($result);
			}else{
				exit;
			}
			break;
	}