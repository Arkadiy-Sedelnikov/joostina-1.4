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
class File_Archive_Writer_Archive extends File_Archive_Writer {
	var $innerWriter;
	var $autoClose;
	function File_Archive_Writer_Archive($filename,&$innerWriter,$stat = array(),$autoClose = true) {
		$this->innerWriter = &$innerWriter;
		$this->autoClose = $autoClose;
		if($filename !== null) {
			$this->innerWriter->newFile($filename,$stat,$this->getMime());
		}
	}
	function getMime() {
		return "application/octet-stream";
	}
	function close() {
		if($this->autoClose) {
			return $this->innerWriter->close();
		}
	}
}

?>
