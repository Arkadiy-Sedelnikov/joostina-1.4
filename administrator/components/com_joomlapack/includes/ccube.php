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

global $CUBE; // The CUBE object instance

class CCUBE{
	/**
	@var string Current domain of operation**/
	var $_currentDomain;
	/**
	@var string Current step**/
	var $_currentStep;
	/**
	@var string Current substep**/
	var $_currentSubstep;
	/**
	@var object Current engine object executing work**/
	var $_currentObject;
	/**
	@var boolean Indicates if we are done**/
	var $_isFinished;
	/**
	@var string The current error, if any**/
	var $_Error;
	var $_OnlyDBMode;

	var $backfile;

	/**
	 * Creates a new instance of the CUBE object and empties the temporary
	 * database tables
	 */
	function CCUBE($OnlyDBMode = false){
		global $JPConfiguration;
		$database = database::getInstance();
		$this->_OnlyDBMode = $OnlyDBMode;
		// Remove old entries from 'packvars' table
		$sql = 'DELETE FROM #__jp_packvars WHERE `key` LIKE "%CUBE%"';
		$database->setQuery($sql);
		$database->query();
		// Initialize internal variables
		$this->_currentDomain = "init"; // Current domain of operation
		$this->_currentObject = null; // Nullify current object
		$this->_isFinished = false;
		$this->_Error = false;
		CJPLogger::ResetLog();
		CJPLogger::WriteLog(_JP_LOG_INFO, _JP_BACKUPPING);

		if($JPConfiguration->logLevel >= 3){
			CJPLogger::WriteLog(_JP_LOG_INFO, _JP_PHPINFO);
			CJPLogger::WriteLog(_JP_LOG_INFO, 'PHP                :' . phpversion());
			CJPLogger::WriteLog(_JP_LOG_INFO, 'OS Version         :' . php_uname('s'));
			CJPLogger::WriteLog(_JP_LOG_INFO, 'Safe mode          :' . ini_get('safe_mode'));
			CJPLogger::WriteLog(_JP_LOG_INFO, 'Display errors     :' . ini_get('display_errors'));
			CJPLogger::WriteLog(_JP_LOG_INFO, 'Disabled functions :' . ini_get('disable_functions'));
			CJPLogger::WriteLog(_JP_LOG_INFO, 'Max. exec. time    :' . ini_get('max_execution_time'));
			CJPLogger::WriteLog(_JP_LOG_INFO, 'Memory limit       :' . ini_get('memory_limit'));

			if(function_exists('memory_get_usage')) CJPLogger::WriteLog(_JP_LOG_INFO, _JP_FREEMEMORY . ' :' . memory_get_usage());

			if(function_exists('gzcompress')){
				CJPLogger::WriteLog(_JP_LOG_INFO, _JP_GZIP_ENABLED);
			} else{
				CJPLogger::WriteLog(_JP_LOG_INFO, _JP_GZIP_NOT_ENABLED);
			}
			CJPLogger::WriteLog(_JP_LOG_INFO, '--------------------------------------------------------------------------------');
		}
		if($this->_OnlyDBMode){
			CJPLogger::WriteLog(_JP_LOG_INFO, _JP_START_BACKUP_DB);
		} else{
			CJPLogger::WriteLog(_JP_LOG_INFO, _JP_START_BACKUP_FILES);
		}
	}

