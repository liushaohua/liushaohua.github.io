<?php
	/*
	*	邀约  ajax处理  zhaOzhenhuan	
	*	日期  2015-02-09
	*/

	session_start();
	header("Content-type:text/html;charset=utf-8");
	require_once('../../includes/common_home_inc.php');
	//require_once(COMMON_PATH.'/invite5.class.php');
	//require_once(COMMON_PATH.'/recruit.class.php');
	//require_once(COMMON_PATH.'/base.class.php');
	
	$invite = new invite();
	$recruit = new recruit();
	$base = new base();
	$userprofile = new userprofile();
	$orgprofile = new orgprofile();
	//$user_info = $user -> get_cookie_user_info();
	//var_dump($cookie);
	$userid = $user_info['id'];
	
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
			$invite_info['rid'] = intval($_REQUEST['rid']);
			$invite_info['uid'] = intval($_REQUEST['uid']);
			$invite_info['r_uid'] = $userid;
			$invite_info['service'] = clear_gpq($_REQUEST['service']);
			$invite_info['service_3'] = clear_gpq($_REQUEST['service_3']);
			$invite_info['mobile'] = clear_gpq($_REQUEST['mobile']);
			$invite_info['email'] = clear_gpq($_REQUEST['email']);
			$invite_info['weixin'] = clear_gpq($_REQUEST['weixin']);
			$invite_info['qq'] = clear_gpq($_REQUEST['qq']);
			$invite_info['user_type'] = clear_gpq($_REQUEST['user_type']);
			user_send_invite($invite_info);
			break;
		case 'get_recruit_2_service';	
			$rid = intval($_REQUEST['rid']);
			get_recruit_2_service($rid);
			break;
		case 'get_3_service_by_2_service';	
			$e_id = intval($_REQUEST['e_id']);
			//$rid = intval($_REQUEST['rid']);
			get_3_service_by_2_service($e_id);
			break;
		case 'deal_result':
			$iid = intval($_REQUEST['iid']);
			$result = clear_gpq($_REQUEST['result']);
			deal_result($iid,$result);
			break;
		case 'deal_description':
			$iid = intval($_REQUEST['iid']);
			$des = clear_gpq($_REQUEST['des']);
			deal_description($iid,$des);
			break;						
	/*	case 'user_cancel_invite':
			$value = '0';
			$iid = clear_gpq($_REQUEST['iid']);
			user_update_invite($iid, $value);
			break;
		case 'user_again_invite':
			$value = '1';
			$iid = clear_gpq($_REQUEST['iid']);
		    user_update_invite($iid, $value);
		    break;
		case 'user_delete_invite':
			$iid = clear_gpq($_REQUEST['iid']); 
			delete_invite($iid);
			break;
		case 'recruit_sure_invite':
			$recruit_info['iid'] = clear_gpq($_REQUEST['iid']);
			$recruit_info['status'] = clear_gpq($_REQUEST['status']);
			$recruit_info['mobile'] = clear_gpq($_REQUEST['mobile']);
			$recruit_info['email'] = clear_gpq($_REQUEST['email']);
			$recruit_info['weixin'] = clear_gpq($_REQUEST['weixin']);
			$recruit_info['qq'] = clear_gpq($_REQUEST['qq']); 
		    recruit_sure_invite($recruit_info);
		    break;
		case 'recruit_refuse_invite':
		    $iid = clear_gpq($_REQUEST['iid']);
			$status = clear_gpq($_REQUEST['status']);
			recruit_refuse_invite($iid, $status);
		    break;
	*/		
	}
	//获取招募下的二级服务
	function get_recruit_2_service($rid){
		global $base,$recruit;
		if($rid < 1){
			echo 1099;
			exit;
		} 
		$result = $recruit -> get_service_list_by_recruit($rid);
		if($result){
			foreach($result as $k => $v){	
				$re = $base -> get_service_info($v['service_2_id']);
				$service_2_list[$v['id']] = $re['name'];
			}
			echo json_encode($service_2_list);
		}
	}
	//获取招募下二级服务的三级服务
	function get_3_service_by_2_service($e_id){
		global $recruit,$base;
		if($e_id <1){
			echo 1099;
			exit;
		} 
		$result = $recruit -> get_service_3_list_by_eid($e_id);
		
		if(is_array($result)){
			foreach($result as $k => $v){				
				$re = $base -> get_service_info($v['service_3_id']);
				$service_3_list[$v['service_3_id']] = $re['name'];
			}
			//var_dump($service_3_list);
			echo json_encode($service_3_list);
		}		
	}	
	//邀约方发送邀约信息
	function user_send_invite($invite_info){
		global $invite,$userprofile,$orgprofile,$userid;
		//检测邀约的服务是否至少选了一项
		
		if(empty($invite_info['service_3'])){
			 echo 1182;
			 exit;
		}
		//至少填写一项联系方式
		if(empty($invite_info['mobile']) && empty($invite_info['email']) && empty($invite_info['weixin']) && empty($invite_info['qq'])){
			echo 1181;
			exit;
		}
		if(empty($invite_info['mobile'])){
			echo 1183;
			exit;
		}
		//var_dump($invite_info);
		//不允许对一个人邀约同一个招募下的同一个服务
		$result = $invite -> check_invite_by_user($invite_info['uid'],$invite_info['rid'],$invite_info['service']);
		if($result){
			echo 1180;
			exit;
		}else{
			$contact_info['contact_mobile'] = $invite_info['mobile']; 
			$contact_info['contact_email']	= $invite_info['email']; 
			$contact_info['contact_weixin']	= $invite_info['weixin'];
			$contact_info['contact_qq']	= $invite_info['qq']; 
			if($invite_info['user_type'] =='user'){
				$re = $userprofile -> update_user_profile($userid,$contact_info);
				$userprofile -> get_user_profile($userid,$flash = 1);
				//var_dump($re);	
			}elseif($invite_info['user_type'] =='org'){
				$re = $orgprofile -> update_org_profile($userid,$contact_info);
				$orgprofile -> get_org_profile($userid,$flash = 1);				
			}		
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
					$re = $invite -> add_third_service($service_id, $invite_info['rid'], $result);
					if(!$re){
						echo 1185;
						exit;
					}
				}		
				echo 1000;
			}else{
				echo 1184;
				exit;
			}
		}
	}

	//招募方方更改接收的邀约信息及状态 确认接受邀约
	function recruit_sure_invite($value){
		global $invite;
		$iid = $value['iid'];
		$state_code = $invite -> recruit_update_invite($value);
		if($state_code){
			//回取邀约者的个人信息
			/*
			$result = $invite -> get_invite($iid);
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
	function recruit_refuse_invite($iid, $value){
		global $invite;
		$state_code = $invite -> recruit_refuse_invite($iid, $value);
		if($state_code){
			echo 1000;
		}else{
			echo 1371;
			exit;
		}
	}

	//邀约方方更改自己的邀约状态
	function user_update_invite($iid, $value){
		global $invite;
		$state_code = $invite -> user_update_invite($iid, $value);
		if($state_code){
			echo 1000;
		}else{
			echo 1372;
			exit;
		}
	}
	
	//删除邀约
	function delete_invite($iid){
		global $invite;
		$state_code = $invite -> user_delete_invite($iid);
		if($state_code){
			echo 1000;
		}else{
			echo 1373;
			exit;
		}
	}
	//备注结果处理
	function deal_description($iid, $des){
		global $invite;
		$re = $invite -> update_invite_description($iid, $des);
		if($re){
			echo 1000;
		}else{
			echo 1380;
		}
	}
	//沟通结果处理
	function deal_result($iid,$result){
		global $invite;
		$re = $invite -> update_invite_result($iid, $result);
		if($re){
			echo 1000;
		}else{
			echo 1380;
		}
	}	
?>