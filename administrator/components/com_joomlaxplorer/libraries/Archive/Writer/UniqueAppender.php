<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_VALID_MOS') or die();
require_once dirname(__file__) . "/../Writer.php";
require_once dirname(__file__) . "/../Reader.php";
require_once dirname(__file__) . "/../Predicate/Index.php";
class File_Archive_Writer_UniqueAppender extends File_Archive_Writer{
	var $reader;
	var $writer;
	var $fileList = array();
	var $toDelete = array();

	function File_Archive_Writer_UniqueAppender(&$reader){
		$reader->close();
		$pos = 0;
		while($reader->next()){
			$this->fileList[$reader->getFilename()] = $pos++;
		}
		$this->reader = &$reader;
		$this->writer = $reader->makeAppendWriter();
	}

	function newFile($filename, $stat = array(), $mime = "application/octet-stream"){
		if(isset($this->fileList[$filename])){
			$this->toDelete[$this->fileList[$filename]] = true;
		}
		return $this->writer->newFile($filename, $stat, $mime);
	}

	function newFromTempFile($tmpfile, $filename, $stat = array(), $mime =
	"application/octet-stream"){
		if(isset($this->fileList[$filename])){
			$this->toDelete[$this->fileList[$filename]] = true;
		}
		return $this->writer->newFromTempFile($tmpfile, $filename, $stat, $mime);
	}

	function newFileNeedsMIME(){
		return $this->writer->newFileNeedsMIME();
	}

	function writeData($data){
		return $this->writer->writeData($data);
	}

	function writeFile($filename){
		return $this->writer->writeFile($filename);
	}

	function close(){
		$error = $this->writer->close();
		if(PEAR::isError($error)){
			return $error;
		}
		if(!empty($this->toDelete)){
			$tmp = $this->reader->makeWriterRemoveFiles(new File_Archive_Predicate_Index($this->toDelete));
			if(PEAR::isError($tmp)){
				return $tmp;
			}
			return $tmp->close();
		}
	}
}

?>
