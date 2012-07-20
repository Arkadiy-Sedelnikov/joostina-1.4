<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */
//error_reporting(E_ALL);
// запрет прямого доступа
defined('_JLINDEX') or die();

require_once ($mainframe->getPath('installer_html', 'installer'));
require_once ($mainframe->getPath('installer_class', 'installer'));

// map the element to the required derived class
$classMap = array(
	'component' => 'mosInstallerComponent'
, 'language'    => 'mosInstallerLanguage'
, 'mambot'      => 'mosInstallerMambot'
, 'module'      => 'mosInstallerModule'
, 'template'    => 'mosInstallerTemplate'
);

$generalInstaller = new mosGeneralInstaller();
js_menu_cache_clear();

switch($task){
	case 'installfromurl':
		$generalInstaller->getPackageFromUrl($url, $client);
		break;

	case 'uploadfile':
		$generalInstaller->uploadPackage($option, $element, $client);
		break;

	case 'installfromdir':
		$generalInstaller->installFromDirectory($option, $element, $client);
		break;

	default:
		$generalInstaller->showInstallForm();
		break;
}

class mosGeneralInstaller{

	function showInstallForm(){
		HTML_installer_core::showInstallForm(_INSTALL_MANAGER, 'com_installer', 'installer', '', JPATH_BASE . '/media');
	}

	function uploadFile($filename, $userfile_name, &$msg){
		josSpoofCheck();
		$baseDir = mosPathName(JPATH_BASE . '/media');

		if(file_exists($baseDir)){
			if(is_writable($baseDir)){
				if(move_uploaded_file($filename, $baseDir . $userfile_name)){
					if(mosChmod($baseDir . $userfile_name)){
						return true;
					} else{
						$msg = _CANNOT_CHMOD;
					}
				} else{
					$msg = _CANNOT_MOVE_TO_MEDIA;
				}
			} else{
				$msg = _CANNOT_WRITE_TO_MEDIA;
			}
		} else{
			$msg = _CANNOT_INSTALL_NO_MEDIA;
		}
		return false;
	}

	/**
	 * @param string The class name for the installer
	 * @param string The URL option
	 * @param string The element name
	 */
	function getPackageFromUrl($url, $client){
		global $classMap;
		$pre_installer = new mosInstaller();
		josSpoofCheck();
		$msg = '';
		// Check if url is available for fopen
		if(!(bool)ini_get('allow_url_fopen')){
			HTML_installer::showInstallMessage(_CANNOT_INSTALL_DISABLED_UPLOAD, _INSTALL_ERROR, $pre_installer->returnTo('com_installer', 'installer', $client));
			return false;
		}

		$p_file = $pre_installer->downloadPackage($url);

		// Was the package downloaded?
		if(!$p_file){
			HTML_installer::showInstallMessage($pre_installer->getError(), _UPLOADING_ERROR . ' ' . 'installer', $pre_installer->returnTo('com_installer', 'installer', $client));
			return false;
		}

		if(!$pre_installer->upload($p_file)){
			HTML_installer::showInstallMessage($pre_installer->getError(), _UPLOADING_ERROR . ' ' . 'installer', $pre_installer->returnTo('com_installer', 'installer', $client));
		}
		$installType = $pre_installer->getInstallType();
		if($installType != "" && array_key_exists($installType, $classMap)){

			require (_JLPATH_ADMINISTRATOR . "/components/com_installer/$installType/$installType.class.php");

			$installer = new $classMap[$installType]($pre_installer);

			$ret = $installer->install();

			HTML_installer::showInstallMessage($installer->getError(), 'installer' . ' - ' . ($ret ? _SUCCESS : _UNSUCCESS), $installer->returnTo('com_installer', 'installer', $client));
			cleanupInstall($p_file, $installer->unpackDir());
		} else{
			HTML_installer::showInstallMessage($msg, 'installer' . ' - ' . _UPLOADING_ERROR, $pre_installer->returnTo('com_installer', 'installer', $client));
		}
	}

