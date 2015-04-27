<?php
	/*
	*	报名  ajax处理  suntianxing	
	*	日期  2014-12-09
	*/

	session_start();
	header("Content-type:text/html;charset=utf-8");
	require_once('../../includes/common_inc_test.php');
	require_once('../../suntianxing/apply/apply.class.php');
	
	$apply = new apply();

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
		case 'user_send_apply':
			//$uid = $cookie[0];
			$apply_info['rid'] = clear_gpq($_REQUEST['recruit']);
			$apply_info['applyer'] = clear_gpq($_REQUEST['applyer']);
			$apply_info['service'] = clear_gpq($_REQUEST['service']);
			$apply_info['service_3'] = clear_gpq($_REQUEST['service_3']);
			$apply_info['mobile'] = clear_gpq($_REQUEST['mobile']);
			$apply_info['email'] = clear_gpq($_REQUEST['email']);
			$apply_info['weixin'] = clear_gpq($_REQUEST['weixin']);
			$apply_info['qq'] = clear_gpq($_REQUEST['qq']);
			user_send_apply($apply_info);
			break;
		case 'user_cancel_apply':
			$value = '0';
			$aid = clear_gpq($_REQUEST['aid']);
			user_update_apply($aid, $value);
			break;
		case 'user_again_apply':
			$value = '1';
			$aid = clear_gpq($_REQUEST['aid']);
		    user_update_apply($aid, $value);
		    break;
		case 'user_delete_apply':
			$aid = clear_gpq($_REQUEST['aid']); 
			delete_apply($aid);
			break;
		case 'recruit_sure_apply':
			$recruit_info['aid'] = clear_gpq($_REQUEST['aid']);
			$recruit_info['status'] = clear_gpq($_REQUEST['status']);
			$recruit_info['mobile'] = clear_gpq($_REQUEST['mobile']);
			$recruit_info['email'] = clear_gpq($_REQUEST['email']);
			$recruit_info['weixin'] = clear_gpq($_REQUEST['weixin']);
			$recruit_info['qq'] = clear_gpq($_REQUEST['qq']); 
		    recruit_sure_apply($recruit_info);
		    break;
		case 'recruit_refuse_apply':
		    $aid = clear_gpq($_REQUEST['aid']);
			$status = clear_gpq($_REQUEST['status']);
			recruit_refuse_apply($aid, $status);
		    break;
	}
	//报名方发送报名信息
	function user_send_apply($apply_info){
		global $apply;
		//检测报名的服务是否至少选了一项
		if(empty($apply_info['service_3'])){
			 echo 1375;
			 exit;
		}
		//不允许一个人报名同一个招募下的同一个服务
		$result = $apply -> check_apply_by_user($apply_info['applyer'],$apply_info['rid'],$apply_info['service']);
		if($result){
			echo 1374;
			exit;
		}else{
			//查询招募发布者的联系方式
			//$recruit_id = $apply_info['rid'];


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
				echo 1000;
			}else{
				echo 1377;
				exit;
			}
		}
	}

	//招募方方更改接收的报名信息及状态 确认接受报名
	function recruit_sure_apply($value){
		global $apply;
		$aid = $value['aid'];
		$state_code = $apply -> recruit_update_apply($value);
		if($state_code){
			//回取报名者的个人信息
			/*
			$result = $apply -> get_apply($aid);
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
	//招募方方更改接收的报名信息及状态 拒绝接受报名
	function recruit_refuse_apply($aid, $value){
		global $apply;
		$state_code = $apply -> recruit_refuse_apply($aid, $value);
		if($state_code){
			echo 1000;
		}else{
			echo 1371;
			exit;
		}
	}

	//报名方方更改自己的报名状态
	function user_update_apply($aid, $value){
		global $apply;
		$state_code = $apply -> user_update_apply($aid, $value);
		if($state_code){
			echo 1000;
		}else{
			echo 1372;
			exit;
		}
	}
	
	//删除报名
	function delete_apply($aid){
		global $apply;
		$state_code = $apply -> user_delete_apply($aid);
		if($state_code){
			echo 1000;
		}else{
			echo 1373;
			exit;
		}
	}
?>