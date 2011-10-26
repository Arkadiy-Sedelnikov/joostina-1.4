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

require_once (JPATH_BASE_ADMIN.'/components/com_installer/installer/installer.class.php');

class XmapAdmin {

	var $config = null;

	/** Parses input parameters and calls appropriate function */
	function show( &$config, &$task, &$cid ) {
		$this->config = &$config;
		global $xmapComponentPath;
		switch ($task) {

			case 'save':
				$this->saveOptions( $config );
				break;

			case 'cancel':
				mosRedirect( 'index2.php' );
				break;

			case 'uploadfile':
				xmapUploadPlugin();
				break;

			case 'installfromdir':
				xmapInstallPluginFromDirectory();
				break;

			case 'ajax_request':
				include($xmapComponentPath . '/admin.xmap.ajax.php');
				break;
			default:
				$success = mosGetParam($_REQUEST,'success','');
				$this->showSettingsDialog($success);
				break;
		}
	}

	/** Show settings dialog
	 * @param integer  configuration save success
	 */
	function showSettingsDialog( $success = 0 ) {
		global $mainframe;

		$database = database::getInstance();

		$menus = $this->getMenus();
		# $this->sortMenus( $menus );

		$config = &$this->config;

		// success messages
		switch( $success ) {
			case 1:
				$lists['msg_success'] = _XMAP_MSG_SET_BACKEDUP;
				break;
			case 2:
				$lists['msg_success'] = _XMAP_ERR_CONF_SAVE;
				break;
			default:
				$lists['msg_success'] =  _XMAP_CFG_COM_TITLE;
				break;
		}

		$pluginList = '';
		$xmlfile = '';
		loadInstalledPlugins($pluginList,$xmlfile);

		require_once( $mainframe->getPath( 'admin_html' ) );
		XmapAdminHtml::show( $config, $menus, $lists,$pluginList,$xmlfile );
	}

	/** Save settings handed via POST */
	function saveOptions( &$config ) {
		$success	= 1;

		$exclude_css	= mosGetParam( $_POST, 'exclude_css', 0 );
		$exclude_xsl	= mosGetParam( $_POST, 'exclude_xsl', 0 );

		$config->exclude_css = $exclude_css;
		$config->exclude_xsl = $exclude_xsl;
		$config->save();

		mosRedirect('index2.php?option=com_xmap&success='.$success);
		exit;
	}

	/**
	 *
	 * get the complete list of menus in joomla
	 */
	function &getMenus() {
		$config = &$this->config;
		$menutypes  = mosAdminMenus::menutypes();

		$allmenus = array();
		foreach( $menutypes as $index => $menutype ) {
			$allmenus[$menutype] = new stdclass;
			$allmenus[$menutype]->ordering = $index;
			$allmenus[$menutype]->show = false;
			$allmenus[$menutype]->showSitemap = false;
			$allmenus[$menutype]->priority = '0.5';
			$allmenus[$menutype]->changefreq = 'weekly';
			$allmenus[$menutype]->id = $index;
			$allmenus[$menutype]->type = $menutype;
		}

		return $allmenus;
	}
}

function loadInstalledPlugins( &$rows,&$xmlfile ) {
	$database = database::getInstance();

	require_once (JPATH_BASE .'/includes/domit/xml_domit_lite_parser.php');

	$query = "SELECT id, extension, published"
			. "\n FROM #__xmap_ext"
			. "\n WHERE extension not like '%.bak'"
			. "\n ORDER BY extension";

	$database->setQuery( $query );
	$rows = $database->loadObjectList();

	$n = count( $rows );
	for ($i = 0; $i < $n; $i++) {
		$row =& $rows[$i];

		// path to module directory
		$extensionBaseDir	= mosPathName( mosPathName( JPATH_BASE ) . '/'.JADMIN_BASE.'/components/com_xmap/extensions/' );

		// xml file for module
		$xmlfile = $extensionBaseDir.DS.$row->extension. ".xml";

		if (file_exists( $xmlfile )) {
			$xmlDoc = new DOMIT_Lite_Document();
			$xmlDoc->resolveErrors( true );
			if (!$xmlDoc->loadXML( $xmlfile, false, true )) {
				continue;
			}

			$root = &$xmlDoc->documentElement;

			if ($root->getTagName() != 'mosinstall') {
				continue;
			}
			if ($root->getAttribute( "type" ) != "xmap_ext") {
				continue;
			}


			$element 			= &$root->getElementsByPath( 'name', 1 );
			$row->name		 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath( 'creationDate', 1 );
			$row->creationdate 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath( 'author', 1 );
			$row->author 		= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath( 'copyright', 1 );
			$row->copyright 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath( 'authorEmail', 1 );
			$row->authorEmail 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath( 'authorUrl', 1 );
			$row->authorUrl 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath( 'version', 1 );
			$row->version 		= $element ? $element->getText() : '';
		}else {
			echo "Missing file '$xmlfile'";
		}
	}
}

