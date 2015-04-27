<?php
	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_inc.php');
	require_once('../../common/userprofile.class.php');
	session_start();

	$userprofile = new userprofile();
	$first_tag = $userprofile -> get_user_tag_first();//个人用户标签一级分类
	foreach($first_tag as $k => $v){
		$parent_id = $v['id'];
		$second_tag[$k] = $userprofile -> get_user_tag_second($parent_id);//个人用户标签二级分类
	}
	
	
	//读取用户已存入标签
	//$uid = $_SESSION['id'];
	$uid = 7;
	$user_tag = $userprofile -> get_user_tag_by_uid($uid);
	if($user_tag == 1304){
		//echo '您还未设置标签！';
		$user_tag = '';
		$num = 0;		//自定义标签数
		$smarty -> assign('sys_tag_p','null');
		$smarty -> assign('self_tag_num',$num);
		$smarty -> assign('user_tag',$user_tag);
	}else{
		if($user_tag == 1305){
			echo $user_tag;
			exit('标签内容读取失败！');
		}else{
			foreach($user_tag as $k => $v){
				if($v['parent_id'] == -1){   //自定义标签					
					$self_tag[$v['id']] = $v['name'];
				}else{						//系统标签					
					$sys_tag[$v['id']] = $v['name']; 					
				}
			}
			if(empty($self_tag) || !isset($self_tag)){
				$num = 0;		//自定义标签数
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
			$user_tag = $self_tag + $sys_tag;
			$smarty -> assign('user_tag',$user_tag);
		}
	}
	
	
	
	$smarty -> assign('first_tag',$first_tag);
	$smarty -> assign('second_tag',$second_tag);
	$smarty -> display("account/userprofile.html");


	