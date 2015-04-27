<?php
/**
 * 红演圈城市三级联动操作
 * 作者：zhaozhenhuan
 * 添加时间：2014-11-21
 *
 */ 
header("content-type: text/html; charset=utf-8");
require "../includes/common_inc.php";
//require COMMON_PATH."/base.class.php"; 
	$base = new base();
	if(isset($_REQUEST['pid'])){
		$pid =clear_gpq($_REQUEST['pid']);
		//获取市列表		
		$clist = $base -> get_city_list_by_province($pid);
		if($clist){
			echo json_encode($clist);
		}	
	}
	if(isset($_REQUEST['cid'])){
		$cid = clear_gpq($_REQUEST['cid']);
		//获取县区列表
		$cid = $_REQUEST['cid'];
		$dlist = $base -> get_district_list_by_city($cid);
		if($dlist){
			echo json_encode($dlist);
		}
	}
	if(!isset($_REQUEST['cid'])&&!isset($_REQUEST['pid'])){
		$plist = $base ->get_province_list();
		echo json_encode($plist);
	}


	