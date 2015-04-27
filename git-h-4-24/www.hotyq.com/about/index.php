<?php
	header("content-type: text/html; charset=utf-8"); 
	require_once('../includes/common_inc.php');
  	//require_once (COMMON_PATH.'/user.class.php');
	$user = new user();
	$userprofile = new userprofile();
	$base = new base();
	$recruit = new recruit;

	if(isset($_REQUEST['type'])){
		$temp_type = $_REQUEST['type'];
	}
	if(!isset($temp_type) || !in_array($temp_type , array('index','business','joinus','privacy','copyright','sitemap','helpcenter','agreement','honest','play','why'))){
		$temp_type = 'index';
	}
	//猜你喜欢红人
	//$youlike_id_list = $user -> get_rank_user_list(4);
	//var_dump($youlike_id_list);
	// foreach($youlike_id_list as $k => $v){
		// $youlike_id_list[$k]['info'] = $userprofile -> get_user_profile($v['id']);
	// }
	// foreach($youlike_id_list as $k => $v){
		// $like_arr[$k]['id'] = $v['id'];
		// $like_arr[$k]['nickname'] = $v['nickname'];
		// $like_arr[$k]['icon_server_url'] = $v['icon_server_url'];
		// $like_arr[$k]['icon_path_url'] = $v['icon_path_url'];
		// $role_info = $userprofile -> get_role_list_by_user($v['id'],$flash);
		// $like_arr[$k]['rolename'] = $role_info;
		// $like_arr[$k]['level'] = $v['level'];
		// $address_info = $base -> get_address_info($v['info']['province_id'],$v['info']['city_id'],$v['info']['district_id'],$flash);
		// $like_arr[$k]['address'] = $address_info['address'];
	// }	
	//var_dump($like_arr);
	//获取角色
	$role_list = $userprofile -> get_role_list();
	//var_dump($role_list);
	//机构列表
	$org_type_list = $base -> get_org_type_list($flash);
	//var_dump($org_type_list);
	//招募分类
	$recruit_list =	$recruit -> get_recruit_type_list();
	$smarty -> assign('recruit_list',$recruit_list);
	//var_dump($recruit_list);		
	$smarty -> assign('org_type_list',$org_type_list);		
	
	$smarty -> assign('role_list',$role_list);		
	//*/about/index.php?type=(business|joinus|contract|copyright|sitemap|helpcenter|agreement)
	$smarty -> display("about/".$temp_type.".html");
?>
