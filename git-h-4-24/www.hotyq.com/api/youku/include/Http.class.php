<?php
/**
 * HTTP
 *
 * @copyright	youku.com
 * @author		panzhiqi
 */
class Http
{
	 /**
     * GET
     * @param $url 目标URL
     * @param $parameters 参数(key=>value)
     * return string
     */
    static public function get($url,$parameters=array()) {
        return self::request('GET',$url,$parameters);
    }
    /**
     * POST
     * @param $url 目标URL
     * @param $parameters 参数(key=>value)
     * return string
     */
    static public function post($url,$parameters=array()) {
        return self::request('POST',$url,$parameters);
    }

	public static function request($method, $url, $params =  array()) {
        $str_params = http_build_query($params);
        $ch = curl_init();
        if ($method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $str_params);
        } else
                $url .= '?' . $str_params;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        return $result;
    }

	public static function do_post_request($url, $postdata, $data) { 
		$str = ""; 
		foreach($postdata as $key=>$value) {   
			$str .= $key.'='.$value.'&'; 
		}   
		$url = $url.'?'.rtrim($str, '&');
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/octet-stream", "Content-length: ".strlen($data)));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result);
	} 
}
?>
