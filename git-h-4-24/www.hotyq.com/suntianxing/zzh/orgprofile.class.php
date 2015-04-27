<?php
	/* 
	*    orgprofile 机构详细信息操作类	
	*/
		
class orgprofile{
	//查询数据的资料
  	public function get_org_profile($uid){
		global $db_hyq_read;	//读取数据
		if($uid < 1){
			return 1099;
			exit;
		}
 		$sql = "SELECT  id,uid,create_time,province_id,city_id,district_id,type,state,introduce,production,heeler,honor,business_num,legal_person FROM hyq_org_profile WHERE uid = {$uid}";
		$orglist=$db_hyq_read->fetch_array($db_hyq_read->query($sql));
		//$userlist=$this->get_hyq_sql_array($sql,1);
		return $orglist;
		//var_dump($userlist);
  	}
	 //更新机构的profile信息 	$org_profile_array是传入数组org_profile信息
	public function update_org_profile($uid,$org_profile_array=array()){
		if($uid < 1){
			return 1099;
			exit;
		}			
		if(!is_array($org_profile_array)){
			return 1099;
			exit;
		}	
		global $db_hyq_write;
		$ip = getIP();
		//修改orgrinfo表 资料的SQL语句
		$sql =" UPDATE hyq_org_profile SET ";							
		foreach($org_profile_array as $k => $v){
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

	//分两种情况（1.直接上传新logo/2.红照片设置成logo）（照片处理在photo类，数据库操作在此处）
	public function update_cover($uid,$cover_path_url){
		global $db_hyq_write,$IMG_WWW;
		$sql = "UPDATE hyq_org_profile SET cover_path_url = '{$cover_path_url}',cover_server_url = '{$IMG_WWW}',modi_date = now() WHERE uid = '{$uid}'";
		$query = $db_hyq_write -> query($sql);
		if($query){
			return true;
		}else{
			return false;//更新封面地址失败
		}
	}
	
			/* 机构标签操作  suntianxing 	start */
	
	//获取机构系统标签的列表
	public  function get_org_tag_list(){
		global $db_hyq_read;
		$sql = "SELECT * FROM hyq_tag WHERE parent_id = 0 AND class = 2"; 
		$query = $db_hyq_read -> query($sql);
		if($result = $db_hyq_read -> fetch_result($query)){
			return $result;
		}else{
			return 1311;   // 机构标签查询失败
		}
	}
	
	//添加机构用户的自定义标签 插入标签库  $class 标签用户类别  $tagname 用户自定义标签名
	public function add_org_self_tag($class, $tagname){
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
			return 1313;   //添加个人自定义标签失败
		} */
	}
	
	//添加机构用户选择的标签到关联表 $uid 用户id, $org_tag 机构用户选择的标签id
	public function add_org_tag($uid, $org_tag){
		global $db_hyq_write;
		$sql = "INSERT INTO hyq_e_tag SET 
				uid = '{$uid}',
				tag_id = '{$org_tag}'";
		if($db_hyq_write -> query($sql)){
			return 1000;
		}else{
			return 1312;   //机构添加标签失败
		}
	}
	
	//读取用户已存的标签 $uid 当前用户的id
	public function get_org_tag_by_uid($uid){
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
					return 1315;    //用户标签内容查询失败 转换标签id失败
				}
			}	
			return $result;
		}else{
			return 1314;   //查询用户标签id失败 用户未设置标签
		}
	}
	
	//根据用户id 删除标签映射表中用户旧标签信息 $uid 用户id  $tagid 自定义标签的id数组
	public function delete_org_tag($uid, $tagid = array()){
		global $db_hyq_write;
		$sql = "DELETE FROM hyq_e_tag WHERE uid = '{$uid}'";   //删除关联表 标签信息
		$query = $db_hyq_write -> query($sql);
		if($query){
			if(!empty($tagid)){
				foreach($tagid as $v){
					$sql = "DELETE FROM hyq_tag WHERE id = '{$v}'";
					$query = $db_hyq_write -> query($sql);
					if(!$query){
						return 1317;	//自定义标签删除失败
					}
				}
			}
			return 1000;			//只有系统标签的时候删除成功
		}else{
			return 1316;  //用户标签关联表中记录删除失败
		}	
	}
	
	//删除自定义标签  单独删除
	function delete_org_self_tag($uid,$tagid) {
		global $db_hyq_write;
		$sql = "DELETE FROM hyq_e_tag WHERE tag_id = '{$tagid}' AND uid = '{$uid}'";   //删除关联表 自定义标签
		$query = $db_hyq_write -> query($sql);
		if($query){
			$sql = "DELETE FROM hyq_tag WHERE id = '{$tagid}'";
			$query = $db_hyq_write -> query($sql);
			if($query){
				return 1000;	
			}else{
				return 1319;	// 自定义标签删除失败
			}
		}else{
			return 1318;  //用户标签关联表中自定义标签id删除失败
		}		
	}
	
	
			/* 机构标签操作   ssuntianxing	 end */
	
	

	  //增加红艺人
	function add_artist($uid,$name,$description){	
		if($uid < 1){
			return 1099;
			exit;
		}	
		$sql="INSERT INTO hyq_artists SET name = '{$name}',description = '{$description}' WHERE uid = '{$uid}'";  	
		if($db_hyq_write -> query($sql)){
			return true;
		}else{
			return false;
		}
	}
	 //修改红艺人
	function update_artist($uid,$name,$description){
		if($uid < 1){
			return 1099;
			exit;
		}		
		$sql="UPDATE hyq_artists SET name = '{$name}',description = '{$description}' WHERE uid = '{$uid}'";  
		if($db_hyq_write -> query($sql)){
			return true;
		}else{
			return false;
		}
	}
	//删除红艺人	
	function delete_artist($id){
		if($id < 1){
			return 1099;
			exit;
		}		
		$sql = "DELETE FROM hyq_artists WHERE id = '{$id}'";
		if($db_hyq_write -> query($sql)){
			return true;
		}else{
			return false;
		}	
	}  
	
}
?>