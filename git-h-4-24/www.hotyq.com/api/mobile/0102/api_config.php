<?php
//发送短信间隔时间
$MOBILE_LOCK_TIME = 60;
//分页公共变量
$PAGESIZE['MYWORK_PAGE'] = '10';
$PAGESIZE['MYCOLLECT_PAGE'] = '10';
$PAGESIZE['RECRUIT_PAGE'] = '10';

// 用户登陆
$action_key = "login";
$action_list[$action_key]["name"] = "用户登录";
$action_list[$action_key]["param_arr"][] = array("field"=>"account", "name"=>"登录账户", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"password", "name"=>"登录密码", "type"=>"string");
// 发送手机验证码
$action_key = "send_mobile_verify";
$action_list[$action_key]["name"] = "发送手机验证码";
$action_list[$action_key]["param_arr"][] = array("field"=>"mobile", "name"=>"手机号", "type"=>"string");
// 设置个人红名片
$action_key = "set_user_card";
$action_list[$action_key]["name"] = "设置个人红名片";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"icon_img", "name"=>"用户头像", "type"=>"file");
$action_list[$action_key]["param_arr"][] = array("field"=>"nickname", "name"=>"用户昵称", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sex", "name"=>"性别", "type"=>"enum","value"=>array("m","f"));
$action_list[$action_key]["param_arr"][] = array("field"=>"province", "name"=>"省份", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"city", "name"=>"城市", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"district", "name"=>"区县", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"保存用户登陆状态的签名", "type"=>"string");
//设置机构红名片
$action_key = "set_org_card";
$action_list[$action_key]["name"] = "设置机构红名片";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"icon_img", "name"=>"用户头像", "type"=>"file");
$action_list[$action_key]["param_arr"][] = array("field"=>"nickname", "name"=>"用户昵称", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"is_legal", "name"=>"是否为法人", "type"=>"enum","value"=>array("0","1"));
$action_list[$action_key]["param_arr"][] = array("field"=>"province", "name"=>"省份", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"city", "name"=>"城市", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"district", "name"=>"区县", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"org_type", "name"=>"机构类型id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"保存用户登陆状态的签名", "type"=>"string");

//forget 邮箱找回密码，对指定邮箱发送修改密码邮件。--
$action_key = "forget_password_email";
$action_list[$action_key]["name"] = "邮箱找回密码，对指定邮箱发送修改密码邮件";
$action_list[$action_key]["param_arr"][] = array("field"=>"email", "name"=>"用户邮箱", "type"=>"string");
//forget_1 发送忘记密码验证码
$action_key = "send_mobile_verify_forget";
$action_list[$action_key]["name"] = "发送忘记密码手机验证码";
$action_list[$action_key]["param_arr"][] = array("field"=>"mobile", "name"=>"手机号", "type"=>"string");
//forget_2 验证忘记密码安全码
$action_key = "verify_code_forget";
$action_list[$action_key]["name"] = "验证忘记密码安全码";
$action_list[$action_key]["param_arr"][] = array("field"=>"mobile", "name"=>"手机号", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"verify_code", "name"=>"验证码", "type"=>"string");
//forget_3 设置新密码(传递安全码)
$action_key = "set_new_password";
$action_list[$action_key]["name"] = "设置新密码";
$action_list[$action_key]["param_arr"][] = array("field"=>"password", "name"=>"新密码", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"mobile", "name"=>"找回密码时收到的手机号", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"verify_code", "name"=>"短信验证码", "type"=>"string");
//check_update  检查更新app版本
$action_key = "check_update";
$action_list[$action_key]["name"] = "检查更新app版本";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户token", "type"=>"string");
//ta的招募（通过的）
$action_key = "get_specify_user_recruit";
$action_list[$action_key]["name"] = "ta的招募";
$action_list[$action_key]["param_arr"][] = array("field"=>"suid", "name"=>"指定用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"page", "name"=>"页数", "type"=>"string");
//0 保存选择的二级角色
$action_key = "set_roles";
$action_list[$action_key]["name"] = "保存选择的二级角色";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"roles", "name"=>"用户选择角色", "type"=>"string");
//1 获取角色字典
$action_key = "get_roles";
$action_list[$action_key]["name"] = "获取角色字典";
//2 获取省份字典
$action_key = "get_provinces";
$action_list[$action_key]["name"] = "获取省份字典";
//3 根据省份获取市
$action_key = "get_cities";
$action_list[$action_key]["name"] = "根据省份获取市";
$action_list[$action_key]["param_arr"][] = array("field"=>"province", "name"=>"省", "type"=>"string");
//4 根据市获取区
$action_key = "get_districts";
$action_list[$action_key]["name"] = "根据市获取区";
$action_list[$action_key]["param_arr"][] = array("field"=>"city", "name"=>"城市", "type"=>"string");
//5 设置用户的头像
$action_key = "set_icon_img";
$action_list[$action_key]["name"] = "设置用户的头像";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户token", "type"=>"string");
//招募展示--
$action_key = "get_recruit_profile";
$action_list[$action_key]["name"] = "获取招募展示页的资料";
$action_list[$action_key]["param_arr"][] = array("field"=>"rid", "name"=>"招募id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
//招募展示(自己查看)--
$action_key = "get_recruit_profile_own";
$action_list[$action_key]["name"] = "获取招募查看页的资料(自己查看)";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"登录token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"rid", "name"=>"招募id", "type"=>"string");


// 获取三围的范围
$action_key = "get_bwh_range";
$action_list[$action_key]["name"] = "获取三围的范围";
// 获取年龄和星座的范围
$action_key = "get_age_constellation_range";
$action_list[$action_key]["name"] = "获取年龄和星座的范围";

// 验证验证码
$action_key = "verify_code";
$action_list[$action_key]["name"] = "验证验证码";
$action_list[$action_key]["param_arr"][] = array("field"=>"mobile", "name"=>"手机号", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"verify_code", "name"=>"验证码", "type"=>"string");
// 注册
$action_key = "regist";
$action_list[$action_key]["name"] = "注册";
$action_list[$action_key]["param_arr"][] = array("field"=>"mobile", "name"=>"手机号", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"user_type", "name"=>"用户属性", "type"=>"enum","value"=>array("0","1"));
$action_list[$action_key]["param_arr"][] = array("field"=>"password", "name"=>"密码", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"verify_code", "name"=>"验证码", "type"=>"string");

//获取我的页面的用户数据
$action_key = "get_mine_user_info";
$action_list[$action_key]["name"] = "获取我的页面的用户数据";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆app_token", "type"=>"string");

//获取我报名的招募列表
$action_key = "get_my_recruit";
$action_list[$action_key]["name"] = "获取我发布招募列表";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"page", "name"=>"当前页码", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");

//我收到的邀约
$action_key = "get_my_invitation";
$action_list[$action_key]['name'] ="我收到邀约招募列表";
$action_list[$action_key]["param_arr"][] = array("field" => "uid","name"=>"用户ID","type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field" => "app_token","name"=>"保存用户登陆状态的签名","type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field" => "page","name"=>"邀约的页数","type"=>"string");

//获取邀约详情
$action_key = "get_invitation_info";
$action_list[$action_key]['name'] ="获取邀约详情";
$action_list[$action_key]["param_arr"][] = array("field" => "uid","name"=>"用户ID","type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field" => "app_token","name"=>"保存用户登陆状态的签名","type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field" => "iid","name"=>"邀约的ID","type"=>"int");

//获取我的报名列表
$action_key = "get_my_apply";
$action_list[$action_key]["name"] = "获取我的报名的招募列表";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"page", "name"=>"当前页码", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");

//手机用户绑定邮箱
$action_key = "bind_email";
$action_list[$action_key]["name"] = "手机用户绑定邮箱";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"email", "name"=>"想要绑定的邮箱", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"保存用户登陆状态的签名", "type"=>"string");

//用户重新发送邮件
$action_key = "resend_verify_email";
$action_list[$action_key]["name"] = "用户重新发送邮件";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"保存用户登陆状态的签名", "type"=>"string");

