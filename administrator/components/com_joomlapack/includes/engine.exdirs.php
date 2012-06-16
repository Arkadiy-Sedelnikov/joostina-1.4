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

class CDirExclusionFilter{

	/**
	@var array Array of the database filters*/
	var $_filterArray;

	/**
	 * Class initializer, loads existing filters
	 */
	function CDirExclusionFilter(){
		$database = database::getInstance();

		// Initialize by loading any exisiting filters
		$sql = 'SELECT* FROM #__jp_def';
		$database->setQuery($sql);
		$database->query();

		$this->_filterArray = $database->loadAssocList();
	}

	function ReplaceSlashes($string){
		return str_replace("\\", "/", $string);
	}

	/**
	 * Returns the array of the filters
	 * @return array The exclusion filters
	 */
	function getFilters(){
		global $JPConfiguration, $mosConfig_cachepath;

		// Initialize with existing filters
		if(is_null($this->_filterArray)){
			$myArray = array();
		} else{
			$myArray = array();

			foreach($this->_filterArray as $filter){
				$myArray[] = $filter['directory'];
			}
		}

		// каталоги которые изначально не надо резервировать
		$myArray[] = $this->ReplaceSlashes($JPConfiguration->OutputDirectory);
		$myArray[] = $this->ReplaceSlashes($JPConfiguration->TempDirectory);
		$myArray[] = $this->ReplaceSlashes($mosConfig_cachepath);
		return $myArray;
	}

	/**
	 * Returns the contents of a directory and their exclusion status
	 * @param $root string Start from this folder
	 * @return array Directories and their status
	 */
	function getDirectory($root){
		// If there's no root directory specified, use the site's root
		$root = is_null($root) ? JPATH_BASE : $root;

		// Initialize filter list
		$tempFilterArray = $this->getFilters();

		$FilterArray = array();
		foreach($tempFilterArray as $filter){
			$FilterArray[] = $this->ReplaceSlashes($filter);
		}

		// Initialize directories array
		$arDirs = array();

		// Get subfolders
		require_once ('engine.abstraction.php');
		$FS = new CFSAbstraction();

		$allFilesAndDirs = $FS->getDirContents($root);

		if(!($allFilesAndDirs === false)){
			foreach($allFilesAndDirs as $fileDef){
				$fileName = $fileDef['name'];
				if($fileDef['type'] == 'dir'){
					$fileName = basename($fileName);
					if(($this->ReplaceSlashes($root) == $this->ReplaceSlashes(JPATH_BASE)) &&
						(($fileName == ".") || ($fileName == '..'))
					){
					} else{
						if($this->_filterArray == ''){
							$arDirs[$fileName] = false;
						} else{
							$arDirs[$fileName] = in_array($this->ReplaceSlashes($root . DS . $fileName),
								$FilterArray);
						}
					}
				} // if
			} // foreach
		} // if

		ksort($arDirs);
		return $arDirs;
	}

	function modifyFilter($root, $dir, $checked){
		$database = database::getInstance();

		$activate = ($checked == 'on') || ($checked == 'yes') || ($checked == 'checked') ? true : false;

		$sql = 'SELECT `def_id` FROM #__jp_def WHERE `directory`=\'' . $database->getEscaped($this->ReplaceSlashes($root . '/' . $dir)) . '\'';
		$database->setQuery($sql);
		$database->query();
		$def_id = $database->loadResult();

		if($activate){
			// Add the filter, if it doesn't exist
			if(is_null($def_id)){
				$sql = 'INSERT INTO #__jp_def (`directory`) VALUES (\'' . $database->getEscaped($this->ReplaceSlashes($root . '/' . $dir)) . '\')';
				$database->setQuery($sql);
				$database->query();
			}
		} else{
			// Remove the filter, if it exists
			$sql = 'DELETE FROM #__jp_def WHERE `directory` = \'' . $database->getEscaped($this->ReplaceSlashes($root . '/' . $dir)) . '\'';
			$database->setQuery($sql);
			$database->query();
		}
	}
}