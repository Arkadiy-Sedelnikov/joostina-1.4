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
 * Module installer
 * @package Joostina
 * @subpackage Installer
 */
class mosInstallerMambot extends mosInstaller{

	function __construct($pre_installer){
		// Copy data
		$this->i_installfilename = $pre_installer->i_installfilename;
		$this->i_installarchive = $pre_installer->i_installarchive;
		$this->i_installdir = $pre_installer->i_installdir;
		$this->i_iswin = $pre_installer->i_iswin;
		$this->i_errno = $pre_installer->i_errno;
		$this->i_error = $pre_installer->i_error;
		$this->i_installtype = $pre_installer->i_installtype;
		$this->i_unpackdir = $pre_installer->i_unpackdir;
		$this->i_docleanup = $pre_installer->i_docleanup;
		$this->i_elementdir = $pre_installer->i_elementdir;
		$this->i_elementname = $pre_installer->i_elementname;
		$this->i_elementspecial = $pre_installer->i_elementspecial;
		$this->i_xmldoc = $pre_installer->i_xmldoc;
		$this->i_hasinstallfile = $pre_installer->i_hasinstallfile;
		$this->i_installfile = $pre_installer->i_installfile;
	}

	/**
	 * Custom install method
	 * @param boolean True if installing from directory
	 */
	function install($p_fromdir = null){
		$database = database::getInstance();

		if(!$this->preInstallCheck($p_fromdir, 'mambot')){
			return false;
		}

		$xmlDoc = $this->xmlDoc();
		$mosinstall = &$xmlDoc->documentElement;

		// Set some vars
		$e = &$mosinstall->getElementsByPath('name', 1);
		$this->elementName($e->getText());

		$folder = $mosinstall->getAttribute('group');
		$this->elementDir(mosPathName(JPATH_BASE . DS . 'mambots' . DS . $folder));

		if(!file_exists($this->elementDir()) && !mosMakePath($this->elementDir())){
			$this->setError(1, _CANNOT_CREATE_DIR . ' "' . $this->elementDir() . '"');
			return false;
		}

		if($this->parseFiles('files', 'mambot', _NO_FILES_OF_MAMBOTS) === false){
			$this->cleanAfterError();
			return false;
		}

		// Are there any SQL queries??
		$query_element = &$mosinstall->getElementsByPath('install/queries', 1);
		if(!is_null($query_element)){
			$queries = $query_element->childNodes;
			foreach($queries as $query){
				// проверяем на наличие в запросе команды задания кодировки таблицы, и если она авно не указана
				$sql = $query->getText();
				// строки явно указывающие кодировку создаваемой таблицы
				$d = strpos($sql, 'DEFAULT');
				$c = strpos($sql, 'CHARSET');
				// если эти слова есть в запросе - то идёт создание таблицы
				$r = strpos($sql, 'CREATE');
				$t = strpos($sql, 'TABLE');
				// если в запросе нет указания кодировки, но есть явные команды создания таблиц, а база работает в режиме совместимости со старшими версиями MySQL - добавим определение кодировки
				if((!$d) && (!$c) && ($r) && ($t)){
					$sql = str_replace(';', '', $sql);
					$sql .= ' CHARACTER SET utf8 COLLATE utf8_general_ci;';
				}

				$database->setQuery($sql);
				if(!$database->query()){
					$this->setError(1, _SQL_ERROR . ": " . $database->getEscaped($sql) . ".<br /> " . _ERROR_MESSAGE . ":" . $database->stderr(true));
					return false;
				}
				unset($sql);
			}
		}

		// Insert mambot in DB
		$query = "SELECT id FROM #__mambots WHERE element = " . $database->Quote($this->elementName());
		$database->setQuery($query);
		if(!$database->query()){
			$this->setError(1, _SQL_ERROR . ': ' . $database->stderr(true));
			return false;
		}

		$id = $database->loadResult();

		if(!$id){
			$row = new mosMambot($database);
			$row->name = $this->elementName();
			$row->ordering = 0;
			$row->folder = $folder;
			$row->iscore = 0;
			$row->access = 0;
			$row->client_id = 0;
			$row->element = $this->elementSpecial();

			if($folder == 'editors'){
				$row->published = 1;
			}

			if(!$row->store()){
				$this->setError(1, _SQL_ERROR . ': ' . $row->getError());
				return false;
			}
		} else{
			$this->setError(1, sprintf(_COM_INSTALLER_MAMBOT_EXIST, $this->elementName()));
			return false;
		}
		if($e = &$mosinstall->getElementsByPath('description', 1)){
			$this->setError(0, $this->elementName() . '<p>' . $e->getText() . '</p>');
		}

		return $this->copySetupFile('front');
	}

