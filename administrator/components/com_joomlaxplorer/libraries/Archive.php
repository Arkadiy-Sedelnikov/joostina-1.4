<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 * @category   File Formats
 * @package    File_Archive
 * @author     Vincent Lascaux <vincentlascaux@php.net>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL
 * @version    CVS: $Id:Archive.php 13 2007-05-13 07:10:43Z soeren $
 * @link       http://pear.php.net/package/File_Archive
 **/
defined('_VALID_MOS') or die();
require_once (dirname(__file__) . "/PEAR.php");
function File_Archive_cleanCache($file, $group){
	$file = explode('_', $file);
	if(count($file) != 3){
		return false;
	}
	$name = $file[2];
	$name = urldecode($name);
	$group = $file[1];
	return substr($group, 0, 11) == 'FileArchive' && !file_exists($name);
}

class File_Archive{
	function &_option($name){
		static $container = array('zipCompressionLevel' => 9, 'gzCompressionLevel' => 9,
								  'tmpDirectory'        => '.', 'cache' => null, 'appendRemoveDuplicates' => false,
								  'blockSize'           => 65536, 'cacheCondition' => false);
		return $container[$name];
	}

	function setOption($name, $value){
		$option = &File_Archive::_option($name);
		$option = $value;
		if($name == 'cache' && $value !== null){
			$value->_fileNameProtection = false;
		}
	}

	function getOption($name){
		return File_Archive::_option($name);
	}

	function readSource(&$source, $URL, $symbolic = null, $uncompression = 0, $directoryDepth =
	-1){
		return File_Archive::_readSource($source, $URL, $reachable, $baseDir, $symbolic, $uncompression,
			$directoryDepth);
	}

