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

global $option;

/**
 * CConfiguration is responsible for loading and saving configuration options
 * Configuration is rather sparse at the moment, but this will change with next versions. All
 * configuration values are saved to and retrieved from a PHP file, in the fashion Joomla does.
 * @package    JoomlaPacker
 * @author     Nicholas K. Dionysopoulos nikosdion@gmail.com
 * @copyright  2006 Nicholas K. Dionysopoulos
 * @license    http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @version    1.0
 * @since      File available since Release 1.0
 */
class CConfiguration{
	/**
	 * The directory used to output packed files. It is suggested to be outside the
	 * web root for security reasons.
	 * @var string
	 */
	var $OutputDirectory;
	/**
	 * The directory used to output temporary files. It is suggested to be outside the
	 * web root for security reasons.
	 * @var string
	 */
	var $TempDirectory;
	/**
	 * MySQL Export compatibility options
	 * @var string
	 */
	var $MySQLCompat;
	/**
	 * The absolute path to the directory Joomla! Pack is installed
	 * @var string
	 */
	var $_InstallationRoot;
	/**
	 * The template name for the archive file; three tags are recognized: [DATE], [TIME], [HOST]
	 * @access public
	 * @var string
	 */
	var $TarNameTemplate;
	/**
	 * Should we use compression or not?
	 * @access public
	 * @var boolean
	 */
	var $boolCompress;
	/**
	 * Algorithm for filelist creation
	 * @access public
	 * @var string
	 */
	var $fileListAlgorithm;
	/**
	 * Algorithm for db backup
	 * @access public
	 * @var string
	 */
	var $dbAlgorithm;
	/**
	 * Algorithm for file packing
	 * @access public
	 * @var string
	 */
	var $packAlgorithm;
	/**
	 * The absolute path to the configuration.php file
	 * @access private
	 * @var string
	 */
	var $_configurationFile;
	/**
	 * The level over which to log events in the log file
	 * @access private
	 * @var integer
	 */
	var $logLevel;
	/**
	 * Режим архивирования базы данных
	 * 0 - не архивировать
	 * 1 - tar.gz
	 * 2 - zip
	 **/
	var $sql_pack = 1;
	/**
	 * использование преффикса при дампе таблиц
	 **/
	var $sql_pref = 1;

	/**
	 * Initializer. Loads a set of default values that are good enough - but not secure enough -
	 * for most users.
	 */
	function CConfiguration(){
		global $option;

		// Private initializers
		$this->_InstallationRoot = _JLPATH_ADMINISTRATOR . "/";
		$this->_configurationFile = $this->_InstallationRoot . "/components/com_joomlapack/jpack.config.php";

		// Default configuration
		$this->TempDirectory = $this->_InstallationRoot . 'backups';
		$this->OutputDirectory = $this->_InstallationRoot . 'backups';
		$this->MySQLCompat = 'default';
		$this->boolCompress = 'zip';
		$this->TarNameTemplate = 'site-[HOST]-[DATE]-[TIME]';
		$this->fileListAlgorithm = 'smart';
		$this->dbAlgorithm = 'smart';
		$this->packAlgorithm = 'smart';
		//$this->InstallerPackage	= 'joostina.xml';
		//$this->AltInstaller = new CAltInstaller();
		//$this->AltInstaller->loadDefinition($this->InstallerPackage);
		$this->logLevel = _JP_LOG_WARNING;
		$this->sql_pack = 1; // по умолчанию сжимать в tar.gz
		$this->sql_pref = 1; // по умолчанию elfkznm ghtaabrc nf,kbw
	}

