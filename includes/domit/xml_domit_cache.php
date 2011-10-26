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
define('DOMIT_FILE_EXTENSION_CACHE','dch');
class DOMIT_cache {
	function toCache($xmlFileName,&$doc,$writeAttributes = 'w') {
		require_once (DOMIT_INCLUDE_PATH.'xml_domit_utilities.php');
		require_once (DOMIT_INCLUDE_PATH.'php_file_utilities.php');
		$name = DOMIT_Utilities::removeExtension($xmlFileName).'.'.
			DOMIT_FILE_EXTENSION_CACHE;
		php_file_utilities::putDataToFile($name,serialize($doc),$writeAttributes);
		return (file_exists($name) && is_writable($name));
	}

	function &fromCache($xmlFileName) {
		require_once (DOMIT_INCLUDE_PATH.'xml_domit_utilities.php');
		require_once (DOMIT_INCLUDE_PATH.'php_file_utilities.php');
		$name = DOMIT_Utilities::removeExtension($xmlFileName).'.'.
			DOMIT_FILE_EXTENSION_CACHE;
		$fileContents = &php_file_utilities::getDataFromFile($name,'r');
		$newxmldoc = &unserialize($fileContents);
		return $newxmldoc;
	}

	function cacheExists($xmlFileName) {
		require_once (DOMIT_INCLUDE_PATH.'xml_domit_utilities.php');
		$name = DOMIT_Utilities::removeExtension($xmlFileName).'.'.
			DOMIT_FILE_EXTENSION_CACHE;
		return file_exists($name);
	}

	function removeFromCache($xmlFileName) {
		require_once (DOMIT_INCLUDE_PATH.'xml_domit_utilities.php');
		$name = DOMIT_Utilities::removeExtension($xmlFileName).'.'.
			DOMIT_FILE_EXTENSION_CACHE;
		return unlink($name);
	}

}



?>
