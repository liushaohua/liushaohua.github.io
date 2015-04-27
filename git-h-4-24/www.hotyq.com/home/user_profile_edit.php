<?php
// 红名片页面
	$PAGE_TYPE = "user";
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');
	require_once ('../../common/userprofile.class.php');
	require_once ('../../common/album.class.php');
	$user = new user();
	$base = new base();
	$album = new album();
	$userprofile = new userprofile();
	//var_dump($user_info);
	//获取cookie中的id 和 type
	$userid = $user_info['id'];
	$usertype = $user_info['user_type'];
//	var_dump($info);
	$smarty -> assign('$user_info',$user_info);		
	//读取角色	
   	$rolelist = $userprofile -> get_role_list_by_parentid();
	//获取用户详情
	$userlist =	$userprofile ->get_user_profile($userid);
	//年龄的
	$AGE_RANGE = $COMMON_CONFIG["AGE"]["RANGE"];
	for($i = $AGE_RANGE['begin'];$i <= $AGE_RANGE['end'];$i++){
		$agenum[] = $i;
	}
	$smarty -> assign('agenum',$agenum);
	$smarty -> assign('AGE_RANGE',$AGE_RANGE);
	//胸围的
	$BUST_RANGE = $COMMON_CONFIG["BUST"]["RANGE"];
	for($i = $BUST_RANGE['begin'];$i <=$BUST_RANGE['end'];$i++){
		$bustnum[] = $i;
	}
	$smarty -> assign('bustnum',$bustnum);
	$smarty -> assign('BUST_RANGE',$BUST_RANGE);
	//腰围
	$WAIST_RANGE = $COMMON_CONFIG["WAIST"]["RANGE"];
	for($i = $WAIST_RANGE['begin'];$i <= $WAIST_RANGE['end'];$i++){
		$waistnum[] = $i;
	}
	$smarty -> assign('waistnum',$waistnum);
	$smarty -> assign('WAIST_RANGE',$WAIST_RANGE);
	//臀围
	$HIPS_RANGE = $COMMON_CONFIG["HIPS"]["RANGE"];
	for($i = $HIPS_RANGE['begin'];$i <= $HIPS_RANGE['end'];$i++){
		$hipsnum[] = $i;
	}
	$smarty -> assign('hipsnum',$hipsnum);
	$smarty -> assign('HIPS_RANGE',$HIPS_RANGE);
	//身高
	$HEIGHT_RANGE = $COMMON_CONFIG["HEIGHT"]["RANGE"];
	for($i = $HEIGHT_RANGE['begin'];$i <= $HEIGHT_RANGE['end'];$i++){
		$heightnum[] = $i;
	}	
	$smarty -> assign('heightnum',$heightnum);
	$smarty -> assign('HEIGHT_RANGE',$HEIGHT_RANGE);
	//毕业年份
	$FINISH_YEAR = $COMMON_CONFIG["FINISH_YEAR"];
	for($i = date('Y');$i >= $FINISH_YEAR['min'] ;$i--){
		$yearnum[] = $i;
	}
	$smarty -> assign('yearnum',$yearnum);	
	$smarty -> assign('FINISH_YEAR',$FINISH_YEAR);	
	//体重
	$WEIGHT_RANGE = $COMMON_CONFIG["WEIGHT"]["RANGE"];
	for($i = $WEIGHT_RANGE['begin'];$i <= $WEIGHT_RANGE['end'];$i++){
		$weightnum[] = $i;
	}			
	$smarty -> assign('weightnum',$weightnum);							
	$smarty -> assign('WEIGHT_RANGE',$WEIGHT_RANGE);
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
	//获取user表详情
	#获取nickname 用
	//年龄的数值
	//var_dump($user_info);
	//根据id获取角色的名字
	$role_c = $userprofile -> get_role_info($userlist['role']);
	//var_dump($role_c) ;
	$smarty -> assign('role_c',$role_c);

	//取得省市区的值显示
	$province_card = $base -> get_province_info($userlist['province_id'],$flash);	
	$province_c = $province_card['pname'];
	$city_card = $base -> get_city_info($userlist['city_id'],$flash);	
	$city_c = $city_card['cname'];	
	$district_card = $base -> get_district_info($userlist['district_id'],$flash);
	$district_c = $district_card['dname'];	

	$province_file = $base -> get_province_info($userlist['native_province_id'],$flash);	
	$province_p = $province_file['pname'];
	$city_file = $base -> get_city_info($userlist['native_city_id'],$flash);	
	$city_p = $city_file['cname'];	
	$district_file = $base -> get_district_info($userlist['native_district_id'],$flash);
	$district_p = $district_file['dname'];	
	
	$smarty -> assign('province_c',$province_c);
	$smarty -> assign('city_c',$city_c);
	$smarty -> assign('district_c',$district_c);
	$smarty -> assign('province_p',$province_p);
	$smarty -> assign('city_p',$city_p);
	$smarty -> assign('district_p',$district_p);

	//用户所有信息信息传到前台
	$smarty -> assign('userlist',$userlist);	#userprofile列表
	$smarty -> assign('user_info',$user_info);		#user用户列表
	//读取省份
	$plist = $base -> get_province_list($flash);
   	
	$smarty -> assign('rolelist',$rolelist);	#角色列表
	$smarty -> assign('provincelist',$plist);	#省的列表
	//  分配一个标识变量  赋值到隐藏域 js取隐藏显示div
	if((empty($userlist['age']) || $userlist['age'] =='0') && (empty($userlist['height']) || $userlist['height'] =='0') && (empty($userlist['weight']) || $userlist['weight'] =='0')  && (empty($userlist['bust']) || $userlist['bust'] =='0')  && (empty($userlist['waist'])||$userlist['waist']=='0') && (empty($userlist['hips'])||$userlist['hips']=='0')&& empty($userlist['star']) && empty($userlist['blood']) && (empty($userlist['native_province_id'])||$userlist['native_province_id']=='0' )&& (empty($userlist['native_city_id'])||$userlist['native_city_id']=='0' )&& (empty($userlist['native_district_id'])||$userlist['native_district_id']=='0')&& empty($userlist['school']) && empty($userlist['specialty']) && (empty($userlist['finish_year']) ||$userlist['finish_year']=='0')&& empty($userlist['degree']) && empty($userlist['in_org']) ){
		$edit_state = 0;
	}else{
		$edit_state = 1;
	}
	$smarty -> assign('edit_state',$edit_state);#置为编辑模式 



	//红标签操作  SUNTIANXING
	


	//红照片
	$result = $album -> get_photo_list_by_user($userid);
	
	if($result){
		$album_list = "[";
		$num = count($result);
		for($i = 0; $i < 6;$i++) {
			if($i < $num){
				$album_list .= "{id:\"".$result[$i]['id']."\",thumbnail:\"".$result[$i]['server_url'].$result[$i]['path_url']."!150.100\",photo:\"".$result[$i]['server_url'].$result[$i]['path_url']."!800\"},";
			}elseif($i >= $num){
				$album_list .= "null,";
			}
		}
		$album_list = rtrim($album_list,',');
		$album_list .= "]";
	}else{
		$album_list = "[null,null,null,null,null,null]";
	}
	//echo $album_list;
	$smarty -> assign('album_list',$album_list);
	//--------------------------------------wangyifan  start----------
	//读取该用户已存入的所有角色
	$role_list['roles'] = $userprofile -> get_role_list();#角色字典
	$all_role = $userprofile -> get_role_list_by_user($userid);#一级 二级 自定义  自定义要分开
	$role_name = array();#浏览模式--
	//1 自定义
	$self_role = array();
	foreach($all_role as $k => $v){
		if($v['parent_id'] == '-1'){
			$self_role[] = $v;
			unset($all_role[$k]);
		}
	}
	if( empty($self_role) ){
		$role_list['userCustomRoles'][] = null;#没有自定义 
	}else{
		$role_list['userCustomRoles'][] = $self_role[0]['name'];#自定义  只name
		$role_name[] = $self_role[0]['name'];#浏览模式--
	}
	//2 系统
	$sys_role = $all_role;
	if( empty($sys_role) ){
		$role_list['userRoles'] = null;#没有系统角色
	}else{
		foreach($sys_role as $v){
			$role_list['userRoles'][] = $v['id'];#用户所选系统  只id
			$role_name[] = $v['name'];#浏览模式--
		}
	}
	// 3 组成需要
	$init_data = json_encode($role_list);
	$json_role_name = json_encode($role_name);
	$smarty -> assign('init_data',$init_data);#初始化json
	$smarty -> assign('role_name',$role_name);#浏览模式
	$smarty -> assign('json_role_name',$json_role_name);#json  name
	//--------------------------------------wangyifan   end-------------------------
	$smarty -> display("home/user_profile_edit.html");
?>