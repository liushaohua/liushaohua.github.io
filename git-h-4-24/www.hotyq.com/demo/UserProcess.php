<?php
/**
* 验证邮箱地址是否被注册
* @UserFunction
*/
function email_exists(Email $email){
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	if ($db->fetchColumn("SELECT count(*) FROM `user` WHERE `email` = ?", $email)!=0) {
		return true;
	} else {
		return false;
	}
}

/**
* 验证手机号是否被注册
* @UserFunction
*/
function mobile_exists(Mobile $mobile){
	return false;
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	if ($db->fetchColumn("SELECT count(*) FROM `user` WHERE `mobile` = ?", $mobile)!=0) {
		return true;
	} else {
		return false;
	}
}

/**
* 邮箱地址用户登录
* @UserFunction(method = POST|GET)
*/
function email_login(Email $email, String $password){
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	$user = $db->fetch("SELECT `id`, `username`, `portrait`, `passwd` FROM `user` WHERE `email` = ?", $email);
	if ($user) {
		if ($password != $user["passwd"]) {
			throw new ProException("email or password is error", 103);
		}/* else if($user["status"] != 1) {
			throw new ProException("user not Activation ", 105);
		}*/ else {
			setUserCredential($user['id']);
			$user['status'] = 1;
			unset($user['passwd']);
			return $user;
		}
	} else {
		throw new ProException("email or password is error", 104);
	}
}

/**
* 手机号用户登录
* @UserFunction(method = POST)
*/
function mobile_login(Mobile $mobile, String $password){
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	$user = $db->fetch("SELECT `id`, `username`, `portrait`, `password`, `status` FROM `user` WHERE `mobile` = ?", $mobile);
	if ($user) {
		if ($password != $user["password"]) {
			throw new ProException("email or password is error", 103);
		} else if($user["status"] != 1) {
			throw new ProException("user not Activation", 105);
		} else {
			setUserCredential($user['id']);
			unset($user['password']);
			return $user;
		}
	} else {
		throw new ProException("email or password is error", 104);
	}
}
/**
* 退出登录
* @UserFunction(method = GET)
* @CheckLogin
*/
function logout(){
	setcookie (AUTH_COOKIE_KEY, "", time() - 3600);
}

/**
* 用户注册
* @UserFunction(method = POST)
*/
function reg(Email $email,/* Mobile $mobile,*/ String $username, String $password){
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	if ($db->fetchColumn("SELECT count(*) FROM `user` WHERE `email` = ?", $email) != 0) {
		throw new ProException("email $email is exists ", 101);
	} /*else if($db->fetchColumn("SELECT count(*) FROM `user` WHERE `mobile` = ?", $mobile) != 0) {
		throw new ProException("mobile $mobile is exists ", 102);
	} */else {
		$portrait = 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($email))).'?s=82&d=wavatar';
		$db->insert("INSERT INTO `user` (`email`, `username`, `portrait`, `passwd`) values(?,?,?,?)", $email, $username, $portrait, $password);
		//send_activatiton_code($mobile);
	}
}

/**
* 激活手机号
* @UserFunction
*/
function activate(Mobile $mobile, Integer $code){
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	$_code = $db->fetchColumn("SELECT `code` FROM `activation_code` WHERE `mobile` = ?", $mobile);
	if ($_code != $code->val) {
		throw new ProException("Activation code is error", 106);
	} else {
		$db->exec("UPDATE `user` SET `status` = 1 WHERE `mobile` = ?", $mobile);
	}
}

/**
* 发送激活码
* @UserFunction
*/
function send_activatiton_code(Mobile $mobile){
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	$code = 2000;
	if ($db->fetchColumn("SELECT count(*) FROM `activation_code` WHERE `mobile` = ?", $mobile) == 0) {
		$db->insert("INSERT INTO `activation_code` (`mobile`, `code`) values(?,?)", $mobile, $code);
	} else {
		$timestamp = $db->fetchColumn("SELECT `timestamp` FROM `activation_code` WHERE `mobile` = ?", $mobile);
		if(time() - $timestamp > 60000) {
			$db->exec("UPDATE `activation_code` SET `code` = ? WHERE `mobile` = ?", $code, $mobile);
		} else {
			throw new ProException("Activation code is sent too frequently", 107);
		}
	}
}

/**
* 获取token
* @UserFunction(method = GET)
* @CheckLogin
*/
function token(){
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	$user = $db->fetch("SELECT `id`, `email`, `username`, `portrait` FROM `user` WHERE `id` = ?", getCurrentUserId());

	if($user) {
		$token = getToken($user['id'], $user['username'], $user['portrait']);
		if (!$token) {
			throw new Exception("API Server Error");
		}

		if ($token->code != 200) {
			throw new ProException($token->errorMessage, $token->code);
		} else {
			unset($token->code);
			unset($token->userId);
			return $token;
		}
	} else {
		throw new ProException("user not found", 108);
	}
}

/**
* 获取某人用户资料
* @UserFunction(method = GET)
* @CheckLogin
*/
function profile(Integer $id){
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	$user = $db->fetch("SELECT `id`, `username`, `portrait` FROM `user` WHERE `id` = ?", $id);

	if ($user) {
		return $user;
	} else {
		throw new ProException("user not found", 109);
	}
}

/**
* 获取全部个人资料
* @UserFunction(method = GET)
* @CheckLogin
*/
function friends(){
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	$result = $db->fetchAll("SELECT `id`, `username`, `portrait` FROM `user`");
	echo " ";
	return $result;
}

/**
* 从融云API上进行用户授权，并获取token
*/
function getToken($id, $username, $portrait) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, RONGCLOUD_API_URL);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('userId'=>$id, 'name'=>$username, 'portraitUri'=>$portrait));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('appKey:'.RONGCLOUD_APP_KEY,'appSecret:'.RONGCLOUD_APP_SECRET));
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$ret = curl_exec($ch);
	if (false === $ret) {
		$err =  curl_errno($ch);
		echo $err;
		curl_close($ch);
		return false;
	}
	curl_close($ch);
	return json_decode($ret);
}

function setUserCredential($userId){
	$_temp = rand(1000,9999)."|rongcloud|$userId|".time();
	setcookie(AUTH_COOKIE_KEY, do_mencrypt($_temp, AUTH_COOKIE_KEY), time() + 3600*24*30);
}

function getCurrentUserId(){
	$credential = $_COOKIE[AUTH_COOKIE_KEY];
	if(isset($credential)) {
		$_temp = do_mdecrypt($credential, AUTH_COOKIE_KEY);
		if (strpos($_temp,'rongcloud') == 5) {
			$arr = explode('|', $_temp);
			return $arr[2];
		} 
	}
	throw new ProException("credential is error", 111);
}

function do_mencrypt($input, $key) {
        $key = substr(md5($key), 0, 24);
        $td = mcrypt_module_open('tripledes', '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return trim(chop(base64_encode($encrypted_data)));
}
    
function do_mdecrypt($input, $key) {
        $input = trim(chop(base64_decode($input)));
        $td = mcrypt_module_open('tripledes', '', 'ecb', '');
        $key = substr(md5($key), 0, 24);
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $decrypted_data = mdecrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return trim(chop($decrypted_data));
}   
