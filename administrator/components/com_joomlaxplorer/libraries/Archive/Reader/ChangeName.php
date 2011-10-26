<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

defined('_VALID_MOS') or die();
require_once dirname(__file__)."/Relay.php";
class File_Archive_Reader_AddBaseName extends File_Archive_Reader_Relay {
	var $baseName;
	function File_Archive_Reader_AddBaseName($baseName,&$source) {
		parent::File_Archive_Reader_Relay($source);
		$this->baseName = $this->getStandardURL($baseName);
	}
	function modifyName($name) {
		return $this->baseName.(empty($this->baseName) || empty($name)?'':'/').$name;
	}
	function unmodifyName($name) {
		if(strncmp($name,$this->baseName.'/',strlen($this->baseName) + 1) == 0) {
			$res = substr($name,strlen($this->baseName) + 1);
			if($res === false) {
				return '';
			} else {
				return $res;
			}
		} else
			if(empty($this->baseName)) {
				return $name;
			} else
				if($name == $this->baseName) {
					return '';
				} else {
					return false;
				}
	}
	function getFilename() {
		return $this->modifyName(parent::getFilename());
	}
	function getFileList() {
		$list = parent::getFileList();
		$result = array();
		foreach($list as $name) {
			$result[] = $this->modifyName($name);
		}
		return $result;
	}
	function select($filename,$close = true) {
		$name = $this->unmodifyName($filename);
		if($name === false) {
			return false;
		} else {
			return $this->source->select($name,$close);
		}
	}
}
class File_Archive_Reader_ChangeBaseName extends File_Archive_Reader_Relay {
	var $oldBaseName;
	var $newBaseName;
	function File_Archive_Reader_ChangeBaseName($oldBaseName,$newBaseName,&$source) {
		parent::File_Archive_Reader_Relay($source);
		$this->oldBaseName = $this->getStandardURL($oldBaseName);
		if(substr($this->oldBaseName,-1) == '/') {
			$this->oldBaseName = substr($this->oldBaseName,0,-1);
		}
		$this->newBaseName = $this->getStandardURL($newBaseName);
		if(substr($this->newBaseName,-1) == '/') {
			$this->newBaseName = substr($this->newBaseName,0,-1);
		}
	}
	function modifyName($name) {
		if(empty($this->oldBaseName) || !strncmp($name,$this->oldBaseName.'/',strlen($this->oldBaseName) +
			1) || strcmp($name,$this->oldBaseName) == 0) {
			return $this->newBaseName.(empty($this->newBaseName) || strlen($name) <= strlen
				($this->oldBaseName) + 1?'':'/').substr($name,strlen($this->oldBaseName) + 1);
		} else {
			return $name;
		}
	}
	function unmodifyName($name) {
		if(empty($this->newBaseName) || !strncmp($name,$this->newBaseName.'/',strlen($this->newBaseName) +
			1) || strcmp($name,$this->newBaseName) == 0) {
			return $this->oldBaseName.(empty($this->oldBaseName) || strlen($name) <= strlen
				($this->newBaseName) + 1?'':'/').substr($name,strlen($this->newBaseName) + 1);
		} else {
			return $name;
		}
	}
	function getFilename() {
		return $this->modifyName(parent::getFilename());
	}
	function getFileList() {
		$list = parent::getFileList();
		$result = array();
		foreach($list as $name) {
			$result[] = $this->modifyName($name);
		}
		return $result;
	}
	function select($filename,$close = true) {
		return $this->source->select($this->unmodifyName($filename));
	}
}

?>
