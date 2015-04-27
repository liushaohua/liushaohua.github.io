<?php
	header("content-type: text/html; charset=utf-8");
  	require_once('../includes/common_inc.php');
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
   	$userinfo =	$user -> get_userinfo($userid,$flash);
    if(!$userinfo){
		$base -> go_404();
    }    		
   	if($userinfo['user_type'] == 'org'){
  		header("location:/user/"); 
  		exit;
   	} 
    $userprofile_info =	$userprofile -> get_user_profile($userid,$flash);
    if(!$userprofile_info){
        //$base ->　go_404();		
        header("site/error_tips.php?state_code=1099"); 
    }    
	
	$userprofile_info['addrinfo'] = $base -> get_address_info($userprofile_info['province_id'], $userprofile_info['city_id'], $userprofile_info['district_id'], $flash); 			
	$userprofile_info['native_addrinfo'] = $base -> get_address_info($userprofile_info['native_province_id'], $userprofile_info['native_city_id'], $userprofile_info['native_district_id'], $flash);
	//获取服务列表
	$service_list = $base -> get_service_list($flash);
	$service_id_list = $service -> get_e_service_by_user($userid,$flash);
	if(empty($service_id_list)){	#拼数组,处理服务名字对应
		$service_arr = '';
	}else{
		foreach($service_id_list as $k => $v){
			$service_id_list[$k]['service_2_id_name'] = $service_list[$v['service_2_id']];
			$service_id_list[$k]['service_3_id_name'] = $service_list[$v['service_3_id']];	
		}
		foreach($service_id_list as $k => $v){
			$service_arr[$v['service_2_id_name']][] = $v;
		}	
	}	
	$smarty -> assign('service_arr',$service_arr);	
	$user_role = $userprofile -> get_role_list_by_user($userid,$flash);	#获取角色
	if(!is_array($user_role)){
		$user_role = array(array('name' => '没选择角色！'));
	}
	//年龄的
	$AGE_RANGE = $COMMON_CONFIG["AGE"]["RANGE"];
	$AGE_OPTION = $COMMON_CONFIG["AGE"]["OPTION"];
	$smarty -> assign('AGE_RANGE',$AGE_RANGE);
	$smarty -> assign('AGE_OPTION',$AGE_OPTION);
	//胸围的
	$BUST_RANGE = $COMMON_CONFIG["BUST"]["RANGE"];
	$BUST_OPTION = $COMMON_CONFIG["BUST"]["OPTION"];
	$smarty -> assign('BUST_RANGE',$BUST_RANGE);
	$smarty -> assign('BUST_OPTION',$BUST_OPTION);
	//腰围
	$WAIST_RANGE = $COMMON_CONFIG["WAIST"]["RANGE"];
	$WAIST_OPTION = $COMMON_CONFIG["WAIST"]["OPTION"];
	$smarty -> assign('WAIST_RANGE',$WAIST_RANGE);
	$smarty -> assign('WAIST_OPTION',$WAIST_OPTION);
	//臀围
	$HIPS_RANGE = $COMMON_CONFIG["HIPS"]["RANGE"];
	$HIPS_OPTION = $COMMON_CONFIG["HIPS"]["OPTION"];
	$smarty -> assign('HIPS_RANGE',$HIPS_RANGE);
	$smarty -> assign('HIPS_OPTION',$HIPS_OPTION);
	//身高
	$HEIGHT_RANGE = $COMMON_CONFIG["HEIGHT"]["RANGE"];
	$HEIGHT_OPTION = $COMMON_CONFIG["HEIGHT"]["OPTION"];
	$smarty -> assign('HEIGHT_RANGE',$HEIGHT_RANGE);
	$smarty -> assign('HEIGHT_OPTION',$HEIGHT_OPTION);
	//毕业年份
	$FINISH_YEAR = $COMMON_CONFIG["FINISH_YEAR"];
	$smarty -> assign('FINISH_YEAR',$FINISH_YEAR);	
	//体重
	$WEIGHT_RANGE = $COMMON_CONFIG["WEIGHT"]["RANGE"];
	$WEIGHT_OPTION = $COMMON_CONFIG["WEIGHT"]["OPTION"];
	$smarty -> assign('WEIGHT_RANGE',$WEIGHT_RANGE);
	$smarty -> assign('WEIGHT_OPTION',$WEIGHT_OPTION);	
	$smarty -> assign('user_role',$user_role);
	$smarty -> assign('userprofile_info',$userprofile_info);
	$userinfo['nickname_r'] = urlencode($userinfo['nickname']);
	$smarty -> assign('userinfo',$userinfo);

	//-------------------他的红形象  start-----------------------
	$album_list = $album -> get_photo_list_by_user($userid,$flash);
	if(!$album_list){
		$album_list	= array();
	}
	$smarty -> assign('album_list',$album_list);
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
	

	$smarty -> display("user/user_info.html");

?>