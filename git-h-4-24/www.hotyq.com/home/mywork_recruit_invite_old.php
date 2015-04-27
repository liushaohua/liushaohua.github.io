<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once('../includes/common_home_inc.php');	
	//require_once ("../../common/invite.class.php");	
	$invite = new invite();
	$user = new user();
	$userprofile = new userprofile();
	$recruit = new recruit;
	$base = new base;
	$service_list = $base -> get_service_list($flash=0);	#所有服务名字;	
	$userid = $user_info['id'];
	$usertype = $user_info['user_type'];
	@$rid = intval($_REQUEST['recruit']);
	if($rid < 0 || $rid == 0){
		exit('系统错误!');
	}
	$recruit_info = $recruit -> get_recruit_info($rid);		#获取招募详情
	$invite_list = $invite -> get_invite_list_by_recruit($rid);	#获取招募下的所有邀约
	if($invite_list){
		foreach ($invite_list as $k => $v) {
			$e_service_id = $v['e_service_id'];
			$result = $invite -> get_second_service($e_service_id);	#获得二级服务
			if($result){
				$service_2_id = $result['service_2_id'];
				
				//$re = $base -> get_service_info($service_2_id);
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
			$result = $invite -> update_service_invite_num_1($eid,$num);
			if(!$result){
				echo '...failed...';
			}
		}
		//exit;


		$smarty -> assign('list',$list);
		//获取招募的服务id	传入条件来筛选结果
		@$e_service = intval($_REQUEST['service']);
		@$invite_sex = clear_gpq($_REQUEST['sex']);
		@$invite_result = clear_gpq($_REQUEST['result']);
		if(!$e_service){
			$service_2_array = array_shift($list);
			$e_service = $service_2_array['e_service_id'];		
		}
		$smarty -> assign('active_service_id',$e_service);

		$result = $invite -> get_invite_by_recruit_service($rid, $e_service); #通过关联 e_service 和 招募rid 获取其下的所有邀约 *
		if($result){
			foreach ($result as $key => $value) {
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
				if($invite_sex){
					if ($invite_sex == $u_sex) {
						$value['u_sex'] = $u_sex;
						$value['user_type'] = $user_type;
						$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
						$value['u_face'] = $u_face;
						$value['u_name'] = $user_info['nickname'];
						$e_invite_id = $value['id'];
						$invite_item = $invite -> get_item_service_by_e_invite_id($e_invite_id);	#获取三级服务
						
						foreach ($invite_item as $kk => $vv) {
							//$rr = $base -> get_service_info($vv['service_3_id']);							
							@$third_service_name[$value['id']][$kk] = $service_list[$vv['service_3_id']];
						}		
						
						$value['third_service'] = $third_service_name;
						$invite_info_list[] = $value;
					}
				}else if($invite_result){
					if ($invite_result == $connect_result) {
						$value['u_sex'] = $u_sex;
						$value['user_type'] = $user_type;
						$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
						$value['u_face'] = $u_face;
						$value['u_name'] = $user_info['nickname'];
						$e_invite_id = $value['id'];
						$invite_item = $invite -> get_item_service_by_e_invite_id($e_invite_id);	#获取三级服务						
						foreach ($invite_item as $kk => $vv) {
							//$re = $base -> get_service_info($vv['service_3_id']);							
							@$third_service_name[$value['id']][$kk] = $service_list[$vv['service_3_id']];
						}						
						$value['third_service'] = $third_service_name;
						$invite_info_list[] = $value;
					}
				}else{
					$value['u_sex'] = $u_sex;
					$value['user_type'] = $user_type;
					$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
					$value['u_face'] = $u_face;
					$value['u_name'] = $user_info['nickname'];
					$e_invite_id = $value['id'];
					$invite_item = $invite -> get_item_service_by_e_invite_id($e_invite_id);
					
					foreach ($invite_item as $kk => $vv) {
						//$rr = $base -> get_service_info($vv['service_3_id']);
						@$third_service_name[$value['id']][$kk] = $service_list[$vv['service_3_id']];
					}					
					$value['third_service'] = $third_service_name;
					$invite_info_list[] = $value;
				}
			}
			if (empty($invite_info_list) || !isset($invite_info_list)) {
				$invite_info_list = '';
				$smarty -> assign('invite_info_list',$invite_info_list);
			}else{
				$smarty -> assign('invite_info_list',$invite_info_list);	
			}
		}else{
			$invite_info_list = '';
			$smarty -> assign('invite_info_list',$invite_info_list);		
		}


	}else{
		$list = '';
		$smarty -> assign('list',$list);	
		$invite_info_list = '';
		$smarty -> assign('invite_info_list',$invite_info_list);
	}	
	//var_dump($invite_info_list);
	$smarty -> assign('recruit_info',$recruit_info);
	
	
	$work_type = 'mywork_recruit'; 
	$smarty -> assign('work_type',$work_type);
	$smarty -> display("home/mywork_recruit_invite_old.html");