<?php
//开发期间打开报错 
error_reporting(E_ALL ^ E_NOTICE);
ini_set( 'display_errors', 'On' );

define('API_DEBUG_URL','http://www.hotyq.com:8888/api/mobile/0102/api.php');
define('COMMON_PATH','/export/home/cms/www/common');
define('WWW_ROOT',$_SERVER['DOCUMENT_ROOT']);
//包含必须的函数及常量文件
require_once COMMON_PATH.'/const_inc.php';						//常量
require_once COMMON_PATH.'/config_inc.php';					//数据库配置文件
require_once COMMON_PATH.'/function_lib.php';					//常用函数 
require_once COMMON_PATH.'/mysql_db_lib.php';					//数据库基础类
require_once COMMON_PATH.'/useCache.class.php';				//缓存基础类
require_once COMMON_PATH.'/dbcache.class.php'; 				//缓存和数据库接口类
require_once COMMON_PATH.'/useSmarty.class.php';				//模板类文件引用
require_once COMMON_PATH.'/message.class.php';				//模板类文件引用
require_once COMMON_PATH."/album.class.php"; 
require_once COMMON_PATH."/user.class.php"; 
require_once COMMON_PATH."/photo.class.php"; 
require_once COMMON_PATH."/check_code.class.php"; 
require_once COMMON_PATH."/check_code_redis.class.php"; 
require_once COMMON_PATH."/redis.class.php";
require_once COMMON_PATH."/redis_find.class.php"; 
require_once COMMON_PATH."/state_code.php";
require_once COMMON_PATH."/base.class.php";
require_once COMMON_PATH."/collect.class.php";
require_once COMMON_PATH."/recruit.class.php";
require_once COMMON_PATH."/userprofile.class.php";
require_once COMMON_PATH."/orgprofile.class.php";
require_once COMMON_PATH."/service.class.php";
require_once COMMON_PATH."/apply.class.php";
require_once COMMON_PATH."/recruit.class.php";
require_once COMMON_PATH."/invite.class.php";
require_once COMMON_PATH."/user_msg_total.class.php";
require_once COMMON_PATH."/rongyunApi.class.php";
require_once COMMON_PATH."/rongyun.class.php";
require_once COMMON_PATH."/find_user.class.php";
require_once COMMON_PATH."/find_org.class.php";
require_once COMMON_PATH."/find_recruit.class.php";
require_once COMMON_PATH."/solr.class.php";
require_once COMMON_PATH."/solr_user.class.php";
require_once COMMON_PATH."/solr_org.class.php";
require_once COMMON_PATH."/solr_recruit.class.php";
require_once COMMON_PATH."/search_user.class.php";
require_once COMMON_PATH."/search_org.class.php";
require_once COMMON_PATH."/search_recruit.class.php";
require_once WWW_ROOT."/api/mobile/0102/api_config.php";
require_once WWW_ROOT."/api/mobile/0102/inc_account.php";
require_once WWW_ROOT."/api/mobile/0102/inc_home.php";
require_once WWW_ROOT."/api/mobile/0102/inc_find.php";
require_once WWW_ROOT."/api/mobile/0102/inc_search.php";
require_once WWW_ROOT."/api/mobile/0102/inc_profile.php";
require_once COMMON_PATH."/identity.class.php";
//require_once WWW_ROOT."/api/mobile/0102/inc_profile_1.php";
//require_once WWW_ROOT."/api/mobile/0102/inc_profile_fan.php";
//require_once WWW_ROOT."/api/mobile/0102/inc_mywork_stx.php";
require_once WWW_ROOT."/api/mobile/0102/inc_mywork.php";


$flash = 0;
if(@$_GET['flash']=='yes'){
        $flash = 1;
}