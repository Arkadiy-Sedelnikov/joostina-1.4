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



class php_http_status_codes {
	var $codes;
	function php_http_status_codes() {
		$this->codes = array(200 => 'OK',201 => 'CREATED',202 => 'Accepted',203 =>
			'Partial Information',204 => 'No Response',301 => 'Moved',302 => 'Found',303 =>
			'Method',304 => 'Not Modified',400 => 'Bad Request',401 => 'Unauthorized',402 =>
			'PaymentRequired',403 => 'Forbidden',404 => 'Not found',500 => 'Internal Error',
			501 => 'Not implemented',502 => 'Service temporarily overloaded',503 =>
			'Gateway timeout');
	}

	function getCodes() {
		return $this->codes;
	}

	function getCodeString($code) {
		return $this->codes[$code];
	}

}




?>
