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

// load the html drawing class
require_once ($mainframe->getPath('front_html'));

$mainframe = mosMainFrame::getInstance();
$my = $mainframe->getUser();
$database = database::getInstance();
$acl = &gacl::getInstance();
global $mosConfig_frontend_login, $mosConfig_db, $mosConfig_no_session_front;

if($mosConfig_frontend_login != null && ($mosConfig_frontend_login === 0 || $mosConfig_frontend_login === '0' || $mosConfig_no_session_front != 0)){
	header("HTTP/1.0 403 Forbidden");
	echo _NOT_AUTH;
	return;
}

$menu = $mainframe->get('menu');
$params = new mosParameters($menu->params);

$params->def('page_title', 1);
$params->def('header_login', $menu->name);
$params->def('header_logout', $menu->name);
$params->def('pageclass_sfx', '');
$params->def('back_button', $mainframe->getCfg('back_button'));
$params->def('login', JPATH_SITE);
$params->def('logout', JPATH_SITE);
$params->def('login_message', 0);
$params->def('logout_message', 0);
$params->def('description_login', 1);
$params->def('description_logout', 1);
$params->def('description_login_text', _LOGIN_DESCRIPTION);
$params->def('description_logout_text', _LOGOUT_DESCRIPTION);
$params->def('image_login', 'key.jpg');
$params->def('image_logout', 'key.jpg');
$params->def('image_login_align', 'right');
$params->def('image_logout_align', 'right');
$params->def('registration', $mainframe->getCfg('allowUserRegistration'));

$image_login = '';
$image_logout = '';
if($params->get('image_login') != -1){
	$image = JPATH_SITE . '/images/stories/' . $params->get('image_login');
	$image_login = '<img src="' . $image . '" align="' . $params->get('image_login_align') . '" hspace="10" alt="" />';
}
if($params->get('image_logout') != -1){
	$image = JPATH_SITE . '/images/stories/' . $params->get('image_logout');
	$image_logout = '<img src="' . $image . '" align="' . $params->get('image_logout_align') . '" hspace="10" alt="" />';
}

if($my->id){
	loginHTML::logoutpage($params, $image_logout);
} else{
	loginHTML::loginpage($params, $image_login);
}