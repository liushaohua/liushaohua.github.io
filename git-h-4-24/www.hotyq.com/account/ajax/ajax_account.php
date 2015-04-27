<?php
	session_start();
	header("Content-type:text/html;charset=utf-8");
	require_once('../../includes/common_inc.php');
	require_once(COMMON_PATH.'/user_msg_total.class.php');
	if(isset($_REQUEST['action'])){
		$action = clear_gpq($_REQUEST['action']);
	}else{
		echo 1099;//非法操作
		exit;
	}
	$user = new user();
	$photo = new photo();
	$user_msg_total = new user_msg_total();
	switch($action){
		case 'add_email_user':
			$email = clear_gpq($_REQUEST['account']);
			$password = clear_gpq($_REQUEST['password']);
			$re_password = clear_gpq($_REQUEST['re_password']);
			$safe_code = clear_gpq($_REQUEST['safe_code']);
			$user_type = clear_gpq($_REQUEST['user_type']);
			echo add_email_user($email,$password,$safe_code,$re_password,$user_type);
			break;
		case "add_mobile_user":
			$mobile = clear_gpq($_REQUEST['account']);
			$password = clear_gpq($_REQUEST['password']);
			$re_password = clear_gpq($_REQUEST['re_password']);
			$check_code = clear_gpq($_REQUEST['check_code']);
			$user_type = clear_gpq($_REQUEST['user_type']);
			echo add_mobile_user($mobile,$password,$re_password,$check_code,$user_type);
		case "user_login":
			$login_type = clear_gpq($_REQUEST['login_type']);
			$account = clear_gpq($_REQUEST['account']);
			$password = clear_gpq($_REQUEST['password']);
			//$safe_code = clear_gpq($_REQUEST['safe_code']);
			echo user_login($login_type,$account,$password);
			break;
		case "get_reg_mobile_code":
			$mobile = clear_gpq($_REQUEST['mobile']);
			echo get_reg_mobile_code($mobile);
			break;
		case "get_forget_mobile_code":
			$mobile = clear_gpq($_REQUEST['mobile']);
			echo get_forget_mobile_code($mobile);
			break;
		case "check_forget_mobile_code":
			$mobile = clear_gpq($_REQUEST['mobile']);
			$mobile_code = clear_gpq($_REQUEST['mobile_code']);
			echo check_forget_mobile_code($mobile,$mobile_code);			
		break;
		case "get_user_exist":
			$login_type = clear_gpq($_REQUEST['login_type']);
			$account = clear_gpq($_REQUEST['account']);
			echo get_user_exist($login_type,$account);
			break;
		case "get_account_exist":
			$login_type = clear_gpq($_REQUEST['login_type']);
			$account = clear_gpq($_REQUEST['account']);
			echo get_account_exist($login_type,$account);
			break;		
		case "send_update_psw_email":
			$login_type = clear_gpq($_REQUEST['login_type']);
			$email = clear_gpq($_REQUEST['email']);
			echo send_update_psw_email($login_type,$email);
			break;
		case "update_psw_mobile":
			$account = clear_gpq($_REQUEST['account']);
			$new_password = clear_gpq($_REQUEST['new_password']);
			$re_new_password = clear_gpq($_REQUEST['re_new_password']);
			$mobile_forget_code = clear_gpq($_REQUEST['mobile_forget_code']);
			echo update_psw_mobile($account,$new_password,$re_new_password,$mobile_forget_code);
			break;
		case "update_psw_email":
			$uid = (int)$_REQUEST['uid'];
			$email_forget_code = clear_gpq($_REQUEST['email_forget_code']);
			$new_password = clear_gpq($_REQUEST['new_password']);
			$re_new_password = clear_gpq($_REQUEST['re_new_password']);
			echo update_psw_email($uid,$email_forget_code,$new_password,$re_new_password);
			break;	
		case 'check_login_identify_code':
			$identify_code = clear_gpq($_REQUEST['identify_code']);
			echo check_login_identify_code($identify_code);
			break;
		case 'check_reg_identify_code':
			$email_code = clear_gpq($_REQUEST['email_code']);
			echo check_reg_identify_code($email_code);
			break;	
		case 'send_active_email':
			$userid = intval($_REQUEST['userid']);
			echo send_active_email($userid);
			break;
		case "bind_account":
			$login_type = clear_gpq($_REQUEST['login_type']);
			$account = clear_gpq($_REQUEST['account']);
			$password = clear_gpq($_REQUEST['password']);
			$sns_username = $_SESSION['sns_username'];
			//$sns_face = $_SESSION['sns_face'];
			$sns_openid = $_SESSION['sns_openid'];
			$sns_type = $_SESSION['sns_type'];
			if($login_type != 'email' && $login_type != 'mobile'){
				error_tips(1099);
				exit;
			}
			if(empty($account)){
				error_tips(1099);
				exit;
			}
			if(empty($password)){
				error_tips(1099);
				exit;
			}
			if(empty($sns_username)){
				error_tips(1099);
				exit;
			}
			/*if(empty($sns_face)){
				error_tips(1099);
				exit;
			}*/
			if(empty($sns_openid)){
				error_tips(1099);
				exit;
			}if(empty($sns_type)){
				error_tips(1099);
				exit;
			}
			$result = $user -> sns_bind_old_user($account,$login_type,$password,$sns_openid,$sns_type,$sns_username);
			if(is_array($result)){
				echo 1000;
				$user -> update_cookie_user_info($result);
			}else{
				echo $result;
				exit;
			}
			break;
		case "create_email_bind_account":
			$email = clear_gpq($_REQUEST['account']);
			$password = clear_gpq($_REQUEST['password']);
			$re_password = clear_gpq($_REQUEST['re_password']);
			$user_type = clear_gpq($_REQUEST['user_type']);
			$login_type = "email";
			$source = "web";
			$sns_username = $_SESSION['sns_username'];
			//$sns_face = $_SESSION['sns_face'];
			$sns_openid = $_SESSION['sns_openid'];
			$sns_type = $_SESSION['sns_type'];
			if(empty($sns_username)){
				error_tips(1099);
				exit;
			}
			/*if(empty($sns_face)){
				error_tips(1099);
				exit;
			}*/
			if(empty($sns_openid)){
				error_tips(1099);
				exit;
			}if(empty($sns_type)){
				error_tips(1099);
				exit;
			}
			if(empty($user_type)){
				error_tips(1099);
				exit;
			}
			if(empty($email)){
				error_tips(1099);
				exit;
			}
			if(empty($password)){
				error_tips(1099);
				exit;
			}
			if(empty($re_password)){
				error_tips(1099);
				exit;
			}
			if($password !== $re_password){
				error_tips(1099);
				exit;				
			}
			$state_code = $user->check_email($email);
			if($state_code !== 1000){
				error_tips($state_code);
				exit;
			}
			$state_code = $user -> email_exist($email);
			if($state_code == 1000){
				error_tips(1010);
				exit;	
			}
			$state_code = $user -> check_password($password);
			if($state_code !== 1000){
				error_tips($state_code);
				exit;
			}
			$result = $user -> add_email_user($user_type,$email,$password,$source);
			if(is_array($result)){
				$userid = $result['id'];
				$hash_dir = $photo -> get_hash_dir('user',$userid);
				$newname = $photo -> create_newname('jpg');
				$icon_path_url = $hash_dir.'/'.$newname;
				//var_dump($sns_face);
				//var_dump($icon_path_url);
				//$photo -> upload_photo_by_url($sns_face,$icon_path_url);
				//存入数据库
				$state_code = $user -> sns_bind_new_user($userid,$password,$sns_openid,$sns_type,$sns_username);
				if($state_code == 1000){
					$user_msg_total -> add_user_msg_total($userid);
					$user -> update_cookie_user_info($result);
					echo 1000;
				}else{
					echo $state_code;
					exit;
				}
			}else{
				echo $result;
				exit;
			}			
	}
	function add_email_user($email,$password,$safe_code,$re_password,$user_type){
		global $user,$user_msg_total;
		if(!isset($safe_code) || empty($safe_code)) return 1006;
		if(!isset($user_type) || !in_array($user_type, array("user","org"))) return 1001;		
		if(!isset($email) || empty($email))  return 1002;
		if(!isset($password) || empty($password))	return 1003;
		if(!isset($re_password) || empty($re_password)) return 1004;
		if($password !== $re_password) return 1005;
		$state_code = $user -> check_email($email);
		if($state_code !== 1000){
			return $state_code;
		}
		$state_code = $user -> email_exist($email);
		if($state_code == 1000){
			return 1010;
		}
		$state_code = $user -> check_password($password);
		if($state_code !== 1000){
			return $state_code;
		}
		$reg_code = new check_code;
		$result = $reg_code -> check_safe_code($safe_code);
		if($result !== 1000){
			return $result;
		}			
		$source = "web";		
		$result = $user -> add_email_user($user_type,$email,$password,$source);
		if(is_array($result)){ 
			$user -> update_cookie_user_info($result);
			$user_msg_total -> add_user_msg_total($result['id']);
			return 1000;
		}else{
			return $result;
		}				
	}
	function add_mobile_user($mobile,$password,$re_password,$check_code,$user_type){
		global $user,$user_msg_total;
		if(!isset($check_code) || empty($check_code)) return 1007;
		if(!isset($user_type) || !in_array($user_type, array("user","org"))) return 1001;	
		if(!isset($mobile) || empty($mobile)) return 1002;	
		if(!isset($password) || empty($password)) return 1003;
		if(!isset($re_password) || empty($re_password)) return 1004;
		if($password !== $re_password) return 1005;	
		$state_code = $user -> check_mobile($mobile);
		if($state_code !== 1000){
			return $state_code;
		}
		$state_code = $user -> mobile_exist($mobile);
		if($state_code == 1000){
			return 1011;			
		}
		$state_code = $user -> check_password($password);
		if($state_code !== 1000){
			return $state_code;
		}			
		//手机激活码匹配   写入数据库
		$result = $user -> add_mobile_user($user_type,$mobile,$password,$check_code,"web");
		if(is_array($result)){ 
			$user -> update_cookie_user_info($result);
			$user_msg_total -> add_user_msg_total($result['id']);			
			return 1000;
		}else{
			return $result;
		}				
	}			
	function user_login($login_type,$account,$password){
		global $user;		
		if(empty($login_type)){
			return 1099;
			exit;
		}
		if($login_type != "mobile" && $login_type != "email"){
			return 1099;
			exit;
		}
		if(empty($account)){
			return 1002;
			exit;
		}
		if(empty($password)){
			return 1003;
			exit;
		}
		$state_code = $user -> user_login($account,$password,$login_type,$user_info);
		if( $state_code == 1000 ){
			unset($_SESSION['error_login_count']);
			$user -> update_user_info($user_info['id'],array("from_cms"=>'no'));
			if( $_REQUEST['remeber_login'] == 'true' ){
				$remeber_login = true;
				$user -> update_cookie_user_info($user_info,$remeber_login);
			}else{
				$remeber_login = false;
				$user -> update_cookie_user_info($user_info,$remeber_login);
			}
		}else if( $state_code == 1510 ){
			$_SESSION['error_login_count']++;
			if($_SESSION['error_login_count'] > 3){
				$state_code = 1225;//密码输错已经超过3次 并弹出验证码
			}
		}
		return $state_code;
	}
	function get_reg_mobile_code($mobile){
		global $user;	
		if(!isset($mobile) || empty($mobile)) return 1016;
		$state_code = $user -> check_mobile($mobile);
		if($state_code !== 1000){
			return $state_code;
		}
		return $user -> get_reg_mobile_code($mobile);
	}	
	function get_forget_mobile_code($mobile){
		global $user;
		if(empty($mobile)){
			return 1211;
			exit;
		}
		$state_code = $user -> check_mobile($mobile);
		if($state_code !== 1000){
			return $state_code;
			exit;
		}
		return $user -> get_forget_mobile_code($mobile);
	}
	function check_forget_mobile_code($mobile,$mobile_code){
		global $user;		
		if(empty($mobile)){
				return 1218;
				exit;
		}
		if(empty($mobile_code)){
				return 1219;
				exit;
		}
		$state_code = $user -> check_mobile($mobile);
		if($state_code !== 1000){
			return $state_code;
			exit;
		}
		$result = $user -> check_forget_mobile_code($mobile,$mobile_code);
		if($result == 1000){
			//查到了  mobile $mobile_code  写入session
			$_SESSION['mobile'] = $mobile;
			$_SESSION['mobile_code'] = $mobile_code;
			return 1000;
		}else{
			return $result;
		}				
	}	
	function get_user_exist($login_type,$account){
		global $user,$flash;		
		if(empty($login_type)){
			return 1099;
			exit;
		}
		if(empty($account)){
			return 1099;
			exit;
		}
		if($login_type == "email" || $login_type == "mobile"){
			$result = $user -> get_userinfo_by_account($account,$login_type,$flash);
			if($result){
				return 1000;
			}else{
				return 1027;//该账号不是登录账号
			}
		}else{
			return 1099;
		}
	}	
	function get_account_exist($login_type,$account){
		global $user;		
		if(empty($login_type)){
			return 1099;
			exit;
		}
		if(empty($account)){
			return 1099;
			exit;
		}
		if($login_type == "email"){
			return $state_code = $user -> email_exist($account);
		}elseif($login_type == "mobile"){
			return $state_code = $user -> mobile_exist($account);
		}else{
			return 1099;
		}
	}	
	function send_update_psw_email($login_type,$email){
		global $user;		
		if(empty($login_type)){
			return 1099;
			exit;
		}
		if(empty($email)){
			return 1099;
			exit;
		}
		$state_code = $user -> get_forget_code_email($email,$login_type);
		if($state_code !== 1000){
			return $state_code;
			exit;
		}				
	}														
	function update_psw_mobile($account,$new_password,$re_new_password,$mobile_forget_code){
		global $user;		
		if(empty($account)){
			return 1230;
			exit;
		}
		if(empty($new_password)){
			return 1231;
			exit;
		}
		if(empty($re_new_password)){
			return 1232;
			exit;
		}
		if($new_password != $re_new_password){
			return 1233;
			exit;
		}
		$state_code = $user->check_password($new_password);
		if($state_code !== 1000){
			return $state_code;
			exit;
		}
		return $user -> update_psw_mobile($account,$new_password,$mobile_forget_code);				
	}
	function update_psw_email($uid,$email_forget_code,$new_password,$re_new_password){
		global $user;		
		if(empty($uid)){
			return 1099;
			exit;
		}
		if(empty($email_forget_code)){
			return 1099;
			exit;
		}
		if(empty($new_password)){
			return 1003;
			exit;
		}
		if(empty($re_new_password)){
			return 1004;
			exit;
		}
		if($new_password != $re_new_password){
			return 1005;
			exit;
		}
		$state_code = $user->check_password($new_password);
		if($state_code !== 1000){
			return $state_code;
			exit;
		}
		return $user -> update_psw_email($uid,$new_password,$email_forget_code);				
		}
	function check_login_identify_code($identify_code){
		$login_code = new check_code;
		$result = $login_code -> check_safe_code($identify_code);
		return $result;				
	}
	function check_reg_identify_code($email_code){
		$reg_code = new check_code;
		$result = $reg_code -> check_safe_code($email_code);
		if($result == 1000){
			return 1000;
		}else{
			return $result;
			exit;
		}				
	}	
	function send_active_email($userid){
		global $user;	
		if($userid < 1) return 1099;
		$state_code = $user->send_active_email($userid);
		if($state_code == 1000){
			return 1000;
		}else{
			return $state_code;
			exit;			
		}				
	}											
			
?>