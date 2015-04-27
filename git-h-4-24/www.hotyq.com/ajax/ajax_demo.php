<?php
	header("Content-type:text/html;charset=utf-8");
	require_once('../includes/common_inc.php');
 	require_once(COMMON_PATH."/recruit.class.php");
	require_once(COMMON_PATH.'/find_recruit.class.php');
	require_once(COMMON_PATH.'/redis.class.php');
	require_once(COMMON_PATH.'/redis_find.class.php');
	$recruit = new recruit();
	$userprofile = new userprofile();
	$base = new base();
	$recruit = new recruit();
	$find_recruit = new find_recruit();
	//判断action是否存在
	if( !isset($_REQUEST["uid"]) || empty($_REQUEST["uid"]) ){
		exit('非法操作！');
	}
	$uid = clear_gpq($_REQUEST["uid"]);
	//1 获取登陆用户的角色信息
	$user_role_list = $userprofile -> get_role_list_by_user($uid);
	//2 根据角色id 获取相关招募
	$key_arr = array();
	$child_key_arr = array();
	foreach($user_role_list as $v){
		if($v['parent_id'] == '0'){
			//父
			$key = $find_recruit -> get_find_key('user_level_1_role',$v['id']);
			$tmp_arr[] = $key;
		}elseif($v['parent_id'] == '-1'){
			//自定义
		}else{
			//系统
			$key = $find_recruit -> get_find_key('user_level_2_role',$v['id']);
			$tmp_arr[] = $key;
		}
	}
	$new_key = $find_recruit -> get_key_by_sunion($tmp_arr);
	$key_arr[] = $new_key;
	$find_recruit_res = $find_recruit -> get_recruit_list_by_sinter($key_arr,0,10);
	$recruit_id_list = $find_recruit_res['list'];
	$recruit_num = count($recruit_id_list);
	if($recruit_num < 10){
		//按键查找不足10条  提最新的
		$add_key = $find_recruit -> get_find_key('all','');
		$add_key_arr[] = $add_key;
		$add_recruit_res = $find_recruit -> get_recruit_list_by_sinter($add_key_arr,0,10-$recruit_num);
		foreach($add_recruit_res['list'] as $v){
			$recruit_id_list[] = $v;
		}
	}
	//print_r($recruit_id_list);exit;
	foreach($recruit_id_list as $k => $v){
		$recruit_list[$k] = $recruit -> get_recruit_info($v);
	}
	//处理下
	foreach($recruit_list as $k=>$v){
		$arr[$k]['id'] = $v['id'];
		$arr[$k]['recruit_city_name'] = $base -> get_city_info($v['city_id']);
		$arr[$k]['sys_start_time'] = date('m.d',strtotime($v['sys_start_time']));
		$arr[$k]['type_info'] = $recruit -> get_recruit_type_info($v['type_id']);
	}
	$recruit_list = $arr;
	echo json_encode($recruit_list);
?>
			