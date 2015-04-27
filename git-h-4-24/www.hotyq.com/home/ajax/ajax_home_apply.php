<?php
	/*
	*	报名  ajax处理  suntianxing	
	*	日期  2014-12-09
	*/

	session_start();
	header("Content-type:text/html;charset=utf-8");
	require_once('../../includes/common_home_inc.php');
	$apply = new apply();
	$recruit = new recruit();
	$userprofile = new userprofile();
	$orgprofile = new orgprofile();
	$user = new user();
	$base = new base();
	$uid = $user_info["id"];
	$usertype = $user_info['user_type'];
	
	$action = clear_gpq($_REQUEST['action']);
	if(empty($action)){
		echo 1099;
		exit;
	} 

	switch($action){
		case "is_apply":
			echo json_encode(is_apply());
			break;
		case 'user_send_apply':
			$apply_info['applyer'] = $uid;
			$apply_info['rid'] = clear_gpq($_REQUEST['recruit']);
			$apply_info['r_uid'] = clear_gpq($_REQUEST['recruit_uid']);
			$apply_info['service'] = clear_gpq($_REQUEST['service']);
			$apply_info['service_3'] = clear_gpq($_REQUEST['service_3']);
			$apply_info['mobile'] = clear_gpq($_REQUEST['mobile']);
			$apply_info['email'] = clear_gpq($_REQUEST['email']);
			$apply_info['weixin'] = clear_gpq($_REQUEST['weixin']);
			$apply_info['qq'] = clear_gpq($_REQUEST['qq']);

			//var_dump($user_type);
			user_send_apply($apply_info);
			break;
		case 'close_recruit':
			$rid = clear_gpq($_REQUEST['rid']);
			close_recruit($rid);
			break;
		case 'get_applyer_connect_way':
			$aid = clear_gpq($_REQUEST['aid']);
			echo json_encode(get_applyer_connect_way($aid));
			break;
		case 'deal_result':
			$aid = clear_gpq($_REQUEST['aid']);
			$result = clear_gpq($_REQUEST['result']);
			deal_result($aid,$result);
			break;
		case 'deal_description':
			$aid = clear_gpq($_REQUEST['aid']);
			$des = clear_gpq($_REQUEST['des']);
			deal_description($aid,$des);
			break;
		case 'get_user_connect_way_and_check_recruit':
			$rid = intval(clear_gpq($_REQUEST['rid']));
			if($rid){
				$r = $recruit -> get_recruit_info($rid);
				if($r){
					$result['status'] = $r['status'];
				}else{
					$result['status'] = 'no';
				}
			}else{
				$result['status'] = 'wrong';
			}
			if($usertype == 'user'){
				$userprofile = new userprofile;
				$info = $userprofile -> get_user_profile($uid);
				$result['mobile'] = $info['contact_mobile'];
				$result['email'] = $info['contact_email'];
				$result['weixin'] = $info['contact_weixin'];
				$result['qq'] = $info['contact_qq'];
			}else{
				$orgprofile = new orgprofile;
				$info = $orgprofile -> get_org_profile($uid);
				$result['mobile'] = $info['contact_mobile'];
				$result['email'] = $info['contact_email'];
				$result['weixin'] = $info['contact_weixin'];
				$result['qq'] = $info['contact_qq'];
			}
			echo json_encode($result);
			break;
		case '':
	}
	
	//备注结果处理
	function deal_description($aid, $des){
		global $apply;
		$r = $apply -> update_apply_description($aid, $des);
		if($r){
			echo 1000;
		}else{
			echo 1380;
		}
	}
	//沟通结果处理
	function deal_result($aid,$result){
		global $apply;
		$r = $apply -> update_apply_result($aid, $result);
		if($r){
			echo 1000;
		}else{
			echo 1380;
		}
	}
	//获取用户 的其他联系方式
	function get_applyer_connect_way($aid){
		global $apply;
		$result = $apply -> get_apply($aid);
		if($result){
			return $result;
		}else{
			return 1379;
		}
	}
	//获取该用户报名过的所有 报名
	function is_apply(){
		global $apply;
		//当前uid 是否存在
		if( !isset($_POST['uid']) || empty($_POST['uid']) ) return get_state_info(1288);
		$uid = clear_gpq($_POST["uid"]);
		$result = $apply -> get_apply_list_by_user($uid);
		if($result){
			//结果集数组
			foreach($result as $v){
				$e_role_id_list[] = intval($v['e_service_id']);
			}
			//var_dump($e_role_id_list);
			return $e_role_id_list;
		}else{
			//没有任何报名  返回空数组
			$apply_list = array();
			return $apply_list;
		}
	}
	//招募方关闭招募
	function close_recruit($rid){
		global $recruit;
		$result = $recruit -> close_recruit_by_id($rid);
		if($result){
			echo 1000;
		}else{
			echo 1376;
		}
	}
	
	//报名方发送报名信息
	function user_send_apply($apply_info){
		global $apply,$recruit,$usertype,$user,$userprofile,$orgprofile,$uid;
		//至少填写一项联系方式
		if(empty($apply_info['mobile']) && empty($apply_info['email']) && empty($apply_info['weixin']) && empty($apply_info['qq'])){
			echo 1375;
			exit;
		}
		//mobile为必填项
		if(empty($apply_info['mobile'])){
			echo 1379;
			exit;
		}
		//检测报名的服务是否至少选了一项
		if(empty($apply_info['service_3'])){
			 echo 1378;
			 exit;
		}
		//不允许一个人报名同一个招募下的同一个服务
		$result = $apply -> check_apply_by_user($apply_info['applyer'],$apply_info['rid'],$apply_info['service']);
		if($result){
			echo 1374;
			exit;
		}else{
			//查询招募发布者的联系方式
			$r_uid = $apply_info['r_uid'];
			$recruiter_info = $user -> get_userinfo($r_uid);
			if($recruiter_info['user_type'] == 'user'){
				$userprofile = new userprofile;
				$recruiter_info = $userprofile -> get_user_profile($r_uid);
			}else{
				$orgprofile = new orgprofile;
				$recruiter_info = $orgprofile -> get_org_profile($r_uid);
			}

			$apply_info['r_mobile'] = $recruiter_info['contact_mobile'];
			$apply_info['r_email'] = $recruiter_info['contact_email'];
			$apply_info['r_weixin'] = $recruiter_info['contact_weixin'];
			$apply_info['r_qq'] = $recruiter_info['contact_qq'];

			//发送报名信息
			$result = $apply -> add_apply($apply_info);
			if($result){
				//报名成功 添加三级服务
				$service_3_str = trim($apply_info['service_3'],'|');
				if(strpos($service_3_str, '|')){
					$third_service_arr = explode('|',$service_3_str);	
				}else{
					$third_service_arr = array($service_3_str);
				}

				foreach($third_service_arr as $service_id){
					$r = $apply -> add_third_service($service_id, $apply_info['rid'], $result);
					if(!$r){
						echo 1376;
						exit;
					}
				}
				$user_type = clear_gpq($_REQUEST['user_type']);				
				$contact_info['contact_mobile'] = clear_gpq($_REQUEST['mobile']);
				$contact_info['contact_email'] = clear_gpq($_REQUEST['email']);
				$contact_info['contact_weixin'] = clear_gpq($_REQUEST['weixin']);
				$contact_info['contact_qq'] = clear_gpq($_REQUEST['qq']);				
				if($user_type =='user'){
					$re = $userprofile -> update_user_profile($uid,$contact_info);
					$userprofile -> get_user_profile($uid,$flash = 1);
					//var_dump($re);	
				}elseif($user_type =='org'){
					$re = $orgprofile -> update_org_profile($uid,$contact_info);
					$orgprofile -> get_org_profile($uid,$flash = 1);				
				}			
				echo 1000;
			}else{
				echo 1377;
				exit;
			}
		}
	}
?>