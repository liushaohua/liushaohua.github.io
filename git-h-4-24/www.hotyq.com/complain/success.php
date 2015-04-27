<?php
	header("content-type:text/html;charset=utf-8");
	require_once('../includes/common_home_inc.php');
	isset($_REQUEST['source_id']) ? $source_id = intval($_REQUEST['source_id']) : $source_id = '';
	isset($_REQUEST['referer_url']) ? $referer_url = clear_gpq($_REQUEST['referer_url']) : $referer_url = '';
	isset($_REQUEST['reason']) ? $reason = clear_gpq($_REQUEST['reason']) : $reason = '';
	isset($_REQUEST['descr']) ? $descr = clear_gpq($_REQUEST['descr']) : $descr = '';
	isset($_REQUEST['type']) ? $type = clear_gpq($_REQUEST['type']) : $type = '';
	function add_complain($source_id,$referer_url,$reason,$descr,$type){
		global $db_hyq_write,$user_info;
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$ip = getIP();
		$sql = "INSERT INTO hyq_complain SET 
					source_id = '{$source_id}',
					userid = '{$user_info['id']}',
					type = '{$type}',
					referer_url = '{$referer_url}',
					reason = '{$reason}',
					descr = '{$descr}',
					add_date = now(),
					ip = '{$ip}',
					agent = '{$agent}',
					status = 'no'
					";
		return $db_hyq_write -> query($sql);
	}
	if($source_id && $referer_url && $reason && $descr && $type && in_array($type,array('user','org','recruit'))){
		$result = add_complain($source_id,$referer_url,$reason,$descr,$type);
	}else{
		$result = false;
	}
	$smarty -> assign('referer_url',$referer_url);
	$smarty -> assign('is_success',$result);
	$smarty -> display("complain/success.html");
?>