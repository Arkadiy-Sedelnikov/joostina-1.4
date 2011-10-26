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
 * @package Joostina
 * @subpackage Installer
 */
class HTML_component {

	/**
	 * @param array An array of records
	 * @param string The URL option
	 */
	public static function showInstalledComponents($rows,$option) {
		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';
		if(count($rows)) {
			// подключение скрипта чудесных таблиц
			mosCommonHTML::loadPrettyTable();

			?>
<form action="index2.php" method="post" name="adminForm" id="adminForm">
	<table class="adminheading">
		<tr>
			<th class="install"><?php echo _INSTALLED_COMPONENTS?></th>
		</tr>
		<tr><?php HTML_installer::cPanel(); ?></tr>
		<tr>
			<td><div class="jwarning"><?php echo _INSTALLED_COMPONENTS2?></div></td>
		</tr>
	</table>
	<table class="adminlist" id="adminlist">
		<tr>
			<th width="20%" class="title"><?php echo _COMPONENT_NAME?></th>
			<th width="5%" align="center"><?php echo _COM_INSTALLER_ACTIVE?></th>
			<th width="5%" align="center"><?php echo _VERSION?></th>
			<th width="20%" class="title"><?php echo _COMPONENT_LINK?></th>
			<th width="10%" align="left"><?php echo _AUTHOR?></th>
			<th width="10%" align="center"><?php echo _DATE?></th>
			<th width="15%" align="left"><?php echo _EMAIL?></th>
			<th width="15%" align="left"><?php echo _COMPONENT_AUTHOR_URL?></th>
		</tr>
					<?php
					$rc = 0;
					$n = count($rows);
					for($i = 0,$n =$n ; $i < $n; $i++) {
						$row = &$rows[$i];
						?>				<tr class="row<?php echo $rc?>">
			<td align="left"><input type="radio" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);"><span class="bold"><?php echo $row->name; ?></span></td>
			<td align="center" onclick="ch_publ(<?php echo $row->id?>,'com_installer');" class="td-state">
				<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/<?php echo $row->img;?>" id="img-pub-<?php echo $row->id;?>" alt="<?php echo _PUBLISHING?>" />
			</td>
			<td align="center"><?php echo isset($row->version) ? $row->version : "&nbsp;"; ?></td>
			<td align="left"><?php echo isset($row->link) ? $row->link : "&nbsp;"; ?></td>
			<td align="left"><?php echo isset($row->author) ? $row->author:"&nbsp;"; ?></td>
			<td align="center"><?php echo isset($row->creationdate) ? $row->creationdate:"&nbsp;"; ?></td>
			<td align="center"><?php echo isset($row->authorEmail) ? $row->authorEmail:"&nbsp;"; ?></td>
			<td align="center"><?php echo isset($row->authorUrl) ? '<a href="'.(substr($row->authorUrl,0,7) =='http://'?$row->authorUrl:'http://'.$row->authorUrl)."\" target=\"_blank\">$row->authorUrl</a>":"&nbsp;"; ?></td>
		</tr>
						<?php				$rc = 1 - $rc;
					}
					?></table>
				<?php
			} else {
				?>
	<td class="small"><?php echo _OTHER_COMPONENTS_NOT_INSTALLED?></td>
				<?php
			}
			?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_installer" />
	<input type="hidden" name="element" value="component" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}
}