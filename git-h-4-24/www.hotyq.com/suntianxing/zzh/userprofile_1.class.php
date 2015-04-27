<?php 
	/*
	*    userProfile 用户详细信息操作类	 
	*/
class userprofile extends dbcache{
	//查询数据的资料id
  	function get_uinfo($uid){
		global $db_hotyq_read;	//读取数据
 		$sql = "SELECT  id,uid,sex,age,star,blood,height,weight,bust,waist,hips,hope_role,state,province_id,city_id,district_id,native_province_id,native_city_id,native_district_id,school,finish_year,specialty,education FROM hyq_uinfo WHERE uid = {$uid}";
		$userlist=$this->get_hyq_sql_array($sql,1);//读取一条数据从缓存
		return $userlist;
		//var_dump($userlist);age height weight ,bust,waist,hips star blood native_province_id,native_city_id,native_district_id,school,finish_year,specialty,education
  	}	
	//更新用户的资料	
	public function update_uinfo($uid,$nickname,$uinfo = array()){
		global $db_hyq_write;
			//修改uinfo表 资料的SQL语句
			$sql =" UPDATE hyq_uinfo SET ";							
			foreach($uinfo as $k => $v){
				$sql .= $k.'='.'"'.$v.'"'.',';				
			}		
			$sql = rtrim($sql,',');
			$sql .= ",modi_date = now() ";
			$sql .= "WHERE uid= {$uid}";			
			//var_dump($sql);						
			$result = $db_hyq_write->query($sql);//修改数据库信息
			$re = $this->update_user_name($uid,$nickname);
			if($result && $re){
				return 1000;
			}else{
				return 1112;
			}
	}

	//修改个人用户名字		
	public function update_user_name($uid,$nickname){
		global $db_hyq_write;	
		$sql="UPDATE hyq_user SET 
			nickname = '{$nickname}' 
			WHERE id = '{$uid}'";
		$result = $db_hyq_write->query($sql);
		if($result){
			return 1000;
		}else{
			return 1111;
		} 
	}			
	
	//检查手机格式
	public function check_mobile($mobile){
		if($mobile == null){
			return 1018;
		}else{
			$pattern = "/^[1][3578][0-9]\d{8}$/";
			$match = preg_match($pattern,$mobile);
			if($match){
				return 1000;
			}else{
				return 1016;
			}
		}
	
	}	
	//手机绑定生成激活码  发送短信
	public function get_binding_mobile_code($mobile){ 
		global $db_hyq_write;
		global $db_hyq_read;
		if($this -> mobile_exist($mobile) != 1000){  
			$check_code=rand(100000,999999); 
			$content = "您的手机绑定验证码为：".$check_code.",请及时输入！"; 
			if($reg_mobile_code_info = $this -> get_reg_mobile_code_info($mobile)){  
				if(intval($reg_mobile_code_info["timediff"]) > 300 ){ 
					//具备发送条件 
					$sql = "UPDATE hyq_mobile_code SET 
						check_code = '{$check_code}',
						send_date = now()
						WHERE mobile = '{$mobile}'
					"; 
					if($db_hyq_write -> query($sql)){
						//发送短信
						send_sms($mobile,$content);
						return 1000;
					}else{
						return 1513;   //验证码发送时更新数据失败！
					} 
				}else{
					//不具备发送条件
					return 1515;       //请5分钟后再发送。
				} 
			}else{
				//新增绑定码
				$sql = "INSERT INTO hyq_mobile_code SET
							mobile = '{$mobile}',
							check_code = '{$check_code}',
							send_date = now()
					";
				if($db_hyq_write -> query($sql)){
					//发送短信
					send_sms($mobile,$content);
					return 1000;
				}else{
					return 1516;   //验证码发送时新建数据失败！
				}
			}
		}else{
			return 1113;     //已经是绑定用户，不能发送。
		}
	} 	
	//检查手机是否已绑定
	public function mobile_exist($mobile){
		global $db_hyq_read;
		$sql = "SELECT id FROM hyq_user WHERE mobile = '{$mobile}'";
		$query = $db_hyq_read -> query($sql);
		$result = $db_hyq_read -> fetch_array($query);
		if($result){
			return 1000; 
		}else{
			return 1023;
		}
	} 
	//检查手机验证码表是否已有值   私有方法
	private function get_reg_mobile_code_info($mobile){
		global $db_hyq_read;
		$sql = "SELECT check_code,mobile,(unix_timestamp(now()) - unix_timestamp(send_date)) as timediff  
				FROM hyq_mobile_code 
				WHERE mobile = '{$mobile}' "; 
		$query = $db_hyq_read -> query($sql);
		if($result = $db_hyq_read -> fetch_array($query)){
			return $result;
		} 
	} 	
	
