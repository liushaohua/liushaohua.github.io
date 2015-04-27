<?php

	//发送手机注册验证码
	function account_send_mobile_verify(){
		global $MOBILE_LOCK_TIME;
		$user = new user();
		if(empty($_POST['mobile'])) return get_state_info(1099);
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
	//验证手机注册啊验证码
	function account_verify_code(){
		$user = new user();
		if(empty($_POST['mobile'])) return  get_state_info(1099);
		if(empty($_POST['verify_code'])) return $ret_arr = get_state_info(1519);
		$mobile = intval($_POST['mobile']);
		$check_code = intval($_POST['verify_code']);
		$mobile_code_info = $user -> get_reg_mobile_code_info($mobile);
		if($mobile_code_info){
			if($mobile_code_info['check_code'] != $check_code){
				return get_state_info(1519);   //手机注册验证码错误。	 
			}else{
				return get_state_info(1000);	
			}
		}else{
			return get_state_info(1518);   
		}

	}
	//----------------------------------------------------------
	//app登陆接口
	function account_login(){
		$user = new user();
		$base = new base();
		//1 验证account
		if(!isset($_POST['account']) || empty($_POST['account'])) return get_state_info(1002);
		//2 根据account获得login_type
		if($base -> is_mobile($_POST['account'])){
			$login_type = 'mobile';
		}elseif($base -> is_email($_POST['account'])){
			$login_type = 'email';
		}else{
			//请输入正确的手机或邮箱
			return get_state_info(1253);
		}
		//3 验证password
		if(!isset($_POST['password']) || empty($_POST['password'])) return get_state_info(1003);
		//4 验证比对
		$state_code = $user -> user_login($_POST['account'],$_POST['password'],$login_type,$user_info);
		if($state_code == 1000){
			//查找addr 并返回
			$data = array();
			$data['uid'] = $user_info['id'];
			$data['has_write_hot_card'] = '0';
			if($user_info['nickname'] != ''){
				$data['has_write_hot_card'] = '1';
			}
			// 生成app_token 返回app_token
			$token = $user -> get_user_token($user_info['id'],$user_info['salt']); 
			if($user_info['user_type'] == 'user'){
				$user_type = '0';
			}elseif($user_info['user_type'] == 'org'){
				$user_type = '1';
			}else{
				$user_type = '0';
			}
			$data['app_token'] = $token;
			$data['user_type'] = $user_type;
			$ret_arr = get_state_info(1000);
			$ret_arr['data'] = $data;
			return $ret_arr;
		}elseif($state_code == 1511){
			return get_state_info(1511);	#用户不存在
		}elseif($state_code == 1510){
			//return get_state_info($state_code);
			return get_state_info(1510);   #密码不正确 
		}
	}
	//app注销登录
	function account_unsubscribe_account(){
		
	}
	//移动端 手机找回密码  给指定用户邮箱发邮件
	function account_forget_password_email(){
		$user = new user();
		$email = clear_gpq(@$_POST['email']);
		//1 验证邮箱是否为空
		if(empty($email))return get_state_info(1017);
		//2是否是邮箱
		$state_code = $user -> check_email($email);
		if($state_code == 1009){//不是邮箱
			return get_state_info(1009);
		}
		//3 是否是注册用户
		$state_code = $user -> email_exist($email);
		if($state_code == 1022){//不是注册用户
			return get_state_info(1022);
		}
		//4 发送邮件
		$state_code = $user -> get_forget_code_email_android($email,'email');
		if($state_code == 1000){
			return get_state_info(1000);
		}else{
			return get_state_info(1506);
		}
	}
	//forget_1 给个account 发验证码
	function account_forget_send_mobile_verify(){
		global $MOBILE_LOCK_TIME,$flash;
		$user = new user();
		//1 验证手机是否为空
		if(empty($_POST['mobile']))return get_state_info(1250);
		//2是否是手机 
		$mobile = intval($_POST['mobile']);
		$state_code = $user -> check_mobile($mobile);
		if($state_code == 1016){//不是手机
			return get_state_info(1016);
		}
		//3 是否是注册用户
		$state_code = $user -> mobile_exist($mobile);
		if($state_code == 1023){//不是注册用户
			return get_state_info(1023);
		}
		//4是  发送短信验证码
		$state_code = $user -> get_forget_mobile_code($mobile);
		if($state_code != 1000){//发送失败
			if($state_code == 1215){
				$ret_arr = get_state_info($state_code);
				//算下时间 赋进去 当前时间now - 发送短信时间 = 间隔时间  >60可发  <60 算出还剩多少
				$forget_user_info = $user -> get_userinfo_by_account($mobile,'',$flash);
				$timediff = time() - strtotime($forget_user_info['mobile_check_date']);
				//$lock_time = 60 - abs($timediff);
				$lock_time = $MOBILE_LOCK_TIME - abs($timediff);
				$ret_arr['data'] = array("lock_time" => $lock_time);
				return $ret_arr;
			}
			//return get_state_info($state_code);
			return get_state_info(1216);//统一发送失败
		}
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = array("lock_time" => $MOBILE_LOCK_TIME);
		return $ret_arr;
	}
	//forget_2 验证验证码
	function account_forget_verify_code(){
		$user = new user();
		//1 手机
		if(!isset($_POST['mobile']) || empty($_POST['mobile']))return get_state_info(1256);//请填写用户
		$mobile = clear_gpq($_POST['mobile']);
		//2 码
		if(!isset($_POST['verify_code']) || empty($_POST['verify_code']))return get_state_info(1252);//请填写安全验证码
		$verify_code = clear_gpq($_POST['verify_code']);
		//3 是否找到
		$state_code = $user -> check_forget_code_mobile($mobile,$verify_code);
		if($state_code == 1000){
			return get_state_info(1000);
		}elseif($state_code == 1217){
			return get_state_info(1217);#验证码不匹配
		}
	}
	//forget_3 移动端手机找回密码  收到  account password code 一起过来
	function account_forget_set_new_password(){
		$user = new user();
		//1 接收code mobile
		if(!isset($_POST['verify_code']) || empty($_POST['verify_code']))return get_state_info(1252);//请填写安全验证码
		$verify_code = clear_gpq($_POST['verify_code']);
		if(!isset($_POST['mobile']) || empty($_POST['mobile']))return get_state_info(1256);//请填写用户
		$mobile = clear_gpq($_POST['mobile']);
		//2 先验证
		$state_code = $user -> check_forget_code_mobile($mobile,$verify_code);
		if($state_code == 1217) return get_state_info(1217);
		//3 检查 password 空  格式
		if(!isset($_POST['password']) || empty($_POST['password']))return get_state_info(1254);//请填写新密码
		$password = clear_gpq($_POST['password']);
		$state_code = $user -> check_password($password);
		if($state_code == 1019){
			return get_state_info(1019);
		}elseif($state_code == 1020){
			return get_state_info(1020);
		}
		//if($state_code !== 1000) return get_state_info($state_code);
		//4 重置
		$state_code = $user -> update_psw_mobile($mobile,$password,$verify_code);
		if($state_code != 1000){
			return get_state_info(1234);
		}
		return get_state_info(1000);
	}
	//上传头像方法  红名片里直接调用这个方法(没action)  那边裁切好 发过来文件流 接收  生成目录 上传到yun和img 写入user表 成功 返回路径
	function account_upload_icon($file_info,$uid){
		global $IMG_WWW,$IMG_CONFIG,$IMG_SERVERINDEX;
		$user = new user();
		$photo = new photo();			
		$state_code = $photo -> check_upload_photo($file_info);
		if($state_code === 1000){
			//1 判断图片是否符合尺寸要求
			//2 生成上传目标路径+名字
			$hash_dir = $photo -> get_hash_dir('user',$uid);
			$file_name = $photo -> create_newname($photo -> get_suffix($file_info["name"]));
			$file_path = $hash_dir."/".$file_name;
			//3 move
			if($photo -> upload_photo($file_info["tmp_name"],$file_path)){
				//4 存库
				$userinfo = $user -> get_userinfo($uid);#先查出 删除放后面
				$result = $user -> update_face($uid,$file_path);#修改头像
				if(!$result) return 1207;#图片保存失败
				//5 删除
				if(!empty($userinfo['icon_path_url'])){
					$result = $photo -> delete_photo_file($userinfo['icon_path_url']);
					if(!$result) return 1208;#图片删除失败
				}
				$success = array('code' => '1000','icon_path_url' => $IMG_WWW.$file_path);
				return $success;#成功
			}else{
				return 1203;
			}
		}elseif($state_code == 1041){
			return 1041;
		}elseif($state_code == 1044){
			return 1044;
		}elseif($state_code == 1042){
			return 1042;
		}elseif($state_code == 1043){
			return 1043;
		}
	}
	//----------------------------------------------------------
	function account_regist(){
		$user = new user();
		if(!isset($_POST['user_type'])) return get_state_info(1001);		
		if(empty($_POST['mobile'])) return get_state_info(1002);
		if(empty($_POST['password'])) return get_state_info(1003);		
		if(empty($_POST['verify_code'])) return get_state_info(1007);
		isset($_POST['app_id']) ? $app_id = clear_gpq($_POST['app_id']) : $app_id = '';
		isset($_POST['app_type']) ? $app_type = clear_gpq($_POST['app_type']) : $app_type = '';
		isset($_POST['app_os']) ? $app_os = clear_gpq($_POST['app_os']) : $app_os = '';
		isset($_POST['app_os_ver']) ? $app_os_ver = clear_gpq($_POST['app_os_ver']) : $app_os_ver = '';
		isset($_POST['app_name']) ? $app_name = clear_gpq($_POST['app_name']) : $app_name = '';
		isset($_POST['app_ver']) ? $app_ver = clear_gpq($_POST['app_ver']) : $app_ver = '';
		$user_type = clear_gpq($_POST['user_type']);			
		$mobile = clear_gpq($_POST['mobile']);
		$password = clear_gpq($_POST['password']);
		$check_code = clear_gpq($_POST['verify_code']);
		$source = 'app';
		if(!in_array($user_type, array("0","1"))) return get_state_info(1001);
		if($user_type == '0'){
			$user_type = 'user';
		}elseif($user_type == '1'){
			$user_type = 'org';	
		}
		$state_code = $user -> check_mobile($mobile);
		if($state_code !== 1000) return get_state_info($state_code);
		$state_code = $user -> mobile_exist($mobile);
		if($state_code == 1000) return get_state_info(1011);
		$state_code = $user->check_password($password);
		if($state_code !== 1000) return get_state_info($state_code);
		$result = $user -> add_mobile_user($user_type,$mobile,$password,$check_code,$source);
		if(is_array($result)){ 
			$app_token = $user -> get_user_token($result['id'],$result['salt']); 	//得到token
			$res = $user -> app_add_reg_info($result['id'],$app_id,$app_type,$app_os,$app_os_ver,$app_name,$app_ver);
			if($res == 1000){
				$ret_arr = get_state_info(1000);
				$data['uid'] =  $result['id'];
				$data['app_token'] =  $app_token;
				$ret_arr['data'] = $data;
				return $ret_arr;
			}else{
				return get_state_info(1014);			
			}
		}else{
			return get_state_info($result);		
		}
	}
	//修改个人红名片
	function account_set_user_card(){
		global $flash;	
		$user = new user();
		$photo = new photo();
		$base = new base();
		$userprofile = new userprofile();
		$rongyun = new rongyun();		
		$token = $_POST['app_token'];
		$uid= $_POST['uid'];
		$file_info = $_FILES['icon_img'];	//接收头像图片
		$nickname = clear_gpq($_POST['nickname']);	 		
		$sex = clear_gpq($_POST['sex']);
		$province_id = intval($_POST['province']);
		$city_id = intval($_POST['city']);
		@$district_id = intval($_POST['district']);
		if($uid<1) return get_state_info(1099);		
		_check_login($uid,$token);			
		if(empty($nickname) ||strlen($nickname) > 90 || !$base -> is_nickname($nickname)){return get_state_info(1101);}	 
		if(!isset($sex) || !in_array($sex , array("m","f")))return get_state_info(1102);	  
		if($province_id < 1)return get_state_info(1103);	
		if($city_id < 1)return get_state_info(1104);	
		// if(empty($user_profile_array['district_id'])){return 1105;}		//区没填写	
		$user_profile_array['sex'] = $sex;
		$user_profile_array['province_id'] = $province_id;
		$user_profile_array['city_id'] = $city_id;
		$user_profile_array['district_id'] = $district_id;
		$result1 = account_upload_icon($file_info,$uid);		
		if(!is_array($result1) && $result1 != 1000){			
			return get_state_info($result1);
		}
		$head_url = $result1['icon_path_url'];
		
		$result = $user -> update_user_nickname($uid,$nickname);
		if($result == 1000){
			$re = $userprofile -> update_user_profile($uid,$user_profile_array);
			if($re == 1000){
				$user -> update_data_percent($uid);
				$rongyun -> get_user_token($uid, $nickname,$head_url);				
				$uinfo = $user -> get_userinfo($uid, $flash = 1);			
				$rongyun_token['rongyun_token'] = $uinfo['rongyun_token'];				
				$ret_arr = get_state_info(1000);
				$ret_arr['data'] = $rongyun_token;
				return $ret_arr;
			}else{
				return get_state_info(1112);
			}
		}else{
			return get_state_info(1112); 	 //资料修改失败
		}  
	}
	//修改机构红名片
	function account_set_org_card(){
		global $flash;		
		$user = new user();
		$photo = new photo();
		$base = new base();
		$orgprofile = new orgprofile();
		$rongyun = new rongyun();		
		$token = $_POST['app_token'];
		$uid= $_POST['uid'];
		$file_info = $_FILES['icon_img'];	//接收头像图片			
		$nickname = clear_gpq($_REQUEST['nickname']);
		$province_id = intval($_REQUEST['province']);
		$city_id = intval($_REQUEST['city']);
		@$district_id = intval($_REQUEST['district']);
		$type = intval($_REQUEST['org_type']);
		$legal_person = clear_gpq($_REQUEST['is_legal']);	
		if($uid<1) return get_state_info(1099);		
		_check_login($uid,$token);			
		if(empty($nickname) || !$base -> is_nickname($nickname)||strlen($nickname)>90) return get_state_info(1101);	#昵称填写错误		
		if($province_id < 1)return get_state_info(1103);	#省id为空
		if($city_id < 1)return get_state_info(1104);	#城市为空	
		if($type < 1)return get_state_info(1137);		#机构类型判断		
	
		if(!isset($legal_person) ||!in_array($legal_person , array("0","1")))return get_state_info(1139);	
		
		$org_profile_array['create_time'] = $create_time;
		$org_profile_array['province_id'] = $pid;
		$org_profile_array['city_id'] = $cid;
		$org_profile_array['district_id'] = $did;
		$org_profile_array['type'] = $type;	
		$org_profile_array['state'] = $state;
		if($legal_person == '1'){
			$org_profile_array['legal_person'] = 'yes';		
		}else{
			$org_profile_array['legal_person'] = 'no';		
		}
		
		$result1 = account_upload_icon($file_info,$uid);		
		if(!is_array($result1) && $result1 != 1000){			
			return get_state_info($result1);
		}
		$head_url = $result1['icon_path_url'];
		
		$result = $user -> update_user_nickname($uid,$nickname);
		if($result == 1000){
			$re = $orgprofile -> update_org_profile($uid,$org_profile_array);
			if($re == 1000){
				$user -> update_data_percent($uid);
				$rongyun -> get_user_token($uid, $nickname,$head_url);				
				$uinfo = $user -> get_userinfo($uid, $flash = 1);			
				$rongyun_token['rongyun_token'] = $uinfo['rongyun_token'];				
				$ret_arr = get_state_info(1000);
				$ret_arr['data'] = $rongyun_token;
				return $ret_arr;
			}else{
				return get_state_info(1112);
			}
		}else{
			return get_state_info(1112); 	 //资料修改失败
		}  
	}
	function check_public_var(){
		if(isset($_POST['app_id']) && isset($_POST['app_type']) && isset($_POST['app_os']) && isset($_POST['app_os_ver']) && isset($_POST['app_name']) && isset($_POST['app_ver'])){
			return true;	
		}else{
			return false;
		} 
	}
	   