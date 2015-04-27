<?php
	header("Content-Type:text/html;charset=utf-8");
	require_once('../includes/common_inc.php');
	$action=clear_gpq($_REQUEST['action']);
	if($action=='create'){
		$name=clear_gpq($_REQUEST['name']);
		$parent_id=intval($_REQUEST['parent_id']);
		if($parent_id==0){
			$path='0';
		}else{
			$path='0,'.$parent_id;
		}
		$descr=clear_gpq($_REQUEST['descr']);
		add_role($name,$parent_id,$path,$descr);
	}else if($action=='modify'){
		$id=intval($_REQUEST['id']);
		$name=clear_gpq($_REQUEST['name']);
		$parent_id=intval($_REQUEST['parent_id']);
		if($parent_id==0){
			$path='0';
		}else{
			$path='0,'.$parent_id;
		}
		$descr=clear_gpq($_REQUEST['descr']);
		mod_role($id,$name,$parent_id,$path,$descr);
	}else if($action=='delete'){
		$id=intval($_REQUEST['id']);
		$sql1="SELECT id FROM hyq_role WHERE parent_id={$id}";
		$result1=$db_hyq_read->query($sql1);
		$list1=$db_hyq_read->fetch_result($result1);
		if($list1==null){
			del_role($id);
		}else{
			echo '<script charset="utf-8">
                    alert("此角色包含子角色，无法直接删除！");
                    window.location="role_list.php";
				 </script>';
		}
	}
	function add_role($name,$parent_id,$path,$descr){
		global $db_hyq_write;
		$sql="INSERT INTO hyq_role(id,name,parent_id,path,descr) VALUES(null,'{$name}',{$parent_id},'{$path}','{$descr}')";
		$result=$db_hyq_write->query($sql);
		if($result){
			echo '<script charset="utf-8">
                    alert("添加角色成功！返回角色列表页！");
                    window.location="role_list.php";
				 </script>';
		}
	}
	function mod_role($id,$name,$parent_id,$path,$descr){
		global $db_hyq_write;
		$sql="UPDATE hyq_role SET name='{$name}',parent_id={$parent_id},path='{$path}',descr='{$descr}' WHERE id={$id}";
		$result=$db_hyq_write->query($sql);
		if($result){
			echo '<script charset="utf-8">
					 	alert("修改角色成功！返回角色列表页！");
                    window.location="role_list.php";
            </script>';
		}
	
	}
	function del_role($id){
		global $db_hyq_write;
		$sql="DELETE FROM hyq_role WHERE id={$id}";
		$result=$db_hyq_write->query($sql);
		if($result){
			echo '<script charset="utf-8">
                    alert("删除成功！返回角色列表页");
                    window.location="role_list.php";
            </script>';
		}
	}
?>