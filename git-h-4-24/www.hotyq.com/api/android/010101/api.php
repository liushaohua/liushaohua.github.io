sns_add_mobile_user<?php
header("Content-type:text/html;charset=utf-8");
require_once("../../../includes/common_api_android_inc.php");
require_once(COMMON_PATH."/userprofile.class.php");
require_once(COMMON_PATH."/orgprofile.class.php");
//接收移动端传递的数据 action app_token(account password login_type)
$json_data = @$GLOBALS['HTTP_RAW_POST_DATA'];
//var_dump($_FILES);exit;
file_put_contents("/tmp/android_post_log.log",date("Y-m-d H:i:s").",json_array:".$json_data."\n",FILE_APPEND);
$json_array = json_decode($json_data,true);
//var_dump($json_array);exit;


 //var_dump($_FILES['icon_img']);
// var_dump($_FILES['source_img']);
// var_dump($_REQUEST);
//var_dump($_FILES);


// $ret_arr = array();
if(!isset($json_array["action"]) || empty($json_array["action"])){
	if($_REQUEST["action"] == "upload_icon"){ #上传图片改为POST提交
		$action = "upload_icon";
	}else if($_REQUEST["action"] == "add_ablum_photo"){
		$action = "add_ablum_photo";
	}else{
		$ret_arr = get_state_info(1099);
		echo json_encode($ret_arr);
		exit;
	}
}else{
	$action=$json_array["action"];
}
$user = new user();
$photo = new photo();
$album = new album();
$userprofile = new userprofile();
$orgprofile = new orgprofile();
$base = new base();
switch($action){
	case 'ping':
		echo filter_null(json_encode(get_state_info(1000)));
		break;
	case 'upload_icon':
		_check_login($_REQUEST['uid'],$_REQUEST['app_token']);
		//check_login($json_array['uid'],$json_array['app_token']);
		echo filter_null(json_encode(upload_icon()));
		break;
	case 'user_login':
		echo filter_null(json_encode(user_login($json_array)));
		break;
	case 'forget1_get_check_code':
		echo filter_null(json_encode(forget1_get_check_code($json_array)));
		break;
	case 'forget2_get_user_info':
		echo filter_null(json_encode(forget2_get_user_info($json_array)));
		break;
	case "forget3_send_forget_code":
		echo filter_null(json_encode(forget3_send_forget_code($json_array)));
		break;
	case "forget4_check_forget_code":
		echo filter_null(json_encode(forget4_check_forget_code($json_array)));
		break;
	case "forget5_reset_password":
		echo filter_null(json_encode(forget5_reset_password($json_array)));
		break;
	case "add_mobile_user":
		echo filter_null(json_encode(add_mobile_user_android($json_array)));
		break;
	case "reg_get_mobile_check_code":
		$mobile = clear_gpq($json_array['mobile']);
		echo filter_null(json_encode(reg_get_mobile_check_code($mobile)));
		break;
	case "sns_bind_exists_account":
		echo filter_null(json_encode(sns_bind_exists_account($json_array)));
		break;
	case "sns_get_mobile_check_code":
		echo filter_null(json_encode(sns_get_mobile_check_code($json_array)));
		break;
	case "sns_add_mobile_user":
		echo filter_null(json_encode(sns_add_mobile_user($json_array)));
		break;
	case "sns_login":
		echo filter_null(json_encode(sns_login($json_array)));
		break;
	case "update_user_card":	
		echo filter_null(json_encode(update_user_card($json_array)));
		break;
	case "update_org_card":	
		echo filter_null(json_encode(update_org_card($json_array)));
		break;
		



	case "update_user_profile":	
		echo filter_null(json_encode(update_user_profile($json_array)));
		break;	
	case 'add_ablum_photo':
	    echo filter_null(json_encode(add_ablum_photo($json_array)));
	    break;
	case 'delete_ablum_photo':
	    echo filter_null(json_encode(delete_ablum_photo($json_array)));
	    break;

	case "update_org_profile":	
		echo filter_null(json_encode(update_org_profile($json_array)));
		break;




	case "get_role_list":
		echo filter_null(json_encode(get_role_list()));
		break;
	case "get_org_type_list":
		echo filter_null(json_encode(get_org_type_list()));
		break;
	case "get_user_card_data":
		echo filter_null(json_encode(get_user_card_data()));
		break;		
	case "get_user_profile_data":
		echo filter_null(json_encode(get_user_profile_data()));
		break;		
	case "get_org_card_data":
		echo filter_null(json_encode(get_org_card_data()));
		break;
	case "get_address_array":
		echo filter_null(json_encode(get_address_array($json_array)));
		break;
	case "get_address_array":
		echo filter_null(json_encode(get_address_array($json_array)));
		break;
	case "get_user_profile":
		echo filter_null(json_encode(get_user_profile($json_array)));
		break;
	case "get_org_profile":
		echo filter_null(json_encode(get_org_profile($json_array)));
		break;
	case "get_sys_tag_list_for_user":
		echo filter_null(json_encode(get_sys_tag_list_for_user()));
		break;
	case "get_sys_tag_list_for_org":
		echo filter_null(json_encode(get_sys_tag_list_for_org()));
		break;
	// 收藏接口开始
	case "get_my_collect_user":
		echo filter_null(json_encode(get_my_collect_user($json_array)));
		break;
	case "get_my_collect_org":
		echo filter_null(json_encode(get_my_collect_org($json_array)));
		break;
	case "get_my_collect_recruit":
		echo filter_null(json_encode(get_my_collect_recruit($json_array)));
		break;
}

function filter_null($str_json){
	return str_replace(":null",":\"\"",$str_json);
}

//获取我的收藏      招募
function get_my_collect_recruit($json_array){
	global $user,$userprofile,$base;
	$uid = intval($json_array['uid']);
	if($uid<1) return get_state_info(1099);
	$u_info = $user -> get_userinfo($uid);
	if(!$u_info){
		return get_state_info(1063);
	}

	$collect = new collect();
	$recruit = new recruit();
	$type = 'recruit';
	$result = $collect -> get_collect_list_by_user_type($uid,$type,$limit = 10);
	if($result){
		foreach ($result as $k => $v) {
			$red_recruit_id =  $v['dynamic_id'];

			$red_recruit_info = $recruit -> get_recruit_info($red_recruit_id);
			$recruit_ower_id = $red_recruit_info['uid'];

			$recruit_ower_info = $user -> get_userinfo($recruit_ower_id);
			$red_recruit_collect[$k]['name'] = $recruit_ower_info['nickname'];

			//获取地址
			$province_id = $red_recruit_info['province_id'];
			$city_id = $red_recruit_info['city_id'];
			$district_id = $red_recruit_info['district_id'];
			/*
			if($province_id){
				$province_name = $base ->  get_province_info($province_id);	
				$province['id'] = $province_id;
				$province['name'] = $province_name['pname'];
				$red_recruit_collect[$k]['province'] = $province;
			}else{
				$red_recruit_collect[$k]['province'] = "";
			}
			if($city_id){
				$city_name = $base ->  get_city_info($city_id);	
				$city['id'] = $city_id;
				$city['name'] = $city_name['cname'];
				$red_recruit_collect[$k]['city'] = $city;	
			}else{
				$red_recruit_collect[$k]['city'] = "";
			}
			if($district_id){
				$district_name = $base ->  get_district_info($district_id);	
				$district['id'] = $district_id;
				$district['name'] = $district_name['dname'];
				$red_recruit_collect[$k]['district'] = $district;
			}else{
				$red_recruit_collect[$k]['district'] = "";
			}*/
			//获取招募地址
			$address_info = $base -> get_address_info($province_id,$city_id,$district_id);
			$addr_detail = $red_recruit_info['addr_detail'];

			if($addr_detail){
				$red_recruit_collect[$k]['recruit_address'] = $address_info['address'].$addr_detail;	
			}else{
				$red_recruit_collect[$k]['recruit_address'] = $address_info['address'];
			}

			//获取招募简介
			$descr = $red_recruit_info['descr'];
			if($descr){
				$red_recruit_collect[$k]['descr'] = $descr;
			}else{
				$red_recruit_collect[$k]['descr'] = "";
			}
			//获取招募的角色
			$recruit_role_list = $userprofile -> get_role_list_by_recruit($red_recruit_id);
			$role_str = '';
			if($recruit_role_list){
				foreach($recruit_role_list as $role_info){
					$role_str .= $role_info['role_info']['name'].'/'; 
				}
				$role_str = rtrim($role_str,'/');
				$red_recruit_collect[$k]['recruit_role'] = $role_str;
			}else{
				$red_recruit_collect[$k]['recruit_role'] = "";
			}
			//获取面试时间
			$red_recruit_collect[$k]['is_interview'] = $red_recruit_info['is_interview'];
			//开始时间
			$interview_start = date('m.d',strtotime($red_recruit_info['sys_start_time']));
			$red_recruit_collect[$k]['interview_start'] = $interview_start;
			//结束时间
			if(date('Y-m-d',strtotime($red_recruit_info['sys_start_time'])) == date('Y-m-d',strtotime($red_recruit_info['sys_end_time']))){
				$interview_end = date('H:i',strtotime($red_recruit_info['sys_start_time'])).'~'.date('H:i',strtotime($red_recruit_info['sys_end_time']));	
			}else{
				$interview_end = '~'.date('m.d',strtotime($red_recruit_info['sys_end_time']));
			}
			$red_recruit_collect[$k]['interview_end'] = $interview_end;
		}
	}else{
		$red_recruit_collect =array();
	}
	$res = get_state_info(1000);
	$res['data'] = $red_recruit_collect;
	return $res;
}

