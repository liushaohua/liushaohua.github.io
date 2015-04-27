<?php
/*
 * message_redis 消息reids操作类
 * 作者：suntianxing
 * 添加时间：2014-11-26
*/
class message_redis{
	private $redis_class; 
	function __construct($config = array()){}
	
	private function new_redis_class(){
		if(!$this->redis_class){
			$this->redis_class = new redis_message();  //实例化redis_message类
		}
	}
	
	
	// 发私信  新私信存入缓存
	public function redis_add_message($user, $friend, $message, $type, $insert_id, $dt){
		$this -> new_redis_class();
		global $IMG_WWW;
		$send_agent = $_SERVER['HTTP_USER_AGENT'];			//浏览器信息
		$send_ip = getIP();
		$key = 'message:'.$insert_id;
		if($type == 0){
			$value = array('id'=>$insert_id,'content'=>$message,'user'=>$user,'path_url'=>'','server_url'=>'','type'=>$type,'send_time'=>$dt,'send_ip'=>$send_ip,'send_agent'=>$send_agent);
		}else if($type == 1){
			$value = array('id'=>$insert_id,'content'=>'','user'=>$user,'path_url'=>$message,'server_url'=>$IMG_WWW,'type'=>$type,'send_time'=>$dt,'send_ip'=>$send_ip,'send_agent'=>$send_agent);
		}

		$result = $this->redis_class->hmset($key, $value);
		if($result){
			return 1000;
		}else{
			return 1360;		//入队失败
		} 
	} 
	
	// 获得消息内容 缓存
	public function redis_get_message_info($mid){
		$this -> new_redis_class();
		$key = 'message:'.$mid;
		if($this->redis_check_key($key)){
			$field = array('id','content','type','path_url','server_url');
			$result = $this->redis_class->hmget($key, $field);
			if($result){
				return $result;
			}else{
				return 1361;		//缓存获取失败
			}
		}else{
			return 1362;			//私信 没有缓存
		}
	}
	
 	// 获得未读消息数 集合set
	public function redis_get_unread_message_num($user){
		$this -> new_redis_class();
		//$key = 'unread_num:'.$user;
		$result = $this->redis_get_user_contacts_list_by_user($user);
		if($result){
			$unread_num = 0;
			foreach($result as $v){
				$v = unserialize($v);
				$unread_num += $v['unread_num'];
			}
			return $unread_num;
		}else{
			return false;
		}
	}
	
	// 更新联系人信息，增加未读消息数 更新最后联系时间  set
	public function redis_update_contacts_set($user, $friend, $mid, $dt){  // 1007  1314
		$this -> new_redis_class();
		$m_key = 'contacts:'.$user;
		$result = $this->redis_get_user_contacts_list_by_user($user);
		/* redis缓存错误处理 */
		if(!$result){
			return $result;
		}
		foreach($result as $k=>$v){
			$v = unserialize($v);
			if($v['user'] == $user && $v['friend'] == $friend){
				$vv = serialize($v);
				//更新redis  删除以前的 保存当前的  集合set
				$r = $this->redis_class->srem($m_key,$vv);
				/* 错误处理判断 */
				$v['last_mid'] = $mid;
				$v['last_time'] = $dt;
				$v['unread_num'] = 0;    //将自己的未读私信书置零
				$v = serialize($v);
				$rr = $this->redis_class->sadd($m_key, $v);
				/* 错误处理判断 */
				if(!$rr){
					return 1369;
				}
			}
			/* 联系人缓存处理 */
		}
		//检测对方的联系人缓存是否存在 存在的时候更新缓存 不存在的时候 不做处理
		$f_key = 'contacts:'.$friend;
		//if($this->redis_check_key($f_key)){
			$result = $this->redis_get_user_contacts_list_by_user($friend);
			/* redis缓存错误处理 */
			if(!$result){
				return $result;
			}
			foreach($result as $k=>$v){
				$v = unserialize($v);
				if($v['user'] == $friend && $v['friend'] == $user){
					$vv = serialize($v);
					//更新redis  删除以前的 保存当前的  集合set
					$r = $this->redis_class->srem($f_key,$vv);
					/* 错误处理判断 */
					
					$v['last_mid'] = $mid;
					$v['last_time'] = $dt;
					$v['unread_num'] = $v['unread_num']+1;
					$v = serialize($v);
					$rr = $this->redis_class->sadd($f_key, $v);
					/* 错误处理判断 */
					if(!$rr){
						return 1369;
					}
				}
				/* 联系人缓存处理 */
				 
			}
		//}
	}
	