function showInstalledPlugins( $_option ) {
	$rows = '';
	$xmlfile = '';
	loadInstalledPlugins($rows,$xmlfile);
	XmapAdminHtml::showInstalledModules( $rows, $_option, $xmlfile, $lists );
}

/**
 * Install a uploaded extension
 */

function xmapUploadPlugin( ) {
	$option ='com_xmap';
	$element = 'plugin';
	$client = '';
	require_once(JPATH_BASE. '/'.JADMIN_BASE.'/components/com_xmap/classes/XmapPluginInstaller.php');
	$installer = new XmapPluginInstaller();

	// Check if file uploads are enabled
	if (!(bool)ini_get('file_uploads')) {
		XmapAdminHtml::showInstallMessage( "The installer can't continue before file uploads are enabled. Please use the install from directory method.",'Installer - Error', $installer->returnTo( $option, $element, $client ) );
		exit();
	}

	// Check that the zlib is available
	if(!extension_loaded('zlib')) {
		XmapAdminHtml::showInstallMessage( "The installer can't continue before zlib is installed",'Installer - Error', $installer->returnTo( $option, $element, $client ) );
		exit();
	}

	$userfile = mosGetParam( $_FILES, 'install_package', null );

	if (!$userfile) {
		XmapAdminHtml::showInstallMessage( 'No file selected', 'Upload new module - error',$installer->returnTo( $option, $element, $client ));
		exit();
	}

	$userfile_name = $userfile['name'];

	$msg = '';
	$resultdir = xmapUploadFile( $userfile['tmp_name'], $userfile['name'], $msg );

	if ($resultdir !== false) {
		if (!$installer->upload( $userfile['name'] )) {
			XmapAdminHtml::showInstallMessage( $installer->getError(), 'Upload '.$element.' - Upload Failed',
					$installer->returnTo( $option, $element, $client ) );
		}
		$ret = $installer->install();

		XmapAdminHtml::showInstallMessage( $installer->getError(), 'Upload '.$element.' - '.($ret ? 'Success' : 'Failed'),
				$installer->returnTo( $option, $element, $client ) );
		cleanupInstall( $userfile['name'], $installer->unpackDir() );
	} else {
		XmapAdminHtml::showInstallMessage( $msg, 'Upload '.$element.' -  Upload Error',
				$installer->returnTo( $option, $element, $client ) );
	}

} 

/**
 * Install a extension from a directory
 */
function xmapInstallPluginFromDirectory() {
	$userfile = mosGetParam( $_REQUEST, 'userfile', '' );
	$option ='com_xmap';
	$element = 'plugin';
	$client = '';
	require_once(JPATH_BASE. '/'.JADMIN_BASE.'/components/com_xmap/classes/XmapPluginInstaller.php');
	$installer = new XmapPluginInstaller();

	if (!$userfile) {
		mosRedirect( "index2.php?option=$option", "Please select a directory" );
	}

	$installer = new XmapPluginInstaller();

	$path = mosPathName( $userfile );
	if (!is_dir( $path )) {
		$path = dirname( $path );
	}

	$ret = $installer->install( $path );
	XmapAdminHtml::showInstallMessage( $installer->getError(), 'Upload new '.$element.' - '.($ret ? 'Success' : 'Error'), $installer->returnTo( $option, $element, $client ) );
}



/**
 *
 * @param
 */
function xmapUninstallPlugin( $extensionid ) {
	require_once(JPATH_BASE_ADMIN.'/components/com_xmap/classes/XmapPluginInstaller.php');
	$installer = new XmapPluginInstaller();
	$result = false;
	if ($extensionid) {
		$result = $installer->uninstall('xmap_ext', $extensionid );
	}

	if (!$result) {
		echo $installer->getError();
	}
	return $result;
}

/**
 * @param string The name of the php (temporary) uploaded file
 * @param string The name of the file to put in the temp directory
 * @param string The message to return
 */
function xmapUploadFile( $filename, $userfile_name, &$msg ) {
	$baseDir = mosPathName( JPATH_BASE . '/media' );

	if (file_exists( $baseDir )) {
		if (is_writable( $baseDir )) {
			if (move_uploaded_file( $filename, $baseDir . $userfile_name )) {
				if (mosChmod( $baseDir . $userfile_name )) {
					return true;
				} else {
					$msg = _CANNOT_CHMOD;
				}
			} else {
				$msg = _CANNOT_MOVE_TO_MEDIA;
			}
		} else {
			$msg = _CANNOT_WRITE_TO_MEDIA;
		}
	} else {
		$msg = _CANNOT_INSTALL_NO_MEDIA;
	}
	return false;
}