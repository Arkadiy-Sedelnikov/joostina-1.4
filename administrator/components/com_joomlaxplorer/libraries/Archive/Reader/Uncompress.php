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
require_once dirname(__file__) . "/ChangeName.php";
class File_Archive_Reader_Uncompress extends File_Archive_Reader_Relay{
	var $readers = array();
	var $toClose = array();
	var $startReader;
	var $uncompressionLevel;
	var $baseDir = '';
	var $baseDirCompressionLevel = null;
	var $baseDirProgression = 0;
	var $currentFileNotDisplayed = false;

	function File_Archive_Reader_Uncompress(&$innerReader, $uncompressionLevel = -1){
		parent::File_Archive_Reader_Relay($innerReader);
		$this->startReader = &$innerReader;
		$this->uncompressionLevel = $uncompressionLevel;
	}

	function push(){
		if($this->uncompressionLevel >= 0 && $this->baseDirCompressionLevel !== null &&
			count($this->readers) >= $this->uncompressionLevel
		){
			return false;
		}
		$filename = $this->source->getFilename();
		$extensions = explode('.', strtolower($filename));
		$reader = &$this->source;
		$nbUncompressions = 0;
		while(($extension = array_pop($extensions)) !== null){
			$nbUncompressions++;
			unset($next);
			$next = File_Archive::readArchive($extension, $reader, $nbUncompressions == 1);
			if($next === false){
				$extensions = array();
			} else{
				unset($reader);
				$reader = &$next;
			}
		}
		if($nbUncompressions == 1){
			return false;
		} else{
			$this->readers[count($this->readers)] = &$this->source;
			unset($this->source);
			$this->source = new File_Archive_Reader_AddBaseName($filename, $reader);
			return true;
		}
	}

	function next(){
		if($this->currentFileNotDisplayed){
			$this->currentFileNotDisplayed = false;
			return true;
		}
		do{
			do{
				$selection = substr($this->baseDir, 0, $this->baseDirProgression);
				if($selection === false){
					$selection = '';
				}
				$error = $this->source->select($selection, false);
				if(PEAR::isError($error)){
					return $error;
				}
				if(!$error){
					if(empty($this->readers)){
						return false;
					}
					$this->source->close();
					unset($this->source);
					$this->source = &$this->readers[count($this->readers) - 1];
					unset($this->readers[count($this->readers) - 1]);
				}
			} while(!$error);
			$filename = $this->source->getFilename();
			if(strlen($filename) < strlen($this->baseDir)){
				$goodFile = (strncmp($filename, $this->baseDir, strlen($filename)) == 0 && $this->baseDir{
				strlen($filename)}
					== '/');
				if($goodFile){
					if(strlen($filename) + 2 < strlen($this->baseDirProgression)){
						$this->baseDirProgression = strpos($this->baseDir, '/', strlen($filename) + 2);
						if($this->baseDirProgression === false){
							$this->baseDirProgression = strlen($this->baseDir);
						}
					} else{
						$this->baseDirProgression = strlen($this->baseDir);
					}
				}
			} else{
				$goodFile = (strncmp($filename, $this->baseDir, strlen($this->baseDir)) == 0);
				if($goodFile){
					$this->baseDirProgression = strlen($this->baseDir);
				}
			}
		} while($goodFile && $this->push());
		return true;
	}

	function setBaseDir($baseDir){
		$this->baseDir = $baseDir;
		$this->baseDirProgression = strpos($baseDir, '/');
		if($this->baseDirProgression === false){
			$this->baseDirProgression = strlen($baseDir);
		}
		$error = $this->next();
		if($error === false){
			return PEAR::raiseError("No directory $baseDir in inner reader");
		} else
			if(PEAR::isError($error)){
				return $error;
			}
		$this->currentFileNotDisplayed = true;
		return strlen($this->getFilename()) > strlen($baseDir);
	}

	function select($filename, $close = true){
		if($close){
			$error = $this->close();
			if(PEAR::isError($close)){
				return $error;
			}
		}
		$oldBaseDir = $this->baseDir;
		$oldProgression = $this->baseDirProgression;
		$this->baseDir = $filename;
		$this->baseDirProgression = 0;
		$res = $this->next();
		$this->baseDir = $oldBaseDir;
		$this->baseDirProgression = $oldProgression;
		return $res;
	}

	function close(){
		for($i = 0; $i < count($this->readers); ++$i){
			$this->readers[$i]->close();
		}
		for($i = 0; $i < count($this->toClose); ++$i){
			if($this->toClose[$i] !== null){
				$this->toClose[$i]->close();
			}
		}
		$this->readers = array();
		$this->toClose = array();
		$error = parent::close();
		$this->baseDirCompressionLevel = null;
		$this->baseDirProgression = 0;
		unset($this->source);
		$this->source = &$this->startReader;
		$this->source->close();
		$this->currentFileNotDisplayed = false;
		return $error;
	}

	function makeAppendWriter(){
		$error = $this->next();
		if(PEAR::isError($error)){
			return $error;
		}
		return parent::makeAppendWriter();
	}

	function makeWriterRemoveFiles($pred){
		$error = $this->next();
		if(PEAR::isError($error)){
			return $error;
		}
		return parent::makeWriterRemoveFiles($pred);
	}
}

?>
