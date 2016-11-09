<?php
/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/11/9
 * Time: 21:56
 */

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
				$fp = fopen('./logs/' . date('Y_m_d') . '.log', 'a+');
				@fputs($fp, (string)$message . PHP_EOL);
				fclose($fp);
				break;
			case 'ERROR':
			case 'error':
				$fp = fopen('./logs/' . date('Y_m_d') . '.error.log', 'a+');
				@fputs($fp, (string)$message . PHP_EOL);
				fclose($fp);
				break;
			default:
				$fp = fopen('./logs/' . date('Y_m_d') . '.log', 'a+');
				@fputs($fp, $level . ':' . PHP_EOL . (string)$message . PHP_EOL);
				fclose($fp);
				break;
		}

	}
}