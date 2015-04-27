<?php
/*
header("Content-type:text/html;charset=utf-8"); 
require_once("../../includes/common_api_android_inc.php");

$action = @$_GET["action"];
$p_action = @$_POST["action"];
if($action){
	$action_info = $action_list[$action];
}
if($action && $p_action==$action){
	$param_arr = $action_info["param_arr"];
	$arr["action"] = $action;
	if(is_array($param_arr)){
		$arr["action"] = $action;
		foreach ($param_arr as $key => $value) {
			$arr[$value["field"]] = $_POST[$value["field"]];
		}
	}
	$result = curl_api($arr);
}

function curl_api($data){
	// $data = array('action' => 'get_reg_mobile_code','mobile'=>'18513200411');	
	$data_string = json_encode($data);
	$ch = curl_init(API_DEBUG_URL);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($data_string))
	);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
function output_form_html($param_arr){
	global $action;
	$html = "<input type='hidden' name='action' value='{$action}' />";
	if(is_array($param_arr)){
		foreach ($param_arr as $key => $value) {
			$html .= "{$value["name"]}:<input type='text' name='{$value["field"]}' value='".@$value["default"]."' /><br />\n";
		}
	}
	$html .="<input type='submit' value='提交' />";
	return $html;
}
function output_select_action_list(){
	global $action_list,$action;
	$html = "<select onchange='change_select(this);'>\n";
	$html .= "<option value=''>--请选择--</option>\n";
	foreach ($action_list as $key => $value) {
		if($key == $action){
			$html .= "<option value='{$key}' selected>{$key} {$value["name"]}</option>\n";
		}else{
			$html .= "<option value='{$key}'>{$key} {$value["name"]}</option>\n";
		}
	}
	$html .= "</select>";
	return $html;
}
?>

<html>
<head>
	<title><?=$action_info["name"]?></title>
	<script type="text/javascript" src="/js/jquery.js"></script>
</head>
<body>
	<script>
	function change_select(e){
		window.location.href="?action="+e.value;
	}
	function del_user(){
		var phonenum = $('#user_id').val();
		$.ajax({
			url:'/api/android/010101/del_user.php',
			type:'post',
			data:{'phonenum':phonenum},
			success:function(data){
				if(data == 1000){
					alert('删除成功');
				}else{
					alert('删除失败');
				}
			},
			dataType:'text'
		})
		
	}
	</script>
	
	<? echo output_select_action_list() ?>
	<h1><?=$action_info["name"]?></h1>
	<form action="?action=<?=$action?>" method="post">
	<? echo output_form_html($action_info["param_arr"]); ?>

	</form>
	<h2>输入参数</h2>
	<? print_r(@$arr) ?>
	<hr />
	<h2>返回结果</h2>
	<per>
	<? print_r(json_decode(@$result)) ?>
	</per>
	<hr />
	<h2>JSON结果</h2>
	<hr />
	删除用户:<input type="text" id="user_id" name="del_user" value="" placeholder="请输入用户手机号">
	<button id="del_user" onclick="return del_user()">确定</button>
	<hr>
	<per>
	<? print_r(@$result) ?>
	
</body>
</html>
*/