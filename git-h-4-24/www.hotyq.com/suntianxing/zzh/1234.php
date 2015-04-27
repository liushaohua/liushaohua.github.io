<?php

	header("content-type: text/html; charset=utf-8");
	session_start();
	//require_once ('../../../common/orgprofile.class.php');
	
	//$orgprofile = new orgprofile();
	//$orgprofile -> add_artist();
	
	$smarty -> display("suntianxing/user_and_org.html");
	// $arr = array( 
	// array('name' => '3', 'age' => 61, 'sex' => '男'), 
	// array('name' => '2', 'age' => 19, 'sex' => '男'), 
	// array('name' => '9', 'age' => 43, 'sex' => '男'), 
	// array('name' => '6', 'age' => 50, 'sex' => '男') 
	// ); $arr = array( array('name' => '3', 'age' => 61, 'sex' => '男'), array('name' => '2', 'age' => 19, 'sex' => '男'), array('name' => '9', 'age' => 43, 'sex' => '男'), array('name' => '6', 'age' => 50, 'sex' => '男') ); 
	// $age = 0; // foreach ($arr as $key => $val) { 
	// if ($val['age'] > $age) { 
	// $age = $val['age']; 
	// $new_arr = $val; 
	// } 
	// } 
	// var_dump($new_arr); 
	$result = array_sort($arr, 'age', SORT_DESC);
	 var_dump($result); 
	 function array_sort($array, $on, $order=SORT_ASC) { 
	 	$new_array = array(); 
	 	$sortable_array = array(); 
	 	if (count($array) > 0) {
	 		foreach ($array as $k => $v){ 
	 			if (is_array($v)) { 
	 				foreach ($v as $k2 => $v2) {
		 				if ($k2 == $on) { 
		 				 	$sortable_array[$k] = $v2; 
		 				 } 
	 				} 
	 			}else{	 				
	 				$sortable_array[$k] = $v;
	 			} 
	 		} 

	 			switch ($order) {
	 				case SORT_ASC: asort($sortable_array);
	 			  	break;
	 			    case SORT_DESC: arsort($sortable_array); 
	 			    break;
	 			} 
	 			foreach ($sortable_array as $k => $v) { 
	 			  	  $new_array[$k] = $array[$k];
	 			} 
	 	} 
	 			return $new_array;

} 
