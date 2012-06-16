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
class File_Archive_Reader_Memory extends File_Archive_Reader{
	var $filename;
	var $stat;
	var $mime;
	var $memory;
	var $offset = 0;
	var $alreadyRead = false;

	function File_Archive_Reader_Memory(&$memory, $filename, $stat = array(), $mime = null){
		$this->memory = &$memory;
		$this->filename = $this->getStandardURL($filename);
		$this->stat = $stat;
		$this->stat[7] = $this->stat['size'] = strlen($this->memory);
		$this->mime = $mime;
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
		return $this->filename;
	}

	function getStat(){
		return $this->stat;
	}

	function getMime(){
		return $this->mime == null ? parent::getMime() : $this->mime;
	}

	function getData($length = -1){
		if($this->offset == strlen($this->memory)){
			return null;
		}
		if($length == -1){
			$actualLength = strlen($this->memory) - $this->offset;
		} else{
			$actualLength = min($length, strlen($this->memory) - $this->offset);
		}
		$result = substr($this->memory, $this->offset, $actualLength);
		$this->offset += $actualLength;
		return $result;
	}

	function skip($length = -1){
		if($length == -1){
			$length = strlen($this->memory) - $this->offset;
		} else{
			$length = min($length, strlen($this->memory) - $this->offset);
		}
		$this->offset += $length;
		return $length;
	}

	function rewind($length = -1){
		if($length == -1){
			$tmp = $this->offset;
			$this->offset = 0;
			return $tmp;
		} else{
			$length = min($length, $this->offset);
			$this->offset -= $length;
			return $length;
		}
	}

	function tell(){
		return $this->offset;
	}

	function close(){
		$this->offset = 0;
		$this->alreadyRead = false;
	}

	function makeAppendWriter(){
		return PEAR::raiseError('Unable to append files to a memory archive');
	}

	function makeWriterRemoveFiles($pred){
		return PEAR::raiseError('Unable to remove files from a memory archive');
	}

	function makeWriterRemoveBlocks($blocks, $seek = 0){
		require_once dirname(__file__) . "/../Writer/Memory.php";
		$data = substr($this->memory, 0, $this->offset + $seek);
		$this->memory = substr($this->memory, $this->offset + $seek);
		$keep = false;
		foreach($blocks as $length){
			if($keep){
				$data .= substr($this->memory, 0, $length);
			}
			$this->memory = substr($this->memory, $length);
			$keep = !$keep;
		}
		if($keep){
			$this->memory = $data . $this->memory;
		} else{
			$this->memory = $data;
		}
		$this->close();
		return new File_Archive_Writer_Memory($this->memory, strlen($this->memory));
	}
}

?>