	//新发私信加入 私信对话 队列 的尾部  同步操作
	public function redis_add_e_message_by_user($user, $friend, $insert_id, $dt, $w_insert_id){
		$this -> new_redis_class();
		$key = 'record:'.$user.'_'.$friend;
		$value = array('id'=>$w_insert_id,'user'=>$user,'friend'=>$friend,'sender'=>$user,'receiver'=>$friend,'mid'=>$insert_id,'send_time'=>$dt,'read_time'=>$dt,'is_show'=>'1');
		
		$message_list = serialize($value);
		$result = $this->redis_class -> lpush($key, $message_list);
		if($result){
			return 1000;
		}else{
			return 1358;		 //value缓存失败
		}
		
	}
	
	//新发私信加入 私信对话 队列 的尾部  同步操作
	public function redis_add_e_message_by_friend($user, $friend, $insert_id, $dt, $w_insert_id){
		$this -> new_redis_class();
		$key = 'record:'.$friend.'_'.$user;
		
		$value = array('id'=>$w_insert_id,'user'=>$friend,'friend'=>$user,'sender'=>$user,'receiver'=>$friend,'mid'=>$insert_id,'send_time'=>$dt,'read_time'=>'','is_show'=>'1');
	
		$message_list = serialize($value);
		$result = $this->redis_class -> lpush($key, $message_list);
		if($result){
			return 1000;
		}else{
			return 1358;		 //value缓存失败
		}
		
	}
	
	//redis缓存联系人信息获取 by set
	public function redis_get_user_contacts_list_by_user($user){
		$this -> new_redis_class();
		$key = 'contacts:'.$user;
		if($this->redis_check_key($key)){
			$contacts_list = $this->redis_class -> smembers($key);	//读取缓存
			return $contacts_list;
		}else{
			return false;
		}
	}
	
	//将联系人数据存入 redis key集合  KEY =>$key
	public function redis_update_user_contacts_last_message_first_to_user($user, $friend, $mid, $dt){
		$this -> new_redis_class();
		$key = 'contacts:'.$user;
		$u_value = array('friend'=>$friend,'user'=>$user,'last_mid'=>$mid,'unread_num'=>'0','last_time'=>$dt);	
		$value = serialize($u_value);
		
		if($this->redis_class->sadd($key, $value)){
			return 1000;
		}else{
			$this -> redis_class -> del($key);
			return 1354;
		}
		
	}
	
	//将联系人数据存入 redis key集合  KEY =>$key
	public function redis_update_user_contacts_last_message_first_to_friend($user, $friend, $mid, $dt){
		$this -> new_redis_class();
		$key = 'contacts:'.$friend;
		//if($this->redis_check_key($key)){
			$f_value = array('friend'=>$user,'user'=>$friend,'last_mid'=>$mid,'unread_num'=>'1','last_time'=>$dt);
			$value = serialize($f_value);
			
			if($this->redis_class->sadd($key, $value)){
				return 1000;
			}else{
				$this -> redis_class -> del($key);
				return 1354;
			}
		//}
	}
	
	//将联系人数据存入 redis key集合  KEY =>$key
	public function redis_add_contacts_set($user, $v){
		$this -> new_redis_class();
	
		$key = 'contacts:'.$user;
		//序列化数据	
		$value = serialize($v);
		
		if($this->redis_class->sadd($key, $value)){
			return true;
		}else{
			$this -> redis_class -> del($key);
			return false;
		}
		
	}
	
	//更新用户缓存私信状态 
	public function redis_update_unread_message_by_user_friend($user, $friend, $dt){  // 1007   1314
		$this -> new_redis_class();
		$list_key = 'record:'.$user.'_'.$friend;
		$set_key = 'contacts:'.$user;
		
		$message_list = $this->redis_class ->lrange($list_key, 0, -1);
		$contacts_list = $this->redis_class ->smembers($set_key);
		//二种列表必须都成功  修改缓存才算成功 故只分二张情况
		if($message_list && $contacts_list){
			//修改记录的缓存队列的未读私信的read_time
			foreach($message_list as $k=>$v){
				$v = unserialize($v);
				if($v['read_time'] == null){
					$v['read_time'] = $dt;
					$v = serialize($v);
					$result1 = $this -> redis_class->lset($list_key, $k, $v);
					if(!$result1){
						return 1355;		//未读消息 已读时间更新失败
						exit;
					}
				}	
			}
			//修改未读消息数
			foreach($contacts_list as $kk=>$vv){
				$vv = unserialize($vv);
				if($vv['friend'] == $friend){
					//先移除集合中已有成员 然后插入更新后的
					$vv1 = serialize($vv);
					$result2 = $this->redis_class->srem($set_key,$vv1);
					$vv['unread_num'] = 0;
					$vv = serialize($vv);
					$result3 = $this->redis_class->sadd($set_key, $vv);
					//return $result3;
					if(!$result2 || !$result3){
						return 1356;		//未读消息数 置0 失败
						exit;
					}
				}
			}
			return 1000;
		}else{
			return 1357;			//未读私信状态 读取失败
		}	
	}
	
