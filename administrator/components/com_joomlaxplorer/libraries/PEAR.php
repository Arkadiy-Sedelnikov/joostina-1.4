<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
**/
defined('_VALID_MOS') or die();
define('PEAR_ERROR_RETURN',1);
define('PEAR_ERROR_PRINT',2);
define('PEAR_ERROR_TRIGGER',4);
define('PEAR_ERROR_DIE',8);
define('PEAR_ERROR_CALLBACK',16);
define('PEAR_ERROR_EXCEPTION',32);
define('PEAR_ZE2',(function_exists('version_compare') && version_compare(zend_version
	(),"2-dev","ge")));
if(substr(PHP_OS,0,3) == 'WIN') {
	define('OS_WINDOWS',true);
	define('OS_UNIX',false);
	define('PEAR_OS','Windows');
} else {
	define('OS_WINDOWS',false);
	define('OS_UNIX',true);
	define('PEAR_OS','Unix');
}
if(!defined('PATH_SEPARATOR')) {
	if(OS_WINDOWS) {
		define('PATH_SEPARATOR',';');
	} else {
		define('PATH_SEPARATOR',':');
	}
}
$GLOBALS['_PEAR_default_error_mode'] = PEAR_ERROR_RETURN;
$GLOBALS['_PEAR_default_error_options'] = E_USER_NOTICE;
$GLOBALS['_PEAR_destructor_object_list'] = array();
$GLOBALS['_PEAR_shutdown_funcs'] = array();
$GLOBALS['_PEAR_error_handler_stack'] = array();
ini_set('track_errors',true);
class PEAR {
	var $_debug = false;
	var $_default_error_mode = null;
	var $_default_error_options = null;
	var $_default_error_handler = '';
	var $_error_class = 'PEAR_Error';
	var $_expected_errors = array();
	function PEAR($error_class = null) {
		$classname = get_class($this);
		if($this->_debug) {
			print "PEAR constructor called, class=$classname\n";
		}
		if($error_class !== null) {
			$this->_error_class = $error_class;
		}
		while($classname) {
			$destructor = "_$classname";
			if(method_exists($this,$destructor)) {
				global $_PEAR_destructor_object_list;
				$_PEAR_destructor_object_list[] = &$this;
				break;
			} else {
				$classname = get_parent_class($classname);
			}
		}
	}
	function _PEAR() {
		if($this->_debug) {
			printf("PEAR destructor called, class=%s\n",get_class($this));
		}
	}
	function &getStaticProperty($class,$var) {
		static $properties;
		return $properties[$class][$var];
	}
	function registerShutdownFunc($func,$args = array()) {
		$GLOBALS['_PEAR_shutdown_funcs'][] = array($func,$args);
	}
	function isError($data,$code = null) {
		if($data instanceof PEAR_Error) {
			if(is_null($code)) {
				return true;
			} elseif(is_string($code)) {
				return $data->getMessage() == $code;
			} else {
				return $data->getCode() == $code;
			}
		}
		return false;
	}
	function setErrorHandling($mode = null,$options = null) {
		if(isset($this) && ($this instanceof PEAR)) {
			$setmode = &$this->_default_error_mode;
			$setoptions = &$this->_default_error_options;
		} else {
			$setmode = &$GLOBALS['_PEAR_default_error_mode'];
			$setoptions = &$GLOBALS['_PEAR_default_error_options'];
		}
		switch($mode) {
			case PEAR_ERROR_EXCEPTION:
			case PEAR_ERROR_RETURN:
			case PEAR_ERROR_PRINT:
			case PEAR_ERROR_TRIGGER:
			case PEAR_ERROR_DIE:
			case null:
				$setmode = $mode;
				$setoptions = $options;
				break;
			case PEAR_ERROR_CALLBACK:
				$setmode = $mode;
				if(is_callable($options)) {
					$setoptions = $options;
				} else {
					trigger_error("invalid error callback",E_USER_WARNING);
				}
				break;
			default:
				trigger_error("invalid error mode",E_USER_WARNING);
				break;
		}
	}
	function expectError($code = '*') {
		if(is_array($code)) {
			array_push($this->_expected_errors,$code);
		} else {
			array_push($this->_expected_errors,array($code));
		}
		return sizeof($this->_expected_errors);
	}
	function popExpect() {
		return array_pop($this->_expected_errors);
	}
	function _checkDelExpect($error_code) {
		$deleted = false;
		foreach($this->_expected_errors as $key => $error_array) {
			if(in_array($error_code,$error_array)) {
				unset($this->_expected_errors[$key][array_search($error_code,$error_array)]);
				$deleted = true;
			}
			if(0 == count($this->_expected_errors[$key])) {
				unset($this->_expected_errors[$key]);
			}
		}
		return $deleted;
	}
	function delExpect($error_code) {
		$deleted = false;
		if((is_array($error_code) && (0 != count($error_code)))) {
			foreach($error_code as $key => $error) {
				if($this->_checkDelExpect($error)) {
					$deleted = true;
				} else {
					$deleted = false;
				}
			}
			return $deleted?true:PEAR::raiseError("The expected error you submitted does not exist");
		} elseif(!empty($error_code)) {
			if($this->_checkDelExpect($error_code)) {
				return true;
			} else {
				return PEAR::raiseError("The expected error you submitted does not exist");
			}
		} else {
			return PEAR::raiseError("The expected error you submitted is empty");
		}
	}
	function raiseError($message = null,$code = null,$mode = null,$options = null,$userinfo = null,
		$error_class = null,$skipmsg = false) {
		if(is_object($message)) {
			$code = $message->getCode();
			$userinfo = $message->getUserInfo();
			$error_class = $message->getType();
			$message->error_message_prefix = '';
			$message = $message->getMessage();
		}
		if(isset($this) && isset($this->_expected_errors) && sizeof($this->_expected_errors) >
			0 && sizeof($exp = end($this->_expected_errors))) {
			if($exp[0] == "*" || (is_int(reset($exp)) && in_array($code,$exp)) || (is_string
				(reset($exp)) && in_array($message,$exp))) {
				$mode = PEAR_ERROR_RETURN;
			}
		}
		if($mode === null) {
			if(isset($this) && isset($this->_default_error_mode)) {
				$mode = $this->_default_error_mode;
				$options = $this->_default_error_options;
			} elseif(isset($GLOBALS['_PEAR_default_error_mode'])) {
				$mode = $GLOBALS['_PEAR_default_error_mode'];
				$options = $GLOBALS['_PEAR_default_error_options'];
			}
		}
		if($error_class !== null) {
			$ec = $error_class;
		} elseif(isset($this) && isset($this->_error_class)) {
			$ec = $this->_error_class;
		} else {
			$ec = 'PEAR_Error';
		}
		if($skipmsg) {
			return new $ec($code,$mode,$options,$userinfo);
		} else {
			return new $ec($message,$code,$mode,$options,$userinfo);
		}
	}
	function throwError($message = null,$code = null,$userinfo = null) {
		if(isset($this) && ($this instanceof PEAR)) {
			return $this->raiseError($message,$code,null,null,$userinfo);
		} else {
			return PEAR::raiseError($message,$code,null,null,$userinfo);
		}
	}
	function pushErrorHandling($mode,$options = null) {
		$stack = &$GLOBALS['_PEAR_error_handler_stack'];
		if(isset($this) && ($this instanceof PEAR)) {
			$def_mode = &$this->_default_error_mode;
			$def_options = &$this->_default_error_options;
		} else {
			$def_mode = &$GLOBALS['_PEAR_default_error_mode'];
			$def_options = &$GLOBALS['_PEAR_default_error_options'];
		}
		$stack[] = array($def_mode,$def_options);
		if(isset($this) && ($this instanceof PEAR)) {
			$this->setErrorHandling($mode,$options);
		} else {
			PEAR::setErrorHandling($mode,$options);
		}
		$stack[] = array($mode,$options);
		return true;
	}
	function popErrorHandling() {
		$stack = &$GLOBALS['_PEAR_error_handler_stack'];
		array_pop($stack);
		list($mode,$options) = $stack[sizeof($stack) - 1];
		array_pop($stack);
		if(isset($this) && is_a($this,'PEAR')) {
			$this->setErrorHandling($mode,$options);
		} else {
			PEAR::setErrorHandling($mode,$options);
		}
		return true;
	}
	function loadExtension($ext) {
		if(!extension_loaded($ext)) {
			if((ini_get('enable_dl') != 1) || (ini_get('safe_mode') == 1)) {
				return false;
			}
			if(OS_WINDOWS) {
				$suffix = '.dll';
			} elseif(PHP_OS == 'HP-UX') {
				$suffix = '.sl';
			} elseif(PHP_OS == 'AIX') {
				$suffix = '.a';
			} elseif(PHP_OS == 'OSX') {
				$suffix = '.bundle';
			} else {
				$suffix = '.so';
			}
			return @dl('php_'.$ext.$suffix) || @dl($ext.$suffix);
		}
		return true;
	}
}
function _PEAR_call_destructors() {
	global $_PEAR_destructor_object_list;
	if(is_array($_PEAR_destructor_object_list) && sizeof($_PEAR_destructor_object_list)) {
		reset($_PEAR_destructor_object_list);
		while(list($k,$objref) = each($_PEAR_destructor_object_list)) {
			$classname = get_class($objref);
			while($classname) {
				$destructor = "_$classname";
				if(method_exists($objref,$destructor)) {
					$objref->$destructor();
					break;
				} else {
					$classname = get_parent_class($classname);
				}
			}
		}
		$_PEAR_destructor_object_list = array();
	}
	if(is_array($GLOBALS['_PEAR_shutdown_funcs']) and !empty($GLOBALS['_PEAR_shutdown_funcs'])) {
		foreach($GLOBALS['_PEAR_shutdown_funcs'] as $value) {
			call_user_func_array($value[0],$value[1]);
		}
	}
}
class PEAR_Error {
	var $error_message_prefix = '';
	var $mode = PEAR_ERROR_RETURN;
	var $level = E_USER_NOTICE;
	var $code = -1;
	var $message = '';
	var $userinfo = '';
	var $backtrace = null;
	function PEAR_Error($message = 'unknown error',$code = null,$mode = null,$options = null,
		$userinfo = null) {
		if($mode === null) {
			$mode = PEAR_ERROR_RETURN;
		}
		$this->message = $message;
		$this->code = $code;
		$this->mode = $mode;
		$this->userinfo = $userinfo;
		if(function_exists("debug_backtrace")) {
			$this->backtrace = debug_backtrace();
		}
		if($mode & PEAR_ERROR_CALLBACK) {
			$this->level = E_USER_NOTICE;
			$this->callback = $options;
		} else {
			if($options === null) {
				$options = E_USER_NOTICE;
			}
			$this->level = $options;
			$this->callback = null;
		}
		if($this->mode & PEAR_ERROR_PRINT) {
			if(is_null($options) || is_int($options)) {
				$format = "%s";
			} else {
				$format = $options;
			}
			printf($format,$this->getMessage());
		}
		if($this->mode & PEAR_ERROR_TRIGGER) {
			trigger_error($this->getMessage(),$this->level);
		}
		if($this->mode & PEAR_ERROR_DIE) {
			$msg = $this->getMessage();
			if(is_null($options) || is_int($options)) {
				$format = "%s";
				if(substr($msg,-1) != "\n") {
					$msg .= "\n";
				}
			} else {
				$format = $options;
			}
			die(sprintf($format,$msg));
		}
		if($this->mode & PEAR_ERROR_CALLBACK) {
			if(is_callable($this->callback)) {
				call_user_func($this->callback,$this);
			}
		}
		if($this->mode & PEAR_ERROR_EXCEPTION) {
			trigger_error("PEAR_ERROR_EXCEPTION is obsolete, use class PEAR_ErrorStack for exceptions",
				E_USER_WARNING);
			eval('$e = new Exception($this->message, $this->code);$e->PEAR_Error = $this;throw($e);');
		}
	}
	function getMode() {
		return $this->mode;
	}
	function getCallback() {
		return $this->callback;
	}
	function getMessage() {
		return ($this->error_message_prefix.$this->message);
	}
	function getCode() {
		return $this->code;
	}
	function getType() {
		return get_class($this);
	}
	function getUserInfo() {
		return $this->userinfo;
	}
	function getDebugInfo() {
		return $this->getUserInfo();
	}
	function getBacktrace($frame = null) {
		if($frame === null) {
			return $this->backtrace;
		}
		return $this->backtrace[$frame];
	}
	function addUserInfo($info) {
		if(empty($this->userinfo)) {
			$this->userinfo = $info;
		} else {
			$this->userinfo .= "** $info";
		}
	}
	function toString() {
		$modes = array();
		$levels = array(E_USER_NOTICE => 'notice',E_USER_WARNING => 'warning',
			E_USER_ERROR => 'error');
		if($this->mode & PEAR_ERROR_CALLBACK) {
			if(is_array($this->callback)) {
				$callback = get_class($this->callback[0]).'::'.$this->callback[1];
			} else {
				$callback = $this->callback;
			}
			return sprintf('[%s: message="%s" code=%d mode=callback '.
				'callback=%s prefix="%s" info="%s"]',get_class($this),$this->message,$this->code,
				$callback,$this->error_message_prefix,$this->userinfo);
		}
		if($this->mode & PEAR_ERROR_PRINT) {
			$modes[] = 'print';
		}
		if($this->mode & PEAR_ERROR_TRIGGER) {
			$modes[] = 'trigger';
		}
		if($this->mode & PEAR_ERROR_DIE) {
			$modes[] = 'die';
		}
		if($this->mode & PEAR_ERROR_RETURN) {
			$modes[] = 'return';
		}
		return sprintf('[%s: message="%s" code=%d mode=%s level=%s '.
			'prefix="%s" info="%s"]',get_class($this),$this->message,$this->code,implode("|",
			$modes),$levels[$this->level],$this->error_message_prefix,$this->userinfo);
	}
}
register_shutdown_function("_PEAR_call_destructors");

?>
