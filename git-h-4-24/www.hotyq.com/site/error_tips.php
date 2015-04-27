<?php
	header("Content-type:text/html;charset=utf-8");
	require_once("../includes/common_inc.php");
	if(isset($_REQUEST['state_code']) && array_key_exists(clear_gpq($_REQUEST['state_code']),$STATE_LIST)){
		$error_tips = $STATE_LIST[clear_gpq($_REQUEST['state_code'])];
	}else{
		$error_tips = $STATE_LIST[1099];
	}
	$smarty -> assign('state_code',clear_gpq($_REQUEST['state_code']));
	$smarty -> assign('error_tips',$error_tips);
	$smarty -> display('site/error_tips.html');
?>