<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

defined('_VALID_MOS') or die();
require_once dirname(__file__)."/../Writer.php";
class File_Archive_Writer_Multi extends File_Archive_Writer {
	var $writers;
	function addWriter(&$writer) {
		$this->writers[] = &$writer;
	}
	function newFile($filename,$stat = array(),$mime = "application/octet-stream") {
		$globalError = null;
		foreach($this->writers as $key => $foo) {
			$error = $this->writers[$key]->newFile($filename,$stat,$mime);
			if(PEAR::isError($error)) {
				$globalError = $error;
			}
		}
		if(PEAR::isError($globalError)) {
			return $globalError;
		}
	}
	function newFileNeedsMIME() {
		foreach($this->writers as $key => $foo) {
			if($this->writers[$key]->newFileNeedsMIME()) {
				return true;
			}
		}
		return false;
	}
	function writeData($data) {
		$globalError = null;
		foreach($this->writers as $key => $foo) {
			$error = $this->writers[$key]->writeData($data);
			if(PEAR::isError($error)) {
				$globalError = $error;
			}
		}
		if(PEAR::isError($globalError)) {
			return $globalError;
		}
	}
	function writeFile($filename) {
		$globalError = null;
		foreach($this->writers as $key => $foo) {
			$error = $this->writers[$key]->writeFile($filename);
			if(PEAR::isError($error)) {
				$globalError = $error;
			}
		}
		if(PEAR::isError($globalError)) {
			return $globalError;
		}
	}
	function close() {
		$globalError = null;
		foreach($this->writers as $key => $foo) {
			$error = $this->writers[$key]->close();
			if(PEAR::isError($error)) {
				$globalError = $error;
			}
		}
		if(PEAR::isError($globalError)) {
			return $globalError;
		}
	}
}
?>
