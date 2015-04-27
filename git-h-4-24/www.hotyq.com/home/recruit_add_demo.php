<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');
	$userprofile = new userprofile();
	$recruit = new recruit();
	$base = new base();
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
	//2 红照片
	$result = $recruit -> get_recruit_photo_list($recruit_id);
	if($result){
		$album_list = "[";
		$num = count($result);
		for($i = 0; $i < 6;$i++) {
			if($i < $num){
				$album_list .= "{id:\"".$result[$i]['id']."\",thumbnail:\"".$result[$i]['server_url'].$result[$i]['path_url']."!150.100\",photo:\"".$result[$i]['server_url'].$result[$i]['path_url']."!800\"},";
			}elseif($i >= $num){
				$album_list .= "null,";
			}
		}
		$album_list = rtrim($album_list,',');
		$album_list .= "]";
	}else{
		$album_list = "[null,null,null,null,null,null]";
	}
	$smarty -> assign('album_list',$album_list);
	//3 招募类型列表分配到页面（页面需要）
	//查出所有的结果集  处理下  装到数组中
	$type_list = $recruit -> get_recruit_type_list();
	foreach($COMMON_CONFIG["RECRUIT_TYPE"] as $k=>$v){
		foreach($type_list as $v0){
			if($v0['type_group'] == $v){
				$type_arr[$k][] = $v0;
			}
		}
	}
	//var_dump($type_arr);
	$smarty -> assign('type_arr',$type_arr);
	//4 服务一级列表 分配到页面
	$service_1_list = $base -> get_service_list_by_parentid();
	$smarty -> assign('service_1_list',$service_1_list);
	//5 算出招募角色的数量  分配
	for($i=1;$i<16;$i++){
		$num[] = $i;
	}
	$num[] = 999;
	$smarty -> assign('num',$num);
	//6 查找出省  分配到模板
	$provincelist = $base -> get_province_list();
	$smarty -> assign('provincelist',$provincelist);	#省的列表
	//7 算出当前时间  分配
	$today = date('Y-m-d H:i',time());
	$tomorrow = date('Y-m-d H:i',time()+24*60*60);
	// echo $today;
	// echo $tomorrow;exit;
	$smarty -> assign('today',$today);   #今天时间
	$smarty -> assign('tomorrow',$tomorrow); #明天时间
	
	$smarty -> display("home/recruit_add_demo.html");exit;
	//---------------------------------------------------------------
	//2 查找出招募类型和 一级服务  分配
	$service_list = $userprofile -> get_service_list_by_parentid();
	var_dump($service_list);
	echo "-----------------";
	$service_list = $userprofile -> get_service_list_by_parentid(1);
	//var_dump($service_list);exit;
	echo "<script>window.top.document.location.href='/home/ajax/ajax_home_recruit.php?action=add_recruit_tmp&id=".$recruit_id."'</script>";
	exit;		
?>