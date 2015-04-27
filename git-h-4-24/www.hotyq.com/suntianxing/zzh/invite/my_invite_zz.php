<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../../includes/common_inc.php');
	//require_once COMMON_PATH."/invite.class.php";
	require_once ("invite.class.php");
	$invite = new invite();
	$invite_list = $invite ->get_invite_list_by_self(444);
	var_dump($invite_list);
	$smarty -> assign('invite_list',$invite_list);
	$smarty -> display("suntianxing/invite/my_invite_zz.html");