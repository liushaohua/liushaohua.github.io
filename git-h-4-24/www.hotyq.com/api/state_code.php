<?php
function get_tips($state_code){
	$state_list = array();
	$state_list[1000] = '成功!';
	$state_list[1001] = '请选择您的身份类型!';
	$state_list[1002] = '请填写要注册的手机号!';
	$state_list[1003] = '请输入6-16位密码，字母区分大小写!';
	$state_list[1004] = '请再次输入您的密码！';
	$state_list[1005] = '两次输入的密码不一致！';
	$state_list[1006] = '请填写验证码!';
	$state_list[1007] = '请输入您接收到的手机激活码!';
	$state_list[1008] = '邮箱或手机号格式不正确!';
	$state_list[1009] = '邮箱格式不正确!';
	$state_list[1010] = '该邮箱已在本站注册过!';
	$state_list[1011] = '该手机号已在本站注册过!';
	$state_list[1012] = '验证码输入错误！';
	$state_list[1013] = '手机激活码输入错误！';
	$state_list[1014] = '数据库内部错误！';
	
	$state_list[1015] = '账户不存在或密码错误,请重新输入!';
	$state_list[1016] = '手机号码格式不正确！';
	$state_list[1017] = '邮箱为空！';
	$state_list[1018] = '手机号码为空！';
	$state_list[1019] = '密码为空!';
	$state_list[1020] = '密码长度不符';
	$state_list[1022] = '邮箱未注册！';
	$state_list[1023] = '手机未注册！';
	$state_list[1024] = '账户绑定失败！';
	$state_list[1025] = '该账户已绑定过QQ！';
	$state_list[1026] = '该账户已绑定过微博！';
	$state_list[1027] = '该账户不是本站登录账户！';
	$state_list[1028] = 'QQ登录授权失败！';
	$state_list[1029] = 'weibo登录授权失败！';
	$state_list[1030] = '获取SNS头像失败！';
	$state_list[1031] = '该sns账号已绑定过其他账号！';
	$state_list[1032] = '该账户已绑定过weixin！';
	$state_list[1040] = '密码修改失败！';
	$state_list[1041] = '无上传操作！';
	$state_list[1042] = '上传文件大小超过限制！';
	$state_list[1043] = '上传文件类型不符！';
	$state_list[1044] = '文件上传失败！';
	$state_list[1045] = '上传文件大小超过服务器限制！';
	$state_list[1046] = '上传文件大小超过页面限制！';
	$state_list[1047] = '只有部分文件被上传！';
	$state_list[1048] = '没有文件被上传！';
	$state_list[1049] = '找不到上传临时目录！';
	$state_list[1050] = '上传文件写入临时目录失败！';
	$state_list[1051] = '服务器上传扩展功能未打开！';
	$state_list[1052] = '服务器写入失败！';
	$state_list[1053] = '云同步失败！';
	$state_list[1054] = '删除服务器文件失败！';
	$state_list[1055] = '服务器文件不存在！';
	$state_list[1056] = '云文件删除失败！';
	$state_list[1057] = '云文件不存在！';
	$state_list[1058] = '您只能上传6张照片，您可以先删除原有照片后重新上传！';
	$state_list[1059] = '用户类型不匹配！';
	
	
	$state_list[1501] = '发送激活邮件失败！';
	$state_list[1502] = '发送激活邮件用户信息无效';
	
	$state_list[1504] = '添加手机用户失败';
	$state_list[1505] = '添加邮件用户失败';
	$state_list[1506] = '发送忘记密码邮件无效';
	$state_list[1507] = '发送找回密码邮件用户信息无效！';
	$state_list[1510] = '密码不正确！';
	$state_list[1511] = '用户不存在！';
	$state_list[1513] = '验证码发送时更新数据失败！';
	$state_list[1515] = '请5分钟后再发送！';
	$state_list[1516] = '验证码发送时新建数据失败';
	$state_list[1517] = '已经是注册用户，不能发送！';
	$state_list[1518] = '您的手机验证码尚未发送！';
	$state_list[1519] = '手机注册验证码错误！';
	
	$state_list[1230] = '手机号码不能为空!';
	$state_list[1231] = '请输入新密码!';
	$state_list[1232] = '请再次输入新密码!';
	$state_list[1233] = '两次密码输入不一致!';
	$state_list[1234] = '数据库修改密码失败!';
	
	$state_list[1201] = '图片宽度不够';
	$state_list[1202] = '图片高度不够';
	$state_list[1203] = '图片移动失败！!';
	$state_list[1204] = '请上传头像!';
	$state_list[1205] = '请上传封面!';
	$state_list[1206] = '请上传原图!';
	$state_list[1207] = '图片保存失败!';
	$state_list[1208] = '图片删除失败!';
	$state_list[1209] = '请传递app_token!';
	
	$state_list[1211] = '请填写手机号码!';
	$state_list[1213] = '手机用户不存在!';
	$state_list[1215] = '请5分钟后再发送!';
	$state_list[1216] = '验证码发送时更新数据失败!';
	$state_list[1217] = '验证码不匹配!';
	$state_list[1218] = '请填写手机号码!';
	$state_list[1219] = '请填写手机验证码!';
	$state_list[1220] = '获取用户信息失败!';
	$state_list[1221] = '修改token失败!';
	$state_list[1222] = '账号身份不匹配，请重新登录!';
	$state_list[1223] = ' 账号身份已过期，请重新登录!';
	
	$state_list[1224] = ' 密码错误!';
	$state_list[1225] = ' 根据id没有查到结果集!';
	
	$state_list[1250] = ' 请填写用户!';
	$state_list[1251] = ' 请填写验证码!';
	$state_list[1252] = ' 请填写安全验证码!';
	$state_list[1253] = ' 请输入正确的手机或邮箱!';
	$state_list[1254] = ' 请填写新密码!';
	$state_list[1255] = ' 根据id没有查到结果集!';
	$state_list[1256] = ' 请填写用户id!';
	$state_list[1258] = '请填写用户类型';
	$state_list[1260] = '自定义角色插入失败';
	$state_list[1261] = '自定义角色删除失败';
	$state_list[1262] = '用户角色删除失败';
	$state_list[1263] = '用户角色插入失败';
	$state_list[1264] = '用户没有自定义角色';
	$state_list[1265] = '请填写app_id';
	
	
	$state_list[1401] = '请填写指定用户id';
	$state_list[1402] = '请填写指定页数';
	$state_list[1403] = '查找不到相关招募';
	$state_list[1404] = '没查到角色字典';
	$state_list[1405] = '没查到省份字典';
	$state_list[1406] = '没查到城市';
	
	
	
	$state_list[1101] = '昵称填写错误';
	$state_list[1102] = '性别没填写';
	$state_list[1103] = '省没填写';
	$state_list[1104] = '市没填写';
	$state_list[1105] = '区没填写';
	$state_list[1106] = '个人状态没填写';
	$state_list[1107] = '期望角色没填写';
	$state_list[1110] = '资料修改失败';
	$state_list[1111] = '昵称修改失败';
	$state_list[1112] = '资料修改失败';
	$state_list[1113] = '已经是绑定用户，不能发送！';
	$state_list[1114] = '用户绑定手机失败';
	$state_list[1115] = '请填写身份证信息';
	$state_list[1116] = '身份证格式不正确';
	$state_list[1117] = '身份证已经绑定过!';
	$state_list[1118] = '插入注册ip时间失败!';
	$state_list[1119] = '工商号为空！!';
	$state_list[1120] = '工商号格式不正确!';
	$state_list[1121] = '工商号已经绑定过!';
	$state_list[1122] = '身份证号码或姓名不符!';
	$state_list[1123] = '工商号号码绑定失败!';
	$state_list[1131] = '学校名字格式错误!';
	$state_list[1132] = '专业名字错误!';
	$state_list[1134] = '注册时插入信息失败!';
	$state_list[1135] = '请选择个人或者机构用户!';
	$state_list[1136] = '公司创建时间没选择!';
	$state_list[1137] = '公司类型没选择!';
	$state_list[1138] = '公司状态没选择!';
	$state_list[1139] = '是否为法人没选择!';
	
	$state_list[1140] = '验证码为空!';	
	$state_list[1141] = '验证码已过期!';	
	$state_list[1142] = '输入的验证码不一致!';	
	$state_list[1143] = '绑定时更新用户邮箱地址失败!';	
	$state_list[1144] = '更新身份证状态失败!';	
	$state_list[1145] = '更新工商号状态失败!';	
	$state_list[1146] = '删除红艺人失败!';
	$state_list[1147] = '增加红艺人失败!';
	$state_list[1148] = '身份证姓名格式不符!';	
	$state_list[1149] = '机构简介字数超出范围了!';	
	$state_list[1150] = '主要作品字数超出范围了!';	
	$state_list[1151] = '主要荣誉字数超出范围了!';	
	
	$state_list[1152] = '邀约处理失败!';	
	$state_list[1153] = '邀约删除失败!';	
	$state_list[1154] = '红艺人名字为空!';	
	$state_list[1155] = '红艺人内容不能为空!';	
	$state_list[1156] = '邀约状态没选!';		
	$state_list[1157] = '密码修改失败!';		
	$state_list[1158] = '密码错误!';		
	$state_list[1159] = '已经是绑定用户!';		
	
	$state_list[1165] = '您的输入的学校名字太长!';		
	$state_list[1166] = '您的输入的专业名字太长!';		
	$state_list[1167] = '您的输入所属机构名字太长!';		
	
	$state_list[1180] = '请不要重复邀约!';		
	$state_list[1181] = '请至少填写一种联系方式!';		
	$state_list[1182] = '请选择邀约的角色!';		
	$state_list[1183] = '请不要重复邀约!';		
			


	
	$state_list[1099] = '非法操作!';
	
	return $state_list[$state_code];
}
?>