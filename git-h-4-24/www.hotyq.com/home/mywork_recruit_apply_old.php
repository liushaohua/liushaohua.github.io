<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once('../includes/common_home_inc.php');	
	
	$user = new user;
	$base = new base;
	$userprofile = new userprofile;
	$apply = new apply;
	$recruit = new recruit;
	//$cookie = $user -> get_cookie_user_info();
	$service_list = $base -> get_service_list($flash=0);	#所有服务名字;		
	$userid = $user_info['id'];
	$usertype = $user_info['user_type'];
	//招募id
	@$rid = intval($_REQUEST['recruit']);
	if($rid < 0 || $rid == 0){
		exit('请不要捣乱!');
	}
	
	$recruit_info = $recruit -> get_recruit_info($rid);
	$apply_list = $apply -> get_apply_list_by_recruit($rid);
	if($apply_list){
		foreach ($apply_list as $k => $v) {
			$e_service_id = $v['e_service_id'];
			$result = $apply -> get_second_service($e_service_id);
			if($result){
				$service_2_id = $result['service_2_id'];
				$service_2_name = $service_list[$service_2_id];
				$list[$service_2_name]['service_2_id'] = $service_2_id;
				$list[$service_2_name]['e_service_id'] = $e_service_id;
				@$list[$service_2_name]['num'] += 1;

			}
		}
		
		//跑报名数据
		foreach ($list as $key => $value) {
			$num = $value['num'];
			$eid = $value['e_service_id'];
			$result = $apply -> update_service_apply_num_1($eid,$num);
			if(!$result){
				echo '...failed...';
			}
		}
		//exit;
		

		$smarty -> assign('list',$list);
		
		@$e_service = intval($_REQUEST['service']);
		@$apply_sex = clear_gpq($_REQUEST['sex']);
		@$apply_result = clear_gpq($_REQUEST['result']);
		
		if(!$e_service){
			$service_2_array = array_shift($list);
			$e_service = $service_2_array['e_service_id'];					
		}
		$smarty -> assign('active_service_id',$e_service);

		$r = $apply -> get_apply_by_recruit_service($rid, $e_service, $userid);
		
		if($r){
			foreach ($r as $key => $value) {
				$uid = $value['uid'];
				$connect_result = $value['result'];
				
				$user_info = $user -> get_userinfo($uid);
				$user_type = $user_info['user_type'];
				if($user_type == 'user'){
					$u_profile = $userprofile -> get_user_profile($uid);
					$u_sex = $u_profile['sex'];
				}else{
					$u_sex = '';
				} 

				if($apply_sex){
					if ($apply_sex == $u_sex) {
						$value['u_sex'] = $u_sex;
						$value['user_type'] = $user_type;
						$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
						$value['u_face'] = $u_face;
						$value['u_name'] = $user_info['nickname'];

						//三级服务
						$e_apply_id = $value['id'];
						$apply_item = $apply -> get_item_service_by_e_apply_id($e_apply_id);
						
						foreach ($apply_item as $kk => $vv) {
							//$rr = $base -> get_service_info($vv['service_3_id']);
							@$third_service_name[$value['id']][$kk] = $service_list[$vv['service_3_id']];
						}		
						
						$value['third_service'] = $third_service_name;
						$apply_info_list[] = $value;
					}
				}else if($apply_result){
					if ($apply_result == $connect_result) {
						$value['u_sex'] = $u_sex;
						$value['user_type'] = $user_type;
						$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
						$value['u_face'] = $u_face;
						$value['u_name'] = $user_info['nickname'];

						//三级服务
						$e_apply_id = $value['id'];
						$apply_item = $apply	 -> get_item_service_by_e_apply_id($e_apply_id);
						
						foreach ($apply_item as $kk => $vv) {
							//$rr = $base -> get_service_info($vv['service_3_id']);
							@$third_service_name[$value['id']][$kk] = $service_list[$vv['service_3_id']];
						}		
						
						$value['third_service'] = $third_service_name;
						$apply_info_list[] = $value;
					}
				}else{
					$value['u_sex'] = $u_sex;
					$value['user_type'] = $user_type;
					$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
					$value['u_face'] = $u_face;
					$value['u_name'] = $user_info['nickname'];

					//三级服务
					$e_apply_id = $value['id'];
					$apply_item = $apply -> get_item_service_by_e_apply_id($e_apply_id);
					
					foreach ($apply_item as $kk => $vv) {
						//$rr = $base -> get_service_info($vv['service_3_id']);
						@$third_service_name[$value['id']][$kk] = $service_list[$vv['service_3_id']];
					}		
					
					$value['third_service'] = $third_service_name;
					$apply_info_list[] = $value;
				}
			}
			if (empty($apply_info_list) || !isset($apply_info_list)) {
				$apply_info_list = '';
				$smarty -> assign('apply_info_list',$apply_info_list);
			}else{
				$smarty -> assign('apply_info_list',$apply_info_list);	
			}
		}else{
			$apply_info_list = '';
			$smarty -> assign('apply_info_list',$apply_info_list);		
		}


	}else{
		$list = '';
		$smarty -> assign('list',$list);	
		$apply_info_list = '';
		$smarty -> assign('apply_info_list',$apply_info_list);
	}


	//print_r($list);
	//exit();
	$smarty -> assign('recruit_info',$recruit_info);
	$work_type = 'mywork_recruit'; 
	$smarty -> assign('work_type',$work_type);
	$smarty -> display("home/mywork_recruit_apply_old.html");