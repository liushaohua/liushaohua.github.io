<?php
	/*
	*    Message 用户私信操作类	 stx  
	*/
class message{
	private $message_redis;
	function __construct($config = array()){}
	
	private function new_message_redis(){
		if(!$this->message_redis){
			$this->message_redis = new message_redis();  //实例化message_redis类
		}
	}
	
	// 用户私信内容存入及相关信息存入  $user 当前用户的id $friend 发送对象的id ,$message 私信内容 ,$type 私信类型
	public function add_message($user, $friend, $message, $type){
		$this -> new_message_redis();
		global $db_hyq_write,$IMG_WWW;
		$send_agent = $_SERVER['HTTP_USER_AGENT'];			//浏览器信息
		$send_ip = getIP();
		$dt = date('Y-m-d H:i:s',time());					//私信发送时间
		
		$sql = "INSERT INTO hyq_message SET ";
			if($type == 0){
				$sql .= "content = '{$message}',";
			}else if($type == 1){
				$sql .= "path_url = '{$message}',
					server_url = '{$IMG_WWW}',";
			}	
			$sql .= "user = '{$user}',
				type = '{$type}',
				send_ip = '{$send_ip}',
				send_agent = '{$send_agent}',
				send_time = '{$dt}'";
		if($db_hyq_write -> query($sql)){
			$insert_id = $db_hyq_write -> insert_id();			//新私信ID
			//更新私信内容缓存
			$result1 = $this->message_redis->redis_add_message($user, $friend, $message, $type, $insert_id, $dt);   //新私信更新存入缓存  hash
			/* 缓存错误的处理1 测试用*/
			//if($result1 == 1360){
				//return $result1;
				//exit;
			//}
			/* 向私信记录表插入记录 */
			$state_code = $this->add_e_message_by_user($user, $friend, $insert_id, $dt);
			return $state_code;
			/*
			if($state_code != 1000){	//操作失败返回错误码
				return $state_code;
			}else{
				return $insert_id;		//操作成功则返回新插入id 默认的返回值
			}
			*/
		}else{
			return 1320;				//私信发送失败
		}		
	}
	
	//增加新记录 到 私信记录表
	public function add_e_message_by_user($user, $friend, $insert_id, $dt){
		$this -> new_message_redis();
		global $db_hyq_write;
		$sql_u = "INSERT INTO hyq_e_message SET 
				user = '{$user}',
				friend = '{$friend}',
				sender = '{$user}',
				receiver = '{$friend}',
				mid = '{$insert_id}',
				send_time = '{$dt}',
				read_time = '{$dt}'";
				
		if($db_hyq_write -> query($sql_u)){
			//缓存当前用户的私信信息到 对话队列
			$u_insert_id = $db_hyq_write -> insert_id();
			
			$result2 = $this->message_redis->redis_add_e_message_by_user($user, $friend, $insert_id, $dt, $u_insert_id);   
			/* 缓存错误的处理2 测试用*/
			if($result2 == 1358){
				return $result2;
			}
			return $this -> add_e_message_by_friend($user, $friend, $insert_id, $dt);
		}else{
			return false; 
		}	
	}
	//增加新记录 到 私信记录表
	public function add_e_message_by_friend($user, $friend, $insert_id, $dt){
		$this -> new_message_redis();
		global $db_hyq_write;
		//$friend_id = $friend;
		//$user_id = $user;
		$sql_f = "INSERT INTO hyq_e_message SET 
			user = '{$friend}',
			friend = '{$user}',
			sender = '{$user}',
			receiver = '{$friend}',
			mid = '{$insert_id}',
			send_time = '{$dt}'";
			
		if($db_hyq_write -> query($sql_f)){			
			$f_insert_id = $db_hyq_write -> insert_id();
			
			$result3 = $this->message_redis->redis_add_e_message_by_friend($user, $friend, $insert_id, $dt, $f_insert_id);
			/* 缓存错误的处理3 测试用*/
			if($result3 == 1358){
				return $result3;
			}
			$state_code = $this -> update_user_contacts_last_message($user, $friend, $insert_id, $dt);
			return $state_code; 
		}else{
			return false;			//数据库写入失败
		}	
	}
	
