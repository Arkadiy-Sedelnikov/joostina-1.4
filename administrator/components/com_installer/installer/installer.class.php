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

/**
 * Installer class
 * @package Joostina
 * @subpackage Installer
 * @abstract
 */
class mosInstaller{

	// name of the XML file with installation information
	var $i_installfilename = "";
	var $i_installarchive = "";
	var $i_installdir = "";
	var $i_iswin = false;
	var $i_errno = 0;
	var $i_error = "";
	var $i_installtype = "";
	var $i_unpackdir = "";
	var $i_docleanup = true;
	/**
	@var string The directory where the element is to be installed */
	var $i_elementdir = '';
	/**
	@var string The name of the Joomla! element */
	var $i_elementname = '';
	/**
	@var string The name of a special atttibute in a tag */
	var $i_elementspecial = '';
	/**
	@var object A DOMIT XML document */
	var $i_xmldoc = null;
	var $i_hasinstallfile = null;
	var $i_installfile = null;

	/**
	 * Constructor
	 */
	function mosInstaller(){
		$this->i_iswin = (substr(PHP_OS, 0, 3) == 'WIN');
	}

	/**
	 * Gets a file name out of a url
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @param string $url URL to get name from
	 * @return mixed String filename or boolean false if failed
	 * @since 1.3
	 */
	function getFilenameFromURL($url){
		if(is_string($url)){
			$parts = explode('/', $url);
			return $parts[count($parts) - 1];
		}
		return false;
	}

	/**
	 * Downloads a package
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @param string URL of file to download
	 * @param string Download target filename [optional]
	 * @return mixed Path to downloaded package or boolean false on failure
	 * @since 1.3
	 */
	function downloadPackage($url, $target = false){
		$base_Dir = mosPathName(JPATH_BASE . DS . 'media');

		// Capture PHP errors
		$php_errormsg = 'Error Unknown';
		ini_set('track_errors', true);

		// Set user agent
		ini_set('user_agent', "Joostina Installer");

		// Open the remote server socket for reading
		$inputHandle = @ fopen($url, "r");
		$error = strstr($php_errormsg, 'failed to open stream:');

		if(!$inputHandle){
			$this->setError(1, _CANNOT_CONNECT_SERVER);
			return false;
		}

		$meta_data = stream_get_meta_data($inputHandle);
		foreach($meta_data['wrapper_data'] as $wrapper_data){
			if(substr($wrapper_data, 0, strlen("Content-Disposition")) == "Content-Disposition"){
				$contentfilename = explode("\"", $wrapper_data);
				$target = $contentfilename[1];
			}
		}

		// Set the target path if not given
		if(!$target){
			$target = $base_Dir . mosInstaller::getFilenameFromURL($url);
		} else{
			$target = $base_Dir . basename($target);
		}

		// Initialize contents buffer
		$contents = null;

		while(!feof($inputHandle)){
			$contents .= fread($inputHandle, 4096);
			if($contents == false){
				$this->setError(44, 'Failed reading network resource: ' . $php_errormsg);
				return false;
			}
		}

		// Write buffer to file
		$ret = file_put_contents($target, $contents);

		// Close file pointer resource
		fclose($inputHandle);

		// Return the name of the downloaded package
		return basename($target);
	}

	/**
	 * Uploads and unpacks a file
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @param string The uploaded package filename or install directory
	 * @param boolean True if the file is an archive file
	 * @return boolean True on success, False on error
	 */
	function upload($p_filename = null, $p_unpack = true){
		$this->i_iswin = (substr(PHP_OS, 0, 3) == 'WIN');
		$this->installArchive($p_filename);

		if($p_unpack){
			if($this->extractArchive()){
				return $this->findInstallFile();
			} else{
				return false;
			}
		}
	}

