<?php
	//----------------zhaozhenhuan--------------start--------------------------
	//发送认证手机验证码
	function home_send_mobile_verify_verification(){
		global $MOBILE_LOCK_TIME;
		$user = new user();
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);			
		if(empty($_POST['mobile']))return get_state_info(1016);
		$mobile = clear_gpq($_POST['mobile']);
		$state_code = $user -> check_mobile($mobile);
		$lock_time = $MOBILE_LOCK_TIME;
		if($state_code == 1000){
			$state_code = $user -> get_reg_mobile_code($mobile);
			if($state_code == 1000 || $state_code == 1515){
				$reg_mobile_code_info = $user -> get_reg_mobile_code_info($mobile);
				$lock_time = $MOBILE_LOCK_TIME - $reg_mobile_code_info['timediff'];
				$ret_arr = get_state_info($state_code);
				$ret_arr['data'] = array("lock_time" => $lock_time);
			}elseif($state_code == 1517){
				$ret_arr = get_state_info(1517);
			}else{
				$ret_arr = get_state_info(1014);	
			}
		}else{
			$ret_arr = get_state_info(1016);	
		}
		return $ret_arr;
	}
	//修改机构简介
	function home_set_org_introduction(){
		global $flash;
		$orgprofile = new orgprofile;
		$user = new user;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$introduction = clear_gpq(strip_tags($_POST['introduction']));
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);
		$orginfo = $orgprofile -> get_org_profile($uid, $flash);
		if(!$orginfo) return get_state_info(1099);
		if(strlen($introduction)>600) return get_state_info(1149);
		$org_profile_array['introduce'] = $introduction;
		$re = $orgprofile -> update_org_profile($uid,$org_profile_array);		
		if($re == 1000){
			$user -> update_data_percent($uid);
			return get_state_info(1000);	
		}else{
			return get_state_info(1112); 		//资料修改失败
		}  			
	}
	//修改学校和专业
	function home_set_school(){
		global $flash;
		$user = new user;
		$userprofile = new userprofile;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$school = clear_gpq($_POST['school']);
		$major = clear_gpq($_POST['major']);
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);
		$userinfo = $userprofile -> get_user_profile($uid, $flash);
		if(!$userinfo) return get_state_info(1099);
		if(strlen($school)>90) return get_state_info(1165);
		if(strlen($major)>90) return get_state_info(1166);
		$user_profile_array['school'] = $school;
		$user_profile_array['specialty'] = $major;		
		$re = $userprofile -> update_user_profile($uid,$user_profile_array);		
		if($re == 1000){
			$user -> update_data_percent($uid);
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
			return get_state_info(1016);
		}
		$state_code = $user -> email_user_certify_mobile($uid,$mobile,$verify_code);
		if($state_code == 1000){
			return get_state_info(1000);
		}else if($state_code == 1519){
			return get_state_info(1519);	#手机验证码错误。
		}else if($state_code == 1518){
			return get_state_info(1518);	#您的手机验证码尚未发送
		}else if($state_code == 1159){
			return get_state_info(1159);   #已经是绑定用户
		}else{
			return get_state_info(1114);	#用户绑定手机失败
		}
	}
	//修改机构的荣誉
	function home_set_org_honor(){
		global $flash;
		$orgprofile = new orgprofile;
		$user = new user;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$honor = clear_gpq(strip_tags($_POST['honor']));
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);
		$orginfo = $orgprofile -> get_org_profile($uid, $flash);
		if(!$orginfo) return get_state_info(1099);		
		if(strlen($honor)>600) return get_state_info(1151);
		$org_profile_array['honor'] = $honor;		
		$re = $orgprofile -> update_org_profile($uid,$org_profile_array);		
		if($re == 1000){
			$user -> update_data_percent($uid);
			return get_state_info(1000);	
		}else{
			return get_state_info(1112); 		//资料修改失败
		}		
	}
	//修改机构的作品
	function home_set_org_showreel(){
		global $flash;
		$orgprofile = new orgprofile;
		$user = new user;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$showreel = clear_gpq(strip_tags($_POST['showreel']));
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);
		$orginfo = $orgprofile -> get_org_profile($uid, $flash);
		if(!$orginfo) return get_state_info(1099);		
		if(strlen($showreel)>600) return get_state_info(1150);
		$org_profile_array['production'] = $showreel;
		$re = $orgprofile -> update_org_profile($uid,$org_profile_array);		
		if($re == 1000){
			$user -> update_data_percent($uid);
			return get_state_info(1000);	
		}else{
			return get_state_info(1112); 		//资料修改失败
		}		
	}	
	//修改籍贯
	function home_set_birthplace(){
		global $flash;
		$userprofile = new userprofile;
		$user = new user;
		$uid = intval($_POST['uid']);	
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		$province = intval($_POST['province']);
		$city = intval($_POST['city']);
		$district = intval($_POST['district']);
		
		_check_login($uid,$app_token);
		$userinfo = $userprofile -> get_user_profile($uid, $flash);
		if(!$userinfo) return get_state_info(1099);			
		$uinfo['native_province_id'] = $province;
		$uinfo['native_city_id'] = $city;
		$uinfo['native_district_id'] = $district;		
		$re = $userprofile -> update_user_profile($uid,$uinfo);
		if($re){
			$user -> update_data_percent($uid);
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}  		
	}
	//修改昵称
	function home_set_nickname(){	
		$user = new user();
		$rongyun = new rongyun();
		$base = new base();
		$uid = intval($_POST['uid']);	
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);		
		$nickname = clear_gpq($_POST['nickname']);
		_check_login($uid,$app_token);			
		//if(empty($nickname) || preg_match("/[\&\<\>\'\"\\\?\=\$\%\^\*\@\/\#]/",$nickname)||strlen($nickname)>90){return 1101;}	#昵称填写错误
		if(empty($nickname) ||strlen($nickname) > 90 || !$base -> is_nickname($nickname)){return get_state_info(1101);}	 
		$result = $user -> update_user_nickname($uid,$nickname);
		if($result == 1000){
			$user -> update_data_percent($uid);		
			$user_info = $user -> get_userinfo($uid, $flash = 1);
			$rongyun -> get_user_token($uid, $nickname, $user_info['icon_server_url'].$user_info['icon_path_url']);
			return get_state_info(1000);
		}else{
			return get_state_info(1112); 
		}		
	}
	//修改年龄和星座
	function home_set_constellation_age(){
		global $COMMON_CONFIG,$flash;
		$userprofile = new userprofile;
		$user = new user;
		$uid = intval($_POST['uid']);	
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		$age = intval($_POST['age']);
		$constellation = clear_gpq($_POST['constellation']);
		_check_login($uid,$app_token);
		$userinfo = $userprofile -> get_user_profile($uid, $flash);
		if(!$userinfo) return get_state_info(1099);			
		if(!empty($constellation)){	
			if(!in_array($constellation,$COMMON_CONFIG["STAR"])) return get_state_info(1191);	
		}	
		$uinfo['age'] = $age;
		$uinfo['star'] = $constellation;		
		$re = $userprofile -> update_user_profile($uid,$uinfo);
		if($re){
			$user -> update_data_percent($uid);
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}		
	}
	//修改三围
	function home_set_bwh(){
		global $flash;
		$userprofile = new userprofile;
		$user = new user;
		$uid = intval($_POST['uid']);	
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		$breast = intval($_POST['breast']);
		$waistline = intval($_POST['waistline']);
		$hipline = intval($_POST['hipline']);
		_check_login($uid,$app_token);		
		$userinfo = $userprofile -> get_user_profile($uid, $flash);
		if(!$userinfo) return get_state_info(1099);		
		$uinfo['bust'] = $breast;
		$uinfo['waist'] = $waistline;
		$uinfo['hips'] = $hipline;
		$re = $userprofile -> update_user_profile($uid,$uinfo);
		if($re){
			$user -> update_data_percent($uid);
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}				
	}
	//修改身高和体重
	function home_set_height_weight(){
		global $flash;
		$userprofile = new userprofile;
		$user = new user;
		$uid = intval($_POST['uid']);	
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		$height = intval($_POST['height']);
		$weight = intval($_POST['weight']);
		_check_login($uid,$app_token);		
		$userinfo = $userprofile -> get_user_profile($uid, $flash);
		if(!$userinfo) return get_state_info(1099);			
		$uinfo['height'] = $height;
		$uinfo['weight'] = $weight;
		$re = $userprofile -> update_user_profile($uid,$uinfo);
		if($re){
			$user -> update_data_percent($uid);
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}			 
	}
	//修改所在地
	function home_set_location(){
		global $flash;
		$userprofile = new userprofile;
		$orgprofile = new orgprofile;
		$user = new user;
		$uid = intval($_POST['uid']);	
		if($uid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		$province = intval($_POST['province']);
		$city = intval($_POST['city']);
		$district = intval($_POST['district']);		
		_check_login($uid,$app_token);		
		$userinfo = $user -> get_userinfo($uid,$flash);
		if(!$userinfo) return get_state_info(1099);
		if($province < 1) return get_state_info(1103);		#省没填写
		if($city < 1) return get_state_info(1104);	#市没填写
		// if(empty($user_profile_array['district_id'])){return get_state_info(1105);}		//区没填写		
		$user_profile_array['province_id'] = $province;
		$user_profile_array['city_id'] = $city;
		$user_profile_array['district_id'] = $district;	
		if($userinfo['user_type'] == 'user'){
			$re = $userprofile -> update_user_profile($uid,$user_profile_array);
		}else{
			$re = $orgprofile -> update_org_profile($uid,$user_profile_array); 
		}	
		if($re){
			$user -> update_data_percent($uid);
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}  				
	}
	
	//手机用户绑定邮箱
	function home_bind_email(){
		global $user,$userprofile;		
		$uid = $_POST['uid'];
		$email = $_POST['email'];
		$token = $_POST['app_token'];
		if($uid<1) return get_state_info(1099);			
		_check_login($uid,$token);
		if(empty($email)){
			return get_state_info(1017);
		}	
		$state_code = $user -> check_email($email);
		if($state_code !== 1000){
			return get_state_info(1009);
		}
		$state_code = $user -> email_exist($email);
		if($state_code == 1000){
			return get_state_info(1010);	
		}				
		$state_code = $user -> mobile_user_certify_email($uid,$email);
		if($state_code == 1000){
			return get_state_info(1000);
		}else{
			return get_state_info(1501);				
		}						
	}
	//再次发送邮件	
	function home_resend_verify_email(){
		global $user;	
		$uid = $_POST['uid'];
		//$email = $_POST['email'];
		$token = $_POST['app_token'];		
		if($uid<1) return get_state_info(1099);			
		_check_login($uid,$token);		
		$state_code = $user -> send_active_email($uid);
		if($state_code == 1000){
			return get_state_info(1000);
		}else{
			return get_state_info(1501);			
		}	
	}	
	//修改密码
	function home_change_password(){
		global $db_hyq_write,$user,$flash;
		$uid = $_POST['uid'];
		$password = $_POST['password'];
		$new_password = $_POST['new_password'];
		$token = $_POST['app_token'];
		if($uid<1) return get_state_info(1099);			
		_check_login($uid,$token);		
		$pwd = $user -> get_user_password($uid);
		if($pwd['password'] !== md5(md5(clear_gpq($password)).$pwd['salt'])){
			return get_state_info(1158);	#密码错误
		}
		if(strlen($new_password) < 6 || strlen($new_password) > 16){
			return get_state_info(1003);
		}		
		
		$userinfo['salt'] = md5(random());
		$userinfo['password'] = md5(md5(clear_gpq($new_password)).$userinfo['salt']);
		//手机验证码+邮箱验证码 重置
		//$userinfo['email_check_code'] = md5(random()); 		
		$result = $user -> update_user_info($uid,$userinfo);
		//var_dump($result);
		if($result){
			$user -> get_userinfo($uid, $flash = 1);
			$user ->delete_cookie_user_info();
			return get_state_info(1000);
		}else{
			return get_state_info(1157); #修改密码失败
		}	
		
	}
	//---------------zhaozhenhuan--------------end--------------------------

	//获取我收到的邀约列表
	function home_get_my_invitation(){
		global $PAGESIZE,$flash;
		$uid = intval($_POST['uid']);
		if($uid < 1) return get_state_info(1099);	
		$app_token = clear_gpq($_POST['app_token']);

		_check_login($uid,$app_token);

		//取服务的缓存数据
		$service = new service();
		$service_array = $service -> get_service($flash);
		foreach ($service_array as $k => $v) {
			$service_arr[$v['id']] = $v['name'];
		}
		$page = intval($_POST['page']);
		if($page < 1){
			$page = 1;
		}
		$pagesize = $PAGESIZE['MYWORK_PAGE'];
		$from_rows = ($page - 1) * $pagesize;
		$invite = new invite();
		$invite_list = $invite -> get_invite_list_by_user($uid, $from_rows, $pagesize);
		if($invite_list){
			$recruit = new recruit();
			$base = new base();
			$i = 0;
			foreach ($invite_list as $key => $value) {
				$rid = $value['recruit_id'];
				$recruit_info = $recruit -> get_recruit_info($rid, $flash);				
				if($recruit_info){
					$invite_recruit_list[$i]['id'] = $rid;
					$invite_recruit_list[$i]['iid'] = $value['id'];
					//$invite_recruit_list[$i]['recruit_id'] = $rid;
					$invite_recruit_list[$i]['title'] = $recruit_info['name'];
					$invite_recruit_list[$i]['status'] = $recruit_info['status'];
					$invite_recruit_list[$i]['deadline'] = strtotime($recruit_info['interview_end_time']);
					$invite_recruit_list[$i]['datetime'] = strtotime($value['invite_date']);
					$city_info = $base -> get_city_info($recruit_info['city_id'], $flash);
					$invite_recruit_list[$i]['city'] = $city_info['cname'];
					$invite_recruit_list[$i]['icon_img'] = $recruit_info['cover_server_url'].$recruit_info['cover_path_url'];

					//获取邀约的二级服务
					//根据招募的服务id 获取招募的二级服务
					$e_service_recruit = $invite -> get_service_recruit($value['e_service_id']);
					$second_service = $service_arr[$e_service_recruit['service_2_id']];
					$invite_recruit_list[$i]['service'] = $second_service;
					$i++;
				}else{
					$invite_recruit_list = array();
				}		
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data'] = $invite_recruit_list;
			return $ret_arr;	

			
		}else{
			//查不到报名信息
			return get_state_info(1303);
		}
	}
	//获取邀约详情
	function home_get_invitation_info(){
		global $flash;
		$uid = intval($_POST['uid']);
		$iid = intval($_POST['iid']);
		if($uid < 1 || $iid < 1) return get_state_info(1099);	
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);

		//取服务的缓存数据
		$service = new service();
		$service_array = $service -> get_service($flash);
		foreach ($service_array as $k => $v) {
			$service_arr[$v['id']] = $v['name'];
		}

		$invite = new invite();
		$user = new user();
		$invite_info = $invite -> get_invite_info($iid);
		if($invite_info){
			$rid = $invite_info['recruit_id'];
			$recruit = new recruit();
			$recruit_info = $recruit -> get_recruit_info($rid, $flash);
			if($recruit_info){
				$r_uid = $recruit_info['uid'];
				$recruiter = $user -> get_userinfo($r_uid, $flash);
				//获取招募方的 验证信息
				if($recruiter['identity_card_status'] == 'yes'){
					$invite_info_arr['recruiter_has_authentication'] = '1'; 
				}else{
					$invite_info_arr['recruiter_has_authentication'] = '0';
				}
				if($recruiter['mobile_status'] == 'yes'){
					$invite_info_arr['recruiter_has_verify_mobile'] = '1';
				}else{
					$invite_info_arr['recruiter_has_verify_mobile'] = '0';
				}
				$invite_info_arr['recruiter_nickname'] = $recruiter['nickname'];
				$invite_info_arr['rid'] = $rid;
				$invite_info_arr['ruid'] = $r_uid;
				$invite_info_arr['title'] = $recruit_info['name'];
				$invite_info_arr['icon_img'] = $recruit_info['cover_server_url'].$recruit_info['cover_path_url'];
				$invite_info_arr['recruiter_mobile'] = $invite_info['r_mobile'];
				$invite_info_arr['recruiter_qq'] = $invite_info['r_qq'];
				$invite_info_arr['recruiter_weixin'] = $invite_info['r_weixin'];
				$invite_info_arr['recruiter_email'] = $invite_info['r_email'];
				//获取邀约的三级服务
				$e_service_id = $invite_info['e_service_id'];
				$result = $invite -> get_service_recruit($e_service_id);
				if($result){
					$service_2_id = $result['service_2_id'];
					$invite_info_arr['service']['e_service_id'] = $e_service_id;
					$invite_info_arr['service']['name'] = $service_arr[$service_2_id];
					$rr = $invite -> get_item_service_by_e_invite_id($iid);
					if(is_array($rr)){
						foreach ($rr as $key => $value) {
							$invite_info_arr['service']['children'][$key]['id'] = $value['service_3_id'];
							$invite_info_arr['service']['children'][$key]['name'] = $service_arr[$value['service_3_id']];
						}
					}else{
						$invite_info_arr['service']['children'] = array();
					}
				}else{
					$invite_info_arr['service'] = array();
				}
				$ret_arr = get_state_info(1000);
				$ret_arr['data'] = $invite_info_arr;
				return $ret_arr;
			}else{
				return get_state_info(1318);
			}
		}else{
			return get_state_info(1318);
		}

	}
	//获取我的招募列表
	function home_get_my_recruit(){
		global $PAGESIZE,$flash;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);	
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);
		$page = intval($_POST['page']);
		if($page < 1){
			$page = 1;
		}
		$pagesize = $PAGESIZE['MYWORK_PAGE'];
		$from_rows = ($page - 1) * $pagesize;
		$recruit = new recruit();
		$recruit_list = $recruit -> get_recruit_list_by_user($uid, $from_rows, $pagesize, $flash);
		if($recruit_list){
			$i = 0;
			$base = new base();
			foreach ($recruit_list as $key => $value) {
				$recruit_arr[$i]['id'] = $value['id'];
				$recruit_arr[$i]['title'] = $value['name'];
				$recruit_arr[$i]['status'] = $value['status'];
				$recruit_arr[$i]['apply_number'] = $value['apply_count'];
				$recruit_arr[$i]['invitation_number'] = $value['invite_count'];
				$recruit_arr[$i]['deadline'] = strtotime($value['interview_end_time']);
				$recruit_arr[$i]['datetime'] = strtotime($value['add_date']);
				$city_info = $base -> get_city_info($value['city_id'], $flash);
				$recruit_arr[$i]['city'] = $city_info['cname'];
				
				$recruit_arr[$i]['icon_img'] = $value['cover_server_url'].$value['cover_path_url'];
				$i++;	
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data'] = $recruit_arr;
			return $ret_arr;	
		}else{
			//查不到招募信息
			return get_state_info(1302);
		}
		
	}

	//获取我的报名列表
	function home_get_my_apply(){
		global $PAGESIZE,$flash;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);	
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);

		//取服务的缓存数据
		$service = new service();
		$service_array = $service -> get_service($flash);
		foreach ($service_array as $k => $v) {
			$service_arr[$v['id']] = $v['name'];
		}
		
		$page = intval($_POST['page']);
		if($page < 1){
			$page = 1;
		}
		$pagesize = $PAGESIZE['MYWORK_PAGE'];
		$from_rows = ($page - 1) * $pagesize;
		$apply = new apply();
		$apply_list = $apply -> get_apply_list_by_user($uid, $from_rows, $pagesize);
		if($apply_list){
			$i = 0;
			$base = new base();
			$recruit = new recruit();
			foreach ($apply_list as $key => $value) {
				$rid = $value['recruit_id'];
				$recruit_info = $recruit -> get_recruit_info($rid, $flash);				
				if($recruit_info){
					$apply_recruit_list[$i]['id'] = $rid;
					//$apply_recruit_list[$i]['recruit_id'] = $rid;
					$apply_recruit_list[$i]['title'] = $recruit_info['name'];
					$apply_recruit_list[$i]['status'] = $recruit_info['status'];
					$apply_recruit_list[$i]['deadline'] = strtotime($recruit_info['interview_end_time']);
					$apply_recruit_list[$i]['datetime'] = strtotime($value['apply_date']);
					$city_info = $base -> get_city_info($recruit_info['city_id'], $flash);
					$apply_recruit_list[$i]['city'] = $city_info['cname'];

					$apply_recruit_list[$i]['icon_img'] = $recruit_info['cover_server_url'].$recruit_info['cover_path_url'];

					//获取报名的二级服务
					//1. 根据招募的服务id 获取招募的二级服务
					$e_service_recruit = $apply -> get_service_recruit($value['e_service_id']);
					$second_service = $service_arr[$e_service_recruit['service_2_id']];
					$apply_recruit_list[$i]['service'] = $second_service;
					$i++;
				}else{
					$apply_recruit_list = array();
				}		
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data'] = $apply_recruit_list;
			return $ret_arr;	
		}else{
			//查不到报名信息
			return get_state_info(1301);
		}
		
	}

	//获取我的页面的用户数据 
	function home_get_mine_user_info(){
		global $flash;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);	
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);

		$user = new user();
		$userinfo = $user -> get_userinfo($uid, $flash);
		
		$u_info['icon_img'] = $userinfo['icon_server_url'].$userinfo['icon_path_url'];
		$u_info['nickname'] = $userinfo['nickname'];
		if($userinfo['user_type'] == 'user'){
			$u_info['user_type'] = '0';
		}else{
			$u_info['user_type'] = '1';
		}
		
		//获取用户的第三方登陆 openid
		empty($userinfo['openid_qq']) && !isset($userinfo['openid_qq']) ? $u_info['qq_openid'] = "" : $u_info['qq_openid'] = $userinfo['openid_qq'];
		empty($userinfo['openid_weibo']) && !isset($userinfo['openid_weibo']) ? $u_info['weibo_openid'] = "" : $u_info['weibo_openid'] = $userinfo['openid_weibo'];
		//empty($userinfo['openid_weixin']) && !isset($userinfo['openid_weixin']) ? $u_info['weixin_openid'] = "" : $u_info['weixin_openid'] = $userinfo['openid_weixin'];
		//获取用户的绑定邮箱 状态
		if(empty($userinfo['email'])){
			$u_info['email_state'] = '0';
			$u_info['email'] = ""; 			
		}else{
			if($userinfo['email_status'] == 'yes'){
				$u_info['email_state'] = '1';
				$u_info['email'] = $userinfo['email']; 			
			}else{
				$u_info['email_status'] = '0';
				$u_info['email'] = $userinfo['email']; 			
			}
		}
		
		//获取用户的服务 字串
		$service = new service();
		$service_array = $service -> get_service($flash);
		foreach ($service_array as $k => $v) {
			$service_arr[$v['id']] = $v['name'];
		}
		$user_service = $service -> get_e_service_by_user($uid);
		if(is_array($user_service)){
			foreach ($user_service as $key => $value) {
				$service_name[] = $service_arr[$value['service_2_id']];
			}
			$u_info['service'] = implode('/',array_unique($service_name));
		}else{
			$u_info['service'] = "";
		}

		//获取用户的新收到的信息 新收到的私信 新收到的邀约 新收到的报名
		$user_msg_total = new user_msg_total();
		$msg_num = $user_msg_total -> get_user_msg_total_info($uid);
		if($msg_num['message'] == '0'){
			$u_info['newest_message_time'] = '0';
		}else{
			$u_info['newest_message_time'] = '1';
		}
		if($msg_num['recruit_apply'] == '0'){
			$u_info['newest_apply_time'] = '0';
		}else{
			$u_info['newest_apply_time'] = '1';
		}
		if($msg_num['reply_invite'] == '0'){
			$u_info['newest_invitation_time'] = '0';
		}else{
			$u_info['newest_invitation_time'] = '1';
		}
		
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $u_info;
		return $ret_arr;	
	}
	//收藏红人列表
	function home_get_my_favorites_reds(){
		global $COMMON_CONFIG;
		$user = new user();
		$base = new base();
		$userprofile = new userprofile();
		$service = new service();
		$collect = new collect();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		_check_login($uid,$token);
		isset($_POST['page']) ? $page = intval($_POST['page']) : $page = '';
		if($page < 1){
			$page = 1;
		}
		$pagesize = 10;
		$from_rows = ($page - 1) * $pagesize;
		$collect_users_all =  $collect -> get_collect_list_by_user_type($uid,'user',0,0,$flash);
		$collect_users =  $collect -> get_collect_list_by_user_type($uid,'user',$from_rows,$pagesize,$flash);
		//获取红服务
		$service_list = $base -> get_service_list();
		if($collect_users){
			foreach($collect_users as $collect_user){
				$userinfo = $user -> get_userinfo($collect_user['dynamic_id']);
				$user_profile = $userprofile -> get_user_profile($collect_user['dynamic_id']);
				$address_info = $base -> get_address_info($user_profile['province_id'],$user_profile['city_id'],$user_profile['district_id']);
				if($userinfo['identity_card_status'] == 'yes'){
					$has_authentication = '1';
				}else{
					$has_authentication = '0';		
				}
				if($userinfo['mobile_status'] == 'yes'){
					$has_verify_mobile = '1';
				}else{
					$has_verify_mobile = '0';		
				}
				//获取用户的所选的二级服务
				$service_array = $service -> get_e_service_by_user($uid);
				$service_2_name = '';
				if($service_array){
					foreach($service_array as $key=>$value){
						$service_2_name .= $service_list[$value['service_2_id']].'/';
					}	
				}
				$collect_user_list[]= array(
					'id' => $collect_user['dynamic_id'],
					'icon_img' => $userinfo['icon_server_url'].$userinfo['icon_path_url'],
					'nickname' => $userinfo['nickname'],
					'city' => $address_info['city_info']['cname'],
					'sex' =>  $COMMON_CONFIG["SEX"][$user_profile['sex']],
					'service' => rtrim($service_2_name,'/'),
					'has_authentication' => $has_authentication,
					'has_verify_mobile' => $has_verify_mobile	
				);
				
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data']['number'] = count($collect_users_all);
			$ret_arr['data']['list'] = $collect_user_list;
		}else{
			$ret_arr = get_state_info(1064);
		}
		
		return $ret_arr;
	}
	//收藏机构列表
	function home_get_my_favorites_organization(){
		global $COMMON_CONFIG;
		$user = new user();
		$base = new base();
		$orgprofile = new orgprofile();
		$service = new service();
		$collect = new collect();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		_check_login($uid,$token);
		isset($_POST['page']) ? $page = intval($_POST['page']) : $page = '';
		if($page < 1){
			$page = 1;
		}
		$pagesize = 10;
		$from_rows = ($page - 1) * $pagesize;
		$collect_orgs_all =  $collect ->  get_collect_list_by_user_type($uid,'org',0,0,$flash);
		$collect_orgs =  $collect ->  get_collect_list_by_user_type($uid,'org',$from_rows,$pagesize,$flash);
		//获取红服务
		$service_list = $base -> get_service_list();
		if($collect_orgs){
			foreach($collect_orgs as $collect_org){
				$userinfo = $user -> get_userinfo($collect_org['dynamic_id']);
				$org_profile = $orgprofile -> get_org_profile($collect_org['dynamic_id']);
				$org_type = $base -> get_org_type_info($org_profile['type']);
				$address_info = $base -> get_address_info($org_profile['province_id'],$org_profile['city_id'],$org_profile['district_id']);
				if($userinfo['identity_card_status'] == 'yes' || $userinfo['business_card_status'] == 'yes'){
					$has_authentication = '1';
				}else{
					$has_authentication = '0';		
				}
				if($userinfo['mobile_status'] == 'yes'){
					$has_verify_mobile = '1';
				}else{
					$has_verify_mobile = '0';		
				}
				$collect_org_list[]= array(
					'id' => $collect_org['dynamic_id'],
					'icon_img' => $userinfo['icon_server_url'].$userinfo['icon_path_url'],
					'nickname' => $userinfo['nickname'],
					'city' => $address_info['city_info']['cname'],
					'type' =>  $org_type['name'],
					'has_authentication' => $has_authentication,
					'has_verify_mobile' => $has_verify_mobile,
				);
				
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data']['number'] = count($collect_orgs_all);
			$ret_arr['data']['list'] = $collect_org_list;
		}else{
			$ret_arr = get_state_info(1065);	
		}
		return $ret_arr;
		
	}
	//收藏招募列表
	function home_get_my_favorites_recruit(){
		global $COMMON_CONFIG;
		$user = new user();
		$base = new base();
		$recruit = new recruit();
		$service = new service();
		$collect = new collect();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		_check_login($uid,$token);
		isset($_POST['page']) ? $page = intval($_POST['page']) : $page = '';
		if($page < 1){
			$page = 1;
		}
		$pagesize = 10;
		$from_rows = ($page - 1) * $pagesize;
		$collect_recruits_all =  $collect ->  get_collect_list_by_user_type($uid,'recruit',0,0,$flash);
		$collect_recruits =  $collect ->  get_collect_list_by_user_type($uid,'recruit',$from_rows,$pagesize,$flash);
		$collect_recruit_list = array();
		if($collect_recruits){
			foreach($collect_recruits as $collect_recruit){
				$recruit_info = $recruit -> get_recruit_info($collect_recruit['dynamic_id']);
				$service_2_list = '';
				if(is_array($service_2_arr = $recruit -> get_service_list_by_recruit($recruit_info['id']))){
					foreach($service_2_arr as $v2){
						if($service_2_info = $base -> get_service_info($v2['service_2_id'])){
							$service_2_list .= $service_2_info['name'].'/';
						}
					}
				}		
				$collect_recruit_list[] = array(
					'id' => $recruit_info['id'],
					'ruid' => $recruit_info['uid'],
					'icon_img' => $recruit_info['cover_server_url'].$recruit_info['cover_path_url'],
					'title' => $recruit_info['name'],
					'service' => rtrim($service_2_list,'/'),
					'status' => $recruit_info['status'],
					'deadline' => strtotime($recruit_info['interview_end_time'])	
				);
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data']['number'] = count($collect_recruits_all);
			$ret_arr['data']['list'] = $collect_recruit_list;
		}else{
			$ret_arr = get_state_info(1066);	
		}
		return $ret_arr;
	}
	//-------------------------wangyifan----start--------------------------
	//检查app版本 看是否需要提示更新
	function home_check_update(){
		global $PROJECT_VERSION;
		$user = new user();
		//1 确定登录了
		$uid = intval(@$_POST['uid']);
		$token = clear_gpq(@$_POST['app_token']);
		if($uid<1) return get_state_info(1099);
		if(empty($token)) return get_state_info(1099);		
		_check_login($uid,$token);
		//2 获取版本号 版本名 更新简介
		$ret_arr = get_state_info(1000);
		$data['version'] =  $PROJECT_VERSION['version'];
		$data['version_name'] =  $PROJECT_VERSION['version_name'];
		$data['update_information'] =  $PROJECT_VERSION['update_information'];
		$data['url'] =  '';//新版本软件包地址
		$ret_arr['data'] = $data;
		return $ret_arr;
	}
	//-------------------------wangyifan----end----------------------------	
	
	
	