<?php
	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_inc.php');
	require_once(COMMON_PATH.'/find_recruit.class.php');
	require_once(COMMON_PATH.'/redis.class.php');
	require_once(COMMON_PATH.'/redis_find.class.php');
	require_once(COMMON_PATH.'/recruit.class.php');
	require_once(COMMON_PATH.'/page.class.php');
	$base = new base();
	$recruit = new recruit();
	$find_recruit = new find_recruit();
	$recruit_type_list = $recruit -> get_recruit_type_list();

	$city_all = $base -> get_city_list($flash);
	$chief_city = $base -> get_chief_city_list($flash);
	foreach($chief_city as $des_info){
		$chief_city_list[$des_info['des']] = $base -> get_city_info($des_info['des']);
	}
	$citys['ABCDEF'] = array();
	$citys['GHIJ'] = array();
	$citys['KLMN'] = array();
	$citys['OPQR'] = array();
	$citys['STUV'] = array();
	$citys['WXYZ'] = array();
	foreach($city_all as $city_info){
		if(in_array($city_info['spell'][0],array('a','b','c','d','e','f'))){
			$citys['ABCDEF'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('g','h','i','j'))){
			$citys['GHIJ'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('k','l','m','n'))){
			$citys['KLMN'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('o','p','q','r'))){
			$citys['OPQR'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('s','t','u','v'))){
			$citys['STUV'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('w','x','y','z'))){
			$citys['WXYZ'][$city_info['id']] = $city_info;
		}
	}

	//未选择条件
	$find_recruit_key[] = $find_recruit -> get_find_key('all','');
	if(isset($_REQUEST['city'])){
		$city = intval($_REQUEST['city']);
		if($city > 0){
			$find_recruit_key[] = $find_recruit -> get_find_key('city',$city);
		}
	}else{
		$city = 0;	
	}
	if($city == 0 || array_key_exists($city,$chief_city_list)){
		$other_color = "";
	}else{
		$other_color = "red";	
	}
	if(isset($_REQUEST['recruit_type'])){
		$recruit_type_option = intval($_REQUEST['recruit_type']);
		//echo $recruit_type_option;
		if($_REQUEST['recruit_type'] != "nolimit"){
			$find_recruit_key[] = $find_recruit -> get_find_key('type',$recruit_type_option);
		}	
	}else{
		$recruit_type_option = 'nolimit';	
	}
	// 4.2 将发送过来的id vlaue id数组  处理下  组成需要的数组  分配回页面遍历
	// 招募类型 $_REQUEST['recruit_type']
	if(isset($_REQUEST['recruit_type']) && $_REQUEST['recruit_type'] != ''){
		 foreach($recruit_type_list as $v){
			$result[$v['id']] = $v;
		}
		$arr['name'] = @$result[$_REQUEST['recruit_type']]['type'];
		$arr['name_value'] = 'recruit_type';
		$select_list[] = $arr;
	}
	//获取 城市名字 对应数组
		//$city_all = $base -> get_city_list($flash);
		foreach($city_all as $v){
			$city_name_arr[$v['id']] = $v['cname'];
		}
	if(isset($_REQUEST['city']) && $_REQUEST['city'] != ''){
		$arr['name'] = @$city_name_arr[intval($_REQUEST['city'])];
		$arr['name_value'] = 'city';
		$select_list[] = $arr;
	}
	if(!isset($select_list))$select_list = array();
	$smarty -> assign('select_list',$select_list);
	//4.5  分页
	if(!empty($find_recruit_key)){
		$find_recruit_res = $find_recruit -> get_recruit_list_by_sinter($find_recruit_key);
		$recruit_id_list = $find_recruit_res['list'];
		$recruit_id_count = $find_recruit_res['count'];
	}else{
		$recruit_id_list = array();
		$recruit_id_count = 0;	
	}
	$pagesize = 20;
	$sum_page = ceil($recruit_id_count/$pagesize);
	isset($_REQUEST['page']) ? $page = intval($_REQUEST["page"]) : $page = 1;
	if($page < 1){
		$page = 1;
	}else if($page > $sum_page){
		$page = $sum_page;
	}
	//获取url  砍掉？page  &page  添加新的
	//1 获取当前url
	$url = $_SERVER['REQUEST_URI'];
	if(strstr($url,"?page")){
		//包含?page  干掉 换？page= 
		$arr = explode('?page',$url);
		$url = $arr[0]."?page=";
	}elseif(strstr($url,"&page")){
		//有&page  干掉  换&page=
		$arr = explode('&page',$url);
		$url = $arr[0]."&page=";
	}elseif(strstr($url,"?")){
		// 直接加？page
		$url = $url."&page=";
	}else{
		$url = $url."?page=";
	}
	$page_list = $base -> getPaging($url.'$page',$page, $sum_page, 2);

	// 一个分页不显示标志
	if(count($page_list) < 2){
		$page_status = 'false';
	}else{
		$page_status = 'true';
	}
	$smarty -> assign('page_status',$page_status);
	//var_dump($page_list);
	$page_first_url = "{$url}1";
	$page_last_url = "{$url}".$sum_page;
	($page > 1) ? $page_pre_url = $url.($page - 1) : $page_pre_url = $page_first_url;
	($page < $sum_page) ? $page_next_url = $url.($page + 1) : $page_next_url = $page_last_url;
	
	$smarty -> assign('page_list', $page_list);		#页数数组
	$smarty -> assign('page_first_url', $page_first_url);	
	$smarty -> assign('page_last_url', $page_last_url);		
	$smarty -> assign('page_pre_url', $page_pre_url);		
	$smarty -> assign('page_next_url', $page_next_url);
	$smarty -> assign('goto_url', $url);
	$smarty -> assign('sum_page', $sum_page);
	if(!empty($find_recruit_key)){
		$find_recruit_res = $find_recruit -> get_recruit_list_by_sinter($find_recruit_key,$page,$pagesize);
		$recruit_id_list = $find_recruit_res['list'];
		$recruit_id_count = $find_recruit_res['count'];
	}else{
		$recruit_id_list = array();
		$recruit_id_count = 0;	
	}
	$find_recruit_list = array();
	foreach($recruit_id_list as $find_recruit_id){
			$recruit_info = $recruit -> get_recruit_info($find_recruit_id);
			$recruit_type = $recruit -> get_recruit_type_info($recruit_info['type_id']);
			$service_2_list = array();
			$city_info = $base -> get_city_info($recruit_info['city_id'],$flash);
			if(is_array($service_2_arr = $recruit -> get_service_list_by_recruit($recruit_info['id']))){
				foreach($service_2_arr as $v2){
					/*if(is_array($service_3_arr = $recruit -> get_service_3_list_by_eid($v2['id']))){
						foreach($service_3_arr as $v3){
							if($service_3_info = $base -> get_service_info($v3['service_3_id'])){
								$service_3_list[] = $service_3_info['name'];
							}
						}
					}*/
					if($service_2_info = $base -> get_service_info($v2['service_2_id'])){
						$service_2_list[] = $service_2_info['name'];
					}
				}
			}
			$timediff = strtotime($recruit_info['interview_end_time']) - time();
			if($timediff > 3600*48){
				$end_time = date('Y-m-d',strtotime($recruit_info['interview_end_time']));
			}else if($timediff > 0 && $timediff < 3600*48){
				$end_time = '剩余'.ceil($timediff/3600).'小时';
			}else{
				$end_time = '已过期';
			}
			
			$find_recruit_list[$find_recruit_id] = array(
				'name' => $recruit_info['name'],
				'uid' => $recruit_info['uid'],
				'dynamic_id' => $find_recruit_id,
				'cover' => $recruit_info['cover_server_url'].$recruit_info['cover_path_url'],
				'interview_end_time' => $end_time,
				'address' => $city_info['cname'],
				'service_2_list' => $service_2_list
			);
		}		
	$smarty -> assign('recruit_type_list',$recruit_type_list);
	$smarty -> assign('citys',$citys);
	$smarty -> assign('city_option',$city);
	$smarty -> assign('recruit_type_option',$recruit_type_option);
	$smarty -> assign('find_recruit_list',$find_recruit_list);
	$smarty -> assign('chief_city_list',$chief_city_list);
	$smarty -> assign('other_color',$other_color);
	$smarty -> display("list/recruit_list.html");
?>