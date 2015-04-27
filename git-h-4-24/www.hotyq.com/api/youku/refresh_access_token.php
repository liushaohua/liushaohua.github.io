<?php


/*****YoukuUpload SDK*****/
header('Content-type: text/html; charset=utf-8');
include("include/Http.class.php");

$client_id = "c661d967548331de"; // Youku OpenAPI client_id
$client_secret = "035093f8221ddcd6a0d933620fc48c1c"; //Youku OpenAPI client_secret
$url = "https://openapi.youku.com/v2/oauth2/token";
$params['access_token'] = "d7ec20ce3e9046e71f833d3170fc1926"; 
$params['refresh_token'] = "5160dbae5667e650468d184fbf5f6f60";
$params = array("client_id"=>$client_id, 
                            "client_secret"=>$client_secret, 
                            "grant_type"=>"refresh_token", 
                            "refresh_token"=>$params['refresh_token']);
$token_data = json_decode(Http::post($url, $params));
writeRefreshFile("refresh.txt", $token_data);
readRefreshFile("refresh.txt");
function writeRefreshFile($refresh_file,$refresh_json_result) {
    $file = @fopen($refresh_file, "w");
    if (!$file){
        echo "Could not open " . $refresh_file . "!\n";
    }else {
        $refreshInfo = json_encode($refresh_json_result);
        $fw = @fwrite($file, $refreshInfo);
        if (!$fw) echo "Write refresh file fail!\n";
        fclose($file);
    }
}
function readRefreshFile($refresh_file) {
    var_dump(date("Y-m-d H:i:s",fileatime($refresh_file)));
    var_dump(time() - fileatime($refresh_file));
    $file = @fopen($refresh_file, "r");
    if ($file) {
        $refreshInfo = json_decode(trim(fgets($file)));
        fclose($file);
        var_dump($refreshInfo);
    }
}

?>
