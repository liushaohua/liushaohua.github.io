<?php
	header("content-type: text/html; charset=utf-8");
	require_once ('../../includes/common_inc.php');
	$base = new base();
	if(empty($_REQUEST['id'])){
		$base -> go_404();
		exit;
	}else{
		$recruit_id = intval($_REQUEST['id']);
	}
	if(in_array($recruit_id,array('324','356','363','773'))){
		require_once ('./'.$recruit_id.'.php');
	}else{
		$base -> go_404();
		exit;
	}
?>