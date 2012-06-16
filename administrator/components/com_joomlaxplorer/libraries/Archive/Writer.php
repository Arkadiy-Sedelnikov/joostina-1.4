<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_VALID_MOS') or die();
class File_Archive_Writer{
	function newFile($filename, $stat = array(), $mime = "application/octet-stream"){
	}

	function newFromTempFile($tmpfile, $filename, $stat = array(), $mime =
	"application/octet-stream"){
		$this->newFile($filename, $stat, $mime);
		$this->writeFile($tmpfile);
		unlink($tmpfile);
	}

	function newFileNeedsMIME(){
		return false;
	}

	function writeData($data){
	}

	function writeFile($filename){
		$handle = fopen($filename, "r");
		if(!is_resource($handle)){
			return PEAR::raiseError("Unable to write to $filename");
		}
		while(!feof($handle)){
			$error = $this->writeData(fread($handle, 102400));
			if(PEAR::isError($error)){
				return $error;
			}
		}
		fclose($handle);
	}

	function close(){
	}
}

?>