	/**
	 * Extracts the package archive file
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @return boolean True on success, False on error
	 */
	function extractArchive(){

		$base_Dir = mosPathName(JPATH_BASE . DS . 'media');

		$archivename = $base_Dir . $this->installArchive();
		$tmpdir = uniqid('install_');

		$extractdir = mosPathName($base_Dir . $tmpdir);
		$archivename = mosPathName($archivename, false);

		$this->unpackDir($extractdir);

		if(preg_match('/.zip$/', $archivename)){
			// Extract functions
			require_once (_JLPATH_ADMINISTRATOR . '/includes/pcl/pclzip.lib.php');
			require_once (_JLPATH_ADMINISTRATOR . '/includes/pcl/pclerror.lib.php');
			$zipfile = new PclZip($archivename);
			if($this->isWindows()){
				define('OS_WINDOWS', 1);
			} else{
				define('OS_WINDOWS', 0);
			}

			$ret = $zipfile->extract(PCLZIP_OPT_PATH, $extractdir);
			if($ret == 0){
				$this->setError(1, _PCLZIP_UNKNOWN_ERROR . ' "' . $zipfile->errorName(true) . '"');
				return false;
			}
		} else{
			require_once (JPATH_BASE . '/includes/Archive/Tar.php');
			$archive = new Archive_Tar($archivename);
			$archive->setErrorHandling(PEAR_ERROR_PRINT);

			if(!$archive->extractModify($extractdir, '')){
				$this->setError(1, 'Extract Error');
				return false;
			}
		}

		$this->installDir($extractdir);

		// Try to find the correct install dir. in case that the package have subdirs
		// Save the install dir for later cleanup
		$filesindir = mosReadDirectory($this->installDir(), '');

		if(count($filesindir) == 1){
			if(is_dir($extractdir . $filesindir[0])){
				$this->installDir(mosPathName($extractdir . $filesindir[0]));
			}
		}
		return true;
	}

	/**
	 * Tries to find the package XML file
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @return boolean True on success, False on error
	 */
	function findInstallFile(){
		$found = false;

		// Search the install dir for an xml file
		$files = mosReadDirectory($this->installDir(), '.xml$', true, true);

		if(count($files) > 0){
			foreach($files as $file){
				$packagefile = $this->isPackageFile($file);
				if(!is_null($packagefile) && !$found){
					$this->xmlDoc($packagefile);
					return true;
				}
			}
			$this->setError(1, _ERROR_NO_XML_JOOMLA);
			return false;
		} else{
			$this->setError(1, _ERROR_NO_XML_INSTALL);
			return false;
		}
	}

	/**
	 * @Returns type of the extension
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @return type
	 */
	function getInstallType(){
		return $this->i_installtype;
	}

	/**
	 * @param string A file path
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @return object A DOMIT XML document, or null if the file failed to parse
	 */
	function isPackageFile($p_file){
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors(true);

		if(!$xmlDoc->loadXML($p_file, false, true)){
			return null;
		}
		$root = &$xmlDoc->documentElement;

		if($root->getTagName() != 'mosinstall'){
			return null;
		}
		// Set the type
		$this->installType($root->getAttribute('type'));
		$this->installFilename($p_file);
		return $xmlDoc;
	}

	/**
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * Loads and parses the XML setup file
	 * @return boolean True on success, False on error
	 */
	function readInstallFile(){
		if($this->installFilename() == ""){
			$this->setError(1, _NO_NAME_DEFINED);
			return false;
		}

		$this->i_xmldoc = new DOMIT_Lite_Document();
		$this->i_xmldoc->resolveErrors(true);
		if(!$this->i_xmldoc->loadXML($this->installFilename(), false, true)){
			return false;
		}
		$root = &$this->i_xmldoc->documentElement;

		// Check that it's an installation file
		if($root->getTagName() != 'mosinstall'){
			$this->setError(1, _FILE . ':"' . $root->getTagName() . '" - ' . _NOT_CORRECT_INSTALL_FILE_FOR_JOOMLA);
			return false;
		}

		$this->installType($root->getAttribute('type'));
		return true;
	}

	/**
	 * Abstract install method
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 */
	function install(){
		die(_CANNOT_RUN_INSTALL_METHOD . ' ' . strtolower(get_class($this)));
	}

	/**
	 * Abstract uninstall method
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 */
	function uninstall($cid, $option, $client = 0){
		die(_CANNOT_RUN_UNINSTALL_METHOD . ' ' . strtolower(get_class($this)));
	}

	/**
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * return to method
	 */
	function returnTo($option, $element){
		return "index2.php?option=$option&element=$element";
	}

	/**
	 * Sets some necessary data before installation
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @param string Install from directory
	 * @return boolean
	 */
	function preInstallSetting($p_fromdir){

		if(!is_null($p_fromdir)){
			$this->installDir($p_fromdir);
		}

		if(!$this->installfile()){
			$this->findInstallFile();
		}

		if(!$this->readInstallFile()){
			$this->setError(1, _CANNOT_FIND_INSTALL_FILE . ':<br />' . $this->installDir());
			return false;
		}

		$this->installType();

		// In case there where an error doring reading or extracting the archive
		if($this->errno()){
			return false;
		}

		return true;
	}

