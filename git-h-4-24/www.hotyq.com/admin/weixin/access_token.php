<?php
	header("Content-Type:text/html;charset=utf-8");
    define("TOKEN", "hotyq");
    define('APP_ID', 'wxe0690159001cdf1f');
    define('APP_SECRET', '0f8ad7d3abf831bfc66955534bb44ebb');
    //echo $access_token = get_access_token();
    responseMsg()
    createmenu($access_token);
    //验证
    function valid()
    {
        $echoStr = $_GET["echostr"];
        if(checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    //响应式回复
    function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
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
            }else if($type == "event" and $customevent == "unsubscribe"){
                $msgType = "text";
                $contentStr = "轻轻地你走了，没有带走一片云彩！欢迎回来！";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }
            if(!empty( $keyword ))
            {
                $msgType = "text";
                if($keyword == "1"){
                    $contentStr = "请您登录红演圈官网http://www.hotyq.com";
                }elseif($keyword == "2"){
                    $contentStr = "红演圈的邮箱：2719326296@qq.com";
                }elseif($keyword == "3"){
                    $contentStr = "请您登录红演圈官网http://www.hotyq.com";
                }else{
                    $contentStr = "请您登录红演圈官网http://www.hotyq.com";
                }
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }else{
                echo "请您登录红演圈官网http://www.hotyq.com";
            }
        }else {
            echo "请您登录红演圈官网http://www.hotyq.com";
            exit;
        }
    }

    function transmitText($hotyq,$open_id,$content)
    {
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        </xml>";
        $result = sprintf($xmlTpl, $hotyq, $open_id, time(), $content);
        return $result;
    }


	function get_access_token()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APP_ID."&secret=".APP_SECRET;
        $data = json_decode(file_get_contents($url),true);
        if($data['access_token']){
            return $data['access_token'];
        }else{
            return "获取access_token错误";
        }
    }

    function createmenu($access_token)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $arr = array( 
            'button' =>array(
                array(
                    'name'=>urlencode("最新招募"),
                    'type'=>'view',
                    'url'=>'/recruit'
                ),
                array(
                    'name'=>urlencode("怎么玩"),
                    'type'=>'view',
                    'url'=>'http://www.hotyq.com:8888/js/mobile/wan/index.html'
                ),
                array(
                    'name'=>urlencode("关于我们"),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode("公司简介"),
                            'type'=>'view',
                            'url'=>'http://mp.weixin.qq.com/s?__biz=MzAwNTExNzAxNg==&mid=205163382&idx=1&sn=d78214b9e3384d77b03fa98fd86573e7&scene=18#wechat_redirect'
                        ),
                        array(
                            'name'=>urlencode("招募合作"),
                            'type'=>'view',
                            'url'=>'http://mp.weixin.qq.com/s?__biz=MzAwNTExNzAxNg==&mid=205161857&idx=1&sn=638f7a7cf7ff055a60cf37fa753b2921&scene=18#wechat_redirect'
                        )，
                         array(
                            'name'=>urlencode("加入我们"),
                            'type'=>'view',
                            'url'=>'http://mp.weixin.qq.com/s?__biz=MzAwNTExNzAxNg==&mid=205172581&idx=1&sn=8d43158c65655bb4901a95b99705186d&scene=18#wechat_redirect'
                        )， array(
                            'name'=>urlencode("APP下载"),
                            'type'=>'view',
                            'url'=>'/account/login'
                        )
                    )
                )
            )
        );
        $jsondata = urldecode(json_encode($arr));
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$jsondata);
        curl_exec($ch);
        curl_close($ch);
    }

    function getmenu($access_token)
    {
        # code...
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$access_token;
        $data = file_get_contents($url);
        return $data;
    }


    function delmenu($access_token)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$access_token;
        $data = json_decode(file_get_contents($url),true);
        if ($data['errcode']==0) {
            return true;
        }else{
            return false;
        }
    }

    function checkSignature()
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

?>