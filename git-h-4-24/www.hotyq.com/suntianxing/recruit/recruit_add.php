<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../../includes/common_inc.php');
	$userprofile = new userprofile();
	$recruit = new recruit();
	var_dump($_POST);
	//操作三个表
	//1
	$arr['name'] = $_POST['recruit_name'];
	$arr['type_id'] = $_POST['recruit_type'];
	$arr['interview_end_time'] = $_POST['end_time'];
	$arr['work_start_time'] = $_POST['work_start_time'];
	$arr['work_end_time'] = $_POST['work_end_time'];
	$arr['province_id'] = $_POST['province_id'];
	$arr['city_id'] = $_POST['city_id'];
	$arr['district_id'] = $_POST['district_id'];
	$arr['descr'] = $_POST['descr'];
	$arr['cover_path_url'] = '/cover/52/108/10852/32954ddc7fcc25c0.jpg';
	$arr['cover_server_url'] = 'http://img.hotyq.com';
	$arr['uid'] = $_POST['uid'];
	$recruit_id = $recruit -> add_recruit($arr);
	if(!$recruit_id){
		exit('发布失败');
	}
	//2
	$recruit_service_info['service_1_id'] = $_POST['service_1'];
	$recruit_service_info['service_2_id'] = $_POST['service_2'];
	$recruit_service_info['service_require'] = $_POST['service_require'];
	$recruit_service_info['sex'] = $_POST['sex'];
	$recruit_service_info['number'] = $_POST['num'];
	$recruit_service_info['recruit_id'] = $recruit_id;
	//var_dump($recruit_service_info);
	$e_id = $recruit -> add_recruit_service($recruit_service_info);
	if(!$e_id){
		exit('一二级服务插入失败');
	}
	//3
	foreach($_POST['service_3'] as $v){
		$service_item['service_1_id'] = $_POST['service_1'];
		$service_item['service_2_id'] = $_POST['service_2'];
		$service_item['service_3_id'] = $v;
		$service_item['recruit_id'] = $recruit_id;
		$result = $recruit -> add_hyq_e_service_item($e_id,$service_item);
		if(!$result){
			exit('三级服务插入失败');
		}
	}
	echo '发布成功！！';
?>