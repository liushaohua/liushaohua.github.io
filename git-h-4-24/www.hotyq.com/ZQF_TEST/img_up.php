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
	//createdir("http://img.hotyq.com/albums/");
	//ftp_copy_files(array("/albums/66154fffc508228d.jpg"),array("/tmp/66154fffc508228d.jpg"),$IMG_SERVERINDEX,FTP_ASCII)
	$source_files[] = "/tmp/66154fffc508228d.jpg";
	$target_files[] = "/albums/66154fffc508228d.jpg";
	$files[] = "/tmp/36155012830ecd53.jpg";
	//ftp开始分发
	//ftp_copy_files($target_files,$source_files,$IMG_SERVERINDEX,FTP_BINARY);
	//ftp_del_files($files,$IMG_SERVERINDEX);
	$remote_url = "http://q.qlogo.cn/qqapp/101159849/4B1C38AB077ED448B2A42585EE33E418/100";
	$dest_path = "/user/85/468/46885/1035508277282492.jpg";  
	$photo -> upload_photo_by_url($remote_url,$dest_path);
?>