//修改密码
$action_key = "change_password";
$action_list[$action_key]["name"] = "修改密码";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"保存用户登陆状态的签名", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"password", "name"=>"原密码", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"new_password", "name"=>"新密码", "type"=>"string");

//获取用户收藏红人列表
$action_key = "get_my_favorites_reds";
$action_list[$action_key]["name"] = "获取我的收藏红人列表";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"page", "name"=>"当前页码", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");

//获取用户收藏机构列表
$action_key = "get_my_favorites_organization";
$action_list[$action_key]["name"] = "获取我的收藏机构列表";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"page", "name"=>"当前页码", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");

//获取用户收藏招募列表
$action_key = "get_my_favorites_recruit";
$action_list[$action_key]["name"] = "获取我的收藏招募列表";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"page", "name"=>"当前页码", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");

//获取个人用户编辑页的资料
$action_key = "get_user_profile";
$action_list[$action_key]["name"] = "获取个人用户编辑页的资料";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
//获取个人用户展示页的资料
$action_key = "get_specify_user_profile";
$action_list[$action_key]["name"] = "获取个人用户展示页的资料";
$action_list[$action_key]["param_arr"][] = array("field"=>"suid", "name"=>"指定用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
//删除指定的二级服务
$action_key = "delete_two_service";
$action_list[$action_key]["name"] = "删除指定的二级服务";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"指定用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"service_id", "name"=>"二级服务ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
//设置指定二级服务的三级服务
$action_key = "set_three_service";
$action_list[$action_key]["name"] = "设置指定二级服务的三级服务";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"指定用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"service_id", "name"=>"二级服务ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"service_list", "name"=>"三级服务ID字符串(逗号分隔)", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
//获取所有的二级服务
$action_key = "get_two_services";
$action_list[$action_key]["name"] = "获取所有的二级服务";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"指定用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
//获取指定二级服务下的三级服务
$action_key = "get_three_services";
$action_list[$action_key]["name"] = "获取指定二级服务下的三级服务";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"指定用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"service_id", "name"=>"二级服务ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");

//获取机构用户编辑页的资料
$action_key = "get_org_profile";
$action_list[$action_key]["name"] = "获取机构用户编辑页的资料";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"保存用户登陆状态的签名", "type"=>"string");
//获取机构用户展示页的资料
$action_key = "get_specify_org_profile";
$action_list[$action_key]["name"] = "获取机构用户展示页的资料";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"suid", "name"=>"指定用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"保存用户登陆状态的签名", "type"=>"string");
//发送认证手机验证码
$action_key = "send_mobile_verify_verification";
$action_list[$action_key]["name"] = "发送认证手机验证码";
$action_list[$action_key]["param_arr"][] = array("field"=>"mobile", "name"=>"手机号", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
//获取身高和体重的范围
$action_key = "get_height_weight_range";
$action_list[$action_key]["name"] = "获取身高和体重的范围";
//修改机构简介
$action_key = "set_org_introduction";
$action_list[$action_key]["name"] = "修改机构简介";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"introduction", "name"=>"机构简介", "type"=>"string");
//修改机构的荣誉
$action_key = "set_org_honor";
$action_list[$action_key]["name"] = "修改机构荣誉";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"honor", "name"=>"荣誉", "type"=>"string");
//修改机构的作品
$action_key = "set_org_showreel";
$action_list[$action_key]["name"] = "修改机构作品";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"showreel", "name"=>"作品", "type"=>"string");
//修改毕业院校
$action_key = "set_school";
$action_list[$action_key]["name"] = "修改毕业院校";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"school", "name"=>"学校信息", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"major", "name"=>"专业信息", "type"=>"string");
//验证认证手机验证码
$action_key = "verify_code_verification";
$action_list[$action_key]["name"] = "验证认证手机验证码";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"mobile", "name"=>"手机号", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"verify_code", "name"=>"用户输入的验证码", "type"=>"string");
//修改籍贯
$action_key = "set_birthplace";
$action_list[$action_key]["name"] = "修改籍贯";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"province", "name"=>"省", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"city", "name"=>"市", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"district", "name"=>"区", "type"=>"string");
//设置用户的昵称
$action_key = "set_nickname";
$action_list[$action_key]["name"] = "设置用户的昵称";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"nickname", "name"=>"用户昵称", "type"=>"string");

