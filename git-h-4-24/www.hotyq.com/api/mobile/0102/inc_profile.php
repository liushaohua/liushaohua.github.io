<?php
	//----------------wangyifan   start-----------------------------------
	//查询指定用户的指定页的招募列表(不管是他人自己  都是这些通过的)
	function profile_get_specify_user_recruit(){
		global $flash,$PAGESIZE;
		$recruit = new recruit();
		//suid  page
		if(!isset($_POST['suid']) || empty($_POST['suid'])) return get_state_info(1401);
		$suid = intval($_POST['suid']);
		if(!isset($_POST['page']) || empty($_POST['page'])) return get_state_info(1402);
		$page = intval($_POST['page']);
		if($page < 1) $page=1;
		
		$from_rows = ($page - 1) * $PAGESIZE['RECRUIT_PAGE'];
		$result =  $recruit -> get_checked_recruit_list_by_user($suid,$from_rows,$PAGESIZE['RECRUIT_PAGE'],$flash);
		// 处理招募结果集
		$recruit_list = array();
		if(!empty($result)){
			foreach($result as $k => $v){//icon_img  title   status   deadline
				$recruit_list[$k]['rid'] = $v['id'];
				$recruit_list[$k]['title'] = $v['name'];
				$recruit_list[$k]['icon_img'] = $v['cover_server_url'].$v['cover_path_url'];
				$recruit_list[$k]['status'] = $v['status'];#过期状态
				$recruit_list[$k]['deadline'] = strtotime($v['interview_end_time']);#处理截止时间
			}	
		}/* 
		else{
			//查不到招募
			return get_state_info(1403);
		} */
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $recruit_list;
		return $ret_arr;
	}
	//0 保存选择的二级角色 roles 
	function profile_set_roles(){
		$userprofile = new userprofile();
		// 验证是否登录
		if(empty($_POST['uid'])) return get_state_info(1256);#uid
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1256);	
		if(empty($_POST['app_token'])) return get_state_info(1209);#app_token
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);#验证登录
		
		// 判断是否传入了角色数据
		if(empty($_POST['roles'])) return get_state_info(1408);
		$roles = clear_gpq($_POST['roles']);
		// 删除用户已选
		$state_code = $userprofile -> delete_role_list_by_user($uid); 
		if($state_code != 1000){
			return get_state_info($state_code);
		}
		// 添加
		$role_arr = explode(',',$roles);
		foreach($role_arr as $k => $v){
			$result = $userprofile -> get_role_info(clear_gpq($v));
			if($result['parent_id'] == 0){
				//一级id
				$result['parent_id'] = $result['id'];
				$result['id'] = 0;
			}
			$role_arr[$k] = $result;
		}
		foreach($role_arr as $v){
			$state_code = $userprofile -> add_role_by_user($uid,$v);
			if($state_code != 1000){
				return get_state_info($state_code);
			}
		}
		return get_state_info(1000);
	}
	//1 获取角色字典传过去 
	function profile_get_roles(){
		$userprofile = new userprofile();
		//不需要验证登录
		$result = $userprofile -> get_role_list_mobile();#角色字典
		if(empty($result)){
			return get_state_info(1404);//没查到角色字典
		}
		$sys_role_list = array();
		$role_list = array();
		//每一个改名字 加children
		foreach($result as $k => $v){
			$result[$k]['parentid'] = $v['parent_id'];
			$result[$k]['children'] = array();
			unset($result[$k]['parent_id']);
			if( $result[$k]['parentid'] == 0 ){
				$sys_role_list[$v['id']] = $result[$k];
			}
		}
		foreach($result as $v){
			if( $v['parentid'] > 0 ){
				$sys_role_list[$v['parentid']]['children'][] = $v;
			}
		}
		foreach($sys_role_list as $v){
			$role_list[] = $v;
		}
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $role_list;
		return $ret_arr;
	}
	//2 获取省份字典  // name不同
	function profile_get_provinces(){
		global $flash;
		$base = new base();
		$provincelist = $base -> get_province_list($flash);
		if(empty($provincelist)){
			return get_state_info(1405);//没查到省份字典
		}
		//每一个 改 name 加parentid
		foreach($provincelist as $k => $v){
			$provincelist[$k]['name'] = $v['pname'];
			$provincelist[$k]['parentid'] = '0';
			unset($provincelist[$k]['pname']);
		}
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $provincelist;
		return $ret_arr;
	}
	//3 根据省份获取市
	function profile_get_cities(){
		global $flash;
		$base = new base();
		if(empty($_POST['province'])) return get_state_info(1103);
		$province = intval($_POST['province']);
		$clist = $base -> get_city_list_by_province($province);
		if(empty($clist)){
			return get_state_info(1406);//没查到城市
		}
		$handle_clist = array();
		foreach($clist as $k => $v){
			$handle_clist[$k]['id'] = $v['id'];
			$handle_clist[$k]['name'] = $v['cname'];
			$handle_clist[$k]['parentid'] = $v['pid'];
		}
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $handle_clist;
		return $ret_arr;
	}
	//4 根据市获取区
	function profile_get_districts(){
		global $flash;
		$base = new base();
		if(empty($_POST['city'])) return get_state_info(1104);
		$city = intval($_POST['city']);
		$dlist = $base -> get_district_list_by_city($city);
		if(empty($dlist)){
			return get_state_info(1407);//没查到区
		}
		$handle_dlist = array();
		foreach($dlist as $k => $v){
			$handle_dlist[$k]['id'] = $v['id'];
			$handle_dlist[$k]['name'] = $v['dname'];
			$handle_dlist[$k]['parentid'] = $v['cid'];
		}
		//var_dump($handle_dlist);exit;
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $handle_dlist;
		return $ret_arr;
	}
	//5 裁切好头像  文件流
	function profile_set_icon_img(){
		global $IMG_WWW;
		$photo = new photo();
		$user = new user();
		//验证是否登录
		if(empty($_POST['uid'])) return get_state_info(1256);#uid
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1256);	
		if(empty($_POST['app_token'])) return get_state_info(1209);#app_token
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);#验证登录
		//文件
		if(empty($_FILES['icon_img'])) return get_state_info(1204);#请上传头像
		$file_info = $_FILES['icon_img'];
		$state_code = $photo -> check_upload_photo($file_info);
		if($state_code === 1000){
			//1 判断图片是否符合尺寸要求
			// list($width,$height) = getimagesize($file_info['tmp_name']);
			// if($height >= $width && $width < 600 ){
				// return get_state_info(1201);
			// }
			// if($width > $height && $height < 600 ){
				// return get_state_info(1202);
			// }
			//2 生成上传目标路径+名字
			$hash_dir = $photo -> get_hash_dir($scene = 'user',$uid);
			$file_name = $photo -> create_newname($photo -> get_suffix($file_info["name"]));
			$file_path = $hash_dir."/".$file_name;
			//3 move
			if($photo -> upload_photo($file_info["tmp_name"],$file_path)){
				//4 存库
				$userinfo = $user -> get_userinfo($uid);#先查出 删除放后面
				$result = $user -> update_face($uid,$file_path);#修改头像
				if(!$result) return get_state_info(1207);#图片保存失败
				//5 删除
				if(!empty($userinfo['icon_path_url'])){
					$result = $photo -> delete_photo_file($userinfo['icon_path_url']);
					if(!$result) return get_state_info(1208);#图片删除失败
				}
				$date['url'] = $IMG_WWW.$file_path;
				$ret_arr = get_state_info(1000);
				$ret_arr['data'] = $data;
				return $ret_arr;
				//return get_state_info(1000);#成功
			}else{
				return get_state_info(1203);
			}
		}else{
			return get_state_info($state_code);
		}
	}
	//----------------wangyifan   end-----------------------------------
	//----------------zhaozhenhuan (=^ ^=)--------------------start------------
	//获取三围的范围
	function profile_get_bwh_range(){
		global $COMMON_CONFIG;
		$ret_array['bust_min'] = $COMMON_CONFIG["BUST"]["RANGE"]['begin'];
		$ret_array['bust_min_limit'] = $COMMON_CONFIG["BUST"]["RANGE"]['min'];
		$ret_array['bust_max'] = $COMMON_CONFIG["BUST"]["RANGE"]['end'];
		$ret_array['bust_max_limit'] = $COMMON_CONFIG["BUST"]["RANGE"]['max'];
		
		$ret_array['waist_min'] = $COMMON_CONFIG["WAIST"]["RANGE"]['begin'];
		$ret_array['waist_min_limit'] = $COMMON_CONFIG["WAIST"]["RANGE"]['min'];
		$ret_array['waist_max'] = $COMMON_CONFIG["WAIST"]["RANGE"]['end'];
		$ret_array['waist_max_limit'] = $COMMON_CONFIG["WAIST"]["RANGE"]['max'];
		
		$ret_array['hips_min'] = $COMMON_CONFIG["HIPS"]["RANGE"]['begin'];
		$ret_array['hips_min_limit'] = $COMMON_CONFIG["HIPS"]["RANGE"]['min'];
		$ret_array['hips_max'] = $COMMON_CONFIG["HIPS"]["RANGE"]['end'];
		$ret_array['hips_max_limit'] = $COMMON_CONFIG["HIPS"]["RANGE"]['max'];
		
		$ret_arr = get_state_info(1000);		
		$ret_arr['data'] = $ret_array;	
		return $ret_arr;
	}
	//获取年龄和星座的范围
	function profile_get_age_constellation_range(){
		global $COMMON_CONFIG;
		$ret_array['age_min'] = $COMMON_CONFIG["AGE"]["RANGE"]['begin'];
		$ret_array['age_min_limit'] = $COMMON_CONFIG["AGE"]["RANGE"]['min'];
		$ret_array['age_max'] = $COMMON_CONFIG["AGE"]["RANGE"]['end'];
		$ret_array['age_max_limit'] = $COMMON_CONFIG["AGE"]["RANGE"]['max'];
		foreach($COMMON_CONFIG["STAR"] as $v){
			$ret_array['constellation'][] = $v;
		}
		$ret_arr = get_state_info(1000);		
		$ret_arr['data'] = $ret_array;	
		return $ret_arr;
	}
	
	//获取机构用户编辑页的资料
	function profile_get_org_profile(){
		global $flash;
		$user = new user();
		$base = new base();
		$userprofile = new userprofile();
		$orgprofile = new orgprofile();
		$service = new service();
		$album = new album();
		$recruit = new recruit();
		$uid = intval($_POST['uid']);
		$app_token = clear_gpq($_POST['app_token']);
		if($uid<1) return get_state_info(1099);
	 	$userinfo =	$user -> get_userinfo($uid,$flash);	
		$orgprofile_info =	$orgprofile -> get_org_profile($uid,$flash);
		_check_login($uid,$app_token);	
		//var_dump($userinfo);
		if(!$userinfo || !$orgprofile_info) return get_state_info(1099);			
		if($userinfo['user_type'] == 'user'){ 
			return get_state_info(1099);
		}else{
			$ret_array['user_type'] = '1';
		}
		$ret_array['nickname'] = $userinfo['nickname'];		
		$ret_array['icon_img'] = $userinfo['icon_server_url'].$userinfo['icon_path_url'];					
		
		if($userinfo['mobile_status'] == 'yes'){	//是否验证手机
			$ret_array['has_verify_mobile'] = '1';
			$ret_array['verify_mobile'] = $userinfo['mobile'];
		}else{
			$ret_array['has_verify_mobile'] = '0';	
			$ret_array['verify_mobile'] = '';
		}
		
		if($orgprofile_info['legal_person'] == 'yes'){		//是否是法人
			$ret_array['is_legal'] = '1';
			if($userinfo['business_card_status'] == 'yes'){
				$ret_array['has_verify_business'] = '1';
			}else{
				$ret_array['has_verify_business'] = '0';
			}			
			$ret_array['has_authentication'] = '0';	
		}else{
			$ret_array['is_legal'] = '0';
			if($userinfo['identity_card_status'] == 'yes'){
				$ret_array['has_authentication'] = '1';		//是否验证身份证
			}else{
				$ret_array['has_authentication'] = '0';
			}
			$ret_array['has_verify_business'] = '0';
		}
		
		//$ret_array['data']['has_authentication'] = $userinfo['business_card_status'];		//是否验证工商号
		$org_type_name = $base -> get_org_type_info($orgprofile_info['type'], $flash);	//机构类型
		$ret_array['org_type'] = $org_type_name['name'];
		$ret_array['instituted'] = $orgprofile_info['create_time'];	//创建时间
		
		$ret_array['introduction'] = $orgprofile_info['introduce'];	//简介		
		$ret_array['showreel'] = $orgprofile_info['production']; // 主要作品
		$ret_array['honor'] = $orgprofile_info['honor'];	//主要荣誉
		$ret_array['phone'] = $orgprofile_info['contact_mobile'];
		$ret_array['qq'] = $orgprofile_info['contact_qq'];
		$ret_array['weixin'] = $orgprofile_info['contact_weixin'];
		$ret_array['email'] = $orgprofile_info['contact_email'];
		$province_info = $base -> get_province_info($orgprofile_info['province_id'],$flash);
		$city_info = $base -> get_city_info($orgprofile_info['city_id'],$flash);
		$district_info = $base -> get_district_info($orgprofile_info['district_id'],$flash);
		$ret_array['province'] = $province_info['pname'];
		$ret_array['city'] = $city_info['cname'];
		$ret_array['district'] = $district_info['dname'];
		//$ret_array['data']['photos'] = 
		//$ret_array['data']['service'] = 
		//获取用户相册

		$album_list = $album -> get_photo_list_by_user($uid);
		if(is_array($album_list)){
			foreach ($album_list as $key => $value) {
				$photo_info['photo'][$key]['id'] = $value['id'];
				$photo_info['photo'][$key]['url'] = $value['server_url'].$value['path_url'];
				$photo_info['photo'][$key]['description'] = $value['title'];
			}
		}else{
			$photo_info['photo'] = array();
		}			
		$ret_array['photos'] = $photo_info['photo'];
		//获取所有服务
		 $service_list = $base -> get_service_list($flash);
		 $service_id_list = $service -> get_e_service_by_user($uid,$flash);
		//var_dump($service_list);exit;
		//var_dump($service_id_list);exit;
		if(empty($service_id_list)){
			$arr_new = array();
		}else{
			// foreach($service_id_list as $k => $v){
				// $service_id_list[$k]['name'] = $service_list[$v['service_3_id']];
			// }
			foreach($service_id_list as $k => $v){
				$arr[$v['service_2_id']]['id']  = $v['service_2_id'];
				$arr[$v['service_2_id']]['name']  = $service_list[$v['service_2_id']];
				$arr[$v['service_2_id']]['children'][]  = $v;
				
			 }
			foreach($arr as $k => $v){
				foreach($v['children'] as $k0 => $v0){
					unset($arr[$k]['children'][$k0]['id']);
					unset($arr[$k]['children'][$k0]['service_1_id']);
					unset($arr[$k]['children'][$k0]['service_2_id']);
					unset($arr[$k]['children'][$k0]['uid']);
					$arr[$k]['children'][$k0]['id'] = $v0['service_3_id'];
					$arr[$k]['children'][$k0]['name'] = $service_list[$v0['service_3_id']];
					unset($arr[$k]['children'][$k0]['service_3_id']);
				}
			}
			foreach($arr as $k => $v){
				$arr_new[] = $v;
			}	
		}
		$ret_arr = get_state_info(1000);		
		$ret_array['service'] = $arr_new;
		$ret_arr['data'] = $ret_array;
		return $ret_arr;
	}	
	//获取机构用户展示页的资料
	function profile_get_specify_org_profile(){
		global $flash;
		$user = new user();
		$base = new base();
		$userprofile = new userprofile();
		$orgprofile = new orgprofile();
		$service = new service();
		$album = new album();
		$recruit = new recruit();
		$collect = new collect();
		
		$uid = intval($_POST['uid']);
		$suid = intval($_POST['suid']);
		@$app_token = clear_gpq($_POST['app_token']);
		if($suid<1) return get_state_info(1099);
	 	$userinfo =	$user -> get_userinfo($suid,$flash);	
		$orgprofile_info =	$orgprofile -> get_org_profile($suid,$flash);
		//_check_login($uid,$app_token);	
	
		if(!$userinfo || !$orgprofile_info) return get_state_info(1099);			
		if($userinfo['user_type'] == 'user'){ 
			return get_state_info(1099);
		}else{
			$ret_array['user_type'] = '1';
		}
		$ret_array['nickname'] = $userinfo['nickname'];			
		$ret_array['icon_img'] = $userinfo['icon_server_url'].$userinfo['icon_path_url'];			
		if($userinfo['mobile_status'] == 'yes'){	//是否验证手机
			$ret_array['has_verify_mobile'] = '1';
		}else{
			$ret_array['has_verify_mobile'] = '0';	
		}
		//判断收藏	
		// $collect_list = $collect -> get_collect_list_by_user($uid,$is_show);	#判断是否收藏
		// if($collect_list){	
			// foreach ($collect_list as $v){
				// if($v['type'] == 'org'){
					// $arr_collect[] = $v['dynamic_id'];		//被收藏的id		
				// }
			// }		
			// if (in_array($suid, $arr_collect)) {
				// $ret_array['has_favorite'] = '1'; 
			// }else{
				// $ret_array['has_favorite'] = '0'; 
			// }
		// }else{
			// $ret_array['has_favorite'] = '0'; 
		// }

		//判断用户是否收藏 登陆 和 未登录
		if($user -> is_login($uid,$app_token)){
			$collect = new collect();
			$result = $collect -> get_collect_exists($uid,'org',$suid);
			if($result){
				$ret_array['has_favorite'] = '1';
			}else{
				$ret_array['has_favorite'] = '0';
			}	
		}else{
			$ret_array['has_favorite'] = '0';
		}

		
		if($orgprofile_info['legal_person'] == 'yes'){		//是否是法人
			$ret_array['is_legal'] = '1';
			if($userinfo['business_card_status'] == 'yes'){
				$ret_array['has_verify_business'] = '1';
			}else{
				$ret_array['has_verify_business'] = '0';
			}			
			$ret_array['has_authentication'] = '0';	
		}else{
			$ret_array['is_legal'] = '0';
			if($userinfo['identity_card_status'] == 'yes'){
				$ret_array['has_authentication'] = '1';		//是否验证身份证
			}else{
				$ret_array['has_authentication'] = '0';
			}
			$ret_array['has_verify_business'] = '0';
		}
		
		//$ret_array['data']['has_authentication'] = $userinfo['business_card_status'];		//是否验证工商号
		$org_type_name = $base -> get_org_type_info($orgprofile_info['type'], $flash);	//机构类型
		$ret_array['org_type'] = $org_type_name['name'];
		$ret_array['instituted'] = $orgprofile_info['create_time'];	//创建时间
		
		$ret_array['introduction'] = $orgprofile_info['introduce'];	//简介		
		$ret_array['showreel'] = $orgprofile_info['production']; // 主要作品
		$ret_array['honor'] = $orgprofile_info['honor'];	//主要荣誉
		//$ret_array['phone'] = $orgprofile_info['contact_mobile'];
		//$ret_array['qq'] = $orgprofile_info['contact_qq'];
		//$ret_array['weixin'] = $orgprofile_info['contact_weixin'];
		//$ret_array['email'] = $orgprofile_info['contact_email'];
		$province_info = $base -> get_province_info($orgprofile_info['province_id'],$flash);
		$city_info = $base -> get_city_info($orgprofile_info['city_id'],$flash);
		$district_info = $base -> get_district_info($orgprofile_info['district_id'],$flash);
		$ret_array['province'] = $province_info['pname'];
		$ret_array['city'] = $city_info['cname'];
		$ret_array['district'] = $district_info['dname'];
		//$ret_array['data']['photos'] = 
		//$ret_array['data']['service'] = 
		//获取用户相册

		$album_list = $album -> get_photo_list_by_user($suid);
		if(is_array($album_list)){
			foreach ($album_list as $key => $value) {
				$photo_info['photo'][$key]['id'] = $value['id'];
				$photo_info['photo'][$key]['url'] = $value['server_url'].$value['path_url'];
				$photo_info['photo'][$key]['description'] = $value['title'];
			}
		}else{
			$photo_info['photo'] = array();
		}			
		$ret_array['photos'] = $photo_info['photo'];
		//获取所有服务
		 $service_list = $base -> get_service_list($flash);
		 $service_id_list = $service -> get_e_service_by_user($suid);
		//var_dump($service_list);exit;
		//var_dump($service_id_list);exit;
		if(empty($service_id_list)){
			$arr_new = array();
		}else{
			// foreach($service_id_list as $k => $v){
				// $service_id_list[$k]['name'] = $service_list[$v['service_3_id']];
			// }
			foreach($service_id_list as $k => $v){
				$arr[$v['service_2_id']]['id']  = $v['service_2_id'];
				$arr[$v['service_2_id']]['name']  = $service_list[$v['service_2_id']];
				$arr[$v['service_2_id']]['children'][]  = $v;
				
			 }
			foreach($arr as $k => $v){
				foreach($v['children'] as $k0 => $v0){
					unset($arr[$k]['children'][$k0]['id']);
					unset($arr[$k]['children'][$k0]['service_1_id']);
					unset($arr[$k]['children'][$k0]['service_2_id']);
					unset($arr[$k]['children'][$k0]['uid']);
					$arr[$k]['children'][$k0]['id'] = $v0['service_3_id'];
					$arr[$k]['children'][$k0]['name'] = $service_list[$v0['service_3_id']];
					unset($arr[$k]['children'][$k0]['service_3_id']);
				}
			}
			foreach($arr as $k => $v){
				$arr_new[] = $v;
			}	
		}

		$ret_arr = get_state_info(1000);		
		$ret_array['service'] = $arr_new;
		$ret_arr['data'] = $ret_array;
		return $ret_arr;
	}		
		
	//获取身高和体重的范围
	function get_height_weight_range(){
		global $COMMON_CONFIG;
		$ret_array['height_min'] = $COMMON_CONFIG["HEIGHT"]["RANGE"]['begin'];
		$ret_array['height_min_limit'] = $COMMON_CONFIG["HEIGHT"]["RANGE"]['min'];
		$ret_array['height_max'] = $COMMON_CONFIG["HEIGHT"]["RANGE"]['end'];
		$ret_array['height_max_limit'] = $COMMON_CONFIG["HEIGHT"]["RANGE"]['max'];
		$ret_array['weight_min'] = $COMMON_CONFIG["WEIGHT"]["RANGE"]['begin'];
		$ret_array['weight_min_limit'] = $COMMON_CONFIG["WEIGHT"]["RANGE"]['min'];
		$ret_array['weight_max'] = $COMMON_CONFIG["WEIGHT"]["RANGE"]['end'];
		$ret_array['weight_max_limit'] = $COMMON_CONFIG["WEIGHT"]["RANGE"]['max'];
		$ret_arr = get_state_info(1000);		
		$ret_arr['data'] = $ret_array;	
		return $ret_arr;		
	}
	//获取所有机构类型
	function profile_get_org_type(){
		global $flash;
		$base = new base;
		$ret_array = $base -> get_org_type_list($flash);	
		$ret_arr = get_state_info(1000);		
		$ret_arr['data'] = $ret_array;	
		return $ret_arr;				
	}	
	//----------------zhaozhenhuan----end-----------------------------------------
	//----------------suntianxing   began-----------------------------------------
	//获取个人用户编辑页的资料
	function profile_get_user_profile(){
		global $flash;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);	
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);

		//获取登陆用户的个人信息
		$user = new user();
		$userinfo = $user -> get_userinfo($uid, $flash);
		
		$u_info['icon_img'] = $userinfo['icon_server_url'].$userinfo['icon_path_url'];
		$u_info['nickname'] = $userinfo['nickname'];
		if($userinfo['user_type'] == 'user'){
			$u_info['user_type'] = '0';
		}else{
			$u_info['user_type'] = '1';
		}
		//手机是否验证
		if($userinfo['mobile_status'] == 'yes'){
			$u_info['has_verify_mobile'] = '1';
			$u_info['verify_mobile'] = $userinfo['mobile'];
		}else{
			$u_info['has_verify_mobile'] = '0';
			$u_info['verify_mobile'] = '';
		}
		//身份证是否验证
		if($userinfo['identity_card_status'] == 'yes'){
			$u_info['has_authentication'] = '1';
		}else{
			$u_info['has_authentication'] = '0';
		}
		//获取用户的资料
		$userprofile = new userprofile();
		$uprofile = $userprofile -> get_user_profile($uid, $flash);

		$u_info['age'] = $uprofile['age'];
		if($uprofile['sex'] == 'm'){
			$u_info['sex'] = '男';
		}else{
			$u_info['sex'] = '女';
		}
		$u_info['height'] = $uprofile['height'];
		$u_info['weight'] = $uprofile['weight'];
		$u_info['constellation'] = $uprofile['star'];
		$u_info['breast'] = $uprofile['bust'];
		$u_info['waistline'] = $uprofile['waist'];
		$u_info['hipline'] = $uprofile['hips'];
		$u_info['scroll'] = $uprofile['school'];
		$u_info['major'] = $uprofile['specialty'];
		$u_info['organization'] = $uprofile['in_org'];
		$u_info['phone'] = $uprofile['contact_mobile'];
		$u_info['qq'] = $uprofile['contact_qq'];
		$u_info['weixin'] = $uprofile['contact_weixin'];
		$u_info['email'] = $uprofile['contact_email'];
		//获取用户的所在地和籍贯
		$base = new base();
		if($uprofile['province_id']){
			$province_info = $base -> get_province_info($uprofile['province_id'],$flash);
			$u_info['province'] = $province_info['pname'];
		}else{
			$u_info['province'] = "";
		} 
		
		if($uprofile['city_id']){
			$city_info = $base -> get_city_info($uprofile['city_id'], $flash); 
			$u_info['city'] = $city_info['cname'];
		}else{
			$u_info['city'] = "";
		}
		if($uprofile['district_id']){
			$district_info = $base -> get_district_info($uprofile['district_id'], $flash); 
			$u_info['district'] = $district_info['dname'];	
		}else{
			$u_info['district'] = "";
		}

		if($uprofile['native_province_id']){
			$native_province_info = $base -> get_province_info($uprofile['native_province_id'], $flash); 
			$u_info['birthplace_province'] = $native_province_info['pname'];	
		}else{
			$u_info['birthplace_province'] = "";
		}	
		if($uprofile['native_city_id']){
			$native_city_info = $base -> get_city_info($uprofile['native_city_id'], $flash); 
			$u_info['birthplace_city'] = $native_city_info['cname'];
		}else{
			$u_info['birthplace_city'] = "";
		}
		if($uprofile['native_district_id']){
			$native_district_info = $base -> get_district_info($uprofile['native_district_id'], $flash); 
			$u_info['birthplace_district'] = $native_district_info['dname']; 
		}else{
			$u_info['birthplace_district'] = "";
		}
		//获取用户相册
		$album = new album();
		$album_list = $album -> get_photo_list_by_user($uid);
		if(is_array($album_list)){
			foreach ($album_list as $key => $value) {
				$u_info['photos'][$key]['id'] = $value['id'];
				$u_info['photos'][$key]['url'] = $value['server_url'].$value['path_url'];
				$u_info['photos'][$key]['description'] = $value['title'];
			}
		}else{
			$u_info['photos'] = array();
		}
		
		//获取用户的角色
		$role_list = $userprofile -> get_e_role_list_by_user($uid);
		if(is_array($role_list)){
			foreach ($role_list as $k => $v) {
				if($v['role_id'] == '0'){
					$u_info['role'][$k]['id'] = $v['role_1_id'];
					$role_info = $userprofile -> get_role_info($v['role_1_id']);
					$u_info['role'][$k]['name'] = $role_info['name'];
				}else{
					$u_info['role'][$k]['id'] = $v['role_id'];
					$role_info = $userprofile -> get_role_info($v['role_id']);
					$u_info['role'][$k]['name'] = $role_info['name'];
				}
			}
		}else{
			$u_info['role'] = array();
		}

		//获取用户的服务
		$service = new service();
		$service_arr = $service -> get_service($flash);  //取所有缓存服务
		foreach ($service_arr as $kkk => $vvv) {
			$service_array[$vvv['id']] = $vvv['name'];
		}
		$service_list = $service -> get_e_service_by_user($uid);
		if(is_array($service_list)){
			foreach ($service_list as $kk => $vv) {
				$service_2_id = $vv['service_2_id'];
				$service_3_id = $vv['service_3_id'];

				$u_info['service'][$service_2_id]['id'] = $service_2_id;
				$u_info['service'][$service_2_id]['name'] = $service_array[$service_2_id];

				$u_info['service'][$service_2_id]['children'][$service_3_id]['id'] = $service_3_id;
				$u_info['service'][$service_2_id]['children'][$service_3_id]['name'] = $service_array[$service_3_id];
			}
			
			//尼玛 排序蛋疼啊
			foreach ($u_info['service'] as $k => $v) {
				$third_service[$k]['id'] = $v['id'];
				$third_service[$k]['name'] = $v['name'];
				$i = 0; 
				foreach ($v['children'] as $kk => $vv) {
					$third_service[$k]['children'][$i]['id'] = $vv['id'];
					$third_service[$k]['children'][$i]['name'] = $vv['name'];
					$i++;
				}
				$u_info['service'] = $third_service;
			}
			$u_info['service'] = array_values($u_info['service']);
		}else{
			$u_info['service'] = array();
		}

		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $u_info;
		return $ret_arr;
	}

	//获取个人用户展示页的资料
	function profile_get_specify_user_profile(){
		global $flash;
		$uid = intval($_POST['uid']);
		$suid = intval($_POST['suid']);
		//if($uid<1) return get_state_info(1099);
		if($suid<1) return get_state_info(1099);
		@$app_token = clear_gpq($_POST['app_token']);
		//_check_login($uid,$app_token);

		//获取指定用户的个人信息
		$user = new user();
		$userinfo = $user -> get_userinfo($suid, $flash);
		//用户类型不匹配
		if($userinfo['user_type'] != 'user'){
			return get_state_info(1099);	
		}

		$u_info['icon_img'] = $userinfo['icon_server_url'].$userinfo['icon_path_url'];
		$u_info['nickname'] = $userinfo['nickname'];
		$u_info['user_type'] = '0';
		
		//手机是否验证
		if($userinfo['mobile_status'] == 'yes'){
			$u_info['has_verify_mobile'] = '1';
		}else{
			$u_info['has_verify_mobile'] = '0';
		}
		//身份证是否验证
		if($userinfo['identity_card_status'] == 'yes'){
			$u_info['has_authentication'] = '1';
		}else{
			$u_info['has_authentication'] = '0';
		}
		//判断用户是否收藏 登陆 和 未登录
		if($user -> is_login($uid,$app_token)){
			$collect = new collect();
			$result = $collect -> get_collect_exists($uid,'user',$suid);
			if($result){
				$u_info['has_favorite'] = '1';
			}else{
				$u_info['has_favorite'] = '0';
			}	
		}else{
			$u_info['has_favorite'] = '0';
		}
		
		//获取用户的资料
		$userprofile = new userprofile();
		$uprofile = $userprofile -> get_user_profile($suid, $flash);

		$u_info['age'] = $uprofile['age'];
		if($uprofile['sex'] == 'm'){
			$u_info['sex'] = '男';
		}else{
			$u_info['sex'] = '女';
		}
		$u_info['height'] = $uprofile['height'];
		$u_info['weight'] = $uprofile['weight'];
		$u_info['constellation'] = $uprofile['star'];
		$u_info['breast'] = $uprofile['bust'];
		$u_info['waistline'] = $uprofile['waist'];
		$u_info['hipline'] = $uprofile['hips'];
		$u_info['scroll'] = $uprofile['school'];
		$u_info['major'] = $uprofile['specialty'];
		$u_info['organization'] = $uprofile['in_org'];

		//获取用户的所在地和籍贯
		$base = new base();
		if($uprofile['province_id']){
			$province_info = $base -> get_province_info($uprofile['province_id'], $flash);
			$u_info['province'] = $province_info['pname'];
		}else{
			$u_info['province'] = "";
		} 
		
		if($uprofile['city_id']){
			$city_info = $base -> get_city_info($uprofile['city_id'], $flash); 
			$u_info['city'] = $city_info['cname'];
		}else{
			$u_info['city'] = "";
		}
		if($uprofile['district_id']){
			$district_info = $base -> get_district_info($uprofile['district_id'], $flash); 
			$u_info['district'] = $district_info['dname'];	
		}else{
			$u_info['district'] = "";
		}

		if($uprofile['native_province_id']){
			$native_province_info = $base -> get_province_info($uprofile['native_province_id'], $flash); 
			$u_info['birthplace_province'] = $native_province_info['pname'];	
		}else{
			$u_info['birthplace_province'] = "";
		}	
		if($uprofile['native_city_id']){
			$native_city_info = $base -> get_city_info($uprofile['native_city_id'], $flash); 
			$u_info['birthplace_city'] = $native_city_info['cname'];
		}else{
			$u_info['birthplace_city'] = "";
		}
		if($uprofile['native_district_id']){
			$native_district_info = $base -> get_district_info($uprofile['native_district_id'], $flash); 
			$u_info['birthplace_district'] = $native_district_info['dname']; 
		}else{
			$u_info['birthplace_district'] = "";
		}
		//获取用户相册
		$album = new album();
		$album_list = $album -> get_photo_list_by_user($suid);
		if(is_array($album_list)){
			foreach ($album_list as $key => $value) {
				$u_info['photos'][$key]['id'] = $value['id'];
				$u_info['photos'][$key]['url'] = $value['server_url'].$value['path_url'];
				$u_info['photos'][$key]['description'] = $value['title'];
			}
		}else{
			$u_info['photos'] = array();
		}
		
		//获取用户的角色
		$role_list = $userprofile -> get_e_role_list_by_user($suid);
		if(is_array($role_list)){
			foreach ($role_list as $k => $v) {
				if($v['role_id'] == '0'){
					$u_info['role'][$k]['id'] = $v['role_1_id'];
					$role_info = $userprofile -> get_role_info($v['role_1_id']);
					$u_info['role'][$k]['name'] = $role_info['name'];
				}else{
					$u_info['role'][$k]['id'] = $v['role_id'];
					$role_info = $userprofile -> get_role_info($v['role_id']);
					$u_info['role'][$k]['name'] = $role_info['name'];
				}
			}
		}else{
			$u_info['role'] = array();
		}

		//获取用户的服务
		$service = new service();
		$service_arr = $service -> get_service($flash);  //取所有缓存服务
		foreach ($service_arr as $kkk => $vvv) {
			$service_array[$vvv['id']] = $vvv['name'];
		}
		$service_list = $service -> get_e_service_by_user($suid);
		if(is_array($service_list)){
			foreach ($service_list as $kk => $vv) {
				$service_2_id = $vv['service_2_id'];
				$service_3_id = $vv['service_3_id'];

				$u_info['service'][$service_2_id]['id'] = $service_2_id;
				$u_info['service'][$service_2_id]['name'] = $service_array[$service_2_id];

				$u_info['service'][$service_2_id]['children'][$service_3_id]['id'] = $service_3_id;
				$u_info['service'][$service_2_id]['children'][$service_3_id]['name'] = $service_array[$service_3_id];
			}
			
			//尼玛 排序蛋疼啊
			foreach ($u_info['service'] as $k => $v) {
				$third_service[$k]['id'] = $v['id'];
				$third_service[$k]['name'] = $v['name'];
				$i = 0; 
				foreach ($v['children'] as $kk => $vv) {
					$third_service[$k]['children'][$i]['id'] = $vv['id'];
					$third_service[$k]['children'][$i]['name'] = $vv['name'];
					$i++;
				}
				$u_info['service'] = $third_service;
			}
			$u_info['service'] = array_values($u_info['service']);
		}else{
			$u_info['service'] = array();
		}

		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $u_info;
		return $ret_arr;
	}

	//删除指定的二级服务
	function profile_delete_two_service(){
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$service_2_id = intval($_POST['service_id']);
		if($service_2_id<1) return get_state_info(1305);
		//验证是不是 二级服务的id
		$service = new service();
		$r = $service -> check_second_service_id($service_2_id);
		if(!$r){
			return get_state_info(1308);
		}

		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);

		$result = $service -> delete_second_service($uid, $service_2_id);
		if($result){
			//刷新缓存
			$service -> get_e_service_by_user($uid,$flash = 1);
			return get_state_info(1000);
		}else{
			//delete success
			return get_state_info(1304);
		}
	}

	//设置指定二级服务的三级服务
	function profile_set_three_service(){
		global $flash;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$service_2_id = intval($_POST['service_id']);
		if($service_2_id<1) return get_state_info(1305);
		//验证是不是 二级服务的id
		$service = new service();
		$r = $service -> check_second_service_id($service_2_id);
		if(!$r){
			return get_state_info(1308);
		}

		$service_3_list = clear_gpq($_POST['service_list']);
		if(empty($service_3_list) || !isset($service_3_list)) return get_state_info(1306);
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);

		//检测二级服务是否存在
		$result = $service -> check_second_service_exits($uid,$service_2_id);
		$service_3_arr = explode(',', $service_3_list);
		if($result){
			//二级服务已存在
			$service_1_id = $result['service_1_id'];
			$r = $service -> delete_second_service($uid,$service_2_id);
			if($r){
				foreach ($service_3_arr as $key => $value) {
					$service_array['service_1_id'] = $service_1_id;	
					$service_array['service_2_id'] = $service_2_id;	
					$service_array['service_3_id'] = $value;
					$rr = $service -> add_user_service($uid, $service_array);
				}
				$service -> get_e_service_by_user($uid, $flash = 1);
				return get_state_info(1000);
			}else{
				return get_state_info(1304);
			}
		}else{
			$service_info = $service -> get_service_info($service_2_id, $flash);
			$service_1_id = $service_info['parent_id'];
			foreach ($service_3_arr as $key => $value) {
				$service_array['service_1_id'] = $service_1_id;	
				$service_array['service_2_id'] = $service_2_id;	
				$service_array['service_3_id'] = $value;
				$rr = $service -> add_user_service($uid, $service_array);
			}
			$service -> get_e_service_by_user($uid, $flash = 1);
			return get_state_info(1000);
		}
	}

	//获取所有的二级服务
	function profile_get_two_services(){
		global $flash;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);	
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);

		//获取所有二级服务
		$service = new service();
		$service_list = $service -> get_service($flash);
		
		if(is_array($service_list) && $service_list){
			//转换服务id 与 name的对应关系
			foreach ($service_list as $k => $v) {
				$service_array[$v['id']] = $v['name'];
			}
			//生成所有的二级服务
			$i = 0;
			foreach ($service_list as $key => $value) {
				$parent_id = $value['parent_id'];
				if($parent_id == 0){
					$service_1_id = $value['id'];
					$result = $service -> get_children_service($service_1_id,$flash);
					$service_arr[$i]['id'] = $service_1_id;
					$service_arr[$i]['name'] = $service_array[$service_1_id];
					$service_arr[$i]['parentid'] = '0';
					foreach ($result as $kk => $vv) {
						$service_arr[$i]['children'][$kk]['id'] = $vv['id'];
						$service_arr[$i]['children'][$kk]['name'] = $service_array[$vv['id']];
						$service_arr[$i]['children'][$kk]['parentid'] = $service_1_id;
						$service_arr[$i]['children'][$kk]['children'] = array();
					}
					$i++;
				}
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data'] = $service_arr;
			return $ret_arr;
		}else{
			//获取服务失败
			return get_state_info(1307);
		}	
	}

	//获取指定二级服务下的三级服务
	function profile_get_three_services(){
		global $flash;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$service_2_id = intval($_POST['service_id']);
		if($service_2_id < 1) return get_state_info(1305);
		//验证是不是 二级服务的id
		$service = new service();
		$r = $service -> check_second_service_id($service_2_id);
		if(!$r){
			return get_state_info(1308);
		}
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);

		//获取登陆用户的个人信息
		$service_list = $service -> get_service($flash);
		if(is_array($service_list) && $service_list){
			//转换服务id 与 name的对应关系
			foreach ($service_list as $k => $v) {
				$service_array[$v['id']] = $v['name'];
			}
			//获取二级服务下的三级服务
			$result = $service -> get_children_service($service_2_id,$flash);
			if(is_array($result) && $result){
				foreach ($result as $key => $value) {
					$third_service[$key]['id'] = $value['id'];
					$third_service[$key]['name'] = $service_array[$value['id']];
					$third_service[$key]['parentid'] = $service_2_id;
				}
				$ret_arr = get_state_info(1000);
				$ret_arr['data'] = $third_service;
				return $ret_arr;
			}else{
				return get_state_info(1308);	
			}
		}else{
			return get_state_info(1307);
		}
	}
	//----------------suntianxing   end-----------------------------------

	//----------------ZQF-----START---------------------------------------
	//修改QQ
	function profile_set_contact_qq(){
		$user = new user();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		isset($_POST['qq']) ? $qq = clear_gpq($_POST['qq']) : $qq = '';
		_check_login($uid,$token);
		$user_info = $user -> get_userinfo($uid,$flash);
		if($user_info['user_type'] == 'user'){
			$userprofile = new userprofile();
			$contact_info['contact_qq'] = $qq;
			$result = $userprofile -> update_user_profile($uid,$contact_info);
		}elseif($user_info['user_type'] == 'org'){
			$orgprofile = new orgprofile();
			$contact_info['contact_qq'] = $qq;
			$result = $orgprofile -> update_org_profile($uid,$contact_info);
		}else{
			return get_state_info(1099);
		}
		if($result){
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}
	}
	//修改手机
	function profile_set_contact_mobile(){
		$user = new user();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		isset($_POST['phone']) ? $phone = clear_gpq($_POST['phone']) : $phone = '';
		_check_login($uid,$token);
		$user_info = $user -> get_userinfo($uid,$flash);
		if($user_info['user_type'] == 'user'){
			$userprofile = new userprofile();
			$contact_info['contact_mobile'] = $phone;
			$result = $userprofile -> update_user_profile($uid,$contact_info);
		}elseif($user_info['user_type'] == 'org'){
			$orgprofile = new orgprofile();
			$contact_info['contact_mobile'] = $phone;
			$result = $orgprofile -> update_org_profile($uid,$contact_info);
		}else{
			return get_state_info(1099);
		}
		if($result){
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}
	}
	
	//修改微信
	function profile_set_contact_weixin(){
		$user = new user();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		isset($_POST['weixin']) ? $weixin = clear_gpq($_POST['weixin']) : $weixin = '';
		_check_login($uid,$token);
		$user_info = $user -> get_userinfo($uid,$flash);
		if($user_info['user_type'] == 'user'){
			$userprofile = new userprofile();
			$contact_info['contact_weixin'] = $weixin;
			$result = $userprofile -> update_user_profile($uid,$contact_info);
		}elseif($user_info['user_type'] == 'org'){
			$orgprofile = new orgprofile();
			$contact_info['contact_weixin'] = $weixin;
			$result = $orgprofile -> update_org_profile($uid,$contact_info);
		}else{
			return get_state_info(1099);
		}
		if($result){
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}
	}
	
	//修改邮箱
	function profile_set_contact_email(){
		$user = new user();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		isset($_POST['email']) ? $email = clear_gpq($_POST['email']) : $email = '';
		_check_login($uid,$token);
		$user_info = $user -> get_userinfo($uid,$flash);
		if($user_info['user_type'] == 'user'){
			$userprofile = new userprofile();
			$contact_info['contact_email'] = $email;
			$result = $userprofile -> update_user_profile($uid,$contact_info);
		}elseif($user_info['user_type'] == 'org'){
			$orgprofile = new orgprofile();
			$contact_info['contact_email'] = $email;
			$result = $orgprofile -> update_org_profile($uid,$contact_info);
		}else{
			return get_state_info(1099);
		}
		if($result){
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}
	}

	//修改用户联系方式
	function profile_set_user_contact(){
		$user = new user();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		isset($_POST['mobile']) ? $mobile = clear_gpq($_POST['mobile']) : $mobile = '';
		isset($_POST['qq']) ? $qq = clear_gpq($_POST['qq']) : $qq = '';
		isset($_POST['weixin']) ? $weixin = clear_gpq($_POST['weixin']) : $weixin = '';
		isset($_POST['email']) ? $email = clear_gpq($_POST['email']) : $email = '';
		$contact_info['contact_mobile'] = $mobile;
		$contact_info['contact_qq'] = $qq;
		$contact_info['contact_weixin'] = $weixin;
		$contact_info['contact_email'] = $email;
		_check_login($uid,$token);
		$user_info = $user -> get_userinfo($uid,$flash);
		if($user_info['user_type'] == 'user'){
			$userprofile = new userprofile();
			$result = $userprofile -> update_user_profile($uid,$contact_info);
		}elseif($user_info['user_type'] == 'org'){
			$orgprofile = new orgprofile();
			$result = $orgprofile -> update_org_profile($uid,$contact_info);
		}else{
			return get_state_info(1099);
		}
		if($result){
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}
	}
	
	//收藏红人
	function profile_set_favorite_reds(){
		$user = new user();
		$collect = new collect();
		$msg_total = new user_msg_total();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		if(empty($_POST['suid'])) return get_state_info(1099);
		$suid = intval($_POST['suid']);
		_check_login($uid,$token);
		if($collect -> get_collect_exists($uid,'user',$suid)) return get_state_info(1062);
		if($collect -> add_collect($uid,$suid,'user')){
			if($result = $collect -> get_collect_list_by_user($uid)){
				$user_msg_total['collect'] = count($result);
				$msg_total -> update_user_msg_total($uid,$user_msg_total);
				return get_state_info(1000);
			}else{
				return get_state_info(1014);
			}
		}else{
			return get_state_info(1059);//收藏失败
		}

	}
	
	//收藏机构
	function profile_set_favorite_org(){
		$user = new user();
		$collect = new collect();
		$msg_total = new user_msg_total();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		if(empty($_POST['suid'])) return get_state_info(1099);
		$suid = intval($_POST['suid']);
		_check_login($uid,$token);
		if($collect -> get_collect_exists($uid,'org',$suid)) return get_state_info(1062);
		if($collect -> add_collect($uid,$suid,'org')){
			if($result = $collect -> get_collect_list_by_user($uid)){
				$user_msg_total['collect'] = count($result);
				$msg_total -> update_user_msg_total($uid,$user_msg_total);
				return get_state_info(1000);
			}else{
				return get_state_info(1014);
			}
		}else{
			return get_state_info(1059);//收藏失败
		}

	}
	
	//收藏招募
	function profile_set_favorite_recruit(){
		$user = new user();
		$collect = new collect();
		$msg_total = new user_msg_total();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		if(empty($_POST['srid'])) return get_state_info(1099);
		$srid = intval($_POST['srid']);
		_check_login($uid,$token);
		if($collect -> get_collect_exists($uid,'recruit',$srid)) return get_state_info(1062);
		if($collect -> add_collect($uid,$srid,'recruit')){
			if($result = $collect -> get_collect_list_by_user($uid)){
				$user_msg_total['collect'] = count($result);
				$msg_total -> update_user_msg_total($uid,$user_msg_total);
				return get_state_info(1000);
			}else{
				return get_state_info(1014);
			}
		}else{
			return get_state_info(1059);//收藏失败
		}

	}
	
	//删除收藏的红人
	function profile_delete_favorite_reds(){
		$user = new user();
		$collect = new collect();
		$msg_total = new user_msg_total();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : '';
		if(empty($_POST['suid'])) return get_state_info(1099);
		$suid = intval($_POST['suid']);
		_check_login($uid,$token);
		$collect_info = $collect -> get_collect_exists($uid,'user',$suid);
		if(!$collect_info) return get_state_info(1099);
		if($collect -> delete_collect($uid,$collect_info['id'])){
			if($result = $collect -> get_collect_list_by_user($uid)){
				$user_msg_total['collect'] = count($result);
				$msg_total -> update_user_msg_total($uid,$user_msg_total);
				return get_state_info(1000);
			}else{
				$user_msg_total['collect'] = 0;
				$msg_total -> update_user_msg_total($uid,$user_msg_total);
				return get_state_info(1000);
			}
		}else{
			return get_state_info(1060);//取消收藏失败
		}
	}
	
	//删除收藏的机构
	function profile_delete_favorite_org(){
		$user = new user();
		$collect = new collect();
		$msg_total = new user_msg_total();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : '';
		if(empty($_POST['suid'])) return get_state_info(1099);
		$suid = intval($_POST['suid']);
		_check_login($uid,$token);
		$collect_info = $collect -> get_collect_exists($uid,'org',$suid);
		if(!$collect_info) return get_state_info(1099);
		if($collect -> delete_collect($uid,$collect_info['id'])){
			if($result = $collect -> get_collect_list_by_user($uid)){
				$user_msg_total['collect'] = count($result);
				$msg_total -> update_user_msg_total($uid,$user_msg_total);
				return get_state_info(1000);
			}else{
				$user_msg_total['collect'] = 0;
				$msg_total -> update_user_msg_total($uid,$user_msg_total);
				return get_state_info(1000);
			}
		}else{
			return get_state_info(1060);//取消收藏失败
		}
	}
	
	
	//删除收藏的招募
	function profile_delete_favorite_recruit(){
		$user = new user();
		$collect = new collect();
		$msg_total = new user_msg_total();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : '';
		if(empty($_POST['srid'])) return get_state_info(1099);
		$srid = intval($_POST['srid']);
		_check_login($uid,$token);
		$collect_info = $collect -> get_collect_exists($uid,'recruit',$srid);
		if(!$collect_info) return get_state_info(1099);
		if($collect -> delete_collect($uid,$collect_info['id'])){
			if($result = $collect -> get_collect_list_by_user($uid)){
				$user_msg_total['collect'] = count($result);
				$msg_total -> update_user_msg_total($uid,$user_msg_total);
				return get_state_info(1000);
			}else{
				$user_msg_total['collect'] = 0;
				$msg_total -> update_user_msg_total($uid,$user_msg_total);
				return get_state_info(1000);
			}
		}else{
			return get_state_info(1060);//取消收藏失败
		}
	}
	
	//验证身份证
	function profile_verify_identity_card(){
		$user = new user();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		_check_login($uid,$token);
		if(empty($_POST['name'])) return get_state_info(1115);
		$identity_name = clear_gpq($_POST['name']);
		if(empty($_POST['identity_num'])) return get_state_info(1115);
		$identity_num = clear_gpq($_POST['identity_num']);
		$identity = new identity($identity_num,$identity_name);
		if($identity -> get_identity_info()){
			return get_state_info(1117);
		}else{
			$result = $identity -> verify_identity();
			$res = array();
			function searchKey($array){
				global $res;
			    foreach($array as $key=>$row){
			        if(!is_array($row)){
			        	if($row == '一致'){
			        		$res[] = $row;
			        	}
			        }else{
			           searchKey($row);
			        }
			    }
			    return $res;
			}
			if(is_array($result)){
				$res = searchKey($result);
				$count = count($res);
				if($count == 2 && $res[0] == "一致" && $res[1] == '一致'){
					$identity_num = $result['ROW']['INPUT'][0]['gmsfhm'][0]['#text'];
					$identity_name = $result['ROW']['INPUT'][0]['xm'][0]['#text'];
					if($identity -> add_verified_identity($uid,$identity_num,$identity_name)){
						if($user -> update_user_info($uid,array("identity_card_status" => "yes"))){
							$user -> update_data_percent($uid);
							$user -> get_userinfo($uid,$flash = 1);
							return get_state_info(1000);	
						}else{
							return get_state_info(1122);
						}
					}else{
						return get_state_info(1122);	#身份证绑定失败	
					}	
				}else{
					return get_state_info(1122);
				}

			}else{
				return get_state_info(1122);
			}	
		}
	}
	
	//上传照片
	function profile_add_photo(){
		global $IMG_WWW;
		$user = new user();
		$album = new album();
		$photo = new photo();
		$file_info = $_FILES['icon_img'];
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		_check_login($uid,$token);
		isset($_POST['$description'])? $description = $_POST['description'] : $description = '';
		$result = $album -> get_photo_list_by_user($uid);
		if(is_array($result) && count($result) >= 12){
			return get_state_info(1058);
		}
		$state_code = $photo -> check_upload_photo($file_info);
		if($state_code === 1000){
			$hash_dir = $photo -> get_hash_dir('user',$uid);
			$file_name = $photo -> create_newname($photo -> get_suffix($file_info["name"]));
			$file_path = $hash_dir."/".$file_name;
			if($photo -> upload_photo($file_info["tmp_name"],$file_path)){
				if($result = $album -> upload_user_photo($uid,$file_path,$description)){
					$user -> update_data_percent($uid);
					$ret_arr = get_state_info(1000);
					$ret_arr['data'] = array('id' => $result,'url' => $IMG_WWW.$file_path,'description' => $description);
					return $ret_arr;
				}else{
					return get_state_info(1203);
				}
			}else{
				return get_state_info(1203);
			}
		}else{
			return get_state_info($state_code);
		}
	}
	
	//删除照片
	function profile_delete_photo(){
		$user = new user();
		$album = new album();
		$photo = new photo();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		_check_login($uid,$token);
		if(empty($_POST['id'])) return get_state_info(1054);
		$photo_id = intval($_POST['id']);
		$photo_info = $album -> get_photo_info($photo_id);
		$album -> delete_user_photo($uid,$photo_id);
		$photo -> delete_photo_file($photo_info['path_url']);
		$user -> update_data_percent($uid);
		return get_state_info(1000);
	}



	//----------------ZQF-----END-----------------------------------------