	function _readSource(&$toConvert, $URL, &$reachable, &$baseDir, $symbolic = null, $uncompression =
	0, $directoryDepth = -1){
		$source = &File_Archive::_convertToReader($toConvert);
		if(PEAR::isError($source)){
			return $source;
		}
		if(is_array($URL)){
			$converted = array();
			foreach($URL as $key => $foo){
				$converted[] = &File_Archive::_convertToReader($URL[$key]);
			}
			return File_Archive::readMulti($converted);
		}
		if($directoryDepth >= 0){
			$uncompressionLevel = min($uncompression, $directoryDepth);
		} else{
			$uncompressionLevel = $uncompression;
		}
		require_once dirname(__file__) . '/Archive/Reader.php';
		$std = File_Archive_Reader::getStandardURL($URL);
		$slashPos = strrpos($std, '/');
		if($symbolic === null){
			if($slashPos === false){
				$realSymbolic = $std;
			} else{
				$realSymbolic = substr($std, $slashPos + 1);
			}
		} else{
			$realSymbolic = $symbolic;
		}
		if($slashPos !== false){
			$baseFile = substr($std, 0, $slashPos + 1);
			$lastFile = substr($std, $slashPos + 1);
		} else{
			$baseFile = '';
			$lastFile = $std;
		}
		if(strpos($lastFile, '*') !== false || strpos($lastFile, '?') !== false){
			$regexp = str_replace(array('\*', '\?'), array('[^/]*', '[^/]'), preg_quote($lastFile));
			$result = File_Archive::_readSource($source, $baseFile, $reachable, $baseDir, null,
				0, -1);
			return File_Archive::filter(File_Archive::predEreg('^' . $regexp . '$'), $result);
		}
		if((empty($URL) || is_dir($URL)) && $source === null){
			require_once dirname(__file__) . "/Archive/Reader/Directory.php";
			require_once dirname(__file__) . "/Archive/Reader/ChangeName.php";
			if($uncompressionLevel != 0){
				require_once dirname(__file__) . "/Archive/Reader/Uncompress.php";
				$result = new File_Archive_Reader_Uncompress(new File_Archive_Reader_Directory($std,
					'', $directoryDepth), $uncompressionLevel);
			} else{
				$result = new File_Archive_Reader_Directory($std, '', $directoryDepth);
			}
			if($directoryDepth >= 0){
				require_once dirname(__file__) . '/Archive/Reader/Filter.php';
				require_once dirname(__file__) . '/Archive/Predicate/MaxDepth.php';
				$tmp = &File_Archive::filter(new File_Archive_Predicate_MaxDepth($directoryDepth),
					$result);
				unset($result);
				$result = &$tmp;
			}
			if(!empty($realSymbolic)){
				if($symbolic === null){
					$realSymbolic = '';
				}
				$tmp = new File_Archive_Reader_AddBaseName($realSymbolic, $result);
				unset($result);
				$result = &$tmp;
			}
		} else
			if(is_file($URL) && substr($URL, -1) != '/' && $source === null){
				require_once dirname(__file__) . "/Archive/Reader/File.php";
				$result = new File_Archive_Reader_File($URL, $realSymbolic);
			} else{
				require_once dirname(__file__) . "/Archive/Reader/File.php";
				$realPath = $std;
				$pos = 0;
				do{
					if($pos + 1 < strlen($realPath)){
						$pos = strpos($realPath, '/', $pos + 1);
					} else{
						$pos = false;
					}
					if($pos === false){
						$pos = strlen($realPath);
					}
					$file = substr($realPath, 0, $pos);
					$baseDir = substr($realPath, $pos + 1);
					$dotPos = strrpos($file, '.');
					$extension = '';
					if($dotPos !== false){
						$extension = substr($file, $dotPos + 1);
					}
				} while($pos < strlen($realPath) && (!File_Archive::isKnownExtension($extension) ||
					(is_dir($file) && $source == null)));
				$reachable = $file;
				if($source === null){
					$result = new File_Archive_Reader_File($file);
				} else{
					require_once dirname(__file__) . "/Archive/Reader/Select.php";
					$result = new File_Archive_Reader_Select($file, $source);
				}
				require_once dirname(__file__) . "/Archive/Reader/Uncompress.php";
				$tmp = new File_Archive_Reader_Uncompress($result, $uncompressionLevel);
				unset($result);
				$result = $tmp;
				$isDir = $result->setBaseDir($std);
				if(PEAR::isError($isDir)){
					return $isDir;
				}
				if($isDir && $symbolic == null){
					$realSymbolic = '';
				}
				if($directoryDepth >= 0){
					require_once dirname(__file__) . "/Archive/Predicate/MaxDepth.php";
					$tmp = new File_Archive_Reader_Filter(new File_Archive_Predicate($directoryDepth +
						substr_count(substr($std, $pos + 1), '/')), $result);
					unset($result);
					$result = &$tmp;
				}
				if($std != $realSymbolic){
					require_once dirname(__file__) . "/Archive/Reader/ChangeName.php";
					$tmp = new File_Archive_Reader_ChangeBaseName($std, $realSymbolic, $result);
					unset($result);
					$result = &$tmp;
				}
			}
		$cacheCondition = File_Archive::getOption('cacheCondition');
		if($cacheCondition !== false && preg_match($cacheCondition, $URL)){
			$tmp = &File_Archive::cache($result);
			unset($result);
			$result = &$tmp;
		}
		return $result;
	}

	function read($URL, $symbolic = null, $uncompression = 0, $directoryDepth = -1){
		$source = null;
		return File_Archive::readSource($source, $URL, $symbolic, $uncompression, $directoryDepth);
	}

