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

$task = mosGetParam($_REQUEST,'task','');

switch($task) {
	case 'upload_avatar':
		echo upload_avatar();
		return;

	case 'del_avatar':
		echo x_delavatar();
		return;

	case 'request_from_plugin':
		request_from_plugin();
		break;

	default:
		echo 'error-task';
		return;
}

function upload_avatar() {
	global $my;

	$database = database::getInstance();

	mosMainFrame::addLib('images');

	$return = array();

	$resize_options = array(
			'method' => '0',		//Приводит к заданной ширине, сохраняя пропорции.
			'output_file' => '',	//если 'thumb', то ресайзенная копия ляжет в подпапку "thumb'
			'width'  => '150',
			'height' => '150'
	);

	$file = new Image();
	$file->field_name = 'avatar';
	$file->directory = 'images/avatars' ;
	$file->file_prefix = 'av_';
	$file->max_size = 0.5 * 1024 * 1024;

	$foto_name = $file->upload($resize_options);

	if($foto_name) {
		if($my->id) {
			$user = new mosUser($database);
			$user->load((int)$my->id);
			$user_id = $user->id;
			if($user->avatar!='') {
				$foto = new Image();
				$foto->directory = 'images/avatars';
				$foto->name = $user->avatar;
				$foto->delFile($foto);
			}
			$user->update_avatar($my->id, $foto_name);
		}
		echo $foto_name;
	}else {
		return false;
	};
}


function x_delavatar() {
	global $my;

	$database = database::getInstance();

	$user = new mosUser($database);
	$user->update_avatar(null, $my->avatar, 1);

	return 'none.jpg';
}


function request_from_plugin() {
	$mainframe = mosMainFrame::getInstance();

	$plugin	= mosGetParam($_REQUEST,'plugin','');
	$act	= mosGetParam($_REQUEST,'act','');

	// проверяем, какой файл необходимо подключить, данные берутся из пришедшего GET запроса
	if(is_file(JPATH_BASE.DS. 'mambots'.DS.'profile'.DS.$plugin.DS.$plugin.'.ajax.php')) {
		if(is_file($mainframe->getLangFile('bot_'.$plugin))) {
			include_once ($mainframe->getLangFile('bot_'.$plugin));
		}
		include_once (JPATH_BASE.DS. 'mambots'.DS.'profile'.DS.$plugin.DS.$plugin.'.ajax.php');
	} else {
		die('error-1:1');
	}
}