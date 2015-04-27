<?php
	header("content-type: text/html; charset=utf-8");
  	require_once('../../includes/common_inc.php');
  	require_once (COMMON_PATH.'/page.class.php');
  	require_once (COMMON_PATH.'/album.class.php');
  	require_once (COMMON_PATH.'/service.class.php');
  	session_start();
    $user = new user();
    $album = new album();
    $userprofile = new userprofile();
    $base = new base();
    $recruit = new recruit();
    $service = new service();
	
	//if(!isset($_REQUEST['id']) || empty($_REQUEST['id'])){
	//	header("location:/user/"); 
	//}	
  	@$userid = intval($_REQUEST['id']);
  	$userid = 100015;

  
	

	//-------------------他的红形象  end-----------------------
	//Ta发布的招募
	$recruit_list = array();
	//1 根据当前页数$page 先显示每次的分页  总数
	$result = $recruit -> get_checked_recruit_list_by_user($userid,$flash);
	$recruit_num = count($result);#招募总数
	$pagesize = 4;
	$sum_page =  ceil($recruit_num/$pagesize);	#分页总数
	isset($_REQUEST['page']) ? $page = intval($_REQUEST["page"]) : $page = 1;
	if($page < 1){
		$page = 1;
	}else if($page > 1 && $page > $sum_page){
		$page = $sum_page;
	}
	$url = $_SERVER['REQUEST_URI'];
	//var_dump($url);
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
	//$smarty -> assign('page_first_url', $page_first_url);	
	//$smarty -> assign('page_last_url', $page_last_url);		
	$smarty -> assign('page_pre_url', $page_pre_url);		
	$smarty -> assign('page_next_url', $page_next_url);
	//$smarty -> assign('goto_url', $url);
	$smarty -> assign('sum_page', $sum_page);
	//2 在根据当前分页page  显示数据
	$from_rows = ($page - 1) * $pagesize;
	$recruit_list =  $recruit -> get_checked_recruit_list_by_user($userid,$from_rows,$pagesize,$flash);
	//---------------------end---------wangyifan-------
	//2.5 生成需要处理的数组
		//获取 城市名字 对应数组
		$city_list = $base -> get_city_list($flash);
		foreach($city_list as $v){
			$city_name_arr[$v['id']] = $v['cname'];
		}
		//获取 服务名字 对应数组
		$service_name_arr = $base -> get_service_list($flash);
	//3 处理招募结果集
	if(!empty($recruit_list)){
		foreach($recruit_list as $k => $v){
			$recruit_list[$k]['city_name'] = $city_name_arr[$v['city_id']];#处理城市
			$recruit_list[$k]['interview_end_time'] =  date( "m月d日", strtotime($v['interview_end_time']) );#处理截止时间
			//处理二级服务
			$recruit_list[$k]['recruit_service_list'] = $recruit -> get_service_list_by_recruit($v['id'],$flash);#可能多个一级二级
			foreach($recruit_list[$k]['recruit_service_list'] as $k1 => $v1){
				$recruit_list[$k]['recruit_service_list'][$k1]['service_2_name'] = $service_name_arr[$v1['service_2_id']];#获取每一项二级服务name
			}
		}	
	}	
	//var_dump($recruit_list);
	$smarty -> assign('recruit_list',$recruit_list);
	//-------------------------end------------
	

	$smarty -> display("suntianxing/zzh/3.html");

?>