	//更新最近联系人信息  $user当前用户的id,$firend 联系人id,$mid 最新插入私信id,$dt 私信发送时间
	public function update_user_contacts_last_message($user, $friend, $mid, $dt){   //1007  1314
		$this -> new_message_redis();
		global $db_hyq_read;
		$sql = "SELECT * FROM hyq_user_contacts WHERE user = '{$user}' AND friend = '{$friend}'";
		$query = $db_hyq_read -> query($sql);
		if($result = $db_hyq_read -> fetch_array($query)){
			global $db_hyq_write;			//联系人已存在  执行更新操作
			//发送私信的时候 更新自己的未读私信数为0
			$u_sql = "UPDATE hyq_user_contacts SET last_mid = '{$mid}',unread_num= '0',last_time = '{$dt}' WHERE friend = '{$friend}' AND user = '{$user}'";
			
			$f_sql = "UPDATE hyq_user_contacts SET last_mid = '{$mid}',unread_num = unread_num+1,last_time = '{$dt}' WHERE friend = '{$user}' AND user = '{$friend}'";
			
			if($db_hyq_write -> query($u_sql) && $db_hyq_write -> query($f_sql)){
				//更新联系人缓存信息
				$r = $this->message_redis->redis_update_contacts_set($user, $friend, $mid, $dt);
				/* 错误异常处理 测试用*/
				if($r == 1369 || $r == 1353){
					return $r;
				}
				return 1000;			//最近联系人信息更新成功
			}else{
				return 1321;			//最近联系人信息更新失败
			}
		}else{
			/* 联系人不存在  执行添加联系人操作 */
			return $this -> update_user_contacts_last_message_first_to_user($user, $friend, $mid, $dt);
		}	
	}
	//联系人不存在  执行添加操作  把对方插入自己的联系人
	public function update_user_contacts_last_message_first_to_user($user, $friend, $mid, $dt){
		global $db_hyq_write;			
		$u_sql = "INSERT INTO hyq_user_contacts SET 
			friend = '{$friend}',
			user = '{$user}',
			last_mid = '{$mid}',
			last_time = '{$dt}'";
			
		if($db_hyq_write -> query($u_sql)){
			//联系人不存在时 缓存的处理
			//当前用户 更新最近联系人信息 集合
			$result1 = $this->message_redis->redis_update_user_contacts_last_message_first_to_user($user, $friend, $mid, $dt);
			/* 缓存错误的处理 测试用*/
			if($result1 == 1354){
				//return $r;
				exit('集合元素添加失败');		
			}
			return $this -> update_user_contacts_last_message_first_to_friend($user, $friend, $mid, $dt);
		}else{
			return 1322;			//添加联系人失败
		}
	}
	//联系人不存在  执行添加操作  把自己插入对方的联系人
	public function update_user_contacts_last_message_first_to_friend($user, $friend, $mid, $dt){
		global $db_hyq_write;			
		$f_sql = "INSERT INTO hyq_user_contacts SET 
				friend = '{$user}',
				user = '{$friend}',
				last_mid = '{$mid}',
				unread_num = '1',
				last_time = '{$dt}'";
				
		if($db_hyq_write -> query($f_sql)){
			//更新对方的的最近联系人信息 集合
			$result2 = $this->message_redis->redis_update_user_contacts_last_message_first_to_friend($user, $friend, $mid, $dt);
			/* 缓存错误的处理 测试用*/
			if($result2 == 1354){
				//return $r;
				exit('集合元素添加失败');		//测试用
			}
			
			return 1000;			//添加联系人成功
		}else{
			return 1322;			//添加联系人失败
		}
	}

	//私信列表首页   $user 当前用户的id 
	public function get_user_contacts_list_by_user($user){
		$this -> new_message_redis();
		$result = $this->message_redis-> redis_get_user_contacts_list_by_user($user);
		
		if(!$result){
			//通过数据库获取数据信息
			global $db_hyq_read;
			$sql1 = "SELECT * FROM hyq_user_contacts WHERE user = '{$user}' ORDER BY unread_num DESC,last_time DESC";
			$query1 = $db_hyq_read -> query($sql1);
			$result1 = $db_hyq_read -> fetch_result($query1);
			
			$sql2 = "SELECT * FROM hyq_user_contacts WHERE friend = '{$user}' ORDER BY unread_num DESC,last_time DESC";
			$query2 = $db_hyq_read -> query($sql2);
			$result2 = $db_hyq_read -> fetch_result($query2);
			if($result1 && $result2){
				$result = array_merge($result1,$result2);
				foreach($result as $k=>$v){
					if($v['user'] == $user){
						$r = $this->message_redis->redis_add_contacts_set($user, $v);//缓存数据  集合
						/* 缓存错误的处理 测试用*/
						if(!$r){
							//echo '集合生产失败';
							return $r;
							//exit('集合生产失败');
						}
					}
				}
				return $result;	//非序列化数据  数组
			}else{
				return false;   //获取未读私信数失败
			}
		}else{
			return $result;		//序列化的数据
		}	
	}

