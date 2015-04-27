<?php 
	session_start();
	header("Content-type:text/html;charset=utf-8"); 
	include "../../includes/common_inc.php";
	require('orgprofile.class.php');
	$action = clear_gpq($_REQUEST['action']);
	if(empty($action)){
		echo 1099;
		exit;
	}
	$orgprofile = new orgprofile();
	switch($action){
		
	//更新orginfo表(第一次填写机构)红名片
	case "update_orginfo_red_card":
		$uid=$_SESSION['uid'];
		$nickname = clear_gpq($_REQUEST['nickname']);
		$orginfo['create_time'] = clear_gpq($_REQUEST['create_time']);
		$orginfo['type'] = clear_gpq($_REQUEST['type']);
		$orginfo['province_id'] = clear_gpq($_REQUEST['pid']);
		$orginfo['city_id'] = clear_gpq($_REQUEST['cid']);
		$orginfo['district_id'] = clear_gpq($_REQUEST['did']);
		$orginfo['state'] = clear_gpq($_REQUEST['state']);
		$orginfo['legal_person'] = clear_gpq($_REQUEST['legal_person']);
		//判断昵称
		if(!preg_match("/^(?!_)(?!.*?_$)[\x{4e00}-\x{9fa5}\w]+$/iu",$nickname)){
			echo 1101;		
			exit;
		}
		if(empty($orginfo['create_time'] )){
			echo 1102;
			exit;
		}
		if(empty($orginfo['type'] )){
			echo 1102;
			exit;
		}		
		if(empty($orginfo['province_id'])){
			echo 1103;
			exit;
		}
		if(empty($orginfo['city_id'])){
			echo 1104;
			exit;
		}
		if(empty($orginfo['district_id'])){
			echo 1105;
			exit;
		}
		
		if(empty($orginfo['state'])){
			echo 1106;
			exit;
		}
		if(empty($orginfo['legal_person'])){
			echo 1107;
			exit;
		}
		
		$re = $orgprofile->update_org_info($uid,$nickname,$orginfo);
		if($re == 1000){
			echo 1000;
		}else{
			echo 1110;
		}  
		break;
	case "update_orginfo_red_date":
		$uid=$_COOKIE['uid'];
		$orginfo['introduce'] = clear_gpq($_REQUEST['introduce']);
		$orginfo['production'] = clear_gpq($_REQUEST['production']);
		$orginfo['honor'] = clear_gpq($_REQUEST['honor']);
		
		if(!preg_match("/^(?!_)(?!.*?_$)[\x{4e00}-\x{9fa5}\w]+$/iu",$orginfo['introduce'])){
			echo 1134;		
			exit;
		}
		if(!preg_match("/^(?!_)(?!.*?_$)[\x{4e00}-\x{9fa5}\w]+$/iu",$orginfo['production'])){
			echo 1135;		
			exit;
		}
		if(!preg_match("/^(?!_)(?!.*?_$)[\x{4e00}-\x{9fa5}\w]+$/iu",$orginfo['honor'])){
			echo 1136;		
			exit;
		}				
		
		$re = $orgprofile->update_org_info($uid,$orginfo);
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
		case 'banding_business_num';	
			if(empty($business_num)){
				echo 1119;
				exit;
			}
			$state_code =$userprofile-> check_business_num($business_num);
			if($state_code !== 1000){			
				return 1120;//工商号格式不正确!
				exit;
			}
			$state_code =$userprofile-> business_num_exist($business_num);
			if($state_code !== 1000){			
				return 1121;//工商号已经绑定过！
				exit;
			}
			$state_code =$userprofile-> banding_business_num($uid,$business_num);
			if($state_code){
				return 1000;//绑定成功
			}else{
				return 1123;//工商号绑定失败
			}
			break;		
			
	}