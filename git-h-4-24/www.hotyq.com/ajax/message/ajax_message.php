<?php
	/* 
	*	私信  ajax处理  suntianxing	
	*
	*/
	
	session_start();
	header("Content-type:text/html;charset=utf-8");
	include "../../includes/common_inc.php";

	
	
	$privatemessage = new Message;
	
	$action = clear_gpq($_REQUEST['action']);
	if(empty($action)){
		echo 1099;
		exit;
	} 
	//$action = 'select_message';
	switch($action){
		case 'send_message':
			$friend = clear_gpq($_REQUEST['rec_id']);			//接收方id
			$message = clear_gpq($_REQUEST['message']);			//私信内容
			$user = clear_gpq($_REQUEST['send_id']);			//发送方id
			$type = clear_gpq($_REQUEST['type_class']);			//私信类型
			$state_code = $privatemessage -> add_message($user, $friend, $message, $type);
			if($state_code == 1320){
				echo $state_code;			//私信发送失败
				exit;
			}else if($state_code == 1321){
				/* 当 id 等于 1327的时候 会有问题 */
				echo $state_code;			//发送失败 联系人信息更新失败
				exit;
			}else if($state_code == 1322){
				echo $state_code;			//发送失败 添加联系人失败
				exit;
			}else{
				echo $state_code;			//私信发送成功
			}
			break;
			
		case 'show_message':
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
				echo json_encode($message_list);
			}
		break;
	case 'contacts_list':
		//实时更新联系人列表  包含所有的联系关系 比如 我发给对方的私信 他没回复的
		//$user = $_SESSION['uid'];
		$user = 1314;
		$contacts_list = $privatemessage -> get_user_contacts_list_by_user($user);
		if($contacts_list == 1323){
			echo $contacts_list;
			exit;
		}else{
			//侧边栏的最近联系人列表
			foreach($contacts_list as $k => $v){
				if($v['user'] == $user){
					$list[$k] = $v;	  //当前用户接收的私信
				}
			}
			echo json_encode($list);
		}
		break;
		
	case 'read_message':								//未读私信列表  点击读取私信置已读
		//$myid = $_SESSION['uid'];
		$user = 1314;
		$friend = clear_gpq($_REQUEST['friend']);	
		$status_code = $privatemessage -> update_unread_message_by_user_friend($user, $friend);
		if($status_code == 1000){
			echo $status_code;
		}else{
			echo $status_code;
			exit;
		}
		break;
		
	case 'delete_mes':									//删除指定私信
		$user = clear_gpq($_REQUEST['fid']);			//当前用户ID
		$friend = clear_gpq($_REQUEST['tid']);	  		//发送者ID
		$mid = clear_gpq($_REQUEST['mid']);	  			//私信ID
		$state_code = $privatemessage -> hide_message($user, $friend, $mid);
		if($state_code == 1000){
			echo $state_code;
		}else{
			echo $state_code;
			exit;
		}
		break;
		
	case 'read_all':									//设置所有未读私信 为已读
		//$myid = $_SESSION['uid'];
		$user = 1314;										//当前用户ID
		$state_code = $privatemessage -> update_unread_message_by_user($user);				//更新私信的状态
		if($state_code == 1000){
			echo $state_code;  							//全部置已读成功
		}else{											//全部置已读失败
			echo $state_code;
			exit;
		}
		break;
	
	
	
	
	
	
	
	
	
	
	}