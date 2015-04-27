<?php
class DataBase {
	private static $db = NULL;

	public static function getInstance($dsn, $user, $password){
		self::$db = new DataBase($dsn, $user, $password);
		return self::$db;
	}

	private $dbh;

	function __construct($dsn, $user, $password){
	   $this->dbh = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
	}

	public function fetch($sql){
		$stmt = $this->prepare(func_get_args());
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		return $stmt->fetch();
	}

	public function fetchAll($sql){
		$stmt = $this->prepare(func_get_args());
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		return $stmt->fetchAll();
	}

	public function fetchColumn($sql){
		$stmt = $this->prepare(func_get_args());
		$stmt->execute();
		return $stmt->fetchColumn();
	}

	public function exec($sql){
		$stmt = $this->prepare(func_get_args());
		return $stmt->execute();
	}

	public function insert($sql){
		$stmt = $this->prepare(func_get_args());
		$stmt->execute();
		return $this->dbh->lastInsertId(); 
	}

	private function prepare(array $args){
		$stmt = $this->dbh->prepare($args[0]);
		for($i = 1; $i < count($args); $i++) {
			$arg = $args[$i];
			if ($arg instanceof BaseType) {
				$stmt->bindValue($i, $arg->val);
			} else {
				$stmt->bindValue($i, $arg);
			}
		}
		return $stmt;
	}
}