<?php
header("Content-type:text/html;charset=utf-8");
include "../../../includes/common_api_inc.php";
session_start();
//接收移动端传递的数据 action app_token(account password login_type)
$json_data = @$GLOBALS['HTTP_RAW_POST_DATA'];
file_put_contents("/tmp/android_post_log.log",date("Y-m-d H:i:s").",json_array:".$json_data."\n",FILE_APPEND);
$json_array = json_decode($json_data,true);
$ret_arr = array();
if(!isset($json_array["action"]) || empty($json_array["action"])){
	$ret_arr = get_state_info($state_code);
	echo json_encode($ret_arr);
	exit;
}
$action=$json_array["action"];
$user = new user();
$photo = new photo();
switch($action){
	case 'user_login':
		echo json_encode(user_login($json_array));
		break;
	case 'forget1_get_check_code':
		echo json_encode(forget1_get_check_code($json_array));
		break;
	case 'forget2_get_user_info':
		echo json_encode(forget2_get_user_info($json_array));
		break;
	case "forget3_send_forget_code":
		echo json_encode(forget3_send_forget_code($json_array));
		break;
	case "forget4_check_forget_code":
		echo json_encode(forget4_check_forget_code($json_array));
		break;
	case "forget5_reset_password":
		echo json_encode(forget5_reset_password($json_array));
		break;
	case "add_mobile_user":
		echo json_encode(add_mobile_user_android($json_array));
		break;
	case "reg_get_mobile_check_code":
		$mobile = clear_gpq($json_array['mobile']);
		echo json_encode(reg_get_mobile_check_code($mobile));
		break;
	case "sns_bind_exists_account":
		echo json_encode(sns_bind_exists_account($json_array));
		break;
	case "sns_get_mobile_check_code":
		echo json_encode(sns_get_mobile_check_code($json_array));
		break;
	case "sns_add_mobile_user":
		echo json_encode(sns_add_mobile_user($json_array));
		break;
}

//手机用户注册
function add_mobile_user_android($json_array){
	global $user;
	$user_type = clear_gpq($json_array['user_type']);		
	$mobile = clear_gpq($json_array['account']);
	$password = clear_gpq($json_array['password']);		
	$check_code = clear_gpq($json_array['check_code']);
	$app_id = isset($json_array['app_id']) ? clear_gpq($json_array['app_id']) : '';	
	$app_type = isset($json_array['app_type']) ? clear_gpq($json_array['app_type']) : '';	
	$app_os  = isset($json_array['app_os']) ? clear_gpq($json_array['app_os']) : '';
	$app_ui_os = isset($json_array['app_ui_os']) ? clear_gpq($json_array['app_ui_os']) : '';
	$app_ui_os_ver = isset($json_array['app_ui_os_ver']) ? clear_gpq($json_array['app_ui_os_ver']) : '';	
	$app_os_ver = isset($json_array['app_os_ver']) ? clear_gpq($json_array['app_os_ver']) : '';	
	$app_name = isset($json_array['app_name']) ? clear_gpq($json_array['app_name']) : '';	
	$app_ver  = isset($json_array['app_ver']) ? clear_gpq($json_array['app_ver']) : '';		
	if(!isset($user_type) || !in_array($user_type, array("user","org"))) return get_state_info(1001);		
	if(!isset($mobile) || empty($mobile)) return get_state_info(1002);	
	if(!isset($password) || empty($password)) return get_state_info(1003);		
	if(!isset($check_code) || empty($check_code)) return get_state_info(1007);
	$state_code = $user -> check_mobile($mobile);
	if($state_code !== 1000){
		return get_state_info($state_code);
	}
	$state_code = $user -> mobile_exist($mobile);
	if($state_code == 1000){
		return get_state_info(1011);		
	}
	$state_code = $user->check_password($password);
	if($state_code !== 1000){
		return get_state_info($state_code);
	}
	$source = 'app';
	$result = $user -> add_mobile_user($user_type,$mobile,$password,$check_code,$source);
	if(is_array($result)){ 
		$app_token = $user -> get_user_token($result['id'],$result['salt']); 	//得到token
		$data['id'] = $result['id'];
		$result = $user -> android_add_reg_info($result['id'],$app_id,$app_type,$app_os,$app_ui_os,$app_ui_os_ver,$app_os_ver,$app_name,$app_ver);if($result){
			$ret_arr = get_state_info(1000);
			$data['app_token'] =  $app_token;
			$data['user_type'] = $user_type;
			$data['level'] = 1;
			$data['data_percent'] = 5;
			$data['nickname'] = '';
			$ret_arr['data'] = $data;
			return $ret_arr;
		}else{
			return get_state_info(1014);			
		}
	}else{
		return get_state_info($result);		
	}
}	

