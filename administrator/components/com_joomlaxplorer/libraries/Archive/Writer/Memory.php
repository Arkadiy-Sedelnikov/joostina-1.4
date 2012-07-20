<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_JLINDEX') or die();
require_once dirname(__file__) . "/../Writer.php";
class File_Archive_Writer_Memory extends File_Archive_Writer{
	var $data = "";
	var $filename;
	var $stat;
	var $mime;

	function File_Archive_Writer_Memory(&$data, $seek = 0){
		$this->data = &$data;
		$this->data = substr($data, 0, $seek);
	}

	function writeData($d){
		$this->data .= $d;
	}

	function newFile($filename, $stat, $mime = "application/octet-stream"){
		$this->filename = $filename;
		$this->stat = $stat;
		$this->mime = $mime;
	}

	function newFileNeedsMIME(){
		return true;
	}

	function &getData(){
		return $this->data;
	}

	function clear(){
		$this->data = "";
	}

	function isEmpty(){
		return empty($this->data);
	}

	function makeReader($filename = null, $stat = null, $mime = null){
		require_once dirname(__file__) . "/../Reader/Memory.php";
		return new File_Archive_Reader_Memory($this->data, $filename === null ? $this->filename :
			$filename, $stat === null ? $this->stat : $stat, $mime === null ? $this->mime : $mime);
	}
}

?>
