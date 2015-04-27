<?php
	/*
	*	身份证验证类
	*	ZQF ----2015-01-24
	*/

	class identity{
		//身份证号码
		public $identity_num;
		//身份证姓名
		public $identity_name;
		//构造函数
		function __construct($num = '',$name = ''){
			$this -> identity_num = $num;
			$this -> identity_name = $name;
		}

		private function check_identity_name($identity_name){
			$pattern = "/^([\x{4e00}-\x{9fa5}]+)[·]*([\x{4e00}-\x{9fa5}]+)$/u";
			$match= preg_match($pattern,$identity_name);
			if($match){
				return true;
			}else{
				return false;	#身份证姓名格式不正确!
			}

		}

		private function check_identity_num($identity_num){
			$pattern_one = "/^\d{6}((0[48]|[2468][048]|[13579][26])0229|\d\d(0[13578]|10|12)(3[01]|[12]\d|0[1-9])|(0[469]|11)(30|[12]\d|0[1-9])|(02)(2[0-8]|1\d|0[1-9]))\d{3}$/";	#一代身份证正则
			$pattern_two = "/^\d{6}((2000|(19|21)(0[48]|[2468][048]|[13579][26]))0229|(((20|19)\d\d)|2100)((0[13578]|10|12)(3[01]|[12]\d|0[1-9])|(0[469]|11)(30|[12]\d|0[1-9])|(02)(2[0-8]|1\d|0[1-9])))\d{3}[\dX]$/";	#二代身份证正则
			$match_one = preg_match($pattern_one,$identity_num);
			$match_two = preg_match($pattern_two,$identity_num);
			if($match_one || $match_two){
				return true;
			}else{
				return false;	#身份证格式不正确!
			}
		}	
		/*
		***************DOM转成数组******************
		*@param string $node       DOM根
		*@return  array/string            成功返回数组，失败返回false
		*/
		private function get_array($node){
			$array = false;
			if($node -> hasAttributes()){
			    foreach($node -> attributes as $attr) {
			    	$array[$attr -> nodeName] = $attr -> nodeValue;
			    }
			}
			if ($node -> hasChildNodes()){
			    if ($node -> childNodes -> length == 1) {
			    	$array[$node -> firstChild -> nodeName] = $this -> get_array($node -> firstChild);
				}else{
				    foreach ($node -> childNodes as $childNode) {
				    	if ($childNode -> nodeType != XML_TEXT_NODE) {
				    		$array[$childNode -> nodeName][] = $this ->get_array($childNode);
				    	}
				    }
				}
			}else{
			    return $node -> nodeValue;
			}
			return $array;
		}

		public function verify_identity(){
			if($this -> check_identity_name($this -> identity_name) && $this -> check_identity_num($this -> identity_num)){
				$res = '';
				try {
				    $client = new SoapClient('/export/home/cms/www/www.hotyq.com/api/authentication/NciicServices.wsdl');
				    $licenseCode = file_get_contents('/export/home/cms/www/www.hotyq.com/api/authentication/license.txt');
				    $condition = '<?xml version="1.0" encoding="UTF-8" ?>
									<ROWS>  
									    <INFO>
									    <SBM>hyyqhyyq53289</SBM>
									    </INFO>
									    <ROW>
									        <GMSFHM>公民身份号码</GMSFHM>
									        <XM>姓名</XM>
									    </ROW>
									    <ROW FSD="100022" YWLX="身份证认证" >
									    <GMSFHM>'.$this -> identity_num.'</GMSFHM>
									    <XM>'.$this -> identity_name.'</XM>
									    </ROW>
									</ROWS>';
				    $params = array('inLicense' => $licenseCode,'inConditions' => $condition);
				    $res = $client -> nciicCheck($params);
				}catch(Exception $e) {
				  	return $e -> getMessage();
				}
				$dom = new DOMDocument();
				$dom -> loadXML($res -> out);
				return $this -> get_array($dom -> documentElement);
			}else{
				return false;
			}
		}
		public function get_identity_info(){
			global $db_hyq_read;
			$sql = "SELECT * FROM hyq_identity WHERE identity_num = '".$this -> identity_num."'";
			return $db_hyq_read -> fetch_array($db_hyq_read -> query($sql));
		}

		public function add_verified_identity($userid,$identity_num,$identity_name){
			global $db_hyq_write;
			$sql = "INSERT INTO hyq_identity SET 
					identity_num = '{$identity_num}',
					identity_name = '{$identity_name}',
					userid = '{$userid}',
					add_date = now()";
			return $db_hyq_write -> query($sql);
		}
	}
?>