<?php
	/*
	*    userProfile 用户详细信息操作类	
	*/
class userprofile{
			//用户标签处理   stx  start
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

	
	
	
	
	
	
	
	
	
	
	
	
	/* -------------- 舍弃 ----------------- */
	
	//根据用户id获取标签信息 $tag_array 用户选择的标签id字符串
	public function get_user_tag($id_str){
		global $db_hyq_read;
		$sql = "SELECT * FROM hyq_tag WHERE is_show = 0 AND id IN({$id_str})";
		
		$query = $db_hyq_read -> query($sql);
		if($result = $db_hyq_read -> fetch_result($query)){
			return $result;
		}else{
			echo 1305;    //用户标签查询失败 转换标签id失败
		}
	} 
	
	//修改个人自定义标签的 is_show字段 $tag_id 自定义标签id
	public function update_tag_show($tag_id){
		global $db_hyq_write;
		$sql="UPDATE hyq_tag SET is_show = 1 WHERE id={$tag_id}";
		$query = $db_hyq_write -> query($sql);
		if($query){
			return 1000;
		}else{
			return 1306;  //自定义标签显示状态更新失败
		}
	}
	
	//判断标签操作 1:增加标签 add_per_tag 2：更新标签 update_per_tag
	public function is_set_tag($uid){
		global $db_hyq_read;
		$sql = "SELECT * FROM hyq_e_tag WHERE uid = {$uid}"; 
		$query = $db_hyq_read -> query($sql);
		if($result = $db_hyq_read -> fetch_result($query)){
			return 1000; 
		}else{
			return 1399;   // 机构用户标签查询失败  未设置过标签
		}
	} 
			//用户标签处理   stx  end
}
?>