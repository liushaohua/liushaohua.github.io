<?php
	header("Content-type:text/html;charset=utf-8");
	$PAGE_TYPE = "ajax_page";	
	require_once('../../includes/common_home_inc.php');
 	require_once(COMMON_PATH."/userprofile.class.php");
 	require_once(COMMON_PATH."/album.class.php");
 	require_once(COMMON_PATH."/collect.class.php");
 	require_once(COMMON_PATH."/apply.class.php");	
 	require_once(COMMON_PATH."/recruit.class.php");	
 	require_once(COMMON_PATH."/invite.class.php");	
 	require_once(COMMON_PATH."/message.class.php");	
 	require_once(COMMON_PATH."/base.class.php");
	require_once(COMMON_PATH."/sensitive_words.class.php");		
	$user = new user();
	$base = new base();
	$userprofile = new userprofile();
	$service = new service();
	$photo = new photo();
	$album = new album();
	$apply = new apply();
	$recruit = new recruit();
	$invite = new invite();
	$message = new message();
	$rongyun = new rongyun();
	
	$uid = $user_info["id"];
	$class = $user_info["user_type"];
	if(!isset($_REQUEST["action"]) || empty($_REQUEST["action"])){
		$ret_arr = get_state_info(1099);
		echo json_encode($ret_arr);
		exit;
	}
	$action = clear_gpq($_REQUEST["action"]);
	switch($action){
		case "upload_icon":
			upload_icon($_FILES['image'],$uid);
			break;
		case "upload_album_img":
			upload_album_img($_FILES['image'],$uid);
			break;
		case "ablum_img_cut":
			ablum_img_cut($uid);
			break;
		case "head_photo_cut":
			head_photo_cut($uid);
			break;
		case "save_hot_role":
			//$_REQUEST['arr'] = '{"userRoles":"40,41,49,43","userCustomRoles":"\u5c31\u662f\u840c\u840c\u54d2448884"}';
			echo json_encode(save_hot_role($uid));
			break;
		case "update_user_card":	
			echo update_user_card($uid);
			break;
		// case "complete_user_card":	
		// 	echo complete_user_card($uid);
		// 	break;
		case "complete_userprofile": #new
			echo complete_userprofile($uid);
			break;	
		case "upload_album_photo":
			echo json_encode(upload_album_photo($uid));
			break;
		case "delete_album_photo":
			echo json_encode(delete_album_photo($uid));
			break;
		case "update_user_contact":
			echo json_encode(update_user_contact($uid));
			break;
		case 'update_user_profile':
			echo update_user_profile($uid);
			break;
		case 'add_user_service':
			$arr = $_REQUEST;
			echo add_user_service($uid, $arr);
			break;
		case 'add_album_title':
			echo add_album_title($uid);
			break;
	}
	//更新红人基本资料
	function complete_userprofile($uid){
		global $user,$userprofile,$user_info,$rongyun,$base,$COMMON_CONFIG,$flash;
		$sensitive_words = new sensitive_words();
		$sensitive_words_arr = $sensitive_words -> get_sensitive_words_list($flash=0);#获取敏感词数组
		
		if($uid<1) return 1099;
		$nickname = clear_gpq($_REQUEST['nickname']);
		//$uinfo['sex'] = clear_gpq($_REQUEST['sex']);
		$uinfo['province_id'] = intval($_REQUEST['pid']);
		$uinfo['city_id'] = intval($_REQUEST['cid']);
		$uinfo['district_id'] = intval($_REQUEST['did']);		
		$uinfo['age'] = intval($_REQUEST['age']);
		$uinfo['height'] = intval($_REQUEST['height']);
		$uinfo['weight'] = intval($_REQUEST['weight']);
		$uinfo['bust'] = intval($_REQUEST['bust']);
		$uinfo['waist'] = intval($_REQUEST['waist']);
		$uinfo['hips'] = intval($_REQUEST['hips']);
		$uinfo['star'] = clear_gpq($_REQUEST['star']);
		$uinfo['blood'] = clear_gpq($_REQUEST['blood']);
		$uinfo['native_province_id'] = intval($_REQUEST['native_province_id']);
		$uinfo['native_city_id'] = intval($_REQUEST['native_city_id']);
		$uinfo['native_district_id'] = intval($_REQUEST['native_district_id']);
		$uinfo['school'] = strip_tags(clear_gpq($_REQUEST['school']));
		$uinfo['finish_year'] = intval($_REQUEST['finish_year']);
		$uinfo['specialty'] = strip_tags(clear_gpq($_REQUEST['specialty']));		
		$uinfo['degree'] = clear_gpq($_REQUEST['degree']);
		if(empty($nickname) || !$base -> is_nickname($nickname)||strlen($nickname)>90){return 1101;}	#昵称填写错误
		//if(empty($nickname) || !preg_match("/^(?!_)(?!.*?_$)[\x{4e00}-\x{9fa5}\w]+$/iu",$nickname)||strlen($nickname)>100){return 1101;}	#昵称填写错误
		//if(!isset($uinfo['sex']) || !in_array($uinfo['sex'] , array("m","f")))return 1102;	#性别没填写
		if($uinfo['province_id'] < 1)return 1103;		#省没填写
		if($uinfo['city_id'] < 1)return 1104;	#市没填写
		// if(empty($uinfo['district_id'])){return 1105;}		//区没填写
		if(!empty($uinfo['degree'])){		
			if(!in_array($uinfo['degree'] ,$COMMON_CONFIG["DEGREE"])) return 1112;
		}	
		$uinfo['in_org'] = strip_tags(clear_gpq($_REQUEST['in_org']));
		if(!empty($uinfo['star'])){	
			if(!in_array($uinfo['star'] ,$COMMON_CONFIG["STAR"])) return 1112;
		}	
		if(!empty($uinfo['blood'])){
			if(!in_array($uinfo['blood'] , $COMMON_CONFIG["BLOOD"])) return 1112;
		}		
		if(strlen($uinfo['school'])>90) return 1165;
		if(strlen($uinfo['specialty'])>90) return 1166;
		if(strlen($uinfo['in_org'])>90) return 1167;
		
		//过滤敏感词
		$nickname = $sensitive_words -> filter_sensitive_words($nickname,$sensitive_words_arr);
		$uinfo['school'] = $sensitive_words -> filter_sensitive_words($uinfo['school'],$sensitive_words_arr);	
		$uinfo['specialty'] = $sensitive_words -> filter_sensitive_words($uinfo['specialty'],$sensitive_words_arr);	
		$uinfo['in_org'] = $sensitive_words -> filter_sensitive_words($uinfo['in_org'],$sensitive_words_arr);	
		
		$result = $user -> update_user_nickname($uid,$nickname);
		if($result == 1000){
			$re = $userprofile -> update_user_profile($uid,$uinfo);			
			if($re ==1000){
				_update_percent_cookie($uid);
				//$rongyun -> update_user_token($uid, $nickname, $user_info['icon_server_url'].$user_info['icon_path_url']);
				return 1000;				
			}else{
				return 1112;	
			}
		}else{
			return 1112;
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
			if(empty($v) || $v = array()){
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

	//添加红标签 废弃 待删
	function add_per_tag($uid,$class,$self_tag,$sys_tag){
		global $userprofile;
		$user_tag = $userprofile -> get_user_tag_by_uid($uid);
		if($user_tag == 1305){
			echo $user_tag;      				//标签内容读取失败！
			exit;
		}else{
			$self_tag_str = rtrim($self_tag, '|');
			if(strpos($self_tag_str, '|')){
				$self_tag_array = explode('|', $self_tag_str);
			}else{
				$self_tag_array = array($self_tag_str);  		//只有一个自定义标签的时候
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
			if(count($sys_tag_id_arr)>15){
				echo 1310;
				exit;
			}
			
			if($user_tag != 1304){
				$state_code = $userprofile -> delete_user_tag($uid);
				if($state_code != 1000){
					echo $state_code;
					exit;
				}
			}
			
		}
		//删除原有标签之后 插入新标签操作
		
		//自定义标签处理
		if(!empty($self_tag)){							//自定义标签不空时
			$usertype = $class;
			if($usertype == 'user'){
				$class = 1;
			}else{
				$class = 2;
			}
			//将自定义标签插入标签库  返回值 新插入标签id
			//同时检查自定义标签在标签库中是否存在过
			$self_tag_array = array_unique($self_tag_array);   //去除重复值
			foreach($self_tag_array as $k => $v){
				//标签的长度超过6个字符 不处理
				if(strlen($v) > 18){
					continue;
				}
				$result = $userprofile -> check_tag_exits($class, $v);
				if($result){
					if($result['parent_id'] != 0){
						$self_tag_id_arr[] = $result['id'];
					}
				}else{
					$self_tag_id_arr[] = $userprofile -> add_user_self_tag($class, $v);
				}	
			}
		}
		
		if(empty($sys_tag)){							//用户选择的系统标签
			if(empty($self_tag_id_arr) || !isset($self_tag_id_arr)){
				echo 1303; 								//系统标签和自定义标签都没有
				exit;
			}else{
				$person_tag = $self_tag_id_arr;	//只有自定义标签
			}
		}else{
			if(empty($self_tag_id_arr) || !isset($self_tag_id_arr)){
				$person_tag = $sys_tag_id_arr; 			//只选了系统标签
			}else{
				$person_tag = array_merge($sys_tag_id_arr, $self_tag_id_arr);      //系统标签和自定义标签都选
			}
		}
			
		//将用户标签映射到用户标签关联表
		foreach($person_tag as $k=>$v){
			//将用户标签映射到用户标签表
			$state_code = $userprofile -> add_user_tag($uid, $v);
			if($state_code == 1302){
				echo $state_code;
				exit;
			}
		}
		_update_percent_cookie($uid);
		echo 1000;
		
	}
	//相册里的上传图片
	function upload_album_img($file_info,$uid){
		global $photo,$IMG_WWW,$album,$IMG_CONFIG,$IMG_SERVERINDEX;
		//执行上传前 先检查上传的相册图片是否超过12张
		$result = $album -> get_photo_list_by_user($uid);
		//var_dump($result);exit;
		if(is_array($result) && count($result) >= 12){
			exit('415|您只能上传12张照片'); //您只能上传12张照片，您可以先删除原有照片后重新上传！
		}
		$state_code = $photo -> check_upload_photo($file_info);
		if($state_code === 1000){
			// 判断图片是否符合尺寸要求
			/* list($width,$height) = getimagesize($file_info['tmp_name']);
			if($height >= $width && $width < 1040 ){
				exit('407|请上传长度大于1040像素，高度大于585像素的图片');
			}
			if($width > $height && $height < 585 ){
				exit('405|请上传长度大于1040像素，高度大于585像素的图片');
			} */
			$file_name = $photo -> create_newname($photo -> get_suffix($file_info["name"]));
			$file_path = $IMG_CONFIG["TMP_PATH"]."/".$file_name;
			if($photo -> upload_photo($file_info["tmp_name"],$file_path)){
				$tmp_url = $IMG_WWW.$file_path;
				exit("200|{$tmp_url}");
			}else{
				exit('403|图片移动失败！');
			}
		}else if($state_code === 1042){
			exit('412|图片大小超出限制');
		}else if($state_code === 1043){
			exit('406|图片类型不符合');
		}else if($state_code === 1041){
			//无文件上传操作  不作提示
			exit;
		}else{
			exit('403|图片移动失败！');
		}
	}
	function upload_icon($file_info,$uid){
		global $photo,$IMG_WWW,$IMG_CONFIG,$IMG_SERVERINDEX;
		//echo　$file_info['tmp_name'];exit;
		$state_code = $photo -> check_upload_photo($file_info);
		if($state_code === 1000){//机构/红人/招募封面 都是这个接口(上传到img/ tmp下)
			//1 判断图片是否符合尺寸要求(不限制宽高)
			/* list($width,$height) = getimagesize($file_info['tmp_name']);
			if($height >= $width && $width < 600 ){
				exit('407|请上传长度大于600像素，高度大于600像素的图片');
			}
			if($width > $height && $height < 600 ){
				exit('405|请上传长度大于600像素，高度大于600像素的图片');
			} */
			//2 生成上传目标路径+名字  tmp/create
			$file_name = $photo -> create_newname($photo -> get_suffix($file_info["name"]));
			$file_path = $IMG_CONFIG["TMP_PATH"]."/".$file_name;
			//3 move---img  upyun
			if($photo -> upload_photo($file_info["tmp_name"],$file_path)){
				$tmp_url = $IMG_WWW.$file_path;
				exit("200|{$tmp_url}");
			}else{
				exit('403|图片移动失败');
			}
		}else if($state_code === 1042){
			exit('412|图片大小超出限制');
		}else if($state_code === 1043){
			exit('406|图片类型不符合');
		}else if($state_code === 1041){
			exit;#无文件上传操作  不作提示
		}else{
			exit('403|图片移动失败！');
		}
	}
	function ablum_img_cut($uid){
		global $IMG_STATIC,$IMG_WWW,$photo,$album,$IMG_CONFIG,$IMG_SERVERINDEX;
		//点保存 传递裁切的参数 src
		if(!isset($_REQUEST['zoom'])){
			exit("403|保存失败");
		}
		$zoom = clear_gpq($_REQUEST['zoom']);
		if(!isset($_REQUEST['x1'])){
			exit("403|保存失败");
		}
		$x1 = clear_gpq($_REQUEST['x1']);
		if(!isset($_REQUEST['y1'])){
			exit("403|保存失败");
		}
		$y1 = clear_gpq($_REQUEST['y1']);
		if(!isset($_REQUEST['w'])){
			exit("403|保存失败");
		}
		$w = clear_gpq($_REQUEST['w']);
		if(!isset($_REQUEST['h'])){
			exit("403|保存失败");
		}
		$h = clear_gpq($_REQUEST['h']);
		if(!isset($_REQUEST['src'])){
			exit("403|保存失败");
		}
		
		//去掉img的域名
		$src = $IMG_CONFIG["TMP_PATH"]."/".basename(clear_gpq($_REQUEST['src']));
		//根据后缀 生成新文件名字
		$file_name = basename(clear_gpq($_REQUEST['src']));
		//根据uid hash出路径（该用户上传的图片存放路径)  裁后图
		$local_path = $IMG_CONFIG["TMP_PATH"]."/".$file_name;//本地tmp
		$icon_path = $photo -> get_hash_dir('albums',$uid)."/".$file_name;

		//1 两张图 上传到云上
		$photo -> ftp_get_img($src,$local_path); 
		$photo -> slice_photo($zoom,$x1,$y1,$w,$h,$local_path,$local_path);//源 目标
		ftp_copy_files(array($icon_path),array($local_path),$IMG_SERVERINDEX,FTP_BINARY);// 目标 源
		if(!$photo -> save_on_upyun($local_path,$icon_path)){//源 目标
			unlink($local_path);
			exit("403|16:9图片保存失败");
		}
		$r = $photo -> delete_photo_file($local_path);
		//2 存库里 insert
		$result = $album -> upload_user_photo($uid,$icon_path);
		if(!$result){
			exit("插入失败");
		}
		_update_percent_cookie($uid);
		exit("200|{$IMG_WWW}{$icon_path}|{$result}");
	}
	//切割头像
	function head_photo_cut($uid){//红人和机构
		global $IMG_STATIC,$IMG_WWW,$photo,$user,$userprofile,$IMG_CONFIG,$IMG_SERVERINDEX,$rongyun,$user_info;
		if(!isset($_REQUEST['zoom'])){
			exit("403|保存失败");
		}
		$zoom = clear_gpq($_REQUEST['zoom']);
		if(!isset($_REQUEST['x1'])){
			exit("403|保存失败");
		}
		$x1 = clear_gpq($_REQUEST['x1']);
		if(!isset($_REQUEST['y1'])){
			exit("403|保存失败");
		}
		$y1 = clear_gpq($_REQUEST['y1']);
		if(!isset($_REQUEST['w'])){
			exit("403|保存失败");
		}
		$w = clear_gpq($_REQUEST['w']);
		if(!isset($_REQUEST['h'])){
			exit("403|保存失败");
		}
		$h = clear_gpq($_REQUEST['h']);
		if(!isset($_REQUEST['src'])){
			exit("403|保存失败");
		}
		//1 src是img-- tmp下 去掉img的域名(方法里会自动加上)
		$src = $IMG_CONFIG["TMP_PATH"]."/".basename(clear_gpq($_REQUEST['src']));
		//2 根据后缀 生成新文件名字(拉下来到本地存储的路径 和 裁后上传的目标路径)
		$file_name = $photo -> create_newname($photo -> get_suffix(clear_gpq($_REQUEST['src'])));
		$local_path = $IMG_CONFIG["TMP_PATH"]."/".$file_name;#本地tmp
		$icon_path = $photo -> get_hash_dir('user',$uid)."/".$file_name;#根据uid hash出路径（裁后上传存放路径)
		//3 拉下 裁切  上传
		$photo -> ftp_get_img($src,$local_path); 
		$photo -> slice_photo($zoom,$x1,$y1,$w,$h,$local_path,$local_path);//源 目标
		ftp_copy_files(array($icon_path),array($local_path),$IMG_SERVERINDEX,FTP_BINARY);// 目标 源
		if(!$photo -> save_on_upyun($local_path,$icon_path)){//源 目标
			unlink($local_path);
			exit("403|1:1图片保存失败");
		}
		//4 存库
		$userinfo = $user -> get_userinfo($uid);#先查出 删除放后面
		$state_code = $user -> update_face($uid,$icon_path);#修改头像
		if(!$state_code){
			exit("500|{$state_code}|{$icon_path}");#返回错误信息
		}
		if(!empty($userinfo['icon_path_url'])){
			$result = $photo -> delete_photo_file($userinfo['icon_path_url']);
			if(!$result){
				exit("500|1:1图片删除失败");//1:1
			}
		}
		_update_percent_cookie($uid);
		if(!empty($user_info['nickname'])){
			$rongyun -> update_user_token($uid, $user_info['nickname'], $user_info['icon_server_url'].$user_info['icon_path_url']);
		}
		exit("200|{$IMG_WWW}{$icon_path}");
	}

	//第一次填写红名片  zzh
	function update_user_card($uid){
		global $user,$userprofile,$user_info,$rongyun,$base,$flash;
		$sensitive_words = new sensitive_words();
		$sensitive_words_arr = $sensitive_words -> get_sensitive_words_list($flash=0);#获取敏感词数组
		
		if($uid<1) return 1099;	
		$nickname = clear_gpq($_REQUEST['nickname']);
		$user_profile_array['sex'] = clear_gpq($_REQUEST['sex']);
		$user_profile_array['province_id'] = intval($_REQUEST['pid']);
		$user_profile_array['city_id'] = intval($_REQUEST['cid']);
		$user_profile_array['district_id'] = intval($_REQUEST['did']);
		$user_profile_array['state'] = clear_gpq($_REQUEST['state']);
		$role_id = intval($_REQUEST['role']);
		if(empty($nickname) || !$base -> is_nickname($nickname)||strlen($nickname)>90){return 1101;}	#昵称填写错误
		
		//过滤敏感词
		$nickname = $sensitive_words -> filter_sensitive_words($nickname,$sensitive_words_arr);	
		
		//if(empty($nickname) || !preg_match("/^(?!_)(?!.*?_$)[\x{4e00}-\x{9fa5}\w]+$/iu",$nickname)||strlen($nickname)>100){return 1101;}	#昵称填写错误
		if(!isset($user_profile_array['sex']) || !in_array($user_profile_array['sex'] , array("m","f")))return 1102;	#性别没填写
		if($user_profile_array['province_id'] < 1)return 1103;		#省没填写
		if($user_profile_array['city_id'] < 1)return 1104;	#市没填写
		// if(empty($user_profile_array['district_id'])){return 1105;}		//区没填写
		if(!isset($role_id) || empty($role_id)) return 1107;		#期望角色没填写					
		if(!isset($user_profile_array['state']) ||!in_array($user_profile_array['state'] , array("busy","free","other")))return 1106;	#个人状态没填写	
		$arr['parent_id'] = $role_id;
		$arr['id'] = 0; 
		$role_status = $userprofile -> add_role_by_user($uid,$arr);
		if($role_status != 1000) return $role_status;		
		$result = $user -> update_user_nickname($uid,$nickname);
		if($result == 1000){
			$re = $userprofile -> update_user_profile($uid,$user_profile_array);
			if($re == 1000){
				_update_percent_cookie($uid);
				//$rongyun -> get_user_token($uid, $nickname, $user_info['icon_server_url'].$user_info['icon_path_url']);
				$user -> get_userinfo($uid, $flash = 1);
				return 1000;				
			}else{
				return 1112;
			}
		}else{
			return 1112; 	 //资料修改失败
		}  
	}	
		
	function upload_album_photo($uid){
		global $album,$photo,$user,$IMG_WWW;
		$result = $album -> get_photo_list_by_user($uid);
		if(is_array($result) && count($result) >= 6){
			return get_state_info(1058); //您只能上传6张照片，您可以先删除原有照片后重新上传！
		}
		$file_info = $_FILES[array_pop(array_keys($_FILES))];
		$state_code = $photo -> check_upload_photo($file_info);
		if($state_code == 1000){
			$hash_dir = $photo -> get_hash_dir('albums',$uid);
			$newname = $photo -> create_newname($photo -> get_suffix($file_info['name']));
			$file_path = $hash_dir .'/'.$newname;
			$result = $photo -> upload_photo($file_info['tmp_name'],$newname,$file_path);
			if($result){
				$result = $album -> upload_user_photo($uid,$file_path,'album');
				if(is_numeric($result)){
					$user -> update_data_percent($uid);
					$ret_arr = get_state_info(1000);
					$data['id'] = $result;
					$data['thumbnail'] = $IMG_WWW.$file_path.'!150.100';
					$data['photo'] = $IMG_WWW.$file_path.'!s800';
					$ret_arr['data'] = $data;
					_update_percent_cookie($uid);
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
		global $album,$photo,$user;
		if(!isset($_REQUEST['photo_id'])) return get_state_info(1054);
		$photo_id = intval($_REQUEST['photo_id']);
		//$photo_id_arr = json_decode($_REQUEST['photo_id_arr']);
		//$fail_photo = array();
		//foreach($photo_id_arr as $v){
		$photo_info = $album -> get_photo_info($photo_id);
		$album -> delete_user_photo($uid,$photo_id);
		$photo -> delete_photo_file($photo_info['path_url']);
		$user -> update_data_percent($uid);
		$ret_arr = get_state_info(1000);
		$ret_arr['data'] = $photo_id;
		_update_percent_cookie($uid);
		return $ret_arr;
	}

	function update_user_contact($uid){
		global $userprofile,$flash;
		
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
		
		$result = $userprofile -> update_user_profile($uid,$contact_info);
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

	function save_hot_role($uid){
		global $userprofile;
		global $user,$flash;
		$sensitive_words = new sensitive_words();
		$sensitive_words_arr = $sensitive_words -> get_sensitive_words_list($flash=0);#获取敏感词数组
		//判断是否传递角色json 
		if( !isset($_REQUEST['role_data']) || empty($_REQUEST['role_data']) ) return get_state_info(1290);
		$arr = json_decode($_REQUEST["role_data"],true);
		//1判断所选系统角色是否存在 
		if( !isset($arr['userRoles']) ) return get_state_info(1291);
		if(empty($arr['userRoles'])) return get_state_info(1299);
		//2判断自定义角色是否存在    //可以为空 不能不存在
		if( !isset($arr['userCustomRoles']) ) return get_state_info(1292);
		
		//3 删除用户自定义角色（不删了）
		//4 删除用户已经选择的系统角色
		$state_code = $userprofile -> delete_role_list_by_user($uid); 
		 if($state_code != 1000){
			return get_state_info($state_code);
		}
		//4.5  传递过来的包含一级和二级 角色 id  
		if( empty($arr['userRoles']) && empty($arr['userCustomRoles']) ){
			return get_state_info(1293);#用户没有选择角色
		}
		foreach($arr['userRoles'] as $k => $v){
			$result = $userprofile -> get_role_info(clear_gpq($v));
			if($result['parent_id'] == 0){
				//一级id
				$result['parent_id'] = $result['id'];
				$result['id'] = 0;
			}
			$arr['userRoles'][$k] = $result;
		}
		//5添加自定义角色
		$role_into = '';
		if( !empty($arr['userCustomRoles']) ){
			$name = clear_gpq($arr['userCustomRoles'][0]);
			//过滤敏感词
			$name = $sensitive_words -> filter_sensitive_words($name,$sensitive_words_arr);
			
			//判断是否和系统角色  相同 all_role_list
			$all_role_list = $userprofile -> all_role_list();
			foreach($all_role_list as $v){
				if($v['name'] == $name){
					//有相同  判断是否是自定义
					if($v['parent_id'] != -1){
						//返回错误信息  与系统角色重复
						return get_state_info(1298);//???
					}else{
						//与自定义角色相同   id赋值给他
						$role_into = $v['id'];
						break;
					}
				}
			}
			if($role_into == ''){
				//没相同   插入到角色表中
				$result = $userprofile -> add_role_user_self($name);
				if($result){
					//id加入到列表中
					$role_into = $result;
				}else{
					return get_state_info($result);//插入失败
				}
			}
			$arr['userRoles'][] = $userprofile -> get_role_info(clear_gpq($role_into));
		}	
		//6添加用户选择的系统角色（包含自定义）
		foreach($arr['userRoles'] as $v){
			$state_code = $userprofile -> add_role_by_user($uid,$v);
			if($state_code != 1000){
				return get_state_info($state_code);
			}
		}
		return get_state_info($state_code);
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