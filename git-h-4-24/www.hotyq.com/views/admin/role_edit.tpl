<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>角色添加和编辑</title>
		<link type='text/css' rel='stylesheet' href='/admin/css/cms.css'>
	</head>
	<body>
		{if $action eq 'create'}
		<span style="background:#ccc">添加角色：</span>
		<hr>
		{else $action eq 'modify'}
		<span style="background:#ccc">修改角色：</span>
		<hr>
		{/if}
		{if $action eq 'create'}
			<!--添加角色-->
			<form method="post" action="role_save.php">
				<input type="hidden" name="action" value="{$action}">
				<table>
					<tr>
						<th>角色名称：</th>
						<td><input type="text" name="name" /></td>
					</tr>
					<tr>
						<th>父角色名：</th>
						<td>
							<select name="parent_id">
								{if $parent_id eq 0}
									<option value="0" selected>--无--</option>
								{else}						
									<option value="{$parent_id}" selected>{$parent_name}</option>
								{/if}							
							</select>
						</td>
					</tr>
					<tr>
						<th>角色描述：</th>
						<td>
							<textarea rows="10" cols="50" name="descr"></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center"><input type="submit" name="sub" value="确认添加" /></td>
					</tr>
				</table>	
			</form>
			{else $action eq 'modify'}
			<form method="post" action="role_save.php">
				<input type="hidden" name="action" value="{$action}">
				<input type="hidden" name="id" value="{$id}">
				<table>
					<tr>
						<th>角色名称：</th>
						<td><input type="text" name="name" value="{$name}"/></td>
					</tr>
					<tr>
						<th>父角色名：</th>
						<td>
							<select name="parent_id">
								<option value="{$parent_id}" selected>{$parent_name}</option>
							</select>
						</td>
					</tr>				
					<tr>
						<th>角色描述：</th>
						<td>
							<textarea rows="10" cols="50" name="descr">{$descr}</textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center"><input type="submit" name="sub" value="确认修改" /></td>
					</tr>
		
				</table>	
			</form>
		{/if}
	</body>
</html>