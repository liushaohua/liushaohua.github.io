<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');
	require_once ('../../common/userprofile.class.php');
	require_once ('../../common/album.class.php');
	$user = new user();
	$base = new base();
	$album = new album();
	$service = new service();
	
	$uid = $user_info["id"];
	$user_type = $user_info["user_type"];
	
	//获取红服务
	$service_list = $base -> get_service_list();
	//获取用户的所选的服务
	$service_array = $service -> get_e_service_by_user($uid);
	$i=0;
	if($service_array){
		foreach($service_array as $key=>$value){
			$i++;
			$service_2_id = $value['service_2_id'];
			//一级服务
			$service_1_name = $service_list[$value['service_1_id']];
			$user_service[$service_2_id]['service_1_id'] = $value['service_1_id'];
			$user_service[$service_2_id]['service_1_name'] = $service_1_name;
			//二级服务
			$service_2_name = $service_list[$value['service_2_id']];
			$user_service[$service_2_id]['service_2_id'] = $value['service_2_id'];
			$user_service[$service_2_id]['service_2_name'] = $service_2_name;
			//三级服务
			if($value['service_3_id']){
					$service_3_name = $service_list[$value['service_3_id']];
					$user_service[$service_2_id]['service_3'][$key]['id'] = $value['service_3_id'];
					$user_service[$service_2_id]['service_3'][$key]['name'] = $service_3_name;
			}
			if(!isset($user_service[$service_2_id]['service_3']) || empty($user_service[$service_2_id]['service_3'])){
				$user_service[$service_2_id]['service_3'] = array();
			}	
		}
		foreach ($user_service as $k=>$v) {
			$service_3_arr = $v['service_3'];
			$new_service_3 = array_values($service_3_arr);
			$user_service[$k]['service_3'] = $new_service_3;
		}
	}else{
		$user_service = '';
	}
	print_r(json_encode($user_service));
	print_r($user_service);
	
	exit;
	//服务一级列表 分配到页面
	$service_1_list = $base -> get_service_list_by_parentid();
	$smarty -> assign('service_1_list',$service_1_list);


	$smarty -> assign('user_service',json_encode($user_service));
	$smarty -> display("home/org_profile_service.html");
?>