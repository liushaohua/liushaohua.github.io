<?php
	$PAGE_TYPE = "user";
	header("content-type: text/html; charset=utf-8");
	require_once ('../includes/common_home_inc.php');
	require_once (COMMON_PATH.'/userprofile.class.php');
	require_once (COMMON_PATH.'/album.class.php');
	require_once (COMMON_PATH.'/recruit.class.php');
	require_once (COMMON_PATH.'/collect.class.php');
	$user = new user();
	$base = new base();
	$recruit = new recruit();
	$collect = new collect();
	$user_msg_total = new user_msg_total;
	$userprofile = new userprofile();
	$user_profile = $userprofile -> get_user_profile($user_info["id"]);
	$user_role = $userprofile -> get_role_list_by_user($user_info["id"],$flash);
	if(!is_array($user_role)){
		$user_role = array(array('name' => '太忙了，忘记设置了！'));
	}
	$recommend_recruits = $recruit -> get_new_recruit_list('6');
	$service_3_list = array();
	if($recommend_recruits){
		foreach($recommend_recruits as $recommend_recruit){
			$recruit_type = $recruit -> get_recruit_type_info($recommend_recruit['type_id']);
			//$address_info = $base -> get_address_info($recommend_recruit['province_id'],$recommend_recruit['city_id'],$recommend_recruit['district_id']);
			$city = $base -> get_city_info($recommend_recruit['city_id'],$flash);
			$service_2_list = array();
			if(is_array($service_2_arr = $recruit -> get_service_list_by_recruit($recommend_recruit['id']))){
				foreach($service_2_arr as $v2){
					/*if(is_array($service_3_arr = $recruit -> get_service_3_list_by_eid($v2['id']))){
						foreach($service_3_arr as $v3){
							if($service_3_info = $base -> get_service_info($v3['service_3_id'])){
								$service_3_list[] = $service_3_info['name'];
							}
						}
					}*/
					if($service_2_info = $base -> get_service_info($v2['service_2_id'])){
						$service_2_list[] = $service_2_info['name'];
					}
				}
			}
			$timediff = strtotime($recommend_recruit['interview_end_time']) - time();
			if($timediff > 3600*48){
				$end_time = date('Y-m-d',strtotime($recommend_recruit['interview_end_time']));
			}else if($timediff > 0 && $timediff < 3600*48){
				$end_time = '剩余'.ceil($timediff/3600).'小时';
			}else{
				$end_time = '已过期';
			}
			
			$recommend_recruit_info[$recommend_recruit['id']] = array(
				'name' => $recommend_recruit['name'],
				'uid' => $recommend_recruit['uid'],
				'cover' => $recommend_recruit['cover_server_url'].$recommend_recruit['cover_path_url'],
				'interview_end_time' => $end_time,
				'status' => $recommend_recruit['status'],
				'address' => $city['cname'],
				'service_2_list' => $service_2_list
			);
		}
	}else{
		$recommend_recruit_info = array();
	}
	$address_info = $base -> get_address_info($user_profile['province_id'],$user_profile['city_id'],$user_profile['district_id']);
	$smarty -> assign('user_info',$user_info);
	$smarty -> assign('address_info',$address_info);
	$smarty -> assign('user_profile',$user_profile);
	$smarty -> assign('user_role',$user_role);
	if($user_profile['sex']){
		$smarty -> assign('sex',$COMMON_CONFIG["SEX"][$user_profile['sex']]);		
	}else{
		$smarty -> assign('sex','');
	}
	//$smarty -> assign('user_state',$COMMON_CONFIG["STATE"][$user_profile['state']]);
	$smarty -> assign('recommend_recruit_info',$recommend_recruit_info);
	

	/* qingfang  勿动 suntianxin  */

	//获取 用户中心的未读信息系 提示 
	$unread_mes_num = $user_msg_total -> get_user_msg_total_info($user_info["id"]);
	//获取我的工作信息 未读提示
	$mywork_info_num = $unread_mes_num['recruit_apply'] + $unread_mes_num['reply_invite'];
	//$mywork_info_num = $unread_mes_num['recruit_apply'] + $unread_mes_num['recruit_invite'] + $unread_mes_num['reply_apply'] + $unread_mes_num['reply_invite'];
	$smarty -> assign('mywork_info_num',$mywork_info_num);
	//获取我的未读私信 未读提示
	$smarty -> assign('message',$unread_mes_num['message']);

	$smarty -> display("home/user_index.html");

?>