<?php
	/*	
		将 数据库的数据 全部实现redis缓存	
	*/
	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_inc_test.php');
	require_once('./message.class.php');
	require_once('./message_redis.class.php');
	require_once('./redis.class.php');
	require_once('./redis_message.class.php');
	session_start();
	
	
	$message = new message;
	$redis = new redis_message;
	$user = new user;
	
	$user_cookie = $user -> get_cookie_user_info();
	//var_dump($cookie);
	//$message = new message;
	$user = $user_cookie['userid'];
	echo $user;
	//$friend = 45618;
	$friend = 100011;
	
	
	/* $value = array('name'=>'tianxing','color'=>'red');
	$key = 'stx_t';
	$result = $redis -> hmset($key, $value);
	if($result){
		$field = array('name','color');
		print_r($redis -> hmget($key, $field));
	}else{
		echo '缓存失败';
	}
	exit; */
	
	
	//第一 读取 私信内容表 数据到 redis缓存  hash 免去序列化 与反序列化
	//$key 私信ID   $value 私信数据详细信息数组
 	/* global $db_hyq_read;
	$sql = "SELECT * FROM hyq_message";
	$query = $db_hyq_read -> query($sql);
	$result = $db_hyq_read -> fetch_result($query); 
	 if($result){
		foreach($result as $k=>$v){
			$key = 'message:'.$v['id'];
			if(!$redis->exists($key)){
				//echo $key;
				//echo '<br>';
				$r = $redis -> hmset($key,$v);  //redis缓存  message
				//var_dump($r);
				//echo '<br>';
				$field = array('id','content','type','path_url','server_url');
				//print_r($redis->hmget($key, $field));
				$message_list[$k][] = $redis->hmget($key, $field);
				//echo '<hr>';
			}
		}
		//取缓存数据			私信内容缓存 	hash(哈希)  	已生产
	}else{
		echo '读取DB失败!';
	} 
	print_r($message_list);  //取缓存 到数组   OK
	exit;   */
	
	//最近联系人  存redis缓存
	
	
	
	
	
	
	
	
	//$user = 1103;
	///$friend = 1104;
	//$key = 'message:'.'*';
	
	
	$key = 'record:'.$user.'_'.'*';
	
	echo '我的私信队列的键:<br>';
	var_dump($list = $redis->keys($key));
	echo '<hr>';
	echo '好友的私信队列键的列表:<br>';
	$key = 'record:'.$friend.'_'.'*';
	var_dump($list = $redis->keys($key));
	echo '<hr>';
	echo '联系人集合的键:<br>';
	$key = 'contacts:'.'*';
	var_dump($list = $redis->keys($key));
	echo '<hr>';
	echo '私信的键:<br>';
	$key = 'message:'.'*';
	//var_dump($list = $redis->keys($key));
	echo '<hr>';
	$unread_num = $message -> get_unread_message_num($user);
	echo 'ID = '.$user.',未读私信总数:'.$unread_num;
	
	
	/* 
	foreach($list as $v){
		if($redis -> del($v)){
			echo 'ok';
		}else{
			echo 'NO';
		}
	}
	exit;
	 */
	
	

	echo '<br><br><br>';
	echo '读私信内容缓存!<br>';
	$key = 1;
	$key = 'message:'.$key;
	$field = array('id','content','type','path_url','server_url');
	print_r($redis->hmget($key, $field));
	echo '<hr><br><br>';
	
	
	
	 //清除缓存
