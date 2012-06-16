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
require_once dirname(__file__) . "/../Writer/Files.php";
class File_Archive_Reader_Bzip2 extends File_Archive_Reader_Archive{
	var $nbRead = 0;
	var $bzfile = null;
	var $tmpName = null;
	var $filePos = 0;

	function close($innerClose = true){
		if($this->bzfile !== null)
			bzclose($this->bzfile);
		if($this->tmpName !== null)
			unlink($this->tmpName);
		$this->bzfile = null;
		$this->tmpName = null;
		$this->nbRead = 0;
		$this->filePos = 0;
		return parent::close($innerClose);
	}

	function next(){
		if(!parent::next()){
			return false;
		}
		$this->nbRead++;
		if($this->nbRead > 1){
			return false;
		}
		$dataFilename = $this->source->getDataFilename();
		if($dataFilename !== null){
			$this->tmpName = null;
			$this->bzfile = @bzopen($dataFilename, 'r');
			if($this->bzfile === false){
				return PEAR::raiseError("bzopen failed to open $dataFilename");
			}
		} else{
			$this->tmpName = tempnam(File_Archive::getOption('tmpDirectory'), 'far');
			$dest = new File_Archive_Writer_Files();
			$dest->newFile($this->tmpName);
			$this->source->sendData($dest);
			$dest->close();
			$this->bzfile = bzopen($this->tmpName, 'r');
		}
		return true;
	}

	function getFilename(){
		$name = $this->source->getFilename();
		$pos = strrpos($name, ".");
		if($pos === false || $pos === 0){
			return $name;
		} else{
			return substr($name, 0, $pos);
		}
	}

	function getData($length = -1){
		if($length == -1){
			$data = '';
			do{
				$newData = bzread($this->bzfile);
				$data .= $newData;
			} while($newData != '');
			$this->filePos += strlen($data);
		} else
			if($length == 0){
				return '';
			} else{
				$data = '';
				while(strlen($data) < $length){
					$newData = bzread($this->bzfile, $length - strlen($data));
					if($newData == ''){
						break;
					}
					$data .= $newData;
				}
				$this->filePos += strlen($data);
			}
		return $data == '' ? null : $data;
	}

	function rewind($length = -1){
		$before = $this->filePos;
		bzclose($this->bzfile);
		if($this->tmpName === null){
			$this->bzfile = bzopen($this->source->getDataFilename(), 'r');
		} else{
			$this->bzfile = bzopen($this->tmpName, 'r');
		}
		$this->filePos = 0;
		if($length != -1){
			$this->skip($before - $length);
		}
		return $before - $this->filePos;
	}

	function tell(){
		return $this->filePos;
	}

	function makeAppendWriter(){
		return PEAR::raiseError('Unable to append files to a bzip2 archive');
	}

	function makeWriterRemoveFiles($pred){
		return PEAR::raiseError('Unable to remove files from a bzip2 archive');
	}

	function makeWriterRemoveBlocks($blocks, $seek = 0){
		require_once dirname(__file__) . "/../Writer/Bzip2.php";
		if($this->nbRead == 0){
			return PEAR::raiseError('No file selected');
		}
		$tmp = tmpfile();
		$expectedPos = $this->filePos + $seek;
		$this->rewind();
		while($this->filePos < $expectedPos && ($data = $this->getData(min($expectedPos -
			$this->filePos, 8192))) !== null){
			fwrite($tmp, $data);
		}
		$keep = false;
		foreach($blocks as $length){
			if($keep){
				$expectedPos = $this->filePos + $length;
				while($this->filePos < $expectedPos && ($data = $this->getData(min($expectedPos -
					$this->filePos, 8192))) !== null){
					fwrite($tmp, $data);
				}
			} else{
				$this->skip($length);
			}
			$keep = !$keep;
		}
		if($keep){
			while(($data = $this->getData(8192)) !== null){
				fwrite($tmp, $data);
			}
		}
		fseek($tmp, 0);
		$this->source->rewind();
		$innerWriter = $this->source->makeWriterRemoveBlocks(array());
		unset($this->source);
		$writer = new File_Archive_Writer_Bzip2(null, $innerWriter);
		while(!feof($tmp)){
			$data = fread($tmp, 8192);
			$writer->writeData($data);
		}
		fclose($tmp);
		$this->close();
		return $writer;
	}
}

?>
