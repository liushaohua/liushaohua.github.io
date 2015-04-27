<?php
header("content-type: text/html; charset=utf-8");
require_once ('../includes/common_inc.php');
require_once (COMMON_PATH.'/base.class.php');

$base = new base();
echo '<pre>';
print_r( $base -> getPaging("/user/45724",6,20,2));

 
?>