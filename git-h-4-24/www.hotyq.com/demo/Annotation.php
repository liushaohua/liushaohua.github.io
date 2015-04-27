<?php 
interface Annotation {
}

class Dependency {
	public function excuting(){}
	public function excuted(){}
}

class UserFunction extends Dependency implements Annotation {
	public $method = "GET|POST";

	public function excuting(){
		if(stripos($this->method, $_SERVER['REQUEST_METHOD'])=== false){
			throw new ProException("Method is Error", 110);
		}
	}
}

class CheckLogin extends Dependency implements Annotation {
	public function excuting(){
		if(!isset($_COOKIE[AUTH_COOKIE_KEY]) || empty($_COOKIE[AUTH_COOKIE_KEY]))
			throw new ProException("user not login", 110);
	}
}