	/**
	 * The main workhorse, does all the job for us
	 */
	function tick(){
		if(!$this->_isFinished){
			switch($this->_runAlgorithm()){
				case 0:
					// more work to do, return OK
					CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_CUBE_ON_STEP . ' ' . $this->_currentDomain);
					return $this->_storeCUBEArray();
					break;
				case 1:
					// Engine part finished
					CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_CUBE_STEP_FINISHED . $this->_currentDomain);
					$this->_getNextObject();
					if($this->_currentDomain == 'finale'){
						// We have finished the whole process.
						$this->_cleanup();
						CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_CUBE_FINISHED);
					}
					return $this->_storeCUBEArray();
					break;
				case 2:
					CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_ERROR_ON_STEP . $this->_currentDomain);
					// An error occured...
					$ret = $this->_storeCUBEArray();
					$this->_cleanup();
					return $ret;
					break;
			} // switch
		}
	}

	/**
	 * Post work clean-up of files & database
	 */
	function _cleanup(){
		global $JPConfiguration;
		$database = database::getInstance();

		CJPLogger::WriteLog(_JP_LOG_INFO, _JP_CLEANUP);
		// Define which entries to keep in #__jp_packvars
		$keepInDB = array('CUBEArray');

		$folderPath = $JPConfiguration->TempDirectory;
		$file1 = $folderPath . '/joostina.sql';
		$file2 = $folderPath . '/sample_data.sql';

		$this->_unlinkRecursive($file1);
		$this->_unlinkRecursive($file2);

		// Clean database
		// ---------------------------------------------------------------------
		$sql = 'SELECT `key` FROM #__jp_packvars';
		$database->setQuery($sql);
		$keys = $database->loadResultArray();

		foreach($keys as $key){
			if(!in_array($key, $keepInDB)){
				$JPConfiguration->DeleteDebugVar($key);
			}
		}

		unset($keys);
	}

	/**
	 * Recursively deletes file inside a directory
	 * @param string $dirName Directory to delete
	 */
	function _unlinkRecursive($dirName){
		require_once 'engine.abstraction.php';
		$FS = new CFSAbstraction();

		CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_RECURSING_DELETION . $dirName);

		if(is_file($dirName)){
			CJPLogger::WriteLog(_JP_LOG_DEBUG, $dirName . ' - ' . _JP_NOT_FILE);
			unlink($dirName);
		} elseif(is_dir($dirName)){
			// получение содержимого каталога
			$fileList = $FS->getDirContents($dirName);
			if($fileList === false){
				// ошибка получения содержимого каталога
				CJPLogger::WriteLog(_JP_LOG_WARNING, $dirName . ' - ' . _JP_ERROR_DEL_DIRECTORY);
			} else{
				foreach($fileList as $fileDescriptor){
					switch($fileDescriptor['type']){
						case 'dir':
							$this->_unlinkRecursive($dirName . '/' . $fileDescriptor['name']);
							break;
						case 'file':
							unlink($fileDescriptor['name']);
							break;
						// All other types (links, character devices etc) are ignored.
					}
				}
				@unlink($dirName);
			}
		}
	}

	/**
	 * Single step algorithm. Runs the tick() function of the $_currentObject
	 * until it finishes or produces an error, then returns the result array.
	 * @return integer 1 if we finished correctly, 2 if error occured.
	 */
	// быстрый режим
	function _algoSingleStep(){
		CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_QUICK_MODE);
		$finished = false;
		$error = false;

		while((!$finished)){
			$result = $this->_currentObject->tick();
			$this->_currentDomain = $result['Domain'];
			$this->_currentStep = $result['Step'];
			$this->_currentSubstep = $result['Substep'];
			if(isset($result['backfile'])) $this->backfile = $result['backfile'];
			if(isset($result['Error'])) $error = !($result['Error'] == '');
			$finished = $error ? true : !($result['HasRun']);

			$this->_storeCUBEArray();
		} // while

		if(!$error){
			CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_QUICK_MODE_ON_STEP . ' ' . $this->_currentDomain);
		} else{
			CJPLogger::WriteLog(_JP_LOG_ERROR, _JP_CANNOT_USE_QUICK_MODE . ' ' . $this->_currentDomain);
			CJPLogger::WriteLog(_JP_LOG_ERROR, $result['Error']);
		}
		$this->_Error = $error ? $result['Error'] : '';
		return $error ? 2 : 1;
	}

	/**
	 * Multi-step algorithm. Runs the tick() function of the $_currentObject once
	 * and returns.
	 * @return integer 0 if more work is to be done, 1 if we finished correctly,
	 * 2 if error eccured.
	 */
	// медленный режим
	function _algoMultiStep(){
		CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_MULTISTEP_MODE);
		$error = false;

		$result = $this->_currentObject->tick();
		$result['Error'] = '';
		$this->_currentDomain = $result['Domain'];
		$this->_currentStep = $result['Step'];
		$this->_currentSubstep = $result['Substep'];
		if(isset($result['backfile'])) $this->backfile = $result['backfile'];
		if(isset($result['Error'])) $error = !($result['Error'] == '');
		$finished = $error ? true : !($result['HasRun']);

		$this->_storeCUBEArray();

		if(!$error){
			CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_MULTISTEP_MODE_ON_STEP . ' ' . $this->_currentDomain);
		} else{
			CJPLogger::WriteLog(_JP_LOG_ERROR, _JP_MULTISTEP_MODE_ERROR . ' ' . $this->_currentDomain);
			CJPLogger::WriteLog(_JP_LOG_ERROR, $result['Error']);
		}
		$this->_Error = $error ? $result['Error'] : '';
		return $error ? 2 : ($finished ? 1 : 0);
	}

	/**
	 * Smart step algorithm. Runs the tick() function until we have consumed 75%
	 * of the maximum_execution_time (minus 1 seconds) within this procedure. If
	 * the available time is less than 1 seconds, it defaults to multi-step.
	 * @return integer 0 if more work is to be done, 1 if we finished correctly,
	 * 2 if error eccured.
	 */
	function _algoSmartStep(){
		CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_SMART_MODE);

		// получение максимального времени выполнения скрипта
		$maxExecTime = ini_get('maximum_execution_time');
		$startTime = $this->_microtime_float();
		// для отладки
		//$maxExecTime = 1;
		// если максимальное время выполнения скрипта не получено, или равно нулю - пропишем жесткое значение - 30 секунд
		if(($maxExecTime == '') || ($maxExecTime == 0)){
			// If we have no time limit, set a hard limit of 30 secs (safe for Apache and IIS timeouts)
			$maxExecTime = 30;

			// Used to equate this with Single Stepping
			//return $this->_algoSingleStep();
		}

		if($maxExecTime <= 1.75){
			// If the available time is less than the trigger value, switch to
			// multi-step
			return $this->_algoMultiStep();
		} else{
			// All checks passes, this is a SmartStep-enabled case
			$maxRunTime = ($maxExecTime - 1) * 0.75;
			$runTime = 0;
			$finished = false;
			$error = false;

			// Loop until time's up, we're done or an error occured
			while(($runTime <= $maxRunTime) && (!$finished) && (!$error)){
				$result = $this->_currentObject->tick();
				$this->_currentDomain = $result['Domain'];
				$this->_currentStep = $result['Step'];
				$this->_currentSubstep = $result['Substep'];
				if(isset($result['backfile'])) $this->backfile = $result['backfile'];
				if(isset($result['Error'])) $error = !($result['Error'] == '');
				$finished = $error ? true : !($result['HasRun']);

				$this->_storeCUBEArray();

				$endTime = $this->_microtime_float();
				$runTime = $endTime - $startTime;
			} // while

			// Return the result
			if(!$error){
				CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_SMART_MODE_ON_STEP . ' ' . $this->_currentDomain);
			} else{
				CJPLogger::WriteLog(_JP_LOG_ERROR, _JP_SMART_MODE_ERROR . ' ' . $this->_currentDomain);
				CJPLogger::WriteLog(_JP_LOG_ERROR, $result['Error']);
			}
			$this->_Error = $error ? $result['Error'] : '';
			return $error ? 2 : ($finished ? 1 : 0);
		}
	}

	/**
	 * Runs the user-selected algorithm for the current engine
	 */
	function _runAlgorithm(){
		$algo = $this->_selectAlgorithm();
		CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_CHOOSED_ALGO . ' ' . $algo . ' ' . _JP_ALGORITHM_FOR . ' ' . $this->_currentDomain);

		switch($algo){
			case 'single':
				// Single-step algorithm - fast but leads to timeouts in medium / big sites
				return $this->_algoSingleStep();
				break;
			case 'multi':
				// Multi-step algorithm - slow but most compatible
				return $this->_algoMultiStep();
				break;
			case 'smart':
				// SmartStep algorithm - best compromise between speed and compatibility
				return $this->_algoSmartStep();
				break;
			default:
				// No algorithm (null algorithm) for "init" and "finale" domains. Always returns success.
				//return $this->_isFinished ? 1 : 0;
				return 1;
		} // switch
	}

	/**
	 * Selects the algorithm to use based on the current domain
	 * @return string The algorithm to use
	 */
	function _selectAlgorithm(){
		global $JPConfiguration;
		switch($this->_currentDomain){
			case 'init':
			case 'finale':
				return '(null)';
				break;
			case 'FileList':
				return $JPConfiguration->fileListAlgorithm;
				break;
			case 'PackDB':
				return $JPConfiguration->dbAlgorithm;
				break;
			case 'Packing':
				return $JPConfiguration->packAlgorithm;
				break;
		}
	}

	/**
	 * Returns the current microtime as a float
	 */
	function _microtime_float(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	/**
	 * Creates the next engine object based on the current execution domain
	 * @return integer 0 = выполняется, 1 = всё выполнено, 2 = ошибка
	 */
	function _getNextObject(){
		// Kill existing object
		$this->_currentObject = null;
		// Try to figure out what object to spawn next
		switch($this->_currentDomain){
			case 'init':
				// ШАГ - создание списка файлов
				if($this->_OnlyDBMode){
					CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_NEXT_STEP_BACKUP_DB);
					// режим архивирования только базы данных
					require_once ('engine.dbdump.php');
					$this->_currentObject = new CDBBackupEngine($this->_OnlyDBMode);
					$this->_currentDomain = 'PackDB';
				} else{
					CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_NEXT_STEP_FILE_LIST);
					require_once ('engine.filelist.php');
					$this->_currentObject = new CFilelistEngine();
					$this->_currentDomain = 'FileList';
				}
				return 0;
				break;
			case 'FileList':
				CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_NEXT_STEP_BACKUP_DB);
				// ШАГ архивирование базы
				require_once ('engine.dbdump.php');
				$this->_currentObject = new CDBBackupEngine();
				$this->_currentDomain = 'PackDB';
				return 0;
				break;
			case 'PackDB':
				if($this->_OnlyDBMode){
					CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_NEXT_STEP_FINISHING);
					// ШАГ - все выполнено
					$this->_currentDomain = 'finale';
					return 1;
				} else{
					CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_NEXT_STEP_GZIP);
					// Next domain : File packing
					require_once ('engine.packer.php');
					$this->_currentObject = new CPackerEngine();
					$this->_currentDomain = 'Packing';
					return 0;
				}
				break;
			case 'Packing':
				CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_NEXT_STEP_FINISHED);
				// ШАГ - всё выполнено
				$this->_currentDomain = 'finale';
				return 1;
				break;
			case 'finale':
			default:
				CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_NO_NEXT_STEP);
				return 1;
				break;
		}
	}

	/**
	 * Creates the CUBE return array
	 * @return array A CUBE return array with timestamp data
	 */
	function _makeCUBEArray(){
		$ret['HasRun'] = $this->_isFinished ? 0 : 1;
		$ret['Domain'] = $this->_currentDomain;
		$ret['Step'] = htmlentities($this->_currentStep);
		$ret['Substep'] = htmlentities($this->_currentSubstep);
		$ret['Error'] = htmlentities($this->_Error);
		$ret['Timestamp'] = $this->_microtime_float();
		// ссылка на файл полученной резервной копии
		$ret['backfile'] = $this->backfile;
		return $ret;
	}

	/**
	 * Stores the CUBE return array to database
	 * @return array The CUBE array we stored in the database
	 */
	function _storeCUBEArray(){
		global $JPConfiguration;
		$ret = $this->_makeCUBEArray();
		$serialized = serialize($ret);
		$JPConfiguration->WriteDebugVar('CUBEArray', $serialized, true);
		unset($serialized);
		return $ret;
	}
}

