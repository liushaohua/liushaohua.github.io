<?php
	//获取用户报名的二级服务的信息
	function mywork_get_recruit_service_info(){
		global $flash;
		$uid = intval($_POST['uid']);
		$rid = intval($_POST['rid']);
		$e_service_id = intval($_POST['e_service_id']);
		if($uid<1 || $rid<1 || $e_service_id<1) return get_state_info(1099);	
		$app_token = clear_gpq($_POST['app_token']);
		//_check_login($uid,$app_token);
		//获取用户的信息
		$user = new user();
		$userinfo = $user -> get_userinfo($uid, $flash);
		$usertype = $userinfo['user_type'];
		//获取服务缓存数据
		$service = new service();
		$service_list = $service -> get_service($flash);
		if(is_array($service_list) && $service_list){
			//转换服务id 与 name的对应关系
			foreach ($service_list as $k => $v) {
				$service_array[$v['id']] = $v['name'];
			}
		}
		
		$recruit = new recruit();
		$recruit_service_info = $recruit -> get_recruit_service($e_service_id);
		//招募服务详情
		$recruit_service_arr['id'] = $recruit_service_info['id'];
		$recruit_service_arr['service_id'] = $recruit_service_info['service_2_id'];
		$recruit_service_arr['name'] = $service_array[$recruit_service_info['service_2_id']];
		$recruit_service_arr['sex'] = $recruit_service_info['sex'];
		$recruit_service_arr['number'] = $recruit_service_info['number'];
		$recruit_service_arr['require'] = $recruit_service_info['service_require'];
		//获取当前用户的联系方式
		if($usertype == 'user'){
			$userprofile = new userprofile();
			$uprofile = $userprofile -> get_user_profile($uid, $flash);
		}else if($usertype == 'org'){
			$orgprofile = new orgprofile();
			$uprofile = $orgprofile -> get_org_profile($uid, $flash);
		}
		$recruit_service_arr['mobile'] = $uprofile['contact_mobile'];
		$recruit_service_arr['qq'] = $uprofile['contact_qq'];
		$recruit_service_arr['weixin'] = $uprofile['contact_weixin'];
		$recruit_service_arr['email'] = $uprofile['contact_email'];
		//检测报名状态
		$apply = new apply();
		$apply_status = $apply -> check_apply_by_user($uid,$rid,$e_service_id);
		if($apply_status){
			$recruit_service_arr['apply_status'] = '1';
 		}else{
 			$recruit_service_arr['apply_status'] = '0';
 		}
 		//获取招募服务下的三级服务
 		$third_service_arr = $recruit -> get_service_3_list_by_eid($e_service_id, $flash);
 		if($third_service_arr && is_array($third_service_arr)){
 			foreach ($third_service_arr as $key => $value) {
 				$service_3_id = $value['service_3_id'];
 				$recruit_service_arr['children'][$key]['id'] = $service_3_id;
 				$recruit_service_arr['children'][$key]['name'] = $service_array[$service_3_id];
 			}
 		}else{
 			$recruit_service_arr['children'] = array();
 		}

		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $recruit_service_arr;
		return $ret_arr;
	}

	//获取招募里，某个二级服务下，已经报名的红人列表
	function mywork_get_apply_user_list(){
		global $flash,$PAGESIZE;
		$uid = intval($_POST['uid']);
		$rid = intval($_POST['rid']);
		$page = intval($_POST['page']);
		$e_service_id = intval($_POST['e_service_id']);
		$sex = clear_gpq($_POST['sex']);
		$result = clear_gpq($_POST['communication']);
		if($uid<1 || $rid<1 || $e_service_id<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		//_check_login($uid,$app_token);
		//页码处理
		if($page < 1) $page = 1;
		//检测当前招募的发布者是否为当前用户
		$recruit = new recruit();
		$recruit_info = $recruit -> get_recruit_info($rid, $flash);
		if($recruit_info){
			$r_uid = $recruit_info['uid'];
			if($r_uid != $uid){
				return get_state_info(1310);
			}
		}else{
			return get_state_info(1311);
		}
		//检查性别是否合法
		if(!in_array($sex, array('','m','f'))) $sex = '';
		//检查沟通结果 是否合法
		if($result == '1'){
			$result = 'sure';
		}elseif($result == '2') {
			$result = 'hold';
		}elseif($result == '3') {
			$result = 'refuse';
		}else{
			$result = '0';
		}	

		$user = new user();
		$userprofile = new userprofile();
		//$orgprofile = new orgprofile();
		//获取服务缓存数据
		$service = new service();
		$service_list = $service -> get_service($flash);
		if(is_array($service_list) && $service_list){
			//转换服务id 与 name的对应关系
			foreach ($service_list as $k => $v) {
				$service_array[$v['id']] = $v['name'];
			}
		}

		$pagesize = $PAGESIZE['MYWORK_PAGE'];
		$from_rows = ($page - 1) * $pagesize;
		$apply = new apply();
		$apply_list = $apply -> get_user_apply_list_by_recruit_service($rid, $e_service_id, $userid, $sex, $result, $from_rows, $limit);

		if(is_array($apply_list) && $apply_list){
			foreach ($apply_list as $key => $value) {
				if($sex != ''){
					$applyer_id = $value['applyer_id'];
					$aid = $value['aid'];	
				}else{
					$applyer_id = $value['uid'];
					$aid = $value['id'];
				}
				$applyer_list_arr[$key]['aid'] = $aid;
				$applyer_list_arr[$key]['comment'] = $value['description'];
				$applyer_list_arr[$key]['communication'] = $value['result'];
				$applyer_list_arr[$key]['apply_time'] = $value['apply_date'];

				$applyer_list_arr[$key]['mobile'] = $value['u_mobile'];
				$applyer_list_arr[$key]['qq'] = $value['u_qq'];
				$applyer_list_arr[$key]['weixin'] = $value['u_weixin'];
				$applyer_list_arr[$key]['email'] = $value['u_email'];

				$applyer_list_arr[$key]['uid'] = $applyer_id;
				$uinfo = $user -> get_userinfo($applyer_id, $flash);
				$applyer_list_arr[$key]['nickname'] = $uinfo['nickname'];
				$applyer_list_arr[$key]['icon_img'] = $uinfo['icon_server_url'].$uinfo['icon_path_url'];
				//验证身份证
				if($uinfo['identity_card_status'] == 'yes'){
					$applyer_list_arr[$key]['recruit_has_authentication'] = '1';
				}else{
					$applyer_list_arr[$key]['recruit_has_authentication'] = '0';
				}
				//验证手机
				if($uinfo['mobile_status'] == 'yes'){
					$applyer_list_arr[$key]['recruit_has_verify_mobile'] = '1';
				}else{
					$applyer_list_arr[$key]['recruit_has_verify_mobile'] = '0';
				}
				//性别
				if($uinfo['user_type'] == 'user'){
					$uprofile = $userprofile -> get_user_profile($applyer_id, $flash);
					$applyer_list_arr[$key]['sex'] = $uprofile['sex'];
				}else{
					//机构红人 性别为 ''
					$applyer_list_arr[$key]['sex'] = '';
				}
				//获取已报名的三级服务
				$item_service = $apply -> get_item_service_by_e_apply_id($aid);
				if(is_array($item_service) && $item_service){
					foreach ($item_service as $k => $v) {
						$third_service .= $service_array[$v['service_3_id']].'/';
					}
					$third_service = rtrim($third_service, '/');
					$applyer_list_arr[$key]['three_service'] = $third_service;
					unset($third_service);
				}else{
					$applyer_list_arr[$key]['three_service'] = '';
				}
				
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data'] = $applyer_list_arr;
			return $ret_arr;
		}else{
			//没有更多数据le 
			return get_state_info(1309);
		}
	}

	//用户报名招募中的某一个二级服务
	function mywork_apply_recruit_service(){
		global $flash;
		$uid = intval($_POST['uid']);
		$rid = intval($_POST['rid']);
		$e_service_id = intval($_POST['e_service_id']);		
		if($uid<1 || $rid<1 || $e_service_id<1) return get_state_info(1099);
		$app_token = clear_gpq($_POST['app_token']);
		//_check_login($uid,$app_token);

		$user = new user();

		$three_service_str = clear_gpq($_POST['three_service_ids']);
		//检查三级服务 不能为空
		if(strlen($three_service_str) != 0){
			$three_service_arr = explode(',', $three_service_str);
			if(is_array($three_service_arr)){
				foreach ($three_service_arr as $key => $value) {
					if(empty($value)){
						return get_state_info(1306);
					}
				}
			}else{
				return get_state_info(1312);
			}
		}else{
			return get_state_info(1312);
		}
		//报名者的联系方式	
		$mobile = clear_gpq($_POST['mobile']);
		$email = clear_gpq($_POST['email']);
		$qq = clear_gpq($_POST['qq']);
		$weixin = clear_gpq($_POST['weixin']);
		//联系方式 检查  mobile 为必填项 联系方式 不能全空
		if(empty($mobile) && empty($email) && empty($qq) && empty($weixin)){
			return get_state_info(1313);  //联系方式不能全空
		}elseif(empty($mobile)){
			return get_state_info(1314);  //手机号必填
		}

		//不允许一个人报名同一个招募下的同一个服务
		$apply = new apply();
		$result = $apply -> check_apply_by_user($uid, $rid, $e_service_id);
		if($result){
			return get_state_info(1315);
		}else{
			$recruit = new recruit();
			$recruit_info = $recruit -> get_recruit_info($rid, $flash);
			$r_uid = $recruit_info['uid'];
			$recruiter_info = $user -> get_userinfo($r_uid, $flash);
			if($recruiter_info['user_type'] == 'user'){
				$userprofile = new userprofile;
				$recruiter_info = $userprofile -> get_user_profile($r_uid, $flash);
			}else{
				$orgprofile = new orgprofile;
				$recruiter_info = $orgprofile -> get_org_profile($r_uid, $flash);
			}
			//报名信息
			$apply_info['r_mobile'] = $recruiter_info['contact_mobile'];
			$apply_info['r_email'] = $recruiter_info['contact_email'];
			$apply_info['r_weixin'] = $recruiter_info['contact_weixin'];
			$apply_info['r_qq'] = $recruiter_info['contact_qq'];
			$apply_info['mobile'] = $mobile;
			$apply_info['email'] = $email;
			$apply_info['qq'] = $qq;
			$apply_info['weixin'] = $weixin;
			$apply_info['r_uid'] = $r_uid;
			$apply_info['rid'] = $rid;
			$apply_info['service'] = $e_service_id;
			$apply_info['applyer'] = $uid;
			//发送报名信息
			$result = $apply -> add_apply($apply_info);
			if($result){
				//报名成功 添加三级服务
				foreach($three_service_arr as $service_id){
					$r = $apply -> add_third_service($service_id, $rid, $result);
					if(!$r){
						return get_state_info(1316);   //三级服务写入失败!
					}
				}
				$userinfo = $user -> get_userinfo($uid, $flash);				
				$my_user_type = $userinfo['user_type'];
				$contact_info['contact_mobile'] = $mobile;
				$contact_info['contact_email'] = $email;
				$contact_info['contact_weixin'] = $weixin;
				$contact_info['contact_qq'] = $qq;				
				if($my_user_type =='user'){
					$re = $userprofile -> update_user_profile($uid,$contact_info);
					$userprofile -> get_user_profile($uid,$flash = 1);	
				}elseif($my_user_type =='org'){
					$re = $orgprofile -> update_org_profile($uid,$contact_info);
					$orgprofile -> get_org_profile($uid,$flash = 1);				
				}			
				return get_state_info(1000);
			}else{
				return get_state_info(1317);   //报名失败
			}
		}
	}