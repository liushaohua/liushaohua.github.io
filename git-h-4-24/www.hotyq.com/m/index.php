<?php
	header("Content-type:textml;charset=utf-8");
	require_once("../includes/common_inc.php");
	
	//echo strtotime("2009-01-31 09:23:10");
	//echo '<hr>';
	
	$service = new service();
	//取服务缓存
	$service_list = $service -> get_service($flash);
	if(is_array($service_list) && $service_list){
		foreach($service_list as $k=>$v){
				$service_arr[$v['id']] = $v['name'];
		}
	}
	//获取后台分类服务数据
	$sql = "SELECT * FROM cms_template_data WHERE t_id ='88' ORDER BY id ASC LIMIT 7";
	$result = $db_read -> fetch_result($db_read -> query($sql));

	if($result && is_array($result)){
		foreach($result as $key=>$value){
			$service_1_id = $value['des'];
			$service_class_arr[$key]['id'] = $service_1_id;
			$service_class_arr[$key]['type'] = 0;
			$service_class_arr[$key]['url'] = '';
			$service_class_arr[$key]['name'] = $service_arr[$service_1_id];
		}
		$last_class_arr = array("id"=>"0","type" => 1,"name" => "更多");
		array_push($service_class_arr,$last_class_arr);
	}else{
		$service_class_arr = array();
	}
	
	//print_r($service_class_arr);exit;
	//获取最新红人
	$template_id_arr1 = array("28"=>"67","39"=>"68","85"=>"69","86"=>"70","87"=>"71","88"=>"72","89"=>"73","90"=>"74","12856"=>"76");
	foreach($template_id_arr1 as $k=>$v){
		$rr = get_red_user($v,$k, $flash);
		if(is_array($rr) && $rr){
			$role_class_user_arr[$k] = $rr;
		}
	}
	$template_id_arr2 = 75;
	$rrr = get_org_user($template_id_arr2, $flash);
	
	$pop_arr = array_pop($role_class_user_arr);
	array_push($role_class_user_arr,$rrr);
	array_push($role_class_user_arr,$pop_arr);
	
	//print_r($role_class_user_arr);exit;
	//获取推荐招募
	$recruit_list = get_recommend_recruit_list(61, $flash);
	//print_r($recruit_list);exit;
	if(is_array($recruit_list) && $recruit_list){
		$smarty -> assign('recruit_list', $recruit_list);
	}else{
		$smarty -> assign('recruit_list', '');
	}
	//获取广告图
	//获取专题图
	
	
	
	$smarty -> assign('service_class',$service_class_arr);
	$smarty -> assign('role_user_arr',$role_class_user_arr);
	
  
	$smarty -> display('m/index.html'); 
	
	//获取推荐招募
	function get_recommend_recruit_list($template_id = 61,$flash){
		global $COMMON_CONFIG,$db_read;
		$base = new base();
		$recruit = new recruit();
		//$userprofile = new userprofile();

		$sql = "SELECT * FROM cms_template_data WHERE t_id ='{$template_id}' LIMIT 4";
		$result = $db_read -> fetch_result($db_read -> query($sql));
		if($result){
			foreach($result as $k=>$v){
				$rid = $v['des'];
				$recruit_pic = $v['pic'];
				$recruit_info = $recruit -> get_recruit_info($rid, $flash);
				
				$address_info = $base -> get_city_info($recruit_info['city_id'],$flash);
				
				$recruit_list[$rid]['rid'] = $rid;
				$recruit_list[$rid]['ruid'] = $recruit_info['uid'];
				$recruit_list[$rid]['title'] = $recruit_info['name'];
				$recruit_list[$rid]['city'] = $address_info['cname'];
				$recruit_list[$rid]['url'] = $recruit_pic;
			}
			
			return $recruit_list;
		}else{
			return false;
		}
	}

	//获取机构最新红人
	function get_org_user($template_id, $flash){
		global $db_read,$db_hyq_read;
		$user = new user();

		$sql = "SELECT * FROM cms_template_data WHERE t_id ='{$template_id}'";
		$result = $db_read -> fetch_result($db_read -> query($sql));
		$userid_arr = array();
		if($result){
			foreach ($result as $kk=>$vv) {
				$userid_arr[] = $vv['des'];
			}
		}
		$userid_arr = array_unique($userid_arr);
		$num = count($userid_arr);
		if($num < 12){
				   $new_num = 12 - $num;
				   $sql2 = "SELECT * FROM hyq_user WHERE user_type = 'org' AND nickname != '' AND icon_path_url != '' ORDER BY id DESC LIMIT 0,12";
				   $r = $db_hyq_read -> fetch_result($db_hyq_read -> query($sql2));
				   if($r){
					   foreach ($r as $key=>$value) {
						   $userid_arr[] = $value['id'];
					   }
					   $userid_arr = array_unique($userid_arr);
					   $userid_arr = array_slice($userid_arr,0,12);
				   }
		 }    
        if(is_array($userid_arr) && !empty($userid_arr)){
			foreach($userid_arr as $k=>$v){
				$uid = $v;
				$orginfo = $user -> get_userinfo($uid, $flash);
				$user_info_list[$uid] = array(
						'id' => $orginfo['id'],
						'icon_url' => $orginfo['icon_server_url'].$orginfo['icon_path_url'],
						'nickname' => $orginfo['nickname'],
				);
			}
			return $user_info_list;
		}else{
			return false;
		}
	}
	//获取个人用户最新红人
	function get_red_user($template_id,$rid, $flash){
		global $db_read,$flash,$db_hyq_read;
		$user = new user();
		$find_user = new find_user();

		$sql = "SELECT * FROM cms_template_data WHERE t_id ='{$template_id}'";
		$result = $db_read -> fetch_result($db_read -> query($sql));
		$userid_arr = array();
		if($result){
			foreach ($result as $kk=>$vv) {
				$userid_arr[] = $vv['des'];
			}
		}
		$userid_arr = array_unique($userid_arr);
		$num = count($userid_arr);
		
		if($num < 12){
			$new_num = 12 - $num;
			$find_user_key[] = $find_user -> get_find_key('level_1_role',$rid);
			
			if(!empty($find_user_key)){
				$find_user_res = $find_user -> get_user_list_by_sinter($find_user_key,'1', 12);
				$find_user_id = $find_user_res['list'];
			}else{
				$find_user_id = array();
			}
			
			if(is_array($find_user_id) && !empty($find_user_id)){
				foreach($find_user_id as $v){
					array_push($userid_arr,$v);
				}
				$userid_arr = array_unique($userid_arr);
				$userid_arr = array_slice($userid_arr,0,12);
			}
		}
		//print_r($userid_arr);exit;
		if(is_array($userid_arr) && $userid_arr){		
			foreach($userid_arr as $k=>$v){
				$uid = $v;
				$userinfo = $user -> get_userinfo($uid, $flash);
				
				$user_info_list[$uid] = array(
						'id' => $userinfo['id'],
						'icon_url' => $userinfo['icon_server_url'].$userinfo['icon_path_url'],
						'nickname' => $userinfo['nickname']
				);
			}
			return $user_info_list;
		}else{
			return false;
		}
    }