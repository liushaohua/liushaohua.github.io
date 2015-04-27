<?php
header("Content-type: text/html; charset=utf-8");
require_once("../../includes/common_inc.php");
require_once('./identity.class.php');

$identity = new identity('1302031982061100','王屹');
if($identity -> get_identity_info()){
	echo '该身份证已验证过';
}else{
	$result = $identity -> verify_identity();
	var_dump($result);
	$res = array();
	function searchKey($array){
		global $res;
	    foreach($array as $key=>$row){
	        if(!is_array($row)){
	        	if($row == '一致'){
	        		$res[] = $row;
	        	}
	        }else{
	           searchKey($row);
	        }
	    }
	    return $res;
	}
	$res = searchKey($result);
	$count = count($res);
	if($count == 2 && $res[0] == "一致" && $res[1] == '一致'){
		$userid = '45803';
		$identity_num = $result['ROW']['INPUT'][0]['gmsfhm'][0]['#text'];
		$identity_name = $result['ROW']['INPUT'][0]['xm'][0]['#text'];
		$identity -> add_verified_identity($userid,$identity_num,$identity_name);
	}
	var_dump($res);		

}
?>