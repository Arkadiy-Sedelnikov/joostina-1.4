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
class File_Archive_Reader_Zip extends File_Archive_Reader_Archive {
	var $currentFilename = null;
	var $currentStat = null;
	var $header = null;
	var $offset = 0;
	var $data = null;
	var $files = array();
	var $seekToEnd = 0;
	var $centralDirectory = null;
	function close() {
		$this->currentFilename = null;
		$this->currentStat = null;
		$this->compLength = 0;
		$this->data = null;
		$this->seekToEnd = 0;
		$this->files = array();
		$this->centralDirectory = null;
		return parent::close();
	}
	function getFilename() {
		return $this->currentFilename;
	}
	function getStat() {
		return $this->currentStat;
	}
	function nextWithFolders() {
		if($this->seekToEnd > 0) {
			return false;
		}
		if($this->header !== null && $this->data === null) {
			$toSkip = $this->header['CLen'];
			$error = $this->source->skip($toSkip);
			if(PEAR::isError($error)) {
				return $error;
			}
		}
		$this->offset = 0;
		$this->data = null;
		$header = $this->source->getData(4);
		if(PEAR::isError($header)) {
			return $header;
		}
		if($header == "\x50\x4b\x03\x04") {
			$header = $this->source->getData(26);
			if(PEAR::isError($header)) {
				return $header;
			}
			$this->header = unpack("vVersion/vFlag/vMethod/vTime/vDate/VCRC/VCLen/VNLen/vFile/vExtra",
				$header);
			if($this->header['Method'] != 0 && $this->header['Method'] != 8 && $this->header['Method'] !=
				12) {
				return PEAR::raiseError("File_Archive_Reader_Zip doesn't ".
					"handle compression method {$this->header['Method']}");
			}
			if($this->header['Flag'] & 1) {
				return PEAR::raiseError("File_Archive_Reader_Zip doesn't ".
					"handle encrypted files");
			}
			if($this->header['Flag'] & 8) {
				if($this->centralDirectory === null) {
					$this->readCentralDirectory();
				}
				$centralDirEntry = $this->centralDirectory[count($this->files)];
				$this->header['CRC'] = $centralDirEntry['CRC'];
				$this->header['CLen'] = $centralDirEntry['CLen'];
				$this->header['NLen'] = $centralDirEntry['NLen'];
			}
			if($this->header['Flag'] & 32) {
				return PEAR::raiseError("File_Archive_Reader_Zip doesn't ".
					"handle compressed patched data");
			}
			if($this->header['Flag'] & 64) {
				return PEAR::raiseError("File_Archive_Reader_Zip doesn't ".
					"handle strong encrypted files");
			}
			$this->currentStat = array(7 => $this->header['NLen'],9 => mktime(($this->header['Time'] &
				0xF800) >> 11,($this->header['Time'] & 0x07E0) >> 5,($this->header['Time'] &
				0x001F) >> 1,($this->header['Date'] & 0x01E0) >> 5,($this->header['Date'] &
				0x001F),(($this->header['Date'] & 0xFE00) >> 9) + 1980));
			$this->currentStat['size'] = $this->currentStat[7];
			$this->currentStat['mtime'] = $this->currentStat[9];
			$this->currentFilename = $this->source->getData($this->header['File']);
			$error = $this->source->skip($this->header['Extra']);
			if(PEAR::isError($error)) {
				return $error;
			}
			$this->files[] = array('name' => $this->currentFilename,'stat' => $this->currentStat,
				'CRC' => $this->header['CRC'],'CLen' => $this->header['CLen']);
			return true;
		} else {
			$this->seekToEnd = 4;
			$this->currentFilename = null;
			return false;
		}
	}
	function next() {
		if(!parent::next()) {
			return false;
		}
		do {
			$result = $this->nextWithFolders();
			if($result !== true) {
				return $result;
			}
		} while(substr($this->getFilename(),-1) == '/');
		return true;
	}
	function getData($length = -1) {
		if($this->offset >= $this->currentStat[7]) {
			return null;
		}
		if($length >= 0) {
			$actualLength = min($length,$this->currentStat[7] - $this->offset);
		} else {
			$actualLength = $this->currentStat[7] - $this->offset;
		}
		$error = $this->uncompressData();
		if(PEAR::isError($error)) {
			return $error;
		}
		$result = substr($this->data,$this->offset,$actualLength);
		$this->offset += $actualLength;
		return $result;
	}
	function skip($length = -1) {
		$before = $this->offset;
		if($length == -1) {
			$this->offset = $this->currentStat[7];
		} else {
			$this->offset = min($this->offset + $length,$this->currentStat[7]);
		}
		return $this->offset - $before;
	}
	function rewind($length = -1) {
		$before = $this->offset;
		if($length == -1) {
			$this->offset = 0;
		} else {
			$this->offset = min(0,$this->offset - $length);
		}
		return $before - $this->offset;
	}
	function tell() {
		return $this->offset;
	}
	function uncompressData() {
		if($this->data !== null)
			return;
		$this->data = $this->source->getData($this->header['CLen']);
		if(PEAR::isError($this->data)) {
			return $this->data;
		}
		if($this->header['Method'] == 8) {
			$this->data = gzinflate($this->data);
		}
		if($this->header['Method'] == 12) {
			$this->data = bzdecompress($this->data);
		}
		if(crc32($this->data) != $this->header['CRC']) {
			return PEAR::raiseError("Zip archive: CRC fails on entry ".$this->currentFilename);
		}
	}
	function makeWriterRemoveFiles($pred) {
		require_once dirname(__file__)."/../Writer/Zip.php";
		$blocks = array();
		$seek = null;
		$gap = 0;
		if($this->currentFilename !== null && $pred->isTrue($this)) {
			$seek = 30 + $this->header['File'] + $this->header['Extra'] + $this->header['CLen'];
			$blocks[] = $seek;
			array_pop($this->files);
		}
		while(($error = $this->nextWithFolders()) === true) {
			$size = 30 + $this->header['File'] + $this->header['Extra'] + $this->header['CLen'];
			if(substr($this->getFilename(),-1) == '/' || $pred->isTrue($this)) {
				array_pop($this->files);
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
		if(PEAR::isError($error)) {
			return $error;
		}
		if($seek === null) {
			$seek = 4;
		} else {
			$seek += 4;
			if($gap == 0) {
				array_pop($blocks);
			} else {
				$blocks[] = $gap;
			}
		}
		$writer = new File_Archive_Writer_Zip(null,$this->source->makeWriterRemoveBlocks
			($blocks,-$seek));
		if(PEAR::isError($writer)) {
			return $writer;
		}
		foreach($this->files as $file) {
			$writer->alreadyWrittenFile($file['name'],$file['stat'],$file['CRC'],$file['CLen']);
		}
		$this->close();
		return $writer;
	}
	function makeWriterRemoveBlocks($blocks,$seek = 0) {
		if($this->currentFilename === null) {
			return PEAR::raiseError('No file selected');
		}
		$keep = false;
		$this->uncompressData();
		$newData = substr($this->data,0,$this->offset + $seek);
		$this->data = substr($this->data,$this->offset + $seek);
		foreach($blocks as $length) {
			if($keep) {
				$newData .= substr($this->data,0,$length);
			}
			$this->data = substr($this->data,$length);
			$keep = !$keep;
		}
		if($keep) {
			$newData .= $this->data;
		}
		$filename = $this->currentFilename;
		$stat = $this->currentStat;
		$writer = $this->makeWriterRemove();
		if(PEAR::isError($writer)) {
			return $writer;
		}
		unset($stat[7]);
		$stat[9] = $stat['mtime'] = time();
		$writer->newFile($filename,$stat);
		$writer->writeData($newData);
		return $writer;
	}
	function makeAppendWriter() {
		require_once dirname(__file__)."/../Writer/Zip.php";
		while(($error = $this->next()) === true) {
		}
		if(PEAR::isError($error)) {
			$this->close();
			return $error;
		}
		$writer = new File_Archive_Writer_Zip(null,$this->source->makeWriterRemoveBlocks
			(array(),-4));
		foreach($this->files as $file) {
			$writer->alreadyWrittenFile($file['name'],$file['stat'],$file['CRC'],$file['CLen']);
		}
		$this->close();
		return $writer;
	}
	function seekToEndOfCentralDirectory() {
		$nbSkipped = $this->source->skip();
		$nbSkipped -= $this->source->rewind(22) - 4;
		if($this->source->getData(4) == "\x50\x4b\x05\x06") {
			return $nbSkipped;
		}
		while($nbSkipped > 0) {
			$nbRewind = $this->source->rewind(min(100,$nbSkipped));
			while($nbRewind >= -4) {
				if($nbRewind-- && $this->source->getData(1) == "\x50" && $nbRewind-- && $this->source->getData
					(1) == "\x4b" && $nbRewind-- && $this->source->getData(1) == "\x05" && $nbRewind-- &&
					$this->source->getData(1) == "\x06") {
					return $nbSkipped - $nbRewind;
				}
			}
			$nbSkipped -= $nbRewind;
		}
		return PEAR::raiseError('End of central directory not found. The file is probably not a zip archive');
	}
	function readCentralDirectory() {
		$nbSkipped = $this->seekToEndOfCentralDirectory();
		if(PEAR::isError($nbSkipped)) {
			return $nbSkipped;
		}
		$this->source->skip(12);
		$offset = $this->source->getData(4);
		$nbSkipped += 16;
		if(PEAR::isError($offset)) {
			return $offset;
		}
		$offset = unpack("Vvalue",$offset);
		$offset = $offset['value'];
		$current = $this->source->tell();
		$nbSkipped -= $this->source->rewind($current - $offset);
		$this->centralDirectory = array();
		while($this->source->getData(4) == "\x50\x4b\x01\x02") {
			$this->source->skip(12);
			$header = $this->source->getData(16);
			$nbSkipped += 32;
			if(PEAR::isError($header)) {
				return $header;
			}
			$header = unpack('VCRC/VCLen/VNLen/vFileLength/vExtraLength',$header);
			$this->centralDirectory[] = array('CRC' => $header['CRC'],'CLen' => $header['CLen'],
				'NLen' => $header['NLen']);
			$nbSkipped += $this->source->skip(14 + $header['FileLength'] + $header['ExtraLength']);
		}
		$this->source->rewind($nbSkipped + 4);
	}
}
?>