	function readUploadedFile($name){
		if(!isset($_FILES[$name])){
			return PEAR::raiseError("File $name has not been uploaded");
		}
		switch($_FILES[$name]['error']){
			case 0:
				break;
			case 1:
				return PEAR::raiseError("The upload size limit didn't allow to upload file " . $_FILES[$name]['name']);
			case 2:
				return PEAR::raiseError("The form size limit didn't allow to upload file " . $_FILES[$name]['name']);
			case 3:
				return PEAR::raiseError("The file was not entirely uploaded");
			case 4:
				return PEAR::raiseError("The uploaded file is empty");
			default:
				return PEAR::raiseError("Unknown error " . $_FILES[$name]['error'] .
					" in file upload. " . "Please, report a bug");
		}
		if(!is_uploaded_file($_FILES[$name]['tmp_name'])){
			return PEAR::raiseError("The file is not an uploaded file");
		}
		require_once dirname(__file__) . "/Archive/Reader/File.php";
		return new File_Archive_Reader_File($_FILES[$name]['tmp_name'], $_FILES[$name]['name'],
			$_FILES[$name]['type']);
	}

	function cache(&$toConvert){
		$source = &File_Archive::_convertToReader($toConvert);
		if(PEAR::isError($source)){
			return $source;
		}
		require_once dirname(__file__) . '/Archive/Reader/Cache.php';
		return new File_Archive_Reader_Cache($source);
	}

	function &_convertToReader(&$source){
		if(is_string($source)){
			$cacheCondition = File_Archive::getOption('cacheCondition');
			if($cacheCondition !== false && preg_match($cacheCondition, $source)){
				return File_Archive::cache(File_Archive::read($source));
			} else{
				return File_Archive::read(@$source);
			}
		} else
			if(is_array($source)){
				return File_Archive::readMulti($source);
			} else{
				return $source;
			}
	}

	function &_convertToWriter(&$dest){
		if(is_string($dest)){
			return File_Archive::appender($dest);
		} else
			if(is_array($dest)){
				require_once dirname(__file__) . '/Archive/Writer/Multi.php';
				$writer = new File_Archive_Writer_Multi();
				foreach($dest as $key => $foo){
					$writer->addWriter($dest[$key]);
				}
			} else{
				return $dest;
			}
	}

	function isKnownExtension($extension){
		return $extension == 'tar' || $extension == 'zip' || $extension == 'gz' || $extension ==
			'tgz' || $extension == 'tbz' || $extension == 'bz2' || $extension == 'bzip2' ||
			$extension == 'ar' || $extension == 'deb';
	}

	function readArchive($extension, &$toConvert, $sourceOpened = false){
		$source = &File_Archive::_convertToReader($toConvert);
		if(PEAR::isError($source)){
			return $source;
		}
		switch($extension){
			case 'tgz':
				return File_Archive::readArchive('tar', File_Archive::readArchive('gz', $source, $sourceOpened));
			case 'tbz':
				return File_Archive::readArchive('tar', File_Archive::readArchive('bz2', $source,
					$sourceOpened));
			case 'tar':
				require_once dirname(__file__) . '/Archive/Reader/Tar.php';
				return new File_Archive_Reader_Tar($source, $sourceOpened);
			case 'gz':
			case 'gzip':
				require_once dirname(__file__) . '/Archive/Reader/Gzip.php';
				return new File_Archive_Reader_Gzip($source, $sourceOpened);
			case 'zip':
				require_once dirname(__file__) . '/Archive/Reader/Zip.php';
				return new File_Archive_Reader_Zip($source, $sourceOpened);
			case 'bz2':
			case 'bzip2':
				require_once dirname(__file__) . '/Archive/Reader/Bzip2.php';
				return new File_Archive_Reader_Bzip2($source, $sourceOpened);
			case 'deb':
			case 'ar':
				require_once dirname(__file__) . '/Archive/Reader/Ar.php';
				return new File_Archive_Reader_Ar($source, $sourceOpened);
			default:
				return false;
		}
	}

	function readMemory($memory, $filename, $stat = array(), $mime = null){
		require_once dirname(__file__) . "/Archive/Reader/Memory.php";
		return new File_Archive_Reader_Memory($memory, $filename, $stat, $mime);
	}