//修改年龄和星座
$action_key = "set_constellation_age";
$action_list[$action_key]["name"] = "修改年龄和星座";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"age", "name"=>"年龄", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"constellation", "name"=>"星座", "type"=>"string");
//修改三围
$action_key = "set_bwh";
$action_list[$action_key]["name"] = "修改三围";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"breast", "name"=>"胸围", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"waistline", "name"=>"腰围", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"hipline", "name"=>"臀围", "type"=>"string");
//修改身高和体重
$action_key = "set_height_weight";
$action_list[$action_key]["name"] = "修改身高和体重";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"weight", "name"=>"体重", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"height", "name"=>"身高", "type"=>"string");
//修改所在地
$action_key = "set_location";
$action_list[$action_key]["name"] = "修改所在地";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"province", "name"=>"省", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"city", "name"=>"市", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"district", "name"=>"区", "type"=>"string");

//修改QQ联系方式
$action_key = "set_contact_qq";
$action_list[$action_key]["name"] = "修改用户qq联系方式";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"qq", "name"=>"QQ", "type"=>"string");

//修改手机联系方式
$action_key = "set_contact_mobile";
$action_list[$action_key]["name"] = "修改用户手机联系方式";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"phone", "name"=>"手机", "type"=>"string");

//修改微信联系方式
$action_key = "set_contact_weixin";
$action_list[$action_key]["name"] = "修改用户微信联系方式";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"weixin", "name"=>"微信", "type"=>"string");

