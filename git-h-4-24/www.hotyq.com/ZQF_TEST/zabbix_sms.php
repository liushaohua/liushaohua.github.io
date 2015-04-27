<?php
	header("Content-Type:text/html;charset=utf-8");
	function real_send_sms($mobile,$content,$ext ='',$stime = '',$rrid = ''){
		$flag=0;
		$args=array(
			'sn'=>'SDK-BBX-010-21450', 
			'pwd'=>strtoupper(md5('SDK-BBX-010-21450'.'b=c8-d=c')),
			'mobile'=>$mobile,
			'content'=>$content.'[红演圈]',
			'ext'=>$ext,		
			'stime'=>$stime,
			'msgfmt'=>'',
			'rrid'=>$rrid
		);
		$params='';
		foreach ($args as $key=>$value) { 
			if ($flag!=0) { 
				$params .= "&"; 
				$flag = 1; 
			} 
			$params.= $key."=";
			$params.= urlencode($value); 
			$flag = 1; 
		} 
		$length = strlen($params);
		//创建socket连接 
		$fp = fsockopen("sdk.entinfo.cn",8061,$errno,$errstr,10) or exit($errstr."--->".$errno); 
		//构造post请求的头 
		$header = "POST /webservice.asmx/mdsmssend HTTP/1.1\r\n"; 
		$header .= "Host:sdk.entinfo.cn\r\n"; 
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
		$header .= "Content-Length: ".$length."\r\n"; 
		$header .= "Connection: Close\r\n\r\n"; 
		//添加post的字符串 
		$header .= $params."\r\n"; 
		//发送post的数据 
		fputs($fp,$header);
		$inheader = 1; 
		while (!feof($fp)) { 
			$line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据 
			if ($inheader && ($line == "\n" || $line == "\r\n")) { 
				$inheader = 0; 
			}
			if ($inheader == 0) { 
				//echo $line; 
			}  	
		}
		$line=str_replace("<string xmlns=\"http://tempuri.org/\">","",$line);
		$line=str_replace("</string>","",$line);
		return $result=explode("-",$line);
	}
	// $arr = array_shift($argv);
	// $content_arr = array_shift($arr);
	// $content_str = implode("\n",$content_arr);
	$mobile = $_GET["mobile"];
	var_dump($mobile);
	real_send_sms($mobile,"您发布的【xxx招募标题xxx】http://www.hotyq.com/recruit/631正在被重点推广，已有艺人浏览报名，请登录查看，账号xxxxxxxxxxx 密码******* （登录后请修改密码哦)，最专业的文化演艺招募发布平台",$ext ='',$stime = '',$rrid = '');

?>

