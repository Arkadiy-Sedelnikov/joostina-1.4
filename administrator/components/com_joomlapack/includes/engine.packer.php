<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();
global $JPConfiguration;
define('PCLZIP_TEMPORARY_DIR', $JPConfiguration->OutputDirectory . '/');

class CPackerEngine{
	/**
	 * Have we finished processing our task?
	 * @access private
	 * @var boolean
	 */
	var $_isFinished;

	/**
	 * Full pathname to the archive file
	 * @access private
	 * @var string
	 */
	var $_archiveFile;

	/**
	 * Maximum fragment number
	 * @access private
	 * @var long
	 */
	var $_maxFragment;

	/**
	 * Current fragment number
	 * @access private
	 * @var long
	 */
	var $_currentFragment;

	/**
	 * Active file list descriptor
	 * @access private
	 * @var array
	 */
	var $_fileListDescriptor;

	/**
	 * Total size of file lists
	 * @access private
	 * @var long
	 */
	var $_totalBytes;

	/**
	 * Total size processed so far
	 * @access private
	 * @var long
	 */
	var $_currentBytes;

	function CPackerEngine(){
		global $JPConfiguration;
		$database = database::getInstance();

		$this->_isFinished = false;
		$this->_archiveFile = $JPConfiguration->OutputDirectory . '/' . $this->_expandTarName($JPConfiguration->TarNameTemplate, $JPConfiguration->boolCompress);
		$this->_currentFragment = 0;
		$this->_totalBytes = 0;
		$this->_currentBytes = 0;

		$sql = 'SELECT* FROM #__jp_packvars WHERE `key` like \'fragment%\'';
		$database->setQuery($sql);
		$database->query();
		$this->_maxFragment = $database->getNumRows();
		for($i = 1; $i <= $this->_maxFragment; $i++){
			$sql = 'SELECT `value2` FROM #__jp_packvars WHERE `key` = \'fragment' . $i . '\'';
			$database->setQuery($sql);
			$serialized = $database->loadResult();
			$descriptor = unserialize($serialized);
			$this->_totalBytes += $descriptor['size'];
			unset($descriptor);
		}

		// Remove any stored compression object
		$JPConfiguration->DeleteDebugVar('zipobject');

		CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_GZIP_FIRST_STEP);
	}

	/**
	 * Try to execute the business logic of this step
	 */
	function tick(){
		global $JPConfiguration;

		if($this->_isFinished){
			// We have already finished
			CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_GZIP_FINISHED);
			$returnArray = array();
			$returnArray['HasRun'] = false;
			$returnArray['Domain'] = 'Packing';
			$returnArray['Step'] = '';
			$returnArray['Substep'] = '';
			$returnArray['backfile'] = $this->_archiveFile;
			// Also remove stored compression object, if exists
			$JPConfiguration->DeleteDebugVar('zipobject');
			return $returnArray; // Indicate we have finished
		} else{
			// Try to pack next fragment
			$this->_currentFragment++;
			if($this->_currentFragment > $this->_maxFragment){
				CJPLogger::WriteLog(_JP_LOG_INFO, _JP_PACK_FINISHED);
				// We have just finished, as we ended up on one fragment past the end. Glue archive and return.
				$this->_fileListDescriptor['files'] = null;
//				$ret = $this->_archiveFileList();

				$this->_isFinished = true;
				$returnArray = array();
				$returnArray['HasRun'] = true;
				$returnArray['Domain'] = 'Packing';
				$returnArray['Step'] = '';
				$returnArray['Substep'] = '';
				return $returnArray; // Indicate we have finished
			} else{
				CJPLogger::WriteLog(_JP_LOG_INFO, _JP_GZIP_OF_FRAGMENT . $this->_currentFragment);
				$this->_importFragment($this->_currentFragment);
				$this->_archiveFileList();
				$returnArray = array();
				$returnArray['HasRun'] = true;
				$returnArray['Domain'] = 'Packing';
				$returnArray['Step'] = $this->_currentFragment;
				$this->_currentBytes += $this->_fileListDescriptor['size'];
				$returnArray['Substep'] = $this->_currentBytes . ' / ' . $this->_totalBytes;
				return $returnArray; // Indicate we have finished
			}
		}
	}

	/**
	 * Loads a fragment's filelist
	 */
	function _importFragment($fragmentID){
		$database = database::getInstance();
		$sql = 'SELECT `value2` FROM #__jp_packvars WHERE `key` = \'fragment' . $fragmentID . '\'';
		$database->setQuery($sql);
		$this->_fileListDescriptor = unserialize($database->loadResult());
		if($this->_fileListDescriptor === false){
			return false;
		} else{
			return true;
		}
	}

	/**
	 * Returns the path to trim and the path to add to the fragment's files
	 */
	function _getPaths($fragmentType){
		global $JPConfiguration;

		$retArray = array();
		switch($fragmentType){
			case 'site':
				$retArray['remove'] = $JPConfiguration->TranslateWinPath(JPATH_BASE);
				$retArray['add'] = '';
				break;
			case 'installation':
				$filePath = $JPConfiguration->TranslateWinPath($JPConfiguration->TempDirectory . '/installation/');
				$retArray['remove'] = $filePath;
				$retArray['add'] = 'installation';
				break;
			case 'sql':
				$retArray['remove'] = $JPConfiguration->TranslateWinPath($JPConfiguration->TempDirectory);
				$retArray['add'] = 'installation/sql';
				break;
			// case "external":
		} // switch

		CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_CURRENT_FRAGMENT . ' ' . $fragmentType);
		CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_DELETE_PATH . ' ' . $retArray['remove']);
		CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_PATH_TO_DELETE . ': ' . $retArray['add']);
		return $retArray;
	}

	/**
	 * Performs the actual archiving of the current file list
	 */
	function _archiveFileList(){
		global $JPConfiguration;
		$database = database::getInstance();

		include_once (_JLPATH_ADMINISTRATOR . '/includes/pcl/pclzip.lib.php');

		// Check for existing instance of the object stored in db
		$sql = "SELECT COUNT(*) FROM #__jp_packvars WHERE `key`='zipobject'";
		$database->setQuery($sql);
		$numRows = $database->loadResult();

		if($numRows == 0){
			CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_SAVING_ARCHIVE_INFO);
			// создание файла архива
			$zip = new PclZip($this->_archiveFile);
		} else{
			// Load from db
			CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_LOADING_ARCHIVE_INFO);
			$sql = "SELECT value2 FROM #__jp_packvars WHERE `key`='zipobject'";
			$database->setQuery($sql);
			$serialized = $database->loadResult();
