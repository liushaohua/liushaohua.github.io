<?php
	header("Content-type:text/html;charset=utf-8");
	require_once('../../includes/common_home_inc.php');
 	require_once(COMMON_PATH."/recruit.class.php");
 	require_once(COMMON_PATH."/collect.class.php");
 	require_once(COMMON_PATH."/find_recruit.class.php");
 	require_once(COMMON_PATH."/redis_find.class.php");
 	require_once(COMMON_PATH."/solr.class.php");
 	require_once(COMMON_PATH."/solr_recruit.class.php");
 	require_once(COMMON_PATH."/solr.class.php");
 	require_once(COMMON_PATH."/search_recruit.class.php");
	require_once(COMMON_PATH."/sensitive_words.class.php");	
	$recruit = new recruit();
	$userprofile = new userprofile();
	$user = new user();
	$photo = new photo();
	$apply = new apply();
	$collect = new collect();
	$base = new base();
	$find_recruit = new find_recruit();
	//判断action是否存在
	if( !isset($_REQUEST["action"]) || empty($_REQUEST["action"]) ){
		$ret_arr = get_state_info(1099);
		echo json_encode($ret_arr);
		exit;
	}
	$action = clear_gpq($_REQUEST["action"]);
	//基础检查完毕
	switch($action){
		//发布招募
		case "is_collect":
			echo json_encode(is_collect());
			break;
		case "is_apply":
			echo json_encode(is_apply());
			break;
		case "add_child_role_list":
			//根据父类角色id查找出子角色列表
			echo json_encode($userprofile -> get_role_list_by_parentid($_REQUEST['id']));
			break;
		case "add_recruit":
			$arr = $_REQUEST;
			$arr['uid'] = $user_info["id"];
			//var_dump($arr);exit;
			echo json_encode(add_recruit($arr));
			break;
		case 'upload_recruit_photo':
			echo json_encode(upload_recruit_photo());
			break;
		case "delete_recruit_photo":
			echo json_encode(delete_recruit_photo());
			break;
		case "add_service_2_list":
			echo json_encode($base -> get_service_list_by_parentid($_REQUEST['s_1_id']));
			break;
		case "add_service_3_list":
			echo json_encode($base -> get_service_list_by_parentid($_REQUEST['s_2_id']));
			break;
		case "recruit_cover_cut":
			if(!isset($_REQUEST['recruit_id'])){
				exit("403|缺少招募id");
			}
			$recruit_id = clear_gpq($_REQUEST['recruit_id']);
			recruit_cover_cut($recruit_id);
			break;
		case "get_recruit_list_by_uid":		#邀约时判断是否有招募intval($_REQUEST['uid'])
			echo get_recruit_list_by_uid(intval($_REQUEST['uid']));
			break;
	}
	function get_recruit_list_by_uid($uid){
		$recruit = new recruit;
		$uid = intval($_REQUEST['uid']);
		$re = $recruit -> get_recruit_list_by_user_for_invite($uid);	
		if($re){
			return 1000;
		}else{
			return 1097;
		}
	}
	function recruit_cover_cut($recruit_id){
		global $IMG_STATIC,$IMG_WWW,$photo,$recruit,$IMG_CONFIG,$IMG_SERVERINDEX;
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
		//$file_name = basename(clear_gpq($_REQUEST['src']));
		$file_name = $photo -> create_newname($photo -> get_suffix(clear_gpq($_REQUEST['src'])));
		$local_path = $IMG_CONFIG["TMP_PATH"]."/".$file_name;#本地tmp
		$icon_path = $photo -> get_hash_dir('recruit',$recruit_id)."/".$file_name;#根据uid hash出路径（裁后上传存放路径)
		//3 拉下 裁切  上传
		$photo -> ftp_get_img($src,$local_path); 
		$photo -> slice_photo($zoom,$x1,$y1,$w,$h,$local_path,$local_path);//源 目标
		ftp_copy_files(array($icon_path),array($local_path),$IMG_SERVERINDEX,FTP_BINARY);// 目标 源
		if(!$photo -> save_on_upyun($local_path,$icon_path)){//源 目标
			unlink($local_path);
			exit("403|1:1图片保存失败");
		}
		//4 存库
		$recruit_info = $recruit -> get_recruit_info($recruit_id);#先查出 删除放后面
		$arr['cover_path_url'] = $icon_path; 
		$arr['cover_server_url'] = $IMG_WWW; 
		$state_code = $recruit -> update_recruit($recruit_id,$arr);#修改头像
		if(!$state_code){
			exit("500|{$state_code}|{$icon_path}");#返回错误信息
		}
		if(!empty($recruit_info['icon_path_url'])){
			$result = $photo -> delete_photo_file($recruit_info['icon_path_url']);
			if(!$result){
				exit("500|1:1图片删除失败");//1:1
			}
		}
		exit("200|{$IMG_WWW}{$icon_path}");
		
		
	}
	//判断用户是否收藏该招募
	function is_collect(){
		global $collect;
		if( !isset($_POST['recruit_id']) || empty($_POST['recruit_id']) ) return get_state_info(1288);
		$recruit_id = intval($_POST["recruit_id"]);
		if( !isset($_POST['uid']) || empty($_POST['uid']) ) return get_state_info(1288);
		$uid = clear_gpq($_POST["uid"]);
		if($collect -> get_collect_exists($uid,'recruit',$recruit_id)){
			return true;
		}
		return false;
	}
	//判断用户是否报名该招募
	function is_apply(){
		global $apply;
		//招募id 和 当前uid 是否存在
		if( !isset($_POST['recruit_id']) || empty($_POST['recruit_id']) ) return get_state_info(1288);
		$recruit_id = intval($_POST["recruit_id"]);
		if( !isset($_POST['uid']) || empty($_POST['uid']) ) return get_state_info(1288);
		$uid = clear_gpq($_POST["uid"]);
		return $apply -> get_apply_role_list($uid,$recruit_id);
	}
	
	//发布招募
	function add_recruit($arr){
		global $recruit,$flash;
		$sensitive_words = new sensitive_words();
		$find_recruit = new find_recruit();
		$search_recruit = new search_recruit();
		$sensitive_words_arr = $sensitive_words -> get_sensitive_words_list($flash=0);#获取敏感词数组
		//空记录id 是否为空
		if( !isset($arr['id']) || empty($arr['id']) ) return get_state_info(1288);
		//1主题是否为空
		$arr["name"] = clear_gpq(@$arr["name"]);
		if( empty($arr['name']) ) return get_state_info(1280);
		
		//1.5用户输入的主题  过滤下
		$arr["name"] = $sensitive_words -> filter_sensitive_words($arr["name"],$sensitive_words_arr);
		
		//2类型是否是空  <0
		if( !isset($arr['type_id']) || empty($arr['type_id']) ) return get_state_info(1281);
		$arr["type_id"] = intval($arr["type_id"]);
		if( $arr["type_id"] < 0 ) return get_state_info(1281);
		//3角色各项是否为空
		if( !isset($arr['service_1_list']) || empty($arr['service_1_list']) ) return get_state_info(1289);
		if( !isset($arr['service_2_list']) || empty($arr['service_2_list']) ) return get_state_info(1289);
		if( !isset($arr['service_3_list']) || empty($arr['service_3_list']) ) return get_state_info(1289);
		if( !isset($arr['sex_list']) || empty($arr['sex_list']) ) return get_state_info(1289);
		if( !isset($arr['number_list']) || empty($arr['number_list']) ) return get_state_info(1289);
		if( !isset($arr['service_requires_list']) || empty($arr['service_requires_list']) ) return get_state_info(1289);
			foreach($arr['service_2_list'] as $v){
				if($v = ''){
					return get_state_info(1289);
				}
			}
			foreach($arr['service_3_list'] as $v){
				if($v = ''){
					return get_state_info(1289);
				}
			}
			foreach($arr['sex_list'] as $v){
				if($v = ''){
					return get_state_info(1289);
				}
			}
			foreach($arr['number_list'] as $v){
				if($v = ''){
					return get_state_info(1289);
				}
			}
			foreach($arr['service_requires_list'] as $v){
				if($v != '' && strlen($v) > 900 ){
					return get_state_info(1297);
				}
			}
			//子角色是否有重复
			$unique_service_2_list = array_unique($arr['service_2_list']);
			if(count($unique_service_2_list) != count($arr['service_2_list'])){
				return get_state_info(1296);
			}
		for($i=0;$i<count($arr['service_2_list']);$i++){
			$arr['role_list'][$i]['service_1_id'] = clear_gpq($arr['service_1_list'][$i]);
			$arr['role_list'][$i]['service_2_id'] = clear_gpq($arr['service_2_list'][$i]);
			foreach($arr['service_3_list'][$i] as $k => $v){
				$arr['service_3_list'][$i][$k] = intval($v);
			}
			$arr['role_list'][$i]['service_3_id'] = $arr['service_3_list'][$i];
			$arr['role_list'][$i]['sex'] =  clear_gpq($arr['sex_list'][$i]);
			$arr['role_list'][$i]['number'] =  clear_gpq($arr['number_list'][$i]);
			
			//过滤敏感词
			$arr['service_requires_list'][$i] = $sensitive_words -> filter_sensitive_words($arr['service_requires_list'][$i],$sensitive_words_arr);
			
			$arr['role_list'][$i]['service_require'] =  strip_tags( clear_gpq($arr['service_requires_list'][$i]) );
			$arr['role_list'][$i]['recruit_id'] =  intval($arr['id']);
		}
		//5招募简介 可以为空  不能超过300字
		if( !isset($arr['descr']) ) return get_state_info(1284);
		if( !empty($arr['descr']) ){
			$arr["descr"] = strip_tags( clear_gpq($arr["descr"]) );
			if(strlen($arr['descr']) > 900 ){
				return get_state_info(1297);
			}
			//过滤敏感词
			$arr["descr"] = $sensitive_words -> filter_sensitive_words($arr["descr"],$sensitive_words_arr);
		}
		//7 截止时间
		if( !isset($arr['interview_end_time']) || empty($arr['interview_end_time']) ) return get_state_info(1286);
		$arr["interview_end_time"] = clear_gpq($arr["interview_end_time"]);
		//6 工作时间
		if( !isset($arr['work_start_time']) || empty($arr['work_end_time']) ) return get_state_info(1285);
		$arr["work_start_time"] = clear_gpq($arr["work_start_time"]);
		if( !isset($arr['work_end_time']) || empty($arr['work_end_time']) ) return get_state_info(1285);
		$arr["work_end_time"] = clear_gpq($arr["work_end_time"]);
		//8工作地点是否为空
		if( !isset($arr['province_id']) || empty($arr['province_id']) ) return get_state_info(1287);
		$arr["province_id"] = clear_gpq($arr["province_id"]);
		if( !isset($arr['city_id']) || empty($arr['city_id']) ) return get_state_info(1287);
		$arr["city_id"] = clear_gpq($arr["city_id"]);
		if( !isset($arr['district_id']) || empty($arr['district_id']) ) return get_state_info(1287);
		$arr["district_id"] = clear_gpq($arr["district_id"]);
		if( !isset($arr['addr_detail']) ) return get_state_info(1287);
		$arr["addr_detail"] = clear_gpq($arr["addr_detail"]);
		//过滤敏感词
		$arr["addr_detail"] = $sensitive_words -> filter_sensitive_words($arr["addr_detail"],$sensitive_words_arr);
		//各项符合后  插入数据库
		$id = intval($arr["id"]);
		unset($arr["id"]);
		unset($arr["action"]);
		unset($arr["service_1_list"]);
		unset($arr["service_2_list"]);
		unset($arr["service_3_list"]);
		unset($arr["sex_list"]);
		unset($arr["number_list"]);
		unset($arr["service_requires_list"]);
		$role_list = $arr["role_list"];
		unset($arr["role_list"]);
		$arr["is_show"] = 'yes';
		$arr["status"] = '1';
		$arr["is_checked"] = 'yes';
		$state_code = $recruit -> update_recruit($id,$arr);
		if($state_code){
			foreach($role_list as $v){
				$e_id = $recruit -> add_recruit_service($v);
				if(!$e_id){
					return get_state_info(1294);//一二级服务插入失败
				}
				foreach($v['service_3_id'] as $v0){
					$service_item['service_1_id'] = $v['service_1_id'];
					$service_item['service_2_id'] = $v['service_2_id'];
					$service_item['service_3_id'] = $v0;
					$service_item['recruit_id'] = $id;
					$result = $recruit -> add_hyq_e_service_item($e_id,$service_item);
					if(!$result){
						return get_state_info(1294);//三级服务插入失败
					}
				}
			}
			//招募发布成功 写进redis里面 solr里  在搜索筛选频道中实时显示
			$find_recruit -> add_recruit_info($id);
			$search_recruit -> add_recruit_info($id);
			return get_state_info(1000);
		}else{
			return get_state_info(1295);
		}
	}

	function upload_recruit_photo(){
		global $recruit,$photo,$user,$IMG_WWW;
		if(!isset($_REQUEST['recruit_id']) || empty($_REQUEST['recruit_id'])) return get_state_info(1099);
		$result = $recruit -> get_recruit_photo_list(intval($_REQUEST['recruit_id']));
		if(is_array($result) && count($result) >= 6){
			return get_state_info(1058); //您只能上传6张照片，您可以先删除原有照片后重新上传！
		}
		$file_info = $_FILES[array_pop(array_keys($_FILES))];
		$state_code = $photo -> check_upload_photo($file_info);
		if($state_code == 1000){
			$hash_dir = $photo -> get_hash_dir('recruit',intval($_REQUEST['recruit_id']));
			$newname = $photo -> create_newname($photo -> get_suffix($file_info['name']));
			$file_path = $hash_dir .'/'.$newname;
			$result = $photo -> upload_photo($file_info['tmp_name'],$file_path);
			if($result){
				$result = $recruit -> upload_recruit_photo(intval($_REQUEST['recruit_id']),$file_path);
				if(is_numeric($result)){
					$ret_arr = get_state_info(1000);
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
	function delete_recruit_photo(){
		global $recruit,$photo;
		if(!isset($_REQUEST['recruit_id']) || empty($_REQUEST['recruit_id'])) return get_state_info(1099);
		if(!isset($_REQUEST['photo_id_arr'])) return get_state_info(1054);
		$photo_id_arr = json_decode($_REQUEST['photo_id_arr']);
		$fail_photo = array();
		foreach($photo_id_arr as $v){
			$photo_info[$v] = $recruit -> get_recruit_photo_info($v);
			if($photo_info[$v]){
				$result[$v] = $recruit -> delete_recruit_photo(intval($_REQUEST['recruit_id']),$v);
				if($result[$v]){
					$result[$v] = $photo -> delete_photo_file($photo_info[$v]['path_url']);
				}else{
					$fail_photo[] = $v; //数据库删除失败的id数组
				}
			}else{
				$fail_photo[] = $v; 
			}
		}
		$ret_arr = get_state_info(1000);
		$data = array_diff($photo_id_arr,$fail_photo);
		$ret_arr['data'] = $data;
		return $ret_arr;
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
?>