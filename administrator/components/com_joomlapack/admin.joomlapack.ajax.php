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

$acl = &gacl::getInstance();

if(!$acl->acl_check('administration','config','users',$my->usertype)) {
	die('error-acl');
}

// подключение класса конфигурации
require_once (JPATH_BASE_ADMIN."/components/com_joomlapack/includes/configuration.php");
require_once (JPATH_BASE_ADMIN."/components/com_joomlapack/includes/sajax.php");
require_once (JPATH_BASE_ADMIN."/components/com_joomlapack/includes/ajaxtool.php");