//获取我的收藏      机构
function get_my_collect_org($json_array){
	global $user,$orgprofile,$base,$orgprofile;
	$uid = intval($json_array['uid']);
	if($uid<1) return get_state_info(1099);
	$u_info = $user -> get_userinfo($uid);
	if(!$u_info){
		return get_state_info(1063);
	}

	$collect = new collect();
	$type = 'org';
	$result = $collect -> get_collect_list_by_user_type($uid,$type,$limit = 10);
	if($result){
		foreach ($result as $k => $v) {
			$red_org_id =  $v['dynamic_id'];

			$red_org_info = $user -> get_userinfo($red_org_id);

			$red_org_collect[$k]['id'] = $red_org_id;
			$red_org_collect[$k]['name'] = $red_org_info['nickname'];
			$red_org_collect[$k]['level'] = $red_org_info['level'];
			$red_org_collect[$k]['face'] = $red_org_info['icon_server_url'].$red_org_info['icon_path_url'].'!f150';

			$red_org_profile = $orgprofile -> get_org_profile($red_org_id);

			//获取机构的所在地
			$province_id = $red_org_profile['province_id'];
			$city_id = $red_org_profile['city_id'];
			$district_id = $red_org_profile['district_id'];

			if($province_id){
				$org_province_name = $base ->  get_province_info($province_id);	
				$org_province['id'] = $province_id;
				$org_province['name'] = $org_province_name['pname'];
				$red_org_collect[$k]['province'] = $org_province;
			}else{
				$red_org_collect[$k]['province'] = "";
			}
			if($city_id){
				$org_city_name = $base ->  get_city_info($city_id);	
				$org_city['id'] = $city_id;
				$org_city['name'] = $org_city_name['cname'];
				$red_org_collect[$k]['city'] = $org_city;	
			}else{
				$red_org_collect[$k]['city'] = "";
			}
			if($district_id){
				$org_district_name = $base ->  get_district_info($district_id);	
				$org_district['id'] = $district_id;
				$org_district['name'] = $org_district_name['dname'];
				$red_org_collect[$k]['district'] = $org_district;
			}else{
				$red_org_collect[$k]['district'] = "";
			}
			//获取机构类型
			$type_id = $red_org_profile['type'];
			$type_info = $base -> get_org_type_info($type_id);
			$type_array['id'] = $type_id;
			$type_array['name'] = $type_info['name'];
			$red_org_collect[$k]['type'] = $type_array;
		}
	}else{
		$red_org_collect =array();
	}
	$res = get_state_info(1000);
	$res['data'] = $red_org_collect;
	return $res;
}
//获取我的收藏      红人
function get_my_collect_user($json_array){
	global $user,$userprofile,$base;

	$uid = intval($json_array['uid']);
	if($uid<1) return get_state_info(1099);
	
	//检查用户类型以及过滤恶意用户
	$u_info = $user -> get_userinfo($uid);
	if(!$u_info){
		return get_state_info(1063);
	}

	$collect = new collect();
	$type = 'user';
	$result = $collect -> get_collect_list_by_user_type($uid,$type,$limit = 10);
	if($result){
		foreach($result as $k=>$v){
			$red_person_id =  $v['dynamic_id'];

			$red_person_info = $user -> get_userinfo($red_person_id);

			$red_person_collect[$k]['id'] = $red_person_id;
			$red_person_collect[$k]['name'] = $red_person_info['nickname'];
			$red_person_collect[$k]['level'] = $red_person_info['level'];
			$red_person_collect[$k]['face'] = $red_person_info['icon_server_url'].$red_person_info['icon_path_url'].'!f150';

			$red_person_profile = $userprofile -> get_user_profile($red_person_id);

			$red_person_collect[$k]['sex'] = $red_person_profile['sex']; 
			$red_person_collect[$k]['age'] = $red_person_profile['age'];

			//获取角色
			$e_role_list = $userprofile -> get_e_role_list_by_user($red_person_id);
			if($e_role_list){
				foreach($e_role_list as $kk=>$vv){
					if($v['role_id'] == 0){
						$parent_id = $vv['role_1_id'];
						$role_info = $userprofile -> get_role_info($parent_id);
						if($role_info){
							$role_array[$kk]['name'] = $role_info['name'];
							$role_array[$kk]['id'] = $vv['id'];
							$role_array[$kk]['parent_id'] = 0;
							$role_array[$kk]['role_id'] = $parent_id;
						}else{
							$role_array = array();
						}
					}else{
						$role_info = $userprofile -> get_role_info($v['role_id']);
						if($role_info){
							$role_array[$kk]['name'] = $role_info['name'];
							$role_array[$kk]['id'] = $vv['id'];
							$role_array[$kk]['parent_id'] = $vv['role_1_id'];
							$role_array[$kk]['role_id'] = $vv['role_id'];
						}else{
							$role_array = array();
						}	
					}
				}
				$red_person_collect[$k]['role_list'] = $role_array;
			}else{
				$red_person_collect[$k]['role_list'] = array();
			}
			//获取所在地
			$province_id = $red_person_profile['province_id'];
			$city_id = $red_person_profile['city_id'];
			$district_id = $red_person_profile['district_id'];

			if($province_id){
				$user_province_name = $base ->  get_province_info($province_id);
				$user_province['id'] = $province_id;
				$user_province['name'] = $user_province_name['pname'];
				$red_person_collect[$k]['province'] = $user_province;
			}else{
				$red_person_collect[$k]['province'] = "";
			}
			if($city_id){
				$user_city_name = $base ->  get_city_info($city_id);
				$user_city['id'] = $city_id;
				$user_city['name'] = $user_city_name['cname'];
				$red_person_collect[$k]['city'] = $user_city;	
			}else{
				$red_person_collect[$k]['city'] = "";
			}
			if($district_id){
				$user_district_name = $base ->  get_district_info($district_id);
				$user_district['id'] = $district_id;
				$user_district['name'] = $user_district_name['dname'];
				$red_person_collect[$k]['district'] = $user_district;
			}else{
				$red_person_collect[$k]['district'] = "";
			}
		}

	}else{
		$red_person_collect = array();
	}
	$res = get_state_info(1000);
	$res['data'] = $red_person_collect;
	return $res;
}

//获取个人用户的系统标签
function get_sys_tag_list_for_user() {
    global $userprofile;
	$first_tag = $userprofile -> get_user_tag_first();
	foreach($first_tag as $k=>$v){
		$sys_tag[$k]['id'] = $v['id'];
		$sys_tag[$k]['name'] = $v['name'];
		$sys_tag[$k]['parent_id'] = $v['parent_id'];

		$parent_id = $v['id'];
		$second_tag = $userprofile -> get_user_tag_second($parent_id);
		if($second_tag == 1301){
			$sys_tag[$k]['children'] = array();
		}else{
			$sys_tag[$k]['children'] = $second_tag;
		}
	}
	
	$res = get_state_info(1000);
	
	$res['data'] = $sys_tag;
	return $res;
}

//获取机构用户的系统标签
function get_sys_tag_list_for_org() {
    global $orgprofile;
	$tag_list = $orgprofile -> get_org_tag_list();
	if($tag_list == 1311){
		$sys_tag = array();
	}else{
		foreach($tag_list as $k=>$v){
			$sys_tag[$k]['id'] = $v['id'];
			$sys_tag[$k]['name'] = $v['name'];
			$sys_tag[$k]['parent_id'] = $v['parent_id'];
		}
	}
	
	
	$res = get_state_info(1000);
	
	$res['data'] = $sys_tag;
	return $res;
}

