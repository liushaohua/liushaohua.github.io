<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_inc.php');
	require_once(COMMON_PATH.'/find_recruit.class.php');
	require_once(COMMON_PATH.'/redis.class.php');
	require_once(COMMON_PATH.'/redis_find.class.php');
	$find_recruit = new find_recruit();
	$recruit = new recruit;
	$base = new base();
	$userprofile = new userprofile;
	$user = new user();
	$orgprofile = new orgprofile();
	//查找所有的城市　按拼音首字母排序　放进不同数组　遍历
	$city_all = $base -> get_city_list($flash);
	$citys['ABCDEF'] = array();
	$citys['GHIJ'] = array();
	$citys['KLMN'] = array();
	$citys['OPQR'] = array();
	$citys['STUV'] = array();
	$citys['WXYZ'] = array();
	foreach($city_all as $city_info){
		if(in_array($city_info['spell'][0],array('a','b','c','d','e','f'))){
			$citys['ABCDEF'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('g','h','i','j'))){
			$citys['GHIJ'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('k','l','m','n'))){
			$citys['KLMN'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('o','p','q','r'))){
			$citys['OPQR'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('s','t','u','v'))){
			$citys['STUV'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('w','x','y','z'))){
			$citys['WXYZ'][$city_info['id']] = $city_info;
		}
	}
	$smarty -> assign('citys',$citys);
	//0 生成需要处理的数组
		//不同类型下  招募封面缺省图
		$smarty -> assign('img_path_arr',$COMMON_CONFIG["RECRUIT_DEFAULT_IMG"]);
		//获取 城市名字 对应数组
		$city_list = $base -> get_city_list($flash);
		foreach($city_list as $v){
			$city_name_arr[$v['id']] = $v['cname'];
		}
		//获取 服务名字 对应数组
		$service_name_arr = $base -> get_service_list($flash);
	//1 生成四维数组雏形  前两维
	$result = $recruit -> get_recruit_type_list($flash);
	foreach($result as $v){
		$arr[$v['type_group']][$v['type']] = $v['id'];//本来放该type对应的结果集   放id  之后替换成类型是该type_id的结果集
	}
	$find_recruit_key = array(); #redis条件
	//如果带了条件(城市)  需要根据城市查询结果集------
	$city_id = 0;
	if( isset($_REQUEST['cid']) && !empty($_REQUEST['cid']) ){
		$_REQUEST['cid'] = intval($_REQUEST['cid']);
		$find_recruit_key['city'] = $find_recruit -> get_find_key('city',$_REQUEST['cid']);
		$city_id = $_REQUEST['cid'];
	}
	$smarty -> assign('city_id',$city_id);
	//1 查招募结果集  根据redis查询
	foreach($arr as $k => $v){#第一维
		foreach($v as $k0 => $v0){#第二维
			//echo $k0,$v0;
			$find_recruit_key['type'] = $find_recruit -> get_find_key('type',$v0);#不同类型写进redis条件
			$find_recruit_res = $find_recruit -> get_recruit_list_by_sinter($find_recruit_key,'1','6');
			$recruit_id_list = $find_recruit_res['list'];
			//根据id查出每一个招募的详情  处理好  放进数组中  在编制
			if(count($recruit_id_list) > 0){
				$recruit_list = array();#第三维
				foreach($recruit_id_list as $k1 => $v1){//$v1--招募id
					$recruit_info = $recruit -> get_recruit_info($v1,$flash);#第四维
					$recruit_info['city_name'] = $city_name_arr[$recruit_info['city_id']];#处理城市
					$recruit_info['interview_end_time'] =  date( "m月d日", strtotime($recruit_info['interview_end_time']) );#处理截止时间
					//处理二级服务
					$recruit_info['recruit_service_list'] = $recruit -> get_service_list_by_recruit($recruit_info['id'],$flash);#可能多个一级二级
					foreach($recruit_info['recruit_service_list'] as $k2 => $v2){
						$recruit_info['recruit_service_list'][$k2]['service_2_name'] = $service_name_arr[$v2['service_2_id']];#获取每一项二级服务name
					}
					$recruit_list[] = $recruit_info;
				}
				$arr[$k][$k0] = $recruit_list;
			}else{
				unset($arr[$k][$k0]);
			}
		}
	}
	//2 已经是四维数组的形式  处理信息也完了  去掉为空的数组项
	foreach($arr as $k => $v){//第一维度
		if(count($v) == 0){
			unset($arr[$k]);
		}
	}
	$smarty -> assign('arr',$arr);
	//3 城市块
	$chief_city_list = $base -> get_chief_city_list($flash);
	foreach($chief_city_list as $k => $v){
		$chief_city_list[$k]['city_name'] = $city_name_arr[$v['des']];
	}
	$json_chief_city_list = json_encode($chief_city_list);
	//var_dump($json_chief_city_list);exit;
	$smarty -> assign('chief_city_list',$chief_city_list);
	$smarty -> assign('json_chief_city_list',$json_chief_city_list);
	$smarty -> assign('active_status','recruit_index');
	//var_dump($chief_city_list);exit;
	$smarty -> assign('active_status','recruit_index');
	$smarty -> display("recruit/index.html");
?>