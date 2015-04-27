<?php
	header("content-type: text/html; charset=utf-8");
	require_once ('./includes/common_inc.php');
	//服务详情
	$service = new service();
	$service_list = $service -> get_service($flash);
	foreach ($service_list as $k=>$v) {
		$service_arr[$v['id']] = $v['name'];
		if($v['parent_id'] == 0){
			$service_1_arr[] = $v; 
		} 
	}
	
	foreach ($service_1_arr as $k=>$v){
		$service_1_id = $v['id'];
		$r = $service -> get_children_service($service_1_id, $flash);
		if(is_array($r) && $r){	
			foreach ($r as $kk=>$vv){
				$service_all[$service_1_id][$kk]['id'] = $vv['id'];
				$service_all[$service_1_id][$kk]['service_1_id'] = $v['id'];
				$service_all[$service_1_id][$kk]['name'] = $vv['name'];
				$service_all[$service_1_id][$kk]['sort'] = $vv['sort'];
				$service_all[$service_1_id][$kk]['name_r'] = urlencode($vv['name']);
				$service_all[$service_1_id][$kk]['parent_id'] = $service_1_id;
				$rr = $service -> get_children_service($vv['id'], $flash);
				if(is_array($rr) && $rr){
					foreach ($rr as $kkk=>$vvv){
						$service_all[$service_1_id][$kk]['children'][$kkk]['service_2_id'] = $vv['id'];
						$service_all[$service_1_id][$kk]['children'][$kkk]['id'] = $vvv['id'];
						$service_all[$service_1_id][$kk]['children'][$kkk]['name'] = $vvv['name'];
						$service_all[$service_1_id][$kk]['children'][$kkk]['name_r'] = urlencode($vvv['name']);
						$service_all[$service_1_id][$kk]['children'][$kkk]['parent_id'] = $vv['id'];
					}
				}
			}
		}
	}
	
	for($i=1;$i<21;$i++){
		foreach ($service_all[$i] as $key=>$value) {
			$sort[] = $value['sort'];
		}
		array_multisort($sort,SORT_ASC,$service_all[$i]);
		unset($sort);
	}
	
	$new_service_arr[$service_arr[1].'、'.$service_arr[6]] = array_merge($service_all[1], $service_all[6]);
	
	$new_service_arr[$service_arr[2].'、'.$service_arr[19]] = array_merge($service_all[2], $service_all[19]);
	
	$new_service_arr[$service_arr[3].'、'.$service_arr[15]] = array_merge($service_all[3], $service_all[15]);
	
	$new_service_arr[$service_arr[4].'、'.$service_arr[7]] = array_merge($service_all[4], $service_all[7]);
	
	$new_service_arr[$service_arr[5].'、'.$service_arr[9]] = array_merge($service_all[5], $service_all[9]);
	
	$new_service_arr[$service_arr[8].'、'.$service_arr[14]] = array_merge($service_all[8], $service_all[14]);
	
	$new_service_arr[$service_arr[11].'、'.$service_arr[10]] = array_merge($service_all[11], $service_all[10]);
	
	$new_service_arr[$service_arr[12].'、'.$service_arr[16]] = array_merge($service_all[12], $service_all[16]);
	
	$new_service_arr[$service_arr[13].'、'.$service_arr[20]] = array_merge($service_all[13], $service_all[20]);
	
	$new_service_arr[$service_arr[17].'、'.$service_arr[18]] = array_merge($service_all[17], $service_all[18]);
	
	
	//print_r($new_service_arr);
	
	
	$smarty -> assign('service', $new_service_arr);
	$smarty -> assign('active_status',"index");                                              
	$smarty -> display('index1.html');                                                         
?>