<?php
//微信管理类

define("TOKEN", "hotyq");
define('APP_ID', 'wxe0690159001cdf1f');
define('APP_SECRET', '0f8ad7d3abf831bfc66955534bb44ebb');

class weixin{

	//获取公众号access_token  注意access_token有效期7200秒
	function get_access_token(){
	    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APP_ID."&secret=".APP_SECRET;
	    $data = json_decode(file_get_contents($url),true);
	    //var_dump($data);
	    if($data['access_token']){
	        return $data['access_token'];
	    }else{
	        return "获取access_token错误";
	    }
	}

	//发送文本推送消息
	function transmit_text($access_token,$openid,$content){
		$data = '{
		    "touser":"oWHIes7X6nUP8-RdSLIs_AkVNUOg",
		    "msgtype":"text",
		    "text":
		    {
		        "content":"你有新的红演圈消息，赶紧去看看！"
		    }
		}';

		$transmitUrl="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;
		$ch = curl_init($transmitUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Content-Type: application/json',
		    'Content-Length: ' . strlen($data))
		);
	}



}
?>