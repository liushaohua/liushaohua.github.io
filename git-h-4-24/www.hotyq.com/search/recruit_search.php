<?php
	header("content-type:text/html;charset=utf-8");
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
	require_once (COMMON_PATH.'/recruit.class.php');	
	$user = new user();
	$base = new base();
	$orgprofile = new orgprofile();	
	$search_user = new search_user();
	$search_org = new search_org();
	$search_recruit = new search_recruit();	
	$recruit = new recruit();
	$userprofile = new userprofile();

	//1 获取关键字
	isset($_REQUEST['q']) ? $q = clear_gpq($_REQUEST['q']) : $q = '';
	//2 搜索页面  一次搜索 红人 机构 招募 个数都得算出来
	$search_user_res = $search_user -> get_user_list($q);#红人
	$search_org_res = $search_org -> get_org_list($q);#机构
	$search_recruit_res = $search_recruit -> get_recruit_list($q);#招募
	$search_total_num = $search_user_res['count'] + $search_org_res['count'] + $search_recruit_res['count'];#总数
	$search_recruit_num = $search_recruit_res['count'];#招募数量
	
	$pagesize = 20;
	$sum_page =  ceil($search_recruit_num/$pagesize);	#分页总数
	isset($_REQUEST['page']) ? $page = intval($_REQUEST["page"]) : $page = 1;
	if($page < 1){
		$page = 1;
	}else if($page > 1 && $page > $sum_page){
		$page = $sum_page;
	}
	
	$page_prefix_url = "/search/recruit?q=".urlencode($q)."&page=";	#分页前缀
	$page_list = $base -> getPaging('/search/recruit?q='.urlencode($q).'&page=$page',$page, $sum_page, 2);
	// 一个分页不显示标志
	if(count($page_list) < 2){
		$page_status = 'false';
	}else{
		$page_status = 'true';
	}
	$smarty -> assign('page_status',$page_status);
	$page_first_url = "{$page_prefix_url}1";
	$page_last_url = "{$page_prefix_url}".$sum_page;
	($page > 1) ? $page_pre_url = $page_prefix_url.($page - 1) : $page_pre_url = $page_first_url;
	($page < $sum_page) ? $page_next_url = $page_prefix_url.($page + 1) : $page_next_url = $page_last_url;
	$smarty -> assign('page_list', $page_list);		#页数数组
	$smarty -> assign('page_first_url', $page_first_url);	
	$smarty -> assign('page_last_url', $page_last_url);		
	$smarty -> assign('page_pre_url', $page_pre_url);		
	$smarty -> assign('page_next_url', $page_next_url);
	$smarty -> assign('goto_url', $page_prefix_url);
	$smarty -> assign('sum_page', $sum_page);
	
	//2 根据关键字 查询结果集
	$recruit_search_list =  $search_recruit -> get_recruit_list($q,$page,$pagesize);
	if(isset($recruit_search_list)){
		$recruit_id_list = $recruit_search_list['list'];
		$recruit_count = $recruit_search_list['count'];
	}
	//var_dump($recruit_search_list);echo '<hr>';
	$search_user_res = $search_user -> get_user_list($q);
	$search_org_res = $search_org -> get_org_list($q);
	

	if(isset($search_org_res)){
		$org_id_list = $search_org_res['list'];
		$search_org_num = $search_org_res['count'];
	}
	$smarty -> assign('search_org_num',$search_org_num);
	if(isset($search_user_res)){
		$user_id_list = $search_user_res['list'];
		$search_user_num = $search_user_res['count'];
	}
	$smarty -> assign('search_user_num',$search_user_num);	
	if(isset($recruit_search_list)){
		$recruit_id_list = $recruit_search_list['list'];
		$recruit_count = $recruit_search_list['count'];
	}
	$search_total = $recruit_count + $search_user_num + $search_org_num;
	$smarty -> assign('search_total',$search_total);		
	//3 -----------------处理招募详情------start-------------------------
		if(is_array($recruit_id_list)){
			foreach($recruit_id_list as $k => $v){
				//$recruit_list[] = $find_recruit -> get_recruit_info($v);
				$recruit_list[$k] = $recruit -> get_recruit_info($v['id']);
			}
		}		
		if(!isset($recruit_list)) $recruit_list = array();
		$search_recruit_info = array();
		if(is_array($recruit_list)){
			foreach($recruit_list as $search_recruit){
				$recruit_type = $recruit -> get_recruit_type_info($search_recruit['type_id']);
				//$address_info = $base -> get_address_info($search_recruit['province_id'],$search_recruit['city_id'],$search_recruit['district_id']);
				$city = $base -> get_city_info($search_recruit['city_id'],$flash);
				$service_2_list = array();
				if(is_array($service_2_arr = $recruit -> get_service_list_by_recruit($search_recruit['id']))){
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
				$timediff = strtotime($search_recruit['interview_end_time']) - time();
				if($timediff > 3600*48){
					$end_time = date('Y-m-d',strtotime($search_recruit['interview_end_time']));
				}else if($timediff > 0 && $timediff < 3600*48){
					$end_time = '剩余'.ceil($timediff/3600).'小时';
				}else{
					$end_time = '已过期';
				}
				
				$search_recruit_info[$search_recruit['id']] = array(
					'name' => $search_recruit['name'],
					'uid' => $search_recruit['uid'],
					'cover' => $search_recruit['cover_server_url'].$search_recruit['cover_path_url'],
					'interview_end_time' => $end_time,
					'status' => $search_recruit['status'],
					'address' => $city['cname'],
					'service_2_list' => $service_2_list
				);
			}
		}

	//-------------------------end------------------------
	$smarty -> assign('search_recruit_info',$search_recruit_info);
	$smarty -> assign('recruit_count',$recruit_count);
	//搜索热词
	$search_hot = $base -> get_hot_search_words($flash);
	if($search_hot){
		foreach($search_hot as $v){
			if($v['des'] == $q) $smarty -> assign('search_banner',$v['pic']); 
		}
	}
	$smarty -> assign('q',$q);
	$smarty -> assign('tr_q',urlencode($q));	
	$smarty -> display("search/recruit_search.html");
?>