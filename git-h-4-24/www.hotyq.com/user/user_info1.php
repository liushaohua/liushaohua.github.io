<?php

	header("content-type: text/html; charset=utf-8");
  	require_once('../includes/common_inc.php');
  	require_once (COMMON_PATH.'/page.class.php');
  	require_once (COMMON_PATH.'/album.class.php');
  	session_start();
    $user = new user();
    $album = new album();
    $userprofile = new userprofile();
    $base = new base();
    $recruit = new recruit();

	//var_dump($user_tags);
	echo '王一帆是好同志';
	$result = $recruit -> get_recruit_list_by_user(45724);
	echo '<pre>';
	var_dump($result);
?>