//			$archive = unserialize($serialized);
			unset($serialized);
		}
		// Get paths to add / remove
		$pathsAddRemove = $this->_getPaths($this->_fileListDescriptor['type']);
		// удаляем всё лишнее из путей к файлам внутри архива
		$pathsAddRemove['remove'] = PclZipUtilTranslateWinPath($pathsAddRemove['remove']);
		// добавление файлов в архив, или завершение архивирования
		if(is_array($this->_fileListDescriptor['files'])){
			CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_ADDING_FILE_TO_ARCHIVE);

			// добавление файлов в архив
			$zip = new PclZip($this->_archiveFile);
			$zip->add($this->_fileListDescriptor['files'], '', $pathsAddRemove['remove']);

			CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_ARCHIVING);
			// Store object
			$serialized = serialize($zip);
			$JPConfiguration->WriteDebugVar('zipobject', $serialized, true);
			unset($serialized);
		} else{
			// завершение архивирования
			$zip = new PclZip($this->_archiveFile);
			$to_file = PclZipUtilTranslateWinPath(_JLPATH_ADMINISTRATOR . '/backups/installation/');
			$zip->add($to_file, '', $pathsAddRemove['remove']);
			CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_ARCHIVE_COMPLETED);
		}
		unset($zip);
	}

	/**
	 * Transforms a naming template to the final name of the archive by parsing template
	 * tags within the name.
	 * @param string  $templateName The naming template
	 * @param boolean $boolCompress "tgz" if the archive should be compressed (and thus have .tar.gz extension),
	 * "tar" for not (and thus have a .tar extension) or "zip" for a .zip file.
	 */
	function _expandTarName($templateName){
		global $JPConfiguration;
		// Get the proper extension
		switch($JPConfiguration->boolCompress){
			case "zip":
				$extension = ".zip";
				break;
			case "jpa":
				$extension = ".jpa";
				break;
		} // switch

		// Parse [DATE] tag
		$dateExpanded = strftime("%Y%m%d", time());
		$templateName = str_replace("[DATE]", $dateExpanded, $templateName);

		// Parse [TIME] tag
		$timeExpanded = strftime("%H%M%S", time());
		$templateName = str_replace("[TIME]", $timeExpanded, $templateName);

		// Parse [HOST] tag
		$templateName = str_replace("[HOST]", $_SERVER['SERVER_NAME'], $templateName);

		return $templateName . $extension;
	}
}