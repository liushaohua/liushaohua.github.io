<?php
	require_once('../includes/common_home_inc.php');
 	require_once(COMMON_PATH."/userprofile.class.php");
 	require_once(COMMON_PATH."/album.class.php");
	$userprofile = new userprofile();
	$photo = new photo();
	$album = new album();
	$recruit = new recruit();
	
	$where = array('is_show'=>'yes');
	$recruit_list = $recruit -> get_recruit_list_by_where($where);
	foreach($recruit_list as $v){
		
		$recruit -> get_recruit_info($v['id'], $flash = 1);
	}
	exit;
?>