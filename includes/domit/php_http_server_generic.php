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




if(!defined('PHP_HTTP_TOOLS_INCLUDE_PATH')) {
	define('PHP_HTTP_TOOLS_INCLUDE_PATH',(dirname(__file__)."/"));
}
define('CRLF',"\r\n");

define('CR',"\r");
define('LF',"\n");
class php_http_server_generic {
	var $httpStatusCodes;
	var $protocol = 'HTTP';
	var $protocolVersion = '1.0';
	var $statusCode = 200;
	var $events = array('onRequest' => null,'onResponse' => null,'onGet' => null,
		'onHead' => null,'onPost' => null,'onPut' => null);
	function php_http_server_generic() {


	}

	function &getHeaders() {
		$headers = headers_list();
		$response = '';
		if(count($headers) > 0) {
			foreach($headers as $key => $value) {
				$response .= $value.CRLF;
			}
		}
		return $response;
	}

	function setProtocolVersion($version) {
		if(($version == '1.0') || ($version == '1.1')) {
			$$this->protocolVersion = $version;
			return true;
		}
		return false;
	}

	function setHeader($name,$value) {
		header($name.': '.$value);
	}

	function setHeaders() {

		$this->setHeader('Content-Type','text/html');
		$this->setHeader('Server','PHP HTTP Server (Generic)/0.1');
	}

	function fireEvent($target,$data) {
		if($this->events[$target] != null) {
			call_user_func($this->events[$target],$data);
		}
	}

	function fireHTTPEvent($target,$data = null) {
		if($this->events[$target] == null) {


			$this->setHTTPEvent($target);
		}
		call_user_func($this->events[$target],$data);
	}

	function setHTTPEvent($option,$customHandler = null) {
		if($customHandler != null) {
			$handler = &$customHandler;
		} else {
			$handler = array(&$this,'defaultHTTPEventHandler');
		}
		switch($option) {
			case 'onGet':
			case 'onHead':
			case 'onPost':
			case 'onPut':
				$this->events[$option] = &$handler;
				break;
		}
	}

	function defaultHTTPHandler() {


	}

	function setDebug($option,$truthVal,$customHandler = null) {
		if($customHandler != null) {
			$handler = &$customHandler;
		} else {
			$handler = array(&$this,'defaultDebugHandler');
		}
		switch($option) {
			case 'onRequest':
			case 'onResponse':
				$truthVal?($this->events[$option] = &$handler):
				($this->events[$option] = null);
				break;
		}
	}

	function getDebug($option) {
		switch($option) {
			case 'onRequest':
			case 'onResponse':
				return ($this->events[$option] != null);
				break;
		}
	}

	function defaultDebugHandler($data) {

		$this->writeDebug($data);
	}

	function writeDebug($data) {
		$filename = 'debug_'.time().'.txt';
		$fileHandle = fopen($fileName,'a');
		fwrite($fileHandle,$data);
		fclose($fileHandle);
	}

	function receive() {
		global $HTTP_SERVER_VARS;
		$requestMethod = strToUpper($HTTP_SERVER_VARS['REQUEST_METHOD']);
		switch($requestMethod) {
			case 'GET':
				$this->fireHTTPEvent('onGet');
				break;
			case 'HEAD':
				$this->fireHTTPEvent('onHead');
				break;
			case 'POST':
				$this->fireHTTPEvent('onPost');
				break;
			case 'PUT':
				$this->fireHTTPEvent('onPut');
				break;
		}
	}

	function respond($response) {



		if(!headers_sent()) {

			$this->setHeader('Date',"date('r')");
			$this->setHeader('Content-Length',strlen($response));
			$this->setHeader('Connection','Close');
		}
		echo $response;
	}

}







?>
