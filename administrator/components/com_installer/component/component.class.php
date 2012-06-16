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

/**
 * Component installer
 * @package Joostina
 * @subpackage Installer
 */
class mosInstallerComponent extends mosInstaller{
	var $i_componentadmindir = '';
	var $i_hasinstallfile = false;
	var $i_installfile = '';

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
	@var string The directory where the element is to be installed*/
	var $i_elementdir = '';
	/**
	@var string The name of the Joomla! element*/
	var $i_elementname = '';
	/**
	@var string The name of a special atttibute in a tag*/
	var $i_elementspecial = '';
	/**
	@var object A DOMIT XML document*/
	var $i_xmldoc = null;

	function __construct($pre_installer = null){
		if(!isset($pre_installer)) return;

		// Copy data  from the base class
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
	}

	function componentAdminDir($p_dirname = null){
		if(!is_null($p_dirname)){
			$this->i_componentadmindir = mosPathName($p_dirname);
		}
		return $this->i_componentadmindir;
	}

	/**
	 * Custom install method
	 * @package Joostina
	 * @subpackage Installer
	 * @abstract
	 * @param boolean True if installing from directory
	 */
	function install($p_fromdir = null){
		josSpoofCheck();

		$database = database::getInstance();
		$config = Jconfig::getInstance();

		if(!$this->preInstallCheck($p_fromdir, 'component')){
			return false;
		}

		// aje moved down to here. ??  seemed to be some referencing problems
		$xmlDoc = $this->xmlDoc();
		$mosinstall = $xmlDoc->documentElement;

		// Set some vars
		$e = $mosinstall->getElementsByPath('name', 1);
		$this->elementName($e->getText());
		$this->elementDir(mosPathName(JPATH_BASE . DS . 'components' . DS . strtolower("com_" . str_replace(" ", "", $this->elementName())) . DS));
		$this->componentAdminDir(mosPathName(JPATH_BASE_ADMIN . DS . 'components' . DS . strtolower('com_' . str_replace(' ', '', $this->elementName()))));

		if(file_exists($this->elementDir())){
			$this->setError(1, _OTHER_COMPONENT_USE_DIR . ': "' . $this->elementDir() . '"');
			return false;
		}

		if(!file_exists($this->elementDir()) && !mosMakePath($this->elementDir())){
			$this->setError(1, _CANNOT_CREATE_DIR . ' "' . $this->elementDir() . '"');
			return false;
		}

		if(!file_exists($this->componentAdminDir()) && !mosMakePath($this->componentAdminDir())){
			$this->setError(1, _CANNOT_CREATE_DIR . ' "' . $this->componentAdminDir() . '"');
			return false;
		}

		// Find files to copy
		if($this->parseFiles('files') === false){
			$this->cleanAfterError();
			return false;
		}
		$this->parseFiles('images');
		$this->parseFiles('languages');

		if($this->parseFiles('administration/files', '', '', 1) === false){
			$this->cleanAfterError();
			return false;
		}
		$this->parseFiles('administration/images', '', '', 1);

		// Are there any SQL queries??
		$query_element = $mosinstall->getElementsByPath('install/queries', 1);
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

		// Is there an installfile
		$installfile_elemet = $mosinstall->getElementsByPath('installfile', 1);

		if(!is_null($installfile_elemet)){
			// check if parse files has already copied the install.component.php file (error in 3rd party xml's!)
			if(!file_exists($this->componentAdminDir() . $installfile_elemet->getText())){
				if(!$this->copyFiles($this->installDir(), $this->componentAdminDir(), array($installfile_elemet->getText()))){
					$this->setError(1, _CANNOT_COPY_PHP_INSTALL);
					return false;
				}
			}
			$this->hasInstallfile(true);
			$this->installFile($installfile_elemet->getText());
		}
		// Is there an uninstallfile
		$uninstallfile_elemet = $mosinstall->getElementsByPath('uninstallfile', 1);
		if(!is_null($uninstallfile_elemet)){
			if(!file_exists($this->componentAdminDir() . $uninstallfile_elemet->getText())){
				if(!$this->copyFiles($this->installDir(), $this->componentAdminDir(), array($uninstallfile_elemet->getText()))){
					$this->setError(1, _CANNOT_COPY_PHP_REMOVE);
					return false;
				}
			}
		}

		// Is the menues ?
		$adminmenu_element = $mosinstall->getElementsByPath('administration/menu', 1);
		if(!is_null($adminmenu_element)){
			$adminsubmenu_element = $mosinstall->getElementsByPath('administration/submenu', 1);
			$com_name = strtolower("com_" . str_replace(" ", "", $this->elementName()));
			$com_admin_menuname = $adminmenu_element->getText();

			if(!is_null($adminsubmenu_element)){
				$com_admin_menu_id = $this->createParentMenu($com_admin_menuname, $com_name);
				if($com_admin_menu_id === false){
					return false;
				}
				$com_admin_submenus = $adminsubmenu_element->childNodes;

				$submenuordering = 0;
				foreach($com_admin_submenus as $admin_submenu){
					$com = new mosComponent($database);
					$com->name = $admin_submenu->getText();
					$com->link = '';
					$com->menuid = 0;
					$com->parent = $com_admin_menu_id;
					$com->iscore = 0;

					if($admin_submenu->getAttribute("act")){
						$com->admin_menu_link = "option=$com_name&act=" . $admin_submenu->getAttribute("act");
					} else
						if($admin_submenu->getAttribute("task")){
							$com->admin_menu_link = "option=$com_name&task=" . $admin_submenu->getAttribute("task");
						} else
							if($admin_submenu->getAttribute("link")){
								$com->admin_menu_link = $admin_submenu->getAttribute("link");
							} else{
								$com->admin_menu_link = "option=$com_name";
							}
					$com->admin_menu_alt = $admin_submenu->getText();
					$com->option = $com_name;
					$com->ordering = $submenuordering++;
					$com->admin_menu_img = "js/ThemeOffice/component.png";

					if(!$com->store()){
						$this->setError(1, $database->stderr(true));
						return false;
					}
				}
			} else{
				$this->createParentMenu($com_admin_menuname, $com_name);
			}
		}

		$desc = '';
		if($e = $mosinstall->getElementsByPath('description', 1)){
			$desc = $this->elementName() . '<p>' . $e->getText() . '</p>';
		}
		$this->setError(0, $desc);

		if($this->hasInstallfile()){
			if(is_file($this->componentAdminDir() . DS . $this->installFile())){
				$mosConfig_live_site = JPATH_SITE;
				require_once ($this->componentAdminDir() . DS . $this->installFile());
				$ret = com_install();
				if($ret != ''){
					$this->setError(0, $desc . $ret);
				}
			}
		}
		return $this->copySetupFile();
	}

