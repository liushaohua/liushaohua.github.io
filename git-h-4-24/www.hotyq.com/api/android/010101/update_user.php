<?php
header("Content-type:text/html;charset=utf-8");
include "../../../includes/common_api_android_inc.php";

$type = clear_gpq($_REQUEST['openid_type']);
$openid = clear_gpq($_REQUEST['openid']);
global $db_hyq_read;
$sql = "SELECT * FROM hyq_user WHERE {$type} = '{$openid}'";
$query = $db_hyq_write -> query($sql);
$result = $db_hyq_read -> fetch_array($query);

if($result){
	global $db_hyq_write;
	$uid = $result['id'];
	$sql_user = "UPDATE hyq_user SET {$type} = null WHERE id = '{$uid}'";
	if($db_hyq_write -> query($sql_user)){
			echo 1000;
	}else{
			echo 1861;//openid删除失败
	}
}else{
	echo 1860;
}

?>