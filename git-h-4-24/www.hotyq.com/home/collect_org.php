
<?php
	header("content-type: text/html; charset=utf-8");
	require_once ('../includes/common_home_inc.php');
	require_once (COMMON_PATH.'/orgprofile.class.php');
	require_once (COMMON_PATH.'/base.class.php');
	require_once (COMMON_PATH.'/collect.class.php');
	$user = new user();
	$base = new base();
	$orgprofile = new orgprofile();
    $collect = new collect();
    $recruit = new recruit();
	$collect_orgs_all =  $collect -> get_collect_list_by_user_type($user_info["id"],'org');
	//分页
	$recruit_num = count($collect_orgs_all);#招募总数
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
	$collect_orgs =  $collect ->  get_collect_list_by_user_type($user_info["id"],'org',$from_rows,$pagesize,$flash);

	if($collect_orgs){
		foreach($collect_orgs as $collect_org){
		$orginfo = $user -> get_userinfo($collect_org['dynamic_id']);
		$org_profile = $orgprofile -> get_org_profile($collect_org['dynamic_id']);
		if(array_key_exists($org_profile['state'],$COMMON_CONFIG['STATE'])){
			$state = $COMMON_CONFIG['STATE'][$org_profile['state']];	
		}else{
			$state = '';	
		}
		$org_type = $base -> get_org_type_info($org_profile['type']);
		$address_info = $base -> get_address_info($org_profile['province_id'],$org_profile['city_id'],$org_profile['district_id']);
		$collect_org_info[$collect_org['id']] = array(
				'dynamic_id' => $collect_org['dynamic_id'],
				'icon_url' => $orginfo['icon_server_url'].$orginfo['icon_path_url'],
				'nickname' => $orginfo['nickname'],
				'level' =>  $orginfo['level'],
				'email_status' => $orginfo['email_status'],
				'mobile_status' => $orginfo['mobile_status'],
				'identity_card_status' => $orginfo['identity_card_status'],
				'business_card_status' => $orginfo['business_card_status'],
				'address' => $address_info['address'],
				'state' => $state,
				'org_type' => $org_type['name']
			);
		}
	}else{
		$collect_org_info = array();
    }
	//别乱动  suntianxin
	$recruit_list = $recruit -> get_recruit_list_by_user_for_invite($user_info["id"]);
	if(!$recruit_list){
		$smarty -> assign('hasRecruit','');
	}else{
		$smarty -> assign('hasRecruit','OK');
	}    
	$smarty -> assign('collect_org_info',$collect_org_info);
	$smarty -> assign('user_info',$user_info);
	$smarty -> assign('collect_type','org');
	$smarty -> display("home/collect_org.html");

?>