	/**
	 * получение конфигурации
	 * @return boolean
	 */
	function LoadConfiguration(){
		$fp = @fopen($this->_configurationFile, "r");
		if($fp === false){
			return false;
		}
		fclose($fp);
		require $this->_configurationFile;
		$this->OutputDirectory = $this->TranslateWinPath($jpConfig_OutputDirectory);
		$this->TempDirectory = $this->TranslateWinPath($jpConfig_OutputDirectory);
		$this->MySQLCompat = $jpConfig_MySQLCompat;
		$this->boolCompress = 'zip';
		$this->TarNameTemplate = $jpConfig_TarNameTemplate;
		$this->fileListAlgorithm = $jpConfig_fileListAlgorithm;
		$this->dbAlgorithm = $jpConfig_dbAlgorithm;
		$this->packAlgorithm = $jpConfig_packAlgorithm;
		$this->logLevel = $jpConfig_logLevel;
		$this->sql_pack = $jpConfig_sql_pack;
		$this->sql_pref = $jpConfig_sql_pref;
		return true;
	}

	/**
	 * Saves configuration to disk
	 * @return boolean
	 */
	function SaveConfiguration(){
		if(!$this->isConfigurationWriteable()){
			return false;
		}
		$config = "<?php\n";
		$config .= "defined( '_JLINDEX' ) or die();\n";
		$config .= '$jpConfig_OutputDirectory = \'' . addslashes($this->OutputDirectory) . "';\n";
		$config .= '$jpConfig_MySQLCompat = \'' . addslashes($this->MySQLCompat) . "';\n";
		$config .= '$jpConfig_boolCompress = "' . $this->boolCompress . "\";\n";
		$config .= '$jpConfig_TarNameTemplate = \'' . addslashes($this->TarNameTemplate) . "';\n";
		$config .= '$jpConfig_fileListAlgorithm = \'' . addslashes($this->fileListAlgorithm) . "';\n";
		$config .= '$jpConfig_dbAlgorithm = \'' . addslashes($this->dbAlgorithm) . "';\n";
		$config .= '$jpConfig_packAlgorithm = \'' . addslashes($this->packAlgorithm) . "';\n";
		$config .= '$jpConfig_logLevel = \'' . addslashes($this->logLevel) . "';\n";
		$config .= '$jpConfig_sql_pack = \'' . $this->sql_pack . "';\n";
		$config .= '$jpConfig_sql_pref = \'' . $this->sql_pref . "';\n";

		$config .= '?>';
		$fp = @fopen($this->_configurationFile, "w");
		if($fp === false){
			return false;
		}
		fputs($fp, $config);
		fclose($fp);
		return true;
	}

	/**
	 * Returns true if configuration.php is present
	 * @return boolean
	 */
	function hasConfiguration(){
		return file_exists($this->_configurationFile);
	}

	/**
	 * Returns true if configuration.php is present
	 * @return boolean
	 */
	function isConfigurationWriteable(){
		if($this->hasConfiguration()){
			return is_writable($this->_configurationFile);
		} else{
			return is_writable($this->_InstallationRoot);
		}
	}

	/**
	 * Returns true if the output target directory is writeable by the PHP script
	 * @return boolean
	 */
	function isOutputWriteable(){
		return is_writable($this->OutputDirectory);
	}

	/**
	 * Returns true if the temporary files directory is writeable by the PHP script
	 * @return boolean
	 */
	function isTempWriteable(){
		return is_writable($this->TempDirectory);
	}

	/**
	 * Writes a debug variable to the database (#__jp_packvars)
	 * @param string The name of the variable to write / update
	 * @param mixed The value of the variable to write / update
	 */
	function WriteDebugVar($varName, &$value, $boolLongText = false){
		$database = database::getInstance();

		$varName = $database->getEscaped($varName);
		$value = $database->getEscaped($value);

		// Kill exisiting variable (if any)
		$database->setQuery('DELETE FROM #__jp_packvars WHERE `key`="' . $varName . '"');
		$database->query();

		// Create variable
		if(!$boolLongText){
			$sql = 'INSERT INTO #__jp_packvars (`key`, value) VALUES ("' . $varName . '", "' . $value . '")';
		} else{
			$sql = 'INSERT INTO #__jp_packvars (`key`, value2) VALUES ("' . $varName . '", "' . $value . '")';
		}

		$database->setQuery($sql);
		$database->query();
	}

