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
* Writes the edit form for new and existing content item
*
* A new record is defined when <var>$row</var> is passed with the <var>id</var>
* property set to 0.
* @package Joostina
* @subpackage Menus
*/
class content_category_menu_html {

	function editCategory(&$menu,&$lists,&$params,$option) {
		mosCommonHTML::loadOverlib();
?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			if ( pressbutton == 'cancel' ) {
				submitform( pressbutton );
				return;
			}
			var form = document.adminForm;
			<?php
		if(!$menu->id) {
?>
				if ( getSelectedValue( 'adminForm', 'componentid' ) < 1 ) {
					alert( '<?php echo _PLEASE_CHOOSE_CATEGORY?>' );
					return;
				}
				sectcat = getSelectedText( 'adminForm', 'componentid' );
				sectcats = sectcat.split('/');
				section = getSelectedOption( 'adminForm', 'componentid' );

				form.link.value = "index.php?option=com_content&task=category&sectionid=" + section.id + "&id=" + form.componentid.value;
				if ( form.name.value == '' ) {
					form.name.value = sectcats[1];
				}
				submitform( pressbutton );
				<?php
		} else {
?>
				if ( form.name.value == '' ) {
					alert( '<?php echo _OBJECT_MUST_HAVE_NAME?>' );
				} else {
					submitform( pressbutton );
				}
				<?php
		}
?>
		}
		</script>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="menus">
			<?php echo $menu->id?_EDITING.' -':_CREATION.' -'; ?> <?php echo _MENU_ITEM_TABLE_CONTENT_CATEGORY?>
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr valign="top">
			<td width="60%">
				<table class="adminform">
				<tr>
					<th colspan="3">
					<?php echo _DETAILS?>
					</th>
				</tr>
				<tr>
					<td width="20%" align="right" valign="top">
					<?php echo _NAME?>:
					</td>
					<td width="200px">
					<input type="text" name="name" size="50" maxlength="100" class="inputbox" value="<?php echo htmlspecialchars($menu->name,ENT_QUOTES); ?>"/>
					</td>
					<td>
<?php
		if(!$menu->id) {
			echo mosToolTip(_CATEGORY_TITLE_IF_FILED_IS_EMPTY);
		}
?>
					</td>
				</tr>
				<tr>
					<td width="10%" align="right" valign="top">
					<?php echo _LINK_TITLE?>:
					</td>
					<td width="80%">
						<input class="inputbox" type="text" name="params[title]" size="50" maxlength="100" value="<?php echo htmlspecialchars($params->get('title',''),ENT_QUOTES); ?>" />
					</td>
				</tr>
				<tr>
					<td width="20%" align="right" valign="top">
					<?php echo _CATEGORY?>:
					</td>
					<td colspan="2">
					<?php echo $lists['componentid']; ?>
					</td>
				</tr>
				<tr>
					<td align="right">
					URL:
					</td>
					<td colspan="2">
					<?php echo ampReplace($lists['link']); ?>
					</td>
				</tr>
				<tr>
					<td align="right">
					<?php echo _PARENT_MENU_ITEM?>:
					</td>
					<td colspan="2">
					<?php echo $lists['parent']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo _ORDER_DROPDOWN?>:
					</td>
					<td colspan="2">
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo _ACCESS?>:
					</td>
					<td colspan="2">
					<?php echo $lists['access']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo _PUBLISHED?>:
					</td>
					<td colspan="2">
					<?php echo $lists['published']; ?>
					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				</table>
			</td>
			<td width="40%">
				<table class="adminform">
				<tr>
					<th>
					<?php echo _PARAMETERS?>
					</th>
				</tr>
				<tr>
					<td>
					<?php echo $params->render(); ?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $menu->id; ?>" />
		<input type="hidden" name="menutype" value="<?php echo $menu->menutype; ?>" />
		<input type="hidden" name="type" value="<?php echo $menu->type; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
		</form>
		<?php
	}
}
?>