<?php
abstract class BaseType{
	public $val;

	public function __construct($val){
		$this->val=$val;
	}

	public function __toString(){
		return strval($this->val);
	}

	public function __invoke(){
		return $this->val;
	}
}

final class Integer extends BaseType{
	public function __construct($val=0){
		if(is_int($val) || preg_match("/^\d+$/", $val)) {
			$val = (int)$val;
			parent::__construct($val);
		} else
			throw new Exception("this value is not Integer");
	}
}

final class String extends BaseType{
	public function __construct($val){
		$val = trim($val);
		if(strlen($val) > 0) {
			parent::__construct($val);
		} else
			throw new Exception("this value is Empty");
	}
}

final class Email extends BaseType{
	public function __construct($val){
		$val = trim($val);
		if(preg_match("/^[\w.-]+@[\w.-]+$/", $val)) {
			parent::__construct($val);
		} else
			throw new Exception("this value is not Email");
	}
}

final class Mobile extends BaseType{
	public function __construct($val){
		$val = trim($val);
		if(preg_match("/^1\d{10}$/", $val)) {
			parent::__construct($val);
		} else
			throw new Exception("this value is not Mobile");
	}
}
