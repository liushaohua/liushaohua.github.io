<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../../includes/common_inc.php');
  	require_once (COMMON_PATH.'/service.class.php');
  	require_once (COMMON_PATH.'/base.class.php');
	
	
	
	
	
	$base = new base;
	$re = $base -> get_org_type_list($flash);
	var_dump($re);
	//date('Y',time());
	
	function get_one_service(){
		global $flash;
		$service = new service;
		$result = $service -> get_service($flash);
		foreach($result as $k => $value){
			if($value['parent_id'] == 0){
				$service_first_list[$k]['id'] = $value['id'];
				$service_first_list[$k]['name'] = $value['name'];
			}
		}
		$service_id = 1;
		$service_second_list = $service -> get_children_service($service_id,$flash);
		if($service_second_list){
			var_dump($service_second_list);
		}else{
			var_dump('no');
		}

	}
	//echo mktime(1426825000);
	echo date("Y-m-d H:i",1426825000);
	$str = "Hello world. It's a beautiful day.";
	print_r (explode(" ",$str));
	//SELECT * FROM `hyq_e_apply` AS apply RIGHT JOIN `hyq_user_profile` AS user ON apply.uid = user.uid WHERE recruit_id = '535' AND e_service_id = '130' 
	function get_user_invite_list_by_recruit_service($rid, $sid, $userid, $sex, $result, $from_rows = 0,$limit = 0){
		global $db_hyq_read, $db_hyq_write;
		$sql = "SELECT * FROM `hyq_e_invite` AS invite RIGHT JOIN `hyq_user_profile` AS user ON invite.uid = user.uid WHERE recruit_id = '{$rid}' AND e_service_id = '{$sid}'";
		//SELECT * FROM `hyq_e_invite` AS invite RIGHT JOIN `hyq_user_profile` AS user ON invite.uid = user.uid WHERE recruit_id = '1398' AND e_service_id = '199' AND sex = 'm' ORDER BY invite_date 		
		if($sex != ''){
			$sql .= " AND sex = '{$sex}'";	
		}
		if($result != '0'){
			$sql .= " AND result = '{$result}'";
		}
		if($limit == 0 ){
			$sql .= " ORDER BY invite_date DESC";
		}else{
			$sql .= " ORDER BY invite_date DESC LIMIT {$from_rows},{$limit}";
		}
//echo $sql;
		$result = $db_hyq_read -> fetch_result($db_hyq_read -> query($sql));
		var_dump($result);		
	}
	$rid = 1398;
	$sid = 199;
	$userid = 45724;
	$sex = 'f';
	$result = '0';
	$from_rows = 0;
	$limit = 0;
	//get_user_invite_list_by_recruit_service($rid, $sid, $userid, $sex, $result, $from_rows,$limit);
	/*
	rid true String 指定招募的id
	e_service_id true String 指定二级服务的报名id
	sex true String 筛选性别。m为男，f为女，不限为空字符串""
	communication true String 筛选沟通结果。不限0，就他1，再说2，没戏3.
	page true String 分页第几页，从1开始。	
	*/	
