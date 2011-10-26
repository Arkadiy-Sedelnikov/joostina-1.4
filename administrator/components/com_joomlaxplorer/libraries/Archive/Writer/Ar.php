<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

defined('_VALID_MOS') or die();
require_once dirname(__file__)."/../Archive.php";
class File_Archive_Writer_Ar extends File_Archive_Writer_Archive {
	var $_buffer = "";
	var $_currentFilename = null;
	var $_useBuffer;
	var $_currentStat = array();
	var $_atStart = true;
	function arHeader($filename,$stat) {
		$mode = isset($stat[2])?$stat[2]:0x8000;
		$uid = isset($stat[4])?$stat[4]:0;
		$gid = isset($stat[5])?$stat[5]:0;
		$size = $stat[7];
		$time = isset($stat[9])?$stat[9]:time();
		$struct = "";
		$currentSize = $size;
		if(strlen($filename) > 16) {
			$currentSize += strlen($filename);
			$struct .= sprintf("#1/%-13d",strlen($filename));
			$struct .= sprintf("%-12d%-6d%-6d%-8s%-10d",$time,$uid,$gid,$mode,$currentSize);
			$struct .= "`\n".$filename;
		} else {
			$struct .= sprintf("%-16s",$filename);
			$struct .= sprintf("%-12d%-6d%-6d%-8s%-10d`\n",$time,$uid,$gid,$mode,$size);
		}
		return $struct;
	}
	function arFooter($filename,$size) {
		$size = (strlen($filename) > 16)?$size + strlen($filename):$size;
		return ($size % 2 == 1)?"\n":"";
	}
	function flush() {
		if($this->_atStart) {
			$this->innerWriter->writeData("!<arch>\n");
			$this->_atStart = false;
		}
		if($this->_currentFilename !== null) {
			$this->_currentStat[7] = strlen($this->_buffer);
			if($this->_useBuffer) {
				$this->innerWriter->writeData($this->arHeader($this->_currentFilename,$this->_currentStat));
				$this->innerWriter->writeData($this->_buffer);
			}
			$this->innerWriter->writeData($this->arFooter($this->_currentFilename,$this->_currentStat[7]));
		}
		$this->_buffer = "";
	}
	function newFile($filename,$stat = array(),$mime = "application/octet-stream") {
		$this->flush();
		$this->_useBuffer = !isset($stats[7]);
		$this->_currentFilename = basename($filename);
		$this->_currentStat = $stat;
		if(!$this->_useBuffer) {
			return $this->innerWriter->writeData($this->arHeader($filename,$stat));
		}
	}
	function close() {
		$this->flush();
		parent::close();
	}
	function writeData($data) {
		if($this->_useBuffer) {
			$this->_buffer .= $data;
		} else {
			$this->innerWriter->writeData($data);
		}
	}
	function writeFile($filename) {
		if($this->_useBuffer) {
			$this->_buffer .= file_get_contents($filename);
		} else {
			$this->innerWriter->writeFile($filename);
		}
	}
}
