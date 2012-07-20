<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_JLINDEX') or die();
require_once dirname(__file__) . "/../Reader.php";
class File_Archive_Reader_Cache extends File_Archive_Reader{
	var $tmpFile;
	var $files = array();
	var $pos = 0;
	var $fromSource = true;
	var $endOfSource = false;
	var $source;

	function File_Archive_Reader_Cache(&$source){
		$this->source = &$source;
		$this->tmpFile = tmpfile();
	}

	function _writeEndOfFile(){
		$bufferSize = File_Archive::getOption('blockSize');
		while(($data = $this->source->getData($bufferSize)) != null){
			fwrite($this->tmpFile, $data);
		}
	}

	function next(){
		if($this->fromSource && !empty($this->files)){
			$this->_writeEndOfFile();
		}
		if($this->pos + 1 < count($this->files) && !$this->fromSource){
			$this->pos++;
			fseek($this->tmpFile, $this->files[$this->pos]['pos'], SEEK_SET);
			return true;
		} else{
			$this->fromSource = true;
			if($this->endOfSource){
				return false;
			}
			$ret = $this->source->next();
			if($ret !== true){
				$this->endOfSource = true;
				$this->source->close();
				return $ret;
			}
			$this->endOfSource = false;
			fseek($this->tmpFile, 0, SEEK_END);
			$this->files[] = array('name' => $this->source->getFilename(), 'stat' => $this->source->getStat
			(), 'mime'                    => $this->source->getMime(), 'pos' => ftell($this->tmpFile));
			$this->pos = count($this->files) - 1;
			return true;
		}
	}

	function getFilename(){
		return $this->files[$this->pos]['name'];
	}

	function getStat(){
		return $this->files[$this->pos]['stat'];
	}

	function getMime(){
		return $this->files[$this->pos]['mime'];
	}

	function getDataFilename(){
		return null;
	}

	function getData($length = -1){
		if($this->fromSource){
			$data = $this->source->getData($length);
			if(PEAR::isError($data)){
				return $data;
			}
			fwrite($this->tmpFile, $data);
			return $data;
		} else{
			if($length == 0){
				return '';
			}
			if($length > 0 && $this->pos + 1 < count($this->files)){
				$maxSize = $this->files[$this->pos + 1]['pos'] - ftell($this->tmpFile);
				if($maxSize == 0){
					return null;
				}
				if($length > $maxSize){
					$length = $maxSize;
				}
				return fread($this->tmpFile, $length);
			} else{
				$contents = '';
				$blockSize = File_Archive::getOption('blockSize');
				while(!feof($this->tmpFile)){
					$contents .= fread($this->tmpFile, $blockSize);
				}
				return $contents == '' ? null : $contents;
			}
		}
	}

	function skip($length = -1){
		if($this->fromSource){
			return strlen($this->getData($length));
		} else{
			if($length >= 0 && $this->pos + 1 < count($this->files)){
				$maxSize = $this->files[$this->pos + 1]['pos'] - ftell($this->tmpFile);
				if($maxSize == 0){
					return null;
				}
				if($length > $maxSize){
					$length = $maxSize;
				}
				fseek($this->tmpFile, $length, SEEK_CUR);
				return $length;
			} else{
				$before = ftell($this->tmpFile);
				fseek($this->tmpFile, 0, SEEK_SET);
				$after = fteel($this->tmpFile);
				return $after - $before;
			}
		}
	}

	function rewind($length = -1){
		if($this->fromSource){
			$this->_writeEndOfFile();
			$this->fromSource = false;
		}
		$before = ftell($this->tmpFile);
		$pos = $this->files[$this->pos]['pos'];
		fseek($this->tmpFile, $pos, SEEK_SET);
		return $pos - $before;
	}

	function tell(){
		return ftell($this->tmpFile) - $this->files[$this->pos]['pos'];
	}

	function close(){
		$this->fromSource = false;
		$this->pos = 0;
		fseek($this->tmpFile, 0, SEEK_SET);
	}

	function _closeAndReset(){
		$this->close();
		fclose($this->tmpFile);
		$this->tmpFile = tmpfile();
		$this->endOfSource = false;
		$this->files = array();
		$this->source->close();
	}

	function makeAppendWriter(){
		$writer = $this->source->makeAppendWriter();
		if(!PEAR::isError($writer)){
			$this->_closeAndReset();
		}
		return $writer;
	}

	function makeWriterRemoveFiles($pred){
		$writer = $this->source->makeWriterRemoveFiles($pred);
		if(!PEAR::isError($writer)){
			$this->_closeAndReset();
		}
		return $writer;
	}

	function makeWriterRemoveBlocks($blocks, $seek = 0){
		$writer = $this->source->makeWriterRemoveBlocks($blocks, $seek);
		if(!PEAR::isError($writer)){
			$this->_closeAndReset();
		}
		return $writer;
	}
}

?>