	//获取私信的内容 $mid  私信id
	public function get_message_info($mid){
		$this -> new_message_redis();
		
		$result = $this -> message_redis -> redis_get_message_info($mid);
		if($result == 1361){
			return $result;
		}else if($result == 1362){
			global $db_hyq_read;
			//$sql = "SELECT content,type,path_url,server_url FROM hyq_message WHERE id = {$mid}";
			$sql = "SELECT content,type,path_url,server_url FROM hyq_message WHERE id = '{$mid}'";
			$query = $db_hyq_read -> query($sql);
			$result = $db_hyq_read -> fetch_array($query);
			if($result){
				return $result;
			}else{
				return 1324;			//读取私信内容失败
			}
		}else{
			return $result;
		}
	}
	
	//查询双方的私信记录(当前用户)   $user  当前用户的id $friend 联系人id
	public function get_message_list_by_user_friend($user, $friend, $start, $end){
			$this -> new_message_redis();
			//读取rdis缓存  操作
			$result = $this -> message_redis -> redis_get_message_list_by_user_friend($user, $friend, $start, $end);
			if($result == 1371){ 
				//数据库操作
				global $db_hyq_read;
				$sql = "SELECT * FROM hyq_e_message WHERE user = '{$user}' AND  friend = '{$friend}' AND is_show = '1' ORDER BY send_time DESC";
				$query = $db_hyq_read -> query($sql);
				$result = $db_hyq_read -> fetch_result($query);
				if($result){
					foreach($result as $k=>$v){
						//第一次 缓存当前用户私信记录队列
						$r = $this -> message_redis ->redis_add_message_to_list_by_user_friend($user, $friend, $v);
						/* 缓存错误的处理 测试用*/
						if(!$r){
							return 1351;		//存缓存失败 1351
						}	
					}
					//生成对方联系人 与自己的私信对话记录队列
					$r = $this -> get_message_list_by_friend_user($friend, $user, $start, $end);
					/* 缓存错误的处理 测试用*/
					/* if(!$r){
						return 1351;
					} */
					return $result;
				}else{
					return 1325;			//读取私信记录失败
				}
			}else if($result == 1370){
					return 1370;						//索引越界 无返回
			}else{
				return $result;			//读缓存数据成功
			}
	}
	
	//查询联系人 与当前用户的私信记录 并做联系人的私信记录 队列缓存
	public function get_message_list_by_friend_user($friend, $user, $start, $end){
		$this -> new_message_redis();
		$result = $this -> message_redis -> redis_get_message_list_by_user_friend($friend, $user, $start, $end);
		if(!$result){ 
			global $db_hyq_read;
			$sql = "SELECT * FROM hyq_e_message WHERE user = '{$friend}' AND  friend = '{$user}' AND is_show = '1' ORDER BY send_time DESC";
			//获取联系人与当前用户的私信记录
			$query = $db_hyq_read -> query($sql);
			$result = $db_hyq_read -> fetch_result($query);
			if($result){
				foreach($result as $k=>$v){
					//第一次 缓存对方私信记录队列
					$r = $this -> message_redis ->redis_add_message_to_list_by_user_friend($friend, $user, $v);
					/* 缓存错误的处理 测试用*/
					if(!$r){
						return 1351;		//存缓存失败 1351
					}	
				}
			}
		}
		/* else{
			// 对方与自己的私信记录缓存已存在 则不做操作 
		} */
	}
	
