<?php
	/*
	*	ajax处理  suntianxing	
	*	日期  2014-12-09
	*/
	session_start();
	header("Content-type:text/html;charset=utf-8");
	require_once('../../includes/common_inc.php');
	require_once('../../suntianxing/rongyun/rongyunApi.class.php');
	
	$serverapi = new ServerAPI('pwe86ga5e1ej6','RhFuW6CvmYPPoC');

	//$cookie = $user -> get_cookie_user_info();
	//var_dump($cookie);
	
	
	$action = clear_gpq($_REQUEST['action']);
	if(empty($action)){
		echo 1099;
		exit;
	} 
	
	//$user = $cookie[0];
	//$uid = 1103;
	
	

	switch($action){
		case 'user_get_token':
			//$uid = $cookie[0];
			$uinfo['uid'] = intval($_REQUEST['uid']);
			$uinfo['uname'] = clear_gpq($_REQUEST['uname']);
			$uinfo['uface'] = clear_gpq($_REQUEST['uface']);
			user_get_token($uinfo);
			break;
	}
	
	function user_get_token($uinfo){
		global $serverapi;
		
		if(empty($uinfo['uid'])){
			 echo 1375;
			 exit;
		}
		if(empty($uinfo['uname'])){
			echo 1376;
			exit;
		}
		if(empty($uinfo['uface'])){
			echo 1377;
			exit;
		}
		
		$result = $serverapi -> getToken($uinfo['uid'],$uinfo['uname'],$uinfo['uface']);
		if($result){
			echo $result;
			exit;
		}else{
			echo 1378;
			exit;
		}
	}
?>