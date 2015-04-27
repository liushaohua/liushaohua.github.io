<?php
require_once('../includes/common_api_android_inc.php');

// $type = @$_GET["type"];
// $app_id = @$_GET["app_id"];
// if(!in_array($type,array("forget_check_code"))){
	// echo "type is err";
	// exit;
// }
// if(empty($app_id)){
	// echo "app_id is null";
	// exit;
// }
// $key = $type."_".$app_id;
// $key = md5($key);
// $redis = new redis_class();
// echo $redis -> get($key);
// exit;

	//$data = array("action" => "user_login","account" => "13718983129", "password" => "222222","login_type"=>"mobile");
	//$data = array("action" => "forget1_get_check_code");
	$data = array("action" => "upload_icon","uid" => "45618","app_token" => "11111111111111");
	//$data = array("action" => "forget2_get_user_info",'account'=>'972899545@qq.com','identify_code'=>'vch44');
	//$data = array("action" => "forget3_send_forget_code",'account'=>'972899545@qq.com','account'=>'email');
	//$data = array("action" => "forget4_check_forget_code",'uid'=>'45393','forget_code'=>'418219');
	//$data = array("action" => "forget5_reset_password",'uid'=>'45393','forget_code'=>'418219','new_password'=>'333333');
	
	//$data = array("action" => "forget2_get_user_info",'account'=>'13718983129','identify_code'=>'111111');
	//$data = array("action" => "forget3_send_forget_code",'account'=>'13718983129','account_type'=>'mobile');
	//$data = array("action" => "forget4_check_forget_code",'uid'=>'45225','forget_code'=>'911134');
	//$data = array("action" => "forget5_reset_password",'uid'=>'45225','forget_code'=>'911134','new_password'=>'222222');
		
	$data_string = json_encode($data);
	//$ch = curl_init('/api/api.php');
	$ch = curl_init('/api/android/010101/api.php');
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($data_string))
	);
	
	$result = curl_exec($ch);
	echo $result;
	echo '<hr>';
	$result = json_decode($result);
	var_dump($result);
	//var_dump($_SESSION);
	
?>
