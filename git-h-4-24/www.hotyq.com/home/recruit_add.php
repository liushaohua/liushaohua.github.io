<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');
	$userprofile = new userprofile();
	$recruit = new recruit();
	$base = new base();
	//0 后台验证用户是否验证过手机
	if( $user_info['mobile_status'] != 'yes'){
		//error_tips('1099');exit;
		echo "<script>top.location.href='/home/account_binding'</script>";exit;
	}
	//1 发布招募之前生成一条空数据  产生id   之后再update
	$arr['uid'] = intval($user_info["id"]);
	$arr['is_show'] = 'no';
	$arr['add_date'] = date('Y-m-d H:i:s',time());
	$recruit_id = $recruit -> add_recruit($arr);
	//$recruit_id = 8;
	if( !is_numeric($recruit_id) ){
		exit('插入失败');
	}
	//将招募id分配到隐藏表单中
	$smarty -> assign('recruit_id',$recruit_id);
	//3 招募类型列表分配到页面（页面需要）
	//查出所有的结果集  处理下  装到数组中
	$type_list = $recruit -> get_recruit_type_list($flash);
	//var_dump($type_list);exit;
	foreach($type_list as $v){
		$type_arr[$v['type_group']][] = $v;
	}
	$smarty -> assign('type_arr',$type_arr);
	//4 服务一级列表 分配到页面
	$service_1_list = $base -> get_service_list_by_parentid($flash);
	$smarty -> assign('service_1_list',$service_1_list);
	//5 算出招募角色的数量  分配
	$smarty -> assign('num',$COMMON_CONFIG["RECRUIT_NUM"]["OPTION"]);
	//6 查找出省  分配到模板
	$provincelist = $base -> get_province_list($flash);
	$smarty -> assign('provincelist',$provincelist);	#省的列表
	//7 算出当前时间  分配
	$today = date('Y-m-d H:00',time()+23*60*60);
	$tomorrow = date('Y-m-d H:00',time()+24*60*60);
	$after_tomorrow = date('Y-m-d H:00',time()+48*60*60);
	// echo $today;
	// echo $tomorrow;exit;
	$smarty -> assign('today',$today);   #今天时间
	$smarty -> assign('tomorrow',$tomorrow); #明天时间
	$smarty -> assign('after_tomorrow',$after_tomorrow); #后天时间
	
	//为了兼容ie php中记录上次的地址 分配过去
	if(!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER']) ){
		$server_http_referer = "http://www.hotyq.com";
	}else{
		$server_http_referer = $_SERVER['HTTP_REFERER'];
	}
	$smarty -> assign('server_http_referer',$server_http_referer);
	
	$smarty -> display("home/recruit_add.html");
?>