	/**
	 * Reads a debug variable out of #__jp_packvars
	 */
	function ReadDebugVar($key, $boolLongText = false){
		$database = database::getInstance();

		$key = $database->getEscaped($key);

		if(!$boolLongText){
			$sql = 'SELECT value FROM #__jp_packvars WHERE `key` = "' . $key . '"';
		} else{
			$sql = 'SELECT value2 FROM #__jp_packvars WHERE `key` = "' . $key . '"';
		}
		$database->setQuery($sql);
		$database->query();
		return $database->loadResult();
	}

	/**
	 * Deletes a debug variable from #__jp_packvars
	 */
	function DeleteDebugVar($key){
		$database = database::getInstance();

		$key = $database->getEscaped($key);

		$sql = 'DELETE FROM #__jp_packvars WHERE `key` = "' . $key . '"';
		$database->setQuery($sql);
		$database->query();
	}

	// работа с Windows системами
	function TranslateWinPath($p_path){
		if(stristr(php_uname(), 'windows')){
			if((strpos($p_path, '\\') > 0) || (substr($p_path, 0, 1) == '\\')){
				$p_path = strtr($p_path, '\\', '/');
			}
		}
		return $p_path;
	}


}


// Log levels
define('_JP_LOG_ERROR', 1);
define('_JP_LOG_WARNING', 2);
define('_JP_LOG_INFO', 3);
define('_JP_LOG_DEBUG', 4);

class CJPLogger{
	/**
	 * Clears the logfile
	 */
	public static function ResetLog(){
		$logName = CJPLogger::logName();
		@unlink($logName);
		touch($logName);
	}

	/**
	 * Writes a line to the log, if the log level is high enough
	 * @param integer $level The log level (_JP_LOG_XXXXX constants)
	 * @param string  $message The message to write to the log
	 */
	public static function WriteLog($level, $message){
		global $JPConfiguration;

		if($JPConfiguration->logLevel >= $level){
			$logName = CJPLogger::logName();
			$message = str_replace(JPATH_BASE, '<root>', $message);
			switch($level){
				case _JP_LOG_ERROR:
					$string = 'ERROR   |';
					break;
				case _JP_LOG_WARNING:
					$string = 'WARNING |';
					break;
				case _JP_LOG_INFO:
					$string = 'INFO    |';
					break;
				default:
					$string = 'DEBUG   |';
					break;
			}
			$string .= strftime('%y%m%d %R') . '|' . $message . "\n";
			$fp = fopen($logName, 'at');
			if(!($fp === false)){
				fwrite($fp, $string);
				fclose($fp);
			}
		}
	}

	/**
	 * Parses the log file and outputs formatted HTML to the standard output
	 */
	public static function VisualizeLogDirect(){
		$logName = CJPLogger::logName();
		if(!file_exists($logName)) return false; //joostina pach
		$fp = fopen($logName, "rt");
		if($fp === false) return false;

		echo '<p style="font-family: vardana, monospace; text-align: left; font-size: 9px;">';
		while(!feof($fp)){
			$line = fgets($fp);
			if(!$line) return;
			$exploded = explode("|", $line, 3);
			unset($line);
			switch(trim($exploded[0])){
				case 'ERROR':
					$fmtString = '<span style="color: red; font-weight: bold;">[';
					break;
				case 'WARNING':
					$fmtString = '<span style="color: #D8AD00; font-weight: bold;">[';
					break;
				case 'INFO':
					$fmtString = '<span style="color: black;\'>[';
					break;
				case 'DEBUG':
					$fmtString = '<span style="color: #666666; font-size: small;">[';
					break;
				default:
					$fmtString = '<span style="font-size: small;">[';
					break;
			}
			$fmtString .= $exploded[1] . '] ' . htmlspecialchars($exploded[2]) . '</span><br/>' . "\n";
			unset($exploded);
			echo $fmtString;
			unset($fmtString);
		}
		echo '</p>';
		ob_flush();
	}