/*	
	$invite = new invite;
	var_dump('go,go,go');
	echo '生存么 阿阿';
	$uid = 45724;
	$limit = 10; 
	$from_rows =5;
	$result = $invite -> get_invite_list_by_user($uid, $from_rows, $limit);
	echo '<pre>';
	print_r($result);
	//获取招募里，某个二级服务下，已经邀约的红人列表
	function mywork_get_invitation_user_list(){
		global $flash,$PAGESIZE;
		$uid = intval($_POST['uid']);
		$rid = intval($_POST['rid']);
		$page = intval($_POST['page']);
		$e_service_id = intval($_POST['e_service_id']);	
		$app_token = clear_gpq($_POST['app_token']);
		$sex = clear_gpq($_POST['sex']);
		$result = clear_gpq($_POST['communication']);
		$pagesize = $PAGESIZE['MYWORK_PAGE'];
		//_check_login($uid,$app_token);
		if($uid<1 || $rid<1 || $e_service_id<1) return get_state_info(1099);
		if($page < 1) $page = 1;		

		$from_rows = ($page - 1) * $pagesize;
		$invite = new invite();
		//$apply_list = $apply -> get_user_apply_list_by_recruit_service($rid, $e_service_id, $userid, $sex, $result, $from_rows, $limit);
		//$sql = "SELECT * FROM `hyq_e_apply` AS apply RIGHT JOIN `hyq_user_profile` AS user ON apply.uid = user.uid WHERE recruit_id = 462 AND e_service_id = 116 AND sex = 'm' AND ";
		$re = $invite -> get_invite_list_by_user($uid, $from_rows = 0, $limit = 0);
	}	
	
*/	
	
	/*		$base = new base();
	global $base;
	$province_list = $base -> get_province_list();
	$city_list = $base -> get_city_list();
	$district_list = $base -> get_district_list();
	foreach($district_list as $key => $item){
		unset($district_info);
		$district_info["id"] = $item["id"];
		$district_info["name"] = $item["dname"];
		$district_info["parent_id"] = $item["cid"];
		$district_array[$item["cid"]][] = $district_info;
	}
	foreach($city_list as $key => $item){
		unset($city_info);
		$city_info['id'] = $item["id"];
		$city_info['name'] = $item["cname"];
		$city_info['parent_id'] = $item["pid"];
		$city_info["child"] = $district_array[$item["id"]];
		$ [$item["pid"]][] = $city_info;
	}
	foreach($province_list as $key => $item){
		unset($province_info);
		$province_info["id"] = $item["id"];
		$province_info["name"] = $item["pname"];
		$province_info["child"] = $city_array[$item["id"]];
		$province_array[] = $province_info;
	}
	print_r($province_array);

	
	
echo '<hr>';	

	echo date('Y');
	$base = new base();
	$p = $base->get_province_list();
	$c = $base ->get_city_list();
	$d = $base ->get_district_list();
	//var_dump($c);
	$new_arr = array();
	$new_arr1 = array();
	foreach ($p as $key => $val) {
			$new_arr[$key]['id'] = $val['id'];
			$new_arr[$key]['pname'] = $val['pname'];
			foreach($c as $k => $v){
					if($val['id'] == $v['pid']){
							$new_arr[$key]['city'][$k]['id'] = $v['id'];			
							$new_arr[$key]['city'][$k]['cname'] = $v['cname'];
							
							foreach($d as $kk=>$vv){
								if($v['id'] == $vv['cid']){
									//$new_arr[$key]['city'][$kk]['district']['id'] = $vv['id'];
									//$new_arr[$key]['city'][$k]['district'][$vv['id']] = $vv['dname'];
								$new_arr[$key]['district'][$kk]['id'] = $vv['id'];			
								$new_arr[$key]['district'][$kk]['dname'] = $vv['dname'];	
								}
								
							}
				
					}
			}
	}

	//print_r($new_arr);
*/	
/*
function _get_sex_list(){
	$res['sex'] = array('男','女');
	return $res;
}
function _get_state_list(){
	$res['state'] = array('空闲','忙碌','其它');
	return $res;
}
function _get_age_list(){
	$res['age'] = array('max' => '71','min' => '7');
	return $res;	
}
function _get_height_list(){
	$res['height'] = array('max' => '201','min' => '99');
	return $res;	
}
function _get_weight_list(){
	$res['weight'] = array('max' => '101','min' => '29');
	return $res;	
}
function _get_bust_list(){
	$res['bust'] = array('max' => '110','min' => '60');
	return $res;	
}
function _get_waist_list(){
	$res['waist'] = array('max' => '100','min' => '40');
	return $res;	
}
function _get_hips_list(){
	$res['hips'] = array('max' => '110','min' => '60');
	return $res;	
}
function _get_star_list(){
	$res['star'] = array('白羊座','金牛座','双子座','巨蟹座','狮子座','处女座','天秤座','天蝎座','射手座','魔蝎座','水瓶座','双鱼座');
	return $res;	
}
function _get_blood_list(){
	$res['blood'] = array('A', 'B', 'AB', 'O');
	return $res;	
}
function _get_finish_year_list(){
	$res['finish_year'] = array('max' => '2015','min' => '1977');
	return $res;	
}
function _get_degree_list(){
	$res['degree'] = array('博士', '硕士', '本科', '高中', '初中', '小学', '其它');
	return $res;	
}
function _get_create_time(){
	$res['create_time'] = array('max' => '2015', 'min' => '1900');
}
function _get_legal_person(){
	$res['legal_person'] = array('yes', 'no');
}
function get(){
	$ret_arr[] = _get_degree_list();
	$ret_arr[] = _get_finish_year_list();
	echo json_encode($ret_arr);
}

*/




























?>	
