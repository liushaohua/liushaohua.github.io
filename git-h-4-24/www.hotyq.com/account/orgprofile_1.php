<?php
	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_inc.php');
	require_once('../../common/orgprofile.class.php');
	session_start();

	$orgprofile = new orgprofile;
	$r = $orgprofile -> get_org_tag_list();
	//echo 12321;
	if($r == 1311){
		echo $r;
		exit;
	}else{
		foreach($r as $k => $v){
			$org_tag_list[$v['id']] = $v['name'];
		}
	}
	
	//读取机构用户已存入标签
	//$uid = $_SESSION['id'];
	$uid = 5;
	$org_tag = $orgprofile -> get_org_tag_by_uid($uid);
	if($org_tag == 1314){									//未设置标签！
		$org_tag = '';
		$num = 0;									//自定义标签数
		$smarty -> assign('sys_tag_p','null');
		$smarty -> assign('self_tag_num',$num);
		$smarty -> assign('org_tag',$org_tag);
	}else{
		if($org_tag == 1315){
			echo '机构标签读取失败！';
		}else{
			foreach($org_tag as $k => $v){
				if($v['parent_id'] == -1){   		//自定义标签					
					$self_tag[$v['id']] = $v['name'];
				}else{								//系统标签					
					$sys_tag[$v['id']] = $v['name']; 					
				}
			}
			if(empty($self_tag) || !isset($self_tag)){
				$num = 0;							//自定义标签数
				$self_tag =array();
			}else{
				$num = count($self_tag);
			}
			if(empty($sys_tag) || !isset($sys_tag)){
				$sys_tag =array();
			}
					
			$smarty -> assign('self_tag_num',$num);
			$smarty -> assign('sys_tag_p',$sys_tag);
			$smarty -> assign('self_tag',$self_tag);
			$org_tag = $self_tag + $sys_tag;
			$smarty -> assign('org_tag',$org_tag);
		}
	}
	
	
	//var_dump($org_tag); 
	$smarty -> assign('org_tag_list',$org_tag_list);
	$smarty -> display('account/orgprofile.html');
