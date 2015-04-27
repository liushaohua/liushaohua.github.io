<?php 
//开发期间打开报错 
error_reporting(E_ALL);
ini_set( 'display_errors', 'On' );
//类库目录字段
define('COMMON_PATH','/export/home/cms/www/common');
//包含必须的函数及常量文件
require_once(COMMON_PATH.'/const_inc.php');						//常量
require_once(COMMON_PATH.'/config_inc.php');					//数据库配置文件
require_once(COMMON_PATH.'/function_lib.php');					//常用函数 
require_once(COMMON_PATH.'/mysql_db_lib.php');					//数据库基础类
require_once(COMMON_PATH.'/useCache.class.php');				//缓存基础类
require_once(COMMON_PATH.'/dbcache.class.php'); 				//缓存和数据库接口类
require_once(COMMON_PATH.'/useSmarty.class.php');				//模板类文件引用
//require_once(COMMON_PATH.'/message.class.php');				//模板类文件引用
require_once(COMMON_PATH.'/user.class.php');					//用户逻辑类
require_once(COMMON_PATH.'/userprofile.class.php');			//模板类文件引用
require_once(COMMON_PATH.'/orgprofile.class.php');			//模板类文件引用


$flash = 0;
if(@$_GET['flash']=='yes'){
        $flash = 1;
}

 

?>
