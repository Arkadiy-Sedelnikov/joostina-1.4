<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*
* dom_xmlrpc_array_document wraps a PHP array with the DOM XML-RPC API
* @package dom-xmlrpc
* @copyright (C) 2004 John Heinstein. All rights reserved
* @license http://www.gnu.org/copyleft/lesser.html LGPL License
* @author John Heinstein <johnkarl@nbnet.nb.ca>
* @link http://www.engageinteractive.com/dom_xmlrpc/ DOM XML-RPC Home Page
* DOM XML-RPC is Free Software
**/

defined('_VALID_MOS') or die();
define('HTTP_SOCKET_CONNECTION_ERR',1);
define('HTTP_TRANSPORT_ERR',2);

define('HTTP_ONERROR_CONTINUE',1);
define('HTTP_ONERROR_DIE',2);
$GLOBALS['HTTP_Exception_errorHandler'] = null;


$GLOBALS['HTTP_Exception_mode'] = 1;
$GLOBALS['HTTP_Exception_log'] = null;
class HTTPExceptions {
	function raiseException($errorNum,$errorString) {

		if($GLOBALS['HTTP_Exception_errorHandler'] != null) {
			call_user_func($GLOBALS['HTTP_Exception_errorHandler'],$errorNum,$errorString);
		} else {
			$errorMessageText = $errorNum.' '.$errorString;
			$errorMessage = 'Error: '.$errorMessageText;
			if((!isset($GLOBALS['HTTP_ERROR_FORMATTING_HTML'])) || ($GLOBALS['HTTP_ERROR_FORMATTING_HTML'] == true)) {
				$errorMessage = "<p><pre>".$errorMessage."</pre></p>";
			}

			if((isset($GLOBALS['HTTP_Exception_log'])) && ($GLOBALS['HTTP_Exception_log'] != null)) {
				require_once (PHP_HTTP_TOOLS_INCLUDE_PATH.'php_file_utilities.php');
				$logItem = "\n".date('Y-m-d H:i:s').' HTTP Error '.$errorMessageText;
				php_file_utilities::putDataToFile($GLOBALS['HTTP_Exception_log'],$logItem,'a');
			}
			switch($GLOBALS['HTTP_Exception_mode']) {
				case HTTP_ONERROR_CONTINUE:
					return;
					break;
				case HTTP_ONERROR_DIE:
					die($errorMessage);
					break;
			}
		}
	}

	function setErrorHandler($method) {
		$GLOBALS['HTTP_Exception_errorHandler'] = &$method;
	}

	function setErrorMode($mode) {
		$GLOBALS['HTTP_Exception_mode'] = $mode;
	}

	function setErrorLog($doLogErrors,$logfile) {
		if($doLogErrors) {
			$GLOBALS['HTTP_Exception_log'] = $logfile;
		} else {
			$GLOBALS['HTTP_Exception_log'] = null;
		}
	}

}




?>