/* 	if($redis -> del($key)){
		echo 'ok';
	}else{
		echo 'NO';
	} 
	exit; */
	/* 
	if($result){
		foreach($result as $k=>$v){
			$key = $v['id'];
			$key = 'message:'.$key;
			echo '<hr>';
			if($redis -> del($key)){
				echo 'ok';
			}else{
				echo 'NO';
			}
		}
	}else{
		echo '读取DB失败!';
	}
	exit;
	 */
	

	

	
	
	//第二 读取 私信记录表 数据到 redis缓存  list(队列) 
	//$key 私信队列键   $value 私信记录数据
	//$key = 'record:'.$user.'_'.$friend;			
	//$user = 1103;
	//$friend = 1104;
	$key = 'record:'.$user.'_'.$friend;			
	/* if($redis -> del($key)){
		echo 'ok';
	}else{
		echo 'NO';
	} 
	exit; */
	$message_list = $message -> get_message_list_by_user_friend($user, $friend,0,-1);
	//$message_list = $redis -> lrange($key, 0, 4);
	//var_dump($message_list);
	
	
	
	
	$len = $redis->llen($key);
	echo '私信记录队列的长度:'.$len;
	echo '<hr>';
	echo '验证索引<hr>';				//操作队列数据 by index
	$index = 0;
	print_r($content = $redis -> lindex($key, $index));
	echo '<hr>';
	echo '索引号为'.$index.'的队列内容:<br>';
	print_r($content = unserialize($content));
	echo '<hr>';
	echo '修改指定索引'.$index.'的队列里的元素值:<br>';
	$content['is_show'] = 0;
	print_r($content);
	//$redis -> lset($key, 0)
	echo '<hr><br><br><br>';

	
	
	for($i=0;$i<100;$i++){
		if(isset($message_list[$i]) || !empty($message_list[$i])){
			//var_dump($message_list[$i]);
			echo '索引号'.$i.'<br>';
			if(is_array($message_list[$i])){
				echo '读私信记录数据库!<br>';
			}else{
				echo '读私信记录缓存数据!<br>';				//取私信记录缓存  队列缓存已生产
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
	
	
	/*
	if($message_list){
		foreach($message_list as $k=>$v){
			$user = $v['user'];
			$friend = $v['friend'];
			$key = 'message_record:'.$user.'_'.$friend;
			//echo $key;
			
			
			
			//删除缓存
			
			if($redis -> del($key)){
				echo 'ok';
			}else{
				echo 'NO';
			}
			 
		}
	}else{
		echo '读取DB失败!';
	}
	*/
	echo '<br><br><br>';
	
	//第三 读取 最近联系人表 数据到 redis缓存  Set(集合) 
	//$key 联系人集合的键   $value 私信记录数据
	//$key = $user			以通信双方为单位的队列缓存
	
	//$user = 1103;
	$key = 'contacts:'.$user;
	/* 
	if($redis -> del($key)){
		echo 'ok';
	}else{
		echo 'NO';
	}
	exit;
	 */
	if($redis->exists($key)){
		echo '读联系人缓存数据!';
		echo '<hr>';
		print_r($result = $redis->srandmember($key));
		echo '<hr>';
		print_r($result = unserialize($redis->srandmember($key)));
		echo '<hr><br><br>';
		print_r('集合成员数目:'.$size = $redis->ssize($key));
		echo '<hr>';
		
		$r = $redis->smembers($key);	//联系人集合全部成员
		foreach($r as $k=>$v){
			print_r($k.' === '.$v);
			echo '<br>';
			print_r(unserialize($v));
			echo '<br><hr>';
		}
		echo '<br><br><br>';
	}else{
		echo '读联系人数据库数据<br>';
		$contacts_list = $message -> get_user_contacts_list_by_user($user);
		if($contacts_list == 1323 || $contacts_list == 1354){
			echo $contacts_list;
			exit;
		}else{
			 
			foreach($contacts_list as $k => $v){
				if($key = $user.'_'.$friend){
					$v = serialize($v);
					$r = $redis->sadd($key, $v);
					if(!$r){
						echo '集合生产失败';
						$redis -> del($key);		//清除缓存
						exit;
					}
				}
			}
			
			echo '联系人集合生产完成';
			echo '<hr>';
			print_r($result = $redis->srandmember($key));
			echo '<hr>';
			print_r($result = unserialize($redis->srandmember($key)));
			echo '<br><br><hr>';
			print_r('集合成员数目:'.$size = $redis->ssize($key));
			echo '<hr><br><br>';
			
			$r = $redis->smembers($key);		//联系人缓存 集合 生产完成
			foreach($r as $k=>$v){
				print_r($k.' === '.$v);
				echo '<br>';
				print_r(unserialize($v));
				echo '<br><hr>';
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	