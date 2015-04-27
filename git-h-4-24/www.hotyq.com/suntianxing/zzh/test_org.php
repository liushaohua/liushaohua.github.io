<?php
	require_once ('../../includes/common_inc.php');
	echo '测试';
	require_once COMMON_PATH.'/userprofile.class.php';
	require_once COMMON_PATH.'/orgprofile.class.php';	
	$userprofile = new userprofile();
	$user = new user();
	$orgprofile = new orgprofile();	
	//$uid = 45582;
	//$user_type = 'org';
	//$user ->get_user_address($uid,$user_type);
	echo '<hr>';
	

echo 	'login_type = mobile;	---------------------------------';
	//手机用户绑定邮箱
	// if(empty($email)){
		// echo 1002;
		// exit;
	// }	
	// $state_code = $user -> check_email($email);
	// if($state_code !== 1000){
		// echo $state_code;
		// exit;
	// }
	// $state_code = $user -> email_exist($email);
	// if($state_code == 1000){
		// echo 1010;
		// exit;	
	// }	
//	$re = $user -> mobile_send_binding_email(45581,'84439571@qq.com');
//	var_dump($re);


echo 	'login_type=email;	--------------------------';
	//邮箱用户绑定手机 #发送短信
	// $mobile = '18513200411';
	// if(empty($mobile)){
		// echo 1016;
		// exit;
	// }
	// $state_code = $user -> check_mobile($mobile);
	// if($state_code !== 1000){
		// echo $state_code;
		// exit;
	// }
	// echo $user -> get_reg_mobile_code($mobile);
	// echo '<hr>';
	
// 收到验证码开始绑定邮箱用户的手机	
	// $check_code = '457667';
	// $mobile = '18513200411';
	// $uid=45582;
	// $state_code = $user -> check_mobile($mobile);
	// if($state_code !== 1000){
		// echo $state_code;
		// exit;	
	// }
	// if(empty($check_code)){
		// echo 1007;
		// exit;
	// }
	// if(strlen($check_code)!== 6){
		// echo 1007;
		// exit;
	// }
// echo 	$state_code =$user->email_check_binding_mobile($uid,$mobile,$check_code);
//邮箱用户激活邮箱

	// $userid = 45588;
	// $state_code = $user->send_active_email($userid);
	// if($state_code == 1000){
		// echo 1000;
	// }else{
		// echo $state_code;
		// exit;			
	// }		

echo '绑定身份证-----------------------------------------';
// 匹配：
// 34052419800229001X
// 340524800229001
// 340524199001010013
// 不匹配：
// 34052419800101001a
// 21552418801010011
// 340524850229001
// 34052422800110081X
// 34052419800229001x
	//case 'binding_identity_card';
	// $uid = '777';
	// $identity_card = '132003198805263520';
	// if(empty($identity_card)){
	// 	echo 1007;
	// 	exit;
	// }
	// $state_code =$userprofile-> check_identity_card($identity_card);
	// if($state_code !== 1000){			
	// 	echo 1116;//身份证格式不正确!
	// 	exit;
	// }
	// $state_code =$userprofile-> identity_card_exist($identity_card);
	// if($state_code !== 1000){			
	// 	echo 1117;//身份证已经绑定过！
	// 	exit;
	// }
	// $state_code =$userprofile-> binding_identity_card($uid,$identity_card);
	// if($state_code){
	// 	echo 1000;//绑定成功
	// }else{
	// 	echo 1122;//身份证绑定失败
	// }
echo "<hr>";
echo '工商号码绑定-org';
	$uid = '444';
	$business_num = '62345612345671';
		if(empty($business_num)){
		echo 1119;
		exit;
		}
		$state_code =$orgprofile-> check_business_num($business_num);
		if($state_code !== 1000){		
			echo '工商号格式不正确!';
			return 1120;//工商号格式不正确!
			exit;
		}
		$state_code =$orgprofile-> business_num_exist($business_num);
		if($state_code == 1000){	
			echo '工商号已经绑定过！';
			return 1121;//工商号已经绑定过！
			exit;
		}
		 $state_code =$orgprofile-> banding_business_num($uid,$business_num);
		if($state_code == 1000 ){
			var_dump($state_code);
			echo '绑定成功！';
			return 1000;//绑定成功
		}else{
			echo '工商号绑定失败！';
			return 1123;//工商号绑定失败
		}
?>