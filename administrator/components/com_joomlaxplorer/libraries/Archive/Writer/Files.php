<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_VALID_MOS') or die();
require_once dirname(__file__) . "/../Writer.php";
class File_Archive_Writer_Files extends File_Archive_Writer{
	var $handle = null;
	var $basePath;
	var $stat = array();
	var $filename;

	function File_Archive_Writer_Files($base = ''){
		if($base === null || $base == ''){
			$this->basePath = '';
		} else{
			if(substr($base, -1) == '/'){
				$this->basePath = $base;
			} else{
				$this->basePath = $base . '/';
			}
		}
	}

	function getFilename($filename){
		return $this->basePath . $filename;
	}

	function mkdirr($pathname){
		if(is_dir($pathname) || empty($pathname)){
			return;
		}
		if(is_file($pathname)){
			return PEAR::raiseError("File $pathname exists, unable to create directory");
		}
		$next_pathname = substr($pathname, 0, strrpos($pathname, "/"));
		$error = $this->mkdirr($next_pathname);
		if(PEAR::isError($error)){
			return $error;
		}
		if(!@mkdir($pathname)){
			return PEAR::raiseError("Unable to create directory $pathname");
		}
	}

	function openFile($filename, $pos = 0){
		$this->close();
		$this->handle = fopen($filename, 'r+');
		$this->stat = array();
		$this->filename = $filename;
		if(!is_resource($this->handle)){
			return PEAR::raiseError("Unable to open file $filename");
		}
		if($pos > 0){
			if(fseek($this->handle, $pos) == -1){
				fread($this->handle, $pos);
			}
		}
	}

	function openFileRemoveBlock($filename, $pos, $blocks){
		$error = $this->openFile($filename, $pos);
		if(PEAR::isError($error)){
			return $error;
		}
		if(!empty($blocks)){
			$read = fopen($filename, 'r');
			if($pos > 0){
				if(fseek($this->handle, $pos) == -1){
					fread($this->handle, $pos);
				}
			}
			$keep = false;
			$data = '';
			foreach($blocks as $length){
				if($keep){
					while($length > 0 && ($data = fread($read, min($length, 8192))) != ''){
						$length -= strlen($data);
						fwrite($this->handle, $data);
					}
				} else{
					fseek($read, $length, SEEK_CUR);
				}
				$keep = !$keep;
			}
			if($keep){
				while(!feof($this->handle)){
					fwrite($this->handle, fread($read, 8196));
				}
			}
			fclose($read);
		}
		ftruncate($this->handle, ftell($this->handle));
	}

	function newFile($filename, $stat = array(), $mime = "application/octet-stream"){
		$this->close();
		$this->stat = $stat;
		$this->filename = $this->getFilename($filename);
		$pos = strrpos($this->filename, "/");
		if($pos !== false){
			$error = $this->mkdirr(substr($this->filename, 0, $pos));
			if(PEAR::isError($error)){
				return $error;
			}
		}
		$this->handle = @fopen($this->filename, "w");
		if(!is_resource($this->handle)){
			return PEAR::raiseError("Unable to write to file $filename");
		}
	}

	function writeData($data){
		fwrite($this->handle, $data);
	}

	function newFromTempFile($tmpfile, $filename, $stat = array(), $mime =
	"application/octet-stream"){
		$this->filename = $filename;
		$complete = $this->getFilename($filename);
		$pos = strrpos($complete, "/");
		if($pos !== false){
			$error = $this->mkdirr(substr($complete, 0, $pos));
			if(PEAR::isError($error)){
				return $error;
			}
		}
		if((file_exists($complete) && !@unlink($complete)) || !@rename($tmpfile, $complete)){
			return parent::newFromTempFile($tmpfile, $filename, $stat, $mime);
		}
	}

	function close(){
		if($this->handle !== null){
			fclose($this->handle);
			$this->handle = null;
			if(isset($this->stat[9])){
				if(isset($this->stat[8])){
					touch($this->filename, $this->stat[9], $this->stat[8]);
				} else{
					touch($this->filename, $this->stat[9]);
				}
			} else
				if(isset($this->stat[8])){
					touch($this->filename, time(), $this->stat[8]);
				}
			if(isset($this->stat[2])){
				chmod($this->filename, $this->stat[2]);
			}
			if(isset($this->stat[5])){
				chgrp($this->filename, $this->stat[5]);
			}
			if(isset($this->stat[4])){
				chown($this->filename, $this->stat[4]);
			}
		}
	}
}

?>
