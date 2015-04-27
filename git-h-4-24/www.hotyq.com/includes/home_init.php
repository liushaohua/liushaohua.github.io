<?php
/*
 * 个人中心页面初始化
 * 包括：登录验证、url地址验证、初始化公共变量
 * 公共变量：$user_info
*/
$user = new user();
$user_cookie = $user -> get_cookie_user_info();
$url = urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
//判断uid和token是否存在
if(!$user_cookie || empty($user_cookie["userid"]) || empty($user_cookie["user_token"])){
	//header("location:/account/login");
	echo "<script>window.top.document.location.href='/account/login?url=".$url."'</script>";
	exit;
}
//验证用户登陆是否有效
//var_dump( $user_cookie);exit;
$user_info = $user -> is_login($user_cookie["userid"], $user_cookie["user_token"]);
if(!$user_info){
	//header("location:/account/login");
	echo "<script>window.top.document.location.href='/account/login?url=".$url."'</script>";
	exit;
}
$PAGE_TYPE = @$PAGE_TYPE;
//验证页面url地址是否正确
if($PAGE_TYPE != 'ajax_page'){
	if(empty($user_info["nickname"])){
		if($user_info["user_type"]."_card" !== $PAGE_TYPE){
			//header("location:/home/{$user_info['user_type']}/card");
			echo "<script>window.top.document.location.href='/home/{$user_info['user_type']}/card'</script>";
			exit;
		}
	}else{
		if(isset($PAGE_TYPE) && $user_info["user_type"] !== $PAGE_TYPE){
			//header("location:/home/{$user_info["user_type"]}");
			echo "<script>window.top.document.location.href='/home/{$user_info["user_type"]}'</script>";
			exit;
		}
	}
}

//临时添加，解决老用户没有user_msg_total数据问题
$user_msg_total = new user_msg_total();
$unread_mes_num = $user_msg_total -> get_user_msg_total_info($user_info["id"]);
if(!$unread_mes_num){
	$user_msg_total -> add_user_msg_total($user_info["id"]);
}

?>