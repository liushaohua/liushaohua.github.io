<?php
	header("content-type: text/html; charset=utf-8");
	require('../includes/common_inc.php');

	require('../../common/orgprofile.class.php');
	require('../../common/userprofile.class.php');
	session_start();
	// $user_info =new user();
	// $uid = 45025;
	// $user_type = '机构';
	// $login_type = 'email';

	// $account = '13888888888@qq.com';
/* 	$user = new user();
	$userprofile = new userprofile();
	$orgprofile = new orgprofile();
	$uid = 888;
	$identity_card = '231003198902283520';
 */
	$base = new base();
	$p = $base -> get_province_list();
	$c = $base ->get_city_list();
	$d = $base ->get_district_list();
	//echo '<pre>';
	$pp = json_encode($p);
	$cc = json_encode($c);
	$dd = json_encode($d);
	
	echo '省列表';
	echo '<hr>';
	var_dump($p);
	echo '<hr>';
	echo 'json省列表';
	echo '<hr>';
	var_dump($pp);
	echo '<hr>';
	
	
	echo '市列表';
	echo '<hr>';	
	var_dump($c);
	echo '<hr>';
	echo 'json市列表';
	echo '<hr>';
	var_dump($cc);	
	echo '<hr>';	
	
	echo '区列表';
	echo '<hr>';
	var_dump($d);
	echo '<hr>';
	echo 'json区列表';
	echo '<hr>';
	var_dump($dd);	

	// $userr = $user ->add_reg_info($uid,$user_type,$login_type,$account);
	// echo $userr;
	// if($userr == 1000){
		// echo 'yes';
	// }else{
		// echo 'no';
	// }
	// $account='zzh6113@126.com';
	// $login_type='email';
	// $user_info = $user_info -> get_userinfo_by_account($account,$login_type);
	// $_SESSION['hyq_user_info']['userid'] = $user_info['id'];
	// $_SESSION['hyq_user_info']['usernick'] = $user_info['nickname'];
	// $_SESSION['hyq_user_info']['user_type'] = $user_info['user_type'];
	// if( isset($_REQUEST['remeber_login']) ){
		// setcookie ( "hyq_user_info" ,  "{$user_info['id']}|{$user_info['user_type']}|{$user_info['nickname']}|{$user_info['level']}|{$user_info['data_percent']}" , time()+24*3600*365,'/','hotyq.com' );
	// }else{
	//	cookie   hyq_user_info={userid}|{user_type}|{usernick}|{level}|{data_percent}|{hyq_cipher}
		// setcookie ( "hyq_user_info" ,  "{$user_info['id']}|{$user_info['user_type']}|{$user_info['nickname']}|{$user_info['level']}|{$user_info['data_percent']}" , time()+24*3600,'/','hotyq.com' );
	// }
	// var_dump($_SESSION['hyq_user_info']);
	// $user = new user();
	// $user ->delete_client_user_info();
	// $re= $user -> add_mobile_user('机构',18513200411,123456,'572214','web');
	//$user -> update_client_user_info($result);
	// if($re){
		// echo '成功';
		// var_dump($re);
	// }else{
		// echo '失败';
		// echo $re;
	// }
		// echo '<hr>';
			// echo '<hr>';
			// $user_info['id'] = 22;
			// $user_info['user_type'] = '个人';
			// $user_info['nickname'] = '';
			// $user_info['salt'] = 123123123;
			// $user_info['level'] = 1;
			// $user_info['data_percent'] = 5;			
			// $user -> update_client_user_info($user_info);
	/*var_dump($_SESSION);
	echo '<hr>';
	var_dump($_COOKIE);
	echo '<hr>';
	var_dump($_COOKIE['hyq_user_info']);
    echo '<hr>';
	$re = $_COOKIE['hyq_user_info'];
	// $fruits = array('apple', 'banana', 'pear');
	// $str = implode(", ", $fruits);
	// echo $str;
	echo '<pre>';
	
	$email_info = explode("|", $re); 
	
	echo $email_info[0];
	
	echo '<pre>';
	$userid = 45322;
	$sql = "SELECT email FROM hyq_user WHERE id={$userid}";
	$email = $db_hyq_read->fetch_result($db_hyq_read->query($sql));
	var_dump($email);
	echo $email[0]['email'];
		echo '<pre>';
	if($email){
		echo 'true';
	}else{
		echo 'flase';
	}
	
	echo '<hr>';
	$sql = "SELECT email FROM hyq_user WHERE id={$userid}";
	$email = $db_hyq_read->query($sql); //不能进行判断
	var_dump($email);
	if($email){
		echo 'true';
	}else{
		echo 'flase';
	}
	*/
	//$photo = new photo();
	//$photo -> save_on_upyun('/user/72/453/45372/286546c5174e83f8.jpg');
	//echo $err = $photo -> delete_photo_upyun('/user/72/453/45372/286546c5174e83f8.jpg');
	//$file = curl_get("http://img.hotyq.com/user/43/454/45443/780546eaaf4e19ed.jpg");
	//$fp = file_put_contents($IMG_WWW.'/a11.jpg',$file);  
	// $ss= new orgprofile();
	// $uid = 13;
	// $orginfo['nickname'] = 'wangyifan';
	// $orginfo['province_id'] = '55';
	// $result = $ss ->update_uinfo($uid,$orginfo);
	// if($result){
		// echo 'yes';
	// }else{
		// echo 'no';
		
	// }
//	 $uid = 7;$user_type = 'user';
	// function get_user_address($uid,$user_type){
		// global $db_hyq_read;
		// $sql .= "SELECT province_id,city_id,district_id FROM "; 
		// if($user_type == "user"){
			// $sql .= "hyq_user_profile ";
		// }else if($user_type == "org"){	
			// $sql .= "hyq_org_profile ";
		// }
		// $sql .=" WHERE uid = '{$uid}'";
		// $result = $db_hyq_read -> fetch_array($db_hyq_read -> query($sql));		
		// var_dump($result);
	// }
//	$user -> get_user_address($uid,$user_type);
//	echo '<hr>';
	//$user -> get_province_info(12);
	// $re =  $orgprofile -> get_org_profile(888); 
	// var_dump($re);
	// echo '<hr>';
	// $rel = $userprofile -> get_user_profile(777);
	// var_dump($rel);

	
	
	
	
	
	