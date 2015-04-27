<?php
	header("content-type:text/html;charset=utf-8");
	require_once('../includes/common_home_inc.php');
	if(isset($_REQUEST['action']) && clear_gpq($_REQUEST['action']) == 'suggest'){
		$smarty -> display("complain/suggest.html");
	}else{
		isset($_REQUEST['source_id']) ? $source_id = intval($_REQUEST['source_id']) : $source_id = '';
		isset($_REQUEST['nickname']) ? $nickname = clear_gpq($_REQUEST['nickname']) : $nickname = '';
		isset($_REQUEST['type']) ? $type = clear_gpq($_REQUEST['type']) : $type = '';
		isset($_SERVER['HTTP_REFERER']) ? $referer_url = $_SERVER['HTTP_REFERER'] : $referer_url = '';
		if(strstr($referer_url,'login')){
			$referer_url = "http://www.hotyq.com";	
		}
		$smarty -> assign("source_id",$source_id);
		$smarty -> assign("nickname",$nickname);
		$smarty -> assign("type",$type);
		$smarty -> assign("referer_url",$referer_url);
		$smarty -> display("complain/complain.html");
	}
?>