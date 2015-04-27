<?php 
//开发期间打开报错 
//error_reporting(E_ALL ^E_NOTICE);
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
require_once(COMMON_PATH.'/base.class.php');				    //模板类文件引用
require_once(COMMON_PATH.'/user.class.php');
require_once(COMMON_PATH.'/redis.class.php');
require_once(COMMON_PATH.'/message_redis.class.php');
require_once(COMMON_PATH.'/apply.class.php');
require_once(COMMON_PATH.'/recruit.class.php');
require_once(COMMON_PATH.'/invite.class.php');
require_once(COMMON_PATH.'/check_code.class.php');
require_once(COMMON_PATH.'/photo.class.php');			        //用户逻辑类
require_once(COMMON_PATH.'/state_code.php');			        //网站提示信息
require_once(COMMON_PATH.'/userprofile.class.php');			//模板类文件引用
require_once(COMMON_PATH.'/orgprofile.class.php');			//模板类文件引用
require_once(COMMON_PATH.'/userprofile.class.php');
require_once(COMMON_PATH.'/recruit.class.php');
require_once(COMMON_PATH.'/album.class.php');
require_once(COMMON_PATH.'/collect.class.php');
require_once(COMMON_PATH.'/message.class.php');
require_once(COMMON_PATH.'/find_user.class.php');
require_once(COMMON_PATH.'/redis.class.php');
require_once(COMMON_PATH.'/redis_find.class.php');
require_once(COMMON_PATH.'/service.class.php');
$flash = 0;
if(@$_GET['flash']=='yes'){
        $flash = 1;
}
?>