<?php
/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/11/9
 * Time: 21:56
 */
define('ROOT_PATH', dirname(__FILE__));

if (!function_exists('write_log')) {
	/**
	 * 写log
	 * @param string $message 记录内容
	 * @param string $level 记录等级
	 */
	function write_log($message = '', $level = 'info')
	{
		switch ($level) {
			case 'INFO':
			case 'info':
				$filename = ROOT_PATH . '/logs/' . date('Y_m_d') . '.log';
				break;
			case 'ERROR':
			case 'error':
				$filename = ROOT_PATH . '/logs/' . date('Y_m_d') . '.error.log';
				break;
			default:
				$filename = ROOT_PATH . '/logs/' . date('Y_m_d') . '.log';
				break;
		}
		$fp = fopen($filename, 'a');
		@fwrite($fp, (string)$message . PHP_EOL);
		fclose($fp);

	}
}