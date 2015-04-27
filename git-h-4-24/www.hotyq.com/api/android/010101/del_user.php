<?php
header("Content-type:text/html;charset=utf-8");
include "../../../includes/common_api_android_inc.php";

$method = clear_gpq($_REQUEST['method']);
global $db_hyq_read;

if($method == 'mobile'){
	$account = clear_gpq($_REQUEST['account']);
	echo $sql = "SELECT * FROM hyq_user WHERE mobile = '{$account}'";
}else{
	$account = clear_gpq($_REQUEST['account']);
	$sql = "SELECT * FROM hyq_user WHERE email = '{$account}'";
}

$query = $db_hyq_write -> query($sql);
$result = $db_hyq_read -> fetch_array($query);

if($result){
	global $db_hyq_write;
	//获取用户 的id
	$uid = $result['id'];
	
	if($result['login_type'] == 'mobile'){
		$sql_user = "DELETE FROM hyq_user WHERE mobile= '{$account}'";
		$sql_code = "DELETE FROM hyq_mobile_code WHERE mobile= '{$account}'";
		$db_hyq_write -> query($sql_user);
		$db_hyq_write -> query($sql_code);
	}else{
		$sql_user = "DELETE FROM hyq_user WHERE email= '{$account}'";
		$db_hyq_write -> query($sql_user);
	}
	
	if($result['user_type'] == 'user'){
		if($result['login_type'] == 'mobile'){
			$sql_profile = "DELETE FROM hyq_user_profile WHERE contact_mobile = '{$account}'";
		}else{
			$sql_profile = "DELETE FROM hyq_user_profile WHERE contact_email = '{$account}'";
		}
	}else{
		if($result['login_type'] == 'mobile'){
			$sql_profile = "DELETE FROM hyq_org_profile WHERE contact_mobile = '{$account}'";
		}else{
			$sql_profile = "DELETE FROM hyq_org_profile WHERE contact_email = '{$account}'";
		}
	}
	
	$db_hyq_write -> query($sql_profile);
	
	/* 个人标签删除操作 start */
// 	$tag_sql = "SELECT * FROM hyq_e_tag WHERE uid = '{$uid}'";
// 	$query = $db_hyq_write -> query($tag_sql);
// 	$tag_result = $db_hyq_read -> fetch_result($query);
	 
// 	if($tag_result){
// 		foreach($tag_result as $k=>$v){
// 			$sql_tag = "DELETE FROM hyq_tag WHERE id = '{$v['tag_id']}' AND parent_id = '-1'";
// 			$tag_query = $db_hyq_write -> query($sql_tag);
// 		}	
// 	}
	
	//$sql_e_tag = "DELETE FROM hyq_e_tag WHERE uid = '{$uid}'";
	//$tag_e_query = $db_hyq_write -> query($sql_e_tag);
	/* 标签删除操作 end */
	
	
	//数据表的数组  删除的对象
	$table_array= array('collect', 'role', 'e_role_user', 'photo', 'e_service_user');
	
	foreach($table_array as $k=>$v){
		//echo $v;
		$sql = 'DELETE FROM hyq_'.$v.' WHERE uid = '.$uid;
		$query = $db_hyq_write -> query($sql);
	}
	echo 1000;
}else{
	echo 1860;			//用户不存在
}

//备用
exit;
/* 招募的删除操作 start */
$sql_recruit = "SELECT * FROM hyq_recruit WHERE uid = '{$uid}'";
$recruit_query = $db_hyq_write -> query($sql_recruit);
$recruit_result = $db_hyq_read -> fetch_result($recruit_query);
if($recruit_result){
	foreach($recruit_result as $k=>$v){
		$sql_e_recruit = "DELETE FROM hyq_e_role_recruit WHERE recruit_id = '{$v['id']}'";
		$sql_del_photo = "DELETE FROM hyq_recruit_photo WHERE recruit_id = '{$v['id']}'";
			
		$recruit_e_query = $db_hyq_write -> query($sql_e_recruit);
		$recruit_photo_query = $db_hyq_write -> query($sql_del_photo);
			
		if (!recruit_e_query || !$recruit_photo_query){
			echo 100864;
			exit;
		}
	}
}
$sql_del_recruit = "DELETE FROM hyq_recruit WHERE uid = '{$uid}'";
$recruit_del_query = $db_hyq_write -> query($sql_del_recruit);
if (!recruit_del_query){
	echo 100865;
	exit;
}
/* 招募删除操作 end */













?>