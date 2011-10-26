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

global $my;
$mainframe = mosMainFrame::getInstance();
$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';

$query = "SELECT COUNT(*)"
		."\n FROM #__messages"
		."\n WHERE state = 0"
		."\n AND user_id_to = ".(int)$my->id;
$database->setQuery($query);
$unread = $database->loadResult();

if($unread) {
	echo "<a class=\"adminmail\" href=\"index2.php?option=com_messages\" style=\"color: red; text-decoration: none;  font-weight: bold\"><img  src=\"".$cur_file_icons_path."/mail.png\" align=\"top\" border=\"0\" alt=\"Почта\" /> $unread </a>";
} else {
	echo "<a class=\"adminmail\" href=\"index2.php?option=com_messages\" style=\"color: black; text-decoration: none;\"><img src=\"".$cur_file_icons_path."/nomail.png\" align=\"top\" border=\"0\" alt=\"Почта\" /> $unread </a>";
}