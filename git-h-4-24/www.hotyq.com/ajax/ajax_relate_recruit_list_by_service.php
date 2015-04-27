<?php
	//右侧-与你相关-推荐招募-ajax;
	header("Content-type:text/html;charset=utf-8");
	require_once('../includes/common_inc.php');
 	require_once(COMMON_PATH."/recruit.class.php");
	require_once(COMMON_PATH.'/find_recruit.class.php');
	require_once(COMMON_PATH.'/redis.class.php');
	require_once(COMMON_PATH.'/redis_find.class.php');
	require_once(COMMON_PATH.'/service.class.php');
	$recruit = new recruit();
	$userprofile = new userprofile();
	$base = new base();
	$recruit = new recruit();
	$find_recruit = new find_recruit();
	$user = new user();
	$service = new service();	
	//$action = clear_gpq($_REQUEST["action"]);
	if( !isset($_REQUEST["action"]) || empty($_REQUEST["action"]) ){
		$base -> go_404();
	}	
	$action = clear_gpq($_REQUEST["action"]);	
	switch($action){
		case "login_recruit_status":	#登录状态下
			$uid = intval($_REQUEST["uid"]);
			if( !isset($_REQUEST["uid"]) || empty($_REQUEST["uid"]) ){
				header("site/error_tips.php?state_code=1099"); 
			}		
			login_recruit_status($uid);
			break;
		case "nologin_recruit_status":	#没登陆状态下
			nologin_recruit_status();
			break;	
	}	
	//$user_type = clear_gpq($_REQUEST['user_type']);
	function login_recruit_status($uid){		##登录状态下
		global $recruit,$userprofile,$base,$find_recruit,$user,$service;
		$result_u = $service -> get_e_service_by_user($uid);		#如登录获取当前用户所有服务
	
		if(count($result_u) > 0){
			foreach($result_u as $k => $v){
				$key[$v['service_2_id']]= $find_recruit -> get_find_key('service_2',$v['service_2_id']);	#通过二级服务获取键
			}			
			$new_key[] = $find_recruit -> get_key_by_sunion($key);
			$recruit_id_list = $find_recruit -> get_recruit_list_by_sinter($new_key,1,20);

			//$recruit_id_res_num = $recruit_id_list['count'];
			$recruit_id_new_list = $recruit_id_list['list'];	#处理相同的 array_diff(array1,array2,array3...)	#new
			//$recruit_id_list[] = $recruit_id_list['list'];
			//var_dump($recruit_id_new_list);
			$resu = $recruit -> get_recruit_list_by_user($uid); 	#用户自己发的招募id列表
			if($resu){
				foreach($resu as $k => $v){
					$res[] = $v['id'];	
				}				
			}else{
				$res = array();
			}
			//var_dump($res);
			$recruit_id_new_list = array_diff($recruit_id_new_list,$res);		 #两个数组取差集
			$recruit_id_res_num = count($recruit_id_new_list);		#计算新数组数量
			//var_dump($recruit_id_new_list);
			if($recruit_id_res_num < 10){		#如果取差集后数组数量小于10,下面补充数据
				$add_key = $find_recruit -> get_find_key('all','');
				$add_key_arr[] = $add_key;
				$add_recruit_res = $find_recruit -> get_recruit_list_by_sinter($add_key_arr,1,10-$recruit_id_res_num);		#如果上面的出的结果集小于10,剩余取all键的值放入数组
				foreach($add_recruit_res['list'] as $v){
					$recruit_id_new_list[] = $v;	#new
				}
			}else{		#如果上面的出的结果集大于10
				$recruit_id_new_list = array_slice($recruit_id_new_list,0,10);				
			}
			//var_dump($recruit_id_new_list);	exit;
		}else{		#如果结果集全时自己发的招募

			$add_key = $find_recruit -> get_find_key('all','');
			$add_key_arr[] = $add_key;
			$recruit_id_list = $find_recruit -> get_recruit_list_by_sinter($add_key_arr,1,10);
			//var_dump($recruit_id_list);
			foreach($recruit_id_list['list'] as $v){
				$recruit_id_new_list[] = $v;	#new
			}	
		}		
		//var_dump($recruit_id_new_list);
		foreach ($recruit_id_new_list as $k => $v) {	#new
			$recruit_info = $recruit -> get_recruit_info($v);
			$recruit_r_list[$k]['rid'] = $recruit_info['id'];
			$recruit_r_list[$k]['name'] = $recruit_info['name'];
			$recruit_r_list[$k]['cover_server_url'] = $recruit_info['cover_server_url'];   
			$recruit_r_list[$k]['cover_path_url'] = $recruit_info['cover_path_url'];
			$user_info_result = $user -> get_userinfo($recruit_info['uid']);
			$recruit_r_list[$k]['u_name'] = $user_info_result['nickname'];
			$recruit_r_list[$k]['interview_end_time'] = date( "m月d日", strtotime($recruit_info['interview_end_time']));

		}
		echo json_encode($recruit_r_list);		
	}
	function nologin_recruit_status(){		#没登陆状态下
		global $recruit,$userprofile,$base,$find_recruit,$user,$service;
		$add_key = $find_recruit -> get_find_key('all','');
		$add_key_arr[] = $add_key;
		$recruit_id_list = $find_recruit -> get_recruit_list_by_sinter($add_key_arr,1,10);
		//var_dump($recruit_id_list);
		foreach($recruit_id_list['list'] as $v){
			$recruit_id_new_list[] = $v;
		}	

		foreach ($recruit_id_new_list as $k => $v) {
			$recruit_info = $recruit -> get_recruit_info($v);
			$recruit_r_list[$k]['rid'] = $recruit_info['id'];
			$recruit_r_list[$k]['name'] = $recruit_info['name'];
			$recruit_r_list[$k]['cover_server_url'] = $recruit_info['cover_server_url'];   
			$recruit_r_list[$k]['cover_path_url'] = $recruit_info['cover_path_url'];
			$user_info_result = $user -> get_userinfo($recruit_info['uid']);
			$recruit_r_list[$k]['u_name'] = $user_info_result['nickname'];
			$recruit_r_list[$k]['interview_end_time'] = date( "m月d日", strtotime($recruit_info['interview_end_time']));
		}
		echo json_encode($recruit_r_list);				
	}
?>
			