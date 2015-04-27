<?php
	header("Content-type: text/html; charset=utf-8");
	require_once '../includes/common_inc.php';
	function list_role($parent_id){
		global $db_hyq_read;
		$sql = "SELECT id,name,parent_id,path,descr FROM hyq_role WHERE parent_id={$parent_id}";
		$rolelist=$db_hyq_read->fetch_result($db_hyq_read->query($sql));
		return $rolelist;
	}
	$rolelist=list_role(0);
	$count=count($rolelist);
	$sonlist=array();
	for($i=0;$i<$count;$i++){
		$id=$rolelist[$i]['id'];
		$rolelist[$i][$id]=list_role($id);
	}
	$smarty -> assign('rolelist',$rolelist);
	$smarty -> display("admin/role_list.tpl");
	