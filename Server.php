<?php
/*
 * power by swoole
 * 必须拓展：swoole,memcache
 * @author Jiang Weilong
 * @time 2015-12-20
 */
require (dirname ( __FILE__ ) . '/Clientsopr.class.php');
class Server {
	private $clientsOpr;
	private $clients = array ();
	private $server;
	private $mem;
	public function __construct() {
		$this->mem = new Memcache ();
		if (! $this->mem->connect ( '127.0.0.1', 11211 )) {
			echo 'cannot connect to memcache';
			exit ();
		}
		$this->clientsOpr = new Clientsopr ( $this->mem );
		$this->mem->flush ();
	}
	public function start() {
		$this->server = new swoole_websocket_server ( "127.0.0.1", 9501 );
		$this->server->set ( array (
				'worker_num' => 8,
				'daemonize' => false,
				'max_request' => 10000,
				'dispatch_mode' => 2,
				'debug_mode' => 1,
				'task_worker_num' => 8 
		) );
		$this->server->on ( 'close', function ($ser, $fd) {
			echo "client {$fd} closed\n";
		} );
		
		$this->server->on ( 'open', [ 
				$this,
				'onOpen' 
		] );
		
		$this->server->on ( 'message', [ 
				$this,
				'onMessage' 
		] );
		
		$this->server->on ( 'close', [ 
				$this,
				'onClose' 
		] );
		//回调
		$this->server->on ( 'task', [ 
				$this,
				'onTask' 
		] );
		$this->server->on ( 'Finish', array (
				$this,
				'onFinish' 
		) );
		$this->server->start ();
	}
	public function onOpen($server, $request) {
		echo $request->fd . ' connect' . PHP_EOL;
	}
	public function onMessage($server, $frame) {
		$fd = $frame->fd;
		$data = $frame->data;
		if($data!=NULL){
			$clients = $this->mem->get ( 'clients' );
			echo 'recieve from:' . $frame->fd . ' data:' . $frame->data . PHP_EOL;
			if (@! $clients [$fd]) {
				$clients = $this->clientsOpr->add ( $clients, $fd, $data );
				$frame->data = json_encode(['message'=>$data.'加入聊天室','type'=>1,'status'=>1,'time'=>date('H:i:s')]);
				$server->task ( $frame );
			} else {
				$frame->data = json_encode(['username'=>$clients[$fd],'message'=>$data,'type'=>2,'status'=>1,'time'=>date('H:i:s')]);
				$server->task ( $frame );
			}
			// 回收内存
			unset ( $clients );
		}
		
	}
	public function onTask($server, $task_id, $from_id, $frame) {
		$clients = $this->mem->get ( 'clients' );
		foreach ( $clients as $key => $value ) {
			$server->push ( ( int ) ($key), $frame->data );
		}
		$data=json_decode($frame->data);
		$fp=fopen('./logs/'.date('Y_m_d').'.txt','a+');
		@fputs($fp,date('H:i:s').' '.$data->username.' '.$data->message.PHP_EOL);
		fclose($fp);
		// 回收内存
		unset ( $clients );
		return "Task {$task_id}'s result";
	}
	public function onClose($server, $fd) {
		echo "{$fd} closed" . PHP_EOL;
		$clients = $this->mem->get ( 'clients' );
		$this->clientsOpr->delete ( $clients, $fd );
		$frame = new stdClass ();
		$frame->fd = $fd;
		$frame->data = json_encode(['message'=>$clients[$fd].'离开聊天室','type'=>1,'status'=>1,'time'=>date('H:i:s')]);
		// 发送离开消息
		$server->task ( $frame );
	}
	public function onFinish($serv, $task_id, $data) {
	}
}
$Server = new Server ();
$Server->start ();
