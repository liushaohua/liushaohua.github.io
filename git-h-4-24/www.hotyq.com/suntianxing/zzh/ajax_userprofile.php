<?php 
	session_start();
	header("Content-type:text/html;charset=utf-8");
	include "../../includes/common_inc.php";
	include "../../common/userprofile.class.php";
	$action = clear_gpq($_REQUEST['action']);
	if(empty($action)){
		echo 1099;
		exit;
	}  
	$userprofile = new userprofile();
	switch($action){		
	//更新uinfo表(第一次填写资料)红名片
	case "update_uinfo_red_card":
		$uid=$_COOKIE['uid'];
		$nickname = clear_gpq($_REQUEST['nickname']);
		$uinfo['hope_role'] = clear_gpq($_REQUEST['hope_role']);
		$uinfo['sex'] = clear_gpq($_REQUEST['sex']);
		$uinfo['province_id'] = clear_gpq($_REQUEST['pid']);
		$uinfo['city_id'] = clear_gpq($_REQUEST['cid']);
		$uinfo['district_id'] = clear_gpq($_REQUEST['did']);
		$uinfo['state'] = clear_gpq($_REQUEST['state']);

		//判断昵称
		if(!preg_match("/^(?!_)(?!.*?_$)[\x{4e00}-\x{9fa5}\w]+$/iu",$nickname)){	//判断nickname是否格式正确
			echo 1101;		
			exit;
		}
		if(empty($uinfo['sex'] )){
			echo 1102;
			exit;
		}
		if(empty($uinfo['province_id'])){
			echo 1103;
			exit;
		}
		if(empty($uinfo['city_id'])){
			echo 1104;
			exit;
		}
		if(empty($uinfo['district_id'])){
			echo 1105;
			exit;
		}
		
		if(empty($uinfo['state'])){
			echo 1106;
			exit;
		}
		if(empty($uinfo['hope_role'])){
			echo 1107;
			exit;
		}
		
		$re = $userprofile->update_user_profile($uid,$user_profile_array=array());
		if($re == 1000){
			echo 1000;
		}else{
			echo 1110;
		}  
		break;
	//更新用户详情(第二次修改资料)
	//age height weight ,bust,waist,hips star blood native_province_id,native_city_id,native_district_id,school,finish_year,specialty,education
	case "update_uinfo_red_date":
		$uid=$_COOKIE['uid'];
		$uinfo['age'] = clear_gpq($_REQUEST['age']);
		$uinfo['height'] = clear_gpq($_REQUEST['height']);
		$uinfo['weight'] = clear_gpq($_REQUEST['weight']);
		$uinfo['bust'] = clear_gpq($_REQUEST['bust']);
		$uinfo['waist'] = clear_gpq($_REQUEST['waist']);
		$uinfo['hips'] = clear_gpq($_REQUEST['hips']);
		$uinfo['star'] = clear_gpq($_REQUEST['star']);
		$uinfo['blood'] = clear_gpq($_REQUEST['blood']);
		$uinfo['native_province_id'] = clear_gpq($_REQUEST['native_province_id']);
		$uinfo['native_city_id'] = clear_gpq($_REQUEST['native_city_id']);
		$uinfo['native_district_id'] = clear_gpq($_REQUEST['native_district_id']);
		$uinfo['school'] = clear_gpq($_REQUEST['school']);
		$uinfo['finish_year'] = clear_gpq($_REQUEST['finish_year']);
		$uinfo['specialty'] = clear_gpq($_REQUEST['specialty']);
		$uinfo['education'] = clear_gpq($_REQUEST['education']);
		$uinfo['in_org'] = clear_gpq($_REQUEST['in_org']);
		if(!preg_match("/^(?!_)(?!.*?_$)[\x{4e00}-\x{9fa5}\w]+$/iu",$uinfo['school'])){
			echo 1131;		//学校错误
			exit;
		}
		if(!preg_match("/^(?!_)(?!.*?_$)[\x{4e00}-\x{9fa5}\w]+$/iu",$uinfo['specialty'])){
			echo 1132;		
			exit;
		}
		if(!preg_match("/^(?!_)(?!.*?_$)[\x{4e00}-\x{9fa5}\w]+$/iu",$uinfo['in_org'])){
			echo 1133;		
			exit;
		}
		
		$re = $userprofile->update_user_info($uid,$uinfo);
		if($re == 1000){
			echo 1000;
		}else{
			echo 1110;
		}  
		break;
		//发送绑定手机的短信
		case "get_binding_mobile_code":
			$mobile = clear_gpq($_REQUEST['mobile']);
			if(empty($mobile)){
				echo 1016;
				exit;
			}
			$state_code = $user -> check_mobile($mobile);
			if($state_code !== 1000){
				echo $state_code;
				exit;
			}
			echo $userprofile -> get_binding_mobile_code($mobile);
			break;
		//检测手机绑定码是否成功	
		case 'check_binding_mobile_code':
			if(empty($check_code)){
				echo 1007;
				exit;
			}
			if(strlen($check_code)!== 6){
				echo 1007;
				exit;
			}
			$state_code =$userprofile->check_binding_mobile_code($uid,$mobile,$check_code);
			if($state_code == 1000){ 
				echo 1000;  //绑定成功
			}else{
				echo 1114;  //绑定失败
			}	
			break;
		//发送邮件激活	
		case 'send_active_email':
			$userid = clear_gpq($_REQUEST['userid']);
			$state_code = $userprofile->send_active_email($userid);
			if($state_code == 1000){
				echo 1000;
			}else{
				echo $state_code;
				exit;			
			}	
			break;	
	case 'binding_identity_card';
		if(empty($identity_card)){
			echo 1007;
			exit;
		}
		$state_code =$userprofile-> check_identity_card($identity_card);
		if($state_code !== 1000){			
			return 1116;//身份证格式不正确!
			exit;
		}
		$state_code =$userprofile-> identity_card_exist($identity_card);
		if($state_code !== 1000){			
			return 1117;//身份证已经绑定过！
			exit;
		}
		$state_code =$userprofile-> binding_identity_card($uid,$identity_card);
		if($state_code){
			return 1000;//绑定成功
		}else{
			return 1122;//身份证绑定失败
		}
		break;
	}
?>





