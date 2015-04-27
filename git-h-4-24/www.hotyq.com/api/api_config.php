<?php

// 手机用户注册验证码获取
$action_key = "reg_get_mobile_check_code";
$action_list[$action_key]["name"] = "注册获取手机验证码";
$action_list[$action_key]["param_arr"][] = array("field"=>"mobile", "name"=>"手机", "type"=>"string");

// 手机用户注册
$action_key = "add_mobile_user";
$action_list[$action_key]["name"] = "手机用户注册";
$action_list[$action_key]["param_arr"][] = array("field"=>"user_type", "name"=>"用户身份", "type"=>"enum","value"=>array("user","org"));
$action_list[$action_key]["param_arr"][] = array("field"=>"account", "name"=>"手机", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"password", "name"=>"密码", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"check_code", "name"=>"验证码", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_type", "name"=>"手机平台类型", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_id", "name"=>"移动设备硬件id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_os", "name"=>"应用操作系统", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_os_ver", "name"=>"应用操作系统版本", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_ui_os", "name"=>"定制系统操作系统", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_ui_os_ver", "name"=>"定制系统操作系统版本号", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_name", "name"=>"移动应用程序名", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_ver", "name"=>"移动应用程序版本号", "type"=>"string", "default"=>"1.0");

// 用户登陆
$action_key = "user_login";
$action_list[$action_key]["name"] = "移动端用户登录";
$action_list[$action_key]["param_arr"][] = array("field"=>"login_type", "name"=>"账号类型", "type"=>"enum","value"=>array("email","mobile"));
$action_list[$action_key]["param_arr"][] = array("field"=>"account", "name"=>"登录账户", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"password", "name"=>"登录密码", "type"=>"string");

//忘记密码 包含以下12345多个接口
//1
$action_key = "forget1_get_check_code";
$action_list[$action_key]["name"] = "发送图片验证码地址";
$action_list[$action_key]["param_arr"][] = array("field"=>"app_id", "name"=>"手机硬件id", "type"=>"string");
//2
$action_key = "forget2_get_user_info";
$action_list[$action_key]["name"] = "验证成功后返回用户信息";
$action_list[$action_key]["param_arr"][] = array("field"=>"account", "name"=>"账号", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_id", "name"=>"手机硬件id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"identify_code", "name"=>"图片验证码", "type"=>"string");
//3
$action_key = "forget3_send_forget_code";
$action_list[$action_key]["name"] = "发送验证码";
$action_list[$action_key]["param_arr"][] = array("field"=>"account_type", "name"=>"账号类型", "type"=>"enum","value"=>array("email","mobile"));
$action_list[$action_key]["param_arr"][] = array("field"=>"account", "name"=>"账号名", "type"=>"string");
//4
$action_key = "forget4_check_forget_code";
$action_list[$action_key]["name"] = "验证验证码";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"forget_code", "name"=>"验证码", "type"=>"string");
//5
$action_key = "forget5_reset_password";
$action_list[$action_key]["name"] = "重置密码";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"new_password", "name"=>"新密码", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"forget_code", "name"=>"重置密码验证码", "type"=>"string");

// 绑定账号
$action_key = "sns_bind_exists_account";
$action_list[$action_key]["name"] = "第三方绑定已有账号";
$action_list[$action_key]["param_arr"][] = array("field"=>"login_type", "name"=>"账号类型", "type"=>"enum","value"=>array("email","mobile"));
$action_list[$action_key]["param_arr"][] = array("field"=>"account", "name"=>"登录账户", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"password", "name"=>"登录密码", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_username", "name"=>"第三方用户名", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_face", "name"=>"第三方头像", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_openid", "name"=>"第三方openid", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_type", "name"=>"第三方类型", "type"=>"enum","value"=>array("qq","weibo","weixin"));

