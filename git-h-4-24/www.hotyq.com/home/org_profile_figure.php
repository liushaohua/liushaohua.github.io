<?php
	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_home_inc.php');
	session_start();
	require_once ('../../common/orgprofile.class.php');
	require_once ('../../common/album.class.php');
	$user = new user();
	$base = new base();
	$album = new album();
	$userprofile = new orgprofile();
	foreach($_REQUEST as $k => $v){
		$album -> update_photo_title($user_info['id'],intval($k),clear_gpq($v));
	}
	$result = $album -> get_photo_list_by_user($user_info['id']);
	if($result){
		$album_list = $result;
	}else{
		$album_list = array();
	}
	isset($_SERVER['HTTP_REFERER']) ? $referer_url = $_SERVER['HTTP_REFERER'] : $referer_url = 'http://www.hotyq.com';
	$smarty -> assign('pre_page',$referer_url);
	$smarty -> assign("edit_part","figure");
	$smarty -> assign('album_list',$album_list);
	$smarty -> assign('org_uid',$user_info['id']);
	$smarty -> display("home/org_profile_figure.html");
?>
