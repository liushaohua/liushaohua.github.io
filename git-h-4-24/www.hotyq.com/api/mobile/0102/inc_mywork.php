<?php
//------------------wangyifan  start---------------------------
function mywork_get_recruit_profile(){
	global $flash;
	$user = new user();
	$recruit = new recruit();
	$collect = new collect();
	$apply = new apply();
	$base = new base();
	$rid = intval(@$_POST['rid']);
	if($rid<1){
		return get_state_info(1099);#非法
	}
	//uid 不能不存在  可以为空 可以为值
	if(!isset($_POST['uid'])) return get_state_info(1099);#非法
	$uid = intval(@$_POST['uid']);
	//1 根据招募id 查找出 该招募详情
	$recruit_info = $recruit -> get_recruit_info($rid,$flash);
	if($recruit_info){
		//处理招募
		//1基本
		$handle_recruit_info['rid'] = $recruit_info['id'];#id
		$handle_recruit_info['title'] = $recruit_info['name'];#name
		$handle_recruit_info['status'] = $recruit_info['status'];#status
		$handle_recruit_info['type'] = $recruit_info['type_info']['type'];#type
		$handle_recruit_info['begin'] = strtotime($recruit_info['add_date']);#begin
		$handle_recruit_info['end'] = strtotime($recruit_info['interview_end_time']);#end
		$handle_recruit_info['introduction'] = $recruit_info['descr'];#introduction??
		$handle_recruit_info['work_begin'] = strtotime($recruit_info['work_start_time']);#work_begin
		$handle_recruit_info['work_end'] = strtotime($recruit_info['work_end_time']);#work_end
		//2根据province_id/city_id/district_id查找出 地址
		$addr = $base -> get_address_info($recruit_info['province_id'],$recruit_info['city_id'],$recruit_info['district_id'],$flash);
		$handle_recruit_info['work_place'] = $addr['address'].$recruit_info['addr_detail'];#work_place
		//3当前登录用户是否收藏??
		if(!empty($uid)){
			$collect_result = $collect -> get_collect_exists($uid,'recruit',$rid);
			if($collect_result){
				$handle_recruit_info['has_favorite'] = 1;
			}else{
				$handle_recruit_info['has_favorite'] = 0;
			}
		}else{
			$handle_recruit_info['has_favorite'] = 0;
		}
		//4招募宣传照
		$recruit_photo_list = $recruit -> get_recruit_photo_list($rid,$flash);
		if(!$recruit_photo_list){
			$recruit_photo_list	= array();
		}
		$photo_list = array();
		foreach($recruit_photo_list as $k => $v){
			$photo_list[$k]['id'] = $v['id'];
			$photo_list[$k]['url'] = $v['server_url'].$v['path_url'];
		}
		$handle_recruit_info['photos'] = $photo_list;#photos--
		//5招募发布人
		$user_info = $user -> get_userinfo($recruit_info['uid'],$flash);
		$handle_recruit_info['recruit_icon_img'] = $user_info['icon_server_url'].$user_info['icon_path_url'];#recruit_icon_img
		$handle_recruit_info['recruit_nickname'] = $user_info['nickname'];#recruit_nickname
		$handle_recruit_info['recruit_uid'] = $user_info['id'];#recruit_uid
		if($user_info['identity_card_status'] == 'yes'){
			$handle_recruit_info['recruit_has_authentication'] = '1';#recruit_has_authentication
		}else{
			$handle_recruit_info['recruit_has_authentication'] = '0';
		}
		if($user_info['mobile_status'] == 'yes'){
			$handle_recruit_info['recruit_has_verify_mobile'] = '1';#recruit_has_verify_mobile
		}else{
			$handle_recruit_info['recruit_has_verify_mobile'] = '0';
		}
		//6 根据招募id 查找出  该招募要招的服务 多个一级二级
		$service_name_arr = $base -> get_service_list($flash);
		$recruit_service_list = $recruit -> get_service_list_by_recruit($rid,$flash);#可能多个一级二级
		//var_dump($recruit_service_list);
		
		if(is_array($recruit_service_list)){
			foreach($recruit_service_list as $k => $v){
				$recruit_service_list[$k]['service_2_name'] = $service_name_arr[$v['service_2_id']];#通过服务数组 获取每一项二级服务name
				$recruit_service_list[$k]['service_3_list'] = $recruit -> get_service_3_list_by_eid($v['id'],$flash);#获取二级下 所有三级服务
				if(is_array($recruit_service_list[$k]['service_3_list'])){#三级服务必选
					foreach($recruit_service_list[$k]['service_3_list'] as $k0 => $v0){
						$recruit_service_list[$k]['service_3_list'][$k0]['service_3_name'] = $service_name_arr[$v0['service_3_id']]; #通过服务数组 获取每一个三级服务的name
					}
				}else{
					//exit('三级服务出错！');
					return get_state_info(1410);
				}
			}
		}else{
			//exit('招募服务出错！');
			return get_state_info(1411);
		}
		//var_dump($recruit_service_list);
		$service_list = array();
		foreach($recruit_service_list as $k => $v){
			$service_list[$k]['id'] = $v['id'];
			$service_list[$k]['name'] = $v['service_2_name'];
			$service_list[$k]['sex'] = $v['sex'];
			$service_list[$k]['number'] = $v['number'];
			$service_list[$k]['require'] = $v['service_require'];
			$service_list[$k]['service_id'] = $v['service_2_id'];
			//判断当前用户针对该服务eid是否报过名
			if(!empty($uid)){
				$result = $apply -> check_apply_by_user($uid,$rid,$v['id']);
				if($result){
					$service_list[$k]['apply_status'] = 1;
				}else{
					$service_list[$k]['apply_status'] = 0;
				}
			}else{
				$service_list[$k]['apply_status'] = 0;
			}
			foreach($v['service_3_list'] as $k0 => $v0){
				$service_list[$k]['children'][$k0]['id'] = $v0['service_3_id'];
				$service_list[$k]['children'][$k0]['name'] = $v0['service_3_name'];
			}
		}
		//var_dump($service_list);exit;
		$handle_recruit_info['service'] = $service_list;#service--
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $handle_recruit_info;
		return $ret_arr;
	}else{
		//招募没内容
		return get_state_info(1409);
	}
}
//招募展示(自己查看)--
function mywork_get_recruit_profile_own(){
	global $flash;
	$user = new user();
	$recruit = new recruit();
	$collect = new collect();
	$apply = new apply();
	$base = new base();
	//0验证用户是否登录
	$rid = intval(@$_POST['rid']);
	$uid = intval(@$_POST['uid']);
	$token = clear_gpq(@$_POST['app_token']);
	if($uid<1) return get_state_info(1099);			
	if(empty($token)) return get_state_info(1099);			
	if($rid<1) return get_state_info(1099);			
	_check_login($uid,$token);
	//1 根据招募id 查找出 该招募详情
	$recruit_info = $recruit -> get_recruit_info($rid,$flash);
	if($recruit_info){
		//处理招募
		//0登录了 验证是否是自己的招募
		if($recruit_info['uid'] != $uid) return get_state_info(1099);
		//1基本
		$handle_recruit_info['rid'] = $recruit_info['id'];#id
		$handle_recruit_info['title'] = $recruit_info['name'];#name
		$handle_recruit_info['status'] = $recruit_info['status'];#status
		$handle_recruit_info['type'] = $recruit_info['type_info']['type'];#type
		$handle_recruit_info['begin'] = strtotime($recruit_info['add_date']);#begin
		$handle_recruit_info['end'] = strtotime($recruit_info['interview_end_time']);#end
		$handle_recruit_info['introduction'] = $recruit_info['descr'];#introduction??
		$handle_recruit_info['work_begin'] = strtotime($recruit_info['work_start_time']);#work_begin
		$handle_recruit_info['work_end'] = strtotime($recruit_info['work_end_time']);#work_end
		//2根据province_id/city_id/district_id查找出 地址
		$addr = $base -> get_address_info($recruit_info['province_id'],$recruit_info['city_id'],$recruit_info['district_id'],$flash);
		$handle_recruit_info['work_place'] = $addr['address'].$recruit_info['addr_detail'];#work_place
		//3当前登录用户是否收藏（自己不能收藏  传0）
		$handle_recruit_info['has_favorite'] = 0;
		//4招募宣传照
		$recruit_photo_list = $recruit -> get_recruit_photo_list($rid,$flash);
		if(!$recruit_photo_list){
			$recruit_photo_list	= array();
		}
		$photo_list = array();
		foreach($recruit_photo_list as $k => $v){
			$photo_list[$k]['id'] = $v['id'];
			$photo_list[$k]['url'] = $v['server_url'].$v['path_url'];
		}
		$handle_recruit_info['photos'] = $photo_list;#photos--
		//5已报名总数 邀约总数apply_number invitation_number
		$handle_recruit_info['apply_number'] = $recruit_info['apply_count'];
		$handle_recruit_info['invitation_number'] = $recruit_info['invite_count'];
		//6 根据招募id 查找出  该招募要招的服务 多个一级二级
		$service_name_arr = $base -> get_service_list($flash);
		$recruit_service_list = $recruit -> get_service_list_by_recruit($rid,$flash);#可能多个一级二级
		//var_dump($recruit_service_list);
		if(is_array($recruit_service_list)){
			foreach($recruit_service_list as $k => $v){
				$recruit_service_list[$k]['service_2_name'] = $service_name_arr[$v['service_2_id']];#通过服务数组 获取每一项二级服务name
				$recruit_service_list[$k]['service_3_list'] = $recruit -> get_service_3_list_by_eid($v['id'],$flash);#获取二级下 所有三级服务
				if(is_array($recruit_service_list[$k]['service_3_list'])){#三级服务必选
					foreach($recruit_service_list[$k]['service_3_list'] as $k0 => $v0){
						$recruit_service_list[$k]['service_3_list'][$k0]['service_3_name'] = $service_name_arr[$v0['service_3_id']]; #通过服务数组 获取每一个三级服务的name
					}
				}else{
					//exit('三级服务出错！');
					return get_state_info(1410);
				}
			}
		}else{
			//exit('招募服务出错！');
			return get_state_info(1411);
		}
		//var_dump($recruit_service_list);
		$service_list = array();
		foreach($recruit_service_list as $k => $v){
			$service_list[$k]['id'] = $v['id'];
			$service_list[$k]['name'] = $v['service_2_name'];
			$service_list[$k]['sex'] = $v['sex'];
			$service_list[$k]['number'] = $v['number'];
			$service_list[$k]['require'] = $v['service_require'];
			$service_list[$k]['service_id'] = $v['service_2_id'];
			//判断当前用户针对该服务eid是否报过名（自己 传0）
			$service_list[$k]['apply_status'] = 0;
			$service_list[$k]['apply_number'] = $v['apply_num'];
			$service_list[$k]['invitation_number'] = $v['invite_num'];
			foreach($v['service_3_list'] as $k0 => $v0){
				$service_list[$k]['children'][$k0]['id'] = $v0['service_3_id'];
				$service_list[$k]['children'][$k0]['name'] = $v0['service_3_name'];
			}
		}
		//var_dump($service_list);exit;
		$handle_recruit_info['service'] = $service_list;#service--
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $handle_recruit_info;
		return $ret_arr;
	}else{
		//招募没内容
		return get_state_info(1409);
	}
}
//------------------wangyifan  end-----------------------------
//设置某二级服务下的邀约用户的备注
	function mywork_set_invitation_comment(){
		$invite = new invite;
		$uid = intval($_POST['uid']);
		$app_token = clear_gpq($_POST['app_token']);	
		$iid = intval($_POST['iid']);	#邀约的id
		$des = clear_gpq($_POST['comment']);	#备注的信息
		_check_login($uid,$app_token);	
		if($iid<1) return get_state_info(1099);		
		$re = $invite -> update_invite_description($iid, $des);
		if($re){
			return get_state_info(1000);
		}else{
			return get_state_info(1380);
		}	
	}
