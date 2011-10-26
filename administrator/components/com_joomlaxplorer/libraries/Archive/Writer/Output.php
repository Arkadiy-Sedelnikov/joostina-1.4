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
class File_Archive_Writer_Output extends File_Archive_Writer {
	var $sendHeaders;
	function File_Archive_Writer_Output($sendHeaders = true) {
		$this->sendHeaders = $sendHeaders;
	}
	function newFile($filename,$stat = array(),$mime = "application/octet-stream") {
		if($this->sendHeaders) {
			if(headers_sent()) {
				return PEAR::raiseError('The headers have already been sent. '.
					'Use File_Archive::toOutput(false) to write '.
					'to output without sending headers');
			}
			header("Content-type: $mime");
			header("Content-disposition: attachment; filename=$filename");
			$this->sendHeaders = false;
		}
	}
	function newFileNeedsMIME() {
		return $this->sendHeaders;
	}
	function writeData($data) {
		echo $data;
	}
	function writeFile($filename) {
		readfile($filename);
	}
}

?>