// 获取第三方登陆注册验证码
$action_key = "sns_get_mobile_check_code";
$action_list[$action_key]["name"] = "获取第三方登陆注册验证码";
$action_list[$action_key]["param_arr"][] = array("field"=>"account", "name"=>"手机", "type"=>"string");
/*$action_list[$action_key]["param_arr"][] = array("field"=>"sns_username", "name"=>"第三方用户名", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_face", "name"=>"第三方头像", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_openid", "name"=>"第三方openid", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_type", "name"=>"第三方类型", "type"=>"string");*/
// 通过第三方登陆创建并绑定手机账号
$action_key = "sns_add_mobile_user";
$action_list[$action_key]["name"] = "通过第三方登陆创建并绑定手机账号";
$action_list[$action_key]["param_arr"][] = array("field"=>"user_type", "name"=>"用户身份", "type"=>"enum","value"=>array("user","org"));
$action_list[$action_key]["param_arr"][] = array("field"=>"account", "name"=>"手机", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"password", "name"=>"密码", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"check_code", "name"=>"验证码", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_username", "name"=>"第三方用户名", "type"=>"string","default"=>"zqf");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_face", "name"=>"第三方头像", "type"=>"string","default"=>"http://img.hotyq.com/user/45/454/45445/896547024fb91a13.jpg");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_openid", "name"=>"第三方openid", "type"=>"string","default"=>"ABCDEFGHIJKLMNO");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_type", "name"=>"第三方类型", "type"=>"enum","value"=>array("qq","weibo","weixin"));
$action_list[$action_key]["param_arr"][] = array("field"=>"app_type", "name"=>"手机平台类型", "type"=>"string","default"=>"app_type");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_id", "name"=>"移动设备硬件id", "type"=>"string","default"=>"app_id");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_os", "name"=>"应用操作系统", "type"=>"string","default"=>"app_os");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_os_ver", "name"=>"应用操作系统版本", "type"=>"string","default"=>"app_os_type");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_ui_os", "name"=>"定制系统操作系统", "type"=>"string","default"=>"app_ui_os");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_ui_os_ver", "name"=>"定制系统操作系统版本号", "type"=>"string","default"=>"app_ui_os_ver");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_name", "name"=>"移动应用程序名", "type"=>"string","default"=>"app_name");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_ver", "name"=>"移动应用程序版本号", "type"=>"string", "default"=>"1.0");

// 绑定账号
$action_key = "sns_login";
$action_list[$action_key]["name"] = "第三方账号登陆";
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_username", "name"=>"第三方用户名", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_face", "name"=>"第三方头像", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_openid", "name"=>"第三方openid", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sns_type", "name"=>"第三方类型", "type"=>"enum","value"=>array("qq","weibo","weixin"));
// 上传头像
$action_key = "upload_icon";
$action_list[$action_key]["name"] = "移动端上传头像";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"icon_img", "name"=>"用户头像", "type"=>"file");
$action_list[$action_key]["param_arr"][] = array("field"=>"cover_img", "name"=>"用户封面", "type"=>"file");
$action_list[$action_key]["param_arr"][] = array("field"=>"source_img", "name"=>"原图", "type"=>"file");

//编辑个人红名片
$action_key = "update_user_card";
$action_list[$action_key]["name"] = "移动端填写个人红名片";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"nickname", "name"=>"艺名", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sex", "name"=>"性别", "type"=>"enum","value"=>array("m","f"));
$action_list[$action_key]["param_arr"][] = array("field"=>"province_id", "name"=>"省id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"city_id", "name"=>"市id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"district_id", "name"=>"区id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"state", "name"=>"用户状态", "type"=>"enum","value"=>array("busy", "free", "other"));
$action_list[$action_key]["param_arr"][] = array("field"=>"role", "name"=>"角色选择", "type"=>"string");

//编辑机构红名片
$action_key = "update_org_card";
$action_list[$action_key]["name"] = "移动端填写机构红名片";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"nickname", "name"=>"机构名", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"create_time", "name"=>"机构建立时间", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"province_id", "name"=>"省id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"city_id", "name"=>"市id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"district_id", "name"=>"区id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"type", "name"=>"机构类型","type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"state", "name"=>"机构状态", "type"=>"enum","value"=>array("busy", "free", "other"));
$action_list[$action_key]["param_arr"][] = array("field"=>"legal_person", "name"=>"是否为法人","type"=>"enum","value"=>array("yes", "no"));

//获取角色九大类
$action_key = "get_role_list";
$action_list[$action_key]["name"] = "获取角色列表(select)";