//手机端上传头像
function upload_icon(){
	global $photo,$IMG_WWW,$user;
	$userprofile = new userprofile();
	
	$uid = intval($_REQUEST['uid']);
	
	$file_info = $_FILES;
	if(!isset($file_info['icon_img']) ) return get_state_info(1204);#请上传头像
	if(!isset($file_info['cover_img']) ) return get_state_info(1205);#请上传封面
	if(!isset($file_info['source_img']) ) return get_state_info(1206);#请上传原图
	//1:1  $file_info['icon_img']------------
	$state_code = $photo -> check_upload_photo($file_info['icon_img']);
	if($state_code != 1000) return get_state_info($state_code);#文件格式
	$state_code = $photo -> check_upload_photo($file_info['cover_img']);
	if($state_code != 1000) return get_state_info($state_code);#文件格式
	$state_code = $photo -> check_upload_photo($file_info['source_img']);
	if($state_code != 1000) return get_state_info($state_code);#文件格式
	$file_name = $photo -> create_newname($photo -> get_suffix($file_info['icon_img']["name"]));
	$icon_path = $photo -> get_hash_dir('user',$uid)."/".$file_name;
	$cover_path = $photo -> get_hash_dir('cover',$uid)."/".$file_name;
	$source_path = $photo -> get_hash_dir('albums',$uid)."/".$file_name;
	if(!$photo -> upload_photo($file_info['icon_img']["tmp_name"],$icon_path)) return get_state_info(1044);
	if(!$photo -> upload_photo($file_info['cover_img']["tmp_name"],$cover_path)) return get_state_info(1044);
	if(!$photo -> upload_photo($file_info['source_img']["tmp_name"],$source_path)) return get_state_info(1044);
	
	$userinfo = $user -> get_userinfo($uid);
	//头像
	$state_code = $user -> update_face($uid,$icon_path);
	if(!$state_code){
		return get_state_info($state_code);//写入数据库失败
	}
	//封面图
	$arr = array('cover_server_url'=>"{$IMG_WWW}",'cover_path_url'=>"{$cover_path}");
	$state_code = $userprofile -> update_user_profile($uid,$arr);
	if(!$state_code){
		return get_state_info($state_code);
	}
	if(!empty($userinfo['icon_path_url'])){
		$result = $photo -> delete_photo_file($userinfo['icon_path_url']);
		if(!$result){
			return get_state_info(1208);//图片删除失败1：1
		}
		$cover_path = str_replace('user',"cover",$userinfo['icon_path_url']);
		$result = $photo -> delete_photo_file($cover_path);
		if(!$result){
			return get_state_info(1208);//4:3
		}
		$albums_path = str_replace('user',"albums",$userinfo['icon_path_url']);
		$result = $photo -> delete_photo_file($albums_path);
		if(!$result){
			return get_state_info(1208);//原图
		}
	}
	$ret_arr = get_state_info(1000);
	//$ret_arr['data'] = _filter_user_info($user -> get_userinfo($uid));
	
	$user_info = $user -> get_userinfo($uid);
	$uinfo['face'] = $user_info['icon_server_url'].$user_info['icon_path_url'];
	$ret_arr['data'] =  $uinfo;
	
	return $ret_arr;
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
		$data['uid'] = $result['id'];
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
		$data['uid'] = $result['id'];
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
		$icon_path_url = '/'.$hash_dir.'/'.$newname;
		$state_code = $photo -> upload_photo_by_url(clear_gpq($json_array['sns_face']),$icon_path_url);
		if($state_code == 1000){
			//存入数据库
			$state_code = $user -> sns_bind_new_user($result['id'],clear_gpq($json_array['password']),clear_gpq($json_array['sns_openid']),clear_gpq($json_array['sns_type']),clear_gpq($json_array['sns_username']),$icon_path_url,$IMG_WWW);
			if($state_code == 1000){
				$data['uid'] = $result['id'];
				$data['face'] = $IMG_WWW.$icon_path_url;
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

	//第三方登陆
	function sns_login($json_array){
		global $user;
		if(!isset($json_array['sns_username']) || empty($json_array['sns_username'])) return get_state_info(1099);
		if(!isset($json_array['sns_face']) || empty($json_array['sns_face'])) return get_state_info(1099);
		if(!isset($json_array['sns_openid']) || empty($json_array['sns_openid'])) return get_state_info(1099);
		if(!isset($json_array['sns_type']) || !in_array($json_array['sns_type'], array("qq","weibo","weixin"))) return get_state_info(1099);
		$data = array();
		$result = $user -> get_userinfo_by_sns(clear_gpq($json_array['sns_openid']),clear_gpq($json_array['sns_type']));
		if($result){
			$data['uid'] = $result['id'];
			$data['face'] = $result['icon_server_url'].$user_info['icon_path_url'];
			$data['user_type'] = $result['user_type'];
			$data['email'] = $result['email'];
			$data['mobile'] = $result['mobile'];
			$data['level'] = $result['level'];
			$data['data_percent'] = $result['data_percent'];
			$data['nickname'] = $result['nickname'];
			$data['addr'] = '北京市朝阳区'; #先写死  等出页面在查表写入
			$data['app_token'] = md5($result['id'].$result['salt']);
			$data['sns_status'] = 'binded';
			$ret_arr = get_state_info(1000);
			$ret_arr['data'] = $data;
			return $ret_arr;	
		}else{
			$ret_arr = get_state_info(1000);
			$data['sns_status'] = 'unbind';
			$ret_arr['data'] = $data;
			return $ret_arr;	
		}
	}

//个人用户填写红名片
function update_user_card($json_array){	
	global $user,$userprofile;
	$userprofile = new userprofile();
	$uid = intval($json_array['uid']);	
	if($uid<1) return get_state_info(1099);
	$nickname = clear_gpq($json_array['nickname']);
	$user_profile_array['sex'] = clear_gpq($json_array['sex']);
	$user_profile_array['province_id'] = intval($json_array['province_id']);
	$user_profile_array['city_id'] = intval($json_array['city_id']);
	$user_profile_array['district_id'] = intval($json_array['district_id']);
	$user_profile_array['state'] = clear_gpq($json_array['state']);

	//红角色  第一次添加期望角色
	if(!empty($json_array['role_list']) && isset($json_array['role_list'])){
		//红角色部分
		$user_role_arr = $json_array['role_list'];
		$rr = _update_user_role_list($uid,$user_role_arr);
		if($rr != 1000){
			return get_state_info($rr);
		}
		// $role_id = intval($json_array['role']);
		// $user_profile_array['role'] = $role_id;
		// $arr['id'] = 0;	
		// $arr['parent_id'] = $role_id;	
		// $r = $userprofile -> add_role_by_user($uid,$arr);
	}else{
		return get_state_info(1107);		#期望角色没填写
	}

	if(empty($nickname) || preg_match("/[\&\<\>\'\"\\\?\=\$\%\^\*\@\/\#]/",$nickname)||strlen($nickname)>100) return 1101;	#昵称填写错误
	if(!isset($user_profile_array['sex']) || !in_array($user_profile_array['sex'] , array("f","m"))) return get_state_info(1102);		#性别没填写
	if(!isset($user_profile_array['province_id']) || empty($user_profile_array['province_id'])) return get_state_info(1103);	#省没填写
	if(!isset($user_profile_array['city_id']) || empty($user_profile_array['city_id'])) return get_state_info(1104);	#市没填写
	// if(empty($user_profile_array['district_id'])){return 1105;}		#区没填写	
	if(!isset($user_profile_array['state']) ||!in_array($user_profile_array['state'] , array("busy","free","other"))) return get_state_info(1106);	#个人状态没填写		
	//if(!isset($user_profile_array['role_list']) || empty($user_profile_array['role_list'])) return get_state_info(1107);		#期望角色没填写
	
	$result = $user -> update_user_nickname($uid,$nickname);
	$re = $userprofile -> update_user_profile($uid,$user_profile_array);

	if($re == 1000 && $result == 1000 && $rr ==1000){
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = _filter_user_info($user -> get_userinfo($uid));
		return $ret_arr;
	}else if($re != 1000){
		return get_state_info($re); 	 #资料修改失败
	}else if($result != 1000) {
		return get_state_info($result);
	} else if($rr != 1000){
		return get_state_info($rr);
	}
	// else{
	// 	return get_state_info(1110); 		//资料修改失败
	// }  
}	
//机构用户填写红名片
function update_org_card($json_array){	
	global $user,$orgprofile;
	$uid = intval($json_array['uid']);	
	if($uid<1) return get_state_info(1099);
	$nickname = clear_gpq($json_array['nickname']);
	$org_profile_array['create_time'] = intval($json_array['create_time']);
	$org_profile_array['province_id'] = intval($json_array['province_id']);
	$org_profile_array['city_id'] = intval($json_array['city_id']);
	$org_profile_array['district_id'] = intval($json_array['district_id']);
	$org_profile_array['type'] = intval($json_array['type']);	
	$org_profile_array['state'] = clear_gpq($json_array['state']);
	$org_profile_array['legal_person'] = clear_gpq($json_array['legal_person']);			
	if(empty($nickname) || preg_match("/[\&\<\>\'\"\\\?\=\$\%\^\*\@\/\#]/",$nickname)||strlen($nickname)>100){return 1101;}	#昵称填写错误
	if(!isset($org_profile_array['create_time']) || empty($org_profile_array['create_time'] ))return get_state_info(1136);
	if(!isset($org_profile_array['province_id']) ||empty($org_profile_array['province_id']))return get_state_info(1103);
	if(!isset($org_profile_array['city_id']) ||empty($org_profile_array['city_id']))return get_state_info(1104);
	//if(!isset($org_profile_array['district_id']) ||empty($org_profile_array['district_id'] )){return get_state_info(1105);}	
	if(!isset($org_profile_array['type']) || empty($org_profile_array['type'] ))return get_state_info(1137);		
	if(!isset($org_profile_array['state']) ||!in_array($org_profile_array['state'] , array("free","busy","other")))return get_state_info(1138);
	if(!isset($org_profile_array['legal_person']) ||!in_array($org_profile_array['legal_person'] , array("yes","no")))return get_state_info(1139);
	
	$result = $user -> update_user_nickname($uid,$nickname);
	$re = $orgprofile -> update_org_profile($uid,$org_profile_array);		
	
	if($re == 1000 && $result == 1000){
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = _filter_user_info($user -> get_userinfo($uid));
		return $ret_arr;
	}else if($re != 1000){
		return get_state_info($re); 		//资料修改失败
	}else if($result != 1000){
		return get_state_info($result); 		//资料修改失败
	}
	// else{
	// 	return get_state_info(1110); 		//资料修改失败
	// } 
}

//更新个人红档案    
function update_user_profile($json_array){
	global $user,$userprofile;
	//$userprofile = new userprofile();
	$uid = intval($json_array['uid']);
	if($uid<1) return get_state_info(1099);
	
	//检查用户类型以及过滤恶意用户
	$u_info = $user -> get_userinfo($uid);
	if($u_info){
		if($u_info['user_type'] != 'user'){
			return get_state_info(1061);
		}
	}else{
		return get_state_info(1063);
	}

	//红名片部分
	//$uinfo['sex'] = clear_gpq($json_array['sex']);

	$uinfo['province_id'] = intval($json_array['province']);
	$uinfo['city_id'] = intval($json_array['city']);

	if($uinfo['province_id'] == 0 || $uinfo['city_id'] == 0 || !isset($uinfo['city_id']) || !isset($uinfo['province_id'])){
		return get_state_info(1113);	
	}
	

	$uinfo['district_id'] = intval($json_array['district']);
	$uinfo['state'] = clear_gpq($json_array['state']);
	
	//红资料部分
	$uinfo['uid'] = $uid;
	$age = intval($json_array['age']);
	if($age<8){
		$age = 1;
	}
	if($age>70){
		$age = 999;
	}
	$uinfo['age'] = $age;

	$height = intval($json_array['height']);
	if($height<141){
		$height = 1;
	}
	if($height>200){
		$height = 999;
	}
	$uinfo['height'] = $height;

	$weight = intval($json_array['weight']);
	if($weight<41){
		$weight = 1;
	}
	if($weight>90){
		$weight = 999;
	}
	$uinfo['weight'] = $weight;

	$bust = intval($json_array['bust']);
	$waist = intval($json_array['waist']);
	$hips = intval($json_array['hips']);
	
	if($bust<71){
		$bust = 1;
	}
	if($bust>100){
		$bust = 999;
	}
	if($waist<56){
		$waist = 1;
	}
	if($waist>80){
		$waist = 999;
	}
	if($hips<70){
		$hips = 1;
	}
	if($hips>100){
		$hips = 999;
	}

	$uinfo['bust'] = $bust;
	$uinfo['waist'] = $waist;
	$uinfo['hips'] = $hips;
	
	$uinfo['star'] = clear_gpq($json_array['star']);
	$uinfo['blood'] = clear_gpq($json_array['blood']);
	
	//红档案部分
	
	$uinfo['native_province_id'] = intval($json_array['native_province']);
	$uinfo['native_city_id'] = intval($json_array['native_city']);
	$uinfo['native_district_id'] = intval($json_array['native_district']);

	$uinfo['school'] = clear_gpq($json_array['school']);
	$uinfo['finish_year'] = intval($json_array['finish_year']);
	$uinfo['specialty'] = clear_gpq($json_array['specialty']);
	$uinfo['degree'] = clear_gpq($json_array['degree']);
	$uinfo['in_org'] = clear_gpq($json_array['in_org']);
	//红名片中必填项验证
	//if(!empty($uinfo['sex'])){
	//	if(!in_array($uinfo['sex'] , array('f','m'))) return get_state_info(1113);
	//}
	if(!empty($uinfo['state'])){
		if(!in_array($uinfo['state'] , array('free','busy','other'))) return get_state_info(1113);
	}

	if(!empty($uinfo['degree'])){
		if(!in_array($uinfo['degree'] , array('博士','硕士','本科','高中','初中','大专','中专','小学','其它',''))) return get_state_info(1113);		
	}
	if(!empty($uinfo['star'])){
		if(!in_array($uinfo['star'] , array('白羊座','金牛座','双子座','巨蟹座','狮子座','处女座','天秤座','天蝎座','射手座','魔蝎座','水瓶座','双鱼座',''))) return get_state_info(1113);	
	}
	if(!empty($uinfo['blood'])){
		if(!in_array($uinfo['blood'] , array('A', 'B', 'AB', 'O',''))) return get_state_info(1113);	
	}

	//艺名单独更新
	$nickname = trim(clear_gpq($json_array['name']));
	
	if(empty($nickname) || preg_match("/[\&\<\>\'\"\\\?\=\$\%\^\*\@\/\#]/",$nickname) || strlen($nickname)>100){
		return get_state_info(1113);
	}
	$rrr = $user -> update_user_nickname($uid,$nickname);
	$result = $userprofile -> update_user_profile($uid,$uinfo);
	
	//红标签部分
	$user_tag_arr = $json_array['tag'];
	$r = _update_user_tags($uid, $user_tag_arr);
	
	//红角色部分
	$user_role_arr = $json_array['role_list'];
	$rr = _update_user_role_list($uid,$user_role_arr);
	//return $user_role_arr;

	if($result && $r == 1000 && $rr == 1000 && $rrr == 1000){
		$json_arr['uid'] = $uid;
		$ret_arr = get_user_profile($json_arr);  //返回修改后的红资料
		return $ret_arr;
	}else{
		//return 5200;
		if(!$result){
			return get_state_info(5200);
		}else if($r != 1000){
			//return get_state_info(5201);
			return get_state_info($r);
		}else if($rr != 1000){
			//return get_state_info(5202);
			return get_state_info($rr);
		}else if($rrr != 1000){
			return get_state_info($rrr);
		}
		
	}  
}
//角色更新操作
function _update_user_role_list($uid,$role_arr){
	global $userprofile;
	//$userprofile = new userprofile();
	if(is_array($role_arr)){
		foreach($role_arr as $k=>$v){
			if($v['parent_id'] == -1){
				$self_role_array[] = $v['name'];		//自定义角色
			}else{
				$sys_role_array[] = $v['id'];
			}
		}
	}

	//检查系统标签是否设置 是否空
	if(empty($sys_role_array) || !isset($sys_role_array)){
		return 1291;
	}
	
	//检查角色的个数
	if(count($self_role_array) > 1 || count($sys_role_array) > 5){
		return 1298;
	}
	//系统角色转换
	$sys_role_array = array_unique($self_tag_array);   //去除重复值
	foreach($sys_role_array as $k=>$v){
		$result = $userprofile -> get_role_info($v);
		if($result['parent_id'] == 0){
			$result['parent_id'] = $result['id'];
			$result['id'] = 0;
		}
		$role_array['userRoles'][$k] = $result;
	}
	
	//删除原来的角色
	$state_code = $userprofile -> delete_role_list_by_user($uid); 	
	if($state_code != 1000){
		return 1262;
	}
	//添加自定义角色
	$role_into = '';
	if(is_array($self_role_array) && !empty($self_role_array) && isset($self_role_array)){
		$name = $self_role_array[0];
		$all_user_role_list = $userprofile -> all_role_list();
		foreach($all_user_role_list as $v){
			if($v['name'] == $name){
				//有相同  判断是否是自定义
				if($v['parent_id'] != -1){
					//返回错误信息  与系统角色重复
					return 1298;	
				}else{
					//与自定义角色相同   id赋值给他
					$role_into = $v['id'];
					break;
				}
			}
		}
		if($role_into == ''){
			//没相同   插入到角色表中
			$result = $userprofile -> add_role_user_self($name);
			if($result){
				//id加入到列表中
				$role_into = $result;
			}else{
				return 1263;	//插入失败
			}
		}
		$role_array['userRoles'][] = $userprofile -> get_role_info($role_into);
	}
	//存入 e_role
	foreach($role_array['userRoles'] as $v){
		$state_code = $userprofile -> add_role_by_user($uid,$v);
		if($state_code != 1000){
			return 1263;
		}
	}

	return 1000;
}


//标签更新操作
function _update_user_tags($uid,$tag_arr) {
	global $userprofile;

	//标签数据
	if(is_array($tag_arr)){	
		foreach($tag_arr as $k=>$v){
			if($v['parent_id'] == -1){
				$self_tag_array[] = $v['name']; 
			}else{
				$sys_tag_array[] = $v['id'];
			}
		}	
	}

	//标签操作  统计数目 删除原来的标签
	$user_tag = $userprofile -> get_user_tag_by_uid($uid);
	if($user_tag == 1305){
		//错误处理 1305 //标签内容读取失败！
		return 1305;
	}else{
		//统计标签的个数
		if(count($self_tag_array)>5 || count($sys_tag_array)>15){
			return 1310;
		}	
		
		if($user_tag != 1304){
			$state_code = $userprofile -> delete_user_tag($uid);
			if($state_code != 1000){
				//错误处理  删除失败 1307
				return 1307;
			}	
		}
		
	}

	//新添加的自定义标签 操作
	if(!empty($self_tag_array) && isset($self_tag_array)){
		$self_tag_array = array_unique($self_tag_array);   //去除重复值
		$class = 1;
		foreach($self_tag_array as $k => $v){
			$result = $userprofile -> check_tag_exits($class, $v);
			if($result){
				if($result['parent_id'] != 0){
					$self_tag_id_arr[] = $result['id'];
				}
				/*else{
					//自定义标签与系统标签 第一级分类 相同 不能插入标签库
				}*/
			}else{
				$self_tag_id_arr[] = $userprofile -> add_user_self_tag($class, $v);
			}	
		}
	}
	//组合自定义标签 与 系统标签 更新到e_tag表
	if(empty($sys_tag_array) || !isset($sys_tag_array)){							//用户选择的系统标签
		if(empty($self_tag_id_arr) || !isset($self_tag_id_arr)){
			return 1000; 															//系统标签和自定义标签都没有   1303
		}else{
			$person_tag = $self_tag_id_arr;											//只有自定义标签
		}
	}else{
		if(empty($self_tag_id_arr) || !isset($self_tag_id_arr)){
			$person_tag = $sys_tag_array; 											//只选了系统标签
		}else{
			$person_tag = array_merge($sys_tag_array, $self_tag_id_arr);			//系统标签和自定义标签都选
		}
	}
	//将用户标签映射到用户标签关联表
	foreach($person_tag as $k=>$v){
		//将用户标签映射到用户标签表
		$state_code = $userprofile -> add_user_tag($uid, $v);
		if($state_code == 1302){
			return 1302;
		}
	}
	//1000  操作成功 返回标识
	return 1000;
}
//红相册上传
function add_ablum_photo(){
	global $album,$photo,$user,$IMG_WWW;
	//$uid = intval(clear_gpq($_REQUEST['uid']));
	$uid = intval($_REQUEST['uid']);
	if($uid<1) return get_state_info(1099);
	//检查用户类型以及过滤恶意用户
	$u_info = $user -> get_userinfo($uid);
	if(!$u_info){
		return get_state_info(1063);
	}

	$result = $album -> get_photo_list_by_user($uid);
	if(is_array($result) && count($result) >= 6){
		return get_state_info(1058); //您只能上传6张照片，您可以先删除原有照片后重新上传！
	}

	//$file_info = $_FILES['album_img'];
	$file_info = $_FILES[array_pop(array_keys($_FILES))];
	$state_code = $photo -> check_upload_photo($file_info);

	if($state_code == 1000){
		$hash_dir = $photo -> get_hash_dir('albums',$uid);
		$newname = $photo -> create_newname($photo -> get_suffix($file_info['name']));
		$file_path = $hash_dir .'/'.$newname;
		$result = $photo -> upload_photo($file_info['tmp_name'],$file_path);
		if($result){
			$result = $album -> upload_user_photo($uid,$file_path,'album');
			if(is_numeric($result)){
				$ret_arr = get_state_info(1000);

				$data['id'] = $result;
				$data['thumbnail'] = $IMG_WWW.$file_path.'!150.100';
				$data['photo'] = $IMG_WWW.$file_path.'!800';
				$ret_arr['data'] = $data;

				return $ret_arr;
			}else{
				return get_state_info(1044);//图片上传失败！
			}
		}else{
			return get_state_info(1044);//图片上传失败！
		}
	}else{
		//return $_FILES;
		return get_state_info($state_code);//1041(无上传操作)1042（上传图片大小超限）1043（上传图片类型不符）
	}
}

//红照片删除
function delete_ablum_photo($json_array){
	global $album,$photo,$user;
	$uid = intval($json_array['uid']);

	if($uid<1) return get_state_info(1099);
	//检查用户类型以及过滤恶意用户
	$u_info = $user -> get_userinfo($uid);
	if($u_info){
		if($u_info['user_type'] != 'user'){
			return get_state_info(1061);
		}
	}else{
		return get_state_info(1063);
	}

	if(!isset($json_array['photo_id'])) return get_state_info(1054);
	$photo_id = $json_array['photo_id'];

	$photo_info = $album -> get_photo_info($photo_id);
	if($photo_info){
		$result = $album -> delete_user_photo($uid,$photo_id);
		if($result){
			$result = $photo -> delete_photo_file($photo_info['path_url']);
		}else{
			$fail_photo = $photo_id; //数据库删除失败的id数组
		}
	}else{
		$fail_photo = $photo_id; 
	}
	return get_state_info(1000);
}
//获取个人红档案
function get_user_profile($json_array){
	global $user,$userprofile,$base,$album;
	$uid = intval($json_array['uid']);
	if($uid<1) return get_state_info(1099);
	$u_info = $user -> get_userinfo($uid);
	if($u_info){
		if($u_info['user_type'] != 'user'){
			return get_state_info(1061);
		}
	}else{
		return get_state_info(1063);
	}
	$u_profile= $userprofile -> get_user_profile($uid);

	$user_info['uid'] = $uid;
	$user_info['name'] = $u_info['nickname'];

	if($u_profile['sex'] == 'm'){
		$sex_array['value'] = 'm';
		$sex_array['name'] = '男';
		$user_info['sex'] = $sex_array;
	}else{
		$sex_array['value'] = 'f';
		$sex_array['name'] = '女';
		$user_info['sex'] = $sex_array;
	}
	
	//获取所在地
	$province_id = $u_profile['province_id'];
	$city_id = $u_profile['city_id'];
	$district_id = $u_profile['district_id'];

	if($province_id){
		$user_province_name = $base ->  get_province_info($province_id);
		$user_province['id'] = $province_id;
		$user_province['name'] = $user_province_name['pname'];
		$user_info['province'] = $user_province;
	}else{
		$user_info['province'] = "";
	}
	if($city_id){
		$user_city_name = $base ->  get_city_info($city_id);
		$user_city['id'] = $city_id;
		$user_city['name'] = $user_city_name['cname'];
		$user_info['city'] = $user_city;	
	}else{
		$user_info['city'] = "";
	}
	if($district_id){
		$user_district_name = $base ->  get_district_info($district_id);
		$user_district['id'] = $district_id;
		$user_district['name'] = $user_district_name['dname'];
		$user_info['district'] = $user_district;
	}else{
		$user_info['district'] = "";
	}	
	//获取角色
	$e_role_list = $userprofile -> get_e_role_list_by_user($uid);
	if($e_role_list){
		foreach($e_role_list as $k=>$v){

			if($v['role_id'] == 0){
				$parent_id = $v['role_1_id'];
				$role_info = $userprofile -> get_role_info($parent_id);
				if($role_info){
					$role_array[$k]['name'] = $role_info['name'];
					$role_array[$k]['id'] = $v['id'];
					$role_array[$k]['parent_id'] = 0;
					$role_array[$k]['role_id'] = $parent_id;
				}else{
					$role_array = array();
				}
			}else{
				$role_info = $userprofile -> get_role_info($v['role_id']);
				if($role_info){
					$role_array[$k]['name'] = $role_info['name'];
					$role_array[$k]['id'] = $v['id'];
					$role_array[$k]['parent_id'] = $v['role_1_id'];
					$role_array[$k]['role_id'] = $v['role_id'];
				}else{
					$role_array = array();
				}	
			}
		}
		$user_info['role_list'] = $role_array;
	}else{
		$user_info['role_list'] = array();
	}
	/*
		$role_id = $u_profile['role'];
		$role_info = $userprofile -> get_role_info($role_id);
		$role_array['id'] = $role_id;
		$role_array['name'] = $role_info['name'];
	*/
	//获取红相册
	$album_list = $album -> get_photo_list_by_user($uid);
	if($album_list){
		foreach($album_list as $k=>$v){
			$photo_array[$k]['id'] = $v['id'];
			$photo_array[$k]['small_src'] = $v['server_url'].$v['path_url'].'!f150';
			$photo_array[$k]['large_src'] = $v['server_url'].$v['path_url'].'!800';
		}
		$user_info['photo_list'] = $photo_array;
	}else{
		$user_info['photo_list'] = array();
	}
	//获取用户状态
	$state = $u_profile['state'];
	$state_array[0]['value'] = 'busy';
	$state_array[0]['name'] = '忙碌';
	$state_array[1]['value'] = 'free';
	$state_array[1]['name'] = '空闲';
	$state_array[2]['value'] = 'other';
	$state_array[2]['name'] = '其他';

	if($state == 'busy'){
		$state_array[0]['select'] = 1;
		$state_array[1]['select'] = 0;
		$state_array[2]['select'] = 0;
	}else if($state == 'free'){
		$state_array[0]['select'] = 0;
		$state_array[1]['select'] = 1;
		$state_array[2]['select'] = 0;
	}else{
		$state_array[0]['select'] = 0;
		$state_array[1]['select'] = 0;
		$state_array[2]['select'] = 1;
	}
	$user_info['state'] = $state_array;
	//获取用户 的头像
	if($u_info['icon_server_url'] && $u_info['icon_path_url']){
		$face = $u_info['icon_server_url'].$u_info['icon_path_url'].'!f150';
	}else{
		$face = "";
	}
	$user_info['face'] = $face;
	//获取用户的年龄 身高 体重 星座 血型 毕业学校 专业 最高学历 毕业年份 所属机构 三围
	$user_info['age'] = $u_profile['age'];
	$user_info['height'] = $u_profile['height'];
	$user_info['weight'] = $u_profile['weight'];
	$user_info['star'] = $u_profile['star'];
	$user_info['blood'] = $u_profile['blood'];
	$user_info['school'] = $u_profile['school'];
	$user_info['specialty'] = $u_profile['specialty'];
	$user_info['degree'] = $u_profile['degree'];
	$user_info['finish_year'] = $u_profile['finish_year'];
	$user_info['in_org'] = $u_profile['in_org'];

	$user_info['bust'] = $u_profile['bust'];
	$user_info['waist'] = $u_profile['waist'];
	$user_info['hips'] = $u_profile['hips'];
	
	//获取用户的籍贯

	$native_province_id = $u_profile['native_province_id'];
	$native_city_id = $u_profile['native_city_id'];
	$native_district_id = $u_profile['native_district_id'];

	if($native_province_id){
		$user_native_province_name = $base ->  get_province_info($native_province_id);
		$user_native_province['id'] = $native_province_id;
		$user_native_province['name'] = $user_native_province_name['pname'];
		$user_info['native_province'] = $user_native_province;
	}else{
		$user_info['native_province'] = "";
	}
	if($native_city_id){
		$user_native_city_name = $base ->  get_city_info($native_city_id);
		$user_native_city['id'] = $native_city_id;
		$user_native_city['name'] = $user_native_city_name['cname'];
		$user_info['native_city'] = $user_native_city;	
	}else{
		$user_info['native_city'] = "";
	}
	if($native_district_id){
		$user_native_district_name = $base ->  get_district_info($native_district_id);
		$user_native_district['id'] = $native_district_id;
		$user_native_district['name'] = $user_native_district_name['dname'];
		$user_info['native_district'] = $user_native_district;	
	}else{
		$user_info['native_district'] = "";
	}
	//获取用户的标签
	$tag_array = $userprofile -> get_user_tag_by_uid($uid);
	if($tag_array == 1304){
		$user_info['tag'] = array();
	}else{
		foreach($tag_array as $k=>$v){
			$tag[$k]['id'] = $v['id'];
			$tag[$k]['name'] = $v['name'];
			$tag[$k]['parent_id'] = $v['parent_id'];
		}
		$user_info['tag'] = $tag;
	}

	$res = get_state_info(1000);
	
	$res['data'] = $user_info;
	return $res;
}

//更新机构红档案
function update_org_profile($json_array){
	global $user,$userprofile;
	$orgprofile = new orgprofile();
	$uid = intval($json_array['uid']);
	if($uid<1) return get_state_info(1099);
	//检查用户类型以及过滤恶意用户
	$o_info = $user -> get_userinfo($uid);
	if($o_info){
		if($o_info['user_type'] != 'org'){
			return get_state_info(1061);
		}
	}else{
		return get_state_info(1063);
	}
	
	//机构红名片部分
	$oinfo['uid'] = $uid;
	$oinfo['province_id'] = intval($json_array['province']);
	$oinfo['city_id'] = intval($json_array['city']);
	$oinfo['district_id'] = intval($json_array['district']);
	$oinfo['state'] = clear_gpq($json_array['state']);
	$oinfo['type'] = intval($json_array['type']);
	$oinfo['create_time'] = intval($json_array['create_time']);

	if($oinfo['create_time'] == 0 || !isset($oinfo['create_time']) || $oinfo['type']  == 0 || !isset($oinfo['type'] ) || $oinfo['province_id'] == 0 || $oinfo['city_id'] == 0 || !isset($oinfo['city_id']) || !isset($oinfo['province_id'])){
		return get_state_info(1113);	
	}
	if(!empty($oinfo['state'])){
		if(!in_array($uinfo['state'] , array('free','busy','other'))) return get_state_info(1113);
	}else{
		return get_state_info(1113);	
	}

	//红资料部分
	$oinfo['introduce'] = clear_gpq($json_array["introduce"]);
	$oinfo['production'] = clear_gpq($json_array["production"]);
	$oinfo['honor'] = clear_gpq($json_array["honor"]);

	if(strlen($oinfo['introduce'])>500) return get_state_info(1149);
	if(strlen($oinfo['production'])>500) return get_state_info(1150);
	if(strlen($oinfo['honor'])>500) return get_state_info(1151);

	//艺名单独更新
	$nickname = trim(clear_gpq($json_array['name']));
	if(empty($nickname) || preg_match("/[\&\<\>\'\"\\\?\=\$\%\^\*\@\/\#]/",$nickname) || strlen($nickname)>60){
		return get_state_info(1113);
	}
	$rrr = $user -> update_user_nickname($uid,$nickname);

	//红标签部分
	$org_tag_arr = $json_array['tag'];
	$r = _update_org_tags($uid, $org_tag_arr);

	//红艺人部分
	$org_artist_arr = $json_array['artist'];
	$rr = _update_artist($uid,$org_artist_arr);

	//更新机构资料
	$result = $orgprofile -> update_org_profile($uid,$oinfo);

	//if($result && $r == 1000 && $rr == 1000 && $rrr == 1000){
	if($result && $rrr == 1000){
		$json_arr['uid'] = $uid;
		$ret_arr = get_org_profile($json_arr);  //返回修改后的红资料
		return $ret_arr;
	}else{
		//return 5200;
		if(!$result){
			return get_state_info(5200);
		}else if($r != 1000){
			//return get_state_info(5201);
			return get_state_info($r);
		}else if($rr != 1000){
			//return get_state_info(5202);
			return get_state_info($rr);
		}else if($rrr != 1000){
			return get_state_info($rrr);
		}
		
	} 	
}
//红艺人更新
function _update_artist($uid,$artist_arr){
	global $orgprofile;
	//红艺人数据
	if(is_array($artist_arr) && !empty($artist_arr)){
		$state_code = $orgprofile -> delete_artist($uid);
		if($state_code == 1000){	
			foreach ($artist_arr as $value) {
				$value['name'] = strip_tags(clear_gpq($value['name']));
				$value['info'] = strip_tags(clear_gpq($value['info']));

				if($value['name'] != ''){
					$state_code = $orgprofile -> add_artist($uid,$value);
					if($state_code != 1000){
						return $state_code;      //1147
					}
				}
			}
			return 1000;
		}else{
			return $state_code;   // 1146
		}		
	}else{
		$state_code = $orgprofile -> delete_artist($uid);	#全部删除
		return $state_code;		// 1146  1000
	}
}
//标签更新操作
function _update_org_tags($uid,$tag_arr) {
	global $orgprofile;

	//标签数据
	if(is_array($tag_arr)){	
		foreach($tag_arr as $k=>$v){
			if($v['parent_id'] == -1){
				$self_tag_array[] = $v['name']; 
			}else{
				$sys_tag_array[] = $v['id'];
			}
		}	
	}

	//标签操作  统计数目 删除原来的标签
	$user_tag = $orgprofile -> get_org_tag_by_uid($uid);
	if($user_tag == 1315){
		//错误处理 1305 //标签内容读取失败！
		return 1315;
	}else{
		//统计标签的个数
		if(count($self_tag_array)>5 || count($sys_tag_array)>5){
			return 1310;
		}	
		
		if($user_tag != 1314){
			$state_code = $orgprofile -> delete_org_tag($uid);
			if($state_code != 1000){
				//错误处理  删除失败 1316
				return 1316;
			}	
		}
		
	}

	//新添加的自定义标签 操作
	if(!empty($self_tag_array) && isset($self_tag_array)){
		$self_tag_array = array_unique($self_tag_array);   //去除重复值
		$class = 2;
		foreach($self_tag_array as $k => $v){
			$result = $userprofile -> check_tag_exits($class, $v);
			if($result){
				$self_tag_id_arr[] = $result['id'];
			}else{
				$self_tag_id_arr[] = $orgprofile -> add_org_self_tag($class, $v);
			}	
		}
	}
	//组合自定义标签 与 系统标签 更新到e_tag表
	if(empty($sys_tag_array) || !isset($sys_tag_array)){							//用户选择的系统标签
		if(empty($self_tag_id_arr) || !isset($self_tag_id_arr)){
			return 1000; 											//系统标签和自定义标签都没有   1303
		}else{
			$person_tag = $self_tag_id_arr;								//只有自定义标签
		}
	}else{
		if(empty($self_tag_id_arr) || !isset($self_tag_id_arr)){
			$person_tag = $sys_tag_array; 								//只选了系统标签
		}else{
			$person_tag = array_merge($sys_tag_array, $self_tag_id_arr);				//系统标签和自定义标签都选
		}
	}
	//将用户标签映射到用户标签关联表
	foreach($person_tag as $k=>$v){
		//将用户标签映射到用户标签表
		$state_code = $orgprofile -> add_org_tag($uid, $v);
		if($state_code == 1302){
			return 1312;
		}
	}
	//1000  操作成功 返回标识
	return 1000;
}
//获取机构红档案
function get_org_profile($json_array){
	global $user,$orgprofile,$base,$orgprofile,$album;
	$oid = intval($json_array['uid']);
	if($oid<1) return get_state_info(1099);
	$o_info = $user -> get_userinfo($oid);
	if($o_info){
		if($o_info['user_type'] != 'org'){
			return get_state_info(1061);
		}
	}else{
		return get_state_info(1063);
	}

	$o_profile= $orgprofile -> get_org_profile($oid);

	$org_info['uid'] = $oid;
	$org_info['name'] = $o_info['nickname'];
	
	//获取所在地
	$province_id = $o_profile['province_id'];
	$city_id = $o_profile['city_id'];
	$district_id = $o_profile['district_id'];

	if($province_id){
		$org_province_name = $base ->  get_province_info($province_id);	
		$org_province['id'] = $province_id;
		$org_province['name'] = $org_province_name['pname'];
		$org_info['province'] = $org_province;
	}else{
		$org_info['province'] = "";
	}
	if($city_id){
		$org_city_name = $base ->  get_city_info($city_id);	
		$org_city['id'] = $city_id;
		$org_city['name'] = $org_city_name['cname'];
		$org_info['city'] = $org_city;	
	}else{
		$org_info['city'] = "";
	}
	if($district_id){
		$org_district_name = $base ->  get_district_info($district_id);	
		$org_district['id'] = $district_id;
		$org_district['name'] = $org_district_name['dname'];
		$org_info['district'] = $org_district;
	}else{
		$org_info['district'] = "";
	}
	//获取机构是否的法人机构
	$legal_person = $o_profile['legal_person'];
	$legal_person_array[0]['value'] = 'yes';
	$legal_person_array[0]['name'] = '是';
	$legal_person_array[1]['value'] = 'no';
	$legal_person_array[1]['name'] = '否';

	if($legal_person = 'yes'){
		$legal_person_array[0]['select'] = 1;
		$legal_person_array[1]['select'] = 0;
	}else{
		$legal_person_array[0]['select'] = 0;
		$legal_person_array[1]['select'] = 1;
	}
	$org_info['legal_person'] = $legal_person_array;
	//获取机构类型
	$type_id = $o_profile['type'];
	$type_info = $base -> get_org_type_info($type_id);
	$type_array['id'] = $type_id;
	$type_array['name'] = $type_info['name'];
	$org_info['type'] = $type_array;
	//获取用户状态
	$state = $o_profile['state'];
	$state_array[0]['value'] = 'busy';
	$state_array[0]['name'] = '忙碌';
	$state_array[1]['value'] = 'free';
	$state_array[1]['name'] = '空闲';
	$state_array[2]['value'] = 'other';
	$state_array[2]['name'] = '其他';

	if($state = 'busy'){
		$state_array[0]['select'] = 1;
		$state_array[1]['select'] = 0;
		$state_array[2]['select'] = 0;
	}else if($state = 'free'){
		$state_array[0]['select'] = 0;
		$state_array[1]['select'] = 1;
		$state_array[2]['select'] = 0;
	}else{
		$state_array[0]['select'] = 0;
		$state_array[1]['select'] = 0;
		$state_array[2]['select'] = 1;
	}
	$org_info['state'] = $state_array;
	//获取红相册
	$album_list = $album -> get_photo_list_by_user($oid);
	if($album_list){
		foreach($album_list as $k=>$v){
			$photo_array[$k]['id'] = $v['id'];
			$photo_array[$k]['small_src'] = $v['server_url'].$v['path_url'].'!f150';
			$photo_array[$k]['large_src'] = $v['server_url'].$v['path_url'].'!800';
		}
		$org_info['photo_list'] = $photo_array;
	}else{
		$org_info['photo_list'] = array();
	}
	//获取机构介绍 作品 荣誉
	$org_info['introduce'] = $o_profile['introduce'];
	$org_info['production'] = $o_profile['production'];
	$org_info['honor'] = $o_profile['honor'];
	//获取红艺人
	$artist_array = $orgprofile -> get_artist($oid);
	if($artist_array){
		foreach ($artist_array as $key => $value) {
			$artist[$key]['name'] = $value['name'];
			$artist[$key]['description'] = $value['description'];	
		}
		$org_info['artist'] = $artist;
	}else{
		$org_info['artist'] = array();
	}
	//获取用户 的头像
	if($o_info['icon_server_url'] && $o_info['icon_path_url']){
		$face = $o_info['icon_server_url'].$o_info['icon_path_url'].'!f150';
	}else{
		$face = "";
	}
	$org_info['face'] = $face;
	//获取用户的成立时间 
	$org_info['create_time'] = $o_profile['create_time'];
	
	
	//获取用户的标签
	$tag_array = $orgprofile -> get_org_tag_by_uid($oid);
	if($tag_array == 1314){
		$org_info['tag'] = array();
	}else{
		foreach($tag_array as $k=>$v){
			$tag[$k]['id'] = $v['id'];
			$tag[$k]['name'] = $v['name'];
		}
		$org_info['tag'] = $tag;
	}

	$res = get_state_info(1000);
		
	$res['data'] = $org_info;
	return $res;
}
//分别获取用户选项的值 
// 获取红名片信息
function get_user_card_data(){
	//$ret_arr['address'] = _get_address_array();
	// $ret_arr['sex'] = _get_sex_list();
	// $ret_arr['role'] = get_role_list();
	$ret= _get_state_list();

	$ret_arr = get_state_info(1000);
		
	$ret_arr['data'] = $ret;
	return $ret_arr;
}
//获取红档案信息
function get_user_profile_data(){
	//$ret_arr['address'] = _get_address_array();
	$ret_arr['sex'] = _get_sex_list();
	$ret_arr['role'] = get_role_list();
	$ret_arr['state'] = _get_state_list();	
	$ret_arr['age'] = _get_age_list();
	$ret_arr['height'] = _get_height_list();
	$ret_arr['weight'] = _get_weight_list();
	$ret_arr['bust'] = _get_bust_list();
	$ret_arr['waist'] = _get_waist_list();
	$ret_arr['hips'] = _get_hips_list();
	$ret_arr['star'] = _get_star_list();
	$ret_arr['blood'] = _get_blood_list();
	$ret_arr['finish_year'] = _get_finish_year_list();
	$ret_arr['degree'] = _get_degree_list();

	$state_code = get_state_info(1000);
	foreach($state_code as $k=>$v){
		$res[$k] = $v;
	}			
	$res['data'] = $ret_arr;
	return $res;
}
// 获取机构红名片信息
function get_org_card_data(){
	//$ret_arr['address'] = _get_address_array();
	$ret['type'] = get_org_type_list();
	// $ret_arr['create_time'] = _get_create_time();
	$ret['legal_person'] = _get_legal_person();
	$ret['state'] = _get_state_list();
	// $state_code = get_state_info(1000);
	// foreach($state_code as $k=>$v){
	// 	$res[$k] = $v;
	// }			
	// $res['data'] = $ret_arr;
	// return $res;
	
	//$ret= _get_state_list();

	$ret_arr = get_state_info(1000);
		
	$ret_arr['data'] = $ret;
	return $ret_arr;
}

//获取城市信息
function get_address_array($json_array){	
	global $base;
	
	if(!empty($json_array["cid"])){
		$cid = intval($json_array["cid"]);
		$district_list = $base ->  get_district_list_by_city($cid);
		foreach($district_list as $key => $item){
			unset($district_info);
			$district_info["id"] = $item["id"];
			$district_info["name"] = $item["dname"];
			$district_info["parent_id"] = $item["cid"];
			$district_array[] = $district_info;
		}
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $district_array;
		return $ret_arr;
	}else{
		if(empty($json_array["pid"])){
			$province_list = $base -> get_province_list();
			foreach($province_list as $key => $item){
				unset($province_info);
				$province_info["id"] = $item["id"];
				$province_info["name"] = $item["pname"];
				$province_info["parent_id"] = 0;
				$province_array[] = $province_info;
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data'] = $province_array;
			return $ret_arr;
		}else{
			$pid = intval($json_array["pid"]);
			$city_list = $base -> get_city_list_by_province($pid);
			foreach($city_list as $key => $item){
				unset($city_info);
				$city_info['id'] = $item["id"];
				$city_info['name'] = $item["cname"];
				$city_info['parent_id'] = $item["pid"];
				$city_array[] = $city_info;
			}
			$ret_arr  = get_state_info(1000);
			$ret_arr['data'] = $city_array;
			return $ret_arr;
		}
	}	
}


//获取角色列表
function get_role_list(){
	global $userprofile;
	$role_list = $userprofile -> get_role_list();
	foreach($role_list as $role_info){
		unset($res_role_info);
		$res_role_info["id"] = $role_info["id"];
		$res_role_info["name"] = $role_info["name"];
		$res_role_info["parent_id"] = $role_info["parent_id"];
		$child_role_list = $role_info['child'];
		unset($res_child_role_list);
		if(empty($child_role_list)){
			$res_child_role_list = array();
		}else{
			foreach($child_role_list  as $child_role_info){
				$res_child_role_list[] = $child_role_info;
			}
		}
		
		$res_role_info['child'] = $res_child_role_list;

		$res_role_list[] = $res_role_info;
	}
	$ret_arr = get_state_info(1000);
	$ret_arr['data'] = $res_role_list;
	return $ret_arr;
}

//读取机构类型列表
function get_org_type_list(){
	global $base;
	return $base -> get_org_type_list();
}
//获取性别
function _get_sex_list(){
	global $COMMON_CONFIG;
	$all_val = $COMMON_CONFIG["SEX"] ;
	foreach ($all_val as $key => $value) {
		$r['name'] = $value;
		$r['value'] = $key;
		$res[] = $r;
	}
	//$res = array(array('name'=>'男','value'=>'m'),array('name'=>'女','value'=>'f'));
	return $res;
}
//获取机构和个人状态
function _get_state_list(){
	global $COMMON_CONFIG;
	$all_val = $COMMON_CONFIG["STATE"] ;
	foreach ($all_val as $key => $value) {
		$r['name'] = $value;
		$r['value'] = $key;
		$res[] = $r;
	}
	//$res = array(array('name'=>'空闲','value'=>'free'),array('name'=>'忙碌','value'=>'busy'),array('name'=>'其它','value'=>'other'));	
	return $res;
}
//获取年龄
function _get_age_list(){
	global $COMMON_CONFIG;
	$min = $COMMON_CONFIG["AGE"]["RANGE"] ['min'];
	$max = $COMMON_CONFIG["AGE"]["RANGE"]['max'];
	$res = array('max' => "{$max}",'min' => "{$min}");
	return $res;	
}
//获取身高
function _get_height_list(){
	global $COMMON_CONFIG;
	$min = $COMMON_CONFIG["HEIGHT"]["RANGE"] ['min'];
	$max = $COMMON_CONFIG["HEIGHT"]["RANGE"]['max'];
	$res = array('max' => "{$max}",'min' => "{$min}");
	return $res;	
}
//获取体重
function _get_weight_list(){
	global $COMMON_CONFIG;
	$min = $COMMON_CONFIG["WEIGHT"]["RANGE"] ['min'];
	$max = $COMMON_CONFIG["WEIGHT"]["RANGE"]['max'];
	$res = array('max' => "{$max}",'min' => "{$min}");
	return $res;	
}
//获取胸围
function _get_bust_list(){
	global $COMMON_CONFIG;
	$min = $COMMON_CONFIG["BUST"]["RANGE"] ['min'];
	$max = $COMMON_CONFIG["BUST"]["RANGE"]['max'];
	$res = array('max' => "{$max}",'min' => "{$min}");
	return $res;	
}
//获取腰围
function _get_waist_list(){
	global $COMMON_CONFIG;
	$min = $COMMON_CONFIG["WAIST"]["RANGE"] ['min'];
	$max = $COMMON_CONFIG["WAIST"]["RANGE"]['max'];
	$res = array('max' => "{$max}",'min' => "{$min}");
	return $res;	
}
//获取臀围
function _get_hips_list(){
	global $COMMON_CONFIG;
	$min = $COMMON_CONFIG["HIPS"]["RANGE"]['min'];
	$max = $COMMON_CONFIG["HIPS"]["RANGE"]['max'];
	$res = array('max' => "{$max}",'min' => "{$min}");
	
	return $res;	
}
//获取星座
function _get_star_list(){
	global $COMMON_CONFIG;
	$all_val = $COMMON_CONFIG["STAR"] ;
	foreach ($all_val as $key => $value) {
		$r['name'] = $key;
		$r['value'] = $value;
		$res[] = $r;
	}

	// $res = array(array('name'=>'白羊座','value'=>'白羊座'),
	// 		array('name'=>'金牛座','value'=>'金牛座'),
	// 		array('name'=>'双子座','value'=>'双子座'),
	// 		array('name'=>'巨蟹座','value'=>'巨蟹座'),
	// 		array('name'=>'狮子座','value'=>'狮子座'),
	// 		array('name'=>'处女座','value'=>'处女座'),
	// 		array('name'=>'天秤座','value'=>'天秤座'),
	// 		array('name'=>'天蝎座','value'=>'天蝎座'),
	// 		array('name'=>'射手座','value'=>'射手座'),
	// 		array('name'=>'魔蝎座','value'=>'魔蝎座'),
	// 		array('name'=>'水瓶座','value'=>'水瓶座'),
	// 		array('name'=>'双鱼座','value'=>'双鱼座'));
	return $res;	
}
//获取血型
function _get_blood_list(){
	global $COMMON_CONFIG;
	$all_val = $COMMON_CONFIG["BLOOD"] ;
	foreach ($all_val as $key => $value) {
		$r['name'] = $key;
		$r['value'] = $value;
		$res[] = $r;
	}
	// $res = array(array('name'=>'A型','value'=>'A'),
	// 		array('name'=>'B型','value'=>'B'),
	// 		array('name'=>'AB型','value'=>'AB'),		
	// 		array('name'=>'O型','value'=>'O'));	
	return $res;	
}
//获取毕业年份
function _get_finish_year_list(){
	global $COMMON_CONFIG;
	$min = $COMMON_CONFIG["FINISH_YEAR"]['min'];
	$max = $COMMON_CONFIG["FINISH_YEAR"]['max'];
	//$year = date('Y');
	$res = array('max' => "{$max}",'min' => "{$min}");
	return $res;	
}
//获取学历
function _get_degree_list(){
	global $COMMON_CONFIG;
	$all_val = $COMMON_CONFIG["DEGREE"] ;
	foreach ($all_val as $key => $value) {
		$r['name'] = $key;
		$r['value'] = $value;
		$res[] = $r;
	}
	// $res = array(array('name'=>'博士','value'=>'博士'),
	// 		array('name'=>'硕士','value'=>'硕士'),
	// 		array('name'=>'本科','value'=>'本科'),
	// 		array('name'=>'高中','value'=>'高中'),
	// 		array('name'=>'初中','value'=>'初中'),
	// 		array('name'=>'小学','value'=>'小学'),
	// 		array('name'=>'其它','value'=>'其它'));	
	return $res;	
}
//获取机构创建时间
function _get_create_time(){
	global $COMMON_CONFIG;
	$min = $COMMON_CONFIG["CREATE_YEAR"]["RANGE"]['min'];
	$max = $COMMON_CONFIG["CREATE_YEAR"]["RANGE"]['max'];
	$res = array('max' => "{$max}",'min' => "{$min}");

	return $res;
}
//获取是否法人
function _get_legal_person(){
	$res = array(array('name'=>'是','value'=>'yes'),
			array('name'=>'否','value'=>'no'));	
	return $res;
}

// 通过状态码获得相应返回信息
function get_state_info($state_code){
	global $STATE_LIST;
	$ret_arr['state_code'] = $state_code;
	$ret_arr['description'] = $STATE_LIST[$state_code];
	$ret_arr['time_stamp'] = time();
	return $ret_arr;
}
// 获取用户信息
function _filter_user_info($user_info){
	$res['uid'] = $user_info['id'];
	$res['user_type'] = $user_info['user_type'];
	$res['nickname'] = $user_info['nickname'];
	$res['email'] = $user_info['email'];
	$res['email_status'] = $user_info['email_status'];
	$res['mobile'] = $user_info['mobile'];
	$res['mobile_status'] = $user_info['mobile_status'];
	$res['icon_server_url'] = $user_info['icon_server_url'];
	$res['icon_path_url'] = $user_info['icon_path_url'];
	$res['business_num_status'] = $user_info['business_card_status'];
	$res['identity_card_status'] = $user_info['identity_card_status'];
	$res['level'] = $user_info['level'];
	$res['data_percent'] = $user_info['data_percent'];
	return $res;
}
//验证手机用户是否登录  uid  app_token
function _check_login($uid,$app_token){
	global $user;
	//判断用户id
	if(!isset($uid) || empty($uid)){
		echo json_encode(get_state_info(1222));#缺省id 请重新登陆
		exit;
	}
	$uid = intval($uid);
	//判断app_token---
	if(!isset($app_token) || empty($app_token)){
		echo json_encode(get_state_info(1222));#缺省app_token 请重新登陆
		exit;
	}
	$app_token = clear_gpq($app_token);
	if(!$user -> is_login($uid,$app_token ) ){
		echo json_encode(get_state_info(1222));#账号身份不匹配，请重新登陆
		exit;
	}
}   		
?>