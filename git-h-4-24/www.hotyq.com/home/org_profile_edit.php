<?php
	$PAGE_TYPE = "org";
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');
	require_once ('../../common/orgprofile.class.php');
	require_once ('../../common/album.class.php');
	$base = new base();
	$orgprofile = new orgprofile();
	$user = new user();
	$album = new album();
	$userprofile = new userprofile();
	//获取cookie中的id 和 type
	$userid = $user_info['id'];
	$usertype = $user_info['user_type'];
	
	$smarty -> assign('info',$user_info);
	//获取用户详情
	$orglist =	$orgprofile -> get_org_profile($userid);
	

	//  分配一个标识变量 	//判断是否存在红档案的值  赋值到隐藏域 js取隐藏显示div
	if(empty($orglist['introduce']) && empty($orglist['production']) && empty($orglist['honor'])){
		$red_file_state = 0;
	}else{
		$red_file_state = 1;
	}
	$smarty -> assign('red_file_state',$red_file_state);	#判断是否存在红档案的值 
	$orglist['introduce'];
	$orglist['production'];
	$orglist['honor'];		
	$smarty -> assign('orglist',$orglist);	#orgprofile列表
	//获取user表详情
	
	$smarty -> assign('user_info',$user_info);		#user用户列表
	//var_dump($user_info);
	//var_dump($userall);
	//获取企业成立时间年份
	for($i = 1900;$i <=date('Y');$i++){
		$create_year[] = $i;
	}
	$smarty -> assign('create_year',$create_year);		
	//取得省市区的值显示
	$province_card = $base -> get_province_info($orglist['province_id'],$flash);	
	$province_c = $province_card['pname'];
	$city_card = $base -> get_city_info($orglist['city_id'],$flash);	
	$city_c = $city_card['cname'];	
	$district_card = $base -> get_district_info($orglist['district_id'],$flash);
	$district_c = $district_card['dname'];		
	$smarty -> assign('province_c',$province_c);
	$smarty -> assign('city_c',$city_c);
	$smarty -> assign('district_c',$district_c);	

	//读取省份
	$plist = $base -> get_province_list($flash);
	//读取机构类型
	$tlist = $base -> get_org_type_list($flash);
	$smarty -> assign('provincelist',$plist);
	$smarty -> assign('tlist',$tlist);
	$type_c = $base -> get_org_type_info($orglist['type']);
	//var_dump($type_c);
	$smarty -> assign('type_c',$type_c);
	//红照片
	$result = $album -> get_photo_list_by_user($userid);
	if($result){
		$album_list = "[";
		$num = count($result);
		for($i = 0; $i < 6;$i++) {
			if($i < $num){
				$album_list .= "{id:\"".$result[$i]['id']."\",thumbnail:\"".$result[$i]['server_url'].$result[$i]['path_url']."!150.100\",photo:\"".$result[$i]['server_url'].$result[$i]['path_url']."!800\"},";
			}elseif($i >= $num){
				$album_list .= "null,";
			}
		}
		$album_list = rtrim($album_list,',');
		$album_list .= "]";
	}else{
		$album_list = "[null,null,null,null,null,null]";
	}
	//echo $album_list;
	$smarty -> assign('album_list',$album_list);
	//机构
	$state_list = $COMMON_CONFIG["STATE"];
	$smarty -> assign('state_list',$state_list);
	//红艺人展示 start
	$result = $orgprofile ->get_artist($userid);
	//var_dump($result);
	if($result){
		// $artist_list = "[";
		// $num = count($result);
		// for($i=0; $i<$num; $i++){
		// 	$artist_list .= '{"name":"'.$result[$i]['name'].'","info":"'.$result[$i]['description'].'"},';
		// }
		// $artist_list = rtrim($artist_list,',');
		// $artist_list .= "]";
		foreach($result as $item){
			unset($json_info);
			$json_info["id"] = $item["id"];
			$json_info["name"] = $item["name"];
			$json_info["info"] = $item["description"];
			$json_arr[] = $json_info;
		}
		$artist_list = json_encode($json_arr);
	}else{
		$artist_list = null;
	}
	$smarty -> assign('artist_list',$artist_list);
	//检测红艺人是否存在数据
		global $db_hyq_read;
		$sql = "SELECT name FROM hyq_artist WHERE uid='{$userid}'";
		$query = $db_hyq_read -> query($sql);
		$result = $db_hyq_read -> fetch_array($query);
		if($result){
			$red_artist_state = 1;
		}else{
			$red_artist_state = 0;
		}
		
	$smarty -> assign('red_artist_state',$red_artist_state);
	
	//机构红标签
	



	//猜你喜欢红人
	$youlike_id_list = $user -> get_rank_user_list(4);
	//var_dump($youlike_id_list);
	foreach($youlike_id_list as $k => $v){
		$youlike_id_list[$k]['info'] = $userprofile -> get_user_profile($v['id']);
	}
	//	print_r($youlike_id_list);
		//exit;
	foreach($youlike_id_list as $k => $v){
		$like_arr[$k]['id'] = $v['id'];
		$like_arr[$k]['nickname'] = $v['nickname'];
		$like_arr[$k]['icon_server_url'] = $v['icon_server_url'];
		$like_arr[$k]['icon_path_url'] = $v['icon_path_url'];
		$role_info = $userprofile -> get_role_list_by_user($v['id']);
		$like_arr[$k]['rolename'] = $role_info;
		$like_arr[$k]['level'] = $v['level'];
		$address_info = $base -> get_address_info($v['info']['province_id'],$v['info']['city_id'],$v['info']['district_id'],$flash);
		$like_arr[$k]['address'] = $address_info['address'];
	}	
	//var_dump($like_arr);
	$smarty -> assign('like_arr',$like_arr);


	$smarty -> display("home/org_profile_edit.html");
?>