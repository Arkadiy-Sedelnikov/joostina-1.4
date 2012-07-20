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
defined('_JLINDEX') or die();
define('PATERRORMANAGER_ERROR_ILLEGAL_OPTIONS', 1);
define('PATERRORMANAGER_ERROR_CALLBACK_NOT_CALLABLE', 2);
define('PATERRORMANAGER_ERROR_ILLEGAL_MODE', 3);
$GLOBALS['_pat_errorHandling'] = array(E_NOTICE => array('mode' => 'echo'), E_WARNING => array('mode' => 'echo'), E_ERROR => array('mode' => 'die'));
$GLOBALS['_pat_errorLevels'] = array(E_NOTICE => 'Notice', E_WARNING => 'Warning', E_ERROR => 'Error');
$GLOBALS['_pat_errorClass'] = 'patError';
$GLOBALS['_pat_errorIgnores'] = array();
$GLOBALS['_pat_errorExpects'] = array();
class patErrorManager{
	function isError(&$object){
		if(!is_object($object)){
			return false;
		}
		if(get_class($object) != strtolower($GLOBALS['_pat_errorClass']) && !is_subclass_of($object, $GLOBALS['_pat_errorClass'])){
			return false;
		}
		return true;
	}

	function &raiseError($code, $msg, $info = null){
		return patErrorManager::raise(E_ERROR, $code, $msg, $info);
	}

	function &raiseWarning($code, $msg, $info = null){
		return patErrorManager::raise(E_WARNING, $code, $msg, $info);
	}

	function &raiseNotice($code, $msg, $info = null){
		return patErrorManager::raise(E_NOTICE, $code, $msg, $info);
	}

	function &raise($level, $code, $msg, $info = null){

		if(in_array($code, $GLOBALS['_pat_errorIgnores'])){
			return false;
		}

		if(!empty($GLOBALS['_pat_errorExpects'])){
			$expected = array_pop($GLOBALS['_pat_errorExpects']);
			if(in_array($code, $expected)){
				return false;
			}
		}

		$class = $GLOBALS['_pat_errorClass'];
		if(!class_exists($class)){
			include_once dirname(__file__) . '/' . $class . '.php';
		}

		$error = new $class($level, $code, $msg, $info);

		$handling = patErrorManager::getErrorHandling($level);
		$function = 'handleError' . ucfirst($handling['mode']);
		return patErrorManager::$function($error, $handling);
	}

	function registerErrorLevel($level, $name){
		if(isset($GLOBALS['_pat_errorLevels'][$level])){
			return false;
		}
		$GLOBALS['_pat_errorLevels'][$level] = $name;
		patErrorManager::setErrorHandling($level, 'ignore');
		return true;
	}

	function setErrorHandling($level, $mode, $options = null){
		$levels = $GLOBALS['_pat_errorLevels'];
		$function = 'handleError' . ucfirst($mode);
		if(!is_callable(array('patErrorManager', $function))){
			return patErrorManager::raiseError(E_ERROR, 'patErrorManager:' . PATERRORMANAGER_ERROR_ILLEGAL_MODE, 'Error Handling mode is not knwon', 'Mode: ' . $mode . ' is not implemented.');
		}
		foreach($levels as $eLevel => $eTitle){
			if(($level & $eLevel) != $eLevel){
				continue;
			}

			if($mode == 'callback'){
				if(!is_array($options)){
					return patErrorManager::raiseError(E_ERROR, 'patErrorManager:' . PATERRORMANAGER_ERROR_ILLEGAL_OPTIONS, 'Options for callback not valid');
				}
				if(!is_callable($options)){
					$tmp = array('GLOBAL');
					if(is_array($options)){
						$tmp[0] = $options[0];
						$tmp[1] = $options[1];
					} else{
						$tmp[1] = $options;
					}
					return patErrorManager::raiseError(E_ERROR, 'patErrorManager:' . PATERRORMANAGER_ERROR_CALLBACK_NOT_CALLABLE, 'Function is not callable', 'Function:' . $tmp[1] . ' scope ' . $tmp[0] . '.');
				}
			}

			$GLOBALS['_pat_errorHandling'][$eLevel] = array('mode' => $mode);
			if($options != null){
				$GLOBALS['_pat_errorHandling'][$eLevel]['options'] = $options;
			}
		}
		return true;
	}

	function getErrorHandling($level){
		return $GLOBALS['_pat_errorHandling'][$level];
	}

