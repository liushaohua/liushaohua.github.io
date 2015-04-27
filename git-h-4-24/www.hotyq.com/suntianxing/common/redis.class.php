<?php
/*
 * redis基础类
 * 作者：wangyi suntianxing
 * 添加时间：2014-11-25
*/
class redis_class{
   
	private $redis; //redis对象
	function __construct($config = array()){}
	private function connect(){
		if(!$this->redis){
			$this->redis = new Redis();
			$this->redis->connect("local_redis_1.hotyq.com");
		}
	}

	/**
	 * 设置私信内容Hash 全部
	 * @param string $key KEY名称
	 * @param string $value 要存入redis的数据
	 */
	public function hmset($key, $value=array()) {
		$this -> connect();
		return $this->redis->hmset($key,$value);// 生成hash数据
	}
	/**
	 * 获取私信内容Hash 
	 * @param string $key hash名称  $field 域名
	 */
	public function hmget($key,$field=array()) {
		$this -> connect();
		return $this->redis->hmget($key,$field);// 全部hash数据
	}
	/**
	 * 获取 Hash 的大小 域的数量
	 * @param string $key hash名称  
	 */
	public function hlen($key) {
		$this -> connect();
		return $this->redis->hlen($key);// key 为 $key 的hash 域的数量
	}
	
	/**
	 * 设置私信队列 队列头部插入数据
	 * @param string $key KEY名称
	 * @param string $value 要存入redis的数据
	 */
	public function lpush($key, $value) {
		$this -> connect();
		//$this->redis->expire($key,5);
		return $this->redis->lpush($key, $value);
	}
	
	/**
	 * 设置私信队列  队列尾部插入数据
	 * @param string $key KEY名称
	 * @param string $value 要存入redis的数据
	 */
	public function rpush($key, $value) {
		$this -> connect();
		//$this->redis->expire($key,5);
		return $this->redis->rpush($key, $value);
	}
	
	/**
	 * 读取私信队列 全部
	 * @param string $key KEY名称
	 * @param string $value 要存入redis的数据
	 */
	public function lrange($key, $start, $end) {
		$this -> connect();
		return $this->redis->lrange($key, $start, $end);// 队列数据
	}
	
	/**
	 * 读取私信 队列 中一条
	 * @param string $key KEY名称
	 * @param string $index index索引
	 */
	public function lindex($key, $index) {
		$this -> connect();
		return $this->redis->lindex($key, $index);
	}
	/**
	 * 重新赋值私信 队列 中指定索引下 的元素
	 * @param string $key KEY名称
	 * @param string $index index索引
	 * @param string $value 新值
	 */
	public function lset($key, $index, $value) {
		$this -> connect();
		return $this->redis->lset($key, $index, $value);
	}
	/**
	 * 获取所有符合要求的 键 的列表
	 * @param string $key KEY名称  $key = 'record:'.$user.'_'.'*';
	 */
	public function keys($key) {
		$this -> connect();
		return $this->redis->keys($key);
	}
	/**
	 * 队列 key 的长度。 
	 * @param string $key KEY名称  
	 */
	public function llen($key) {
		$this -> connect();
		return $this->redis->llen($key);
	}
	
	
	
	
	
	/**
	 * 设置联系人 集合
	 * @param string $key KEY名称
	 * @param string $value 集合元素值
	 */
	public function sadd($key, $value) {
		$this -> connect();
		return $this->redis->sadd($key, $value);
	}
	/**
	 * 获取联系人缓存 集合 全部成员
	 * @param string $key KEY名称
	 */
	public function smembers($key) {
		$this -> connect();
		return $this->redis->smembers($key);
	}
	/**
	 * 获取联系人缓存 集合 成员个数
	 * @param string $key KEY名称
	 */
	public function ssize($key) {
		$this -> connect();
		return $this->redis->ssize($key);
	}
	/**
	 * 移除 集合 成员
	 * @param string $key KEY名称
	 * @param string $value 集合成员
	 */
	public function srem($key, $value) {
		$this -> connect();
		return $this->redis->srem($key, $value);
	}
	
	
	
	
	
	/**
	 * 设置 键值对(string)
	 * @param string $key KEY名称
	 * @param string $value 要存入redis的数据
	 * @param int $time 生存时间设置
	 */
	public function set($key, $value, $time = 5) {
		$this -> connect();
		return $this->redis->set($key, $value, $time);
	}
	/**
	 * 一次设置多个值
	 * @param array $value 要存入redis的数据
	 * @param int $time 生存时间设置    ???
	 */
	public function mset($value=array()) {
		$this -> connect();
		return $this->redis->mset($value);
	}
	/**
	 * 通过KEY获取数据
	 * @param string $key KEY名称
	 */
	public function get($key) {
		$this -> connect();
		return $this->redis->get($key);
	}
	

	//验证缓存 操作
	
	/**
	 * 检测 集合
	 * @param string $key KEY名称
	 * @param string $value 集合元素值
	 */
	public function srandmember($key) {
		$this -> connect();
		return $this->redis->srandmember($key);
	}
	/**
	 * 通过检查KEY是否存在 判断数据是否存在
	 * @param string $key KEY名称
	 */
	public function exists($key) {
		$this -> connect();
		return $this->redis-> exists($key);
	}
	
	/**
	 * 删除一个键
	 * @param string $key KEY名称
	 */
	public function del($key) {
		$this -> connect();
		return $this->redis->del($key);
	}
	
	
	
	





	
}
?>