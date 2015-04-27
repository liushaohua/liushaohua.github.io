<?php
require_once("common_inc.php");
file_put_contents("/tmp/mobile_post_log.log",date("Y-m-d H:i:s").",json_array:".json_encode($_POST)."\n",FILE_APPEND);
$action = clear_gpq(@$_POST["action"]);
//var_dump($action);
switch($action){
		//第一批接口
	case "login":
		echo json_encode(val_to_str(account_login()));
		break;
	case "unsubscribe_account"://注销登录
		echo json_encode(val_to_str(account_unsubscribe_account()));
		break;
	case "send_mobile_verify":
		echo json_encode(val_to_str(account_send_mobile_verify()));
		break;
	case "set_user_card":
		echo json_encode(val_to_str(account_set_user_card()));
		break;
	case "forget_password_email":#邮箱找回密码  给指定邮箱发邮件
		echo json_encode(val_to_str(account_forget_password_email()));
		break;
	case "send_mobile_verify_forget":#1发送安全码
		echo json_encode(val_to_str(account_forget_send_mobile_verify()));
		break;
	case "verify_code_forget":#2验证安全码
		echo json_encode(val_to_str(account_forget_verify_code()));
		break;
	case "set_new_password":#3找回密码 再验证安全码 设置新密码
		echo json_encode(val_to_str(account_forget_set_new_password()));
		break;
	case "verify_code":
		echo json_encode(val_to_str(account_verify_code()));
		break;
	case "regist":
		echo json_encode(val_to_str(account_regist()));
		break;
		//第二批接口
	case "bind_email":		//绑定邮箱
		echo json_encode(val_to_str(home_bind_email()));
		break;
	case "resend_verify_email":		//重发验证邮件
		echo json_encode(val_to_str(home_resend_verify_email()));
		break;
	case "change_password":		//修改密码	
		echo json_encode(val_to_str(home_change_password()));
		break;			
	case "check_update":		//检查更新app版本
		echo json_encode(val_to_str(home_check_update()));
		break;
	case "get_mine_user_info":		//获取我的页面的用户数据
		echo json_encode(val_to_str(home_get_mine_user_info()));
		break;
	case "get_my_apply":		//获取我的报名列表
		echo json_encode(val_to_str(home_get_my_apply()));
		break;
	case "get_my_recruit":		//获取我发布的招募列表
		echo json_encode(val_to_str(home_get_my_recruit()));
		break;
	case "get_my_invitation":	//获取我收到的邀约列表
		echo json_encode(val_to_str(home_get_my_invitation()));
		break;
	case "get_invitation_info":	//获取我收到的邀约列表
		echo json_encode(val_to_str(home_get_invitation_info()));
		break;
	case "get_my_favorites_reds":	//获取我收藏的红人
		echo json_encode(val_to_str(home_get_my_favorites_reds()));
		break;
	case "get_my_favorites_organization":	//获取我收藏的机构
		echo json_encode(val_to_str(home_get_my_favorites_organization()));
		break;
	case "get_my_favorites_recruit":		////获取我收藏的招募
		echo json_encode(val_to_str(home_get_my_favorites_recruit()));
		break;
		//第三批接口
	case "get_user_profile":		//获取个人用户编辑页的资料
		echo json_encode(val_to_str(profile_get_user_profile()));
		break;
	case "get_specify_user_profile":		//获取个人用户展示页的资料
		echo json_encode(val_to_str(profile_get_specify_user_profile()));
		break;
	case "delete_two_service":	//删除指定的二级服务
		echo json_encode(val_to_str(profile_delete_two_service()));
		break;
	case "set_three_service":	//设置指定二级服务的三级服务
		echo json_encode(val_to_str(profile_set_three_service()));
		break;
	case "get_two_services":	//获取所有的二级服务
		echo json_encode(val_to_str(profile_get_two_services()));
		break;
	case "get_three_services":	//获取指定二级服务下的三级服务
		echo json_encode(val_to_str(profile_get_three_services()));
		break;		
	case "get_org_profile":		//获取机构用户编辑页的资料
		echo json_encode(val_to_str(profile_get_org_profile()));
		break;
	case "get_specify_org_profile":		//获取机构用户展示页的资料
		echo json_encode(val_to_str(profile_get_specify_org_profile()));
		break;
	case "get_specify_user_recruit": //获取指定用户发布的招募列表
		echo json_encode(val_to_str(profile_get_specify_user_recruit()));
		break;
	case "set_roles":		//0 保存选择的二级角色
		echo json_encode(val_to_str(profile_set_roles()));
		break;
	case "get_roles":		//1 获取角色字典
		echo json_encode(val_to_str(profile_get_roles()));
		break;
	case "get_provinces":		//2 获取省份字典
		echo json_encode(val_to_str(profile_get_provinces()));
		break;
	case "get_cities":		//3 根据省份获取市
		echo json_encode(val_to_str(profile_get_cities()));
		break;
	case "get_districts":		//4 根据市获取区
		echo json_encode(val_to_str(profile_get_districts()));
		break;
	case "set_icon_img":		//5 设置用户的头像
		echo json_encode(val_to_str(profile_set_icon_img()));
		break;
	case "get_bwh_range":		// 获取三围的范围
		echo json_encode(val_to_str(profile_get_bwh_range()));
		break;
	case "get_age_constellation_range":		// 获取年龄和星座的范围
		echo json_encode(val_to_str(profile_get_age_constellation_range()));
		break;	
	case "send_mobile_verify_verification":		//发送手机验证码
		echo json_encode(val_to_str(home_send_mobile_verify_verification()));
		break;	
	case "get_height_weight_range":		//获取身高和体重的范围
		echo json_encode(val_to_str(get_height_weight_range()));
		break;	
	case "set_org_introduction":		//修改机构简介
		echo json_encode(val_to_str(home_set_org_introduction()));
		break;	
	case "set_school":		//修改毕业院校
		echo json_encode(val_to_str(home_set_school()));
		break;
	case "verify_code_verification":	//验证认证手机验证码
		echo json_encode(val_to_str(home_verify_code_verification()));
		break;		
	case "set_org_honor":	//修改机构的荣誉
		echo json_encode(val_to_str(home_set_org_honor()));
		break;	
	case "set_org_showreel":	//修改机构的荣誉
		echo json_encode(val_to_str(home_set_org_showreel()));
		break;
	case "set_birthplace":	//修改籍贯
		echo json_encode(val_to_str(home_set_birthplace()));
		break;
	case "set_nickname":	//修改昵称
		echo json_encode(val_to_str(home_set_nickname()));
		break;	
	case "set_constellation_age":	//修改年龄和星座
		echo json_encode(val_to_str(home_set_constellation_age()));
		break;
	case "set_bwh":	   //修改三围
		echo json_encode(val_to_str(home_set_bwh()));
		break;	
	case "set_height_weight":	   //修改身高和体重
		echo json_encode(val_to_str(home_set_height_weight()));
		break;
	case "set_location":	   //修改所在地
		echo json_encode(val_to_str(home_set_location()));
		break;
	case "set_contact_qq":	   //修改QQ
		echo json_encode(val_to_str(profile_set_contact_qq()));
		break;
	case "set_contact_mobile":	   //修改mobile
		echo json_encode(val_to_str(profile_set_contact_mobile()));
		break;
	case "set_contact_weixin":	   //修改weixin
		echo json_encode(val_to_str(profile_set_contact_weixin()));
		break;
	case "set_contact_email":	   //修改email
		echo json_encode(val_to_str(profile_set_contact_email()));
		break;
	case "set_favorite_red":	   //收藏红人
		echo json_encode(val_to_str(profile_set_favorite_reds()));
		break;
	case "set_favorite_org":	   //收藏机构
		echo json_encode(val_to_str(profile_set_favorite_org()));
		break;
	case "set_favorite_recruit":	   //收藏招募
		echo json_encode(val_to_str(profile_set_favorite_recruit()));
		break;
	case "delete_favorite_red":	   //删除收藏红人
		echo json_encode(val_to_str(profile_delete_favorite_reds()));
		break;
	case "delete_favorite_org":	   //删除收藏机构
		echo json_encode(val_to_str(profile_delete_favorite_org()));
		break;
	case "delete_favorite_recruit":	   //删除收藏招募
		echo json_encode(val_to_str(profile_delete_favorite_recruit()));
		break;
	case "verify_identity_card":	   //验证身份证
		echo json_encode(val_to_str(profile_verify_identity_card()));
		break;
	case "add_photo":	   //增加照片
		echo json_encode(val_to_str(profile_add_photo()));
		break;		
	case "delete_photo":	   //删除照片
		echo json_encode(val_to_str(profile_delete_photo()));
		break;
		//第四批接口
	case "set_user_contact":	   //修改联系方式
		echo json_encode(val_to_str(profile_set_user_contact()));
		break;		
	case "get_recruit_service_info";	//获取用户报名的二级服务的信息
		echo json_encode(val_to_str(mywork_get_recruit_service_info()));
		break;
	case "get_apply_user_list";		//获取招募里，某个二级服务下，已经报名的红人列表
		echo json_encode(val_to_str(mywork_get_apply_user_list()));
		break;
	case "get_invitation_user_list";		//获取招募里，某个二级服务下，已经邀约的红人列表
		echo json_encode(val_to_str(mywork_get_invite_user_list()));
		break;
	case "apply_recruit_service";	//用户报名招募中的某一个二级服务
		echo json_encode(val_to_str(mywork_apply_recruit_service()));
		break;	
	case "set_invitation_communication";	//修改邀约的沟通结果
		echo json_encode(val_to_str(mywork_set_invitation_communication()));
		break;
	case "set_invitation_comment";	//修改邀约的备注
		echo json_encode(val_to_str(mywork_set_invitation_comment()));
		break;
	case "set_apply_communication";	//修改报名的沟通结果
		echo json_encode(val_to_str(mywork_set_apply_communication()));
		break;			
	case "set_apply_comment";	//修改报名的备注
		echo json_encode(val_to_str(mywork_set_apply_comment()));
		break;
	case "get_recruit_profile_own";	//招募展示(自己查看)--
		echo json_encode(val_to_str(mywork_get_recruit_profile_own()));
		break;
	case "get_recruit_profile";	//招募展示--
		echo json_encode(val_to_str(mywork_get_recruit_profile()));
		break;
	case "get_one_service";	//获取一级服务列表
		echo json_encode(val_to_str(mywork_get_one_service()));
		break;
	case "get_two_service";	//获取指定一级服务下的二级服务列表
		echo json_encode(val_to_str(mywork_get_two_service()));
		break;
	case "get_hot_search";	//获取搜索热词
		echo json_encode(val_to_str(mywork_get_hot_search()));
		break;
	//第五批接口
	case "category_reds":	   //二级服务下的红人列表
		echo json_encode(val_to_str(find_category_reds()));
		break;
	case "category_orgs":	   //二级服务下的机构列表
		echo json_encode(val_to_str(find_category_orgs()));
		break;
	case "category_recruit":	   //二级服务下的招募列表
		echo json_encode(val_to_str(find_category_recruit()));
		break;
	case "search_reds":	   //搜索红人列表
		echo json_encode(val_to_str(search_reds()));
		break;
	case "search_orgs":	   //搜索机构列表
		echo json_encode(val_to_str(search_orgs()));
		break;
	case "search_recruit":	   //搜索招募列表
		echo json_encode(val_to_str(search_recruit()));
		break;
	//新增接口
	case "set_org_card":	   //修改机构红名片
		echo json_encode(val_to_str(account_set_org_card()));
		break;	
	case "get_org_type":	   //获取所有的机构类型
		echo json_encode(val_to_str(profile_get_org_type()));
		break;	
	case "get_invitation_service":	   //获取邀约时的招募下的所有二级服务列表
		echo json_encode(val_to_str(mywork_get_invitation_service()));
		break;
	case "get_invitation_three_service":	   //获取邀约时的二级服务下的所有三级服务列表。
		echo json_encode(val_to_str(mywork_get_invitation_three_service()));
		break;		
	case "get_instituted_date_and_org_type_range":		//获取机构创立时间和机构类型的范围
		echo json_encode(val_to_str(mywork_get_instituted_date_and_org_type_range()));
		break;
	case "get_invitation_recruit":		//获取邀约下的所有招募
		echo json_encode(val_to_str(mywork_get_invitation_recruit()));
		break;
	case "invitation_someone";	
		echo json_encode(val_to_str(mywork_invitation_someone())); 
		break;
		//第六批接口
	// case "get_main_info":
	// 	echo json_encode(val_to_str(index_get_main_info()));
	// 	break; 
	case "set_instituted_date_and_org_type":		//修改机构创立时间和机构类型。
		echo json_encode(val_to_str(mywork_set_instituted_date_and_org_type()));
		break;	
	default:
		echo json_encode(val_to_str(get_state_info(1099)));
		break;
}
// 将返回结果的value值全部转化成string类型  iam sb
function val_to_str($data){
	if(is_array($data)){
		foreach($data as &$value){
			$value = val_to_str($value);
		}
		return $data;
	}else{
		return strval($data);
	}
}
function get_state_info($state_code){
	global $STATE_LIST;
	if(array_key_exists($state_code,$STATE_LIST)){
		$ret_arr['state_code'] = $state_code;
		$ret_arr['description'] = $STATE_LIST[$state_code];
		$ret_arr['time_stamp'] = time();
		return $ret_arr;
	}else{
		return false;
	}	
}
//验证手机用户是否登录  uid  app_token
function _check_login($uid,$app_token){
	$user = new user();
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
	if(!$user -> is_login($uid,$app_token)){
		echo json_encode(get_state_info(1222));#账号身份不匹配，请重新登陆
		exit;
	}
}