/**
 * Tries to load and unserialize a CUBE object from the database. If it fails, it
 * creates a new object
 */
function loadJPCUBE($forceNew = false){
	global $JPConfiguration, $CUBE;
	$database = database::getInstance();

	if($forceNew){
		$CUBE = new CCUBE();
	} else{
		// Search for CUBEObject entry in database
		$sql = 'SELECT COUNT(*) FROM #__jp_packvars WHERE `key`=\'CUBEObject\'';
		$database->setQuery($sql);
		$numRecords = $database->loadResult();

		if($numRecords < 1){
			CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_NO_CUBE);
			$CUBE = new CCUBE();
		} else{
			// First, we need to see if we have to include an Engine class
			$cubeArray = loadJPCUBEArray();
			CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_CURRENT_STEP . ' ' . $cubeArray['Domain']);
			switch($cubeArray['Domain']){
				case 'FileList':
					require_once ('engine.filelist.php');
					break;
				case 'PackDB':
					require_once ('engine.dbdump.php');
					break;
				case 'Packing':
					require_once ('engine.packer.php');
					break;
			}
			// Now, resume the CUBE object
			$serializedCUBE = $JPConfiguration->ReadDebugVar('CUBEObject', true);
			$CUBE = unserialize($serializedCUBE);
			unset($serializedCUBE);
			CJPLogger::WriteLog(_JP_LOG_DEBUG, _JP_UNPACKING_CUBE);
		}
	}
}

