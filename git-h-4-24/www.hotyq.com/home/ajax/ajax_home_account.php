<?php
	/*
	*	修改账户密码验证ajax处理  zhaozhenhuan
	*	
	*/

	session_start();
	header("Content-type:text/html;charset=utf-8");
	require_once('../../includes/common_home_inc.php');
	require_once(COMMON_PATH.'/orgprofile.class.php');
	require_once(COMMON_PATH.'/identity.class.php');

	$user = new user();
	$userprofile = new userprofile();
	$orgprofile = new orgprofile();
	$uid = $user_info["id"];
	$user_type = $user_info["user_type"];
	//var_dump($cookie);
	
	
	$action = clear_gpq($_REQUEST['action']);
	if(!isset($_REQUEST["action"]) || empty($_REQUEST["action"]))	return 1099;
 
	if($uid<1) return 1099; 
	
	//$user = $user_info["id"];
	
	

	switch($action){
		case 'check_password_correct':
			//check_session();
			echo check_password_correct($uid,clear_gpq($_REQUEST['input_password']));
			break;
		case 'modify_new_password':
			//check_session();
			echo modify_new_password($uid,clear_gpq($_REQUEST['input_password']),clear_gpq($_REQUEST['new_password']),clear_gpq($_REQUEST['r_new_password']));
			break;
		case "mobile_user_certify_email":
			$email = $_REQUEST['email'];
			echo mobile_user_certify_email($uid,$email);
			break;			
		case 'get_certify_mobile_code':				#login_type=email
			$mobile = $_REQUEST['mobile'];	
			echo get_certify_mobile_code($mobile);	
			break;
		case 'email_user_certify_mobile':
			$mobile = $_REQUEST['mobile'];
			$check_code = $_REQUEST['check_code'];
			echo email_user_certify_mobile($uid,$mobile,$check_code);
			break;
		case 'email_user_certify_email':
			echo email_user_certify_email($uid);
			break;
		case 'certify_identity_card':
			//check_session();
			$identity_name = clear_gpq($_REQUEST['identity_name']);
			$identity_num = clear_gpq($_REQUEST['identity_num']);
			echo certify_identity_card($uid,$identity_name,$user_type,$identity_num);
			//var_dump(certify_identity_card($uid,$identity_name,$user_type,$identity_num));
			break;	
		case 'certify_business_num':
			//check_session();
			$business_num = clear_gpq($_REQUEST['business_num']);
			$business_name = clear_gpq($_REQUEST['business_name']);		
			echo certify_business_num($uid,$business_num,$business_name);
			break;
		case 'mobile_is_checked':
			//var_dump($user_info['mobile_status']);exit;
			if($user_info['mobile_status'] == 'yes'){
				echo 1;
			}else{
				echo 0;
			}
			break;			
	}
	//检查密码是否正确
	function check_password_correct($uid,$input_password){
		global $db_hyq_read,$user;	
		if(strlen($input_password) < 6 || strlen($input_password) > 16){
			return 1003;
		}			
		$pwd = $user->get_user_password($uid);
		if($pwd['password'] == md5(md5(clear_gpq($input_password)).$pwd['salt'])){
			return 1000;
		}else{
			return 1158;	#密码错误
		}
	}		
	//修改密码
	function modify_new_password($uid,$input_password,$new_password,$r_new_password){
		global $db_hyq_write,$user,$flash;		
		$pwd = $user->get_user_password($uid);
		if($pwd['password'] !== md5(md5(clear_gpq($input_password)).$pwd['salt'])){
			return 1158;	#密码错误
		}
		if(strlen($new_password) < 6 || strlen($new_password) > 16){
			return 1003;
		}	
		if($new_password !== $r_new_password){
			return 1005;
		}			
		$userinfo['agent'] = $_SERVER['HTTP_USER_AGENT'];
		$userinfo['salt'] = md5(random());
		$userinfo['password'] = md5(md5(clear_gpq($new_password)).$userinfo['salt']);
		$userinfo['ip'] = getIP();
		//$userinfo['email_check_code'] = md5(random()); 		
		$result = $user -> update_user_info($uid,$userinfo);
		//var_dump($result);
		if($result){
			$user ->delete_cookie_user_info();
			$user -> get_userinfo($uid, $flash = 1);
			return 1000;
		}else{
			return 1157; #修改密码失败
		}	
		
	}
	//手机注册的用户绑定邮箱  
	function mobile_user_certify_email($uid,$email){
		global $user,$userprofile;
		if(empty($email)){
			return 1017;
		}	
		$userinfo = $user -> get_userinfo($uid);
		if($email == $userinfo['email']){	#如果邮箱一样
			return email_user_certify_email($uid);
		}else{
			$state_code = $user -> check_email($email);
			if($state_code !== 1000){
				return $state_code;
			}
			$state_code = $user -> email_exist($email);
			if($state_code == 1000){
				return 1010;	
			}				
			return $user -> mobile_user_certify_email($uid,$email);							
		}

	}
	//邮箱用户  发短信  
	function get_certify_mobile_code($mobile){
		global $user;
		if(empty($mobile)){
			return 1018;
		}
		$state_code = $user -> check_mobile($mobile);
		if($state_code !== 1000){
			return $state_code;
		}
		return $user -> get_reg_mobile_code($mobile);
	}
	// 收到验证码开始绑定  邮箱用户的手机	
	function email_user_certify_mobile($uid,$mobile,$check_code){
		global $user;
		if(empty($mobile)){
			return 1018;
		}
		if(empty($check_code) || strlen($check_code)!== 6){
			return 1007;
		}
		$state_code = $user -> check_mobile($mobile);
		if($state_code !== 1000){
			return $state_code;
		}
		return $state_code = $user -> email_user_certify_mobile($uid,$mobile,$check_code);
	}
	//邮箱用户  激活邮箱	
	function email_user_certify_email($uid){
		global $user;		
		$state_code = $user -> send_active_email($uid);
		if($state_code == 1000){
			return 1000;
		}else{
			return $state_code;			
		}	
	}
	//绑定身份证	
	function certify_identity_card($uid,$identity_name,$user_type,$identity_num){
		global $userprofile,$orgprofile,$user;
		if(empty($identity_name)){
			return 1115;
		}
		if(empty($identity_num)){
			return 1115;
		}
		$identity = new identity($identity_num,$identity_name);

		if($identity -> get_identity_info()){
			return 1117;
		}else{
			$result = $identity -> verify_identity();
			$res = array();
			function searchKey($array){
				global $res;
			    foreach($array as $key=>$row){
			        if(!is_array($row)){
			        	if($row == '一致'){
			        		$res[] = $row;
			        	}
			        }else{
			           searchKey($row);
			        }
			    }
			    return $res;
			}
			if(is_array($result)){
				$res = searchKey($result);
				$count = count($res);
				if($count == 2 && $res[0] == "一致" && $res[1] == '一致'){
					$userid = '45803';
					$identity_num = $result['ROW']['INPUT'][0]['gmsfhm'][0]['#text'];
					$identity_name = $result['ROW']['INPUT'][0]['xm'][0]['#text'];
					if($identity -> add_verified_identity($uid,$identity_num,$identity_name)){
						if($user -> update_user_info($uid,array("identity_card_status" => "yes"))){
							$user -> get_userinfo($uid,$flash = 1);
							return 1000;	
						}else{
							return 1122;
						}
					}else{
						return 1122;	#身份证绑定失败	
						//return $result;	
					}	
				}else{
					return 1122;
					//return $result;
				}

			} else{
				return 1122;
				//return $result;
			}
			
		}
		
	}
	//机构法人验证工商号
	function certify_business_num($uid,$business_num,$business_name){
		global $orgprofile,$user;		
		if(empty($business_name)){
			return 1155;
		}
		if(empty($business_num)){
			return 1119;
		}		
		$state_code =$orgprofile-> check_business_num($business_num);
		if($state_code !== 1000){		
			return 1120;	#工商号格式不正确!
		}
		$state_code =$orgprofile-> business_num_exist($business_num);
		if($state_code == 1000){	
			return 1121;	
		}
		$state_code =$orgprofile-> banding_business_num($uid,$business_name,$business_num);
		//echo $state_code; 
		if($state_code == 1000 ){
			//var_dump($state_code);
			$user -> get_userinfo($uid,$flash = 1);
			return 1000;	#绑定成功
		}else{
			return 1123;	#工商号绑定失败
		}
	}
	//检测session是否存在
	function check_session(){
		if(empty($_SESSION)){
			header("location:/account/login"); 
			exit;
		}
	}



	

?>