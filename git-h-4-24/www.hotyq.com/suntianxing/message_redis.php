<?php
	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_inc_test.php');
	require_once('./message.class.php');
	require_once('./message_redis.class.php');
	require_once('./redis.class.php');
	//session_start();
	$user = new user;
	$user_cookie = $user -> get_cookie_user_info();
	$uid = $user_cookie['userid'];
	//$usertype = $user_cookie['user_type'];
	$smarty -> assign('uid', $uid);
	

	//$smarty -> assign('friend', $friend);
	//$smarty -> assign('detail_list', $message_list);
	$smarty -> display("suntianxing/message_redis.html");
	exit;
	
	
	
	
	
	
	$message = new message;
	
	//获取私信记录
	//$user = 1314;
	$friend = 513;
	$key = $user.'_'.$friend;
	//$friend = clear_gpq($_REQUEST['friend']);
	
	/* 
	$redis = new redis_class;   //清除Redis缓存
 	if($redis -> del($key)){
		echo 'ok';
	}else{
		echo 'NO';
	}
	exit; 
	 */
	$message_list = $message -> get_message_list_by_user_friend($user, $friend);
	/* foreach($message_list as $k=>$v){
			print_r($v);
			echo '<hr>';
			//exit;
	}
	exit; */
	
	/*
	echo '验证索引';				//操作队列数据 by index
	echo '<hr>';
	print_r($index = $redis -> lindex($key, 10));
	echo '<hr>';
	print_r($index = unserialize($index));
	echo '<hr>';
	$index['is_show'] = 0;
	print_r($index);
	//$redis -> lset($key, 0)
	echo '<hr>';
	*/
	for($i=0;$i<50;$i++){
		if(isset($message_list[$i]) || !empty($message_list[$i])){
			//var_dump($message_list[$i]);
			if(is_array($message_list[$i])){
				echo '读私信记录数据库!';
			}else{
				echo '读私信记录缓存数据!';				//取私信记录缓存  队列缓存已生产
			}
			//echo  '<hr>';
			//print_r($message_list[$i]);
			//echo '<hr>';
			
			print_r(unserialize($message_list[$i]));
			echo '<hr>';
		}else{
			echo '暂无数据,请稍候!   ';
		}
	}
	//exit;
	
	//echo '<hr>';
	if($message_list == 1325){
		echo $message_list;
		exit('私信记录查询失败!');
	}elseif($message_list == 1351){
		echo $message_list;
		exit('存缓存失败');
	}else{
		foreach($message_list as $k => $v){
			if(!is_array($v)){
				$v = unserialize($v);
			}
			$mid = $v['mid'];
			$message_content = $message -> get_message_info($mid);
			if($message_content == 1324){
				echo $message_content;
				exit;
			}elseif($message_content == 1361){
				echo $message_content;			//缓存获取失败
				exit;
			}else{
				$message_list[$k] = $v + $message_content;
			}
		}
	}
		
	//exit;	
	//测试
	/* echo '<hr>';
	foreach($message_list as $k=>$v){
			print_r($v);
			echo '<hr>';
			//exit;
	} */
	
	
	
	
	
	
	
	
	
?>