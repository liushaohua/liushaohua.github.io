<?php
header("Content-type:text/html;charset=utf-8");
//--------------------wangyifan  start-----------------------

//招募展示--
/* function mywork_get_recruit_profile(){
	global $flash;
	$user = new user();
	$recruit = new recruit();
	$collect = new collect();
	$apply = new apply();
	$base = new base();
	$rid = intval(@$_POST['rid']);
	$uid = intval(@$_POST['uid']);
	if($rid<1){
		return get_state_info(1099);#非法
	}
	if($uid<1){
		return get_state_info(1099);#非法
	}
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
		$collect_list = $collect -> get_collect_list_by_user_type($uid,'recruit');
		if(in_array($rid,$collect_list)){
			$handle_recruit_info['has_favorite'] = 1;#has_favorite
		}else{
			$handle_recruit_info['has_favorite'] = 0;
		}
		//4招募宣传照
		$recruit_photo_list = $recruit -> get_recruit_photo_list($rid,$flash);
		if(!$recruit_photo_list){
			$recruit_photo_list	= array();
		}
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
		foreach($recruit_service_list as $k => $v){
			$service_list[$k]['id'] = $v['id'];
			$service_list[$k]['name'] = $v['service_2_name'];
			$service_list[$k]['sex'] = $v['sex'];
			$service_list[$k]['number'] = $v['number'];
			$service_list[$k]['require'] = $v['service_require'];
			$service_list[$k]['service_id'] = $v['service_2_id'];
			//判断当前用户针对该服务eid是否报过名
			$result = $apply -> check_apply_by_user($uid,$rid,$v['id']);
			if($result){
				$service_list[$k]['apply_status'] = 1;
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
} */
//--------------------wangyifan  end-----------------------	
//                          	    service  id url
	//id  name  sex number require  service_id  apply_status children 
	//id  name
	
	
	//rid  title  photos  status  type has_favorite  begin  end  recruit_uid  recruit_icon_img  recruit_nickname  recruit_has_authentication recruit_has_verify_mobile introduction  work_begin 	  work_end work_place service  id url
	//id  name  sex number require  service_id  apply_status children 
	//id  name
	