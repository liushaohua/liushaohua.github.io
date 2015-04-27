<?php
/*****YoukuUpload SDK*****/
header('Content-type: text/html; charset=utf-8');
include("include/Http.class.php");

$client_id = "b29bb4e411682f81"; // Youku OpenAPI client_id
$client_secret = "afea957647ea4d431a7b414c489312c5"; //Youku OpenAPI client_secret

// $client_id = "14164c85f31390cc"; // Youku OpenAPI client_id
// $client_secret = "df39dd687a309355fa429af7cc1a751a"; //Youku OpenAPI client_secret

$ACCESS_TOKEN_URL = "https://openapi.youku.com/v2/oauth2/token";
$REFRESH_FILE = "refresh_test.txt";

if(time() - fileatime($REFRESH_FILE) > 86400*29){ # 29天
	$refresh_info = readRefreshFile();
	$params = array("client_id"=>$client_id, 
                            "client_secret"=>$client_secret, 
                            "grant_type"=>"refresh_token", 
                            "refresh_token"=>$refresh_info->refresh_token);
	$token_data = json_decode(Http::post($ACCESS_TOKEN_URL, $params));
	writeRefreshFile($REFRESH_FILE, $token_data);
}
unset($refresh_info);
$refresh_info = readRefreshFile($REFRESH_FILE);
$refresh_token = $refresh_info->refresh_token;
$access_token = $refresh_info->access_token;
function refresh_token($refresh_info){
	global $REFRESH_FILE, $ACCESS_TOKEN_URL;
	$params = array("client_id"=>$client_id, 
                            "client_secret"=>$client_secret, 
                            "grant_type"=>"refresh_token", 
                            "refresh_token"=>$params['refresh_token']);
}
function writeRefreshFile($refresh_file,$refresh_json_result) {
	$file = @fopen($refresh_file, "w");
	if (!$file){
		echo "Could not open " . $refresh_file . "!\n";
	}else {
		$refresh_info = json_encode($refresh_json_result);
		$fw = @fwrite($file, $refreshInfo);
		if (!$fw) echo "Write refresh file fail!\n";
		fclose($file);
		return $refresh_info;
	}
}
function readRefreshFile($refresh_file) {
	$file = @fopen($refresh_file, "r");
	if ($file) {
		$refresh_info = json_decode(trim(fgets($file)));
		fclose($file);
		return $refresh_info;
	}
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>YOUKU OPENAPI File Upload Demo</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <link href="http://open.youku.com/assets/lib/bootstrap2.1.0/css/bootstrap.css" rel="stylesheet">
        <link href="http://open.youku.com/assets/lib/bootstrap2.1.0/css/bootstrap-responsive.css" rel="stylesheet">
		<style> 
			.uploadfile{width:150px;height: 14px;vertical-align: top;} 
		</style> 
        <script src="http://open.youku.com/assets/lib/jquery-1.8.1.min.js"></script>
		<script src="http://open.youku.com/assets/lib/uploadjs.php"></script>
		<script>
			//document.domain = "youku.com";
			var USE_STREAM_UPLOAD = true;
			jQuery(document).ready(function(){ 
				//oauth_opentype:iframe,newWindow,currentWindow
				//iframe
				var param = {client_id:"<?=$client_id?>",access_token:"<?=$access_token?>",oauth_opentype:"iframe",oauth_redirect_uri:"/api/youku/oauth_result.html",oauth_state:"",completeCallback:"uploadComplete",categoryCallback:"categoryLoaded"};
					youkuUploadInit(param);
					/*
					if(window.localStorage){
						localStorage.setItem("ResumeBrokenTransferData",JSON.stringify({name:"shiwei"}));
						var obj = JSON.parse(localStorage.getItem("ResumeBrokenTransferData"));
						console.log(obj.name);
					}else{
						alert('您的浏览器不支持断点续传，请重新手动上传');
					}
					*/
			});

			function uploadComplete(data){
				alert("videoid="+data.videoid+";title="+data.title);
				uploadagain();
			}
			
			function categoryLoaded(data){
				if(data.categories) {
					   var tpl = '';
					   for (var i=0; i<data.categories.length; i++) {
							if(data.categories[i].term == 'Autos'){
								tpl += '<option value="' + data.categories[i].term + '" selected>' + data.categories[i].label + '</option>'; 
							}else{
								tpl += '<option value="' + data.categories[i].term + '" >' + data.categories[i].label + '</option>'; 
							}
					   }
					   $("#category-node").html(tpl);
					}
			}
		</script>
    </head>
    <body>
		<div id="youku-upload">
			<div class="container">
				<form class="well form-horizontal" name="video-upload">
					<fieldset><div class="control-group">
						<label class="control-label" for="spanSWFUploadButton">选择文件：</label>
							<div id="uploadControl" class="controls">

							</div>
							</div> 
							<div class="control-group">
								<label class="control-label" for="input01">标题：</label>
								<div class="controls">
									<input type="text" class="input-xlarge" id="input01" name="title">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="textarea">简介：</label>
								<div class="controls">
									<textarea class="input-xlarge" id="textarea" rows="3" name="description"></textarea>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="input02">标签：</label>
								<div class="controls">
									<input type="text" class="input-xlarge" id="input02" name="tags">
										<span class="help-inline"></span>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="category-node">类别：</label>
								<div class="controls">
									<select id="category-node" name="category" ></select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label">版权所有</label>
							<div class="controls">
								<label class="radio inline">
									<input type="radio" name="copyright_type" id="copyright_type2" value="original" checked="">原创</label>
								<label class="radio inline"><input type="radio" name="copyright_type" id="copyright_type1" value="reproduced">转载</label>
							</div>
						</div>
							<div class="control-group">
								<label class="control-label">视频权限</label>
								<div class="controls">
									<label class="radio inline">
										<input type="radio" name="public_type" id="public_type1" value="all" checked="">公开
									</label>
									<label class="radio inline">
										<input type="radio" name="public_type" id="public_type2" value="friend">仅好友
									</label>
									<label class="radio inline">
										<input type="radio" name="public_type" id="public_type3" value="password">输入密码观看 
									</label>
									<label class="radio inline" style="display:none" id="passwrod">
										<input type="text" class="input "name="watch_password">
									</label>
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" class="btn btn-primary start" id="btn-upload-start">
									<i class="icon-upload icon-white"></i>
									<span>开始上传</span>
								</button>
							</div>
							</fieldset>
						</form>
						<div class="row" >
							<div class="span5" id="upload-status-wraper" ></div>
						</div>
						<br>
						<div class="well"><h3>说明</h3><ul><li>最大支持上传<strong>1 GB</strong> 视频文件</li><li>允许上传的视频格式为：wmv,avi,dat,asf,rm,rmvb,ram,mpg,mpeg,3gp,mov,mp4,m4v,dvix,dv,dat,</br>mkv,flv,vob,ram,qt,divx,cpk,fli,flc,mod。不符合格式的视频将会被丢弃，请确保视频格式的正确性，避免上传失败</li></ul>
						</div>
					</div>
					<div id="complete"></div>
					<div id="login" style="width:100%;height:100%;position:fixed;z-index:999;left:0px;top:0px;overflow:hidden;display:none;">
					</div>
    </body>
</html>