	function uploadPackage($option, $element, $client){
		global $classMap;

		$config = Jconfig::getInstance();

		$pre_installer = new mosInstaller();
		josSpoofCheck();

		// Check if file uploads are enabled
		if(!(bool)ini_get('file_uploads')){
			HTML_installer::showInstallMessage(_CANNOT_INSTALL_DISABLED_UPLOAD, _INSTALL_ERROR, $pre_installer->returnTo($option, $element, $client));
			exit();
		}
		// Check that the zlib is available
		if(!extension_loaded('zlib')){
			HTML_installer::showInstallMessage(_CANNOT_INSTALL_NO_ZLIB, _INSTALL_ERROR, $pre_installer->returnTo($option, $element, $client));
			return false;
		}

		$userfile = mosGetParam($_FILES, 'userfile', null);

		if(!$userfile){
			HTML_installer::showInstallMessage(_NO_FILE_CHOOSED, _ERORR_UPLOADING_EXT, $pre_installer->returnTo($option, $element, $client));
			return false;
		}

		$userfile_name = $userfile['name'];

		$msg = '';
		$resultdir = $this->uploadFile($userfile['tmp_name'], $userfile['name'], $msg);

		if($resultdir !== false){
			if(!$pre_installer->upload($userfile['name'])){
				HTML_installer::showInstallMessage($pre_installer->getError(), _UPLOADING_ERROR . ' ' . $element, $pre_installer->returnTo($option, $element, $client));
			}
			$installType = $pre_installer->getInstallType();
			if($installType != "" && array_key_exists($installType, $classMap)){

				require (_JLPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_installer' . DS . $installType . DS . $installType . '.class.php');

				$installer = new $classMap[$installType]($pre_installer);

				$ret = $installer->install();

				HTML_installer::showInstallMessage($installer->getError(), _INSTALLATION . ' ' . $userfile['name'] . ' - ' . ($ret ? _SUCCESS : _UNSUCCESS), $installer->returnTo($option, $element, $client));
				cleanupInstall($userfile['name'], $installer->unpackDir());
			} else{
				HTML_installer::showInstallMessage($msg, $element . ' - ' . _UPLOADING_ERROR, $pre_installer->returnTo($option, $element, $client));
			}

		} else{
			HTML_installer::showInstallMessage(_UPLOADING_ERROR, $element . ' - ' . _UPLOADING_ERROR, $pre_installer->returnTo($option, $element, $client));
		}
	}

	/**
	 * Install a template from a directory
	 * @param string The URL option
	 */
	function installFromDirectory($option, $element, $client){
		global $classMap;
		$config = Jconfig::getInstance();

		$pre_installer = new mosInstaller();
		$userfile = mosGetParam($_REQUEST, 'userfile', '');
		josSpoofCheck();

		if(!$userfile){
			mosRedirect("index2.php?option=com_installer&element=installer", _CHOOSE_DIRECTORY_PLEASE);
		}

		$path = mosPathName($userfile);

		if(!is_dir($path)){
			mosRedirect("index2.php?option=com_installer&element=installer", _CHOOSE_DIRECTORY_NOT_ARCHIVE);
		}

		$pre_installer->preInstallSetting($path);

		$installType = $pre_installer->getInstallType();
		if($installType != "" && array_key_exists($installType, $classMap)){
			require (_JLPATH_ADMINISTRATOR . "/components/com_installer/$installType/$installType.class.php");
			$installer = new $classMap[$installType]($pre_installer);
			$ret = $installer->install($path);
			HTML_installer::showInstallMessage($installer->getError(), _UPLOAD_OF_EXT . ': ' . $element . ' - ' . ($ret ? _SUCCESS : _UNSUCCESS), $installer->returnTo($option, $element, $client));
		} else{
			HTML_installer::showInstallMessage(_UPLOADING_ERROR, $element . ' - ' . _UNKNOWN_EXTENSION_TYPE, $pre_installer->returnTo($option, $element, $client));
		}
	}
}