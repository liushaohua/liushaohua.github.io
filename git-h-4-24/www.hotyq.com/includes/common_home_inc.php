<?php 
// 个人中心页面（验证登陆）
error_reporting(E_ALL);
ini_set( 'display_errors', 'On' );
//类库目录字段
define('COMMON_PATH','/export/home/cms/www/common');
//根目录
define('WWW_ROOT',$_SERVER['DOCUMENT_ROOT']);
//包含必须的函数及常量文件
require_once(COMMON_PATH.'/const_inc.php');						//常量
require_once(COMMON_PATH.'/config_inc.php');					//数据库配置文件
require_once(COMMON_PATH.'/function_lib.php');					//常用函数 
require_once(COMMON_PATH.'/mysql_db_lib.php');					//数据库基础类
require_once(COMMON_PATH.'/useCache.class.php');				//缓存基础类
require_once(COMMON_PATH.'/dbcache.class.php'); 				//缓存和数据库接口类
require_once(COMMON_PATH.'/useSmarty.class.php');				//模板类文件引用
require_once(COMMON_PATH.'/base.class.php');				//模板类文件引用
require_once(COMMON_PATH.'/user.class.php');
require_once(COMMON_PATH.'/check_code.class.php');
require_once(COMMON_PATH.'/photo.class.php');			//用户逻辑类
require_once(COMMON_PATH.'/state_code.php');			//用户逻辑类
require_once(COMMON_PATH.'/apply.class.php');
require_once(COMMON_PATH.'/recruit.class.php');
require_once(COMMON_PATH.'/invite.class.php');

require_once(COMMON_PATH.'/redis.class.php');				//模板类文件引用
require_once(COMMON_PATH.'/redis_message.class.php');
require_once(COMMON_PATH.'/message_redis.class.php');
require_once(COMMON_PATH.'/message.class.php');
require_once(COMMON_PATH.'/rongyun.class.php');
require_once(COMMON_PATH.'/rongyunApi.class.php');
//require_once(COMMON_PATH.'/photo.class.php');			//用户逻辑类
require_once(COMMON_PATH.'/userprofile.class.php');			
require_once(COMMON_PATH.'/service.class.php');
require_once(COMMON_PATH.'/orgprofile.class.php');			
require_once(COMMON_PATH.'/user_msg_total.class.php');
require_once(COMMON_PATH.'/page.class.php');

require_once(WWW_ROOT.'/includes/home_init.php');			//个人中心初始化




$flash = 0;
if(@$_GET['flash']=='yes'){
        $flash = 1;
}

 

?>