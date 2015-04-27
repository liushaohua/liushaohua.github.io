<?php
	header("Content-type:text/html; charset=utf-8");
	require_once("../includes/common_inc.php");
	session_start();
	//用户来源
	if(isset($_REQUEST['sns_type'])){
		$sns_type = clear_gpq($_REQUEST['sns_type']);
	}else{
		$sns_type = 'qq';
	}
	//回调地址
	define('HOTYQ_SNS_CALLBACK_URL','http://www.hotyq.com/account/login_sns.php?');
	//qq登录
	if($sns_type == "qq"){
		$app_id = "101159849";
		$app_key = "5cf1b31639a6cd3d59ca189e35a3a37b";
		//成功授权返回地址
		$succeed_url = HOTYQ_SNS_CALLBACK_URL."sns_type=qq";
		//获取随机授权码地址
		define('GET_AUTH_CODE_URL', "https://graph.qq.com/oauth2.0/authorize");
		//获取access_token地址
		define('GET_ACCESS_TOKEN_URL', "https://graph.qq.com/oauth2.0/token");
		//获取open_id地址
		define('GET_OPENID_URL',"https://graph.qq.com/oauth2.0/me");
		//获取Authorization Code(授权码)
		if(isset($_REQUEST['code'])){
			$code = clear_gpq($_REQUEST['code']);
		}else{
			$code = '';
		}
		//权限范围
		$scope = "get_user_info";
		if(empty($code)){
			//state参数用于防止CSRF攻击，成功授权后回调时会原样带回
			$_SESSION['state'] = md5(uniqid(rand(), TRUE));      
			$code_url = GET_AUTH_CODE_URL."?response_type=code&client_id=" 
			.$app_id."&redirect_uri=".urlencode($succeed_url)."&state=".$_SESSION['state']."&scope=".$scope;
			echo "<script> top.location.href='" . $code_url . "'</script>";
			exit;
		}
		//获取access_token
		if(isset($_REQUEST['state'])){
			$state = clear_gpq($_REQUEST['state']);
		}else{
			$state = '';
		}
		if($state == $_SESSION['state']){
			$token_url = GET_ACCESS_TOKEN_URL."?grant_type=authorization_code&"
			."client_id=".$app_id."&redirect_uri=".urlencode($succeed_url)."&client_secret=".$app_key."&code=".$code;
			$ch = curl_init($token_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			// 抓取URL并把它传递给浏览器
			$response = curl_exec($ch);
			// 关闭cURL资源，并且释放系统资源
			curl_close($ch);
			if (strpos($response, "callback") !== false){
				$lpos = strpos($response, "(");
				$rpos = strrpos($response, ")");
				$response  = substr($response, $lpos + 1, $rpos - $lpos -1);
				$msg = json_decode($response);
				if (isset($msg -> error)){
					//echo "<h3>error:</h3>" . $msg->error;
					//echo "<h3>msg  :</h3>" . $msg->error_description;
					error_tips(1028);//QQ登录授权失败
					exit;
				}
			}
			//Step3：使用Access Token来获取用户的OpenID
			$params = array();
			parse_str($response, $params);
			$graph_url = GET_OPENID_URL."?access_token=" 
			.$params['access_token'];
			$ch = curl_init($graph_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			// 抓取URL并把它传递给浏览器
			$str = curl_exec($ch);
			// 关闭cURL资源，并且释放系统资源
			curl_close($ch);
			if (strpos($str, "callback") !== false){
				$lpos = strpos($str, "(");
				$rpos = strrpos($str, ")");
				$str  = substr($str, $lpos + 1, $rpos - $lpos -1);
			}
			$user = json_decode($str);	
			if (isset($user->error)){
				echo "<h3>error:</h3>" . $user->error;
				echo "<h3>msg  :</h3>" . $user->error_description;
				exit;
			}
			$_SESSION['access_token'] = $params['access_token'];
			$access_token = $_SESSION['access_token'];
			//qq用户名id
			$openid = $user -> openid;
			$url =  "https://graph.qq.com/user/get_user_info?access_token=".$access_token."&oauth_consumer_key=".$app_id."&openid=".$openid;
			// 创建一个cURL资源
			$ch = curl_init($url); 	//CURLOPT_RETURNTRANSFER将curl_exec()获取的信息以文件流的形式返回，而不是直接输出
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			// 抓取URL并把它传递给浏览器
			$qq_user_info = json_decode(curl_exec($ch),true);
			// 关闭cURL资源，并且释放系统资源
			curl_close($ch);	
		}else{
			error_tips(1099);
			exit;
		}
		if($qq_user_info){
			$sns_username = $qq_user_info['nickname'];
			$face = $qq_user_info['figureurl_qq_2'];
			$openid_qq = $openid;
			$user_handle = new user();
			$result = $user_handle -> get_userinfo_by_sns($openid_qq,$sns_type);
			if($result){
				$user_handle -> update_cookie_user_info($result);
				if(empty($result['nickname'])){
					header("location:/home/".$result['user_type']."/card");
				}else{
					header("location:http://www.hotyq.com");
				}
			}else{
				$_SESSION['sns_username'] = $sns_username;
				$_SESSION['sns_type'] = $sns_type;
				$_SESSION['sns_face'] = $face;
				$_SESSION['sns_openid'] = $openid;
				header("location:/account/binding.php?sns_username=".urlencode($sns_username));
			}
		}else{
			//error_tips(1099);
			//exit;
		}
	}elseif($sns_type == "weibo"){
		include_once("./API/saetv2.ex.class.php" );
		define( "WB_AKEY" , '456538100' );
		define( "WB_SKEY" , 'e1e504dd44402140b85bf8594da0bf46' );
		define( "WB_CALLBACK_URL",HOTYQ_SNS_CALLBACK_URL."sns_type=weibo");
		$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
		if (isset($_REQUEST['code'])) {
			//判断权限认证的code是否存在
			$keys = array();
			// 验证state，防止伪造请求跨站攻击
			$state = $_REQUEST['state'];
			if(isset($_SESSION['weibo_state'])){
				$weibo_state = $_SESSION['weibo_state'];
			}else{
				error_tips(1029);//微博登录授权失败
				exit;
			}
			if ( empty($state) || $state !== $weibo_state ) {
				error_tips(1099);
				exit;
			}
    	
			unset($_SESSION['weibo_state']);
	
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = WB_CALLBACK_URL;
			try {
				//获取code和回调地址，$token是一个数组
				$token = $o->getAccessToken( 'code', $keys ) ;
			} catch (OAuthException $e) {
			}
		}else{
			$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
			$state = uniqid( 'weibo_', true);
			$_SESSION['weibo_state'] = $state; 
			//获取授权的回调地址
			$code_url = $o->getAuthorizeURL( WB_CALLBACK_URL ,'code', $state );
			header("Location:$code_url");
		}
		if($token){  
			$c = new SaeTClientV2( WB_AKEY,WB_SKEY,$token['access_token'] ); 
			$uid_get = $c->get_uid();
			$uid = isset($uid_get['uid']) ? $uid_get['uid'] : '';
			$weibo_user_info = $c->show_user_by_id($uid);
			if($weibo_user_info){ 
				$sns_username   = $weibo_user_info['screen_name'];
				$face   = $weibo_user_info['avatar_large'];
				$openid_weibo  = $uid;
				$user_handle = new user();
				$result = $user_handle -> get_userinfo_by_sns($openid_weibo,$sns_type);
				if($result){
					$user_handle -> update_cookie_user_info($result);
					if(empty($result['nickname'])){
						header("location:/home/".$result['user_type']."/card");
					}else{
						header("location:http://www.hotyq.com");
					}
				}else{
					$_SESSION['sns_username'] = $sns_username;
					$_SESSION['sns_type'] = $sns_type;
					$_SESSION['sns_face'] = $face;
					$_SESSION['sns_openid'] = $openid_weibo;
					header("location:/account/binding.php?sns_username=".urlencode($sns_username));
				}	
			}
		}else{
			error_tips(1099);
			exit;
		}	
	}else{
		error_tips(1099);
		exit;
	}
?>