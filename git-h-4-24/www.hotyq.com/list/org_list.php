<?php
	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_inc.php');
	require_once(COMMON_PATH.'/redis.class.php');
	require_once(COMMON_PATH.'/redis_find.class.php');
	require_once(COMMON_PATH.'/find_org.class.php');
	require_once (COMMON_PATH.'/page.class.php');
 	$redis_find = new redis_find();
	$user = new user();
	$base = new base();
	$orgprofile = new orgprofile();

	$find_org = new find_org();
	$redis_find = new redis_find();
	$base = new base();
	$city_all = $base -> get_city_list($flash);
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
	$chief_city = $base -> get_chief_city_list($flash);
	foreach($chief_city as $des_info){
		$chief_city_list[$des_info['des']] = $base -> get_city_info($des_info['des']);
	}
	//接收所传过来变量	
	isset($_REQUEST['type']) ? $typeid = intval($_REQUEST['type']) : $typeid = 0;
	isset($_REQUEST['city']) ? $city_val = intval($_REQUEST['city']) : $city_val = 0;
	if($city_val == 0 || array_key_exists($city_val,$chief_city_list)){
		$other_color = "";
	}else{
		$other_color = "red";	
	}
	//获取机构类型列表
	$org_type_list = $base -> get_org_type_list($flash);
	//按机构类型筛选 
	if($typeid == 0){
	    $find_keys[] = $find_org -> get_find_key('all','');	#all键
	}else{
		foreach($org_type_list as $k => $v){
			$result[$v['id']] = $v;
			if($v['id'] == $typeid){
				$tname = $org_type_list[$k]['name'];
			}
		}
		if(isset($tname)){
			$return_arr['id'] = $typeid;
			$return_arr['name'] = $tname;
			$return_arr['value'] = 'type';
			$option_list[] = $return_arr;		
		}
		$find_keys[] = $find_org -> get_find_key('type', $typeid);	#组成键type的键
	}
	
	//按市筛选 
	$city_item_name = $base -> get_city_info($city_val, $flash);
	isset($city_item_name) ? $city_name_val = $city_item_name['cname'] : $city_name_val = '不限';
	if(empty($city_name_val)) $city_name_val = '不限';
	if($city_val == 0){
		$find_keys[] = $find_org -> get_find_key('all','');		#all键
	}else{
		//if(!in_array($city_val,array(1,2,73,234,343,344,345))){
			$return_arr['id'] = $city_val;
			$return_arr['name'] = $city_name_val;
			$return_arr['value'] = 'city';
			$option_list[] = $return_arr;	
		//}
		$find_keys[] =  $find_org -> get_find_key('city', $city_val);	#组成键 市的键
	}
	
	$find_org_list_res = $find_org -> get_org_list_by_sinter($find_keys); 	
	$count_nums = $find_org_list_res['count'];		#计算总数目
	//分页时所传条件
	if($typeid == 0){
		$typeid_sth = '';
	}else{
		$typeid_sth ='type='.$typeid.'&';	
	}
	if($city_val == 0){
		$city_val_sth = '';		
	}else{
		$city_val_sth = 'city='.$city_val.'&';	
	}
	$sth = $typeid_sth.$city_val_sth;	
	
	
	
	//分页开始
	$pagesize = 50;
	$sum_page =  ceil($count_nums/$pagesize);	#分页总数
	isset($_REQUEST['page']) ? $page = intval($_REQUEST["page"]) : $page = 1;
	if($page < 1){
		$page = 1;
	}else if($page > 1 && $page > $sum_page){
		$page = $sum_page;
	}

	$page_prefix_url = "/list/org?".$sth."page=";	#分页前缀
	$page_list = $base -> getPaging('/list/org?'.$sth.'page=$page',$page, $sum_page, 2);	#地址,当前页,总页数偏移量
	$page_first_url = "{$page_prefix_url}1";
	$page_last_url = "{$page_prefix_url}".$sum_page;
	//var_dump($sum_page);
	if($page > 1){
		$page_pre_url = $page_prefix_url.($page - 1);
	}else{
		$page_pre_url = $page_first_url;
	}
	//($page > 1) ? $page_pre_url = $page_prefix_url.($page - 1) : $page_pre_url = $page_first_url;
	if($page < $sum_page){
		$page_next_url = $page_prefix_url.($page + 1);
	}else{
		$page_next_url = $page_next_url = $page_last_url;
	}
	if(count($page_list) < 2){
		$page_status = 'false';
	}else{
		$page_status = 'true';
	}
	
	
	
	$find_org_list_res = $find_org -> get_org_list_by_sinter($find_keys,$page,$pagesize); 	#得到筛选的机构id 列表
	$find_org_id_list = $find_org_list_res['list'];		#得到筛选机构的ID列表	
	if(isset($create_time_tmp_key)) $find_org -> del_key($create_time_tmp_key);
	
	if(is_array($find_org_id_list)){
		foreach($find_org_id_list as $k => $v){
			$org_find_all_list[$k] = $user -> get_userinfo($v);
			$org_find_all_list[$k]['info'] = $orgprofile -> get_org_profile($v);
			$org_find_all_list[$k]['type_name'] = $base -> get_org_type_info($org_find_all_list[$k]['info']['type'],$flash);
			$org_find_all_list[$k]['address'] = $base -> get_address_info($org_find_all_list[$k]['info']['province_id'],$org_find_all_list[$k]['info']['city_id'],$org_find_all_list[$k]['info']['district_id'],$flash);				
		}
		if(!isset($org_find_all_list)){
			$org_find_all_list = array();
		}
		foreach($org_find_all_list as $k => $v){	#总页数 $org_total_list;
			$org_total_list[$k]['id'] = $org_find_all_list[$k]['id'];
			$org_total_list[$k]['level'] = $org_find_all_list[$k]['level'];
			$org_total_list[$k]['nickname'] = $org_find_all_list[$k]['nickname'];
			$org_total_list[$k]['email_status'] = $org_find_all_list[$k]['email_status'];
			$org_total_list[$k]['mobile_status'] = $org_find_all_list[$k]['mobile_status'];
			$org_total_list[$k]['business_card_status'] = $org_find_all_list[$k]['business_card_status'];
			$org_total_list[$k]['identity_card_status'] = $org_find_all_list[$k]['identity_card_status'];
			$org_total_list[$k]['icon_server_url'] = $org_find_all_list[$k]['icon_server_url'];
			$org_total_list[$k]['icon_path_url'] = $org_find_all_list[$k]['icon_path_url'];
			$org_total_list[$k]['type_name'] = $org_find_all_list[$k]['type_name']['name'];
			$org_total_list[$k]['address'] = $org_find_all_list[$k]['address']['address'];
			$org_total_list[$k]['state'] = $org_find_all_list[$k]['info']['state'];
			$org_total_list[$k]['legal_person'] = $org_find_all_list[$k]['info']['legal_person'];
		}	
	}
	if(!isset($org_total_list)){
		$org_total_list = array();
	}
	if(!isset($option_list)) $option_list = array();
	$smarty -> assign('page_list', $page_list);		#页数数组
	$smarty -> assign('page_first_url', $page_first_url);	
	$smarty -> assign('page_last_url', $page_last_url);		
	$smarty -> assign('page_pre_url', $page_pre_url);		
	$smarty -> assign('page_next_url', $page_next_url);
	$smarty -> assign('goto_url', $page_prefix_url );
	$smarty -> assign('sum_page', $sum_page);	
	$smarty -> assign('page_status', $page_status);	
	$smarty -> assign('option_list',$option_list);  #option 选择的条件;
	$smarty -> assign('typeid',$typeid);	#判断选择type
	$smarty -> assign('citys',$citys);	#判断province
	$smarty -> assign('city_val',$city_val);	#判断city
	$smarty -> assign('city_name_val',$city_name_val);	#判断city name
	$smarty -> assign('org_type_list',$org_type_list);	#机构类型
	$smarty -> assign('org_total_list',$org_total_list);  #总数
	$smarty -> assign('chief_city_list',$chief_city_list);
	$smarty -> assign('other_color',$other_color);
	$smarty -> display("list/org_list.html");

?>