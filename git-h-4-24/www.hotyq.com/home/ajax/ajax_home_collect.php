<?php
	header("Content-type:text/html;charset=utf-8");
	require_once('../../includes/common_home_inc.php');
	require_once(COMMON_PATH.'/collect.class.php');
	require_once(COMMON_PATH.'/user_msg_total.class.php');
	$msg_total = new user_msg_total();
	$collect = new collect();
	isset($_REQUEST['action']) ? $action = clear_gpq($_REQUEST['action']) : exit(json_encode(get_state_info(1099)));
	switch($action){
		case 'add_collect':
			echo json_encode(add_collect());
			break;
		case 'cancel_collect':
			echo json_encode(cancel_collect());
			break;
		case 'get_collected_list':
			echo json_encode(get_collected_list());
			break;		
	}

	function add_collect(){
		global $collect,$user_info,$msg_total;
		if(!isset($_REQUEST['collect_type'])) return get_state_info(1099);	
		if(!isset($_REQUEST['dynamic_id'])) return get_state_info(1099);
		if($collect -> get_collect_exists($user_info['id'],clear_gpq($_REQUEST['collect_type']),intval($_REQUEST['dynamic_id']))){
			return get_state_info(1062);
		};
		if($collect -> add_collect($user_info['id'],intval($_REQUEST['dynamic_id']),clear_gpq($_REQUEST['collect_type']))){
			if($result = $collect -> get_collect_list_by_user($user_info['id'])){
				$user_msg_total['collect'] = count($result);
				$msg_total -> update_user_msg_total($user_info['id'],$user_msg_total);
				return get_state_info(1000);
			}else{
				return get_state_info(1014);
			}
		}else{
			return get_state_info(1059);//收藏失败
		}

	}

	function cancel_collect(){
		global $collect,$user_info,$msg_total;
		if(!isset($_REQUEST['collect_id'])) return get_state_info(1099);
		if($collect -> delete_collect($user_info['id'],intval($_REQUEST['collect_id']))){
			if($result = $collect -> get_collect_list_by_user($user_info['id'])){
				$user_msg_total['collect'] = count($result);
				$msg_total -> update_user_msg_total($user_info['id'],$user_msg_total);
				return get_state_info(1000);
			}else{
				$user_msg_total['collect'] = 0;
				$msg_total -> update_user_msg_total($user_info['id'],$user_msg_total);
				return get_state_info(1000);
			}
		}else{
			return get_state_info(1060);//取消收藏失败
		}
	}

	function get_collected_list(){
		global $collect,$user_info;
		$collected_arr['user'] = array();
		$collected_arr['org'] = array();
		$collected_arr['recruit'] = array();
		$collect_list = $collect -> get_collect_list_by_user($user_info['id']);
		if($collect_list){
			foreach($collect_list as $collect_info){
				if($collect_info['type'] == 'user'){
					$collected_arr['user'][] = $collect_info['dynamic_id'];
				}else if($collect_info['type'] == 'org'){
					$collected_arr['org'][] = $collect_info['dynamic_id'];
				}else{
					$collected_arr['recruit'][] = $collect_info['dynamic_id'];
				}
			}
		}
		return $collected_arr;
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