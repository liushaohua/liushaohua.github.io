<?php
class userprofile{
	//获取个人用户的profile信息  zzh
	public function get_user_profile($uid){
		global $db_hyq_read;	
 		$sql = "SELECT id,uid,sex,age,star,blood,height,weight,bust,waist,hips,role,state,province_id,city_id,district_id,native_province_id,native_city_id,native_district_id,school,finish_year,specialty,education FROM hyq_user_profile WHERE uid = '{$uid}'";
		$userlist = $db_hyq_read -> fetch_array($db_hyq_read -> query($sql));
		return $userlist;
	}
	
	//更新个人的profile信息    $user_profile_array是传入数组user_profile信息  zzh
	public function update_user_profile($uid,$user_profile_array = array()){
		global $db_hyq_write;
		$ip = getIP();
		$sql =" UPDATE hyq_user_profile SET ";							
		foreach($user_profile_array as $k => $v){
			$sql .= $k.'='.'"'.$v.'"'.',';				
		}		
		$sql = rtrim($sql,',');
		$sql .= ",modi_date = now() ";
		$sql .= "WHERE uid = '{$uid}'";			
		//var_dump($sql);						
		$result = $db_hyq_write -> query($sql);
		if($result){
			return true;
		}else{
			return false;
		}
	}	
	//检测身份证格式	zzh
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
				identity_card = {$identity_card}
				WHERE uid = {$uid}";
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

