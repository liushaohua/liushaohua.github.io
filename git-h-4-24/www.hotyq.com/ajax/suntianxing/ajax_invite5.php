<?php
	/*
	*	邀约  ajax处理  zhaOzhenhuan	
	*	日期  2015-02-09
	*/

	session_start();
	header("Content-type:text/html;charset=utf-8");
	require_once('../../includes/common_inc_test.php');
	require_once('../../suntianxing/invite/invite5.class.php');
	
	$invite = new invite();

	//$cookie = $user -> get_cookie_user_info();
	//var_dump($cookie);
	
	
	$action = clear_gpq($_REQUEST['action']);
	if(empty($action)){
		echo 1099;
		exit;
	} 
	
	//$user = $cookie[0];
	//$uid = 1103;
	
	

	switch($action){
		case 'user_send_invite':
			//$uid = $cookie[0];
			$invite_info['rid'] = clear_gpq($_REQUEST['recruit']);
			$invite_info['uid'] = clear_gpq($_REQUEST['uid']);
			$invite_info['service'] = clear_gpq($_REQUEST['service']);
			$invite_info['service_3'] = clear_gpq($_REQUEST['service_3']);
			$invite_info['mobile'] = clear_gpq($_REQUEST['mobile']);
			$invite_info['email'] = clear_gpq($_REQUEST['email']);
			$invite_info['weixin'] = clear_gpq($_REQUEST['weixin']);
			$invite_info['qq'] = clear_gpq($_REQUEST['qq']);
			user_send_invite($invite_info);
			break;
		case 'user_cancel_invite':
			$value = '0';
			$aid = clear_gpq($_REQUEST['aid']);
			user_update_invite($aid, $value);
			break;
		case 'user_again_invite':
			$value = '1';
			$aid = clear_gpq($_REQUEST['aid']);
		    user_update_invite($aid, $value);
		    break;
		case 'user_delete_invite':
			$aid = clear_gpq($_REQUEST['aid']); 
			delete_invite($aid);
			break;
		case 'recruit_sure_invite':
			$recruit_info['aid'] = clear_gpq($_REQUEST['aid']);
			$recruit_info['status'] = clear_gpq($_REQUEST['status']);
			$recruit_info['mobile'] = clear_gpq($_REQUEST['mobile']);
			$recruit_info['email'] = clear_gpq($_REQUEST['email']);
			$recruit_info['weixin'] = clear_gpq($_REQUEST['weixin']);
			$recruit_info['qq'] = clear_gpq($_REQUEST['qq']); 
		    recruit_sure_invite($recruit_info);
		    break;
		case 'recruit_refuse_invite':
		    $aid = clear_gpq($_REQUEST['aid']);
			$status = clear_gpq($_REQUEST['status']);
			recruit_refuse_invite($aid, $status);
		    break;
	}
	//邀约方发送邀约信息
	function user_send_invite($invite_info){
		global $invite;
		//检测邀约的服务是否至少选了一项
		
		if(empty($invite_info['service_3'])){
			 echo 1375;
			 exit;
		}
		//不允许一个人邀约同一个招募下的同一个服务
		$result = $invite -> check_invite_by_user($invite_info['uid'],$invite_info['rid'],$invite_info['service']);
		if($result){
			echo 1374;
			exit;
		}else{
			$result = $invite -> add_invite($invite_info);
			if($result){
				//邀约成功 添加三级服务
				$service_3_str = trim($invite_info['service_3'],'|');
				if(strpos($service_3_str, '|')){
					$third_service_arr = explode('|',$service_3_str);	
				}else{
					$third_service_arr = array($service_3_str);
				}
				foreach($third_service_arr as $service_id){
					$r = $invite -> add_third_service($service_id, $invite_info['rid'], $result);
					if(!$r){
						echo 1376;
						exit;
					}
				}		
				echo 1000;
			}else{
				echo 1377;
				exit;
			}
		}
	}

	//招募方方更改接收的邀约信息及状态 确认接受邀约
	function recruit_sure_invite($value){
		global $invite;
		$aid = $value['aid'];
		$state_code = $invite -> recruit_update_invite($value);
		if($state_code){
			//回取邀约者的个人信息
			/*
			$result = $invite -> get_invite($aid);
			if($result){
				echo json_encode($result); 
			}else{
				echo 1374;
				exit;
			}*/
			echo 1000;
		}else{
			echo 1370;
			exit;
		}
	}
	//招募方方更改接收的邀约信息及状态 拒绝接受邀约
	function recruit_refuse_invite($aid, $value){
		global $invite;
		$state_code = $invite -> recruit_refuse_invite($aid, $value);
		if($state_code){
			echo 1000;
		}else{
			echo 1371;
			exit;
		}
	}

	//邀约方方更改自己的邀约状态
	function user_update_invite($aid, $value){
		global $invite;
		$state_code = $invite -> user_update_invite($aid, $value);
		if($state_code){
			echo 1000;
		}else{
			echo 1372;
			exit;
		}
	}
	
	//删除邀约
	function delete_invite($aid){
		global $invite;
		$state_code = $invite -> user_delete_invite($aid);
		if($state_code){
			echo 1000;
		}else{
			echo 1373;
			exit;
		}
	}
?>