	function readMulti($sources = array()){
		require_once dirname(__file__) . "/Archive/Reader/Multi.php";
		$result = new File_Archive_Reader_Multi();
		foreach($sources as $index => $foo){
			$s = &File_Archive::_convertToReader($sources[$index]);
			if(PEAR::isError($s)){
				return $s;
			} else{
				$result->addSource($s);
			}
		}
		return $result;
	}

	function readConcat(&$toConvert, $filename, $stat = array(), $mime = null){
		$source = &File_Archive::_convertToReader($toConvert);
		if(PEAR::isError($source)){
			return $source;
		}
		require_once dirname(__file__) . "/Archive/Reader/Concat.php";
		return new File_Archive_Reader_Concat($source, $filename, $stat, $mime);
	}

	function filter($predicate, &$toConvert){
		$source = &File_Archive::_convertToReader($toConvert);
		if(PEAR::isError($source)){
			return $source;
		}
		require_once dirname(__file__) . "/Archive/Reader/Filter.php";
		return new File_Archive_Reader_Filter($predicate, $source);
	}

	function predTrue(){
		require_once dirname(__file__) . "/Archive/Predicate/True.php";
		return new File_Archive_Predicate_True();
	}

	function predFalse(){
		require_once dirname(__file__) . "/Archive/Predicate/False.php";
		return new File_Archive_Predicate_False();
	}

	function predAnd(){
		require_once dirname(__file__) . "/Archive/Predicate/And.php";
		$pred = new File_Archive_Predicate_And();
		$args = func_get_args();
		foreach($args as $p){
			$pred->addPredicate($p);
		}
		return $pred;
	}

	function predOr(){
		require_once dirname(__file__) . "/Archive/Predicate/Or.php";
		$pred = new File_Archive_Predicate_Or();
		$args = func_get_args();
		foreach($args as $p){
			$pred->addPredicate($p);
		}
		return $pred;
	}

	function predNot($pred){
		require_once dirname(__file__) . "/Archive/Predicate/Not.php";
		return new File_Archive_Predicate_Not($pred);
	}

	function predMinSize($size){
		require_once dirname(__file__) . "/Archive/Predicate/MinSize.php";
		return new File_Archive_Predicate_MinSize($size);
	}

	function predMinTime($time){
		require_once dirname(__file__) . "/Archive/Predicate/MinTime.php";
		return new File_Archive_Predicate_MinTime($time);
	}

	function predMaxDepth($depth){
		require_once dirname(__file__) . "/Archive/Predicate/MaxDepth.php";
		return new File_Archive_Predicate_MaxDepth($depth);
	}

	function predExtension($list){
		require_once dirname(__file__) . "/Archive/Predicate/Extension.php";
		return new File_Archive_Predicate_Extension($list);
	}

	function predMIME($list){
		require_once dirname(__file__) . "/Archive/Predicate/MIME.php";
		return new File_Archive_Predicate_MIME($list);
	}

	function predEreg($ereg){
		require_once dirname(__file__) . "/Archive/Predicate/Ereg.php";
		return new File_Archive_Predicate_Ereg($ereg);
	}

	function predEregi($ereg){
		require_once dirname(__file__) . "/Archive/Predicate/Eregi.php";
		return new File_Archive_Predicate_Eregi($ereg);
	}

	function predIndex($indexes){
		require_once dirname(__file__) . "/Archive/Predicate/Index.php";
		return new File_Archive_Predicate_Index($indexes);
	}

	function predCustom($expression){
		require_once dirname(__file__) . "/Archive/Predicate/Custom.php";
		return new File_Archive_Predicate_Custom($expression);
	}

	function toMail($to, $headers, $message, $mail = null){
		require_once dirname(__file__) . "/Archive/Writer/Mail.php";
		return new File_Archive_Writer_Mail($to, $headers, $message, $mail);
	}