	/**
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @param string Install from directory
	 * @param string The install type
	 * @return boolean
	 */
	function preInstallCheck($p_fromdir, $type){

		if(!is_null($p_fromdir)){
			$this->installDir($p_fromdir);
		}

		if(!$this->installfile()){
			$this->findInstallFile();
		}

		if(!$this->readInstallFile()){
			$this->setError(1, _CANNOT_FIND_INSTALL_FILE . ':<br />' . $this->installDir());
			return false;
		}

		if($this->installType() != $type){
			$this->setError(1, _XML_NOT_FOR . ' "' . $type . '".');
			return false;
		}

		// In case there where an error doring reading or extracting the archive
		if($this->errno()){
			return false;
		}

		return true;
	}

	/**
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @param string The tag name to parse
	 * @param string An attribute to search for in a filename element
	 * @param string The value of the 'special' element if found
	 * @param boolean True for Administrator components
	 * @return mixed Number of file or False on error
	 */
	function parseFiles($tagName = 'files', $special = '', $specialError = '', $adminFiles = 0){
		// Find files to copy
		$xmlDoc = &$this->xmlDoc();
		$root = &$xmlDoc->documentElement;

		$files_element = &$root->getElementsByPath($tagName, 1);
		if(is_null($files_element)){
			return 0;
		}

		if(!$files_element->hasChildNodes()){
			// no files
			return 0;
		}
		$files = $files_element->childNodes;
		$copyfiles = array();
		if(count($files) == 0){
			// nothing more to do
			return 0;
		}

		if($folder = $files_element->getAttribute('folder')){
			$temp = mosPathName($this->unpackDir() . $folder);
			if($temp == $this->installDir()){
				// this must be only an admin component
				$installFrom = $this->installDir();
			} else{
				$installFrom = mosPathName($this->installDir() . $folder);
			}
		} else{
			$installFrom = $this->installDir();
		}

		foreach($files as $file){
			if(basename($file->getText()) != $file->getText()){
				$newdir = dirname($file->getText());

				if($adminFiles){
					if(!mosMakePath($this->componentAdminDir(), $newdir)){
						$this->setError(1, _CANNOT_CREATE_DIR . ' "' . ($this->componentAdminDir()) . $newdir . '"');
						return false;
					}
				} else{
					if(!mosMakePath($this->elementDir(), $newdir)){
						$this->setError(1, _CANNOT_CREATE_DIR . ' "' . ($this->elementDir()) . $newdir . '"');
						return false;
					}
				}
			}
			$copyfiles[] = $file->getText();

			// check special for attribute
			if($file->getAttribute($special)){
				$this->elementSpecial($file->getAttribute($special));
			}
		}

		if($specialError){
			if($this->elementSpecial() == ''){
				$this->setError(1, $specialError);
				return false;
			}
		}

		if($tagName == 'media'){
			$installTo = mosPathName(JPATH_BASE . '/images/stories');
		} elseif($tagName == 'languages'){
			foreach($files as $file){
				$lang_path = $file->getAttribute('folder');
				$installTo = mosPathName(JPATH_BASE . DS . $lang_path);
				$fil = $file->getText();
				$file_path = dirname($fil);
				$file_name = basename($fil);
				$result = $this->copyFiles($installFrom . $file_path . DS, $installTo, array($file_name));
			}
			return $result;
		} elseif($adminFiles){
			$installTo = $this->componentAdminDir();
		} else{
			$installTo = $this->elementDir();
		}
		$result = $this->copyFiles($installFrom, $installTo, $copyfiles);

		return $result;
	}

