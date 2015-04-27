<?php 
	header("Content-type: text/html; charset=utf-8");
	require ("../includes/common_inc.php");
	
	$photo = new photo;
	$message = new Message;
	//var_dump($_FILES);
	//exit;
	$user = 1314;			//发送方id
	$friend = clear_gpq($_REQUEST['rec_id']);			//接收方id
	$type = 1;		//私信类型——图片
	$error = '';
	$max_file = 5*1024*1024;
	if (isset($_POST["submit"])) {
		//获取文件信息  和临时文件路径 判断
		$file_info = $_FILES['image'];
		$scene ='message';
		$error_info = $photo -> check_upload_file($file_info);

		if(!empty($file_info) && $file_info["error"] == 0){
			$up_result = $photo -> upload_photo($scene,$file_info);
			$path_url =  '/'.$up_result [0].'/'.$up_result [1];
			if($up_result){
				$result = $photo -> save_on_upyun($path_url);
				$state_code = $message ->	add_message($user, $friend, $path_url, $type);
				if($state_code == 1000){
					echo '私信发送成功';
				}else{
					echo $state_code;
					exit('发送失败!');
				}
			}
		}	
	}
	
	
	
	
	
?>