//修改邮箱联系方式
$action_key = "set_contact_email";
$action_list[$action_key]["name"] = "修改用户邮箱联系方式";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"email", "name"=>"邮箱", "type"=>"string");

//修改用户联系方式
$action_key = "set_user_contact";
$action_list[$action_key]["name"] = "修改用户联系方式";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"mobile", "name"=>"手机", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"qq", "name"=>"QQ", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"weixin", "name"=>"微信", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"email", "name"=>"邮箱", "type"=>"string");

//收藏红人
$action_key = "set_favorite_red";
$action_list[$action_key]["name"] = "收藏指定红人";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"suid", "name"=>"被收藏红人", "type"=>"int");

//收藏机构
$action_key = "set_favorite_org";
$action_list[$action_key]["name"] = "收藏指定机构";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"suid", "name"=>"被收藏机构", "type"=>"int");

//收藏招募
$action_key = "set_favorite_recruit";
$action_list[$action_key]["name"] = "收藏指定招募";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"srid", "name"=>"被收藏招募", "type"=>"int");

//删除收藏红人
$action_key = "delete_favorite_red";
$action_list[$action_key]["name"] = "删除收藏的红人";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"suid", "name"=>"被收藏红人", "type"=>"int");

//删除收藏机构
$action_key = "delete_favorite_org";
$action_list[$action_key]["name"] = "删除收藏的机构";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"suid", "name"=>"被收藏机构", "type"=>"int");

//删除收藏招募
$action_key = "delete_favorite_recruit";
$action_list[$action_key]["name"] = "删除收藏的招募";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"srid", "name"=>"被收藏招募", "type"=>"int");

//验证身份证
$action_key = "verify_identity_card";
$action_list[$action_key]["name"] = "验证身份证";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"name", "name"=>"姓名", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"identity_num", "name"=>"身份证号", "type"=>"string");

//上传形象照
$action_key = "add_photo";
$action_list[$action_key]["name"] = "上传相册照片";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"icon_img", "name"=>"照片", "type"=>"file");
$action_list[$action_key]["param_arr"][] = array("field"=>"description", "name"=>"描述", "type"=>"string");

//删除形象照
$action_key = "delete_photo";
$action_list[$action_key]["name"] = "删除相册照片";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"id", "name"=>"照片ID", "type"=>"int");

//设置某二级服务下的邀约用户的备注
$action_key = "set_invitation_comment";
$action_list[$action_key]["name"] = "设置某二级服务下的邀约用户的备注";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"iid", "name"=>"邀约的id", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"comment", "name"=>"备注的信息", "type"=>"string");

//设置某二级服务下的报名用户的备注
$action_key = "set_apply_comment";
$action_list[$action_key]["name"] = "设置某二级服务下的报名用户的备注";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"aid", "name"=>"报名的id", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"comment", "name"=>"备注的信息", "type"=>"string");

//设置某二级服务下的邀约用户的沟通结果
$action_key = "set_invitation_communication";
$action_list[$action_key]["name"] = "设置某二级服务下的邀约用户的沟通结果";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"iid", "name"=>"邀约的id", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"communication", "name"=>"沟通结果", "type"=>"enum","value"=>array("1","2","3"));


//设置某二级服务下的报名用户的沟通结果
$action_key = "set_apply_communication";
$action_list[$action_key]["name"] = "设置某二级服务下的报名用户的沟通结果";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"aid", "name"=>"报名的id", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"communication", "name"=>"沟通结果", "type"=>"enum","value"=>array("1","2","3"));

//获取用户报名的二级服务的信息
$action_key = "get_recruit_service_info";
$action_list[$action_key]["name"] = "获取用户报名的二级服务的信息";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"rid", "name"=>"招募ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"e_service_id", "name"=>"招募服务ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");

//获取招募里，某个二级服务下，已经报名的红人列表
$action_key = "get_apply_user_list";
$action_list[$action_key]["name"] = "获取招募里，某个二级服务下，已经报名的红人列表";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"rid", "name"=>"招募ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"e_service_id", "name"=>"招募服务ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"sex", "name"=>"红人性别", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"communication", "name"=>"沟通结果", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"page", "name"=>"当前页码", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");

//获取招募里，某个二级服务下，已经邀约的红人列表
$action_key = "get_invitation_user_list";
$action_list[$action_key]["name"] = "获取招募里，某个二级服务下，已经邀约的红人列表";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"rid", "name"=>"招募ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"e_service_id", "name"=>"招募服务ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"sex", "name"=>"红人性别", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"communication", "name"=>"沟通结果", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"page", "name"=>"当前页码", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");

