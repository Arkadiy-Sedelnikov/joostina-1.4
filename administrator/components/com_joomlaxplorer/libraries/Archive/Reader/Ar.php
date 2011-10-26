<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

defined('_VALID_MOS') or die();
require_once dirname(__file__)."/Archive.php";
class File_Archive_Reader_Ar extends File_Archive_Reader_Archive {
	var $_nbBytesLeft = 0;
	var $_header = 0;
	var $_footer = false;
	var $_alreadyRead = false;
	var $_currentFilename = null;
	var $_currentStat = null;
	function getFilename() {
		return $this->_currentFilename;
	}
	function close() {
		$this->_currentFilename = null;
		$this->_currentStat = null;
		$this->_nbBytesLeft = 0;
		$this->_header = 0;
		$this->_footer = false;
		$this->_alreadyRead = false;
		return parent::close();
	}
	function getStat() {
		return $this->_currentStat;
	}
	function next() {
		$error = parent::next();
		if($error !== true) {
			return $error;
		}
		$this->source->skip($this->_nbBytesLeft + ($this->_footer?1:0));
		$filename = $this->source->getDataFilename();
		if(!$this->_alreadyRead) {
			$header = $this->source->getData(8);
			if($header != "!<arch>\n") {
				return PEAR::raiseError("File {$filename} is not a valid Ar file format (starts with $header)");
			}
			$this->_alreadyRead = true;
		}
		$name = $this->source->getData(16);
		$mtime = $this->source->getData(12);
		$uid = $this->source->getData(6);
		$gid = $this->source->getData(6);
		$mode = $this->source->getData(8);
		$size = $this->source->getData(10);
		$delim = $this->source->getData(2);
		if($delim === null) {
			return false;
		}
		if($size < 0) {
			return PEAR::raiseError("Files must be at least one byte long");
		}
		$this->_footer = ($size % 2 == 1);
		if(preg_match("/\#1\/(\d+)/",$name,$matches)) {
			$this->_header = 60 + $matches[1];
			$name = $this->source->getData($matches[1]);
			$size -= $matches[1];
		} else {
			$this->_header = 60;
			$name = preg_replace("/\s+$/","",$name);
		}
		$this->_nbBytesLeft = $size;
		if(empty($name) || empty($mtime) || empty($uid) || empty($gid) || empty($mode) ||
			empty($size)) {
			return PEAR::raiseError("An ar field is empty");
		}
		$this->_currentFilename = $this->getStandardURL($name);
		$this->_currentStat = array(2 => $mode,'mode' => $mode,4 => $uid,'uid' => $uid,
			5 => $gid,'gid' => $gid,7 => $size,'size' => $size,9 => $mtime,'mtime' => $mtime);
		return true;
	}
	function getData($length = -1) {
		if($length == -1) {
			$length = $this->_nbBytesLeft;
		} else {
			$length = min($length,$this->_nbBytesLeft);
		}
		if($length == 0) {
			return null;
		} else {
			$this->_nbBytesLeft -= $length;
			$data = $this->source->getData($length);
			if(PEAR::isError($data)) {
				return $data;
			}
			if(strlen($data) != $length) {
				return PEAR::raiseError('Unexpected end of Ar archive');
			}
			return $data;
		}
	}
	function skip($length = -1) {
		if($length == -1) {
			$length = $this->_nbBytesLeft;
		} else {
			$length = min($length,$this->_nbBytesLeft);
		}
		if($length == 0) {
			return 0;
		} else {
			$this->_nbBytesLeft -= $length;
			$skipped = $this->source->skip($length);
			if(PEAR::isError($skipped)) {
				return $skipped;
			}
			if($skipped != $length) {
				return PEAR::raiseError('Unexpected end of Ar archive');
			}
			return $skipped;
		}
	}
	function rewind($length = -1) {
		if($length == -1) {
			$length = $this->_currentStat[7] - $this->_nbBytesLeft;
		} else {
			$length = min($length,$this->_currentStat[7] - $this->_nbBytesLeft);
		}
		if($length == 0) {
			return 0;
		} else {
			$rewinded = $this->source->rewind($length);
			if(!PEAR::isError($rewinded)) {
				$this->_nbBytesLeft += $rewinded;
			}
			return $rewinded;
		}
	}
	function tell() {
		return $this->_currentStat[7] - $this->_nbBytesLeft;
	}
	function makeWriterRemoveFiles($pred) {
		require_once dirname(__file__)."/../Writer/Ar.php";
		$blocks = array();
		$seek = null;
		$gap = 0;
		if($this->_currentFilename !== null && $pred->isTrue($this)) {
			$seek = $this->_header + $this->_currentStat[7] + ($this->_footer?1:0);
			$blocks[] = $seek;
		}
		while(($error = $this->next()) === true) {
			$size = $this->_header + $this->_currentStat[7] + ($this->_footer?1:0);
			if($pred->isTrue($this)) {
				if($seek === null) {
					$seek = $size;
					$blocks[] = $size;
				} else
					if($gap > 0) {
						$blocks[] = $gap;
						$blocks[] = $size;
						$seek += $size;
					} else {
						$blocks[count($blocks) - 1] += $size;
						$seek += $size;
					}
					$gap = 0;
			} else {
				if($seek !== null) {
					$seek += $size;
					$gap += $size;
				}
			}
		}
		if($seek === null) {
			$seek = 0;
		} else {
			if($gap == 0) {
				array_pop($blocks);
			} else {
				$blocks[] = $gap;
			}
		}
		$writer = new File_Archive_Writer_Ar(null,$this->source->makeWriterRemoveBlocks
			($blocks,-$seek));
		$this->close();
		return $writer;
	}
	function makeWriterRemoveBlocks($blocks,$seek = 0) {
		if($this->_currentStat === null) {
			return PEAR::raiseError('No file selected');
		}
		$blockPos = $this->_currentStat[7] - $this->_nbBytesLeft + $seek;
		$this->rewind();
		$keep = false;
		$data = $this->getData($blockPos);
		foreach($blocks as $length) {
			if($keep) {
				$data .= $this->getData($length);
			} else {
				$this->skip($length);
			}
			$keep = !$keep;
		}
		if($keep) {
			$data .= $this->getData();
		}
		$filename = $this->_currentFilename;
		$stat = $this->_currentStat;
		$writer = $this->makeWriterRemove();
		if(PEAR::isError($writer)) {
			return $writer;
		}
		unset($stat[7]);
		$writer->newFile($filename,$stat);
		$writer->writeData($data);
		return $writer;
	}
	function makeAppendWriter() {
		require_once dirname(__file__)."/../Writer/Ar.php";
		while(($error = $this->next()) === true) {
		}
		if(PEAR::isError($error)) {
			$this->close();
			return $error;
		}
		$innerWriter = $this->source->makeWriterRemoveBlocks(array());
		if(PEAR::isError($innerWriter)) {
			return $innerWriter;
		}
		unset($this->source);
		$this->close();
		return new File_Archive_Writer_Ar(null,$innerWriter);
	}
}
?>
