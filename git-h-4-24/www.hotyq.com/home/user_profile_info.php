<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	$PAGE_TYPE = "user";
	require_once ('../includes/common_home_inc.php');
	require_once ('../../common/userprofile.class.php');
	require_once ('../../common/album.class.php');
	$user = new user();
	$base = new base();
	$album = new album();
	$userprofile = new userprofile();
	$userid = $user_info['id'];
	$usertype = $user_info['user_type'];
	$smarty -> assign('user_info',$user_info);
	//获取用户详情
	$userprofile_info =	$userprofile ->get_user_profile($userid,$flash);
	//年龄的
	$AGE_RANGE = $COMMON_CONFIG["AGE"]["RANGE"];
	$AGE_OPTION = $COMMON_CONFIG["AGE"]["OPTION"];
	for($i = $AGE_RANGE['begin'];$i <= $AGE_RANGE['end'];$i++){
		$agenum[] = $i;
	}
	$smarty -> assign('agenum',$agenum);
	$smarty -> assign('AGE_RANGE',$AGE_RANGE);
	$smarty -> assign('AGE_OPTION',$AGE_OPTION);
	//胸围的
	$BUST_RANGE = $COMMON_CONFIG["BUST"]["RANGE"];
	$BUST_OPTION = $COMMON_CONFIG["BUST"]["OPTION"];
	for($i = $BUST_RANGE['begin'];$i <=$BUST_RANGE['end'];$i++){
		$bustnum[] = $i;
	}
	$smarty -> assign('bustnum',$bustnum);
	$smarty -> assign('BUST_RANGE',$BUST_RANGE);
	$smarty -> assign('BUST_OPTION',$BUST_OPTION);
	//腰围
	$WAIST_RANGE = $COMMON_CONFIG["WAIST"]["RANGE"];
	$WAIST_OPTION = $COMMON_CONFIG["WAIST"]["OPTION"];
	for($i = $WAIST_RANGE['begin'];$i <= $WAIST_RANGE['end'];$i++){
		$waistnum[] = $i;
	}
	$smarty -> assign('waistnum',$waistnum);
	$smarty -> assign('WAIST_RANGE',$WAIST_RANGE);
	$smarty -> assign('WAIST_OPTION',$WAIST_OPTION);
	//臀围
	$HIPS_RANGE = $COMMON_CONFIG["HIPS"]["RANGE"];
	$HIPS_OPTION = $COMMON_CONFIG["HIPS"]["OPTION"];
	for($i = $HIPS_RANGE['begin'];$i <= $HIPS_RANGE['end'];$i++){
		$hipsnum[] = $i;
	}
	$smarty -> assign('hipsnum',$hipsnum);
	$smarty -> assign('HIPS_RANGE',$HIPS_RANGE);
	$smarty -> assign('HIPS_OPTION',$HIPS_OPTION);
	//身高
	$HEIGHT_RANGE = $COMMON_CONFIG["HEIGHT"]["RANGE"];
	$HEIGHT_OPTION = $COMMON_CONFIG["HEIGHT"]["OPTION"];
	for($i = $HEIGHT_RANGE['begin'];$i <= $HEIGHT_RANGE['end'];$i++){
		$heightnum[] = $i;
	}	
	$smarty -> assign('heightnum',$heightnum);
	$smarty -> assign('HEIGHT_RANGE',$HEIGHT_RANGE);
	$smarty -> assign('HEIGHT_OPTION',$HEIGHT_OPTION);
	//毕业年份
	$FINISH_YEAR = $COMMON_CONFIG["FINISH_YEAR"];
	for($i = date('Y');$i >= $FINISH_YEAR['min'] ;$i--){
		$yearnum[] = $i;
	}
	$smarty -> assign('yearnum',$yearnum);	
	$smarty -> assign('FINISH_YEAR',$FINISH_YEAR);	
	//体重
	$WEIGHT_RANGE = $COMMON_CONFIG["WEIGHT"]["RANGE"];
	$WEIGHT_OPTION = $COMMON_CONFIG["WEIGHT"]["OPTION"];
	for($i = $WEIGHT_RANGE['begin'];$i <= $WEIGHT_RANGE['end'];$i++){
		$weightnum[] = $i;
	}			
	$smarty -> assign('weightnum',$weightnum);							
	$smarty -> assign('WEIGHT_RANGE',$WEIGHT_RANGE);
	$smarty -> assign('WEIGHT_OPTION',$WEIGHT_OPTION);
	//用户状态
	$STATE = $COMMON_CONFIG["STATE"];
	$smarty -> assign('STATE',$STATE);			
	//星座
	$star_list = $COMMON_CONFIG["STAR"];
	$smarty -> assign('star_list',$star_list);	
	//血型
	$blood_list = $COMMON_CONFIG["BLOOD"];
	$smarty -> assign('blood_list',$blood_list);	
	//学历
	$degree_list = $COMMON_CONFIG["DEGREE"];			
	$smarty -> assign('degree_list',$degree_list);	

	//var_dump($user_info);


	//取得省市区的值显示
	$province_card = $base -> get_province_info($userprofile_info['province_id'],$flash);	
	$userprofile_info['province_c']	= $province_card['pname'];
	@$city_card = $base -> get_city_info($userprofile_info['city_id'],$flash);		
	$userprofile_info['city_c']	=  $city_card['cname'];
	@$district_card = $base -> get_district_info($userprofile_info['district_id'],$flash);
	$userprofile_info['district_c']	= $district_card['dname'];
	@$province_file = $base -> get_province_info($userprofile_info['native_province_id'],$flash);	
	$userprofile_info['province_p'] = $province_file['pname'];
	@$city_file = $base -> get_city_info($userprofile_info['native_city_id'],$flash);	
	$userprofile_info['city_p']	= $city_file['cname'];
	@$district_file = $base -> get_district_info($userprofile_info['native_district_id'],$flash);
	$userprofile_info['district_p']	= $district_file['dname'];		

	//用户所有信息信息传到前台
	$smarty -> assign('userprofile_info',$userprofile_info);	#userprofile列表
	$smarty -> assign('user_info',$user_info);		#user用户列表
	//读取省份
	$plist = $base -> get_province_list($flash);
	//var_dump($userprofile_info);
	$smarty -> assign('provincelist',$plist);	#省的列表
	$smarty -> assign("edit_part","info");

	//为了兼容ie php中记录上次的地址 分配过去
	if(!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER']) ){
		$previous_page = "http://www.hotyq.com";
	}else{
		$previous_page = $_SERVER['HTTP_REFERER'];
	}
	$smarty -> assign('previous_page',$previous_page);
	$smarty -> display("home/user_profile_info.html");
?>