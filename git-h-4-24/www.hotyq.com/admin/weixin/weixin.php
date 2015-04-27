<?php
/*
  * wechat php test
*/
require_once("../../includes/common_inc.php");
define("TOKEN", "hotyq");
define('APP_ID', 'wxe0690159001cdf1f');
define('APP_SECRET', '0f8ad7d3abf831bfc66955534bb44ebb');
var_dump($_GET);
$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();
class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function transmitText($hotyq,$open_id,$content)
    {
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        </xml>";
        $result = sprintf($xmlTpl,$open_id,$hotyq,time(),$content);
        echo $result;
    }

    public function responseMsg()
    {
    	global $db_hyq_write;
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
			/* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
			   the best way is to check the validity of xml by yourself */
			libxml_disable_entity_loader(true);
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$type = $postObj->MsgType;
			$customevent = $postObj->Event;
			$keyword = trim($postObj->Content);
			$time = time();
			$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";  
			if($type == "event" and $customevent == "subscribe"){
				$msgType = "text";
				$contentStr = '您好，欢迎关注红演圈微信公众号！红演圈pc端已经全面上线，了解玩法，点击下方"怎么玩"按钮，想要了解我们请点击"关于我们"。回复1查看地址，回复2查看电话，回复3查看电话';
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;
			}
			if(!empty( $keyword ))
			{
				$msgType = "text";
				if($keyword == "1"){
					$contentStr = "红演圈的地址：北京市惠新东街12号华德公寓1段1501";
				}elseif($keyword == "2"){
					$contentStr = "红演圈的邮箱：2719326296@qq.com";
				}elseif($keyword == "3"){
					$contentStr = "红演圈的电话：010-84299348";
				}else{
					$contentStr = "Hi,欢迎你的关注！回复1查看地址，回复2查看邮箱，回复3查看电话";
				}
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;
			}else{
				echo "请您登录红演圈官网http://www.hotyq.com";
			}
        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>