	function toFiles($baseDir = ""){
		require_once dirname(__file__) . "/Archive/Writer/Files.php";
		return new File_Archive_Writer_Files($baseDir);
	}

	function toMemory(){
		$v = '';
		return File_Archive::toVariable($v);
	}

	function toVariable(&$v){
		require_once dirname(__file__) . "/Archive/Writer/Memory.php";
		return new File_Archive_Writer_Memory($v);
	}

	function toMulti(&$aC, &$bC){
		$a = &File_Archive::_convertToWriter($aC);
		$b = &File_Archive::_convertToWriter($bC);
		if(PEAR::isError($a)){
			return $a;
		}
		if(PEAR::isError($b)){
			return $b;
		}
		require_once dirname(__file__) . "/Archive/Writer/Multi.php";
		$writer = new File_Archive_Writer_Multi();
		$writer->addWriter($a);
		$writer->addWriter($b);
		return $writer;
	}

	function toOutput($sendHeaders = true){
		require_once dirname(__file__) . "/Archive/Writer/Output.php";
		return new File_Archive_Writer_Output($sendHeaders);
	}

	function toArchive($filename, &$toConvert, $type = null, $stat = array(), $autoClose = true){
		$innerWriter = &File_Archive::_convertToWriter($toConvert);
		if(PEAR::isError($innerWriter)){
			return $innerWriter;
		}
		$shortcuts = array("tgz", "tbz");
		$reals = array("tar.gz", "tar.bz2");
		if($type === null){
			$extensions = strtolower($filename);
		} else{
			$extensions = strtolower($type);
		}
		$extensions = explode('.', str_replace($shortcuts, $reals, $extensions));
		if($innerWriter !== null){
			$writer = &$innerWriter;
		} else{
			$writer = File_Archive::toFiles();
		}
		$nbCompressions = 0;
		$currentFilename = $filename;
		while(($extension = array_pop($extensions)) !== null){
			unset($next);
			switch($extension){
				case "tar":
					require_once dirname(__file__) . "/Archive/Writer/Tar.php";
					$next = new File_Archive_Writer_Tar($currentFilename, $writer, $stat, $autoClose);
					unset($writer);
					$writer = &$next;
					break;
				case "zip":
					require_once dirname(__file__) . "/Archive/Writer/Zip.php";
					$next = new File_Archive_Writer_Zip($currentFilename, $writer, $stat, $autoClose);
					unset($writer);
					$writer = &$next;
					break;
				case "gz":
				case "gzip":
					require_once dirname(__file__) . "/Archive/Writer/Gzip.php";
					$next = new File_Archive_Writer_Gzip($currentFilename, $writer, $stat, $autoClose);
					unset($writer);
					$writer = &$next;
					break;
				case "bz2":
				case "bzip2":
					require_once dirname(__file__) . "/Archive/Writer/Bzip2.php";
					$next = new File_Archive_Writer_Bzip2($currentFilename, $writer, $stat, $autoClose);
					unset($writer);
					$writer = &$next;
					break;
				case "deb":
				case "ar":
					require_once dirname(__file__) . "/Archive/Writer/Ar.php";
					$next = new File_Archive_Writer_Ar($currentFilename, $writer, $stat, $autoClose);
					unset($writer);
					$writer = &$next;
					break;
				default:
					if($type !== null || $nbCompressions == 0){
						return PEAR::raiseError("Archive $extension unknown");
					}
					break;
			}
			$nbCompressions++;
			$autoClose = true;
			$currentFilename = implode(".", $extensions);
		}
		return $writer;
	}

	function extract(&$sourceToConvert, &$destToConvert, $autoClose = true, $bufferSize =
	0){
		$source = &File_Archive::_convertToReader($sourceToConvert);
		if(PEAR::isError($source)){
			return $source;
		}
		$dest = &File_Archive::_convertToWriter($destToConvert);
		return $source->extract($dest, $autoClose, $bufferSize);
	}

