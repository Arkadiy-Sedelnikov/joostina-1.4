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

// параметр выполняемого действия
$task	= mosGetParam($_GET,'task','publish');

// обрабатываем полученный параметр task
switch($task) {
	case 'toggle_editor':
		echo x_toggle_editor();
		return;
	case 'upload':
		echo x_upload();
		return;
	default:
		echo 'error-task';
		return;
}

// включение / отключение визуального редактора
function x_toggle_editor() {
	$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';

	if(!intval(mosGetParam($_SESSION,'user_editor_off',''))) {
		// отключаем редактор
		$_SESSION['user_editor_off'] = 1;
		return $cur_file_icons_path.'/editor_off.png';
	}else {
		// включаем редактор
		$_SESSION['user_editor_off'] = 0;
		return $cur_file_icons_path.'/editor_on.png';
	}
}

function x_upload() {
	?>
<form method="post" action="uploadimage.php" enctype="multipart/form-data" name="filename" id="filename">
	<table class="adminform" style="width:100%;">
		<tr>
			<th class="title"><?php echo _FILE_UPLOAD?>:</th>
		</tr>
		<tr>
			<td align="center">
				<input class="inputbox" name="userfile" type="file" /><input class="button" type="submit" value="Загрузить" name="fileupload" />
			</td>
		</tr>
		<tr>
			<td><?php echo _MAX_SIZE?> = <?php echo ini_get('post_max_size'); ?></td>
		</tr>
	</table>
	<input type="hidden" name="directory" value="" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
	<?php
}