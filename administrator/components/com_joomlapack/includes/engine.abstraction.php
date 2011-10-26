<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

class CFSAbstraction {

	/** Should we use glob() ?
	 * @var boolean
	 */
	var $_globEnable;

	/**
	 * Public constructor for CFSAbstraction class. Does some heuristics to figure out the
	 * server capabilities and setup internal variables
	 */
	function CFSAbstraction() {
		// Don't use glob if it's disabled or if opendir is available
		$this->_globEnable = function_exists('glob');
		if(function_exists('opendir') && function_exists('readdir') && function_exists('closedir'))
			$this->_globEnable = false;
	}

	/**
	 * Searches the given directory $dirName for files and folders and returns a multidimensional array.
	 * If the directory is not accessible, returns FALSE
	 * @return array See function description for details
	 */
	function getDirContents($dirName,$shellFilter = null) {
		if($this->_globEnable) {
			return $this->_getDirContents_glob($dirName,$shellFilter);
		} else {
			return $this->_getDirContents_opendir($dirName,$shellFilter);
		}
	}

	// ============================================================================
	// PRIVATE SECTION
	// ============================================================================

	/**
	 * Searches the given directory $dirName for files and folders and returns a multidimensional array.
	 * If the directory is not accessible, returns FALSE. This function uses the PHP glob() function.
	 * @return array See function description for details
	 */
	function _getDirContents_glob($dirName,$shellFilter = null) {
		global $JPConfiguration; // Needed for TranslateWinPath function

		if(is_null($shellFilter)) {
			// Get folder contents
			$allFilesAndDirs1 = @glob($dirName."/*"); // regular files
			$allFilesAndDirs2 = @glob($dirName."/.*"); //*nix hidden files

			// Try to merge the arrays
			if($allFilesAndDirs1 === false) {
				if($allFilesAndDirs2 === false) {
					$allFilesAndDirs = false;
				} else {
					$allFilesAndDirs = $allFilesAndDirs2;
				}
			} elseif($allFilesAndDirs2 === false) {
				$allFilesAndDirs = $allFilesAndDirs1;
			} else {
				$allFilesAndDirs = @array_merge($allFilesAndDirs1,$allFilesAndDirs2);
			}

			// Free unused arrays
			unset($allFilesAndDirs1);
			unset($allFilesAndDirs2);

		} else {
			$allFilesAndDirs = @glob($dirName.'/'.$shellFilter); // filtered files
		}

		// Check for unreadable directories
		if($allFilesAndDirs === false) {
			return false;
		}

		// Populate return array
		$retArray = array();

		foreach($allFilesAndDirs as $filename) {
			$filename = $JPConfiguration->TranslateWinPath($filename);
			$newEntry['name'] = $filename;
			$newEntry['type'] = filetype($filename);
			if($newEntry['type'] == "file") {
				$newEntry['size'] = filesize($filename);
			} else {
				$newEntry['size'] = 0;
			}
			$retArray[] = $newEntry;
		}

		return $retArray;
	}
	// получение списка файлов каталога
	function _getDirContents_opendir($dirName,$shellFilter = null) {
		global $JPConfiguration;
		$handle = @opendir($dirName);
		if($handle === false) {
			return false;
		}
		$retArray = array();
		while(!(($filename = readdir($handle)) === false)) {
			$match = is_null($shellFilter);
			$match = (!$match)?fnmatch($shellFilter,$filename):true;
			if($match) {
				$filename = $JPConfiguration->TranslateWinPath($dirName."/".$filename);
				$newEntry['name'] = $filename;
				$newEntry['type'] = @filetype($filename);
				if($newEntry['type'] !== false) {
					if($newEntry['type'] == 'file') {
						$newEntry['size'] = @filesize($filename);
					} else {
						$newEntry['size'] = 0;
					}
					$retArray[] = $newEntry;
				}
			}
		}
		closedir($handle);
		return $retArray;
	}
}

// FIX 1.1.0 -- fnmatch not available on non-POSIX systems
// Thanks to soywiz@php.net for this usefull alternative function [http://gr2.php.net/fnmatch]
if(!function_exists('fnmatch')) {
	function fnmatch($pattern,$string) {
		return @preg_match('/^'.strtr(addcslashes($pattern,'/\\.+^$(){}=!<>|'),array('*' =>'.*','?' => '.?')).'$/i',$string);
	}
}