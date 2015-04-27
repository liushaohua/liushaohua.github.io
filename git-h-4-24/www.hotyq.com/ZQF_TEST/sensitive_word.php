<?php
require_once('../includes/common_home_inc.php');
$fopen=fopen("sensitive_word.html","rb");
while(!feof($fopen)){
	$word = trim(fgetss($fopen));
	echo $sql = "INSERT INTO hyq_sensitive_words SET sensitive_word = '{$word}'";
	$db_hyq_write -> query($sql);
}
fclose($fopen);
?>