//发送手机注册激活码(注册)
function reg_get_mobile_check_code($mobile){
	$user = new user();
	if(!isset($mobile) || empty($mobile)) return get_state_info(1018);	
 	$state_code = $user -> check_mobile($mobile);
	if($state_code !== 1000){
		return get_state_info($state_code);
	}
	$state_code = $user -> get_reg_mobile_code($mobile);
	return get_state_info($state_code);
}		


function sns_bind_exists_account($json_array){
	global $user;
	if(!isset($json_array['login_type']) || !in_array($json_array['login_type'], array("mobile","email"))) return get_state_info(1099);
	if(!isset($json_array['account']) || empty($json_array['account'])) return get_state_info(1099);
	if(!isset($json_array['password']) || empty($json_array['password'])) return get_state_info(1099);
	if(!isset($json_array['sns_username']) || empty($json_array['sns_username'])) return get_state_info(1099);
	if(!isset($json_array['sns_face']) || empty($json_array['sns_face'])) return get_state_info(1099);
	if(!isset($json_array['sns_openid']) || empty($json_array['sns_openid'])) return get_state_info(1099);
	if(!isset($json_array['sns_type']) || !in_array($json_array['sns_type'], array("qq","weibo","weixin"))) return get_state_info(1099);
	$data = array();
	$result = $user -> sns_bind_old_user(clear_gpq($json_array['account']),clear_gpq($json_array['login_type']),clear_gpq($json_array['password']),clear_gpq($json_array['sns_openid']),clear_gpq($json_array['sns_type']),clear_gpq($json_array['sns_username']));
	if(is_array($result)){
		$data['id'] = $result['id'];
		$data['face'] = $result['icon_server_url'].$result['icon_path_url'];
		$data['user_type'] = $result['user_type'];
		if(clear_gpq($json_array['login_type']) == 'email'){
			$data['email'] = $result['email'];
		}elseif(clear_gpq($json_array['login_type']) == 'mobile'){
			$data['mobile'] = $result['mobile'];
		}
		$data['level'] = $result['level'];
		$data['data_percent'] = $result['data_percent'];
		$data['nickname'] = $result['nickname'];
		$data['app_token'] = md5($result['id'].$result['salt']);
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $data;
		return $ret_arr;
	}else{
		return get_state_info($result);
	}
}

//发送手机注册激活码(创建并绑定)
function sns_get_mobile_check_code($json_array){
	global $user;
	if(!isset($json_array['account']) || empty($json_array['account'])) return get_state_info(1099);
	$state_code = $user -> check_mobile(clear_gpq($json_array['account']));
	if($state_code !== 1000){
		return get_state_info($state_code);
	}
	$state_code = $user -> get_reg_mobile_code(clear_gpq($json_array['account']));
	return get_state_info($state_code);
}		

