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
 * Template installer
 * @package Joostina
 * @subpackage Installer
 */
class mosInstallerTemplate extends mosInstaller {

	function __construct($pre_installer) {
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
	function install($p_fromdir = null) {
		$database = database::getInstance();
		$database = database::getInstance();

		josSpoofCheck();
		if (!$this->preInstallCheck($p_fromdir, 'template')) {
			return false;
		}

		$xmlDoc = &$this->xmlDoc();
		$mosinstall = &$xmlDoc->documentElement;

		$client = '';
		if ($mosinstall->getAttribute('client')) {
			$validClients = array('administrator');
			if (!in_array($mosinstall->getAttribute('client'), $validClients)) {
				$this->setError(1, _UNKNOWN_CLIENT . ' [' . $mosinstall->getAttribute('client') . ']');
				return false;
			}
			$client = 'admin';
		}

		// Set some vars
		$e = &$mosinstall->getElementsByPath('name', 1);

		$f = &$mosinstall->getElementsByPath('folder', 1);
        $f = (!empty($f)) ? @$f->getText() : '';
        $f = (!empty($f)) ? $f = $f.DS : '';

		$this->elementName($f . $e->getText());
        $this->elementDir(mosPathName(JPATH_BASE.($client == 'admin'? DS.JADMIN_BASE:'').DS.'templates'.DS.strtolower(str_replace(" ","_",$this->elementName()))));
		$base = mosPathName(JPATH_BASE . ($client == 'admin' ? '/' . JADMIN_BASE : '') . '/templates');
		$path = strtolower(str_replace(" ", "_", $this->elementName()));
		if (!file_exists($this->elementDir()) && !mosMakePath($base, $path)) {
			$this->setError(1, _CANNOT_CREATE_DIR . ' "' . $this->elementDir() . '"');
			return false;
		}

		if ($this->parseFiles('files') === false) {
			$this->cleanAfterError();
			return false;
		}
		if ($this->parseFiles('images') === false) {
			$this->cleanAfterError();
			return false;
		}
		if ($this->parseFiles('css') === false) {
			$this->cleanAfterError();
			return false;
		}
		if ($this->parseFiles('media') === false) {
			$this->cleanAfterError();
			return false;
		}
		if ($e = &$mosinstall->getElementsByPath('description', 1)) {
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
	function uninstall($id, $option, $client = 0) {
		$database = database::getInstance();
		josSpoofCheck(null, null, 'request');
		// Delete directories
		$path = JPATH_BASE . ($client == 'admin' ? '/' . JADMIN_BASE : '') . '/templates/' . $id;

		$id = str_replace('..', '', $id);
		if (trim($id)) {
			if (is_dir($path)) {
				return deldir(mosPathName($path));
			} else {
				HTML_installer::showInstallMessage(_CANNOT_DEL_FILE_NO_DIR, _UNINSTALL_ERROR, $this->returnTo($option, $client));
			}
		} else {
			HTML_installer::showInstallMessage(_WRONG_ID, _UNINSTALL_ERROR, $this->returnTo($option, $client));
			exit();
		}
	}

	/**
	 * Custom install method
	 * @param int The id of the module
	 * @param string The URL option
	 * @param int The client id
	 */
	function cleanAfterError() {
		global $client;
        $database = database::getInstance();
		josSpoofCheck(null, null, 'request');
		// Delete directories
		$path = JPATH_BASE . ($client == 'admin' ? '/' . JADMIN_BASE : '') . '/templates/' . $this->elementName();
		// get the files element
		if (is_dir($path)) {
			return deldir(mosPathName($path));
		}
	}

	/**
	 * return to method
	 */
	function returnTo($option,  $client) {
		return "index2.php?option=com_installer&element=template&client=$client";
	}

}