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

$database = database::getInstance();
if(!defined('IS_ADMIN')) define('IS_ADMIN', 1);
// check to see if site is a production site
// allows multiple logins with same user for a demo site
if(joomlaVersion::get('SITE') == 1){
	// обновление записи последнего посещения панели управления в базе данных
	if(isset($_SESSION['session_user_id']) && $_SESSION['session_user_id'] != ''){
		$currentDate = date("Y-m-d\TH:i:s");

		$query = "UPDATE #__users SET lastvisitDate = " . $database->Quote($currentDate) . " WHERE id = " . (int)$_SESSION['session_user_id'];
		$database->setQuery($query);

		if(!$database->query()){
			echo $database->stderr();
		}
	}

	// delete db session record corresponding to currently logged in user
	if(isset($_SESSION['session_id']) && $_SESSION['session_id'] != ''){
		$query = "DELETE FROM #__session WHERE session_id = " . $database->Quote($_SESSION['session_id']);
		$database->setQuery($query);

		if(!$database->query()){
			echo $database->stderr();
		}
	}
}

// destroy PHP session
session_destroy();

// return to site homepage
mosRedirect('index.php');