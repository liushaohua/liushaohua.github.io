<?php
/*
 * redis_search基础类
 * 作者：wangyi
 * 添加时间：2015-3-5
*/
class redis_message extends redis_class{
	protected function connect(){
		if(!$this->redis){
			$this->redis = new Redis();
			$this->redis->connect("local_redis_3.hotyq.com");
			$this->redis->select(4); # 设置redis为4号数据库
		}
	}
}
?>