//创建并绑定手机账户（app）
function sns_add_mobile_user($json_array){
	global $user,$IMG_STATIC,$IMG_WWW,$photo;
	if(!isset($json_array['account']) || empty($json_array['account'])) return get_state_info(1099);
	if(!isset($json_array['password']) || empty($json_array['password'])) return get_state_info(1099);
	if(!isset($json_array['check_code']) || empty($json_array['check_code'])) return get_state_info(1007);
	if(!isset($json_array['user_type']) || !in_array($json_array['user_type'], array("user","org"))) return get_state_info(1099);
	if(!isset($json_array['sns_username']) || empty($json_array['sns_username'])) return get_state_info(1099);
	if(!isset($json_array['sns_face']) || empty($json_array['sns_face'])) return get_state_info(1099);
	if(!isset($json_array['sns_openid']) || empty($json_array['sns_openid'])) return get_state_info(1099);
	if(!isset($json_array['sns_type']) || empty($json_array['sns_type'])) return get_state_info(1099);
	$app_id = isset($json_array['app_id']) ? clear_gpq($json_array['app_id']) : '';	
	$app_type = isset($json_array['app_type']) ? clear_gpq($json_array['app_type']) : '';	
	$app_os  = isset($json_array['app_os']) ? clear_gpq($json_array['app_os']) : '';
	$app_ui_os = isset($json_array['app_ui_os']) ? clear_gpq($json_array['app_ui_os']) : '';
	$app_ui_os_ver = isset($json_array['app_ui_os_ver']) ? clear_gpq($json_array['app_ui_os_ver']) : '';	
	$app_os_ver = isset($json_array['app_os_ver']) ? clear_gpq($json_array['app_os_ver']) : '';	
	$app_name = isset($json_array['app_name']) ? clear_gpq($json_array['app_name']) : '';	
	$app_ver  = isset($json_array['app_ver']) ? clear_gpq($json_array['app_ver']) : '';		
	$login_type = "mobile";
	$source = "app";
	$data = array();
	$state_code = $user -> check_mobile(clear_gpq($json_array['account']));
	if($state_code == !1000){
		return get_state_info($state_code);
	}
	$state_code = $user -> mobile_exist(clear_gpq($json_array['account']));
	if($state_code == 1000){
		return get_state_info(1011);
	}
	$state_code = $user -> check_password(clear_gpq($json_array['password']));
	if($state_code !== 1000){
		return get_state_info($state_code); 
	}
	$result = $user -> add_mobile_user(clear_gpq($json_array['user_type']),clear_gpq($json_array['account']),clear_gpq($json_array['password']),clear_gpq($json_array['check_code']),$source);
	if(is_array($result)){
		$user -> android_add_reg_info($result['id'],$app_id,$app_type,$app_os,$app_ui_os,$app_ui_os_ver,$app_os_ver,$app_name,$app_ver);
		$hash_dir = $photo -> get_hash_dir('user',$result['id']);
		createdir($IMG_STATIC.'/'.$hash_dir);
		$newname = $photo -> create_newname('jpg');
		$icon_local_url = $IMG_STATIC.'/'.$hash_dir.'/'.$newname;
		$state_code = $photo -> get_photo_by_url(clear_gpq($json_array['sns_face']),$icon_local_url);
		if($state_code == 1000){
			$icon_server_url = $IMG_WWW;
			$icon_path_url = '/'.$hash_dir.'/'.$newname;
			//存入数据库
			$state_code = $user -> sns_bind_new_user($result['id'],clear_gpq($json_array['password']),clear_gpq($json_array['sns_openid']),clear_gpq($json_array['sns_type']),clear_gpq($json_array['sns_username']),$icon_path_url,$icon_server_url);
			if($state_code == 1000){
				$photo -> save_on_upyun($icon_path_url);
				$data['id'] = $result['id'];
				$data['face'] = $icon_server_url.$icon_path_url;
				$data['user_type'] = $result['user_type'];
				$data['mobile'] = clear_gpq($json_array['account']);
				$data['level'] = $result['level'];
				$data['data_percent'] = $result['data_percent'];
				$data['nickname'] = $result['nickname'];
				$data['app_token'] = md5($result['id'].$result['salt']);
				return get_state_info(1000);
			}else{
				return get_state_info($state_code);
			}
		}else{
			return get_state_info($state_code);
		}
	}else{
		return get_state_info($result);
	}
}
function user_login($json_array){
	global $user;
	if(!isset($json_array['login_type']) || !in_array($json_array['login_type'], array("mobile","email"))) return get_state_info(1099);
	if(!isset($json_array['account']) || empty($json_array['account'])) return get_state_info(1002);
	if(!isset($json_array['password']) || empty($json_array['password'])) return get_state_info(1003);
	$state_code = $user -> user_login($json_array['account'],$json_array['password'],$json_array['login_type'],$user_info);
	if($state_code == 1000){
		//查找addr 并返回
		$data = array();
		$data['uid'] = $user_info['id'];
		$data['face'] = $user_info['icon_server_url'].$user_info['icon_path_url'];
		$data['user_type'] = $user_info['user_type'];
		$data['email'] = $user_info['email'];
		$data['mobile'] = $user_info['mobile'];
		$data['level'] = $user_info['level'];
		$data['data_percent'] = $user_info['data_percent'];
		$data['nickname'] = $user_info['nickname'];
		if( $user_info['user_type'] == 'user' ){
			//...
		}else if( $user_info['user_type'] == 'org' ){
			//...
		}
		$data['addr'] = '北京市朝阳区'; #先写死  等出页面在查表写入
		// 生成app_token 返回app_token
		$app_token = $user -> get_user_token($user_info['id'],$user_info['salt']); 
		$data['app_token'] = $app_token;
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $data;
		return $ret_arr;
	}else{
		return get_state_info($state_code);
	}
}
function forget1_get_check_code($json_array){
	if(!isset($json_array["app_id"]) || empty($json_array["app_id"])) return get_state_info(1265);
	//1发送图片验证码地址
	$data = array();
	$data['path'] = "/account/check_code_cache.php?type=forget_check_code&app_id={$json_array["app_id"]}";
	$ret_arr = get_state_info(1000);
	$ret_arr['data'] = $data;
	return $ret_arr;
}
function forget2_get_user_info($json_array){
	//account code  验证 成功返回用户信息
	global $user;
	//接收account和identify_code 并赋值
	if(!isset($json_array['identify_code']) || empty($json_array['identify_code']))return get_state_info(1251);
	$identify_code = clear_gpq($json_array['identify_code']);
	if(!isset($json_array['app_id']) || empty($json_array['app_id']))return get_state_info(1265);
	if(!isset($json_array['account']) || empty($json_array['account']))return get_state_info(1250);
	$account = clear_gpq($json_array['account']);
	//判断 验证码是否正确
	$check_code = new check_code_redis();
	$key = md5("forget_check_code_{$json_array['app_id']}");
	$state_code = $check_code -> check_safe_code($key, $identify_code);
	//$state_code = 1000;//先这样写死
	if($state_code == 1000){
		//验证码匹配 
		if( preg_match("/^[1][3578][0-9]\d{8}$/",$account) == 1){
			//获取用户信息  返回
			$user_info = $user -> get_userinfo_by_account($account,'mobile');
			if($user_info){
				$data = array();
				$data['uid'] = $user_info['id'];
				$data['mobile'] = $user_info['mobile'];
				$data['email'] = $user_info['email'];
				$data['nickname'] = $user_info['nickname'];
				
				$ret_arr = get_state_info($state_code);
				$ret_arr['data'] = $data;
				return $ret_arr;
			}else{
				//用户不存在
				return get_state_info(1511);
			}
		}else if( preg_match("/^[a-zA-Z0-9_+.-]+\@([a-zA-Z0-9-]+\.)+[a-zA-Z0-9]{2,4}$/",$account) == 1 ){
			//获取用户信息  返回
			$user_info = $user -> get_userinfo_by_account($account,'email');
			if($user_info){
				$data = array();
				$data['uid'] = $user_info['id'];
				$data['mobile'] = $user_info['mobile'];
				$data['email'] = $user_info['email'];
				$data['nickname'] = $user_info['nickname'];
				
				$ret_arr = get_state_info($state_code);
				$ret_arr['data'] = $data;
				return $ret_arr;
			}else{
				//用户不存在
				return get_state_info(1511);
			}
		}else{
			//请输入正确的手机或邮箱
			return get_state_info(1253);
		}
	}else{
		return get_state_info($state_code);#验证码不匹配
	}
}
function forget3_send_forget_code($json_array){
	global $user;
	if(!isset($json_array['account_type']) || empty($json_array['account_type'])) return get_state_info(1258); //请填写用户类型
	$account_type = clear_gpq($json_array['account_type']);
	if(!isset($json_array['account']) || empty($json_array['account']))return get_state_info(1250);//请填写用户
	$account = clear_gpq($json_array['account']);
	//因为用户类型不同 发送验证码方式不一样  正则判断用户类型
	if( preg_match("/^[1][3578][0-9]\d{8}$/",$account) ){
		//mobile 是否是注册用户
		$state_code = $user -> mobile_exist($account);
		if($state_code != 1000){
			return get_state_info($state_code);
		}
		//是  发送短信验证码
		$state_code = $user -> get_forget_mobile_code($account);
		if($state_code != 1000){
			return get_state_info($state_code);
		}
		//验证码已经发送  并写入数据库  记下时间  5分钟内不能再发送
		return get_state_info(1000);
	}else if( preg_match("/^[a-zA-Z0-9_+.-]+\@([a-zA-Z0-9-]+\.)+[a-zA-Z0-9]{2,4}$/",$account) ){
		//email  是否是注册用户
		$state_code = $user -> email_exist($account);
		if($state_code != 1000){
			return get_state_info($state_code);
		}
		//是 发送邮箱验证码
		$state_code = $user -> get_forget_code_email_android($account,'email');
		if($state_code != 1000){
			return get_state_info($state_code);
		}
		//验证码已经发送  并写入数据库  
		return get_state_info(1000);
	}else{
		//请输入正确的手机或邮箱
		return get_state_info(1253);
	}
}
function forget4_check_forget_code($json_array){
	global $user;
	//接收uid和forget_code
	if(!isset($json_array['forget_code']) || empty($json_array['forget_code']))return get_state_info(1252);//请填写安全验证码
	$forget_code = clear_gpq($json_array['forget_code']);
	if(!isset($json_array['uid']) || empty($json_array['uid']))return get_state_info(1256);//请填写uid
	$uid = clear_gpq($json_array['uid']);
	$state_code = $user -> check_forget_code_android($uid,$forget_code);//??这个方法要写
	return get_state_info($state_code);
}
function forget5_reset_password($json_array){
	global $user;
	//获取uid forget_code new_password
	if(!isset($json_array['forget_code']) || empty($json_array['forget_code']))return get_state_info(1252);//请填写安全验证码
	$forget_code = clear_gpq($json_array['forget_code']);
	if(!isset($json_array['new_password']) || empty($json_array['new_password']))return get_state_info(1254);//请填写新密码
	$new_password = clear_gpq($json_array['new_password']);
	$state_code = $user -> check_password($new_password);
	if($state_code !== 1000){
		return get_state_info($state_code);
	}
	if(!isset($json_array['uid']) || empty($json_array['uid']))return get_state_info(1256);//请填写用户
	$uid = clear_gpq($json_array['uid']);
	
	$state_code = $user -> update_psw_by_uid_android($uid,$new_password,$forget_code);
	return get_state_info($state_code);
}





// 通过状态码获得相应返回信息
function get_state_info($state_code){
	$ret_arr['state_code'] = $state_code;
	$ret_arr['description'] = get_tips($state_code);
	$ret_arr['time_stamp'] = time();
	return $ret_arr;
}
	    		
?>