//设置某二级服务下的邀约用户的沟通结果
	function mywork_set_invitation_communication(){
		$invite = new invite;
		$uid = intval($_POST['uid']);	
		$app_token = clear_gpq($_POST['app_token']);	
		$iid = intval($_POST['iid']);	#邀约的id
		$communication = intval($_POST['communication']);	#沟通结果信息
		if($communication == '1'){
			$result ='sure';
		}else if($communication == '2'){
			$result = 'hold';
		}else if($communication == '3'){
			$result = 'refuse';
		}
		_check_login($uid,$app_token);		
		if($iid<1) return get_state_info(1099);		
		$re = $invite -> update_invite_result($iid, $result);
		if($re){
			return get_state_info(1000);
		}else{
			return get_state_info(1380);
		}	
	}	
		
//邀约某人
	function mywork_invitation_someone(){
		global $flash;
		$invite = new invite;
		$userprofile = new userprofile;
		$orgprofile = new orgprofile;
		$user = new user;
		//rid true String 招募id
		//e_service_id true String 二级服务的id
		//three_service_list true String 二级服务的列表。例如1,2,3,4,5,6
		//检测邀约的服务是否至少选了一项

		$uid = intval($_POST['uid']);	
		$app_token = clear_gpq($_POST['app_token']);	
		$rid = intval($_POST['rid']);	
		$invite_uid = intval($_POST['invite_uid']); 		
		$e_service_id = intval($_POST['e_service_id']);	
		$three_service_list = clear_gpq($_POST['three_service_list']);	
		$mobile = clear_gpq($_POST['mobile']);	
		$email = clear_gpq($_POST['email']);	
		$weixin = clear_gpq($_POST['weixin']);	
		$qq = clear_gpq($_POST['qq']);	
		_check_login($uid,$app_token);	
		$invite_info['rid'] = $rid;	//招募id
		$invite_info['uid'] = $invite_uid;	//邀约着id
		$invite_info['r_uid'] = $uid;	//发招募者id
		$invite_info['service'] = $e_service_id;  //e_service ID
		$invite_info['service_3'] = $three_service_list;  //三级服务ID
		$invite_info['mobile'] = $mobile;  //手机号
		$invite_info['email'] = $email; //邮箱
		$invite_info['weixin'] = $weixin; //微信
		$invite_info['qq'] = $qq; //qq
		if($uid<1 || $rid<1 || $e_service_id<1) return get_state_info(1099);	
		$userinfo = $user -> get_userinfo($uid);
		if(!$userinfo) return get_state_info(1099);	
		$invite_info['user_type'] = $userinfo['user_type'];	//邀约着的用户类型		
		if(empty($invite_info['service_3'])) return get_state_info(1182);
		//至少填写一项联系方式
		if(empty($invite_info['mobile']) && empty($invite_info['email']) && empty($invite_info['weixin']) && empty($invite_info['qq'])){
			return get_state_info(1181);
		}
		if(empty($invite_info['mobile'])){
			return get_state_info(1183);
		}
		//var_dump($invite_info);
		//不允许对一个人邀约同一个招募下的同一个服务
		$result = $invite -> check_invite_by_user($invite_info['uid'],$invite_info['rid'],$invite_info['service']);
		if($result){
			return get_state_info(1180);
		}else{
			$contact_info['contact_mobile'] = $invite_info['mobile']; 
			$contact_info['contact_email']	= $invite_info['email']; 
			$contact_info['contact_weixin']	= $invite_info['weixin'];
			$contact_info['contact_qq']	= $invite_info['qq']; 
			if($invite_info['user_type'] =='user'){
				$re = $userprofile -> update_user_profile($uid,$contact_info);
				$userprofile -> get_user_profile($uid,$flash = 1);
			}elseif($invite_info['user_type'] =='org'){
				$re = $orgprofile -> update_org_profile($uid,$contact_info);
				$orgprofile -> get_org_profile($uid,$flash = 1);				
			}		
			$result = $invite -> add_invite($invite_info);
			if($result){
				//邀约成功 添加三级服务
				$service_3_str = trim($invite_info['service_3'],',');
				if(strpos($service_3_str, ',')){
					$third_service_arr = explode(',',$service_3_str);	
				}else{
					$third_service_arr = array($service_3_str);
				}
				foreach($third_service_arr as $service_id){
					$re = $invite -> add_third_service($service_id, $invite_info['rid'], $result);
					if(!$re){
						return get_state_info(1185);
					}
				}		
				return get_state_info(1000);
			}else{
				return get_state_info(1184);
			}
		}			
	}
	
