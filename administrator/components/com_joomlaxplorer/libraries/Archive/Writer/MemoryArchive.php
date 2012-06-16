<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_VALID_MOS') or die();
require_once dirname(__file__) . "/Archive.php";
require_once dirname(__file__) . "/Memory.php";
class File_Archive_Writer_MemoryArchive extends File_Archive_Writer_Archive{
	var $buffer = '';
	var $currentFilename = null;
	var $currentStat = null;
	var $currentDataFile = null;
	var $nbFiles = 0;

	function File_Archive_Writer_MemoryArchive($filename, &$t, $stat = array(), $autoClose = true){
		parent::File_Archive_Writer_Archive($filename, $t, $stat, $autoClose);
	}

	function newFile($filename, $stat = array(), $mime = "application/octet-stream"){
		if($this->nbFiles == 0){
			$error = $this->sendHeader();
			if(PEAR::isError($error)){
				return $error;
			}
		} else{
			$error = $this->flush();
			if(PEAR::isError($error)){
				return $error;
			}
		}
		$this->nbFiles++;
		$this->currentFilename = $filename;
		$this->currentStat = $stat;
		return true;
	}

	function close(){
		$error = $this->flush();
		if(PEAR::isError($error)){
			return $error;
		}
		$error = $this->sendFooter();
		if(PEAR::isError($error)){
			return $error;
		}
		return parent::close();
	}

	function flush(){
		if($this->currentFilename !== null){
			if($this->currentDataFile !== null){
				$error = $this->appendFile($this->currentFilename, $this->currentDataFile);
			} else{
				$error = $this->appendFileData($this->currentFilename, $this->currentStat, $this->buffer);
			}
			if(PEAR::isError($error)){
				return $error;
			}
			$this->currentFilename = null;
			$this->currentDataFile = null;
			$this->buffer = '';
		}
	}

	function writeData($data){
		if($this->currentDataFile !== null){
			$this->buffer .= file_get_contents($this->currentDataFile);
			$this->currentDataFile = null;
		}
		$this->buffer .= $data;
	}

	function writeFile($filename){
		if($this->currentDataFile === null && empty($this->buffer)){
			$this->currentDataFile = $filename;
		} else{
			if($this->currentDataFile !== null){
				$this->buffer .= file_get_contents($this->currentDataFile);
				$this->currentDataFile = null;
			}
			$this->buffer .= file_get_contents($filename);
		}
	}

	function appendFileData($filename, $stat, &$data){
	}

	function sendHeader(){
	}

	function sendFooter(){
	}

	function appendFile($filename, $dataFilename){
		return $this->appendFileData($filename, stat($dataFilename), file_get_contents($dataFilename));
	}
}

?>
