<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../../includes/common_inc.php');
	//require_once COMMON_PATH."/apply.class.php";
	require_once ("./rongyunApi.class.php");

	
	$user = new user();
	$serverapi = new ServerAPI('pwe86ga5e1ej6','RhFuW6CvmYPPoC');
	
	function get_user_list(){
		global $db_hyq_read;
		$sql = "SELECT * FROM hyq_user";
		return $db_hyq_read -> fetch_result($db_hyq_read -> query($sql));
	}
	function update_user_token($userid, $token){
		global $db_hyq_write;
		$sql = "UPDATE hyq_user SET rongyun_token = '{$token}' WHERE id = '{$userid}'";
		$rr = $db_hyq_write -> query($sql);
		if(!$rr){
			return false;
		}
	}
	
	$user_list = get_user_list();
	$i = 0;
	$ii = 0;
	foreach ($user_list as $k=>$v){
		//$i++;
		//if($v['rongyun_token']){
		//	echo '<hr> OKO <hr>';
		//}else{
			$uid = $v['id'];
			$uname = $v['nickname'];
			$uface = $v['icon_server_url'].$v['icon_path_url'];
			if(empty($uname) || empty($uface)){
				$i++;
				echo '<hr>用户信息不完整!!!<hr>';
			}else{
				$result = $serverapi -> getToken($uid,$uname,$uface);
				//var_dump($result);
				//echo '<hr>';
				$r = json_decode($result, true);
				//print_r($r);exit;
				if($r['code'] == 200 && $r['userId'] == $uid){
					$rr = update_user_token($uid, $r['token']);
				}else{
					$ii++;
					echo '<hr>'.$r['code'].'::获取失败!<hr>';
				}
			}
		//}
	}
	echo '获取失败数:'.$ii;
	echo '信息不完整数:'.$i;