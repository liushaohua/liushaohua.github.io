<?php
	session_start();
	header("Content-type:text/html;charset=utf-8");
	$PAGE_TYPE = "ajax_page";		
	require_once('../../includes/common_home_inc.php');
 	require_once(COMMON_PATH."/orgprofile.class.php");
 	require_once(COMMON_PATH."/album.class.php");	
 	require_once(COMMON_PATH."/collect.class.php");	
 	require_once(COMMON_PATH."/apply.class.php");	
 	require_once(COMMON_PATH."/recruit.class.php");	
 	require_once(COMMON_PATH."/invite.class.php");	
 	require_once(COMMON_PATH."/message.class.php");	
 	require_once(COMMON_PATH."/base.class.php");	
 	require_once(COMMON_PATH."/sensitive_words.class.php");	
	$orgprofile = new orgprofile();
	$user = new user();	
	$photo = new photo();
	$album = new album();
	$service = new service();
	$apply = new apply();
	$recruit = new recruit();
	$invite = new invite();
	$message = new message();
	$rongyun = new rongyun();
	$base = new base();
	$uid = $user_info["id"];
	$class = $user_info['user_type'];
	if(!isset($_REQUEST["action"]) || empty($_REQUEST["action"])){
		$ret_arr = get_state_info(1099);
		echo json_encode($ret_arr);
		exit;
	}
	$action = clear_gpq($_REQUEST["action"]);
	switch($action){
		case "org_card":			
			echo update_org_card($uid);
			break;
		case "complete_orgprofile":
			echo complete_orgprofile($uid);
			break;	
		case "upload_album_photo":
			echo json_encode(upload_album_photo($uid));
			break;
		case "delete_album_photo":
			echo json_encode(delete_album_photo($uid));
			break;
		case "update_org_contact":
			echo json_encode(update_org_contact($uid));
			break;
		case "update_org_profile":
			echo update_org_profile($uid);
			break;
		case 'arr_test':
			$arr = clear_gpq($_REQUEST['arr']);
			var_dump($arr);
			break;
		case 'add_org_tag':
			$self_tag = clear_gpq($_REQUEST['self']);		//自定义标签
			$sys_tag = clear_gpq($_REQUEST['sys']);			//系统标签
			add_org_tag($uid, $class, $self_tag, $sys_tag);
			break;
		case 'delete_self_tag':
			//用户自定义的标签删除处理
			$tagid = clear_gpq($_REQUEST['tid']);		//自定义标签id
			delete_self_tag($uid,$tagid);
			break;
		case 'update_artist':
			//var_dump($_REQUEST['artist_info']);
			echo update_artist($uid);
			break;
		case 'add_user_service':
			$arr = $_REQUEST;
			echo add_org_service($uid, $arr);
			break;
		case 'add_album_title':
			echo add_album_title($uid);
			break;
	}
	//删除自定义标签
	function delete_self_tag($uid,$tagid){
		global $userprofile;
		//$uid =7;
		$state_code = $orgprofile -> delete_self_tag_org($uid,$tagid);
		if($state_code == 1318){
			echo $state_code;
			exit;
		}else if($state_code == 1319){
			echo $state_code;
			exit;
		}else{
			echo $state_code;
		}
	}
	
