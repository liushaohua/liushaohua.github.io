<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
//	require_once ('../includes/common_home_inc.php');
	require_once('../includes/common_home_inc.php');
	
	
	$user = new user;
	$base = new base;
	$apply = new apply;
	$recruit = new recruit;
	$invite = new invite;
	
	$userid = $user_info['id'];
	// //$userid = 45618;
	$usertype = $user_info['user_type'];
	
	//$recruit_list = $recruit -> get_recruit_list_by_user($userid, 0, 0, $flash);
	$recruit_list = $recruit -> get_checked_recruit_list_by_user($userid, 0, 0, $flash);

	//分页
	$recruit_num = count($recruit_list);#招募总数
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
	//$show_recruit_list =  $recruit -> get_recruit_list_by_user($userid,$from_rows,$pagesize,$flash);
	$show_recruit_list =  $recruit -> get_checked_recruit_list_by_user($userid,$from_rows,$pagesize,$flash);
          
          
	
	
	
          
	if($show_recruit_list){
		foreach ($show_recruit_list as $key => $value) {
			$type_id = $value['type_id'];
			$result = $recruit -> get_recruit_type_info($type_id, $flash);
			$show_recruit_list[$key]['type'] = $result['type'];
		}
		$smarty -> assign('recruit_list',$show_recruit_list);
	}else{
		$show_recruit_list = '';
		$smarty -> assign('recruit_list',$show_recruit_list);
	}
	
	
	
	
	$work_type = 'mywork_recruit'; 
	$smarty -> assign('work_type',$work_type);
	$smarty -> assign('usertype',$usertype);
	$smarty -> display("home/mywork_recruit.html");