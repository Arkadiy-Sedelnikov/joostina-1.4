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

/**
 * @package Joostina
 * @subpackage Installer
 */
class HTML_module{
	public static function showInstalledModules(&$rows, $option, &$xmlfile, &$lists){
		if(count($rows)){
			// подключение скрипта чудесных таблиц
			mosCommonHTML::loadPrettyTable();
			?>
<form action="index2.php" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th class="install"><?php echo _INSTALL_MODULE?></th>
			<td><?php echo _FILTER ?></td>
			<td width="right"><?php echo $lists['filter']; ?></td>
		</tr>
		<tr><?php HTML_installer::cPanel(); ?></tr>
		<tr>
			<td colspan="3">
				<div class="jwarning"><?php echo _INSTALLED_COMPONENTS2?></div>
			</td>
		</tr>
	</table>
	<table class="adminlist" id="adminlist">
		<tr>
			<th width="20%" class="title"><?php echo _MODULE?></th>
			<th width="5%" align="center"><?php echo _VERSION?></th>
			<th width="10%" align="left"><?php echo _USED_ON?></th>
			<th width="10%" align="left"><?php echo _AUTHOR?></th>
			<th width="10%" align="center"><?php echo _DATE?></th>
			<th width="15%" align="left">E-mail</th>
			<th width="15%" align="left"><?php echo _COMPONENT_AUTHOR_URL?></th>
		</tr>
			<?php
			$rc = 0;
			$_n = count($rows);
			for($i = 0, $n = $_n; $i < $n; $i++){
				$row = &$rows[$i];
				?>
				<tr class="row<?php echo $rc ?>">
					<td align="left">
						<input type="radio" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);"><span class="bold"><?php echo $row->module; ?></span></td>
					<td align="center"><?php echo @$row->version != "" ? $row->version : "&nbsp;"; ?></td>
					<td align="left"><?php echo $row->client_id == "0" ? _SITE : _CONTROL_PANEL; ?></td>
					<td><?php echo @$row->author != "" ? $row->author : "&nbsp;"; ?></td>
					<td align="center"><?php echo @$row->creationdate != "" ? $row->creationdate : "&nbsp;"; ?></td>
					<td align="center"><?php echo @$row->authorEmail != "" ? $row->authorEmail : "&nbsp;"; ?></td>
					<td align="center"><?php echo @$row->authorUrl != "" ? "<a href=\"" . (substr($row->authorUrl, 0, 7) == 'http://' ? $row->authorUrl : 'http://' . $row->authorUrl) . "\" target=\"_blank\">$row->authorUrl</a>" : "&nbsp;"; ?></td>
				</tr>
				<?php
				$rc = $rc == 0 ? 1 : 0;
			}
		} else{
			?>
			<td class="small"><?php echo _NO_OTHER_MODULES?></td>
			<?php
		}
		?>
	</table>
	<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="option" value="com_installer"/>
		<input type="hidden" name="element" value="module"/>
		<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1"/>
</form>
		<?php
	}
}