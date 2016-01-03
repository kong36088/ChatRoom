<?php
class Clientsopr {
	private $expire_time=0;
	private $mem;
	public function __construct($memcache){
		$this->mem=$memcache;
	}
	/*
	 * memcache增加删除人数
	 * @param @clients 存在memcache中的用户数组
	 * @param $fd swoole对应fd号
	 * @param $data 传入数据，数组方式
	 */
	public function add($clients,$fd, $name) {
			$clients [$fd] = $name;
			$this->mem->delete ( 'clients' );
			$this->mem->set ( 'clients', $clients, 0, $this->expire_time );
			return $clients;
	}
	public function delete($clients,$fd){
		unset($clients[$fd]);
		$this->mem->delete ( 'clients' );
		$this->mem->set ( 'clients', $clients, 0, $this->expire_time );
		return $clients;
	}
	/*
	 * 类设置
	 * */
	public function setExpire($time){
		$this->expire_time=$time;
	}
}