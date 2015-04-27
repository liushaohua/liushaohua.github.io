<?php
	$PAGE_TYPE = "org";
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
	$userid = $user_info['id'];
	$usertype = $user_info['user_type'];
	//var_dump($user_info);
	$smarty -> assign('info',$user_info);
	//获取用户详情
	$orgprofile_info =	$orgprofile -> get_org_profile($userid,$flash);
	//获取user表详情
	$smarty -> assign('user_info',$user_info);		#user用户列表
	//var_dump($orgprofile_info);
	//获取企业成立时间年份
	for($i = $COMMON_CONFIG["CREATE_YEAR"]["RANGE"]['min'];$i <=date('Y');$i++){
		$create_year[] = $i;
	}
	$smarty -> assign('create_year',$create_year);		
	//取得省市区的值显示
	@$province_card = $base -> get_province_info($orgprofile_info['province_id'],$flash);	
	$orgprofile_info['province_c'] = $province_card['pname'];
	@$city_card = $base -> get_city_info($orgprofile_info['city_id'],$flash);	
	$orgprofile_info['city_c'] = $city_card['cname'];
	@$district_card = $base -> get_district_info($orgprofile_info['district_id'],$flash);
	$orgprofile_info['district_c'] = $district_card['dname'];		

	//读取省份
	$plist = $base -> get_province_list($flash);
	//读取机构类型
	$tlist = $base -> get_org_type_list($flash);
	$smarty -> assign('provincelist',$plist);
	$smarty -> assign('tlist',$tlist);
	@$type_c = $base -> get_org_type_info($orgprofile_info['type'],$flash);
	//var_dump($type_c);
	$smarty -> assign('type_c',$type_c);
	$state_list = $COMMON_CONFIG["STATE"];
	$smarty -> assign('state_list',$state_list);
	$smarty -> assign('orgprofile_info',$orgprofile_info);
	//var_dump($orgprofile_info);	
	$smarty -> assign("edit_part","info");
	//为了兼容ie php中记录上次的地址 分配过去
	if(!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER']) ){
		$previous_page = "http://www.hotyq.com";
	}else{
		$previous_page = $_SERVER['HTTP_REFERER'];
	}
	$smarty -> assign('previous_page',$previous_page);		
	$smarty -> display("home/org_profile_info.html");
?>