	function createParentMenu($_menuname, $_comname, $_image = "js/ThemeOffice/component.png"){
		$database = database::getInstance();

		$db_name = $_menuname;
		$db_link = "option=$_comname";
		$db_menuid = 0;
		$db_parent = 0;
		$db_admin_menu_link = "option=$_comname";
		$db_admin_menu_alt = $_menuname;
		$db_option = $_comname;
		$db_ordering = 0;
		$db_admin_menu_img = $_image;
		$db_iscore = 0;
		$db_params = '';

		$query = "INSERT INTO #__components VALUES( null, " . $database->Quote($db_name) . ", " . $database->Quote($db_link) . ", " . (int)$db_menuid . ", " . (int)$db_parent . ", " . $database->Quote($db_admin_menu_link) . ", " . $database->Quote($db_admin_menu_alt) . ", " . $database->Quote($db_option) . ", " . (int)$db_ordering . ", " . $database->Quote($db_admin_menu_img) . ", " . (int)$db_iscore . ", '' )";
		$database->setQuery($query);
		if(!$database->query()){
			$this->setError(1, $database->stderr(true));
			return false;
		}
		$menuid = $database->insertid();
		return $menuid;
	}

	/**
	 * Custom install method
	 * @param int The id of the module
	 * @param string The URL option
	 * @param int The client id
	 */
	public function uninstall($cid, $option, $client = 0){
		$database = database::getInstance();
		$config = Jconfig::getInstance();

		josSpoofCheck();
		$uninstallret = '';

		$sql = "SELECT* FROM #__components WHERE id = " . (int)$cid;
		$database->setQuery($sql);

		$row = null;
		if(!$database->loadObject($row)){
			HTML_installer::showInstallMessage($database->stderr(true), _ERROR_DELETING, $this->returnTo($option, 'component', $client));
			exit();
		}

		if($row->iscore){
			HTML_installer::showInstallMessage(_COMPONENT . " $row->name " . _IS_PART_OF_CMS, _DELETE_ERROR, $this->returnTo($option, 'component', $client));
			exit();
		}

		// Delete entries in the DB
		$sql = "DELETE FROM #__components WHERE parent = " . (int)$row->id;
		$database->setQuery($sql);
		if(!$database->query()){
			HTML_installer::showInstallMessage($database->stderr(true), _DELETE_ERROR, $this->returnTo($option, 'component', $client));
			exit();
		}

		$sql = "DELETE FROM #__components WHERE id = " . (int)$row->id;
		$database->setQuery($sql);
		if(!$database->query()){
			HTML_installer::showInstallMessage($database->stderr(true), _DELETE_ERROR, $this->returnTo($option, 'component', $client));
			exit();
		}

		// Try to find the XML file
		$filesindir = mosReadDirectory(mosPathName(JPATH_BASE_ADMIN . DS . 'components' . DS . $row->option), '.xml$');
		if(count($filesindir) > 0){
			$ismosinstall = false;
			$found = 0;
			foreach($filesindir as $file){
				$xmlDoc = new DOMIT_Lite_Document();
				$xmlDoc->resolveErrors(true);
				if(!$xmlDoc->loadXML(JPATH_BASE_ADMIN . DS . 'components' . DS . $row->option . DS . $file, false, true)){
					return false;
				}
				$root = $xmlDoc->documentElement;

				if($root->getTagName() != 'mosinstall'){
					continue;
				}
				$found = 1;

				// Is there an uninstallfile
				$uninstallfile_elemet = &$root->getElementsByPath('uninstallfile', 1);
				if(!is_null($uninstallfile_elemet)){
					$uninstall_file = $uninstallfile_elemet->getText();
					if(!is_null($uninstall_file) && file_exists(JPATH_BASE_ADMIN . DS . 'components' . DS . $row->option . DS . $uninstall_file)){
						require_once (JPATH_BASE_ADMIN . DS . 'components' . DS . $row->option . DS . $uninstall_file);
						$uninstallret = com_uninstall();
					}
				}
				$query_element = &$root->getElementsbyPath('uninstall/queries', 1);
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
			}
			if(!$found){
				HTML_installer::showInstallMessage(_BAD_XML_FILE, _UNINSTALL_ERROR, $this->returnTo($option, 'component', $client));
				exit();
			}
		} else{
			/* :) */
		}
		// Delete directories
		if(trim($row->option)){
			$result = 0;
			$path = mosPathName(JPATH_BASE_ADMIN . DS . 'components' . DS . $row->option);
			if(is_dir($path)){
				$result |= deldir($path);
			}
			$path = mosPathName(JPATH_BASE . DS . 'components' . DS . $row->option);
			if(is_dir($path)){
				$result |= deldir($path);
			}
			return $result;
		} else{
			HTML_installer::showInstallMessage(_PARAM_FILED_EMPTY, _DELETE_ERROR, $option, 'component');
			exit();
		}

		return $uninstallret;
	}