	// 全部未读消息置已读
	public function redis_update_unread_message_by_user($user, $dt){
		$this -> new_redis_class();
		$key = 'record:'.$user.'_'.'*';
		$contacts_key = 'contacts:'.$user;
		//获取keys
		$result = $this->redis_class ->keys($key);
		//联系人列表
		$contacts_list = $this->redis_class ->smembers($contacts_key);
		if($result && $contacts_list){
			foreach($result as $v){
				$message_list = $this->redis_class ->lrange($v, 0, -1);
				/* 错误异常处理 */
				foreach($message_list as $kk=>$vv){
					$vv = unserialize($vv);
					if($vv['user'] == $user && $vv['receiver'] == $user){
						$vv['read_time'] = $dt;
						$vv = serialize($vv);
						$result1 = $this -> redis_class->lset($v, $kk, $vv);
						/* 错误异常处理 */
						if(!$result1){
							return 1355;		//全部未读消息 已读时间更新失败
							exit;
						}
					}
				}
			}
			
			//修改所有未读消息数 unread_num=>0 
			foreach($contacts_list as $kk=>$vv){
				$vv = unserialize($vv);
				if($vv['user'] == $user){
					//先移除集合中已有成员 然后插入更新后的
					$vv1 = serialize($vv);
					$result2 = $this->redis_class->srem($contacts_key,$vv1);
					$vv['unread_num'] = 0;
					$vv = serialize($vv);
					$result3 = $this->redis_class->sadd($contacts_key, $vv);
					//return $result3;
					if(!$result2 || !$result3){
						return 1356;		//全部未读消息数 置0 失败
						exit;
					}
				}
			}
			return 1000;
		}else{
			return 1365;			//未读消息置已读 失败
		}
		
	}
	
	//删除私信 缓存 is_show 字段的修改
	public function redis_hide_message($user, $friend, $index){
		$this -> new_redis_class();
		$key = 'record:'.$user.'_'.$friend;
		$result = $this->redis_class ->lindex($key, $index);
		if($result){
			$result = unserialize($result);
			$result['is_show'] = 0;
			$result = serialize($result);
			$r = $this->redis_class ->lset($key, $index, $result);
			if($r){
				return 1000;		//ok
			}else{
				return 1363;		//缓存修改失败
			}
		}else{
			return 1364;			//获取缓存失败
		}
	}
	
	//双方的私信记录Redis list  $user  当前用户的id 		
	public function redis_add_message_to_list_by_user_friend($user, $friend, $value){
		$this -> new_redis_class();
		$key = 'record:'.$user.'_'.$friend;
		//序列化一维数组  存入redis                                     
		$message_list = serialize($value);
		$result = $this->redis_class -> rpush($key,$message_list);
		if($result){
			return true;
		}else{
			$this -> redis_class -> del($key);  //value缓存失败 则清除已经缓存的数据
			return false;
		}
	}
	
	//查询双方的私信记录  
	public function redis_get_message_list_by_user_friend($user, $friend, $start = 0, $end = 9){
		$this -> new_redis_class();
		$key = 'record:'.$user.'_'.$friend;
		
		if($this->redis_check_key($key)){
			$message_list = $this->redis_class -> lrange($key, $start, $end);				//读取缓存
			if(empty($message_list)){    //索引越界 换回空队列
				return 1370;
			}else{
				return $message_list;
			}
		}else{
			return 1371;
		}
	}
	//获取队列的指定下标(索引)下的元素
	public function redis_list_index($key,$index){
		$this -> new_redis_class();
		$result = $this->redis_class -> lindex($key,$index);				//读取缓存
		if($result){
			return 1000;
		}else{
			return 1352;
		}	
	}
	//检查 指定的redis key  是否存在
	public function redis_check_key($key){
		$this -> new_redis_class();
		if($this->redis_class->exists($key)){
			return true;
		}else{
			return false;
		}
	}
	
	
	
	
	
	
	
	
}
?>