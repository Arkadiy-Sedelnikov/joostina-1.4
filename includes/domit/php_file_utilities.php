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
if(!defined('PHP_TEXT_CACHE_INCLUDE_PATH')) {
	define('PHP_TEXT_CACHE_INCLUDE_PATH',(dirname(__file__)."/"));
}
class php_file_utilities {
	function &getDataFromFile($filename,$readAttributes,$readSize = 8192) {
		$fileContents = null;
		$fileHandle = @fopen($filename,$readAttributes);
		if($fileHandle) {
			do {
				$data = fread($fileHandle,$readSize);
				if(Jstring::strlen($data) == 0) {
					break;
				}
				$fileContents .= $data;
			} while(true);
			fclose($fileHandle);
		}
		$fileContents = Jstring::to_utf8($fileContents);
		return $fileContents;
	}

	public static function putDataToFile($fileName,&$data,$writeAttributes) {
		$fileHandle = @fopen($fileName,$writeAttributes);
		/* нехорошо так делать, но работает */
		$data = str_ireplace(' encoding="windows-1251"',' encoding="utf-8"',$data);
		$data = Jstring::to_utf8($data);
		if($fileHandle) {
			fwrite($fileHandle,$data);
			fclose($fileHandle);
		}
	}

}