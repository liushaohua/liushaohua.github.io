<?php
/*
 * 邀约基础类
 * 作者：suntianxing
 * 添加时间：2014-12-9
*/
class invite{
  
	/**
	 * 发送邀约
	 * @param string $value 要存入的邀约信息数组
	 */
	public function add_invite($value=array()) {
		global $db_hyq_write;
		$agent = $_SERVER['HTTP_USER_AGENT'];   		//浏览器信息
		$ip = getIP();
		$add_time = date('Y-m-d H:i:s',time());
		$sql = "INSERT INTO hyq_e_invite SET 
					uid = '{$value['receiver']}',
					recruit_id = '{$value['rid']}',
					role_id = '{$value['role']}',
					invite_date = '{$add_time}',
					r_mobile = '{$value['mobile']}',
					r_email = '{$value['email']}',
					r_weixin = '{$value['weixin']}',
					r_qq = '{$value['qq']}',
					invite_ip = '{$ip}',
					invite_agent = '{$agent}'";
		if($db_hyq_write -> query($sql)){
			return true;			
		}else{
			return false;
		}
	}

	/**
	 * 获取邀约信息 
	 * @param string $rid 招募id
	 */
	public function get_invite_list_by_recruit($rid) {
	//	global $db_hyq_read;	
 	//	$sql = "SELECT recruit_id,role_id,add_date,u_deal_date,status,r_mobile,r_email,r_weixin,r_qq,is_show,u_mobile,u_email,u_weixin,u_qq FROM hyq_e_invite WHERE id = '{$rid}'";
	//	return $db_hyq_read -> fetch_array($db_hyq_read -> query($sql));			
	}
	
	/**
	 * 获取用户收到的邀约列表 
	 * @param string $uid 用户uid
	 */
	public function get_invite_list_by_self($uid) {
		global $db_hyq_read;	
 		$sql = "SELECT * FROM hyq_e_invite WHERE uid = '{$uid}'";
		return $db_hyq_read -> fetch_result($db_hyq_read -> query($sql));			
	}
	
	/**
	 * 被邀约者的邀约处理
	 * @param string $id  邀约的id 
	 * @param string $value  更新的邀约信息 
	 */
	public function update_invite($id,$value=array()) {
		global $db_hyq_write;
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$ip = getIP();
		$u_deal_date = date('Y-m-d H:i:s',time());
		$sql = "UPDATE hyq_e_invite SET 
					u_status = '{$value['status']}',
					u_mobile = '{$value['mobile']}',
					u_email = '{$value['email']}',
					u_weixin = '{$value['weixin']}',
					u_qq = '{$value['qq']}',
					u_deal_date = '{$u_deal_date}',
					invite_ip = '{$ip}',
					invite_agent = '{$agent}'
					WHERE id = '{$id}'";
		if($db_hyq_write -> query($sql)){
			return true;			
		}else{
			return false;
		}	
	}
	
	/**
	 * 用户删除收到的邀约
	 * @param string $id  收到的邀约的id
	 */
	public function delete_invite($id) {
		global $db_hyq_write;
		$sql = "UPDATE hyq_e_invite SET u_show = '0' WHERE id = '{$id}'";
		if($db_hyq_write -> query($sql)){
			return true;
		}else{
			return false; 	//删除收到的邀约失败
		}		
					
	}
}
?>