//获取邀约的招募列表	
function mywork_get_invitation_recruit(){
	$recruit = new recruit;
	$uid = intval($_POST['uid']);	
	$app_token = clear_gpq($_POST['app_token']);
	_check_login($uid,$app_token);	
	$result = $recruit -> get_recruit_list_by_user_for_invite($uid);
	if($result){
		foreach($result as $k => $v){
			$recruit_list[$k]['rid'] = $v['id'];
			$recruit_list[$k]['title'] = $v['name'];
		}	
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $recruit_list;
		return $ret_arr;		
	}else{
		$ret_arr = get_state_info(1197);	
		return $ret_arr;
	}


}	
	
//获取邀约时的招募下的所有二级服务列表。
	function mywork_get_invitation_service(){
		global $flash;
		$base = new base;
		$recruit= new recruit;
		$service= new service;
		$uid = intval($_POST['uid']);	
		$rid = intval($_POST['rid']);	
		$app_token = clear_gpq($_POST['app_token']);		
		//_check_login($uid,$app_token);	
		if($rid < 1) return get_state_info(1099);
		$result = $recruit -> get_service_list_by_recruit($rid);
		//var_dump($result);
		if($result){
			$service_list = $service -> get_service($flash);		
			foreach($service_list as $v){
				$service_list_arr[$v['id']] =  $v['name'];
			}		
			foreach($result as $k => $v){	
				$service_2_list[$k]['e_service_id'] = $v['id'];
				$service_2_list[$k]['name'] = $service_list_arr[$v['service_2_id']];
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data'] = $service_2_list;
			return $ret_arr;
		}else{
			//不存在此招募1196
			return get_state_info(1196);
		}	
	}
//获取邀约时的二级服务下的所有三级服务列表

	function mywork_get_invitation_three_service(){
		global $flash;
		$base = new base;
		$user = new user;
		$userprofile = new userprofile; 
		$orgprofile = new orgprofile; 
		$recruit= new recruit;
		$service= new service;
		$uid = intval($_POST['uid']);	
		$rid = intval($_POST['rid']);	
		$e_service_id = intval($_POST['e_service_id']);	
		$app_token = clear_gpq($_POST['app_token']);		
		//_check_login($uid,$app_token);	
		if($e_service_id <1) return get_state_info(1099);
		$userinfo = $user -> get_userinfo($uid, $flash);
		if(!$userinfo) return get_state_info(1099);
		$usertype = $userinfo['user_type'];
		//获取当前用户的联系方式
		if($usertype == 'user'){
			$uprofile = $userprofile -> get_user_profile($uid, $flash);
		}else if($usertype == 'org'){
			$uprofile = $orgprofile -> get_org_profile($uid, $flash);
		}		
		$result = $recruit -> get_service_3_list_by_eid($e_service_id);	
		if(is_array($result)){
			$service_list = $service -> get_service($flash);
			foreach($service_list as $v){
				$service_list_arr[$v['id']] =  $v['name'];
			}		
			foreach($result as $k => $v){				
				//$re = $base -> get_service_info($v['service_3_id']);
				$service_3_list[$k]['rid'] = $v['service_3_id'];
				$service_3_list[$k]['title'] = $service_list_arr[$v['service_3_id']];
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data']['service'] = $service_3_list;
			$ret_arr['data']['mobile'] = $uprofile['contact_mobile'];
			$ret_arr['data']['qq'] = $uprofile['contact_qq'];
			$ret_arr['data']['weixin'] = $uprofile['contact_weixin'];
			$ret_arr['data']['email'] = $uprofile['contact_email'];
			return $ret_arr;
		}else{
			$ret_arr = get_state_info(1196);
		}
	}	

//设置某二级服务下的报名用户的备注
	function mywork_set_apply_comment(){
		$apply = new apply;
		$uid = intval($_POST['uid']);	
		$app_token = clear_gpq($_POST['app_token']);	
		$aid = intval($_POST['aid']);	#报名的id
		$des = clear_gpq($_POST['comment']);	#备注的信息
		_check_login($uid,$app_token);		
		if($aid<1) return get_state_info(1099);			
		$re = $apply -> update_apply_description($aid, $des);
		if($re){
			return get_state_info(1000);
		}else{
			return get_state_info(1380);
		}	
	}
//设置某二级服务下的报名用户的沟通结果
	function mywork_set_apply_communication(){
		$apply = new apply;
		$uid = intval($_POST['uid']);	
		$app_token = clear_gpq($_POST['app_token']);	
		$aid = intval($_POST['aid']);	#报名的id
		$communication = intval($_POST['communication']);	#沟通结果信息
		_check_login($uid,$app_token);	
		if($aid<1) return get_state_info(1099);		
		if($communication == '1'){
			$result ='sure';
		}else if($communication == '2'){
			$result = 'hold';
		}else if($communication == '3'){
			$result = 'refuse';
		}	
		$re = $apply -> update_apply_result($aid, $result);
		if($re){
			return get_state_info(1000);
		}else{
			return get_state_info(1380);
		}	
	}		
//获取机构创立时间和机构类型的范围	
	function mywork_get_instituted_date_and_org_type_range(){
		global $flash,$COMMON_CONFIG;
		$base = new base;
		$ret_array = $base -> get_org_type_list($flash);	
		$ret_arr = get_state_info(1000);		
		$ret_arr['data']['org_types'] = $ret_array;	
		$ret_arr['data']['instituted_date_begin'] = $COMMON_CONFIG["CREATE_YEAR"]["RANGE"]['min'];
		return $ret_arr;			
	}	
//获取招募里，某个二级服务下，已经邀约的红人列表
	function mywork_get_invite_user_list(){
		global $flash,$PAGESIZE;
		$uid = intval($_POST['uid']);
		$rid = intval($_POST['rid']);
		$page = intval($_POST['page']);
		$e_service_id = intval($_POST['e_service_id']);
		$sex = clear_gpq($_POST['sex']);
		$result = clear_gpq($_POST['communication']);
		$app_token = clear_gpq($_POST['app_token']);		
		_check_login($uid,$app_token);	
		if($uid<1 || $rid<1 || $e_service_id<1) return get_state_info(1099);
		//检查页码是否 合法
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
		$app_token = clear_gpq($_POST['app_token']);
		//_check_login($uid,$app_token);

		$user = new user();
		$userprofile = new userprofile();

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
		$invite = new invite();
		$invite_list = $invite -> get_user_invite_list_by_recruit_service($rid, $e_service_id, $userid, $sex, $result, $from_rows, $limit);

		if(is_array($invite_list) && $invite_list){
			foreach ($invite_list as $key => $value) {
				if($sex != ''){
					$inviter_id = $value['inviter_id'];
					$iid = $value['iid'];	
				}else{
					$inviter_id = $value['uid'];
					$iid = $value['id'];
				}
				$inviter_list_arr[$key]['iid'] = $iid;
				$inviter_list_arr[$key]['comment'] = $value['description'];
				$inviter_list_arr[$key]['communication'] = $value['result'];
				$inviter_list_arr[$key]['invite_time'] = strtotime($value['invite_date']);

				// $inviter_list_arr[$key]['mobile'] = $value['u_mobile'];
				// $inviter_list_arr[$key]['qq'] = $value['u_qq'];
				// $inviter_list_arr[$key]['weixin'] = $value['u_weixin'];
				// $inviter_list_arr[$key]['email'] = $value['u_email'];

				$inviter_list_arr[$key]['uid'] = $inviter_id;
				$uinfo = $user -> get_userinfo($inviter_id, $flash);
				$inviter_list_arr[$key]['nickname'] = $uinfo['nickname'];
				$inviter_list_arr[$key]['icon_img'] = $uinfo['icon_server_url'].$uinfo['icon_path_url'];
				//验证身份证
				if($uinfo['identity_card_status'] == 'yes'){
					$inviter_list_arr[$key]['recruit_has_authentication'] = '1';
				}else{
					$inviter_list_arr[$key]['recruit_has_authentication'] = '0';
				}
				//验证手机
				if($uinfo['mobile_status'] == 'yes'){
					$inviter_list_arr[$key]['recruit_has_verify_mobile'] = '1';
				}else{
					$inviter_list_arr[$key]['recruit_has_verify_mobile'] = '0';
				}
				//性别
				if($uinfo['user_type'] == 'user'){
					$uprofile = $userprofile -> get_user_profile($inviter_id, $flash);
					$inviter_list_arr[$key]['sex'] = $uprofile['sex'];
				}else{
					//机构红人 性别为 ''
					$inviter_list_arr[$key]['sex'] = '';
				}
				//获取已报名的三级服务
				$item_service = $invite -> get_item_service_by_e_invite_id($iid);
				if(is_array($item_service) && $item_service){
					foreach ($item_service as $k => $v) {
						$third_service .= $service_array[$v['service_3_id']].'/';
					}
					$third_service = rtrim($third_service, '/');
					$inviter_list_arr[$key]['three_service'] = $third_service;
					unset($third_service);
				}else{
					$inviter_list_arr[$key]['three_service'] = '';
				}
				
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data'] = $inviter_list_arr;
			return $ret_arr;
		}else{
			//没有更多数据le 
			return get_state_info(1195);
		}
	}
//获取一级服务列表
	function mywork_get_one_service(){
		global $flash;
		$service = new service;
		$result = $service -> get_service($flash);
		foreach($result as $k => $value){
			if($value['parent_id'] == 0){
				$service_first_list[$k]['id'] = $value['id'];
				$service_first_list[$k]['name'] = $value['name'];
			}
		}
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $service_first_list;
		return $ret_arr;		
	}
//获取指定一级服务下的二级服务列表
	function mywork_get_two_service(){
		global $flash;
		$service = new service;
		$sid = intval($_POST['sid']);
		if($sid < 1) return get_state_info(1099);
		$service_second_list = $service -> get_children_service($sid,$flash);
		if($service_second_list){
			foreach ($service_second_list as $k => $v){
				$arr[$k]['id'] = $v['id'];	
				$arr[$k]['name'] = 	$v['name'];
				$arr[$k]['url'] = $v['cover'];
			}
			$ret_arr = get_state_info(1000);			
			$ret_arr['data'] = $arr;			
		}else{
			$arr = '';
			$ret_arr = get_state_info(1194);					
		}
		return $ret_arr;		
	}
//获搜索热词
	function mywork_get_hot_search(){
		global $flash;
		$base = new base();
		$result = $base -> get_hot_search_words($flash);
		
		if(is_array($result) && $result){
			foreach ($result as $key => $value) {
				$hot_words[] = $value['des'];
			}
			$ret_arr = get_state_info(1000);
			$ret_arr['data'] = $hot_words;
			return $ret_arr;
		}else{
			return get_state_info(1319);
		}
	}

//获取用户报名的二级服务的信息
	function mywork_get_recruit_service_info(){
		global $flash;
		$uid = intval($_POST['uid']);
		$rid = intval($_POST['rid']);
		$e_service_id = intval($_POST['e_service_id']);
		if($uid<1 || $rid<1 || $e_service_id<1) return get_state_info(1099);	
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);
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
		_check_login($uid,$app_token);
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
		_check_login($uid,$app_token);

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
	//修改机构创立时间和机构类型
	function mywork_set_instituted_date_and_org_type(){
		global $flash;
		$orgprofile = new orgprofile;
		$user = new user;
		$uid = intval($_POST['uid']);
		if($uid<1) return get_state_info(1099);
		$create_time = intval($_POST['instituted_date_begin']);
		$type = intval($_POST['org_type']);
		$app_token = clear_gpq($_POST['app_token']);
		_check_login($uid,$app_token);
		$orginfo = $orgprofile -> get_org_profile($uid, $flash);
		if(!$orginfo) return get_state_info(1099);				
		$org_profile_array['create_time'] = $create_time;		
		$org_profile_array['type'] = $type;		
		if($create_time < 1) return get_state_info(1136);
		if($type < 1) return get_state_info(1137);
		$re = $orgprofile -> update_org_profile($uid,$org_profile_array);		
		if($re == 1000){
			$user -> update_data_percent($uid);
			return get_state_info(1000);	
		}else{
			return get_state_info(1112); 		//资料修改失败
		}				
	}
	
	