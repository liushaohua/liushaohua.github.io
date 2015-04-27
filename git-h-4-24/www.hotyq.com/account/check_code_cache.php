<?php
require_once('../includes/common_api_android_inc.php');

$type = @$_GET["type"];
$app_id = @$_GET["app_id"];
if(!in_array($type,array("forget_check_code"))){
	echo "type is err";
	exit;
}
if(empty($app_id)){
	echo "app_id is null";
	exit;
}
$key = $type."_".$app_id;
$key = md5($key);
$check_code_redis = new check_code_redis();
$check_code = $check_code_redis -> entry($key);

$smarty -> assign('code',$check_code);	
$smarty -> display("account/code.html");



?>