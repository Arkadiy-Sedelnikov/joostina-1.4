<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

defined('_VALID_MOS') or die();
require_once dirname(__file__)."/MemoryArchive.php";
class File_Archive_Writer_Zip extends File_Archive_Writer_MemoryArchive {
	var $compressionLevel;
	var $offset = 0;
	var $comment = "";
	var $central = "";
	function File_Archive_Writer_Zip($filename,&$innerWriter,$stat = array(),$autoClose = true) {
		global $_File_Archive_Options;
		parent::File_Archive_Writer_MemoryArchive($filename,$innerWriter,$stat,$autoClose);
		$this->compressionLevel = File_Archive::getOption('zipCompressionLevel',9);
	}
	function setCompressionLevel($compressionLevel) {
		$this->compressionLevel = $compressionLevel;
	}
	function setComment($comment) {
		$this->comment = $comment;
	}
	function getMTime($time) {
		$mtime = ($time !== null?getdate($time):getdate());
		$mtime = preg_replace("/(..){1}(..){1}(..){1}(..){1}/","\\x\\4\\x\\3\\x\\2\\x\\1",
			dechex(($mtime['year'] - 1980 << 25) | ($mtime['mon'] << 21) | ($mtime['mday'] <<
			16) | ($mtime['hours'] << 11) | ($mtime['minutes'] << 5) | ($mtime['seconds'] >>
			1)));
		eval('$mtime = "'.$mtime.'";');
		return $mtime;
	}
	function alreadyWrittenFile($filename,$stat,$crc32,$complength) {
		$filename = preg_replace("/^(\.{1,2}(\/|\\\))+/","",$filename);
		$mtime = $this->getMTime(isset($stat[9])?$stat[9]:null);
		$normlength = $stat[7];
		$this->nbFiles++;
		$this->central .= "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00".$mtime.
			pack("VVVvvvvvVV",$crc32,$complength,$normlength,strlen($filename),0x00,0x00,
			0x00,0x00,0x0000,$this->offset).$filename;
		$this->offset += 30 + strlen($filename) + $complength;
	}
	function appendFileData($filename,$stat,$data) {
		$crc32 = crc32($data);
		$normlength = strlen($data);
		$data = gzcompress($data,$this->compressionLevel);
		$data = substr($data,2,strlen($data) - 6);
		return $this->appendCompressedData($filename,$stat,$data,$crc32,$normlength);
	}
	function appendCompressedData($filename,$stat,$data,$crc32,$normlength) {
		$filename = preg_replace("/^(\.{1,2}(\/|\\\))+/","",$filename);
		$mtime = $this->getMTime(isset($stat[9])?$stat[9]:null);
		$complength = strlen($data);
		$zipData = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00".$mtime.pack("VVVvv",$crc32,
			$complength,$normlength,strlen($filename),0x00).$filename.$data;
		$error = $this->innerWriter->writeData($zipData);
		if(PEAR::isError($error)) {
			return $error;
		}
		$this->central .= "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00".$mtime.
			pack("VVVvvvvvVV",$crc32,$complength,$normlength,strlen($filename),0x00,0x00,
			0x00,0x00,0x0000,$this->offset).$filename;
		$this->offset += strlen($zipData);
	}
	function appendFile($filename,$dataFilename) {
		$cache = File_Archive::getOption('cache',null);
		if($cache !== null && $this->compressionLevel > 0) {
			$id = realpath($dataFilename);
			$id = urlencode($id);
			$id = str_replace('_','%5F',$id);
			$group = 'FileArchiveZip'.$this->compressionLevel;
			$mtime = filemtime($dataFilename);
			if(($data = $cache->get($id,$group)) !== false) {
				$info = unpack('Vmtime/Vcrc/Vnlength',substr($data,0,12));
				$data = substr($data,12);
			}
			if($data === false || $info['mtime'] != $mtime) {
				$data = file_get_contents($dataFilename);
				$info = array('crc' => crc32($data),'nlength' => strlen($data),'mtime' => $mtime);
				$data = gzcompress($data,$this->compressionLevel);
				$data = substr($data,2,strlen($data) - 6);
				$data = pack('VVV',$info['mtime'],$info['crc'],$info['nlength']).$data;
				$cache->save($data,$id,$group);
			}
			return $this->appendCompressedData($filename,stat($dataFilename),$data,$info['crc'],
				$info['nlength']);
		}
		return parent::appendFile($filename,$dataFilename);
	}
	function sendFooter() {
		return $this->innerWriter->writeData($this->central."\x50\x4b\x05\x06\x00\x00\x00\x00".
			pack("vvVVv",$this->nbFiles,$this->nbFiles,strlen($this->central),$this->offset,
			strlen($this->comment)).$this->comment);
	}
	function getMime() {
		return "application/zip";
	}
}

?>
