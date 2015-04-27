<?php
	//$PAGE_TYPE = "org";
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');
	require_once ('../../common/orgprofile.class.php');
	require_once ('../../common/album.class.php');
	$base = new base();
	$orgprofile = new orgprofile();
	$user = new user();
	$album = new album();
	$userprofile = new userprofile();
	//获取cookie中的id 和 type
	$userid = $user_info['id'];
	$usertype = $user_info['user_type'];
	//var_dump($user_info);
	$smarty -> assign('info',$user_info);
	//获取用户详情
	$orglist =	$orgprofile -> get_org_profile($userid);
	

	//  分配一个标识变量 	//判断是否存在红档案的值  赋值到隐藏域 js取隐藏显示div
	if(empty($orglist['introduce']) && empty($orglist['production']) && empty($orglist['honor'])){
		$red_file_state = 0;
	}else{
		$red_file_state = 1;
	}
	$smarty -> assign('red_file_state',$red_file_state);	#判断是否存在红档案的值 
	$orglist['introduce'];
	$orglist['production'];
	$orglist['honor'];		
	$smarty -> assign('orglist',$orglist);	#orgprofile列表
	var_dump($orglist['honor']);
	//获取user表详情
	$smarty -> assign('user_info',$user_info);		#user用户列表
	//var_dump($user_info);
	//var_dump($userall);
	//获取企业成立时间年份
	for($i = 1900;$i <=date('Y');$i++){
		$create_year[] = $i;
	}
	$smarty -> assign('create_year',$create_year);		
	//取得省市区的值显示
	$province_card = $base -> get_province_info($orglist['province_id'],$flash);	
	$province_c = $province_card['pname'];
	$city_card = $base -> get_city_info($orglist['city_id'],$flash);	
	$city_c = $city_card['cname'];	
	$district_card = $base -> get_district_info($orglist['district_id'],$flash);
	$district_c = $district_card['dname'];		
	$smarty -> assign('province_c',$province_c);
	$smarty -> assign('city_c',$city_c);
	$smarty -> assign('district_c',$district_c);	

	//读取省份
	$plist = $base -> get_province_list($flash);
	//读取机构类型
	$tlist = $base -> get_org_type_list($flash);
	$smarty -> assign('provincelist',$plist);
	$smarty -> assign('tlist',$tlist);
	$type_c = $base -> get_org_type_info($orglist['type']);
	//var_dump($type_c);
	$smarty -> assign('type_c',$type_c);
	
	//机构
	$state_list = $COMMON_CONFIG["STATE"];
	$smarty -> assign('state_list',$state_list);

	
	
	




	
	$smarty -> display("home/org_profile_base_edit.html");
?>