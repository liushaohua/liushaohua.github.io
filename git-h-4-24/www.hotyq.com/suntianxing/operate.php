<?php 
	header("Content-type: text/html; charset=utf-8");
	require_once ("../includes/common_inc_test.php");
	require_once ("../../common/photo.class.php");
	require_once ("./message.class.php");
	require_once('./message_redis.class.php');
	require_once('./redis.class.php');
	$photo = new photo;
	$message = new message;
	var_dump($_FILES);    //测试
	//exit;
	$user = new user;
	$user_cookie = $user -> get_cookie_user_info();
	$user = $user_cookie['userid'];
	//$user = 1103;			//发送方id
	@$friend = clear_gpq($_REQUEST['rec_id']);			//接收方id
	$type = 1;		//私信类型——图片
	$error = '';
	$max_file = 5*1024*1024;
	if (isset($_POST["submit"])) {
		//获取文件信息  和临时文件路径 判断
		$file_info = $_FILES['image'];
		$scene ='message';
		$error_info = $photo -> check_upload_photo($file_info);

		if(!empty($file_info) && $error_info == 1000){
			$hash_dir = $photo -> get_hash_dir($scene);
			$newname = $photo -> create_newname($photo -> get_suffix($file_info['name']));
			$path_url =  $hash_dir.'/'.$newname;
			$up_result = $photo -> upload_photo($file_info['tmp_name'],$path_url);
			
			if($up_result){
				$result = $photo -> save_on_upyun($path_url);
				$state_code = $message -> add_message($user, $friend, $path_url, $type);
				if($state_code == 1000){
					echo '私信发送成功';
				}else{
					echo $state_code;
					exit('发送失败!');
				}
			}
		}else{
			 /* 上传图片错误处理 */
			 echo $error_info;
		}	
	}
	
	
	
	
	
?>