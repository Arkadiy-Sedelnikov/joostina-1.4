<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_VALID_MOS') or die();
require_once dirname(__file__) . "/../Reader.php";
require_once dirname(__file__) . "/../../MIME/Type.php";
class File_Archive_Reader_File extends File_Archive_Reader{
	var $handle = null;
	var $filename;
	var $symbolic;
	var $stat = null;
	var $mime = null;
	var $alreadyRead = false;

	function File_Archive_Reader_File($filename, $symbolic = null, $mime = null){
		$this->filename = $filename;
		$this->mime = $mime;
		if($symbolic === null){
			$this->symbolic = $this->getStandardURL($filename);
		} else{
			$this->symbolic = $this->getStandardURL($symbolic);
		}
	}

	function close(){
		$this->alreadyRead = false;
		if($this->handle !== null){
			fclose($this->handle);
			$this->handle = null;
		}
	}

	function next(){
		if($this->alreadyRead){
			return false;
		} else{
			$this->alreadyRead = true;
			return true;
		}
	}

	function getFilename(){
		return $this->symbolic;
	}

	function getDataFilename(){
		return $this->filename;
	}

	function getStat(){
		if($this->stat === null){
			$this->stat = @stat($this->filename);
			if($this->stat === false){
				$this->stat = array();
			}
		}
		return $this->stat;
	}

	function getMime(){
		if($this->mime === null){
			PEAR::pushErrorHandling(PEAR_ERROR_RETURN);
			$this->mime = MIME_Type::autoDetect($this->getDataFilename());
			PEAR::popErrorHandling();
			if(PEAR::isError($this->mime)){
				$this->mime = parent::getMime();
			}
		}
		return $this->mime;
	}

	function _ensureFileOpened(){
		if($this->handle === null){
			$this->handle = @fopen($this->filename, "r");
			if(!is_resource($this->handle)){
				$this->handle = null;
				return PEAR::raiseError("Can't open {$this->filename} for reading");
			}
			if($this->handle === false){
				$this->handle = null;
				return PEAR::raiseError("File {$this->filename} not found");
			}
		}
	}

	function getData($length = -1){
		$error = $this->_ensureFileOpened();
		if(PEAR::isError($error)){
			return $error;
		}
		if(feof($this->handle)){
			return null;
		}
		if($length == -1){
			$contents = '';
			$blockSize = File_Archive::getOption('blockSize');
			while(!feof($this->handle)){
				$contents .= fread($this->handle, $blockSize);
			}
			return $contents;
		} else{
			if($length == 0){
				return "";
			} else{
				return fread($this->handle, $length);
			}
		}
	}

	function skip($length = -1){
		$error = $this->_ensureFileOpened();
		if(PEAR::isError($error)){
			return $error;
		}
		$before = ftell($this->handle);
		if(($length == -1 && @fseek($this->handle, 0, SEEK_END) === -1) || ($length >= 0 &&
			@fseek($this->handle, $length, SEEK_CUR) === -1)
		){
			return parent::skip($length);
		} else{
			return ftell($this->handle) - $before;
		}
	}

	function rewind($length = -1){
		if($this->handle === null){
			return 0;
		}
		$before = ftell($this->handle);
		if(($length == -1 && @fseek($this->handle, 0, SEEK_SET) === -1) || ($length >= 0 &&
			@fseek($this->handle, -$length, SEEK_CUR) === -1)
		){
			return parent::rewind($length);
		} else{
			return $before - ftell($this->handle);
		}
	}

	function tell(){
		if($this->handle === null){
			return 0;
		} else{
			return ftell($this->handle);
		}
	}

	function makeWriterRemoveFiles($pred){
		return PEAR::raiseError('File_Archive_Reader_File represents a single file, you cant remove it');
	}

	function makeWriterRemoveBlocks($blocks, $seek = 0){
		require_once dirname(__file__) . '/../Writer/Files.php';
		$writer = new File_Archive_Writer_Files();
		$file = $this->getDataFilename();
		$pos = $this->tell();
		$this->close();
		$writer->openFileRemoveBlock($file, $pos + $seek, $blocks);
		return $writer;
	}

	function makeAppendWriter(){
		return PEAR::raiseError('File_Archive_Reader_File represents a single file.' .
			' makeAppendWriter cant be executed on it');
	}
}

?>