	function translateErrorLevel($level){
		if(isset($GLOBALS['_pat_errorLevels'][$level])){
			return $GLOBALS['_pat_errorLevels'][$level];
		}
		return 'Unknown error level';
	}

	function setErrorClass($name){

		if($name !== $GLOBALS['_pat_errorClass'] && !class_exists($GLOBALS['_pat_errorClass'])){
			include_once dirname(__file__) . '/' . $GLOBALS['_pat_errorClass'] . '.php';
		}
		$GLOBALS['_pat_errorClass'] = $name;
		return true;
	}

	function addIgnore($codes){
		if(!is_array($codes)){
			$codes = array($codes);
		}
		$codes = array_merge($GLOBALS['_pat_errorIgnores'], $codes);
		$GLOBALS['_pat_errorIgnores'] = array_unique($codes);
		return true;
	}

	function removeIgnore($codes){
		if(!is_array($codes)){
			$codes = array($codes);
		}
		foreach($codes as $code){
			$index = array_search($code, $GLOBALS['_pat_errorIgnores']);
			if($index === false){
				continue;
			}
			unset($GLOBALS['_pat_errorIgnores'][$index]);
		}

		$GLOBALS['_pat_errorIgnores'] = array_values($GLOBALS['_pat_errorIgnores']);
		return true;
	}

	function getIgnore(){
		return $GLOBALS['_pat_errorIgnores'];
	}

	function clearIgnore(){
		$GLOBALS['_pat_errorIgnores'] = array();
		return true;
	}

	function pushExpect($codes){
		if(!is_array($codes)){
			$codes = array($codes);
		}
		array_push($GLOBALS['_pat_errorExpects'], $codes);
		return true;
	}

	function popExpect(){
		if(empty($GLOBALS['_pat_errorExpects'])){
			return false;
		}
		array_pop($GLOBALS['_pat_errorExpects']);
		return true;
	}

	function getExpect(){
		return $GLOBALS['_pat_errorExpects'];
	}

	function clearExpect(){
		$GLOBALS['_pat_errorExpects'] = array();
		return true;
	}

	function &handleErrorIgnore(&$error, $options){
		return $error;
	}

	function &handleErrorEcho(&$error, $options){
		$level_human = patErrorManager::translateErrorLevel($error->getLevel());
		if(isset($_SERVER['HTTP_HOST'])){

			echo "<br /><b>pat-$level_human</b>: " . $error->getMessage() . "<br />\n";
		} else{

			if(defined('STDERR')){
				fwrite(STDERR, "pat-$level_human: " . $error->getMessage() . "\n");
			} else{
				echo "pat-$level_human: " . $error->getMessage() . "\n";
			}
		}
		return $error;
	}

	function &handleErrorVerbose(&$error, $options){
		$level_human = patErrorManager::translateErrorLevel($error->getLevel());
		$info = $error->getInfo();
		if(isset($_SERVER['HTTP_HOST'])){

			echo "<br /><b>pat-$level_human</b>: " . $error->getMessage() . "<br />\n";
			if($info != null){
				echo "&nbsp;&nbsp;&nbsp;" . $error->getInfo() . "<br />\n";
			}
			echo $error->getBacktrace(true);
		} else{

			echo "pat-$level_human: " . $error->getMessage() . "\n";
			if($info != null){
				echo "    " . $error->getInfo() . "\n";
			}
		}
		return $error;
	}

	function &handleErrorDie(&$error, $options){
		$level_human = patErrorManager::translateErrorLevel($error->getLevel());
		if(isset($_SERVER['HTTP_HOST'])){

			die("<br /><b>pat-$level_human</b> " . $error->getMessage() . "<br />\n");
		} else{

			if(defined('STDERR')){
				fwrite(STDERR, "pat-$level_human " . $error->getMessage() . "\n");
			} else{
				die("pat-$level_human " . $error->getMessage() . "\n");
			}
		}
		return $error;
	}

	function &handleErrorTrigger(&$error, $options){
		switch($error->getLevel()){
			case E_NOTICE:
				$level = E_USER_NOTICE;
				break;
			case E_WARNING:
				$level = E_USER_WARNING;
				break;
			case E_NOTICE:
				$level = E_NOTICE;
				break;
			default:
				$level = E_USER_ERROR;
				break;
		}
		trigger_error($error->getMessage(), $level);
		return $error;
	}

	function &handleErrorCallback(&$error, $options){
		$opt = $options['options'];
		$result = call_user_func($opt, $error);
		return $result;
	}
}

?>
