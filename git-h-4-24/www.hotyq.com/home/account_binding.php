<?php

	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once('../includes/common_home_inc.php');
	require_once ('../../common/orgprofile.class.php');	


	$user = new user();
	$orgprofile = new orgprofile();
	$orginfo = $orgprofile -> get_org_profile($user_info["id"]);
	$legal_person = $orginfo['legal_person'];

	$user_type = $user_info["user_type"];
	$login_type = $user_info['login_type'];
	$email_status = $user_info['email_status'];
	$mobile_status = $user_info['mobile_status'];
	$identity_card_status = $user_info['identity_card_status'];
	$business_card_status = $user_info['business_card_status'];
	$smarty -> assign('user_type',$user_type);
	$smarty -> assign('login_type',$login_type);
	$smarty -> assign('legal_person',$legal_person);	
	$smarty -> assign('email_status',$email_status);	
	$smarty -> assign('mobile_status',$mobile_status);	
	$smarty -> assign('identity_card_status',$identity_card_status);	
	$smarty -> assign('business_card_status',$business_card_status);
	//var_dump($user_info);
		
    //	$smarty -> assign('business_card_status',$business_card_status);
    $account_update = 'auth';		
	$smarty -> assign('account_update',$account_update);
	$smarty -> display("home/account_binding.html");

?>
