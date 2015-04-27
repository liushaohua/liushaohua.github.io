<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../../includes/common_inc_test.php');
	//require_once COMMON_PATH."/apply.class.php";
	require_once ("./apply.class.php");
	$apply = new apply;

    //$cookie = $user -> get_cookie_user_info();
	//var_dump($cookie);

	$uid = 520;
	$apply_list = $apply -> get_apply_list_by_user($uid);
	if($apply_list){
		$smarty -> assign('list',$apply_list);
	}else{
		$apply_list = '';
		$smarty -> assign('list',$apply_list);
	}

	$smarty -> assign('uid',$uid);
	$smarty -> display("suntianxing/apply/receive_apply.html");