	//统计收到的消息总数 $user 当前用户的id   	
	public function get_unread_message_num($user){
		$this -> new_message_redis();
		//从缓存取当前用户的未读私信总数
		$unread_num = $this -> message_redis -> redis_get_unread_message_num($user);
		if($unread_num){
			return $unread_num;
		}else{
			//从数据库取未读私信总数
			global $db_hyq_read;
			$sql = "SELECT unread_num FROM hyq_user_contacts WHERE user = '{$user}'";
			$query = $db_hyq_read -> query($sql);
			$result = $db_hyq_read -> fetch_result($query);
			if($result){
				$unread_num = 0;
				foreach($result as $v){
					$unread_num += $v['unread_num'];
				}
				return $unread_num;
			}else{
				return false;			//获取私信总数失败
			}
		}
	}
	
	
	
	//更新未读私信状态为已读状态 read_time = null  => read_time = now()  $user 当前用户id  $friend 对方的id
	//更新未读私信数为0 unread_num = 0
	public function update_unread_message_by_user_friend($user, $friend){   //1007   1314
		$this -> new_message_redis();
		//修改数据库字段
		global $db_hyq_write;
		$dt = date('Y-m-d H:i:s',time());
		$update_status_sql = "UPDATE hyq_e_message SET read_time = '{$dt}' WHERE user = '{$user}' AND  sender = '{$friend}' AND receiver = '{$user}'";
		//return $update_status_sql;
		$update_unread_num_sql = "UPDATE hyq_user_contacts SET unread_num = '0' WHERE friend = '{$friend}'  AND user = '{$user}'";
		//return $update_unread_num_sql;
		if($db_hyq_write -> query($update_status_sql) && $db_hyq_write -> query($update_unread_num_sql)){
			
			//修改缓存私信状态
			$update_redis_list = $this->message_redis->redis_update_unread_message_by_user_friend($user, $friend,$dt);
			/* 缓存错误的处理 测试用*/
			if($update_redis_list == 1000){
				return 1000;		//私信状态更新成功
			}else if($update_redis_list == 1355){
				return 1355;
			}else if($update_redis_list == 1356){
				return 1356;
			}else if($update_redis_list == 1357){
				return 1357;
			}
		}else{
			return 1328;			//私信状态信息更新失败
		}
	}
	
	//根据私信id改变自己的私信显示（表现为删除）   更改私信的显示状态 并没有真的删除数据  
	public function hide_message($user, $friend, $mid, $index){
		$this -> new_message_redis();
		global $db_hyq_write;
		$sql = "UPDATE hyq_e_message SET is_show = '0' WHERE user = '{$user}' AND friend = '{$friend}' AND mid = '{$mid}'";
		$query = $db_hyq_write -> query($sql);
		if($query){
			//修改缓存队列的私信显示状态  is_show = 0
			$result = $this->message_redis->redis_hide_message($user, $friend, $index);
			/* 缓存错误的处理 测试用*/
			if($result == 1363 || $result == 1364){
				return $result;		//缓存修改失败时的 错误处理  ???????
			}else{
				return 1000;			//私信显示状态更新成功(DB与redis都ok)
			}	
		}else{
			return 1329;				//私信显示状态更新失败
		}
	}
	
	//置当前用户的所有未读私信为已读  read_time = null  => read_time = now()  $user 当前用户id
	public function update_unread_message_by_user($user){
		$this -> new_message_redis();
		global $db_hyq_write; 
		$dt = date('Y-m-d H:i:s',time());
		$change_read_time_sql = "UPDATE hyq_e_message SET read_time = '{$dt}' WHERE user = '{$user}' AND receiver = '{$user}'";
		$change_unread_num_sql = "UPDATE hyq_user_contacts SET unread_num = '0' WHERE user = '{$user}'";

		if($db_hyq_write -> query($change_read_time_sql) && $db_hyq_write -> query($change_unread_num_sql)){
			//全部置已读  缓存 更新操作
			$result = $this->message_redis->redis_update_unread_message_by_user($user, $dt);
			/* 缓存错误的处理 测试用*/
			if($result == 1365){
				return $result;
			}
			return 1000;	//全部置已读成功
		}else{
			return 1330;   //全部置已读失败
		}
	}
	
	//检查用户是否使用过私信
	public function get_message_list_by_user($uid){
		global $db_hyq_read;
		$sql = "SELECT * FROM hyq_message WHERE user = '{$uid}'";
		$query = $db_hyq_read -> query($sql);
		$result = $db_hyq_read -> fetch_result($query);
		if($result){
			return $result;			
		}else{
			return false;
		}
	}
	
	
	

}

?>