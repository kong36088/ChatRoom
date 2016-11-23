<?php
/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/11/8
 * Time: 17:49
 */

function get_config()
{
	$config = array(
		'sys' => array(
			//这么分开是因为防止有的童鞋利用了docker进行了端口转发和IP转发，导致监听地址和连接地址不一致，一般情况下两个address和port填写一致即可
			'socket_listen_address' => '0.0.0.0',  //服务器监听的地址
			'socket_listen_port' => 9501,  //服务器监听的端口
			'server_address' => 'chat.jwlchina.cn', //前端页面WS连接地址，一般填写服务器地址即可
			'server_port' => 9501, //前端页面WS连接地址，填写需要连接的端口
		)
	);
	return $config;
}
