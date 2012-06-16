<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_VALID_MOS') or die();
require_once dirname(__file__) . "/Relay.php";
require_once dirname(__file__) . "/File.php";
class File_Archive_Reader_Directory extends File_Archive_Reader_Relay{
	var $directory;
	var $maxRecurs;
	var $directoryHandle = null;

	function File_Archive_Reader_Directory($directory, $symbolic = '', $maxRecurs = -
	1){
		parent::File_Archive_Reader_Relay($tmp = null);
		$this->directory = empty($directory) ? '.' : $directory;
		$this->symbolic = $this->getStandardURL($symbolic);
		$this->maxRecurs = $maxRecurs;
	}

	function close(){
		$error = parent::close();
		if($this->directoryHandle !== null){
			closedir($this->directoryHandle);
			$this->directoryHandle = null;
		}
		return $error;
	}

	function next(){
		if($this->directoryHandle === null){
			$this->directoryHandle = opendir($this->directory);
			if(!is_resource($this->directoryHandle)){
				return PEAR::raiseError("Directory {$this->directory} not found");
			}
		}
		while($this->source === null || ($error = $this->source->next()) !== true){
			if($this->source !== null){
				$this->source->close();
			}
			$file = readdir($this->directoryHandle);
			if($file == '.' || $file == '..'){
				continue;
			}
			if($file === false){
				return false;
			}
			$current = $this->directory . '/' . $file;
			if(is_dir($current)){
				if($this->maxRecurs != 0){
					$this->source = new File_Archive_Reader_Directory($current, $file . '/', $this->maxRecurs -
						1);
				}
			} else{
				$this->source = new File_Archive_Reader_File($current, $file);
			}
		}
		return $error;
	}

	function getFilename(){
		return $this->symbolic . parent::getFilename();
	}

	function makeWriterRemoveFiles($pred){
		if($source !== null && $pred->isTrue($this)){
			$toUnlink = $this->getDataFilename();
		} else{
			$toUnlink = null;
		}
		while($this->next()){
			if($toUnlink !== null && !@unlink($toUnlink)){
				return PEAR::raiseError("Unable to unlink $toUnlink");
			}
			$toUnlink = ($pred->isTrue($this) ? $this->getDataFilename() : null);
		}
		if($toUnlink !== null && !@unlink("Unable to unlink $toUnlink")){
			return PEAR::raiseError($pred);
		}
		require_once dirname(__file__) . "/../Writer/Files.php";
		$writer = new File_Archive_Writer_Files($this->directory);
		$this->close();
		return $writer;
	}

	function &getLastSource(){
		if($this->source === null || ($this->source instanceof  File_Archive_Reader_File)){
			return $this->source;
		} else{
			return $this->source->getLastSource();
		}
	}

	function makeWriterRemoveBlocks($blocks, $seek = 0){
		$lastSource = &$this->getLastSource();
		if($lastSource === null){
			return PEAR::raiseError('No file selected');
		}
		require_once dirname(__file__) . "/../Writer/Files.php";
		$writer = $lastSource->makeWriterRemoveBlocks($blocks, $seek);
		if(!PEAR::isError($writer)){
			$writer->basePath = $this->directory;
			$this->close();
		}
		return $writer;
	}

	function makeAppendWriter(){
		require_once dirname(__file__) . "/../Writer/Files.php";
		if($this->source === null || ($this->source instanceof  File_Archive_Reader_File)){
			$writer = new File_Archive_Writer_Files($this->directory);
		} else{
			$writer = $this->source->makeAppendWriter($seek);
		}
		$this->close();
		return $writer;
	}
}

?>
