<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>角色列表</title>
		<link type="text/css" rel="stylesheet" href="/admin/css/cms.css">
		<script src="/js/jquery.js" language="javascript"></script>
	</head>
	<body>
		<div style="background:#ccc;display:inline-block;width:400px;margin-right:110px">角色列表</div>
		<span style="background:lightblue;display" width="100px"><a href="role_edit.php?action=create&parent_id=0">添加一级角色</a></span>
		<table background="#ccc" border="1" width="600" align="center">
			<tr align="center">
				<th>ID</th>
				<th>角色名</th>
				<th>操作</th>
			</tr>
		{if $rolelist eq null}
			<tr align="center">
				<td colspan="3">没有角色信息！请<a href="role_edit.php?action=create&parent_id=0">添加角色</a>！</th>
			</tr>
		{/if}
		{foreach $rolelist as $first}
			<tr align="center">
				<td width="10%">{$first['id']}</td>
				<td width="30%" align="left">&nbsp;&nbsp;&nbsp;{$first['name']}</td>
				<td>
					<a href="role_edit.php?action=modify&id={$first['id']}">修改</a>&nbsp;
					<a href="role_save.php?action=delete&id={$first['id']}" class="del1">删除</a>&nbsp;
					<a href="role_edit.php?action=create&parent_id={$first['id']}">添加子角色</a>
				</td>
			</tr>
		{foreach $first[$first['id']] as $second}
			<tr align="center">
				<td>{$second['id']}</td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$second['name']}</td>
				<td>
					
					<a href="role_edit.php?action=modify&id={$second['id']}">修改</a>&nbsp;
					<a href="role_save.php?action=delete&id={$second['id']}" class="del2">删除</a>&nbsp;
				</td>
			</tr>
		{/foreach}
		{/foreach}
		</table>
	</body>
	{literal}
	<script charset="utf-8">
		$('.del1').click(function(){
			if(!confirm('你确定删除吗')){
				$('.del1').removeAttr('href');
				location.reload();
			}
			
		});
		$('.del2').click(function(){
			if(!confirm('你确定删除吗')){
				$('.del2').removeAttr('href');
				location.reload();
			}
		
		});
	</script>
	{/literal}
</html>