	public function check_binding_mobile_code($uid,$mobile,$check_code){
		global $db_hyq_write;
		//$uid=$_SESSION['uid'];
		$mobile_code_info = $this -> get_reg_mobile_code_info($mobile);
		if($mobile_code_info){
			if($mobile_code_info['check_code'] != $check_code){
				return 1519;   //手机验证码错误。
			}
		}else{
			return 1518;   //您的手机验证码尚未发送。
		}
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$sql="UPDATE hyq_user SET 
				mobile = '{$mobile}',			
				mobile_status = 'yes',
				mobile_check_code = '{$check_code}', 
				agent = '{$agent}',
				modi_date = now()
				WHERE id = {$uid}
				";
		//var_dump($sql);		
		if($query=$db_hyq_write -> query($sql)){ 
			return 1000;
		}else{
			return 1114;  //用户绑定手机失败
		} 
	} 
	//检测email地址
	public function check_email($email){
		if(empty($email)){
			return 1017;//邮箱为空！
		}else{
			$pattern="/^[a-zA-Z0-9_+.-]+\@([a-zA-Z0-9-]+\.)+[a-zA-Z0-9]{2,4}$/";
			$match = preg_match($pattern,$email);
			if($match){
				return 1000;
			}else{
				return 1009;//邮箱格式不正确!
			}
		}
	}	
	//检查邮箱是否已注册
	public function email_exist($email){
		global $db_hyq_read;
		$sql = "SELECT id FROM hyq_user WHERE email='{$email}'";
		$query = $db_hyq_read -> query($sql);
		$result = $db_hyq_read -> fetch_array($query);
		if($result){
			return 1000;//邮箱已注册！
		}else{
			return 1022;//邮箱未注册！
		} 
	}
	//发送激活邮件
	function send_active_email($uid,$email){
		$userinfo = $this -> get_userinfo($uid);
		if($userinfo){
			//邮件主题
			$email_subject = "红眼圈账号激活";
			//邮件内容
			$email_content = '您好,点击如下链接激活红眼圈账号:<br>';
			$email_content .= '<a href ="/account/active_email.php?uid='.$uid.'&email_check_code='.$userinfo['email_check_code'] .'">/account/active_email.php?uid='.$uid.'&email_check_code='.$userinfo['email_check_code'].'</a><br>';
			$email_content .= '请点击完成验证.';
			$email_content .= '<hr>';
			$email_content .= '<font color = "#dddddd">如果链接无法点击，请将它拷贝到浏览器的地址栏中。</font>'; 
			//直接发送邮件
			if(send_mail($email,$email_subject,$email_content)){
				return 1000;
			}else{
				return 1501; //发送激活邮件失败
			} 
		}else{
			return 1502;  //发送激活邮件用户信息无效
		}
	}	
	//根据用户id获取用户信息  私有方法
	public function get_userinfo($uid){
		global $db_hyq_read;
		$sql = "SELECT * FROM hyq_user WHERE id ='{$uid}'";
		return $db_hyq_read->fetch_array($db_hyq_read->query($sql));
	}
	//检测身份证格式
	public function check_identity_card($identity_card){
			$pattern="/^(?:\d{15}|\d{18})$/";
			$match = preg_match($pattern,$identity_card);
			if($match){
				return 1000;
			}else{
				return 1116;//身份证格式不正确!
			}
		}
	}
	//检测身份证是否存在
	public function identity_card_exist($identity_card){
		global $db_hyq_read;
		$sql = "SELECT id FROM hyq_uinfo WHERE identity_card='{$identity_card}'";
		$query = $db_hyq_read -> query($sql);
		$result = $db_hyq_read -> fetch_array($query);
		if($result){
			return 1117;//身份证已经绑定过！
		}else{
			return 1000;//身份证可以绑定!
		} 
	}
	//开始绑定身份证	
	public function binding_identity_card($uid,$identity_card){
		global $db_hyq_write;
		$sql = "UPDATE hyq_uinfo SET 
				identity_card = {$identity_card}
				WHERE uid = {$uid}";
		$result = $db_hyq_write->query($sql);
		if($result){
			return 1000;
		}else{
			return 1122;//身份证绑定失败
		}
	}
}	
?>