/**
 * Stores the current CUBE object to the database
 */
function saveJPCUBE(){
	global $JPConfiguration, $CUBE;

	$CUBE->_storeCUBEArray();
	$serializedCUBE = serialize($CUBE);
	$JPConfiguration->WriteDebugVar('CUBEObject', $serializedCUBE, true);
	unset($serializedCUBE);
	unset($CUBE);
}

/**
 * Returns the current CUBE Array from the database
 */
function loadJPCUBEArray(){
	global $CUBE;
	$database = database::getInstance();

	// Search for CUBEObject entry in database
	$sql = 'SELECT COUNT(*) FROM #__jp_packvars WHERE `key`=\'CUBEArray\'';
	$database->setQuery($sql);
	$numRecords = $database->loadResult();

	if($numRecords < 1){
		if(is_object($CUBE)){
			$ret = $CUBE->_storeCUBEArray();
		} else{
			$ret = 'finale';
		}
	} else{
		$sql = 'SELECT `value2` FROM #__jp_packvars WHERE `key`=\'CUBEArray\'';
		$database->setQuery($sql);
		$serializedArray = $database->loadResult();
		$ret = unserialize($serializedArray);
		unset($serializedArray);
	}

	return $ret;
}

// Code to detect and log timeouts
function deadOnTimeOut(){
	if(connection_status() >= 2){
		CJPLogger::WriteLog(_JP_LOG_ERROR, _JP_TIMEOUT);
	}
}

register_shutdown_function('deadOnTimeOut');