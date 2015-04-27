<?php
	header("content-type:text/html;charset=utf-8");
	require_once('../includes/common_inc.php');
	require_once(COMMON_PATH.'/solr.class.php');
	require_once(COMMON_PATH.'/userprofile.class.php');
	require_once(COMMON_PATH.'/orgprofile.class.php');
	require_once(COMMON_PATH.'/search_user.class.php');
	require_once(COMMON_PATH.'/search_org.class.php');
	require_once(COMMON_PATH.'/search_recruit.class.php');
	require_once(COMMON_PATH.'/solr_user.class.php');
	require_once(COMMON_PATH.'/solr_org.class.php');
	require_once(COMMON_PATH.'/solr_recruit.class.php');
	$search_user = new search_user();
	$search_org = new search_org();
	$search_recruit = new search_recruit();
	$user = new user();
	$base = new base();
	$userprofile = new userprofile() ;
	isset($_REQUEST['q']) ? $q = clear_gpq($_REQUEST['q']) : $q = '';
	isset($_REQUEST['from']) ? $from = clear_gpq($_REQUEST['from']) : $from = '';
	$search_user_all = $search_user -> get_user_list($q);
	$search_org_res = $search_org -> get_org_list($q);
	$search_recruit_res = $search_recruit -> get_recruit_list($q);
	$search_total_num = $search_user_all['count'] + $search_org_res['count'] + $search_recruit_res['count'];
	$search_user_num = $search_user_all['count'];
	$pagesize = 50;
	$sum_page =  ceil($search_user_num/$pagesize);	#分页总数
	isset($_REQUEST['page']) ? $page = intval($_REQUEST["page"]) : $page = 1;
	if($page < 1){
		$page = 1;
	}else if($page > 1 && $page > $sum_page){
		$page = $sum_page;
	}

	$page_prefix_url = "/search/user?q=".urlencode($q)."&page=";	#分页前缀
	$page_list = $base -> getPaging('/search/user?q='.urlencode($q).'&page=$page',$page, $sum_page, 2);	
	$page_first_url = "{$page_prefix_url}1";
	$page_last_url = "{$page_prefix_url}".$sum_page;
	($page > 1) ? $page_pre_url = $page_prefix_url.($page - 1) : $page_pre_url = 1;
	($page < $sum_page) ? $page_next_url = $page_prefix_url.($page + 1) : $page_next_url = $page_last_url;
	if(count($page_list) < 2){
		$page_status = 'false';
	}else{
		$page_status = 'true';
	}
	$smarty -> assign('page_status', $page_status);
	$smarty -> assign('page_list', $page_list);		#页数数组
	$smarty -> assign('page_first_url', $page_first_url);	
	$smarty -> assign('page_last_url', $page_last_url);		
	$smarty -> assign('page_pre_url', $page_pre_url);		
	$smarty -> assign('page_next_url', $page_next_url);
	$smarty -> assign('goto_url', $page_prefix_url);
	$smarty -> assign('sum_page', $sum_page);
	$search_user_res =  $search_user -> get_user_list($q,$page,$pagesize);
	$user_list = $search_user_res['list'];
	$search_user_list = array();
	foreach($user_list as $user_arr){
		$userinfo = $user -> get_userinfo($user_arr['id']);
		$user_profile = $userprofile -> get_user_profile($user_arr['id']);
		is_array($user_role_list = $userprofile -> get_role_list_by_user($user_arr['id'],$flash)) ? null : $user_role_list = array();
		$role_str = '';
		foreach($user_role_list as $role_info){
			$role_str .= $role_info['name'].'/'; 
		}
		$role_str = rtrim($role_str,'/');
		//$address_info = $base -> get_address_info($user_profile['province_id'],$user_profile['city_id'],$user_profile['district_id']);
		$city = $base -> get_city_info($user_profile['city_id'],$flash);
		$search_user_list[$user_arr['id']] = array(
			'id' => $user_arr['id'],
			'icon_url' => $userinfo['icon_server_url'].$userinfo['icon_path_url'],
			'nickname' => $userinfo['nickname'],
			'level' =>  $userinfo['level'],
			'email_status' => $userinfo['email_status'],
			'mobile_status' => $userinfo['mobile_status'],
			'identity_card_status' => $userinfo['identity_card_status'],
			'address' => $city['cname'],
			'role_str' => $role_str
		);		
	}
	//搜索热词
	$search_hot = $base -> get_hot_search_words($flash);
	if($search_hot){
		foreach($search_hot as $v){
			if($v['des'] == $q) $smarty -> assign('search_banner',$v['pic']); 
		}
	}
	$smarty -> assign('q',$q);
	$smarty -> assign('tr_q',urlencode($q));
	$smarty -> assign('search_total_num',$search_total_num);
	$smarty -> assign('search_user_num',$search_user_all['count']);
	$smarty -> assign('search_org_num',$search_org_res['count']);
	$smarty -> assign('search_recruit_num',$search_recruit_res['count']);
	$smarty -> assign('search_user_list',$search_user_list);
	$max_num = max($search_user_all['count'],$search_org_res['count'],$search_recruit_res['count']);
	if($from == 'org' || $from == 'recruit'){
		$smarty -> display("search/user_search.html");
		exit;	
	}
	if($max_num == $search_user_all['count']){
		$smarty -> display("search/user_search.html");
		exit;	
	}elseif($max_num == $search_org_res['count']){
		header("location:http://www.hotyq.com/search/org?q=".urlencode($q));
		exit;
	}elseif($max_num == $search_recruit_res['count']){
		header("location:http://www.hotyq.com/search/recruit?q=".urlencode($q));
		exit;
	}
	
?>