//获取机构类型大类
$action_key = "get_org_type_list";
$action_list[$action_key]["name"] = "获取机构类型列表(select)";
//ping
$action_key = "ping";
$action_list[$action_key]["name"] = "ping一下";
//更新个人红档案
$action_key = "update_user_profile";
$action_list[$action_key]["name"] = "更新个人红档案";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"age", "name"=>"年龄", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"height", "name"=>"身高", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"weight", "name"=>"体重", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"bust", "name"=>"胸围", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"waist", "name"=>"腰围", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"hips", "name"=>"臀围", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"star", "name"=>"星座", "type"=>"enum","value"=>array('白羊座','金牛座','双子座','巨蟹座','狮子座','处女座','天秤座','天蝎座','射手座','魔蝎座','水瓶座','双鱼座',''));
$action_list[$action_key]["param_arr"][] = array("field"=>"blood", "name"=>"血型", "type"=>"enum","value"=>array('A', 'B', 'AB', 'O',''));
$action_list[$action_key]["param_arr"][] = array("field"=>"native_province_id", "name"=>"籍贯省id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"native_city_id", "name"=>"籍贯市id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"native_district_id", "name"=>"籍贯区id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"school", "name"=>"学校", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"finish_year", "name"=>"毕业年份", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"specialty", "name"=>"专业", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"degree", "name"=>"最高学历", "type"=>"enum",'value'=>array('博士', '硕士', '本科', '高中', '初中', '小学', '其它',''));
$action_list[$action_key]["param_arr"][] = array("field"=>"in_org", "name"=>"所在机构", "type"=>"string");

// 上传红相册
$action_key = "add_ablum_photo";
$action_list[$action_key]["name"] = "移动端红相册上传图片";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"album_img", "name"=>"用户相册图片", "type"=>"file");


// 红相册照片删除
$action_key = "delete_ablum_photo";
$action_list[$action_key]["name"] = "移动端红相册照片删除";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"photo_id", "name"=>"用户相册图片ID", "type"=>"string");

//更新机构红档案
$action_key = "update_org_profile";
$action_list[$action_key]["name"] = "更新机构红档案";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"introduce", "name"=>"公司介绍", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"production", "name"=>"公司作品", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"honor", "name"=>"主要荣誉", "type"=>"string");


//获取个人用户信息
$action_key = "get_user_profile";
$action_list[$action_key]["name"] = "获取个人用户信息";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"个人用户id", "type"=>"int");

//获取我的收藏信息 红人
$action_key = "get_my_collect_user";
$action_list[$action_key]["name"] = "获取我的收藏信息-红人";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"int");

//获取我的收藏信息 机构
$action_key = "get_my_collect_org";
$action_list[$action_key]["name"] = "获取我的收藏信息-机构";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"int");

//获取我的收藏信息 招募
$action_key = "get_my_collect_recruit";
$action_list[$action_key]["name"] = "获取我的收藏信息-招募";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"int");

//获取机构用户信息
$action_key = "get_org_profile";
$action_list[$action_key]["name"] = "获取机构用户信息";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"机构用户id", "type"=>"int");

//获取地址信息
$action_key = "get_address_array";
$action_list[$action_key]["name"] = "获取地址字段信息(select)";
$action_list[$action_key]["param_arr"][] = array("field"=>"pid", "name"=>"省份id", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"cid", "name"=>"城市id", "type"=>"int");

//获取个人用户的系统标签
$action_key = "get_sys_tag_list_for_user";
$action_list[$action_key]["name"] = "获取个人用户的系统标签";

//获取机构用户的系统标签
$action_key = "get_sys_tag_list_for_org";
$action_list[$action_key]["name"] = "获取机构用户的系统标签";

//获取红名片信息
$action_key = "get_user_card_data";
$action_list[$action_key]["name"] = "获取红名片字段信息(select)";

//获取红档案信息
$action_key = "get_user_profile_data";
$action_list[$action_key]["name"] = "获取红档案字段信息(select)";

//获取机构红名片信息
$action_key = "get_org_card_data";
$action_list[$action_key]["name"] = "获取机构红名片字段信息(select)";



?>