	/**
	 * @param string Source directory
	 * @param string Destination directory
	 * @param array array with filenames
	 * @param boolean True is existing files can be replaced
	 * @return boolean True on success, False on error
	 */
	function copyFiles($p_sourcedir, $p_destdir, $p_files, $overwrite = false){
		if(is_array($p_files) && count($p_files) > 0){
			foreach($p_files as $_file){
				$filesource = mosPathName(mosPathName($p_sourcedir) . $_file, false);
				$filedest = mosPathName(mosPathName($p_destdir) . $_file, false);
				if(!file_exists($filesource)){
					$this->setError(1, _FILE_NOT_EXISTSS . " $filesource");
					return false;
				} else
					if(file_exists($filedest) && !$overwrite){
						$this->setError(1, '' . " $filedest - " . _INSTALL_TWICE);
						return false;
					} else{
						$path_info = pathinfo($_file);
						if(!is_dir($path_info['dirname'])){
							mosMakePath($p_destdir, $path_info['dirname']);
						}
						if(!(copy($filesource, $filedest) && mosChmod($filedest))){
							$this->setError(1, _ERROR_COPYING_FILE . ": $filesource в $filedest");
							return false;
						}
					}
			}
		} else{
			return false;
		}
		return count($p_files);
	}

	/**
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * Copies the XML setup file to the element Admin directory
	 * Used by Components/Modules/Mambot Installer
	 * @return boolean True on success, False on error
	 */
	function copySetupFile($where = 'admin'){
		if($where == 'admin'){
			return $this->copyFiles($this->installDir(), $this->componentAdminDir(), array(basename
			($this->installFilename())), true);
		} else
			if($where == 'front'){
				return $this->copyFiles($this->installDir(), $this->elementDir(), array(basename($this->installFilename())), true);
			}
	}

	/**
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @param int The error number
	 * @param string The error message
	 */
	function setError($p_errno, $p_error){
		$this->errno($p_errno);
		$this->error($p_error);
	}

	/**
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @param boolean True to display both number and message
	 * @param string The error message
	 * @return string
	 */
	function getError($p_full = false){
		if($p_full){
			return $this->errno() . " " . $this->error();
		} else{
			return $this->error();
		}
	}

	/**
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @param string The name of the property to set/get
	 * @param mixed The value of the property to set
	 * @return The value of the property
	 */
	function &setVar($name, $value = null){
		if(!is_null($value)){
			$this->$name = $value;
		}
		return $this->$name;
	}

	function installFilename($p_filename = null){
		if(!is_null($p_filename)){
			if($this->isWindows()){
				$this->setVar('i_installfilename', str_replace('/', '\\', $p_filename));
			} else{
				$this->setVar('i_installfilename', str_replace('\\', '/', $p_filename));
			}
		}
		return $this->i_installfilename;
	}

	function installType($p_installtype = null){
		return $this->setVar('i_installtype', $p_installtype);
	}

	function error($p_error = null){
		return $this->setVar('i_error', $p_error);
	}

	function &xmlDoc($p_xmldoc = null){
		return $this->setVar('i_xmldoc', $p_xmldoc);
	}

	function installArchive($p_filename = null){
		return $this->setVar('i_installarchive', $p_filename);
	}

	function installDir($p_dirname = null){
		return $this->setVar('i_installdir', $p_dirname);
	}

	function unpackDir($p_dirname = null){
		return $this->setVar('i_unpackdir', $p_dirname);
	}

	function isWindows(){
		return $this->i_iswin;
	}

	function errno($p_errno = null){
		return $this->setVar('i_errno', $p_errno);
	}

	function hasInstallfile($p_hasinstallfile = null){
		return $this->setVar('i_hasinstallfile', $p_hasinstallfile);
	}

	function installfile($p_installfile = null){
		return $this->setVar('i_installfile', $p_installfile);
	}

	function elementDir($p_dirname = null){
		return $this->setVar('i_elementdir', $p_dirname);
	}

	function elementName($p_name = null){
		return $this->setVar('i_elementname', $p_name);
	}

	function elementSpecial($p_name = null){
		return $this->setVar('i_elementspecial', $p_name);
	}

}

function cleanupInstall($userfile_name, $resultdir){
	if(file_exists($resultdir)){
		deldir($resultdir);
		unlink(mosPathName(JPATH_BASE . '/media/' . $userfile_name, false));
	}
}

function deldir($dir){
	$current_dir = opendir($dir);
	$old_umask = umask(0);
	while($entryname = readdir($current_dir)){
		if($entryname != '.' and $entryname != '..'){
			if(is_dir($dir . $entryname)){
				deldir(mosPathName($dir . $entryname));
			} else{
				@chmod($dir . $entryname, 0777);
				unlink($dir . $entryname);
			}
		}
	}
	umask($old_umask);
	closedir($current_dir);
	return rmdir($dir);
}