	/**
	 * Calculates the absolute path to the log file
	 */
	public static function logName(){
		global $JPConfiguration;
		return $JPConfiguration->TranslateWinPath($JPConfiguration->OutputDirectory . '/joomlapack.log');
	}
}


class CAltInstaller{
	/**
	@var string Short name of the installer*/
	var $Name;

	/**
	@var string Package file, wihout path*/
	var $Package;

	/**
	@var string List of installer files*/
	var $fileList;

	/**
	@var string Dump mode for the SQL data (split, one)*/
	var $SQLDumpMode;

	/**
	@var string Filename of the unified or table definition dump, relative to installer root*/
	var $BaseDump;

	/**
	@var string Filename of the data dump, relative to installer root*/
	var $SampleDump;

	/**
	 * Loads a definition file.
	 * @param string The name of the file you want to load. Relative to 'installers' directory.
	 * @return boolean True if loaded successful the file
	 */
	function loadDefinition($file){
		global $option;
		require_once (JPATH_BASE . '/includes/domit/xml_domit_lite_include.php');
		// Instanciate new parser object
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors(true);
		if(!$xmlDoc->loadXML(_JLPATH_ADMINISTRATOR . '/components/com_joomlapack/installers/' . $file, false, true)){
			return false;
		}
		$root = &$xmlDoc->documentElement;
		// Check if it is a valid description file
		if($root->getTagName() != 'jpconfig'){
			return false;
		} elseif($root->getAttribute('type') != 'installpack'){
			return false;
		}

		// Set basic elements
		$e = &$root->getElementsByPath('name', 1);
		$this->Name = $e->getText();
		$e = &$root->getElementsByPath('package', 1);
		$this->Package = $e->getText();
		$sqlDumpRoot = &$root->getElementsByPath('sqldump', 1);
		$this->SQLDumpMode = &$sqlDumpRoot->getAttribute("mode");

		// Get SQL filenames
		if($sqlDumpRoot->hasChildNodes()){
			$e = $sqlDumpRoot->getElementsByPath('basedump', 1);
			if(!is_null($e)){
				$this->BaseDump = $e->getText();
			} else{
				$this->BaseDump = '';
			}

			$e = $sqlDumpRoot->getElementsByPath('sampledump', 1);
			if(!is_null($e)){
				$this->SampleDump = $e->getText();
			} else{
				$this->SampleDump = '';
			}
		}

		// Get file list
		$this->fileList = array();
		$flRoot = &$root->getElementsByPath('filelist', 1);
		if(!is_null($flRoot)){
			if($flRoot->hasChildNodes()){
				$files = $flRoot->childNodes;
				foreach($files as $file){
					$this->fileList[] = $file->getText();
				}
			}
		}

		return true;
	}

	/**
	 * Loads all installer definition files
	 * @return array An array of the installer names and packages
	 */
	function loadAllDefinitions(){
		global $option;
		require_once 'engine.abstraction.php';
		$FS = new CFSAbstraction;
		$defs = array();
		$fileList = $FS->getDirContents(_JLPATH_ADMINISTRATOR . '/components/com_joomlapack/installers/', '*.xml');
		foreach($fileList as $fileDef){
			$file = $fileDef['name'];
			$baseName = basename($file);
			if($this->loadDefinition($baseName)){
				$newDef['name'] = $this->Name;
				$newDef['package'] = $this->Package;
				$newDef['meta'] = $baseName;
				$defs[] = $newDef;
			}
		}

		return $defs;
	}
}

$JPConfiguration = new CConfiguration;
if($JPConfiguration->hasConfiguration()){
	$JPConfiguration->LoadConfiguration();
}