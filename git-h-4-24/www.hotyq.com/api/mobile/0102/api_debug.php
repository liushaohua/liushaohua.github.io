<?php
header("Content-type:text/html;charset=utf-8"); 
require_once("common_inc.php");
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
	$ch = curl_init(API_DEBUG_URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function output_form_html($param_arr){
	global $action;
	$html = "<input type='hidden' name='action' value='{$action}' />";
	if(is_array($param_arr)){
		foreach ($param_arr as $key => $value) {
			if($value['type'] == 'enum'){
				$html .= "{$value["name"]}:<select name='{$value["field"]}'>\n";
				$html .= "<option>--请选择--</option>\n";
				foreach($value['value'] as $kk=>$vv){
					$html .= "<option value='{$vv}'>{$vv}</option>\n";
				}
				$html .= "</select><br>";
			}elseif($value['type'] == 'file'){
				$html .= "{$value["name"]}:<input type='file' name='{$value["field"]}' value='{$value["default"]}'><br>";
			}else{
				$html .= "{$value["name"]}:<input type='text' name='{$value["field"]}' value='{$value["default"]}' /><br />\n";
			}
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
	function del_user(type){
		if(type == 'mobile'){
			var account = $('#user_id').val();
			var method = 'mobile';
		}else{
			var account = $('#user_email').val();
			var method = 'email';
		}
		
		$.ajax({
			url:'/api/mobile/0102/del_user.php',
			type:'post',
			data:{'account':account,'method':method},
			success:function(data){
				//alert(data);
				if(data == 1000){
					alert('删除成功');
				}else if(data == 1860){
					alert('用户不存在');
				}else{
					alert('删除失败');
				}
			},
			dataType:'text'
		})
		
	}

	function update_user(){
		var open_type = $('#id_type').val();
		var openid = $('#open_id').val();
		$.ajax({
			url:'/api/mobile/0102/update_user.php',
			type:'post',
			data:{'openid_type':open_type,'openid':openid},
			success:function(data){
				//alert(data);
				if(data == 1000){
					alert('OPENID清空成功');
				}else if(data == 1860){
					alert('OPENID类型值不匹配');
				}else{
					alert('OPENID清空失败');
				}
			},
			dataType:'text'
		})
		
	}

	function get_type(){
		var type = $('#openid_type option:selected').val();
		//alert(type);
		$('#id_type').val(type);
		//alert($('#id_type').val());
	}
	</script>
	
	<? echo output_select_action_list() ?>
	<h1><?=$action_info["name"]?></h1>
	<form action="?action=<?=$action?>" method="post" enctype="multipart/form-data">
	<? echo output_form_html($action_info["param_arr"]); ?>

	</form>
	<h2>输入参数</h2>
	<? print_r(@$arr) ?>
	<hr />
	<h2>返回结果</h2>
	<per>
	<? print_r(json_decode(@$result,true)); ?>
	</per>
	<hr />
	<h2>JSON结果</h2>
	<hr />
	删除用户:<input type="text" id="user_id" name="del_user" value="" placeholder="请输入你的注册手机号"><button onclick="return del_user('mobile')">确定</button>
			 <input type="text" id="user_email" name="del_user" value="" placeholder="请输入你的注册邮箱"><button onclick="return del_user('email')">确定</button>
	<hr>
	删除OPEN_ID:
	<select name="openid_type" id="openid_type" onchange="return get_type();">
		<option>--请选择OPENID类型--</option>
		<option value="openid_weibo">weibo</option>
		<option value="openid_qq">qq</option>
		<option value="openid_weixin" >weixin</option>
	</select>
	<input type="hidden" id="id_type" name="id_type" value="">
	<input type="text" id="open_id" name="update_user" value="" placeholder="请输入OPENID">
	<button onclick="return update_user()">确定</button>
	<hr>
	<per>
	<? print_r(@$result) ?>
	</per>
</body>
</html>