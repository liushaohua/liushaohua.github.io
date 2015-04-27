<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
//	require_once ('../includes/common_home_inc.php');
	require_once('../includes/common_home_inc.php');	
	require_once ("../../common/invite.class.php");	
	//require_once "../../common/userprofile.class.php";	
	//require_once ('../../common/recruit.class.php');
	$base = new base();
	$user = new user();
	$userprofile = new userprofile();
	$orgprofile =new orgprofile();
	$invite = new invite();	
	$recruit = new recruit();
	
	//var_dump($cookie);
	$uid = $user_info["id"];
	$usertype = $user_info['user_type'];
	$service_list = $base -> get_service_list($flash);	#所有服务名字;
	
	$invite_list = $invite -> get_invite_list_by_user($uid);
	
	//分页
        /*   $data['total_rows'] = count($invite_list);
          $page = '';
          if($data['total_rows'] > 0){
                	//1 根据当前页p 先显示每次的分页
                	$data['list_rows'] = 1;
                	$page = new page($data);
                	$page_div = $page -> show_2();
                	
                	//2  再根据当前分页p  显示数据
                	$total_page = ceil($data['total_rows']/$data['list_rows']);
                	$_REQUEST['page'] = (isset($_REQUEST['page']) && $_REQUEST['page']>0) ? intval($_REQUEST['page']) : 1;
                	$_REQUEST['page'] = ($_REQUEST['page'] > $total_page) ? $total_page : intval($_REQUEST['page']);
                
                	$from_rows = ($_REQUEST['page']-1) * $data['list_rows'];
                	
                	$show_invite_list = $invite -> get_invite_list_by_user($uid, $from_rows, $data['list_rows']);
 
               		$smarty -> assign('page_div',$page_div);    
		}else{
                     $page_div = '';
                     $smarty -> assign('page_div',$page_div);    
    	} */
    	
    	
    	
    	//分页
    	$invite_num = count($invite_list);#招募总数
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
    	$show_invite_list = $invite -> get_invite_list_by_user($uid,$from_rows,$pagesize,$flash);
    	
    	
    	
		
	if($show_invite_list){
		foreach($show_invite_list as $k => $v){
			$rid = $v['recruit_id'];
			$recruit_info = $recruit -> get_recruit_info($rid, $flash);	#根据招募id获取招募详情
			if($recruit_info){
				
				$uid = $recruit_info['uid'];
				$show_invite_list[$k]['recruit_name'] = $recruit_info['name'];
				$show_invite_list[$k]['status'] = $recruit_info['status'];

				$e_service_recruit = $invite -> get_service_recruit($v['e_service_id']);	
				$second_service = $service_list[$e_service_recruit['service_2_id']];	#获取服务详情

				$show_invite_list[$k]['second_service'] = $second_service;
				$third_service_arr = $invite -> get_item_service_by_e_invite_id($v['id']); #获取报名的三级服务 ***
				if(is_array($third_service_arr)){
					foreach ($third_service_arr as $kk => $vv) {
						$service_id  = $vv['service_3_id'];
						//$service_info = $base -> get_service_info($service_id,$flash = 0);
						@$show_invite_list[$k]['third_service'] .= ($kk + 1).'.'.$service_list[$service_id].'  ';
					}
					$show_invite_list[$k]['third_service'] = trim($show_invite_list[$k]['third_service'],'  ');
				}
				$show_invite_list[$k]['third_service_num'] = count($third_service_arr);
				
				$user_info = $user -> get_userinfo($uid, $flash);	#获取招募发布者的信息
				$show_invite_list[$k]['user_type'] = $user_info['user_type'];
				$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
				$show_invite_list[$k]['u_face'] = $u_face;
				$show_invite_list[$k]['r_uid'] = $user_info['id'];
				$show_invite_list[$k]['nickname'] = $user_info['nickname'];		
			}
		
		}
		
		$service_list = $base -> get_service_list();
		
		$smarty -> assign('list',$show_invite_list);	
	}else{
		$show_invite_list = '';
		$smarty -> assign('list',$show_invite_list);
	}
 
	
	$user_work_type = $usertype;
	$smarty -> assign('user_work_type',$user_work_type);		
	$work_type = 'mywork_invite'; 
	$smarty -> assign('work_type',$work_type);	
	$smarty -> display("home/mywork_invite.html");
