<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2007 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

global $JPConfiguration,$option;

$WSOutdir = $JPConfiguration->isOutputWriteable();

$appStatusGood = true;
if(!($WSOutdir)) {
	$appStatusGood = false;
}

// информация о состоянии пакера
echo colorizeAppStatus($appStatusGood);
?>

<table class="adminheading">
	<tr>
		<th class="cpanel"><?php echo _JP_BACKUP_MANAGEMENT?></th>
	</tr>
</table>
<table>
	<tr>
		<td width="40%" valign="top">
			<div class="cpicons">
				<?php
				$link = "index2.php?option=com_joomlapack&act=pack";
				quickiconButton($link,'pack.png', _JP_CREATE_BACKUP);

				$link = 'index2.php?option=com_joomlapack&act=db';
				quickiconButton($link,'db.png',_DB_MANAGEMENT);

				$link = "index2.php?option=com_joomlapack&act=def";
				quickiconButton($link,'stopfolder.png', _JP_DONT_SAVE_DIRECTORIES);

				$link = "index2.php?option=com_joomlapack&act=config";
				quickiconButton($link,'config.png', _JP_CONFIG);

				$link = "index2.php?option=com_joomlapack&act=log";
				quickiconButton($link,'log.png', _JP_ACTIONS_LOG);
				?>
			</div>
			<div style="clear:both;">&nbsp;</div>
		</td>
		<td valign="top">
			<?php
			require_once (JPATH_BASE_ADMIN.'/components/com_joomlapack/includes/html.files.php');
			?>
		</td>
	</tr>
</table>

<?php

/**
 * вывод итогового состояния пакера
 */
function colorizeAppStatus($status) {
	global $JPConfiguration;
	$statusVerbal = _JP_ERRORS_TMP_DIR.' ( <b>'.$JPConfiguration->OutputDirectory.'</b> )';
	if(!$status) {
		return '<div class="jwarning">'.$statusVerbal.'</div>';
	}
}
// прорисовка кнопок управления
function quickiconButton($link,$image,$text) {
	?>
<span>
	<a href="<?php echo $link; ?>" title="<?php echo $text; ?>">
			<?php
			echo mosAdminMenus::imageCheckAdmin($image,'/'.JADMIN_BASE.'/templates/'.mosMainFrame::getInstance(true)->getTemplate().'/images/system_ico/',null,null,$text);
			echo $text;
			?>
	</a>
</span>
	<?php
}