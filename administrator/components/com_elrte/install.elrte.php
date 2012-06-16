<?php
/**
 * JoiEditor - Joostina WYSIWYG Editor
 * Backend install handler
 * @version 1.0 beta 3
 * @package JoiEditor
 * @subpackage    Installer
 * @filename install.joieditor.php
 * @author JoostinaTeam
 * @copyright (C) 2008-2010 Joostina Team
 * @license see license.txt
 **/

defined('_VALID_MOS') or die();

function com_install(){

	//Создание директории для пользовательских изображений
	$main_dir = JPATH_BASE . '/' . 'user_images';
	joi_check_dir($main_dir);

	//Установка бота
	joi_install_bot();
}


function joi_check_dir($dir){
	if(!is_dir($dir)){
		if(!mkdir($dir)){
			echo 'Ошибка установки: невозможно создать директорию ' . $dir;
			return false;
		}
		mosChmod($dir, 0777);
		return true;
	}
	return true;
}


function joi_unzip($zip, $unzip_dir){
	require_once (JPATH_BASE . '/administrator/includes/pcl/pclzip.lib.php');
	require_once (JPATH_BASE . '/administrator/includes/pcl/pclerror.lib.php');

	$zipfile = new PclZip($zip);

	$ret = $zipfile->extract(PCLZIP_OPT_PATH, $unzip_dir);
	if($ret == 0){
		echo "Unrecoverable error: " . $zipfile->errorName(true) . '<br />';
		return false;
	}
	unlink($zip);
	return true;
}

function joi_install_bot(){
	$database = &database::getInstance();

	$zipfile = JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_elrte' . DS . 'elrte.zip';
	$unzip_dir = JPATH_BASE . DS . 'mambots' . DS . 'editors';

	if(!joi_unzip($zipfile, $unzip_dir)){
		echo 'Не удалось распаковать архив ' . $zipfile . '<br />';
		return false;
	}

	$mambot = new mosMambot($database);
	$mambot->name = 'elRTE Mambot';
	$mambot->element = 'elrte';
	$mambot->folder = 'editors';
	$mambot->ordering = 1;
	$mambot->published = 1;

	if(!$mambot->store()){
		echo 'Ошибка установки мамбота: ' . $mambot->getError() . '<br />';
		return false;
	}
}