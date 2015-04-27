<?php
	//获取个人用户编辑页的资料
	function profile_get_user_profile(){
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);	
		$app_token = clear_gpq($_POST['app_token']);
		//_check_login($uid,$app_token);

		//获取登陆用户的个人信息
		$user = new user();
		$userinfo = $user -> get_userinfo($uid);
		
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
		}else{
			$u_info['has_verify_mobile'] = '0';
		}
		//身份证是否验证
		if($userinfo['identity_card_status'] == 'yes'){
			$u_info['has_authentication'] = '1';
		}else{
			$u_info['has_authentication'] = '0';
		}
		//获取用户的资料
		$userprofile = new userprofile();
		$uprofile = $userprofile -> get_user_profile($uid);

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
			$province_info = $base -> get_province_info($uprofile['province_id']);
			$u_info['province'] = $province_info['pname'];
		}else{
			$u_info['province'] = "";
		} 
		
		if($uprofile['city_id']){
			$city_info = $base -> get_city_info($uprofile['city_id']); 
			$u_info['city'] = $city_info['cname'];
		}else{
			$u_info['city'] = "";
		}
		if($uprofile['district_id']){
			$district_info = $base -> get_district_info($uprofile['district_id']); 
			$u_info['district'] = $district_info['dname'];	
		}else{
			$u_info['district'] = "";
		}

		if($uprofile['native_province_id']){
			$native_province_info = $base -> get_province_info($uprofile['native_province_id']); 
			$u_info['birthplace_province'] = $native_province_info['pname'];	
		}else{
			$u_info['birthplace_province'] = "";
		}	
		if($uprofile['native_city_id']){
			$native_city_info = $base -> get_city_info($uprofile['native_city_id']); 
			$u_info['birthplace_city'] = $native_city_info['cname'];
		}else{
			$u_info['birthplace_city'] = "";
		}
		if($uprofile['native_district_id']){
			$native_district_info = $base -> get_district_info($uprofile['native_district_id']); 
			$u_info['birthplace_district'] = $native_district_info['dname']; 
		}else{
			$u_info['birthplace_district'] = "";
		}
		//获取用户相册
		$album = new album();
		$album_list = $album -> get_photo_list_by_user($uid);
		if(is_array($album_list)){
			foreach ($album_list as $key => $value) {
				$u_info['photo'][$key]['id'] = $value['id'];
				$u_info['photo'][$key]['url'] = $value['server_url'].$value['path_url'];
				$u_info['photo'][$key]['description'] = $value['title'];
			}
		}else{
			$u_info['photo'] = array();
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
		$service_arr = $service -> get_service();  //取所有缓存服务
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
		$uid = intval($_POST['uid']);
		$suid = intval($_POST['suid']);
		if($uid<1) return get_state_info(1099);
		if($suid<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		//_check_login($uid,$app_token);

		//获取指定用户的个人信息
		$user = new user();
		$userinfo = $user -> get_userinfo($suid);
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
		//判断用户是否收藏
		$collect = new collect();
		$result = $collect -> get_collect_exists($uid,'user',$suid);
		if($result){
			$u_info['has_favorite'] = '1';
		}else{
			$u_info['has_favorite'] = '0';
		}
		//获取用户的资料
		$userprofile = new userprofile();
		$uprofile = $userprofile -> get_user_profile($suid);

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
			$province_info = $base -> get_province_info($uprofile['province_id']);
			$u_info['province'] = $province_info['pname'];
		}else{
			$u_info['province'] = "";
		} 
		
		if($uprofile['city_id']){
			$city_info = $base -> get_city_info($uprofile['city_id']); 
			$u_info['city'] = $city_info['cname'];
		}else{
			$u_info['city'] = "";
		}
		if($uprofile['district_id']){
			$district_info = $base -> get_district_info($uprofile['district_id']); 
			$u_info['district'] = $district_info['dname'];	
		}else{
			$u_info['district'] = "";
		}

		if($uprofile['native_province_id']){
			$native_province_info = $base -> get_province_info($uprofile['native_province_id']); 
			$u_info['birthplace_province'] = $native_province_info['pname'];	
		}else{
			$u_info['birthplace_province'] = "";
		}	
		if($uprofile['native_city_id']){
			$native_city_info = $base -> get_city_info($uprofile['native_city_id']); 
			$u_info['birthplace_city'] = $native_city_info['cname'];
		}else{
			$u_info['birthplace_city'] = "";
		}
		if($uprofile['native_district_id']){
			$native_district_info = $base -> get_district_info($uprofile['native_district_id']); 
			$u_info['birthplace_district'] = $native_district_info['dname']; 
		}else{
			$u_info['birthplace_district'] = "";
		}
		//获取用户相册
		$album = new album();
		$album_list = $album -> get_photo_list_by_user($suid);
		if(is_array($album_list)){
			foreach ($album_list as $key => $value) {
				$u_info['photo'][$key]['id'] = $value['id'];
				$u_info['photo'][$key]['url'] = $value['server_url'].$value['path_url'];
				$u_info['photo'][$key]['description'] = $value['title'];
			}
		}else{
			$u_info['photo'] = array();
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
		$service_arr = $service -> get_service();  //取所有缓存服务
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
		//_check_login($uid,$app_token);

		$result = $service -> delete_second_service($uid, $service_2_id);
		if($result){
			return get_state_info(1000);
		}else{
			//delete success
			return get_state_info(1304);
		}
	}

	//设置指定二级服务的三级服务
	function profile_set_three_service(){
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
		//_check_login($uid,$app_token);

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
				return get_state_info(1000);
			}else{
				return get_state_info(1304);
			}
		}else{
			$service_info = $service -> get_service_info($service_2_id);
			$service_1_id = $service_info['parent_id'];
			foreach ($service_3_arr as $key => $value) {
				$service_array['service_1_id'] = $service_1_id;	
				$service_array['service_2_id'] = $service_2_id;	
				$service_array['service_3_id'] = $value;
				$rr = $service -> add_user_service($uid, $service_array);
			}
			return get_state_info(1000);
		}
	}

	//获取所有的二级服务
	function profile_get_two_services(){
		global $flash;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);	
		$app_token = clear_gpq($_POST['app_token']);
		//_check_login($uid,$app_token);

		//获取所有二级服务
		$service = new service();
		$service_list = $service -> get_service();
		
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
		//_check_login($uid,$app_token);

		//获取登陆用户的个人信息
		$service_list = $service -> get_service();
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