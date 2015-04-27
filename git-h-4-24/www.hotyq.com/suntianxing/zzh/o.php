<?php
class orgprofile{
	//获取机构用户的orgprofile信息	zzh
  	public function get_org_profile($uid){
		global $db_hyq_read;
		if($uid < 1){
			return 1099;
		}
 		$sql = "SELECT id,uid,create_time,province_id,city_id,district_id,type,state,introduce,production,honor,business_num,legal_person FROM hyq_org_profile WHERE uid = '{$uid}'";
		$orglist = $db_hyq_read -> fetch_array($db_hyq_read -> query($sql));
		//orglist = $this->get_hyq_sql_array($sql,1);
		return $orglist;
  	}
	 //更新机构的orgprofile信息 	$org_profile_array是传入数组org_profile信息 zzh
	public function update_org_profile($uid,$org_profile_array = array()){
		global $db_hyq_write;
		if($uid < 1){
			return 1099;
		}			
		if(!is_array($org_profile_array)){
			return 1099;
		}	
		$ip = getIP();
		$sql =" UPDATE hyq_org_profile SET ";							
		foreach($org_profile_array as $k => $v){
			$sql .= $k.'='.'"'.$v.'"'.',';				
		}		
		$sql = rtrim($sql,',');
		$sql .= ",modi_date = now() ";
		$sql .= "WHERE uid = '{$uid}'";			
		//var_dump($sql);						
		$result = $db_hyq_write -> query($sql);	#修改数据库信息
		if($result){
			return 1000;
		}else{
			return 1112;
		}
	}
	//增加红艺人	zzh
	function add_artist($uid,$name,$description){
		global $db_hyq_write;	
		if($uid < 1){
			return 1099;
		}	
		$sql = "INSERT INTO hyq_artists SET name = '{$name}',description = '{$description}' WHERE uid = '{$uid}'";  	
		if($db_hyq_write -> query($sql)){
			return true;
		}else{
			return false;
		}
	}
	 //修改红艺人	zzh
	function update_artist($uid,$name,$description){
		global $db_hyq_write;
		if($uid < 1){
			return 1099;
		}		
		$sql = "UPDATE hyq_artists SET name = '{$name}',description = '{$description}' WHERE uid = '{$uid}'";  
		if($db_hyq_write -> query($sql)){
			return true;
		}else{
			return false;
		}
	}
	//删除红艺人	zzh
	function delete_artist($id){
		global $db_hyq_write;
		if($id < 1){
			return 1099;
		}		
		$sql = "DELETE FROM hyq_artists WHERE id = '{$id}'";
		if($db_hyq_write -> query($sql)){
			return true;
		}else{
			return false;
		}	
	}  
	//检测工商号格式	zzh
	public function check_business_num($business_num){
		if(empty($business_num)){
			return 1119;	#工商号为空！
		}else{
			$pattern = "/^\d{14}$/";
			$match = preg_match($pattern,$business_num);
			if($match){
				return 1000;
			}else{
				return 1120;	#工商号格式不正确!
			}
		}
	}
	//检测工商号是否存在	zzh
	public function business_num_exist($business_num){
		global $db_hyq_read;
		$sql = "SELECT id FROM hyq_org_profile WHERE business_num = '{$business_num}'";
		$query = $db_hyq_read -> query($sql);
		$result = $db_hyq_read -> fetch_array($query);
		if($result){
			return 1000;	#工商号已经绑定过！
		}else{
			return 1121;	#工商号可以绑定!
		} 
	}
	//工商号开始绑定	zzh
	public function banding_business_num($uid,$business_num){
		global $db_hyq_write;
		$sql = "UPDATE hyq_org_profile SET 
				business_num = '{$business_num}',
				modi_date = now()
				WHERE uid = '{$uid}'";
		$result = $db_hyq_write->query($sql);
		if($result){
			$result = $this ->update_user_business_num_status($uid);	#更新工商号状态
			if($result == 1000){
				return 1000;
			}else{
				return 1145;	#更新身份证状态失败
			}
		}else{
			return 1123;	#工商号绑定失败
		}
	}
	//绑定工商号更新状态
	public function update_user_business_num_status($uid){
		global $db_hyq_write;
		$modi_date = date('Y-m-d H:i:s');		
		$sql="UPDATE hyq_user SET 
			business_num_status = 'yes',
			modi_date = '{$modi_date}' WHERE id = '{$uid}'";
		$result = $db_hyq_write -> query($sql);
		if($result){
			return 1000;
		}else{
			return 1145;	#更新身份证状态失败
		} 
	}		
	//检测身份证格式   如果机构是法人择绑定身份证   zzh
	public function check_identity_card($identity_card){
		if(empty($identity_card)){
			return 1115;	#身份证为空！
		}else{
			$pattern_one = "/^\d{6}((0[48]|[2468][048]|[13579][26])0229|\d\d(0[13578]|10|12)(3[01]|[12]\d|0[1-9])|(0[469]|11)(30|[12]\d|0[1-9])|(02)(2[0-8]|1\d|0[1-9]))\d{3}$/";	#一代身份证正则
			$pattern_two = "/^\d{6}((2000|(19|21)(0[48]|[2468][048]|[13579][26]))0229|(((20|19)\d\d)|2100)(0[13578]|10|12)(3[01]|[12]\d|0[1-9])|(0[469]|11)(30|[12]\d|0[1-9])|(02)(2[0-8]|1\d|0[1-9]))\d{3}[\dX]$/";	#二代身份证正则
			$match_one = preg_match($pattern_one,$identity_card);
			$match_two = preg_match($pattern_two,$identity_card);
			if($match_one || $match_two){
				return 1000;
			}else{
				return 1116;	#身份证格式不正确!
			}
		}
	}	

	//检测身份证是否存在	zzh
	public function identity_card_exist($identity_card){
		global $db_hyq_read;
		$sql = "SELECT id FROM hyq_user_profile WHERE identity_card='{$identity_card}'";
		$query = $db_hyq_read -> query($sql);
		$result = $db_hyq_read -> fetch_array($query);
		if($result){
			return 1117; #身份证已经绑定过！
		}else{
			return 1000; #身份证可以绑定!
		} 
	}

	//开始绑定身份证	zzh
	public function binding_identity_card($uid,$identity_card){
		global $db_hyq_write;
		$sql = "UPDATE hyq_user_profile SET 
				identity_card = {$identity_card},
				modi_date = now()
				WHERE uid = '{$uid}'";
		$result = $db_hyq_write->query($sql);
		if($result){
			$result = $this ->update_user_identity_card_status($uid);	#更新身份证状态
			if($result == 1000){
				return 1000;
			}else{
				return 1144;	#更新身份证状态失败
			}
		}else{
			return 1122;  #身份证绑定失败
		}
	}
	//绑定身份证更新身份证状态
	public function update_user_identity_card_status($uid){
		global $db_hyq_write;
		$modi_date = date('Y-m-d H:i:s');		
		$sql="UPDATE hyq_user SET 
			identity_card_status = 'yes',
			modi_date = '{$modi_date}' WHERE id = '{$uid}'";
		$result = $db_hyq_write -> query($sql);
		if($result){
			return 1000;
		}else{
			return 1144;	#更新身份证状态失败
		} 
	}		
}		
