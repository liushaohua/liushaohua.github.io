<?php
	//右侧-猜你喜欢-红人-ajax
	header("Content-type:text/html;charset=utf-8");
	require_once('../includes/common_inc.php');
 	require_once(COMMON_PATH."/user.class.php");
	require_once(COMMON_PATH.'/find_user.class.php');
	require_once(COMMON_PATH.'/redis.class.php');
	require_once(COMMON_PATH.'/redis_find.class.php');
	require_once(COMMON_PATH.'/service.class.php');
	$user = new user();
	$userprofile = new userprofile();
	$base = new base();
	$user = new user();
	$find_user = new find_user();
	$user = new user();
	$service = new service();
	//判断action是否存在
	//$action = clear_gpq($_REQUEST["action"]);
	if( !isset($_REQUEST["action"]) || empty($_REQUEST["action"]) ){
		$base -> go_404();	#报错系统错误
	}	
	$action = clear_gpq($_REQUEST["action"]);	
	switch($action){
		case "login_user_status":	#登录状态下
			$uid = intval($_REQUEST["uid"]);
			if( !isset($_REQUEST["uid"]) || empty($_REQUEST["uid"]) ){
				header("site/error_tips.php?state_code=1099"); 	#报错系统错误
			}		
			login_user_status($uid);
			break;
		case "nologin_user_status":	#没登陆状态下
			nologin_user_status();
			break;	
	}	
	//$user_type = clear_gpq($_REQUEST['user_type']);
	function login_user_status($uid){		##登录状态下
		global $user,$userprofile,$base,$find_user,$user,$service,$flash;
		$result_u = $service -> get_e_service_by_user($uid);		#如登录获取当前用户所有服务
		//echo 'user';	
		if(count($result_u) > 0){
			foreach($result_u as $k => $v){
				$key[$v['service_2_id']]= $find_user -> get_find_key('service_2',$v['service_2_id']);	#通过二级服务获取键
			}			
			$new_key[] = $find_user -> get_key_by_sunion($key);
			$user_id_list = $find_user -> get_user_list_by_sinter($new_key,1,20);
			$user_id_res_num = $user_id_list['count'];
			$user_id_new_list = $user_id_list['list'];
			//$user_id_list[] = $user_id_list['list'];
			//var_dump($user_id_new_list);
			if($user_id_res_num < 5){		
				$add_key = $find_user -> get_find_key('all','');
				$add_key_arr[] = $add_key;
				$add_user_res = $find_user -> get_user_list_by_sinter($add_key_arr,1,20-$user_id_res_num);		#如果上面的出的结果集小于10,剩余取all键的值放入数组
				foreach($add_user_res['list'] as $v){
					$user_id_new_list[] = $v;
				}
			}
			//var_dump($user_id_new_list);	
		}else{		#如果结果集全时自己发的招募

			$add_key = $find_user -> get_find_key('all','');
			$add_key_arr[] = $add_key;
			$user_id_list = $find_user -> get_user_list_by_sinter($add_key_arr,1,20);
			//var_dump($user_id_list);
			foreach($user_id_list['list'] as $v){
				$user_id_new_list[] = $v;
			}	

		}
		unset($user_id_new_list[array_search($uid,$user_id_new_list)]);	#去除自己
		//var_dump($user_id_new_list);
		foreach ($user_id_new_list as $k => $v) {
			$user_info_res = $user -> get_userinfo($v,$flash);
			if(!empty($user_info_res['nickname']) && !empty($user_info_res['icon_server_url'])){
				$user_r_list[$k]['uid'] = $user_info_res['id'];
				$user_r_list[$k]['name'] = $user_info_res['nickname'];
				$user_r_list[$k]['icon_server_url'] = $user_info_res['icon_server_url'];   
				$user_r_list[$k]['icon_path_url'] = $user_info_res['icon_path_url'];
			}
		}		
		@$user_r_list = array_slice($user_r_list,0,4);	
		echo json_encode($user_r_list);		
	}
	function nologin_user_status(){		#没登陆状态下
		global $user,$userprofile,$base,$find_user,$user,$service,$flash;
		$add_key = $find_user -> get_find_key('all','');
		$add_key_arr[] = $add_key;
		$user_id_list = $find_user -> get_user_list_by_sinter($add_key_arr,1,20);
		//var_dump($user_id_list);
		foreach($user_id_list['list'] as $v){
			$user_id_new_list[] = $v;
		}	
		foreach ($user_id_new_list as $k => $v) {
			$user_info_res = $user -> get_userinfo($v,$flash);
			if(!empty($user_info_res['nickname']) && !empty($user_info_res['icon_server_url'])){
				$user_r_list[$k]['uid'] = $user_info_res['id'];
				$user_r_list[$k]['name'] = $user_info_res['nickname'];
				$user_r_list[$k]['icon_server_url'] = $user_info_res['icon_server_url'];   
				$user_r_list[$k]['icon_path_url'] = $user_info_res['icon_path_url'];
			}
		}
		$user_r_list = array_slice($user_r_list,0,4);	
		echo json_encode($user_r_list);				
	}	

?>
			