<?php
	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_inc.php');
	require_once(COMMON_PATH.'/search_org.class.php');
	require_once(COMMON_PATH.'/solr.class.php');
	require_once(COMMON_PATH.'/userprofile.class.php');
	require_once(COMMON_PATH.'/orgprofile.class.php');
	require_once(COMMON_PATH.'/search_user.class.php');
	require_once(COMMON_PATH.'/search_recruit.class.php');
	require_once(COMMON_PATH.'/solr_user.class.php');
	require_once(COMMON_PATH.'/solr_org.class.php');
	require_once(COMMON_PATH.'/solr_recruit.class.php');	
	require_once (COMMON_PATH.'/page.class.php');	
	$user = new user();
	$base = new base();
	$orgprofile = new orgprofile();	
	$search_user = new search_user();
	$search_org = new search_org();
	$search_recruit = new search_recruit();	

	isset($_REQUEST['q']) ? $q = clear_gpq($_REQUEST['q']) : $q = '';
	
	$org_search_list = $search_org -> get_org_list($q); 	#计算出数目页数	
	if(isset($org_search_list)){
		$org_id_list = $org_search_list['list'];
		$search_org_num = $org_search_list['count'];
	}	
	//分页开始
	$pagesize = 50;	
	$sum_page =  ceil($search_org_num/$pagesize);	#分页总数
	isset($_REQUEST['page']) ? $page = intval($_REQUEST["page"]) : $page = 1;
	if($page < 1){
		$page = 1;
	}else if($page > 1 && $page > $sum_page){
		$page = $sum_page;
	}
	$page_prefix_url = "/search/org?q=".urlencode($q)."&page=";	#分页前缀
	$page_list = $base -> getPaging($page_prefix_url.'$page',$page, $sum_page, 2);	#地址,当前页,总页数偏移量
	$page_first_url = "{$page_prefix_url}1";
	$page_last_url = "{$page_prefix_url}".$sum_page;
	if($page > 1){
		$page_pre_url = $page_prefix_url.($page - 1);
	}else{
		$page_pre_url = $page_first_url;
	}
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
	$org_search_list = $search_org -> get_org_list($q, $page, $pagesize); 	#计算出数目页数
	//var_dump($id_list);
	$search_user_res = $search_user -> get_user_list($q);
	$search_recruit_res = $search_recruit -> get_recruit_list($q);
	if(isset($org_search_list)){
		$org_id_list = $org_search_list['list'];
		$search_org_num = $org_search_list['count'];
	}
	if(isset($search_user_res)){
		$user_id_list = $search_user_res['list'];
		$search_user_num = $search_user_res['count'];
	}
	if(isset($search_recruit_res)){
		$recruit_id_list = $search_recruit_res['list'];
		$search_recruit_num = $search_recruit_res['count'];
	}			
	//var_dump($org_id_list);
	//var_dump($search_org_num);
	if(is_array($org_id_list)){
		foreach($org_id_list as $k => $v){
			$search_org_id_list[] = $v['id'];
		}
	}	
	//var_dump($search_org_id_list);
	if(!isset($search_org_id_list)){
		$search_org_id_list = array();
	}	
	if(is_array($search_org_id_list)){
		foreach($search_org_id_list as $k => $v){
			$org_search_all_list[$k] = $user -> get_userinfo($v);
			$org_search_all_list[$k]['info'] = $orgprofile -> get_org_profile($v);
			$org_search_all_list[$k]['type_name'] = $base -> get_org_type_info($org_search_all_list[$k]['info']['type'],$flash);
			//$org_search_all_list[$k]['address'] = $base -> get_address_info($org_search_all_list[$k]['info']['province_id'],$org_search_all_list[$k]['info']['city_id'],$org_search_all_list[$k]['info']['district_id'],$flash);
			$org_search_all_list[$k]['address'] = $base -> get_city_info($org_search_all_list[$k]['info']['city_id'],$flash);				
		}
		if(!isset($org_search_all_list)){
			$org_search_all_list = array();
		}
		foreach($org_search_all_list as $k => $v){	#总页数 $org_total_list;
			$org_total_list[$k] ['id'] = $org_search_all_list[$k]['id'];
			$org_total_list[$k] ['level'] = $org_search_all_list[$k]['level'];
			$org_total_list[$k] ['nickname'] = $org_search_all_list[$k]['nickname'];
			$org_total_list[$k] ['email_status'] = $org_search_all_list[$k]['email_status'];
			$org_total_list[$k] ['mobile_status'] = $org_search_all_list[$k]['mobile_status'];
			$org_total_list[$k] ['business_card_status'] = $org_search_all_list[$k]['business_card_status'];
			$org_total_list[$k] ['identity_card_status'] = $org_search_all_list[$k]['identity_card_status'];
			$org_total_list[$k] ['icon_server_url'] = $org_search_all_list[$k]['icon_server_url'];
			$org_total_list[$k] ['icon_path_url'] = $org_search_all_list[$k]['icon_path_url'];
			$org_total_list[$k] ['type_name'] = $org_search_all_list[$k]['type_name']['name'];
			$org_total_list[$k] ['address'] = $org_search_all_list[$k]['address']['cname'];
			$org_total_list[$k] ['state'] = $org_search_all_list[$k]['info']['state'];
			$org_total_list[$k] ['legal_person'] = $org_search_all_list[$k]['info']['legal_person'];			
		}	
		if(!isset($org_total_list)){
			$org_total_list = array();
		}
	}
	//搜索热词
	$search_hot = $base -> get_hot_search_words($flash);
	if($search_hot){
		foreach($search_hot as $v){
			if($v['des'] == $q) $smarty -> assign('search_banner',$v['pic']); 
		}
	}
	//var_dump($org_total_list);
	$search_total_num = $search_user_num + $search_recruit_num + $search_org_num;
	$smarty -> assign('q',$q);
	$smarty -> assign('tr_q',urlencode($q));
	$smarty -> assign('page_list', $page_list);		#页数数组
	$smarty -> assign('page_first_url', @$page_first_url);	
	$smarty -> assign('page_last_url', @$page_last_url);		
	$smarty -> assign('page_pre_url', @$page_pre_url);		
	$smarty -> assign('page_next_url', @$page_next_url);
	$smarty -> assign('page_status', @$page_status);
	$smarty -> assign('goto_url', $page_prefix_url );
	$smarty -> assign('sum_page', $sum_page);	
	$smarty -> assign('search_total_num',$search_total_num);
	$smarty -> assign('search_user_num',$search_user_num);
	$smarty -> assign('search_recruit_num',$search_recruit_num);	
	$smarty -> assign('org_total_list',$org_total_list);  #搜索数目列表	
	$smarty -> assign('search_org_num',$search_org_num);	#搜索数目
	$smarty -> display("search/org_search.html");	
?>