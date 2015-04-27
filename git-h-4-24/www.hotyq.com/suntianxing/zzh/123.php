<?php

	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../../includes/common_inc.php');

	
	
	
	echo date('Y');
/*	$base = new base();
	$p = $base->get_province_list();
	$c = $base ->get_city_list();
	$d = $base ->get_district_list();
	//var_dump($c);
	$new_arr = array();
	$new_arr1 = array();
	foreach ($p as $key => $val) {
			$new_arr[$key]['id'] = $val['id'];
			$new_arr[$key]['pname'] = $val['pname'];
			foreach($c as $k => $v){
					if($val['id'] == $v['pid']){
							$new_arr[$key]['city'][$k]['id'] = $v['id'];			
							$new_arr[$key]['city'][$k]['cname'] = $v['cname'];
							
							foreach($d as $kk=>$vv){
								if($v['id'] == $vv['cid']){
									//$new_arr[$key]['city'][$k]['district']['id'] = $vv['id'];
									$new_arr[$key]['city'][$k]['district'][$vv['id']] = $vv['dname'];
								}
								
							}
				
					}
			}
	}

	print_r($new_arr);
*/	
	// print_r(array_slice($p,0,true));

	// $foo[1]['a']['xx'] = 'bar 1';
	// $foo[1]['b']['xx'] = 'bar 2';
	// $foo[2]['a']['bb'] = 'bar 3';
	// $foo[2]['a']['yy'] = 'bar 4';
	// $foo[3]['c']['dd'] = 'bar 3';
	// $foo[3]['f']['gg'] = 'bar 3';
	// $foo['info'][1] = 'bar 5';
// function array_search_re($needle, $haystack, $a=0, $nodes_temp=array()){
	// global $nodes_found;
	// $a++;
	// foreach ($haystack as $key1=>$value1) {
		// $nodes_temp[$a] = $key1;
		// if (is_array($value1)){   
		  // array_search_re($needle, $value1, $a, $nodes_temp);
		// }
		// else if ($value1 === $needle){
		  // $nodes_found[] = $nodes_temp;
		// }
	// }
	// return $nodes_found;
// }	
$asd = date('Y')+1;

	$res['finish_year'] = array('max' => "{$asd}",'min' => '1977');
	var_dump($res);	
	
	