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
require_once dirname(__file__)."/../Writer/Files.php";
class File_Archive_Reader_Gzip extends File_Archive_Reader_Archive {
	var $nbRead = 0;
	var $filePos = 0;
	var $gzfile = null;
	var $tmpName = null;
	function close($innerClose = true) {
		if($this->gzfile !== null) {
			gzclose($this->gzfile);
		}
		if($this->tmpName !== null) {
			unlink($this->tmpName);
		}
		$this->nbRead = 0;
		$this->filePos = 0;
		$this->gzfile = null;
		$this->tmpName = null;
		return parent::close($innerClose);
	}
	function next() {
		if(!parent::next()) {
			return false;
		}
		$this->nbRead++;
		$this->filePos = 0;
		if($this->nbRead > 1) {
			return false;
		}
		$dataFilename = $this->source->getDataFilename();
		if($dataFilename !== null) {
			$this->tmpName = null;
			$this->gzfile = gzopen($dataFilename,'r');
		} else {
			$this->tmpName = tempnam(File_Archive::getOption('tmpDirectory'),'far');
			$dest = new File_Archive_Writer_Files();
			$dest->newFile($this->tmpName);
			$this->source->sendData($dest);
			$dest->close();
			$this->gzfile = gzopen($this->tmpName,'r');
		}
		return true;
	}
	function getFilename() {
		$name = $this->source->getFilename();
		$slashPos = strrpos($name,'/');
		if($slashPos !== false) {
			$name = substr($name,$slashPos + 1);
		}
		$dotPos = strrpos($name,'.');
		if($dotPos !== false && $dotPos > 0) {
			$name = substr($name,0,$dotPos);
		}
		return $name;
	}
	function getData($length = -1) {
		if($length == -1) {
			$data = '';
			do {
				$newData = gzread($this->gzfile,8192);
				$data .= $newData;
			} while($newData != '');
		} else
			if($length == 0) {
				return '';
			} else {
				$data = gzread($this->gzfile,$length);
			}
			$this->filePos += strlen($data);
		return $data == ''?null:$data;
	}
	function skip($length = -1) {
		if($length == -1) {
			do {
				$tmp = gzread($this->gzfile,8192);
				$this->filePos += strlen($tmp);
			} while($tmp != '');
		} else {
			if(@gzseek($this->gzfile,$this->filePos + $length) === -1) {
				return parent::skip($length);
			} else {
				$this->filePos += $length;
				return $length;
			}
		}
	}
	function rewind($length = -1) {
		if($length == -1) {
			if(@gzseek($this->gzfile,0) === -1) {
				return parent::rewind($length);
			} else {
				$tmp = $this->filePos;
				$this->filePos = 0;
				return $tmp;
			}
		} else {
			$length = min($length,$this->filePos);
			if(@gzseek($this->gzfile,$this->filePos - $length) === -1) {
				return parent::rewind($length);
			} else {
				$this->filePos -= $length;
				return $length;
			}
		}
	}
	function tell() {
		return $this->filePos;
	}
	function makeAppendWriter() {
		return PEAR::raiseError('Unable to append files to a gzip archive');
	}
	function makeWriterRemoveFiles($pred) {
		return PEAR::raiseError('Unable to remove files from a gzip archive');
	}
	function makeWriterRemoveBlocks($blocks,$seek = 0) {
		require_once dirname(__file__)."/../Writer/Gzip.php";
		if($this->nbRead == 0) {
			return PEAR::raiseError('No file selected');
		}
		$tmp = tmpfile();
		$expectedPos = $this->filePos + $seek;
		$this->rewind();
		while($this->filePos < $expectedPos && ($data = $this->getData(min($expectedPos -
			$this->filePos,8192))) !== null) {
			fwrite($tmp,$data);
		}
		$keep = false;
		foreach($blocks as $length) {
			if($keep) {
				$expectedPos = $this->filePos + $length;
				while($this->filePos < $expectedPos && ($data = $this->getData(min($expectedPos -
					$this->filePos,8192))) !== null) {
					fwrite($tmp,$data);
				}
			} else {
				$this->skip($length);
			}
			$keep = !$keep;
		}
		if($keep) {
			while(($data = $this->getData(8192)) !== null) {
				fwrite($tmp,$data);
			}
		}
		fseek($tmp,0);
		$this->source->rewind();
		$innerWriter = $this->source->makeWriterRemoveBlocks(array());
		unset($this->source);
		$writer = new File_Archive_Writer_Gzip(null,$innerWriter);
		while(!feof($tmp)) {
			$data = fread($tmp,8192);
			$writer->writeData($data);
		}
		fclose($tmp);
		$this->close();
		return $writer;
	}
}

?>
