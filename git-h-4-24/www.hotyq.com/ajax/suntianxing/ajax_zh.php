<?php
	/*
	*	邀约  ajax处理  suntianxing	
	*	日期  2014-12-09
	*/

	session_start();
	header("Content-type:text/html;charset=utf-8");
	require_once('../../includes/common_inc_test.php');
	//var_dump($_REQUEST);
	
	var_dump($_REQUEST['action']);#action 
	//var_dump($_REQUEST['ajaxData']);
	var_dump($_REQUEST['ajaxData']['firmIntro']);
		var_dump($_REQUEST['ajaxData']['firmWorks']);
			var_dump($_REQUEST['ajaxData']['firmHonor']);
	//$json = $_REQUEST['ajaxData'];
	//$val =  json_decode($json);
	//var_dump($val['firmIntro']);