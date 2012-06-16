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
class File_Archive_Reader_Tar extends File_Archive_Reader_Archive{
	var $currentFilename = null;
	var $currentStat = null;
	var $leftLength = 0;
	var $footerLength = 0;
	var $seekToEnd = null;

	function skip($length = -1){
		if($length == -1){
			$length = $this->leftLength;
		} else{
			$length = min($this->leftLength, $length);
		}
		$skipped = $this->source->skip($length);
		if(!PEAR::isError($skipped)){
			$this->leftLength -= $skipped;
		}
		return $skipped;
	}

	function rewind($length = -1){
		if($length == -1){
			$length = $this->currentStat[7] - $this->leftLength;
		} else{
			$length = min($length, $this->currentStat[7] - $this->leftLength);
		}
		$rewinded = $this->source->rewind($length);
		if(!PEAR::isError($rewinded)){
			$this->leftLength += $rewinded;
		}
		return $rewinded;
	}

	function tell(){
		return $this->currentStat[7] - $this->leftLength;
	}

	function close(){
		$this->leftLength = 0;
		$this->currentFilename = null;
		$this->currentStat = null;
		$this->seekToEnd = null;
		return parent::close();
	}

	function getFilename(){
		return $this->currentFilename;
	}

	function getStat(){
		return $this->currentStat;
	}

	function next(){
		$error = parent::next();
		if($error !== true){
			return $error;
		}
		if($this->seekToEnd !== null){
			return false;
		}
		do{
			$error = $this->source->skip($this->leftLength + $this->footerLength);
			if(PEAR::isError($error)){
				return $error;
			}
			$rawHeader = $this->source->getData(512);
			if(PEAR::isError($rawHeader)){
				return $rawHeader;
			}
			if(strlen($rawHeader) < 512 || $rawHeader == pack("a512", "")){
				$this->seekToEnd = strlen($rawHeader);
				$this->currentFilename = null;
				return false;
			}
			$header = unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/" .
				"a8checksum/a1type/a100linkname/a6magic/a2version/" .
				"a32uname/a32gname/a8devmajor/a8devminor/a155prefix", $rawHeader);
			$this->currentStat = array(2 => octdec($header['mode']), 4 => octdec($header['uid']),
									   5 => octdec($header['gid']), 7 => octdec($header['size']), 9 => octdec($header['mtime']));
			$this->currentStat['mode'] = $this->currentStat[2];
			$this->currentStat['uid'] = $this->currentStat[4];
			$this->currentStat['gid'] = $this->currentStat[5];
			$this->currentStat['size'] = $this->currentStat[7];
			$this->currentStat['mtime'] = $this->currentStat[9];
			if($header['magic'] == 'ustar'){
				$this->currentFilename = $this->getStandardURL($header['prefix'] . $header['filename']);
			} else{
				$this->currentFilename = $this->getStandardURL($header['filename']);
			}
			$this->leftLength = $this->currentStat[7];
			if($this->leftLength % 512 == 0){
				$this->footerLength = 0;
			} else{
				$this->footerLength = 512 - $this->leftLength % 512;
			}
			$checksum = 8 * ord(" ");
			for($i = 0; $i < 148; $i++){
				$checksum += ord($rawHeader{$i});
			}
			for($i = 156; $i < 512; $i++){
				$checksum += ord($rawHeader{$i});
			}
			if(octdec($header['checksum']) != $checksum){
				die('Checksum error on entry ' . $this->currentFilename);
			}
		} while($header['type'] != 0);
		return true;
	}

	function getData($length = -1){
		if($length == -1){
			$actualLength = $this->leftLength;
		} else{
			$actualLength = min($this->leftLength, $length);
		}
		if($this->leftLength == 0){
			return null;
		} else{
			$data = $this->source->getData($actualLength);
			if(strlen($data) != $actualLength){
				return PEAR::raiseError('Unexpected end of tar archive');
			}
			$this->leftLength -= $actualLength;
			return $data;
		}
	}

	function makeWriterRemoveFiles($pred){
		require_once dirname(__file__) . "/../Writer/Tar.php";
		$blocks = array();
		$seek = null;
		$gap = 0;
		if($this->currentFilename !== null && $pred->isTrue($this)){
			$seek = 512 + $this->currentStat[7] + $this->footerLength;
			$blocks[] = $seek;
		}
		while(($error = $this->next()) === true){
			$size = 512 + $this->currentStat[7] + $this->footerLength;
			if($pred->isTrue($this)){
				if($seek === null){
					$seek = $size;
					$blocks[] = $size;
				} else
					if($gap > 0){
						$blocks[] = $gap;
						$blocks[] = $size;
						$seek += $size;
					} else{
						$blocks[count($blocks) - 1] += $size;
						$seek += $size;
					}
				$gap = 0;
			} else{
				if($seek !== null){
					$seek += $size;
					$gap += $size;
				}
			}
		}
		if($seek === null){
			$seek = $this->seekToEnd;
		} else{
			$seek += $this->seekToEnd;
			if($gap == 0){
				array_pop($blocks);
			} else{
				$blocks[] = $gap;
			}
		}
		$writer = new File_Archive_Writer_Tar(null, $this->source->makeWriterRemoveBlocks
		($blocks, -$seek));
		$this->close();
		return $writer;
	}

	function makeWriterRemoveBlocks($blocks, $seek = 0){
		if($this->seekToEnd !== null || $this->currentStat === null){
			return PEAR::raiseError('No file selected');
		}
		$blockPos = $this->currentStat[7] - $this->leftLength + $seek;
		$this->rewind();
		$keep = false;
		$data = $this->getData($blockPos);
		foreach($blocks as $length){
			if($keep){
				$data .= $this->getData($length);
			} else{
				$this->skip($length);
			}
			$keep = !$keep;
		}
		if($keep){
			$data .= $this->getData();
		}
		$filename = $this->currentFilename;
		$stat = $this->currentStat;
		$writer = $this->makeWriterRemove();
		if(PEAR::isError($writer)){
			return $writer;
		}
		unset($stat[7]);
		$stat[9] = $stat['mtime'] = time();
		$writer->newFile($filename, $stat);
		$writer->writeData($data);
		return $writer;
	}

	function makeAppendWriter(){
		require_once dirname(__file__) . "/../Writer/Tar.php";
		while(($error = $this->next()) === true){
		}
		if(PEAR::isError($error)){
			$this->close();
			return $error;
		}
		$innerWriter = $this->source->makeWriterRemoveBlocks(array(), -$this->seekToEnd);
		if(PEAR::isError($innerWriter)){
			return $innerWriter;
		}
		$this->close();
		return new File_Archive_Writer_Tar(null, $innerWriter);
	}
}

?>
