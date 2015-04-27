
<?php
	header("content-type: text/html; charset=utf-8");
	require_once ('../includes/common_home_inc.php');
	require_once (COMMON_PATH.'/base.class.php');
	require_once (COMMON_PATH.'/collect.class.php');
	require_once (COMMON_PATH.'/userprofile.class.php');
	require_once (COMMON_PATH.'/recruit.class.php');
	$user = new user();
	$base = new base();
	$userprofile = new userprofile();
	$recruit = new recruit();
	$collect = new collect();
	$collect_recruits_all =  $collect -> get_collect_list_by_user_type($user_info["id"],'recruit');
	//分页
	$recruit_num = count($collect_recruits_all);#招募总数
	$pagesize = 10;
	$sum_page =  ceil($recruit_num/$pagesize);	#分页总数
	isset($_REQUEST['page']) ? $page = intval($_REQUEST["page"]) : $page = 1;
	if($page < 1){
		$page = 1;
	}else if($page > 1 && $page > $sum_page){
		$page = $sum_page;
	}
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
	//2 在根据当前分页page  显示数据
	$from_rows = ($page - 1) * $pagesize;
	$collect_recruits =  $collect ->  get_collect_list_by_user_type($user_info["id"],'recruit',$from_rows,$pagesize,$flash);
	if($collect_recruits){
		foreach($collect_recruits as $collect_recruit){
			$recruit_info = $recruit -> get_recruit_info($collect_recruit['dynamic_id']);
			$recruit_type = $recruit -> get_recruit_type_info($recruit_info['type_id']);
			//$address_info = $base -> get_address_info($collect_recruit['province_id'],$collect_recruit['city_id'],$collect_recruit['district_id']);
			$city = $base -> get_city_info($recruit_info['city_id'],$flash);
			$service_2_list = array();
			//$service_3_list = array();
			if(is_array($service_2_arr = $recruit -> get_service_list_by_recruit($recruit_info['id']))){
				foreach($service_2_arr as $v2){
					/*if(is_array($service_3_arr = $recruit -> get_service_3_list_by_eid($v2['id']))){
						foreach($service_3_arr as $v3){
							if($service_3_info = $base -> get_service_info($v3['service_3_id'])){
								$service_3_list[] = $service_3_info['name'];
							}
						}
					}
					*/
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
			
			$collect_recruit_info[$collect_recruit['id']] = array(
				'name' => $recruit_info['name'],
				'uid' => $recruit_info['uid'],
				'dynamic_id' => $collect_recruit['dynamic_id'],
				'cover' => $recruit_info['cover_server_url'].$recruit_info['cover_path_url'],
				'interview_end_time' => $end_time,
				'address' => $city['cname'],
				'service_2_list' => $service_2_list
			);
		}
	}else{
		$collect_recruit_info = array();
	}
	$smarty -> assign('collect_recruit_info',$collect_recruit_info);
	$smarty -> assign('user_info',$user_info);
	$smarty -> assign('collect_type','recruit');
	$smarty -> display("home/collect_recruit.html");

?>