<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_JLINDEX') or die();
require_once dirname(__file__) . "/Archive.php";
class File_Archive_Writer_Tar extends File_Archive_Writer_Archive{
	var $buffer;
	var $useBuffer;
	var $filename = null;
	var $stats = null;

	function tarHeader($filename, $stat){
		$mode = isset($stat[2]) ? $stat[2] : 0x8000;
		$uid = isset($stat[4]) ? $stat[4] : 0;
		$gid = isset($stat[5]) ? $stat[5] : 0;
		$size = $stat[7];
		$time = isset($stat[9]) ? $stat[9] : time();
		$link = "";
		if($mode & 0x4000){
			$type = 5;
		} else
			if($mode & 0x8000){
				$type = 0;
			} else
				if($mode & 0xA000){
					$type = 1;
					$link = @readlink($current);
				} else{
					$type = 9;
				}
		$filePrefix = '';
		if(strlen($filename) > 255){
			return PEAR::raiseError("$filename is too long to be put in a tar archive");
		} else
			if(strlen($filename) > 100){
			}
		$blockbeg = pack("a100a8a8a8a12a12", $filename, decoct($mode), sprintf("%6s ",
				decoct($uid)), sprintf("%6s ", decoct($gid)), sprintf("%11s ", decoct($size)),
			sprintf("%11s ", decoct($time)));
		$blockend = pack("a1a100a6a2a32a32a8a8a155a12", $type, $link, "ustar", "00",
			"Unknown", "Unknown", "", "", $filePrefix, "");
		$checksum = 8 * ord(" ");
		for($i = 0; $i < 148; $i++){
			$checksum += ord($blockbeg{$i});
		}
		for($i = 0; $i < 356; $i++){
			$checksum += ord($blockend{$i});
		}
		$checksum = pack("a8", sprintf("%6s ", decoct($checksum)));
		return $blockbeg . $checksum . $blockend;
	}

	function tarFooter($size){
		if($size % 512 > 0){
			return pack("a" . (512 - $size % 512), "");
		} else{
			return "";
		}
	}

	function flush(){
		if($this->filename !== null){
			if($this->useBuffer){
				$this->stats[7] = strlen($this->buffer);
				$this->innerWriter->writeData($this->tarHeader($this->filename, $this->stats));
				$this->innerWriter->writeData($this->buffer);
			}
			$this->innerWriter->writeData($this->tarFooter($this->stats[7]));
		}
		$this->buffer = "";
	}

	function newFile($filename, $stats = array(), $mime = "application/octet-stream"){
		$this->flush();
		$this->useBuffer = !isset($stats[7]);
		$this->filename = $filename;
		$this->stats = $stats;
		if(!$this->useBuffer){
			return $this->innerWriter->writeData($this->tarHeader($filename, $stats));
		}
	}

	function close(){
		$this->flush();
		$this->innerWriter->writeData(pack("a1024", ""));
		parent::close();
	}

	function writeData($data){
		if($this->useBuffer){
			$this->buffer .= $data;
		} else{
			$this->innerWriter->writeData($data);
		}
	}

	function writeFile($filename){
		if($this->useBuffer){
			$this->buffer .= file_get_contents($filename);
		} else{
			$this->innerWriter->writeFile($filename);
		}
	}

	function getMime(){
		return "application/x-tar";
	}
}

require_once dirname(__file__) . "/../Predicate.php";
class File_Archive_Predicate_TARCompatible extends File_Archive_Predicate{
	function isTrue($source){
		return strlen($source->getFilename()) <= 255;
	}
}

?>
