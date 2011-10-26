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
class File_Archive_Writer_Gzip extends File_Archive_Writer {
	var $compressionLevel = 9;
	var $gzfile;
	var $tmpName;
	var $nbFiles = 0;
	var $innerWriter;
	var $autoClose;
	var $filename;
	var $stat;
	function File_Archive_Writer_Gzip($filename,&$innerWriter,$stat = array(),$autoClose = true) {
		$this->innerWriter = &$innerWriter;
		$this->autoClose = $autoClose;
		$this->filename = $filename;
		$this->stat = $stat;
		if($this->filename === null) {
			$this->newFile(null);
		}
		$compressionLevel = File_Archive::getOption('gzCompressionLevel',9);
	}
	function setCompressionLevel($compressionLevel) {
		$this->compressionLevel = $compressionLevel;
	}
	function newFile($filename,$stat = array(),$mime = "application/octet-stream") {
		if($this->nbFiles > 1) {
			return PEAR::raiseError("A Gz archive can only contain one single file.".
				"Use Tgz archive to be able to write several files");
		}
		$this->nbFiles++;
		$this->tmpName = tempnam(File_Archive::getOption('tmpDirectory'),'far');
		$this->gzfile = gzopen($this->tmpName,'w'.$this->compressionLevel);
		return true;
	}
	function close() {
		gzclose($this->gzfile);
		if($this->filename === null) {
			$this->innerWriter->writeFile($this->tmpName);
			unlink($this->tmpName);
		} else {
			$this->innerWriter->newFromTempFile($this->tmpName,$this->filename,$this->stat,
				'application/x-compressed');
		}
		if($this->autoClose) {
			return $this->innerWriter->close();
		}
	}
	function writeData($data) {
		gzwrite($this->gzfile,$data);
	}
}

?>
