<?php
	require_once ('../includes/common_inc.php');
	require_once(COMMON_PATH.'/collect.class.php');
	$recruit = new recruit();
	$base = new base();
	$userprofile = new userprofile();
	$user = new user();
	$orgprofile = new orgprofile();
	
	if(!isset($_REQUEST['id']) || empty($_REQUEST['id'])){
		$base -> go_404();
		exit;
	}
	
	//1 根据招募id 查找出 该招募详情
	$recruit_id = intval($_REQUEST['id']);
	$recruit_info = $recruit -> get_recruit_info($recruit_id,$flash);
	if(!$recruit_info){
		$base -> go_404();
		exit;
	}
	//判断是否显示该招募--------
	//只要是审核通过的 任何人都能看  不是审核通过的 管理员可以看
	if($recruit_info['is_checked'] != 'yes'){
		//0 是否是后台管理员来看
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'review'){
			//管理员
		}else{
			//exit('该招募正在审核中或未通过审核！');//审核中/审核未通过
			$base -> go_404();
			exit;
		}
	}
	//判断该招募是否跳转到 招募专题页
	if(isset($recruit_info['type']) && isset($recruit_info['url']) && $recruit_info['type'] == '1' && $recruit_info['url'] != ''){
		echo "<script>top.location.href='".$recruit_info['url']."'</script>";
		exit;
	}
	/* if(isset($recruit_info['type']) && $recruit_info['type'] == '1'){//自身符合  下走
		//是否存在专题数组 存在 下一步
		$special_recruit_list = $recruit -> get_cms_special_recruit_list();
		if($special_recruit_list){#后台有专题
			//处理成 专题id数组
			foreach($special_recruit_list as $k => $v){
				$special_recruit_id_list[$k] = $v['des'];
			}
			//在专题id里 跳
			if(in_array($recruit_id,$special_recruit_id_list)){
				//跳转
				echo "<script>top.location.href='".$special_recruit_list['url']."'</script>";
			}
		}
		
	} */
	
	//正常年月日显示的 add_date  interview_end_time  和时间戳形式的interview_end_time
	$recruit_info['interview_end_time_time_stamp'] = strtotime( $recruit_info['interview_end_time'] );#截止时间转为时间戳状态
	$recruit_info['add_date'] = date( "Y年m月d日", strtotime( $recruit_info['add_date'] ));
	$recruit_info['interview_end_time'] = date( "Y年m月d日", strtotime( $recruit_info['interview_end_time'] ));
	
	
	if(!empty($recruit_info['work_start_time'])){
		$recruit_info['work_start_time'] = date( "Y年m月d日H:i", strtotime( $recruit_info['work_start_time'] ));
		$recruit_info['work_end_time'] = date( "Y年m月d日H:i", strtotime( $recruit_info['work_end_time'] ));
	}
	//var_dump($recruit_info);
	$recruit_info['name_r'] = urlencode($recruit_info['name']);
	$smarty -> assign('recruit_info',$recruit_info);
	//根据province_id/city_id/district_id查找出 地址
	$addr = $base -> get_address_info($recruit_info['province_id'],$recruit_info['city_id'],$recruit_info['district_id'],$flash);
	$smarty -> assign('addr',$addr);
	//2 根据招募id 查找出  该招募要招的服务 多个一级二级
	$service_name_arr = $base -> get_service_list($flash);
	//var_dump($service_list);exit;
	$recruit_service_list = $recruit -> get_service_list_by_recruit($recruit_id,$flash);#可能多个一级二级
	if(is_array($recruit_service_list)){
		foreach($recruit_service_list as $k => $v){
			$recruit_service_list[$k]['number'] = $COMMON_CONFIG["RECRUIT_NUM"]["OPTION"][$v['number']];#数量
			$recruit_service_list[$k]['service_2_name'] = $service_name_arr[$v['service_2_id']];#通过服务数组 获取每一项二级服务name
			$recruit_service_list[$k]['service_3_list'] = $recruit -> get_service_3_list_by_eid($v['id'],$flash);#获取二级下 所有三级服务
			if(is_array($recruit_service_list[$k]['service_3_list'])){#三级服务必选
				foreach($recruit_service_list[$k]['service_3_list'] as $k0 => $v0){
					$recruit_service_list[$k]['service_3_list'][$k0]['service_3_name'] = $service_name_arr[$v0['service_3_id']]; #通过服务数组 获取每一个三级服务的name
				}
			}else{
				exit('三级服务出错！');
			}
		}
	}else{
		exit('招募服务出错！');
	}
	//var_dump($recruit_service_list);exit;
	$smarty -> assign('recruit_service_list',$recruit_service_list);
	//3 根据uid 查找出用户详情
	$user_info = $user -> get_userinfo($recruit_info['uid'],$flash);
	$userlist = array();
	$orglist = array();
	//判断是否是机构
	if($user_info['user_type'] == 'org'){
		//机构  头像 nickmame 工商号 手机status
		//机构详情  成立时间  类型  地址
		$orglist = $orgprofile -> get_org_profile($recruit_info['uid'],$flash);
		$orglist['type_name'] = $base -> get_org_type_info($orglist['type'],$flash);#根据type的id获取type的name
		$orglist['address_info'] = $base -> get_address_info($orglist['province_id'],$orglist['city_id'],$orglist['district_id'],$flash);#根据省市区id查找出name
	}else{
		//红人  头像 nickname 身份证 手机status
		//红人详情   根据红人id查找红人选取的角色 地址
		$userlist = $userprofile -> get_user_profile($recruit_info['uid'],$flash);
		$userlist['address_info'] = $base -> get_address_info($userlist['province_id'],$userlist['city_id'],$userlist['district_id'],$flash);#根据省市区id查找出name
		$userlist['user_role'] = $userprofile -> get_role_list_by_user($recruit_info['uid'],$flash);
		//var_dump($userlist['user_role']);
	}
	$smarty -> assign('user_info',$user_info);
	$smarty -> assign('userlist',$userlist);
	$smarty -> assign('orglist',$orglist);
	
	//招募宣传照
	$recruit_photo_list = $recruit -> get_recruit_photo_list($recruit_id,$flash);
	if(!$recruit_photo_list){
		$recruit_photo_list	= array();
	}
	//var_dump($recruit_photo_list);
	$smarty -> assign('recruit_photo_list',$recruit_photo_list);
	$json_recruit_photo_list = json_encode($recruit_photo_list);
	$smarty -> assign('json_recruit_photo_list',$json_recruit_photo_list);
	
	$smarty -> display("recruit/recruit_info.html");
?>