//添加红服务处理
	function add_user_service($uid, $arr){
		global $service;
		if( !isset($arr['service_1_list']) || empty($arr['service_1_list']) ) return 1288;
		if( !isset($arr['service_2_list']) || empty($arr['service_2_list']) ) return 1289;
		if( !isset($arr['service_3_list']) || empty($arr['service_3_list']) ) return 1290;
		foreach($arr['service_1_list'] as $v){
			if(empty($v)){
				return 1288;
			}
		}
		foreach($arr['service_2_list'] as $v){
			if(empty($v)){
				return 1289;
			}
		}
		//检查三级  不能空
		foreach($arr['service_3_list'] as $v){
			if(empty($v)){
				return 1290;
			}
		}
		$unique_service_2_list = array_unique($arr['service_2_list']);
		if(count($unique_service_2_list) != count($arr['service_2_list'])){
			return 1296;
		}
		
		for($i=0;$i<count($arr['service_2_list']);$i++){
			$arr['service_list'][$i]['service_1_id'] = clear_gpq($arr['service_1_list'][$i]);
			$arr['service_list'][$i]['service_2_id'] = clear_gpq($arr['service_2_list'][$i]);
			if(empty($arr['service_3_list'][$i])){
				return 1290;
				//$arr['service_list'][$i]['service_3_id'] = '';
			}else{
				foreach($arr['service_3_list'][$i] as $k => $v){
					$arr['service_3_list'][$i][$k] = intval($v);
				}
				$arr['service_list'][$i]['service_3_id'] = $arr['service_3_list'][$i];
			}
			
			
		}
		$service_list = $arr["service_list"];
		$r = $service -> check_service_exits($uid);
		if($r){
			$rr = $service -> delete_user_service($uid);
			if($rr){
				foreach($service_list as $v){
					if(empty($v['service_3_id'])){
						$service_item['service_1_id'] = $v['service_1_id'];
						$service_item['service_2_id'] = $v['service_2_id'];
						$service_item['service_3_id'] = $v['service_3_id'];
						$result = $service -> add_user_service($uid,$service_item);
						if(!$result){
							return 1294;
						}
					}else{
						foreach($v['service_3_id'] as $v0){
							$service_item['service_1_id'] = $v['service_1_id'];
							$service_item['service_2_id'] = $v['service_2_id'];
							$service_item['service_3_id'] = $v0;
							$result = $service -> add_user_service($uid,$service_item);
							if(!$result){
								return 1294;
							}
						}
					}
				}
				_update_percent_cookie($uid);
				$service -> get_e_service_by_user($uid, $flash = 1);//刷新缓存
				return 1000;
			}
		}else{
			foreach($service_list as $v){
				if(empty($v['service_3_id'])){
					$service_item['service_1_id'] = $v['service_1_id'];
					$service_item['service_2_id'] = $v['service_2_id'];
					$service_item['service_3_id'] = $v['service_3_id'];
					$result = $service -> add_user_service($uid,$service_item);
					if(!$result){
						return 1294;
					}
				}else{
					foreach($v['service_3_id'] as $v0){
						$service_item['service_1_id'] = $v['service_1_id'];
						$service_item['service_2_id'] = $v['service_2_id'];
						$service_item['service_3_id'] = $v0;
						$result = $service -> add_user_service($uid,$service_item);
						if(!$result){
							return 1294;
						}
					}
				}
			}
			_update_percent_cookie($uid);
			$service -> get_e_service_by_user($uid, $flash = 1);//刷新缓存
			return 1000;
		}	
	}
	//添加红标签
	function add_org_tag($uid, $class, $self_tag, $sys_tag){
		global $orgprofile;
		$org_tag = $orgprofile -> get_org_tag_by_uid($uid);
		if($org_tag == 1315){
			echo $org_tag;      				//标签内容读取失败！
			exit;	
		}else{
			$self_tag_str = rtrim($self_tag, '|');
			if(strpos($self_tag_str, '|')){
				$self_tag_array = explode('|', $self_tag_str);
			}else{
				$self_tag_array = array($self_tag_str);
			}
			//统计自定义标签的个数
			if(count($self_tag_array)>5){
				echo 1310;
				exit;
			}	
			////////////////////////////////////////////////
			$sys_tag_str = rtrim($sys_tag, '|');
			if(strpos($sys_tag_str, '|')){
				$sys_tag_id_arr = explode('|', $sys_tag_str);
			}else{
				$sys_tag_id_arr = array($sys_tag_str);		//只有一个系统标签的时候
			}
			
			//统计系统标签的数目
			if(count($sys_tag_id_arr)>5){
				echo 1310;
				exit;
			}

			if($org_tag != 1314){
				$state_code = $orgprofile -> delete_org_tag($uid);
				if($state_code != 1000){
					echo $state_code;
					exit;
				}
			}

		}
		//删除原有标签之后 插入新标签操作
		
		//自定义标签处理
		if(!empty($self_tag)){							//自定义标签不空
			$usertype = $class;
			if($usertype == 'user'){
				$class = 1;
			}else{
				$class = 2;
			}
			//将自定义标签插入标签库
			//同时检查自定义标签在标签库中是否存在过
			$self_tag_array = array_unique($self_tag_array);   //去除重复值
			foreach($self_tag_array as $k => $v){
				//标签的长度超过6个字符 不处理
				if(strlen($v) > 18){
					continue;
				}

				$result = $orgprofile -> check_tag_exits($class, $v);
				if($result){
					$self_tag_id_arr[] = $result['id'];
				}else{
					$self_tag_id_arr[] = $orgprofile -> add_org_self_tag($class, $v);
				}	
			}
		}
		if(empty($sys_tag)){							//用户选择的系统标签
			if(empty($self_tag_id_arr) || !isset($self_tag_id_arr)){
				echo 1313; 
				exit;									//系统标签和自定义标签都没有
			}else{
				$org_tag = $self_tag_id_arr;			//只有自定义标签
			}
		}else{
			if(empty($self_tag_id_arr) || !isset($self_tag_id_arr)){
				$org_tag = $sys_tag_id_arr; 			//只选了系统标签
			}else{
				$org_tag = array_merge($sys_tag_id_arr, $self_tag_id_arr);      //系统标签和自定义标签都选
			}
		}
		//将用户标签映射到用户标签表
		foreach($org_tag as $k=>$v){
			//将用户标签映射到用户标签表
			$state_code = $orgprofile -> add_org_tag($uid, $v);
			if($state_code == 1312){
				echo $state_code;
				exit('添加自定义标签失败,请重新填写！');
			}
		}

		echo 1000;
	}
	//更新红艺人	
	function update_artist($uid){
		global $orgprofile,$user,$user_info;
		if(isset($_REQUEST['artist_info'])){
			$artist_info = $_REQUEST['artist_info'];
		}else{
			$state_code = $orgprofile -> delete_artist($uid);	#全部删除
			_update_percent_cookie($uid); #更新百分比
			return $state_code;	
					
		}
		$state_code = $orgprofile -> delete_artist($uid);
		if($state_code == 1000){
			foreach ($artist_info as $value) {
				$value['name'] = strip_tags(clear_gpq($value['name']));
				$value['info'] = strip_tags(clear_gpq($value['info']));

				if($value['name'] == ''){
					//return clear_gpq(htmlspecialchars($value['info'], ENT_NOQUOTES ));
					return 1155;
				}else{
					if(strlen($value['name']) > 80 || strlen($value['info']) > 500){
						return 1155;
					}
					$state_code = $orgprofile -> add_artist($uid,$value);					
					if($state_code != 1000){
						return $state_code;
					}
				}
			}
			_update_percent_cookie($uid); #更新百分比
			return 1000;			
		}else{
			return $state_code;
			
		}
	}

	//第一次填写机构红名片  zzh
	function update_org_card($uid){
		global $user,$orgprofile,$user_info,$rongyun,$base,$flash;
		
		$sensitive_words = new sensitive_words();
		$sensitive_words_arr = $sensitive_words -> get_sensitive_words_list($flash=0);#获取敏感词数组
		
		$nickname = clear_gpq($_REQUEST['nickname']);
		$org_profile_array['create_time'] = intval($_REQUEST['create_time']);
		$org_profile_array['province_id'] = intval($_REQUEST['pid']);
		$org_profile_array['city_id'] = intval($_REQUEST['cid']);
		$org_profile_array['district_id'] = intval($_REQUEST['did']);
		$org_profile_array['type'] = intval($_REQUEST['type']);	
		$org_profile_array['state'] = clear_gpq($_REQUEST['state']);
		$org_profile_array['legal_person'] = clear_gpq($_REQUEST['legal_person']);			
		if(empty($nickname) || !$base -> is_nickname($nickname)||strlen($nickname)>90){return 1101;}	#昵称填写错误

		//过滤敏感词
		$nickname = $sensitive_words -> filter_sensitive_words($nickname,$sensitive_words_arr);	
		
		if(!isset($org_profile_array['create_time']) || empty($org_profile_array['create_time'] ))return 1136;
		if($org_profile_array['province_id'] < 1)return 1103;
		if($org_profile_array['city_id'] < 1)return 1104;
		if(!isset($org_profile_array['type']) || empty($org_profile_array['type'] ))return 1137;			
		if(!isset($org_profile_array['state']) ||!in_array($org_profile_array['state'] , array("busy","free","other")))return 1138;
		if(!isset($org_profile_array['legal_person']) ||!in_array($org_profile_array['legal_person'] , array("yes","no")))return 1139;			
		$result = $user -> update_user_nickname($uid,$nickname);
		if($result == 1000){
			$re = $orgprofile -> update_org_profile($uid,$org_profile_array);
			if($re == 1000){
				_update_percent_cookie($uid);
				$rongyun -> get_user_token($uid, $nickname, $user_info['icon_server_url'].$user_info['icon_path_url']);
				$user -> get_userinfo($uid, $flash = 1);
				return 1000;	
			}else{
				return 1112;
			}
		}else{
			return 1112; 		//资料修改失败
		}  
	}
	//更新机构红档案 待删
	function update_org_profile($uid){
		global $user,$orgprofile,$user_info;

		$org_profile_array['introduce'] =  strip_tags(clear_gpq($_REQUEST['ajaxData']['firmIntro']));
		$org_profile_array['production'] = strip_tags(clear_gpq($_REQUEST['ajaxData']['firmWorks']));
		$org_profile_array['honor'] = strip_tags(clear_gpq($_REQUEST['ajaxData']['firmHonor']));
		if(strlen($org_profile_array['introduce'])>600) return 1149;
		if(strlen($org_profile_array['production'])>600) return 1150;
		if(strlen($org_profile_array['honor'])>600) return 1151;
		$result = $orgprofile -> update_org_profile($uid,$org_profile_array);
		if($result == 1000){
			_update_percent_cookie($uid);
			return 1000;	
		}else{
			return 1112; 		//资料修改失败
		}  		
	}	
	//保存机构的新资料
	function complete_orgprofile($uid){
		global $user,$orgprofile,$user_info,$rongyun,$base,$flash;
		$sensitive_words = new sensitive_words();
		$sensitive_words_arr = $sensitive_words -> get_sensitive_words_list($flash=0);#获取敏感词数组
		
		$nickname = clear_gpq($_REQUEST['nickname']);
		$org_profile_array['create_time'] = intval($_REQUEST['create_time']);
		$org_profile_array['province_id'] = intval($_REQUEST['pid']);
		$org_profile_array['city_id'] = intval($_REQUEST['cid']);
		$org_profile_array['district_id'] = intval($_REQUEST['did']);
		$org_profile_array['type'] = intval($_REQUEST['type']);	
		//$org_profile_array['legal_person'] = clear_gpq($_REQUEST['legal_person']);	
		$org_profile_array['introduce'] =  strip_tags(clear_gpq($_REQUEST['introduce']));
		$org_profile_array['production'] = strip_tags(clear_gpq($_REQUEST['production']));
		$org_profile_array['honor'] = strip_tags(clear_gpq($_REQUEST['honor']));
		if(empty($nickname) || !$base -> is_nickname($nickname)||strlen($nickname)>90){return 1101;}	#昵称填写错误
		if(!isset($org_profile_array['create_time']) || empty($org_profile_array['create_time'] ))return 1136;
		if(!isset($org_profile_array['type']) || empty($org_profile_array['type'] ))return 1137;	
		if($org_profile_array['province_id'] < 1)return 1103;
		if($org_profile_array['city_id'] < 1)return 1104;
		//if(!isset($org_profile_array['legal_person']) ||!in_array($org_profile_array['legal_person'] , array("yes","no")))return 1139;
		if(strlen($org_profile_array['introduce'])>600) return 1149;
		if(strlen($org_profile_array['production'])>600) return 1150;
		if(strlen($org_profile_array['honor'])>600) return 1151;	

		//过滤敏感词
		$nickname = $sensitive_words -> filter_sensitive_words($nickname,$sensitive_words_arr);
		$org_profile_array['introduce'] = $sensitive_words -> filter_sensitive_words($org_profile_array['introduce'],$sensitive_words_arr);
		$org_profile_array['production'] = $sensitive_words -> filter_sensitive_words($org_profile_array['production'],$sensitive_words_arr);
		$org_profile_array['honor'] = $sensitive_words -> filter_sensitive_words($org_profile_array['honor'],$sensitive_words_arr);
				
		$result = $user -> update_user_nickname($uid,$nickname);
		if($result == 1000){
			$re = $orgprofile -> update_org_profile($uid,$org_profile_array);
			if($re ==1000){
				_update_percent_cookie($uid);
				$rongyun -> update_user_token($uid, $nickname, $user_info['icon_server_url'].$user_info['icon_path_url']);
				return 1000;					
			}else{
				return 1112;
			}
		}else{
			return 1112; 		//资料修改失败
		}  		
	}
	function upload_album_photo($uid){
		global $album,$photo,$user,$IMG_WWW;
		$result = $album -> get_photo_list_by_user($uid);
		if(is_array($result) && count($result) >= 6){
			return get_state_info(1058); //您只能上传6张照片，您可以先删除原有照片后重新上传！
		}
		$file_info = $_FILES[array_shift(array_keys($_FILES))];
		$state_code = $photo -> check_upload_photo($file_info);
		if($state_code == 1000){
			$hash_dir = $photo -> get_hash_dir('albums',$uid);
			$newname = $photo -> create_newname($photo -> get_suffix($file_info['name']));
			$file_path = $hash_dir .'/'.$newname;
			$result = $photo -> upload_photo($file_info['tmp_name'],$file_path);
			if($result){
				$result = $album -> upload_user_photo($uid,$file_path,'album');
				if(is_numeric($result)){
					$ret_arr = get_state_info(1000);
					_update_percent_cookie($uid);
					$data['id'] = $result;
					$data['thumbnail'] = $IMG_WWW.$file_path.'!150.100';
					$data['photo'] = $IMG_WWW.$file_path.'!s800';
					$ret_arr['data'] = $data;
					return $ret_arr;
				}else{
					return get_state_info(1044);//图片上传失败！
				}
			}else{
				return get_state_info(1044);//图片上传失败！
			}
		}else{
			return get_state_info($state_code);//1041(无上传操作)1042（上传图片大小超限）1043（上传图片类型不符）
		}
	}
	//删除相册图片
	function delete_album_photo($uid){
		global $album,$photo;
		if(!isset($_REQUEST['photo_id_arr'])) return get_state_info(1054);
		$photo_id_arr = json_decode($_REQUEST['photo_id_arr']);
		$fail_photo = array();
		foreach($photo_id_arr as $v){
			$photo_info[$v] = $album -> get_photo_info($v);
			if($photo_info[$v]){
				$result[$v] = $album -> delete_user_photo($uid,$v);
				if($result[$v]){
					$result[$v] = $photo -> delete_photo_file($photo_info[$v]['path_url']);
				}else{
					$fail_photo[] = $v; //数据库删除失败的id数组
				}
			}else{
				$fail_photo[] = $v; 
			}
		}
		_update_percent_cookie($uid);
		$ret_arr = get_state_info(1000);
		$data = array_diff($photo_id_arr,$fail_photo);
		$ret_arr['data'] = $data;
		return $ret_arr;
	}

	function update_org_contact($uid){
		global $orgprofile,$flash;
		
		$sensitive_words = new sensitive_words();
		$sensitive_words_arr = $sensitive_words -> get_sensitive_words_list($flash=0);#获取敏感词数组
		
		$contact_info['contact_mobile'] = clear_gpq($_REQUEST['contact_mobile']);
		$contact_info['contact_email'] = clear_gpq($_REQUEST['contact_email']);
		$contact_info['contact_weixin'] = clear_gpq($_REQUEST['contact_weixin']);
		$contact_info['contact_qq'] = clear_gpq($_REQUEST['contact_qq']);
		
		//过滤敏感词
		$contact_info['contact_mobile'] = $sensitive_words -> filter_sensitive_words($contact_info['contact_mobile'],$sensitive_words_arr);
		$contact_info['contact_email'] = $sensitive_words -> filter_sensitive_words($contact_info['contact_email'],$sensitive_words_arr);
		$contact_info['contact_weixin'] = $sensitive_words -> filter_sensitive_words($contact_info['contact_weixin'],$sensitive_words_arr);
		$contact_info['contact_qq'] = $sensitive_words -> filter_sensitive_words($contact_info['contact_qq'],$sensitive_words_arr);
		
		$result = $orgprofile -> update_org_profile($uid,$contact_info);
		if($result){
			return get_state_info(1000);
		}else{
			return get_state_info(1112);
		}
	}
	
	function get_state_info($state_code){
		global $STATE_LIST;
		if(array_key_exists($state_code,$STATE_LIST)){
			$ret_arr['code'] = $state_code;
			$ret_arr['desc'] = $STATE_LIST[$state_code];
			return $ret_arr;
		}else{
			return false;
		}	
	}

	//给图片增加title
	function add_album_title($uid){
		global $album,$flash;
		$sensitive_words = new sensitive_words();
		$sensitive_words_arr = $sensitive_words -> get_sensitive_words_list($flash=0);#获取敏感词数组
		foreach($_REQUEST["photo_arr"] as $k => $v){
			//过滤敏感词
			$_REQUEST["photo_arr"][$k] = $sensitive_words -> filter_sensitive_words($v,$sensitive_words_arr);
			$album -> update_photo_title($uid,intval($k),clear_gpq($_REQUEST["photo_arr"][$k]));
			//$album -> update_photo_title($uid,intval($k),clear_gpq($v));
		}
		return 1000;
	}

	function _update_percent_cookie($uid){
		global $user,$flash;
		if($user -> update_data_percent($uid)){
			$user_info = $user -> get_userinfo($uid,$flash);
			$user -> update_cookie_user_info($user_info);
		}
	}	
	
?>	