<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../../includes/common_inc_test.php');
	//require_once COMMON_PATH."/apply.class.php";
	require_once ("./apply.class.php");
	$apply = new apply;


	$rid = 2;
	$result = $apply -> get_apply_num_by_recruit($rid);
	if($result){
		$smarty -> assign('apply_num',$result['num']);
	}else{
		$smarty -> assign('apply_num',0);
	}
	$apply_list = $apply -> get_apply_list_by_recruit($rid);
	if($apply_list){
		$smarty -> assign('list',$apply_list);
	}else{
		$apply_list = '';
		$smarty -> assign('list',$apply_list);
	}

	$smarty -> assign('rid',$rid);
	$smarty -> display("suntianxing/apply/recruit_apply_list.html");