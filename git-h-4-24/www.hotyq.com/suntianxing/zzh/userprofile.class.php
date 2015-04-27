<?php
	/*
	*    userprofile 用户详细信息操作类	
	*/
class userprofile{
	//获取个人用户的profile信息
	public function get_user_profile($uid){
		global $db_hyq_read;	//读取数据
		if($uid < 1){
			return 1099;
			exit;
		}		
 		$sql = "SELECT  id,uid,sex,age,star,blood,height,weight,bust,waist,hips,hope_role,state,province_id,city_id,district_id,native_province_id,native_city_id,native_district_id,school,finish_year,specialty,education FROM hyq_user_profile WHERE uid = {$uid}";
		$userlist=$db_hyq_read->fetch_array($db_hyq_read->query($sql));
		//$userlist=$this->get_hyq_sql_array($sql,1);//
		return $userlist;
	}
	
	 //更新个人的profile信息    $user_profile_array是传入数组user_profile信息
	public function update_user_profile($uid,$user_profile_array=array()){
		global $db_hotyq_read;	//读取数据
		if($uid < 1){
			return 1099;
			exit;
		}	
		if(!is_array($user_profile_array)){
			return 1099;
			exit;
		}
		global $db_hyq_write;
			$ip = getIP();
			//修改orgrinfo表 资料的SQL语句
			$sql =" UPDATE hyq_user_profile SET ";							
			foreach($s as $k => $v){
				$sql .= $k.'='.'"'.$v.'"'.',';				
			}		
			$sql = rtrim($sql,',');
			$sql .= ",ip = '{$ip}',modi_date = now() ";
			$sql .= "WHERE uid= {$uid}";			
			//var_dump($sql);						
			$result = $db_hyq_write->query($sql);//修改数据库信息
			if($result){
				return 1000;
			}else{
				return 1112;
			}
	}
	//更新封面地址	
	public function update_cover($uid,$cover_path_url){
		global $db_hyq_write,$IMG_WWW;
		$sql = "UPDATE hyq_user_profile SET cover_path_url = '{$cover_path_url}',cover_server_url = '{$IMG_WWW}',modi_date = now() WHERE uid = '{$uid}'";
		$query = $db_hyq_write -> query($sql);
		if($query){
			return true;
		}else{
			return false;//更新封面地址失败
		}
	}

					/* 设置红标签  用户标签处理   suntianxing  start */
	//获取标签列表一级分类   个人
	public function get_user_tag_first(){
		global $db_hyq_read;
		$sql = "SELECT * FROM hyq_tag WHERE parent_id = 0 AND class = 1"; 
		$query = $db_hyq_read -> query($sql);
		if($result = $db_hyq_read -> fetch_result($query)){
			return $result;
		}else{
			return 1301;   // 标签一级分类查询失败
		}
	}
	
	//获取一级分类下的二级分类 $parent_id 标签父级id
	public function get_user_tag_second($parent_id){
		global $db_hyq_read;
		$sql = "SELECT * FROM hyq_tag WHERE parent_id= {$parent_id} AND class = 1";
		$query = $db_hyq_read -> query($sql);
		if($result = $db_hyq_read -> fetch_result($query)){
			return $result;
		}else{
			return 1301;   // 标签二级分类查询失败
		}
	}
	
	//添加个人用户选择的标签 $uid 用户id $person_tag 用户选择的标签id
	public function add_user_tag($uid, $tagid){
		global $db_hyq_write;
		$sql = "INSERT INTO hyq_e_tag SET 
				uid = '{$uid}',
				tag_id = '{$tagid}'";
		if($db_hyq_write -> query($sql)){
			return 1000;
		}else{
			return 1302;   //关联表添加标签id失败
		}
	}
	
	//添加个人用户的自定义标签 插入标签库  $class 标签用户类别  $tagname 用户自定义标签名
	public function add_user_self_tag($class, $tagname){
		global $db_hyq_write;
		$dt = date('Y-m-d H:i:s',time());
		$agent = $_SERVER['HTTP_USER_AGENT'];   		//浏览器信息
		$ip = getIP();
		$sql = "INSERT INTO hyq_tag SET 
				name = '{$tagname}',
				parent_id = '-1',
				class = '{$class}',
				add_ip = '{$ip}',
				agent = '{$agent}',
				add_time = '{$dt}'";
		if($db_hyq_write -> query($sql)){
			$insert_id = $db_hyq_write -> insert_id();   //自定义标签插入 id
			return $insert_id;
		}
		/* else{
			return 1303;   //添加个人自定义标签失败
		} */
	}
	
	//读取用户已存的标签 $uid 当前用户的id
	public function get_user_tag_by_uid($uid){
		global $db_hyq_read;
		$sql = "SELECT * FROM hyq_e_tag where uid = '{$uid}'";
		$query = $db_hyq_read -> query($sql);
		if($results = $db_hyq_read -> fetch_result($query)){
			foreach($results as $k=>$v){
				$tid = $v['tag_id'];
				$sql = "SELECT * FROM hyq_tag WHERE id = '{$tid}'";
				$query = $db_hyq_read -> query($sql);
				if($r = $db_hyq_read -> fetch_array($query)){
					$result[$k] = $r; 
				}else{
					return 1305;    //用户标签内容查询失败 转换标签id失败
				}
			}	
			return $result;
		}else{
			return 1304;   //查询用户标签id失败 用户未设置标签
		}
	}
	
	//根据用户id 删除标签映射表中用户旧标签信息 $uid 用户id  $tagid 自定义标签的id数组
	public function delete_user_tag($uid, $tagid = array()){
		global $db_hyq_write;
		$sql = "DELETE FROM hyq_e_tag WHERE uid = '{$uid}'";   //删除关联表 标签信息
		$query = $db_hyq_write -> query($sql);
		if($query){
			if(!empty($tagid)){
				foreach($tagid as $v){
					$sql = "DELETE FROM hyq_tag WHERE id = '{$v}'";
					$query = $db_hyq_write -> query($sql);
					if(!$query){
						return 1307;	//自定义标签删除失败
					}
				}
			}
			return 1000;			//只有系统标签的时候删除成功
		}else{
			return 1306;  //用户标签关联表中记录删除失败
		}	
	}
	
	//删除自定义标签  单独删除  $uid 当前用户的id, $tagid被删除标签的id
	function delete_user_self_tag($uid,$tagid) {
		global $db_hyq_write;
		$sql = "DELETE FROM hyq_e_tag WHERE tag_id = '{$tagid}' AND uid = '{$uid}'";   //删除关联表 自定义标签
		$query = $db_hyq_write -> query($sql);
		if($query){
			$sql = "DELETE FROM hyq_tag WHERE id = '{$tagid}'";
			$query = $db_hyq_write -> query($sql);
			if($query){
				return 1000;	
			}else{
				return 1309;	// 自定义标签删除失败
			}
		}else{
			return 1308;  //用户标签关联表中自定义标签id删除失败
		}		
	}

					/* 用户标签处理  end */
	
	
	
	
	
	
	
	
	
	



	
	 //设置红角色
	public function update_user_roles($uid,$role_id_arr){

	}
	  //增加自定义角色
	public function add_role_user_self($uid,$role_info){

	}
	 //修改自定义角色
	public function update_role_user_self($self_role_info){

	} 
	 //删除自定义角色
	public function delete_role_user_self($self_role_id){
		
	} 
}
?>