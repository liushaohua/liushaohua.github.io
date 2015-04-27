<?php
header("Content-type:text/html;charset=utf-8"); 

/*
	//获取机构用户编辑页的资料
	function profile_get_org_profile(){
		$user = new user();
		$base = new base();
		$userprofile = new userprofile();
		$orgprofile = new orgprofile();
		$service = new service();
		$album = new album();
		$recruit = new recruit();
		$service = new service();
		$uid = intval($_POST['uid']);
		$app_token = clear_gpq($_POST['app_token']);
		if($uid<1) return get_state_info(1099);
	 	$userinfo =	$user -> get_userinfo($uid,$flash);	
		$orgprofile_info =	$orgprofile -> get_org_profile($uid,$flash);
		_check_login($uid,$app_token);	
		//var_dump($userinfo);
		if(!$userinfo || !$orgprofile_info) return get_state_info(1099);			
		if($userinfo['user_type'] == 'user'){ 
			return get_state_info(1099);
		}else{
			$ret_array['user_type'] = '1';
		}
		$ret_array['nickname'] = $userinfo['nickname'];		
		$ret_array['icon_img'] = $userinfo['icon_server_url'].$userinfo['icon_path_url'];					
		$ret_array['has_verify_mobile'] = $userinfo['mobile_status'];		//是否验证手机

		
		if($orgprofile_info['legal_person'] == 'yes'){		//是否是法人
			$ret_array['is_legal'] = '1';
			if($userinfo['business_card_status'] == 'yes'){
				$ret_array['has_verify_business'] = '1';
			}else{
				$ret_array['has_verify_business'] = '0';
			}			
			$ret_array['has_authentication'] = '0';	
		}else{
			$ret_array['is_legal'] = '0';
			if($userinfo['identity_card_status'] == 'yes'){
				$ret_array['has_authentication'] = '1';		//是否验证身份证
			}else{
				$ret_array['has_authentication'] = '0';
			}
			$ret_array['has_verify_business'] = '0';
		}
		
		//$ret_array['data']['has_authentication'] = $userinfo['business_card_status'];		//是否验证工商号
		$org_type_name = $base -> get_org_type_info($orgprofile_info['type'], $flash);	//机构类型
		$ret_array['org_type'] = $org_type_name['name'];
		$ret_array['instituted'] = $orgprofile_info['create_time'];	//创建时间
		
		$ret_array['introduction'] = $orgprofile_info['introduce'];	//简介		
		$ret_array['showreel'] = $orgprofile_info['production']; // 主要作品
		$ret_array['honor'] = $orgprofile_info['honor'];	//主要荣誉
		$ret_array['phone'] = $orgprofile_info['contact_mobile'];
		$ret_array['qq'] = $orgprofile_info['contact_qq'];
		$ret_array['weixin'] = $orgprofile_info['contact_weixin'];
		$ret_array['email'] = $orgprofile_info['contact_email'];
		$province_info = $base -> get_province_info($orgprofile_info['province_id'],$flash);
		$city_info = $base -> get_city_info($orgprofile_info['city_id'],$flash);
		$district_info = $base -> get_district_info($orgprofile_info['district_id'],$flash);
		$ret_array['province'] = $province_info['pname'];
		$ret_array['city'] = $city_info['cname'];
		$ret_array['district'] = $district_info['dname'];
		//$ret_array['data']['photos'] = 
		//$ret_array['data']['service'] = 
		//获取用户相册

		$album_list = $album -> get_photo_list_by_user($uid);
		if(is_array($album_list)){
			foreach ($album_list as $key => $value) {
				$photo_info['photo'][$key]['id'] = $value['id'];
				$photo_info['photo'][$key]['url'] = $value['server_url'].$value['path_url'];
				$photo_info['photo'][$key]['description'] = $value['title'];
			}
		}else{
			$photo_info['photo'] = array();
		}			
		$ret_array['photo'] = $photo_info['photo'];
		//获取所有服务
		 $service_list = $base -> get_service_list($flash);
		 $service_id_list = $service -> get_e_service_by_user($uid);
		//var_dump($service_list);exit;
		//var_dump($service_id_list);exit;
		if(empty($service_id_list)){
			$service_arr = '';
		}else{
			// foreach($service_id_list as $k => $v){
				// $service_id_list[$k]['name'] = $service_list[$v['service_3_id']];
			// }
			foreach($service_id_list as $k => $v){
				$arr[$v['service_2_id']]['id']  = $v['service_2_id'];
				$arr[$v['service_2_id']]['name']  = $service_list[$v['service_2_id']];
				$arr[$v['service_2_id']]['children'][]  = $v;
				
			 }
			foreach($arr as $k => $v){
				foreach($v['children'] as $k0 => $v0){
					unset($arr[$k]['children'][$k0]['id']);
					unset($arr[$k]['children'][$k0]['service_1_id']);
					unset($arr[$k]['children'][$k0]['service_2_id']);
					unset($arr[$k]['children'][$k0]['uid']);
					$arr[$k]['children'][$k0]['id'] = $v0['service_3_id'];
					$arr[$k]['children'][$k0]['name'] = $service_list[$v0['service_3_id']];
					unset($arr[$k]['children'][$k0]['service_3_id']);
				}
			}
			foreach($arr as $k => $v){
				$arr_new[] = $v;
			}	
		}
		//var_dump($service_id_list);exit;
		//var_dump($arr2);
		$ret_arr = get_state_info(1000);		
		$ret_array['service'] = $arr_new;
		$ret_arr['data'] = $ret_array;
		return $ret_arr;
	}	
	//获取机构用户展示页的资料
	function profile_get_specify_org_profile(){
		$user = new user();
		$base = new base();
		$userprofile = new userprofile();
		$orgprofile = new orgprofile();
		$service = new service();
		$album = new album();
		$recruit = new recruit();
		$service = new service();
		$collect = new collect();
		
		$uid = intval($_POST['uid']);
		$suid = intval($_POST['suid']);
		$app_token = clear_gpq($_POST['app_token']);
		if($uid<1) return get_state_info(1099);
	 	$userinfo =	$user -> get_userinfo($uid,$flash);	
		$orgprofile_info =	$orgprofile -> get_org_profile($uid,$flash);
		//_check_login($uid,$app_token);	
	
		if(!$userinfo || !$orgprofile_info) return get_state_info(1099);			
		if($userinfo['user_type'] == 'user'){ 
			return get_state_info(1099);
		}else{
			$ret_array['user_type'] = '1';
		}
		$ret_array['nickname'] = $userinfo['nickname'];			
		$ret_array['icon_img'] = $userinfo['icon_server_url'].$userinfo['icon_path_url'];			
		$ret_array['has_verify_mobile'] = $userinfo['mobile_status'];		//是否验证手机
		//判断收藏	
		$collect_list = $collect -> get_collect_list_by_user($suid,$is_show);	#判断是否收藏
		if($collect_list){	
			foreach ($collect_list as $v){
				if($v['type'] == 'org'){
					$arr_collect[] = $v['dynamic_id'];		//被收藏的id		
				}
			}		
			if (in_array($uid, $arr_collect)) {
				$ret_array['has_favorite'] = '1'; 
			}else{
				$ret_array['has_favorite'] = '0'; 
			}
		}else{
			$ret_array['has_favorite'] = '0'; 
		}
		
		if($orgprofile_info['legal_person'] == 'yes'){		//是否是法人
			$ret_array['is_legal'] = '1';
			if($userinfo['business_card_status'] == 'yes'){
				$ret_array['has_verify_business'] = '1';
			}else{
				$ret_array['has_verify_business'] = '0';
			}			
			$ret_array['has_authentication'] = '0';	
		}else{
			$ret_array['is_legal'] = '0';
			if($userinfo['identity_card_status'] == 'yes'){
				$ret_array['has_authentication'] = '1';		//是否验证身份证
			}else{
				$ret_array['has_authentication'] = '0';
			}
			$ret_array['has_verify_business'] = '0';
		}
		
		//$ret_array['data']['has_authentication'] = $userinfo['business_card_status'];		//是否验证工商号
		$org_type_name = $base -> get_org_type_info($orgprofile_info['type'], $flash);	//机构类型
		$ret_array['org_type'] = $org_type_name['name'];
		$ret_array['instituted'] = $orgprofile_info['create_time'];	//创建时间
		
		$ret_array['introduction'] = $orgprofile_info['introduce'];	//简介		
		$ret_array['showreel'] = $orgprofile_info['production']; // 主要作品
		$ret_array['honor'] = $orgprofile_info['honor'];	//主要荣誉
		$ret_array['phone'] = $orgprofile_info['contact_mobile'];
		$ret_array['qq'] = $orgprofile_info['contact_qq'];
		$ret_array['weixin'] = $orgprofile_info['contact_weixin'];
		$ret_array['email'] = $orgprofile_info['contact_email'];
		$province_info = $base -> get_province_info($orgprofile_info['province_id'],$flash);
		$city_info = $base -> get_city_info($orgprofile_info['city_id'],$flash);
		$district_info = $base -> get_district_info($orgprofile_info['district_id'],$flash);
		$ret_array['province'] = $province_info['pname'];
		$ret_array['city'] = $city_info['cname'];
		$ret_array['district'] = $district_info['dname'];
		//$ret_array['data']['photos'] = 
		//$ret_array['data']['service'] = 
		//获取用户相册

		$album_list = $album -> get_photo_list_by_user($uid);
		if(is_array($album_list)){
			foreach ($album_list as $key => $value) {
				$photo_info['photo'][$key]['id'] = $value['id'];
				$photo_info['photo'][$key]['url'] = $value['server_url'].$value['path_url'];
				$photo_info['photo'][$key]['description'] = $value['title'];
			}
		}else{
			$photo_info['photo'] = '';
		}			
		$ret_array['photo'] = $photo_info['photo'];
		//获取所有服务
		 $service_list = $base -> get_service_list($flash);
		 $service_id_list = $service -> get_e_service_by_user($uid);
		//var_dump($service_list);exit;
		//var_dump($service_id_list);exit;
		if(empty($service_id_list)){
			$arr_new = '';
		}else{
			// foreach($service_id_list as $k => $v){
				// $service_id_list[$k]['name'] = $service_list[$v['service_3_id']];
			// }
			foreach($service_id_list as $k => $v){
				$arr[$v['service_2_id']]['id']  = $v['service_2_id'];
				$arr[$v['service_2_id']]['name']  = $service_list[$v['service_2_id']];
				$arr[$v['service_2_id']]['children'][]  = $v;
				
			 }
			foreach($arr as $k => $v){
				foreach($v['children'] as $k0 => $v0){
					unset($arr[$k]['children'][$k0]['id']);
					unset($arr[$k]['children'][$k0]['service_1_id']);
					unset($arr[$k]['children'][$k0]['service_2_id']);
					unset($arr[$k]['children'][$k0]['uid']);
					$arr[$k]['children'][$k0]['id'] = $v0['service_3_id'];
					$arr[$k]['children'][$k0]['name'] = $service_list[$v0['service_3_id']];
					unset($arr[$k]['children'][$k0]['service_3_id']);
				}
			}
			foreach($arr as $k => $v){
				$arr_new[] = $v;
			}	
		}

		$ret_arr = get_state_info(1000);		
		$ret_array['service'] = $arr_new;
		$ret_arr['data'] = $ret_array;
		return $ret_arr;
	}		
	
	//发送认证手机验证码
	function home_send_mobile_verify_verification(){
		$user = new user();
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);			
		if(empty($_POST['mobile'])){
			return get_state_info(1016);
		}
		$mobile = intval($_POST['mobile']);
		$state_code = $user -> check_mobile($mobile);
		$lock_time = 60;
		if($state_code == 1000){
			$state_code = $user -> get_reg_mobile_code($mobile);
			if($state_code == 1000 || $state_code == 1515){
				$reg_mobile_code_info = $user -> get_reg_mobile_code_info($mobile);
				$lock_time = 60 - $reg_mobile_code_info['timediff'];
				$ret_arr = get_state_info($state_code);
				$ret_arr['data'] = array("lock_time" => $lock_time);
			}else{
				$ret_arr = get_state_info($state_code);
			}
		}else{
			$ret_arr = get_state_info($state_code);	
		}		
		return $ret_arr;	
	}
	//获取身高和体重的范围
	function get_height_weight_range(){
		global $COMMON_CONFIG;
		$ret_array['height_min'] = $COMMON_CONFIG["HEIGHT"]["RANGE"]['begin'];
		$ret_array['height_min_limit'] = $COMMON_CONFIG["HEIGHT"]["RANGE"]['min'];
		$ret_array['height_max'] = $COMMON_CONFIG["HEIGHT"]["RANGE"]['end'];
		$ret_array['height_max_limit'] = $COMMON_CONFIG["HEIGHT"]["RANGE"]['max'];
		$ret_array['weight_min'] = $COMMON_CONFIG["WEIGHT"]["RANGE"]['begin'];
		$ret_array['weight_min_limit'] = $COMMON_CONFIG["WEIGHT"]["RANGE"]['min'];
		$ret_array['weight_max'] = $COMMON_CONFIG["WEIGHT"]["RANGE"]['end'];
		$ret_array['weight_max_limit'] = $COMMON_CONFIG["WEIGHT"]["RANGE"]['max'];
		$ret_arr = get_state_info(1000);		
		$ret_arr['data'] = $ret_array;	
		return $ret_arr;		
	}
	//修改机构简介
	function home_set_org_introduction(){
		$orgprofile = new orgprofile;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$introduction = clear_gpq($_POST['introduction']);
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);
		$org_profile_array['introduce'] = $introduction;
		if(strlen($org_profile_array['introduce'])>600) return get_state_info(1149);
		$re = $orgprofile -> update_org_profile($uid,$org_profile_array);		
		if($re == 1000){
			//_update_percent_level_cookie($uid);
			return get_state_info(1000);	
		}else{
			return get_state_info(1112); 		//资料修改失败
		}  			
	}
	//修改学校和专业
	function home_set_school(){
		$userprofile = new userprofile;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$school = clear_gpq($_POST['school']);
		$major = clear_gpq($_POST['major']);
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);
		$user_profile_array['school'] = $school;
		$user_profile_array['specialty'] = $major;
		if(strlen($user_profile_array['school'])>90) return get_state_info(1165);
		if(strlen($user_profile_array['specialty'])>90) return get_state_info(1166);
		$re = $userprofile -> update_user_profile($uid,$user_profile_array);		
		if($re == 1000){
			//_update_percent_level_cookie($uid);
			return get_state_info(1000);	
		}else{
			return get_state_info(1112); 		//资料修改失败
		}  				
		
	}
	//验证认证手机验证码
	function home_verify_code_verification(){
		$user = new user();
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$mobile = clear_gpq($_POST['mobile']);
		$verify_code = clear_gpq($_POST['verify_code']);
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);		
		if(empty($mobile)){
			return get_state_info(1018);
		}
		if(empty($verify_code) || strlen($verify_code)!== 6){
			return get_state_info(1007);
		}
		$state_code = $user -> check_mobile($mobile);
		if($state_code !== 1000){
			return get_state_info($state_code);
		}
		$state_code = $user -> email_user_certify_mobile($uid,$mobile,$verify_code);
		return get_state_info($state_code);
	}
	//修改机构的荣誉
	function home_set_org_honor(){
		$orgprofile = new orgprofile;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$honor = clear_gpq($_POST['honor']);
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);
		$org_profile_array['honor'] = $honor;
		if(strlen($org_profile_array['honor'])>600) return get_state_info(1151);
		$re = $orgprofile -> update_org_profile($uid,$org_profile_array);		
		if($re == 1000){
			//_update_percent_level_cookie($uid);
			return get_state_info(1000);	
		}else{
			return get_state_info(1112); 		//资料修改失败
		}		
	}
	//修改机构的作品
	function home_set_org_showreel(){
		$orgprofile = new orgprofile;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$showreel = clear_gpq($_POST['showreel']);
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);
		$org_profile_array['production'] = $showreel;
		if(strlen($org_profile_array['production'])>600) return get_state_info(1150);
		$re = $orgprofile -> update_org_profile($uid,$org_profile_array);		
		if($re == 1000){
			//_update_percent_level_cookie($uid);
			return get_state_info(1000);	
		}else{
			return get_state_info(1112); 		//资料修改失败
		}		
	}	
	//修改籍贯
	function home_set_birthplace(){
		$userprofile = new userprofile;
		$uid = intval($_POST['uid']);	
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		$province = intval($_POST['province']);
		$city = intval($_POST['city']);
		$district = intval($_POST['district']);
		
		_check_login($uid,$app_token);		
		$uinfo['native_province_id'] = $province;
		$uinfo['native_city_id'] = $city;
		$uinfo['native_district_id'] = $district;		
		$re = $userprofile -> update_user_profile($uid,$uinfo);
		if($re){
			//_update_percent_level_cookie($uid);
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}  		
	}
	//修改昵称
	function home_set_nickname(){	
		$user = new user();
		$rongyun = new rongyun();
		$uid = intval($_POST['uid']);	
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);		
		$nickname = clear_gpq($_POST['nickname']);
		_check_login($uid,$app_token);			
		if(empty($nickname) || preg_match("/[\&\<\>\'\"\\\?\=\$\%\^\*\@\/\#]/",$nickname)||strlen($nickname)>90){return 1101;}	#昵称填写错误
		$result = $user -> update_user_nickname($uid,$nickname);
		if($result == 1000){
			//_update_percent_level_cookie($uid);			
			$user_info = $user -> get_userinfo($uid, $flash = 1);
			$rongyun -> get_user_token($uid, $nickname, $user_info['icon_server_url'].$user_info['icon_path_url']);
			return get_state_info(1000);
		}else{
			return get_state_info(1112); 
		}		
	}
	//修改年龄和星座
	function home_set_constellation_age(){
		$userprofile = new userprofile;
		$uid = intval($_POST['uid']);	
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		$age = intval($_POST['age']);
		$constellation = clear_gpq($_POST['constellation']);
		_check_login($uid,$app_token);		
		$uinfo['age'] = $age;
		$uinfo['star'] = $constellation;
		$star_arr = array('','白羊座','金牛座','双子座','巨蟹座','狮子座','处女座','天秤座','天蝎座','射手座','魔羯座','水瓶座','双鱼座');

		if(!in_array($uinfo['star'],$star_arr)) return get_state_info(1191);		
		$re = $userprofile -> update_user_profile($uid,$uinfo);
		if($re){
			//_update_percent_level_cookie($uid);
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}		
	}
	//修改三维
	function home_set_bwh(){
		$userprofile = new userprofile;
		$uid = intval($_POST['uid']);	
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		$breast = intval($_POST['breast']);
		$waistline = intval($_POST['waistline']);
		$hipline = intval($_POST['hipline']);
		_check_login($uid,$app_token);		
		$uinfo['bust'] = $breast;
		$uinfo['waist'] = $waistline;
		$uinfo['hips'] = $hipline;
		$re = $userprofile -> update_user_profile($uid,$uinfo);
		if($re){
			//_update_percent_level_cookie($uid);
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}				
	}
	//修改身高和体重
	function home_set_height_weight(){
		$userprofile = new userprofile;
		$uid = intval($_POST['uid']);	
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		$height = intval($_POST['height']);
		$weight = intval($_POST['weight']);
		_check_login($uid,$app_token);		
		$uinfo['height'] = $height;
		$uinfo['weight'] = $weight;
		$re = $userprofile -> update_user_profile($uid,$uinfo);
		if($re){
			//_update_percent_level_cookie($uid);
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}			 
	}
	//修改所在地
	function home_set_location(){
		$userprofile = new userprofile;
		$uid = intval($_POST['uid']);	
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		$province = intval($_POST['province']);
		$city = intval($_POST['city']);
		$district = intval($_POST['district']);
		
		_check_login($uid,$app_token);		
		$user_profile_array['province_id'] = $province;
		$user_profile_array['city_id'] = $city;
		$user_profile_array['district_id'] = $district;	
		if(!isset($user_profile_array['province_id'])||$user_profile_array['province_id'] ==0|| empty($user_profile_array['province_id']))return get_state_info(1103);		#省没填写
		if(!isset($user_profile_array['city_id']) || $user_profile_array['city_id'] ==0||empty($user_profile_array['city_id']))return get_state_info(1104);	#市没填写
		// if(empty($user_profile_array['district_id'])){return get_state_info(1105);}		//区没填写		
		$re = $userprofile -> update_user_profile($uid,$user_profile_array);	
		if($re){
			//_update_percent_level_cookie($uid);
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}  				
	}
iid true String 指定邀约的id
comment true	
	
	
*//*
//设置某二级服务下的邀约用户的备注
function mywork_set_invitation_comment(){
	$invite = new invite;
	$uid = intval($_POST['uid']);
	$app_token = clear_gpq($_POST['app_token']);	
	$iid = intval($_POST['iid']);	#邀约的id
	$des = clear_gpq($_POST['comment']);	#备注的信息
	_check_login($uid,$app_token);			
	$re = $invite -> update_invite_description($iid, $des);
	if($re){
		echo 1000;
	}else{
		echo 1380;
	}	
}
//设置某二级服务下的邀约用户的沟通结果
function mywork_set_invitation_communication(){
	$invite = new invite;
	$uid = intval($_POST['uid']);	
	$app_token = clear_gpq($_POST['app_token']);	
	$iid = intval($_POST['iid']);	#邀约的id
	$communication = intval($_POST['communication']);	#沟通结果信息
	if($communication == '1'){
		$result ='sure';
	}else if($communication == '2'){
		$result = 'hold';
	}else if($communication == '3'){
		$result = 'refuse';
	}
	_check_login($uid,$app_token);			
	$re = $invite -> update_invite_result($iid, $result);
	if($re){
		echo 1000;
	}else{
		echo 1380;
	}	
}	
	

	
	
//设置某二级服务下的报名用户的备注
function mywork_set_apply_comment(){
	$apply = new apply;
	$uid = intval($_POST['uid']);	
	$app_token = clear_gpq($_POST['app_token']);	
	$iid = intval($_POST['iid']);	#报名的id
	$des = clear_gpq($_POST['comment']);	#备注的信息
	_check_login($uid,$app_token);			
	$re = $apply -> update_apply_description($iid, $des);
	if($re){
		echo 1000;
	}else{
		echo 1380;
	}	
}
//设置某二级服务下的报名用户的沟通结果
function mywork_set_apply_communication(){
	$apply = new apply;
	$uid = intval($_POST['uid']);	
	$app_token = clear_gpq($_POST['app_token']);	
	$iid = intval($_POST['iid']);	#报名的id
	$communication = intval($_POST['communication']);	#沟通结果信息
	_check_login($uid,$app_token);	
	if($communication == '1'){
		$result ='sure';
	}else if($communication == '2'){
		$result = 'hold';
	}else if($communication == '3'){
		$result = 'refuse';
	}	
	$re = $apply -> update_apply_result($iid, $result);
	if($re){
		echo 1000;
	}else{
		echo 1380;
	}	
}		
	
*/
/*
		//获取招募里，某个二级服务下，已经邀约的红人列表
	function mywork_get_invitation_user_list(){
		global $flash,$PAGESIZE;
		$uid = intval($_POST['uid']);
		$rid = intval($_POST['rid']);
		$page = intval($_POST['page']);
		$e_service_id = intval($_POST['e_service_id']);	
		$app_token = clear_gpq($_POST['app_token']);
		$sex = clear_gpq($_POST['sex']);
		$result = clear_gpq($_POST['communication']);
		$pagesize = $PAGESIZE['MYWORK_PAGE'];
		//_check_login($uid,$app_token);
		if($uid<1 || $rid<1 || $e_service_id<1) return get_state_info(1099);
		if($page < 1) $page = 1;		

		$from_rows = ($page - 1) * $pagesize;
		$invite = new invite();
		//$apply_list = $apply -> get_user_apply_list_by_recruit_service($rid, $e_service_id, $userid, $sex, $result, $from_rows, $limit);
		//$sql = "SELECT * FROM `hyq_e_apply` AS apply RIGHT JOIN `hyq_user_profile` AS user ON apply.uid = user.uid WHERE recruit_id = 462 AND e_service_id = 116 AND sex = 'm' AND ";
		$re = $invite -> get_invite_list_by_user($uid, $from_rows = 0, $limit = 0);
	}
*/	
//获取一级服务的列表。一次性返回所有的，不分页

function get_one_service(){
	$service = new service;
	$service -> get_service($flash = 0);
}







	