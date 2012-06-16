<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 * @version        3.1.0
 * @package        patTemplate
 * @author        Stephan Schmidt <schst@php.net>
 * @license        LGPL
 * @link        http://www.php-tools.net
 */
// запрет прямого доступа
defined('_VALID_MOS') or die();
class patError{
	var $level = null;
	var $code = null;
	var $message = null;
	var $info = '';
	var $file = '';
	var $line = 0;
	var $function = '';
	var $class = '';
	var $type = '';
	var $args = array();
	var $backtrace = false;

	function patError($level, $code, $msg, $info = null){
		$this->__construct($level, $code, $msg, $info);
	}

	function __construct($level, $code, $msg, $info = null){
		static $raise = array('raise', 'raiseerror', 'raisewarning', 'raisenotice');
		$this->level = $level;
		$this->code = $code;
		$this->message = $msg;
		if($info != null){
			$this->info = $info;
		}
		if(function_exists('debug_backtrace')){
			$this->backtrace = debug_backtrace();
			for($i = count($this->backtrace) - 1; $i >= 0; --$i){
				if(in_array($this->backtrace[$i]['function'], $raise)){
					++$i;
					if(isset($this->backtrace[$i]['file']))
						$this->file = $this->backtrace[$i]['file'];
					if(isset($this->backtrace[$i]['line']))
						$this->line = $this->backtrace[$i]['line'];
					if(isset($this->backtrace[$i]['class']))
						$this->class = $this->backtrace[$i]['class'];
					if(isset($this->backtrace[$i]['function']))
						$this->function = $this->backtrace[$i]['function'];
					if(isset($this->backtrace[$i]['type']))
						$this->type = $this->backtrace[$i]['type'];
					$this->args = false;
					if(isset($this->backtrace[$i]['args'])){
						$this->args = $this->backtrace[$i]['args'];
					}
					break;
				}
			}
		}
	}

	function getLevel(){
		return $this->level;
	}

	function getMessage(){
		return $this->message;
	}

	function getInfo(){
		return $this->info;
	}

	function getCode(){
		return $this->code;
	}

	function getBacktrace($formatted = false){
		if($formatted && is_array($this->backtrace)){
			$result = '';
			foreach(debug_backtrace() as $back){
				if(!preg_match('/patErrorManager.php/i', $back['file'])){
					$result .= '<br />' . $back['file'] . ':' . $back['line'];
				}
			}
			return $result;
		}
		return $this->backtrace;
	}

	function getFile(){
		return $this->file;
	}

	function getLine(){
		return $this->line;
	}
}

?>
