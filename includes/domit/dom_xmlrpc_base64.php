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
if(!defined('DOM_XMLRPC_INCLUDE_PATH')) {
	define('DOM_XMLRPC_INCLUDE_PATH',(dirname(__file__)."/"));
}
class dom_xmlrpc_base64 {
	var $stringData;
	function fromBinary($binaryData) {

		$this->stringData = $this->encode($binaryData);
	}

	function fromFile($fileName) {

		require_once (DOM_XMLRPC_INCLUDE_PATH.'php_file_utilities.php');
		$binaryData = &php_file_utilities::getDataFromFile($fileName,'rb');
		$this->stringData = $this->encode($binaryData);
	}

	function fromString($stringData) {



		$this->stringData = $stringData;
	}

	function convertToRFC2045($stringData) {
		return chunk_split($stringData);
	}

	function &encode($binaryData) {

		return chunk_split(base64_encode($binaryData));
	}

	function &decode($stringData) {

		return base64_decode($stringData);
	}

	function &getBinary() {
		return $this->decode($this->stringData);
	}

	function getEncoded() {
		return $this->stringData;
	}

}



?>
