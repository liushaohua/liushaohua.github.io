<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_inc_test.php');
	require_once COMMON_PATH."/invite5.class.php";
	require_once COMMON_PATH."/recruit.class.php";
	//require_once ("./apply.class.php");

	$recruit = new recruit();
	$recruit_list =$recruit -> get_recruit_list_by_user_for_invite(45678);
	var_dump($recruit_list);
	$smarty -> display("suntianxing/send_invite5.html");