	/**
	 * Custom install method
	 * @param int The id of the module
	 * @param string The URL option
	 * @param int The client id
	 */
	function uninstall($id, $option, $client = 0){
		$database = database::getInstance();

		$id = intval($id);
		$query = "SELECT name, folder, element, iscore FROM #__mambots WHERE id = " . (int)
		$id;
		$database->setQuery($query);

		$row = null;
		$database->loadObject($row);
		if($database->getErrorNum()){
			HTML_installer::showInstallMessage($database->stderr(), _UNINSTALL_ERROR, $this->returnTo($option, 'mambot', $client));
			exit();
		}
		if($row == null){
			HTML_installer::showInstallMessage(_WRONG_ID, _UNINSTALL_ERROR, $this->returnTo($option, 'mambot', $client));
			exit();
		}

		if(trim($row->folder) == ''){
			HTML_installer::showInstallMessage(_BAD_DIR_NAME_EMPTY, _UNINSTALL_ERROR, $this->returnTo($option, 'mambot', $client));
			exit();
		}

		$basepath = JPATH_BASE . DS . 'mambots' . DS . $row->folder . DS;
		$xmlfile = $basepath . $row->element . '.xml';

		// see if there is an xml install file, must be same name as element
		if(file_exists($xmlfile)){
			$this->i_xmldoc = new DOMIT_Lite_Document();
			$this->i_xmldoc->resolveErrors(true);

			if($this->i_xmldoc->loadXML($xmlfile, false, true)){
				$mosinstall = &$this->i_xmldoc->documentElement;

				// Are there any SQL queries??
				$query_element = &$mosinstall->getElementsbyPath('uninstall/queries', 1);
				if(!is_null($query_element)){
					$queries = $query_element->childNodes;
					foreach($queries as $query){
						$database->setQuery($query->getText());
						if(!$database->query()){
							HTML_installer::showInstallMessage($database->stderr(true), _UNINSTALL_ERROR, $this->returnTo($option, 'component', $client));
							exit();
						}
					}
				}

				// get the files element
				$files_element = &$mosinstall->getElementsByPath('files', 1);
				if(!is_null($files_element)){
					$files = $files_element->childNodes;
					foreach($files as $file){
						// delete the files
						$filename = $file->getText();
						if(file_exists($basepath . $filename)){
							$parts = pathinfo($filename);
							$subpath = $parts['dirname'];
							if($subpath != '' && $subpath != '.' && $subpath != '..'){
								echo '<br />' . _DELETING . ': ' . $basepath . $subpath;
								$result = deldir(mosPathName($basepath . $subpath . '/'));
							} else{
								echo '<br />' . _DELETING . ': ' . $basepath . $filename;
								$result = unlink(mosPathName($basepath . $filename, false));
							}
							echo intval($result);
						}
					}

					@unlink(mosPathName($xmlfile, false));

					// define folders that should not be removed
					$sysFolders = array('content', 'search');
					if(!in_array($row->folder, $sysFolders)){
						// delete the non-system folders if empty
						if(count(mosReadDirectory($basepath)) < 1){
							deldir($basepath);
						}
					}
				}
			}
		}

		if($row->iscore){
			HTML_installer::showInstallMessage($row->name . ' - ' . _IS_PART_OF_CMS, _UNINSTALL_ERROR, $this->returnTo($option, 'mambot', $client));
			exit();
		}

		$query = "DELETE FROM #__mambots WHERE id = " . (int)$id;
		$database->setQuery($query);
		if(!$database->query()){
			$msg = $database->stderr;
			die($msg);
		}
		return true;
	}

	/**
	 * Uninstall method
	 */
	function cleanAfterError(){
		josSpoofCheck();

		$basepath = $this->elementDir();
		$mosinstall = &$this->i_xmldoc->documentElement;
		// get the files element
		$files_element = &$mosinstall->getElementsByPath('files', 1);

		// Search the install dir for an xml file
		$files = mosReadDirectory($this->installDir(), '.xml$', true, true);

		if(count($files) > 0){
			foreach($files as $file){
				$packagefile = $this->isPackageFile($file);
				if(!is_null($packagefile)){
					$xmlfilename = $file;
				}
			}
		}
		if($this->isWindows()){
			$elementName = substr(substr(strrchr($xmlfilename, '\\'), 1), 0, -4);
		} else{
			$elementName = substr(substr(strrchr($xmlfilename, '/'), 1), 0, -4);
		}

		if(!is_null($files_element)){
			$files = $files_element->childNodes;
			foreach($files as $file){
				// delete the files
				$filename = $file->getText();
				if(file_exists($basepath . $filename)){
					$parts = pathinfo($filename);
					$subpath = $parts['dirname'];
					if($subpath != '' && $subpath != '.' && $subpath != '..'){
						$result = deldir(mosPathName($basepath . $subpath . '/'));
					} else{
						$result = unlink(mosPathName($basepath . $filename, false));
					}
				}
			}
			// remove XML file from front
			@unlink(mosPathName($xmlfilename, false));
			if(file_exists($basepath . $elementName)){
				deldir($basepath . $elementName . '/');
			}
			return true;
		}
	}
}