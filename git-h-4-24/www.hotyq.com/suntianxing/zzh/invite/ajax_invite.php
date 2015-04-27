<?php
	session_start();
	header("Content-type:text/html;charset=utf-8");
	require_once('../../includes/common_inc_test.php');
	require_once('invite.class.php');
	
	$invite = new invite();
	$action = clear_gpq($_REQUEST['action']);
	if(empty($action)){
		echo 1099;
		exit;
	} 
switch($action){
	case 'send_invite':
		break;

	case 'update_invite':
		echo update_invite($id);
		break;

	case 'delete_invite':
		echo delete_invite($id);
		break;

	

	
	function update_invite($id){
		if($id < 1)	return 1099;			
		$invite_info['status'] = clear_gpq($_REQUEST['status']);
		$invite_info['mobile'] = clear_gpq($_REQUEST['mobile']);
		$invite_info['email'] = clear_gpq($_REQUEST['email']);
		$invite_info['weixin'] = clear_gpq($_REQUEST['weixin']);
		$invite_info['qq'] = clear_gpq($_REQUEST['qq']);	
		$result =  $invite -> update_invite($id,$invite_info); 
		if($result){
			return 1000;
		}else{
			return 1152; #错误值(邀约修改失败)
		}			
	}
	function delete_invite($id){
		$result =  $invite -> delete_invite($id);
		if($result){
			return 1000;
		}else{
			return 1153; #错误值(邀约删除失败)
		}				
	}
}
?>