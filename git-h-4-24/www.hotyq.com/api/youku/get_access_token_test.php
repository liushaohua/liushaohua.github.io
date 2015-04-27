<?php

/*****YoukuUpload SDK*****/
header('Content-type: text/html; charset=utf-8');
include("include/Http.class.php");

// $client_id = "c661d967548331de"; // Youku OpenAPI client_id
// $client_secret = "035093f8221ddcd6a0d933620fc48c1c"; //Youku OpenAPI client_secret

$client_id = "b29bb4e411682f81"; // Youku OpenAPI client_id
$client_secret = "afea957647ea4d431a7b414c489312c5"; //Youku OpenAPI client_secret

$url = "https://openapi.youku.com/v2/oauth2/token";
$code = $_GET["code"];
$params = array("client_id"=>$client_id, 
                            "client_secret"=>$client_secret, 
                            "grant_type"=>"authorization_code", 
                            "code"=>$code,
                            "redirect_uri"=>"/api/youku/get_access_token_test.php");
var_dump(Http::post($url, $params));
exit;

/*
**The way with username and password applys to the partner level clients!
**Others may use access_token to upload the video file.
**In addition, refresh_token is to refresh expired access_token. 
**If it is null, the access_token would not be refreshed.
**You may refresh it by yourself.
**Like "http://open.youku.com/docs/api/uploads/client/english" for reference.
*/

$params['username'] = ""; //Youku username or email
$params['password'] = md5(""); //Youku password

set_time_limit(0);
ini_set('memory_limit', '128M');
$youkuUploader = new YoukuUploader($client_id, $client_secret);
$file_name = "/export/home/cms/www/www.hotyq.com/test/youku/hehe.mp4"; //video file
try {
    $file_md5 = @md5_file($file_name);
    if (!$file_md5) {
        throw new Exception("Could not open the file!\n");
    }
}catch (Exception $e) {
    echo "(File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage();
    return;
}
$file_size = filesize($file_name);
$uploadInfo = array(
		"title" => "", //video title
		"tags" => "", //tags, split by space
		"file_name" => $file_name, //video file name
		"file_md5" => $file_md5, //video file's md5sum
		"file_size" => $file_size //video file size
);
$progress = true; //if true,show the uploading progress 
$youkuUploader->upload($progress, $params,$uploadInfo); 
?>
