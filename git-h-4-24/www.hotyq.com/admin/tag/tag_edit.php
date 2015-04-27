<?php
	header("Content-type: text/html; charset=utf-8");
	require_once('../includes/common_inc.php');
	$action=clear_gpq($_GET['action']);
	if($action=='create'){
		$parent_id=intval($_GET['parent_id']);
	}else if($action=='modify'){
		$id=intval($_GET['id']);
	}
	if($action=='create'){
		$sql1 = "SELECT id,name,parent_id,path,descr FROM hyq_role WHERE id={$parent_id}";
		$parent_role=$db_hyq_read->fetch_array($db_hyq_read->query($sql1));
		$parent_name=$parent_role['name'];
	}else if($action=='modify' && !$id==null){
		$sql2="SELECT name,parent_id,descr FROM hyq_role WHERE id={$id}";
		$arr=$db_hyq_read->fetch_array($db_hyq_read->query($sql2));
		$name=$arr['name'];
		$parent_id=$arr['parent_id'];
		$descr=$arr['descr'];
		if($parent_id==0){
			$parent_name="none";
		}else{
			$sql3="SELECT name,parent_id,descr FROM hyq_role WHERE id={$parent_id}";
				$arr3=$db_hyq_read->fetch_array($db_hyq_read->query($sql3));
				$parent_name=$arr3['name'];
		}
		$smarty -> assign('id',$id);
		$smarty -> assign('name',$name);
		$smarty -> assign('descr',$descr);
	}
	$smarty -> assign('action',$action);
	$smarty -> assign('parent_id',$parent_id);
	$smarty -> assign('parent_name',$parent_name);
	$smarty -> display("admin/role_edit.tpl");
?>