<?php
/**
* 获取我加入的群
* @UserFunction(method = GET)
* @CheckLogin
*/
function get_my_group() {
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	$group_list = $db->fetchAll("SELECT `group`.`id`, `group`.`name`,`group_user`.`timestamp` AS `join_date`  FROM `group_user` INNER JOIN `group` ON `group_user`.`group_id` = `group`.`id` WHERE `group_user`.`group_id` = ?", getCurrentUserId());

	return $group_list;
}

/**
* 创建群
* @UserFunction(method = GET)
* @CheckLogin
*/
function create_group(String $name) {
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	$user_id = getCurrentUserId();
	$group_id = $db->insert("INSERT INTO `group`(`name`, `create_user_id`) VALUES(?,?);", $name, $user_id);
	$db->exec("INSERT INTO `group_user`(`group_id`, `user_id`, `role`) VALUES(?,?,1);", $group_id, $user_id);
	return $group_id;
}

/**
* 加入群
* @UserFunction(method = GET)
* @CheckLogin
*/
function join_group(Integer $id) {
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	$user_id = getCurrentUserId();
	$db->exec("INSERT INTO `group_user`(`group_id`, `user_id`, `role`) VALUES(?,?,0);", $id, $user_id);
}

/**
* 退出群
* @UserFunction(method = GET)
* @CheckLogin
*/
function quit_group(Integer $id) {
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	$user_id = getCurrentUserId();
	$db->exec("DELETE FROM `group_user` WHERE `group_id` = ? AND `user_id` = ?", $id, $user_id);
}

/**
* 获取指定群信息
* @UserFunction(method = GET)
* @CheckLogin
*/
function get_group(Integer $id) {
	$db = new DataBase(DB_DNS, DB_USER, DB_PASSWORD);
	$user_id = getCurrentUserId();
	$db->exec("SELETE * FROM `group` WHERE `id` = ?", $id);

	return $group_list;
}