<?php
	header('Content-type: text/html; charset=utf-8');
	include("include/Http.class.php");
	//ºìÑÝÈ¦
	$client_id = "c661d967548331de"; // Youku OpenAPI client_id
	$client_secret = "035093f8221ddcd6a0d933620fc48c1c"; //Youku OpenAPI client_secret
	//ºìÑÝÈ¦hotyq
	// $client_id = "14164c85f31390cc"; // Youku OpenAPI client_id
	// $client_secret = "df39dd687a309355fa429af7cc1a751a"; //Youku OpenAPI client_secret

	$url = "https://openapi.youku.com/v2/videos/show.json";
	$video_id = $_GET["videoid"];
	sleep(5);
	$params = array("client_id"=>$client_id, 
					"video_id"=>$video_id, 
					"ext "=>"show");
	var_dump(json_decode(Http::post($url, $params)));
	echo '<hr>';
	$url1 = "https://openapi.youku.com/v2/videos/show_basic.json";
	$params1 = array("client_id"=>$client_id, 
					"video_id"=>$video_id);
	var_dump(json_decode(Http::post($url1, $params1)));
	


?>
<html>
	<head>
		<meta charset="UTF-8">
	</head>
	<body>
		<div id="youkuplayer" style="width:480px;height:400px"></div>
		<script type="text/javascript" src="http://player.youku.com/jsapi">
		player = new YKU.Player('youkuplayer',{
		styleid: '0',
		client_id: '<?=$client_id?>',
		vid: '<?=$video_id?>'
		});
		</script>
	</body>
</html>