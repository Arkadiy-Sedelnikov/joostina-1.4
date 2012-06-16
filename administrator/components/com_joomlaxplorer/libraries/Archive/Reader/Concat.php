<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_VALID_MOS') or die();
require_once dirname(__file__) . "/Relay.php";
class File_Archive_Reader_Concat extends File_Archive_Reader{
	var $source;
	var $filename;
	var $stat;
	var $mime;
	var $opened = false;
	var $filePos = 0;

	function File_Archive_Reader_Concat(&$source, $filename, $stat = array(), $mime = null){
		$this->source = &$source;
		$this->filename = $filename;
		$this->stat = $stat;
		$this->mime = $mime;
		$this->stat[7] = 0;
		while(($error = $source->next()) === true){
			$sourceStat = $source->getStat();
			if(isset($sourceStat[7])){
				$this->stat[7] += $sourceStat[7];
			} else{
				unset($this->stat[7]);
				break;
			}
		}
		if(isset($this->stat[7])){
			$this->stat['size'] = $this->stat[7];
		}
		if(PEAR::isError($error) || PEAR::isError($source->close())){
			die("Error in File_Archive_Reader_Concat constructor " . '(' . $error->getMessage() .
				'), cannot continue');
		}
	}

	function next(){
		if(!$this->opened){
			return $this->opened = $this->source->next();
		} else{
			return false;
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
		if($length == 0){
			return '';
		}
		$result = '';
		while($length == -1 || strlen($result) < $length){
			$sourceData = $this->source->getData($length == -1 ? -1 : $length - strlen($result));
			if(PEAR::isError($sourceData)){
				return $sourceData;
			}
			if($sourceData === null){
				$error = $this->source->next();
				if(PEAR::isError($error)){
					return $error;
				}
				if(!$error){
					break;
				}
			} else{
				$result .= $sourceData;
			}
		}
		$this->filePos += strlen($result);
		return $result == '' ? null : $result;
	}

	function skip($length = -1){
		$skipped = 0;
		while($skipped < $length){
			$sourceSkipped = $this->source->skip($length);
			if(PEAR::isError($sourceSkipped)){
				return $skipped;
			}
			$skipped += $sourceSkipped;
			$filePos += $sourceSkipped;
		}
		return $skipped;
	}

	function rewind($length = -1){
		return parent::rewind($length);
	}

	function tell(){
		return $this->filePos;
	}

	function close(){
		$this->opened = false;
		$this->filePos = 0;
		return $this->source->close();
	}

	function makeWriter($fileModif = true, $seek = 0){
		return $this->source->makeWriter($fileModif, $seek);
	}
}

?>