	function appenderFromSource(&$toConvert, $URL = null, $unique = null, $type = null,
		$stat = array()){
		$source = &File_Archive::_convertToReader($toConvert);
		if(PEAR::isError($source)){
			return $source;
		}
		if($unique == null){
			$unique = File_Archive::getOption("appendRemoveDuplicates");
		}
		PEAR::pushErrorHandling(PEAR_ERROR_RETURN);
		if($URL === null){
			$result = &$source;
		} else{
			if($type === null){
				$result = File_Archive::_readSource($source, $URL . '/', $reachable, $baseDir);
			} else{
				$result = File_Archive::readArchive($type, File_Archive::_readSource($source, $URL,
					$reachable, $baseDir));
			}
		}
		PEAR::popErrorHandling();
		if(!PEAR::isError($result)){
			if($unique){
				require_once dirname(__file__) . "/Archive/Writer/UniqueAppender.php";
				return new File_Archive_Writer_UniqueAppender($result);
			} else{
				return $result->makeAppendWriter();
			}
		}
		$stat[9] = $stat['mtime'] = time();
		if(empty($baseDir)){
			if($source !== null){
				$writer = &$source->makeWriter();
			} else{
				$writer = &File_Archive::toFiles();
			}
			if(PEAR::isError($writer)){
				return $writer;
			}
			PEAR::pushErrorHandling(PEAR_ERROR_RETURN);
			$result = File_Archive::toArchive($reachable, $writer, $type);
			PEAR::popErrorHandling();
			if(PEAR::isError($result)){
				$result = File_Archive::toFiles($reachable);
			}
		} else{
			$reachedSource = File_Archive::readSource($source, $reachable);
			if(PEAR::isError($reachedSource)){
				return $reachedSource;
			}
			$writer = $reachedSource->makeWriter();
			if(PEAR::isError($writer)){
				return $writer;
			}
			PEAR::pushErrorHandling(PEAR_ERROR_RETURN);
			$result = File_Archive::toArchive($baseDir, $writer, $type);
			PEAR::popErrorHandling();
			if(PEAR::isError($result)){
				require_once dirname(__file__) . "/Archive/Writer/AddBaseName.php";
				$result = new File_Archive_Writer_AddBaseName($baseDir, $writer);
				if(PEAR::isError($result)){
					return $result;
				}
			}
		}
		return $result;
	}

	function appender($URL, $unique = null, $type = null, $stat = array()){
		$source = null;
		return File_Archive::appenderFromSource($source, $URL, $unique, $type, $stat);
	}

	function removeFromSource(&$pred, &$toConvert, $URL = null){
		$source = &File_Archive::_convertToReader($toConvert);
		if(PEAR::isError($source)){
			return $source;
		}
		if($URL === null){
			$result = &$source;
		} else{
			if(substr($URL, -1) !== '/'){
				$URL .= '/';
			}
			$result = File_Archive::readSource($source, $URL);
		}
		$writer = $result->makeWriterRemoveFiles($pred);
		if(PEAR::isError($writer)){
			return $writer;
		}
		$writer->close();
	}

	function remove($pred, $URL){
		$source = null;
		return File_Archive::removeFromSource($pred, $source, $URL);
	}

	function removeDuplicatesFromSource(&$toConvert, $URL = null){
		$source = &File_Archive::_convertToReader($toConvert);
		if(PEAR::isError($source)){
			return $source;
		}
		if($URL !== null && substr($URL, -1) != '/'){
			$URL .= '/';
		}
		if($source === null){
			$source = File_Archive::read($URL);
		}
		require_once dirname(__file__) . "/Archive/Predicate/Duplicate.php";
		$pred = new File_Archive_Predicate_Duplicate($source);
		$source->close();
		return File_Archive::removeFromSource($pred, $source, null);
	}

	function removeDuplicates($URL){
		$source = null;
		return File_Archive::removeDuplicatesFromSource($source, $URL);
	}
}

?>
