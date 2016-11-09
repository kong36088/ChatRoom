<?php
/*
 * power by swoole
 * 必须拓展：swoole,memcache
 * @author Jiang Weilong
 * @time 2016-11-08
 */
require(dirname(__FILE__) . '/config.php');
require(dirname(__FILE__) . '/functions.php');

class Server
{
	private $clients = array();  //保存连接的用户，fd=>nickname的形式保存
	private $server;
	private $config;
	private $lock;

	public function __construct()
	{
		$this->config = get_config();
		$this->clients = array();
	}

	public function start()
	{
		//获取互斥锁 文件锁
		$this->lock = new swoole_lock(SWOOLE_MUTEX);

		$this->server = new swoole_websocket_server ($this->config['sys']['socket_listen_address'], $this->config['sys']['socket_listen_port']);
		$this->server->set(array(
			'daemonize' => false,
			'max_request' => 10000,
			'dispatch_mode' => 2,
			'debug_mode' => 1,
			'task_worker_num' => 8,
			'worker_num' => 1
		));
		$this->server->on('close', function ($ser, $fd) {
			echo "client {$fd} closed\n";
		});

		$this->server->on('open', [
			$this,
			'onOpen'
		]);

		$this->server->on('message', [
			$this,
			'onMessage'
		]);

		$this->server->on('close', [
			$this,
			'onClose'
		]);
		//回调
		$this->server->on('task', [
			$this,
			'broadcast'
		]);
		$this->server->on('Finish', array(
			$this,
			'onFinish'));
		$this->server->start();
	}

	/**
	 * 用户打开聊天室并连接
	 * @param $server
	 * @param $request
	 */
	public function onOpen($server, $request)
	{
		$this->clients[$request->fd] = '';
		echo $request->fd . ' connect'.' IP:' . $request->server['remote_addr'] . PHP_EOL;
		write_log($request->fd . ' connect'.' IP:' . $request->server['remote_addr'], 'info');
	}

	/**
	 * 接收到用户发来的message
	 * @param $server
	 * @param $frame
	 */
	public function onMessage($server, $frame)
	{
		$fd = $frame->fd;
		$json = $frame->data;
		if ($json != NULL) {
			$data = json_decode($json);

			echo 'receive from:' . $frame->fd . ' data:' . $frame->data . PHP_EOL;

			switch ($data->action) {
				case 'INIT':
				case 'init':
					try {
						$this->clients[$fd] = $data->username;
						$frame->data = array('username' => $this->clients[$fd], 'message' => $this->clients[$fd] . '加入聊天室', 'type' => 1, 'status' => 1, 'time' => date('H:i:s'));
					} catch (Exception $e) {
						//发生错误时
						write_log('发生错误的frame信息：' . serialize($frame), 'error');
						$server->close($frame->fd);
						return;
					}
					$frame->clients = $this->clients;  //传全局clients
					$server->task($frame);
					break;
				case 'MESSAGE':
				case 'message':
					try {
						$frame->data = array('username' => $this->clients[$fd], 'message' => $data->message, 'type' => 2, 'status' => 1, 'time' => date('H:i:s'));
					} catch (Exception $e) {
						//发生错误时
						write_log('发生错误的frame信息：' . serialize($frame), 'error');
						$server->close($frame->fd);
						return;
					}
					$frame->clients = $this->clients; //传全局clients
					$server->task($frame);
					break;
				default:
					return;
			}
		}

	}

	/**
	 *  广播信息，绑定task事件onTask
	 * @param object $server swoole server对象
	 * @param $task_id
	 * @param $from_id
	 * @param object $frame 包含frame的信息，并且$frame->data保存了调用task传过来的参数
	 * @return string
	 */
	public function broadcast($server, $task_id, $from_id, $frame)
	{
		$clients = $frame->clients;
		foreach ($clients as $fd => $nickname) {
			$server->push((int)($fd), json_encode($frame->data));
		}
		$data = $frame->data;
		//保存记录至log文件
		write_log(date('H:i:s') . ' ' . $data['username'] . ' ' . $data['message']);
		// 回收内存
		return "Task {$task_id} finish";
	}

	/**
	 * @param object $serv swoole server对象
	 * @param int $task_id 任务id
	 * @param array $data 传递参数
	 */
	public function onFinish($server, $task_id, $data)
	{
	}

	/**
	 * 用户关闭连接
	 * @param $server
	 * @param $fd
	 */
	public function onClose($server, $fd)
	{
		echo "{$fd} closed" . PHP_EOL;
		$username = $this->clients[$fd];

		//释放客户，利用锁进行数据同步
		$this->lock->lock();
		unset($this->clients[$fd]);
		$this->lock->unlock();

		$frame = new stdClass ();
		$frame->fd = $fd;
		$frame->data = array('message' => $username . '离开聊天室', 'type' => 1, 'status' => 1, 'time' => date('H:i:s'));
		// 发送离开消息
		$server->task($frame);
	}

	/**
	 * 写log
	 * @param string $message 记录内容-
	 * @param string $level 记录等级
	 */
	public function log($message, $level = 'info')
	{

	}
}

$Server = new Server ();
$Server->start();
