<?php
/*
 * 报名基础类
 * 作者：suntianxing
 * 添加时间：2014-12-9
*/
class apply{
  
	/**
	 * 发送报名
	 * @param string $value 要存入的报名信息数组
	 */
	public function add_apply($value=array()) {
		global $db_hyq_write;
		$agent = $_SERVER['HTTP_USER_AGENT'];   		//浏览器信息
		$ip = getIP();
		$add_time = date('Y-m-d H:i:s',time());
		$sql = "INSERT INTO hyq_e_apply SET 
					uid = '{$value['applyer']}',
					recruit_id = '{$value['rid']}',
					e_service_id = '{$value['service']}',
					apply_date = '{$add_time}',
					u_mobile = '{$value['mobile']}',
					u_email = '{$value['email']}',
					u_weixin = '{$value['weixin']}',
					u_qq = '{$value['qq']}',
					ip = '{$ip}',
					agent = '{$agent}'";
		if($db_hyq_write -> query($sql)){
			$insert_id = $db_hyq_write -> insert_id();
			return $insert_id;			
		}else{
			return false;
		}
	}
	/**
	  *保存三级服务
	  *$service_id 三级服务id
	  *$rid 招募id
	  *$aid 报名id
	*/
	public function add_third_service($service_id, $rid, $aid){
		global $db_hyq_write;
		$sql = "INSERT INTO hyq_e_apply_item SET 
					service_3_id = '{$service_id}',
					recruit_id = '{$rid}',
					e_apply_id = '{$aid}'";
		if($db_hyq_write -> query($sql)){
			return true;
		}else{
			return false;	
		}
	}

	/**
	 * 不允许一个人报名同一个招募下的同一个角色   报名检测
	 * @param string $rid 招募id $uid 报名者id $service 报名的角色id
	 */
	public function check_apply_by_user($uid,$rid,$service) {
		global $db_hyq_read;	
 		$sql = "SELECT * FROM hyq_e_apply WHERE uid = '{$uid}' AND recruit_id = '{$rid}' AND e_service_id = '{$service}'";
		$result = $db_hyq_read -> fetch_array($db_hyq_read -> query($sql));
		if($result){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 招募方获取报名信息 
	 * @param string $rid 招募id
	 */
	public function get_apply_list_by_recruit($rid) {
		global $db_hyq_read;	
 		$sql = "SELECT * FROM hyq_e_apply WHERE recruit_id = '{$rid}' AND u_status = '1'";
		$result = $db_hyq_read -> fetch_result($db_hyq_read -> query($sql));
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	/**
	 * 获取一个招募下的报名数统计 
	 * @param string $rid 招募id
	 */
	public function get_apply_num_by_recruit($rid) {
		global $db_hyq_read;	
 		$sql = "SELECT count('id') as num FROM hyq_e_apply WHERE recruit_id = '{$rid}' AND u_status = '1'";
		$result = $db_hyq_read -> fetch_array($db_hyq_read -> query($sql));
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	/**
	 *通过用户的 uid 获取其名下的 所有的招募 再通过 招募的id 查询该招募下的所有报名
	 * 获取用户的报名列表 
	 * @param string $uid 用户uid
	 */
	public function get_apply_list_by_user($uid) {
		global $db_hyq_read;	
 		$sql = "SELECT * FROM hyq_e_apply WHERE uid = '{$uid}'";
		$result = $db_hyq_read -> fetch_result($db_hyq_read -> query($sql));
		if($result){
			return $result;
		}else{
			return false;
		}			
	}
	/**
	 *查询单条的报名详细信息  回调用
	 * 获取用户的报名列表 
	 * @param string $aid 报名的id
	 */
	public function get_apply($aid) {
		global $db_hyq_read;	
 		$sql = "SELECT id,uid,u_mobile,u_email,u_weixin,u_qq FROM hyq_e_apply WHERE id = '{$aid}'";
		$result = $db_hyq_read -> fetch_array($db_hyq_read -> query($sql));
		if($result){
			return $result;
		}else{
			return false;
		}			
	}
	/**
	 * 招募方对报名者的报名处理  确认接受报名
	 * @param string $aid  报名的id 
	 * @param string $value  招募方 更改的报名信息 
	 */
	public function recruit_update_apply($value=array()) {
		global $db_hyq_write;
		$time = date('Y-m-d H:i:s',time());
		$sql = "UPDATE hyq_e_apply SET 
					r_deal_date = '{$time}',
					r_status = '{$value['status']}',
					r_mobile = '{$value['mobile']}',
					r_email = '{$value['email']}',
					r_weixin = '{$value['weixin']}',
					r_qq = '{$value['qq']}' WHERE id='{$value['aid']}'";
		if($db_hyq_write -> query($sql)){
			return true;			
		}else{
			return false;
		}
	}
	/**
	 * 招募方对报名者的报名处理  拒绝报名
	 * @param string $aid  报名的id 
	 * @param string $value  招募方 拒绝信息 refuse 
	 */
	public function recruit_refuse_apply($aid, $value) {
		global $db_hyq_write;
		$time = date('Y-m-d H:i:s',time());
		$sql = "UPDATE hyq_e_apply SET r_deal_date = '{$time}',r_status = '{$value}' WHERE id='{$aid}'";
		if($db_hyq_write -> query($sql)){
			return true;			
		}else{
			return false;
		}
	}
	/**
	 * 报名方对已报名的处理  取消报名/重新报名
	 * @param string $aid  报名的id 
	 * @param string $value  更新的值 
	 */
	public function user_update_apply($aid,$value) {
		global $db_hyq_write;
		$time = date('Y-m-d H:i:s',time());
		if($value == '0'){
			$sql = "UPDATE hyq_e_apply SET u_status = '{$value}' WHERE id = '{$aid}'";
		}else{
			$sql = "UPDATE hyq_e_apply SET u_status = '{$value}',apply_date = '{$time}' WHERE id = '{$aid}'";
		}
		if($db_hyq_write -> query($sql)){
			return true;			
		}else{
			return false;
		}
	}
	
	/**
	 * 报名者删除自己的报名
	 * @param string $aid  报名者报名的id
	 */
	public function user_delete_apply($aid) {
		global $db_hyq_write;
		$sql="UPDATE hyq_e_apply SET u_show = '0' WHERE id={$aid}";
		$query = $db_hyq_write -> query($sql);
		if($query){
			return true;
		}else{
			return false;  //自定义标签显示状态更新失败
		}
	}
}
?>