//报名 招募服务
$action_key = "apply_recruit_service";
$action_list[$action_key]["name"] = "用户报名招募中的某一个二级服务";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"rid", "name"=>"招募ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"e_service_id", "name"=>"招募服务ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"three_service_ids", "name"=>"三级服务ID字符串", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"mobile", "name"=>"用户手机", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"qq", "name"=>"用户QQ", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"weixin", "name"=>"用户微信", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"email", "name"=>"用户邮箱", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");

//获取一级服务列表
$action_key = "get_one_service";
$action_list[$action_key]["name"] = "获取一级服务列表";

//获取指定一级服务下的二级服务列表
$action_key = "get_two_service";
$action_list[$action_key]["name"] = "获取指定一级服务下的二级服务列表";
$action_list[$action_key]["param_arr"][] = array("field"=>"sid", "name"=>"一级服务的id", "type"=>"int");

//获取搜索热词
$action_key = "get_hot_search";
$action_list[$action_key]["name"] = "获取搜索热词";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户TOKEN", "type"=>"string");

//二级服务下的红人列表
$action_key = "category_reds";
$action_list[$action_key]["name"] = "二级服务下的红人列表";
//$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
//$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sid", "name"=>"二级服务ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field" => "page","name"=>"页数","type"=>"string");

//二级服务下的机构列表
$action_key = "category_orgs";
$action_list[$action_key]["name"] = "二级服务下的机构列表";
//$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
//$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sid", "name"=>"二级服务ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field" => "page","name"=>"页数","type"=>"string");

//二级服务下的招募列表
$action_key = "category_recruit";
$action_list[$action_key]["name"] = "二级服务下的招募列表";
//$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
//$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"sid", "name"=>"二级服务ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field" => "page","name"=>"页数","type"=>"string");

//搜索红人列表
$action_key = "search_reds";
$action_list[$action_key]["name"] = "搜索红人";
//$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
//$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"keywords", "name"=>"搜素关键词", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field" => "page","name"=>"页数","type"=>"string");

//搜索机构列表
$action_key = "search_orgs";
$action_list[$action_key]["name"] = "搜索机构";
//$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
//$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"keywords", "name"=>"搜素关键词", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field" => "page","name"=>"页数","type"=>"string");

//搜索招募列表
$action_key = "search_recruit";
$action_list[$action_key]["name"] = "搜索招募";
//$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
//$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"keywords", "name"=>"搜素关键词", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field" => "page","name"=>"页数","type"=>"string");

//获取所有的机构类型
$action_key = "get_org_type";
$action_list[$action_key]["name"] = "获取所有的机构类型";

//获取邀约时的招募下的所有二级服务列表
$action_key = "get_invitation_service";
$action_list[$action_key]['name'] = "获取邀约时的招募下的所有二级服务列表";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"rid", "name"=>"招募ID", "type"=>"int");

//获取邀约三级服务信息
$action_key = "get_invitation_three_service";
$action_list[$action_key]['name'] = "获取邀约三级服务信息";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"rid", "name"=>"招募ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"e_service_id", "name"=>"二级服务的id", "type"=>"int");

//获取机构创立时间和机构类型的范围	
$action_key = "get_instituted_date_and_org_type_range";
$action_list[$action_key]['name'] = "获取机构创立时间和机构类型的范围";

//获取邀约下的所有招募列表	
$action_key = "get_invitation_recruit";
$action_list[$action_key]['name'] = "获取邀约下的所有招募";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");

//邀约某人	
$action_key = "invitation_someone";
$action_list[$action_key]['name'] = "邀约某人";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"rid", "name"=>"招募id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"invite_uid", "name"=>"邀约人的id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"e_service_id", "name"=>"e_service_id", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"three_service_list", "name"=>"所选三级服务列表", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"mobile", "name"=>"手机号", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"email", "name"=>"邮箱地址", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"weixin", "name"=>"微信", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"qq", "name"=>"qq号", "type"=>"string");

//修改机构创立时间和机构类型
$action_key = "set_instituted_date_and_org_type";
$action_list[$action_key]["name"] = "修改机构创立时间和机构类型";
$action_list[$action_key]["param_arr"][] = array("field"=>"uid", "name"=>"用户ID", "type"=>"int");
$action_list[$action_key]["param_arr"][] = array("field"=>"app_token", "name"=>"用户登陆token", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"instituted_date_begin", "name"=>"创立时间", "type"=>"string");
$action_list[$action_key]["param_arr"][] = array("field"=>"org_type", "name"=>"机构类型的id", "type"=>"string");

