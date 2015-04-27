<?php
session_set_cookie_params(1800 , '/', '.hotyq.com');
session_start();
require ('../includes/common_inc.php');

$check_code = new check_code();
$check_code = $check_code -> entry();
// $smarty -> assign('code',$check_code);	
// $smarty -> display("account/code.html");
