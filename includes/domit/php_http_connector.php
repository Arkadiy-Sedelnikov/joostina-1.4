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
class php_http_connector {
	var $httpConnection = null;
	function setConnection($host,$path = '/',$port = 80,$timeout = 0,$user = null,$password = null) {
		require_once (PHP_HTTP_TOOLS_INCLUDE_PATH.'php_http_client_generic.php');
		$this->httpConnection = new php_http_client_generic($host,$path,$port,$timeout,
			$user,$password);
	}

	function setAuthorization($user,$password) {
		$this->httpConnection->setAuthorization($user,$password);
	}

	function setProxyConnection($host,$path = '/',$port = 80,$timeout = 0,$user = null,
		$password = null) {
		require_once (PHP_HTTP_TOOLS_INCLUDE_PATH.'php_http_proxy.php');
		$this->httpConnection = new php_http_proxy($host,$path,$port,$timeout,$user,$password);
	}

	function setProxyAuthorization($user,$password) {
		$this->httpConnection->setProxyAuthorization($user,$password);
	}

}




?>
