<?php
	header("content-type: text/html; charset=utf-8");
  	require_once('../../includes/common_inc.php');
	require_once(COMMON_PATH.'/sensitive_words.class.php');
	
	 $sensitive_words = new sensitive_words();
	 $result = $sensitive_words -> get_sensitive_words_list($flash);
	//var_dump($result);
	$input_text = '出售猎枪的风热反出售54式手枪对反对法销售狙击枪付电费惹人出售军用手狗大风新博彩通大风热污染反对法士大夫士大夫十分';
	$input_text2 = $sensitive_words -> filter_sensitive_words($input_text,$result);
	echo $input_text2;exit;
	
	
	
	foreach($result as $v){
		echo $v['sensitive_word'];
		$num = substr_count($input_text,$v['sensitive_word']);//在文本中 找该项 有几个
		echo $v['sensitive_word'];
		echo $input_text.'<br>';
		echo $v['sensitive_word'].'<br>';
		echo strlen($v['sensitive_word']).'<br>';
		$num = substr_count($input_text,$v['sensitive_word']);//在文本中 找该项 有几个
		var_dump($num);echo '<br>';
		if($num > 0){
			$input_text = str_replace($v['sensitive_word'],'**',$input_text);
		}
		//echo $input_text.'<br>';
	}
	echo $input_text;exit;
	
	
	$result = $sensitive_words -> get_sensitive_words_list($flash);
	//var_dump($result);
	$input_text = '出售猎枪dfdfdferf 反对反对法销售狙击枪付电费惹人出售军用手狗大风大风热污染反对法士大夫士大夫十分';
	
	
?>