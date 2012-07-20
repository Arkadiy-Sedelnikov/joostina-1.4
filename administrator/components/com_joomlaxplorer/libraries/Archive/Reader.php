<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_JLINDEX') or die();
class File_Archive_Reader{
	function next(){
		return false;
	}

	function select($filename, $close = true){
		$std = $this->getStandardURL($filename);
		if($close){
			$error = $this->close();
			if(PEAR::isError($error)){
				return $error;
			}
		}
		while(($error = $this->next()) === true){
			$sourceName = $this->getFilename();
			if(empty($std) || $std == $sourceName || strncmp($std . '/', $sourceName, strlen($std) +
				1) == 0
			){
				return true;
			}
		}
		return $error;
	}

	function getStandardURL($path){
		if($path == '.'){
			return '';
		}
		$std = str_replace("\\", "/", $path);
		while($std != ($std = preg_replace("/[^\/:?]+\/\.\.\//", "", $std)))
			;
		$std = str_replace("/./", "", $std);
		if(strncmp($std, "./", 2) == 0){
			return substr($std, 2);
		} else{
			return $std;
		}
	}

	function getFilename(){
		return PEAR::raiseError("Reader abstract function call (getFilename)");
	}

	function getFileList(){
		$result = array();
		while(($error = $this->next()) === true){
			$result[] = $this->getFilename();
		}
		$this->close();
		if(PEAR::isError($error)){
			return $error;
		} else{
			return $result;
		}
	}

	function getStat(){
		return array();
	}

	function getMime(){
		require_once dirname(__file__) . "/Reader/MimeList.php";
		return File_Archive_Reader_GetMime($this->getFilename());
	}

	function getDataFilename(){
		return null;
	}

	function getData($length = -1){
		return PEAR::raiseError("Reader abstract function call (getData)");
	}

	function skip($length = -1){
		$data = $this->getData($length);
		if(PEAR::isError($data)){
			return $data;
		} else{
			return strlen($data);
		}
	}

	function rewind($length = -1){
		return PEAR::raiseError('Rewind function is not implemented on this reader');
	}

	function tell(){
		$offset = $this->rewind();
		$this->skip($offset);
		return $offset;
	}

	function close(){
	}

	function sendData(&$writer, $bufferSize = 0){
		if(PEAR::isError($writer)){
			return $writer;
		}
		if($bufferSize <= 0){
			$bufferSize = File_Archive::getOption('blockSize');
		}
		$filename = $this->getDataFilename();
		if($filename !== null){
			$error = $writer->writeFile($filename);
			if(PEAR::isError($error)){
				return $error;
			}
		} else{
			while(($data = $this->getData($bufferSize)) !== null){
				if(PEAR::isError($data)){
					return $data;
				}
				$error = $writer->writeData($data);
				if(PEAR::isError($error)){
					return $error;
				}
			}
		}
	}

	function extract(&$writer, $autoClose = true, $bufferSize = 0){
		if(PEAR::isError($writer)){
			$this->close();
			return $writer;
		}
		while(($error = $this->next()) === true){
			if($writer->newFileNeedsMIME()){
				$mime = $this->getMime();
			} else{
				$mime = null;
			}
			$error = $writer->newFile($this->getFilename(), $this->getStat(), $mime);
			if(PEAR::isError($error)){
				break;
			}
			$error = $this->sendData($writer, $bufferSize);
			if(PEAR::isError($error)){
				break;
			}
		}
		$this->close();
		if($autoClose){
			$writer->close();
		}
		if(PEAR::isError($error)){
			return $error;
		}
	}

	function extractFile($filename, &$writer, $autoClose = true, $bufferSize = 0){
		if(PEAR::isError($writer)){
			return $writer;
		}
		if(($error = $this->select($filename)) === true){
			$result = $this->sendData($writer, $bufferSize);
			if(!PEAR::isError($result)){
				$result = true;
			}
		} else
			if($error === false){
				$result = PEAR::raiseError("File $filename not found");
			} else{
				$result = $error;
			}
		if($autoClose){
			$error = $writer->close();
			if(PEAR::isError($error)){
				return $error;
			}
		}
		return $result;
	}

	function makeAppendWriter(){
		require_once dirname(__file__) . "/Predicate/False.php";
		return $this->makeWriterRemoveFiles(new File_Archive_Predicate_False());
	}

	function makeWriterRemoveFiles($pred){
		return PEAR::raiseError("Reader abstract function call (makeWriterRemoveFiles)");
	}

	function makeWriterRemove(){
		require_once dirname(__file__) . '/Predicate/Current.php';
		return $this->makeWriterRemoveFiles(new File_Archive_Predicate_Current());
	}

	function remove(){
		$writer = $this->makeWriterRemove();
		if(PEAR::isError($writer)){
			return $writer;
		}
		$writer->close();
	}

	function makeWriterRemoveBlocks($blocks, $seek = 0){
		return PEAR::raiseError("Reader abstract function call (makeWriterRemoveBlocks)");
	}
}

?>
