<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once('../includes/common_home_inc.php');	
	//require_once ("../../common/invite.class.php");	
	$invite = new invite();
	$user = new user();
	$userprofile = new userprofile();
	$recruit = new recruit;
	$base = new base;
	$service_list = $base -> get_service_list($flash);	#所有服务名字;	
	$userid = $user_info['id'];
	$usertype = $user_info['user_type'];
	@$rid = intval($_REQUEST['recruit']);
	if($rid < 0 || $rid == 0){
		$base -> go_404();
		//exit('系统错误!');
	}
	$recruit_info = $recruit -> get_recruit_info($rid, $flash);		#获取招募详情
	$r_uid = $recruit_info['uid'];
	if($r_uid != $userid){
		$base -> go_404();
		//exit('不该看的不要看哦!');
	}
	//获取招募的服务(二级服务)
          $recruit_service_list = $invite -> get_recruit_service($rid);
	if($recruit_service_list){
		foreach ($recruit_service_list as $k => $v) {
			$service_2_id = $v['service_2_id'];
			$invite_num = $v['invite_num'];
			$service_2_name = $service_list[$service_2_id];
			$e_service_id = $v['id'];
			$list[$service_2_name]['service_2_id'] = $service_2_id;
			$list[$service_2_name]['e_service_id'] = $e_service_id;
			@$list[$service_2_name]['num'] = $invite_num;
		}

		$smarty -> assign('list',$list);

		//获取招募的服务id	传入条件来筛选结果
		@$e_service = intval($_REQUEST['service']);
		@$invite_sex = clear_gpq($_REQUEST['sex']);
		@$invite_result = clear_gpq($_REQUEST['result']);
		@$s_help = clear_gpq($_REQUEST['s_help']);
                     @$r_help = clear_gpq($_REQUEST['r_help']);
		if(!$e_service){
			$service_2_array = array_shift($list);
			$e_service = $service_2_array['e_service_id'];		
		}
		$smarty -> assign('active_service_id',$e_service);

		$result = $invite -> get_invite_by_recruit_service($rid, $e_service); #通过关联 e_service 和 招募rid 获取其下的所有邀约 *
		if($result){
			foreach ($result as $key => $value) {
				$uid = $value['uid'];
				$connect_result = $value['result'];
				$user_info = $user -> get_userinfo($uid, $flash);
				$user_type = $user_info['user_type'];
				if($user_type == 'user'){
					$u_profile = $userprofile -> get_user_profile($uid, $flash);
					$u_sex = $u_profile['sex'];
				}else{
					$u_sex = '';
				}
				if($invite_sex){
					if ($invite_sex == $u_sex) {
						$value['u_sex'] = $u_sex;
						$value['user_type'] = $user_type;
						$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
						$value['u_face'] = $u_face;
						$value['u_name'] = $user_info['nickname'];
						$e_invite_id = $value['id'];
						$invite_item = $invite -> get_item_service_by_e_invite_id($e_invite_id);	#获取三级服务
						
						if(is_array($invite_item)){
							foreach (@$invite_item as $kk => $vv) {
								@$third_service_name[$value['id']][$kk] = $service_list[$vv['service_3_id']];
							}	
							$value['third_service'] = $third_service_name;
						}else{
							$value['third_service'] = '';
						}
						
						$invite_info_list[] = $value;
					}
					$smarty -> assign('sex',$invite_sex);

				}else if($invite_result){
					if ($invite_result == $connect_result) {
						$value['u_sex'] = $u_sex;
						$value['user_type'] = $user_type;
						$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
						$value['u_face'] = $u_face;
						$value['u_name'] = $user_info['nickname'];
						$e_invite_id = $value['id'];
						$invite_item = $invite -> get_item_service_by_e_invite_id($e_invite_id);	#获取三级服务
						
						if(is_array($invite_item)){
							foreach (@$invite_item as $kk => $vv) {
								@$third_service_name[$value['id']][$kk] = $service_list[$vv['service_3_id']];
							}	
							$value['third_service'] = $third_service_name;
						}else{
							$value['third_service'] = '';
						}
						
						$invite_info_list[] = $value;
					}
					 $smarty -> assign('result',$invite_result);

				}else{
					$value['u_sex'] = $u_sex;
					$value['user_type'] = $user_type;
					$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
					$value['u_face'] = $u_face;
					$value['u_name'] = $user_info['nickname'];
					$e_invite_id = $value['id'];
					@$invite_item = $invite -> get_item_service_by_e_invite_id($e_invite_id);
					
					if(is_array($invite_item)){
						foreach (@$invite_item as $kk => $vv) {
							@$third_service_name[$value['id']][$kk] = $service_list[$vv['service_3_id']];
						}	
						$value['third_service'] = $third_service_name;
					}else{
						$value['third_service'] = '';
					}
					
					$invite_info_list[] = $value;
					if($s_help == 'all'){
						$smarty -> assign('sex','all'); 
					}else if($r_help == 'all'){
						$smarty -> assign('result','all');
					}
				}
			}
			if (empty($invite_info_list) || !isset($invite_info_list)) {
				$invite_info_list = '';
				$smarty -> assign('invite_info_list',$invite_info_list);
				$page_div = '';
                $smarty -> assign('page_div',$page_div);
			}else{
				
				  //分页
				  $invite_num = count($invite_info_list);#招募总数
				  $pagesize = 10;
				  $sum_page =  ceil($invite_num/$pagesize);	#分页总数
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
				  $show_invite_list = array_slice($invite_info_list, $from_rows, $pagesize);
				  //$show_apply_list = $apply -> get_apply_list_by_user($uid,$from_rows,$pagesize,$flash);
				  
				  
                                          
                                          
				$smarty -> assign('invite_info_list',$show_invite_list);	
			}
		}else{
			$invite_info_list = '';
			$page_div = '';
                            	$smarty -> assign('page_div',$page_div);
			$smarty -> assign('invite_info_list',$invite_info_list);		
		}


	}else{
		$list = '';
		$smarty -> assign('list',$list);	
		$page_div = '';
                     $smarty -> assign('page_div',$page_div);   
		$invite_info_list = '';
		$smarty -> assign('invite_info_list',$invite_info_list);
	}	
	
	$smarty -> assign('recruit_info',$recruit_info);
	$smarty -> assign('usertype',$usertype);
	$work_type = 'mywork_recruit'; 
	$smarty -> assign('work_type',$work_type);
	$smarty -> display("home/mywork_recruit_invite.html");