	/**
	 * Uninstall method
	 */
	function cleanAfterError(){
		$config = Jconfig::getInstance();

		josSpoofCheck();
		$basepath = mosPathName(JPATH_BASE . DS . 'components' . DS . strtolower("com_" . str_replace(" ", "", $this->elementName())));
		$adminpath = mosPathName(JPATH_BASE_ADMIN . DS . 'components' . DS . strtolower("com_" . str_replace(" ", "", $this->elementName())));
		;

		if(file_exists($adminpath)){
			deldir($adminpath);
		}
		if(file_exists($basepath)){
			deldir($basepath);
		}
		$this->cleanMediaData(0);
		$this->cleanMediaData(1);

		return true;
	}

	function cleanMediaData($adminFiles = 0){
		$config = Jconfig::getInstance();

		$xmlDoc = $this->xmlDoc();
		$root = $xmlDoc->documentElement;
		if($adminFiles == 1){
			$files_element = &$root->getElementsByPath('administration/images', 1);
		} else{
			$files_element = &$root->getElementsByPath('images', 1);
		}

		if(!is_null($files_element)){
			if($files_element->hasChildNodes()){
				$files = $files_element->childNodes;
				if(count($files) != 0){
					foreach($files as $file){
						if($adminFiles == 1){
							if(file_exists(JPATH_BASE_ADMIN . DS . $file->getText())){
								unlink(JPATH_BASE_ADMIN . DS . $file->getText());
							}
						} else{
							if(file_exists(JPATH_BASE . DS . $file->getText())){
								unlink(JPATH_BASE . DS . $file->getText());
							}
						}
					}
				}
			}
		}
	}
}