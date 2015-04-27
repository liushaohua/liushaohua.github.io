<?php
  header("Content-type:textml;charset=utf-8");
  require_once("./includes/common_inc.php");
  $user = new user();
  if(isset($_COOKIE['hyq_user_info'])){
    $user_cookie = $user -> get_cookie_user_info();
    $user_type = $user_cookie['user_type'];
  }else{
    $user_type = 'nologin';
  }
  $smarty -> assign('user_type',$user_type);
  $smarty -> assign('nav_main','index');
  $smarty -> display('index.html');                           