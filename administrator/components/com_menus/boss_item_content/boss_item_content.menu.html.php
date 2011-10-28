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
class boss_item_content_menu_html {

	function editCategory(&$menu,&$lists,&$params,$option) {
		mosCommonHTML::loadOverlib();
?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="javascript" type="text/javascript">




		function submitbutton(pressbutton) {
            var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

            // do field validation
            if ( form.name.value == '' ) {
			    alert( '<?php echo _OBJECT_MUST_HAVE_NAME?>' );
                return;
			}
            if( trim(form.category.value) == ""){
                alert( "<?php echo _BOSS_MUST_HAVE_CATEGORY?>" );
                return;
            }
			else if ( trim(form.content_id.value) == "0" ){
				alert( "<?php echo _BOSS_MUST_HAVE_CONTENT?>" );
                return;
			}
            else if( trim(form.directory.value) == "0"){
                alert( "<?php echo _BOSS_MUST_HAVE_DIRECTORY?>" );
                return;
            }

			form.link.value = "index.php?option=com_boss&task=show_content&contentid="+form.content_id.value+"&catid="+form.category.value+"&directory=" + form.directory.value;
			submitform( pressbutton );
		}

        function selectBossContent() {
            var directory = jQuery('#directory').val();
			var catid = jQuery('#category').val();
            jQuery.ajax({
                type: "POST",
                url: '/administrator/ajax.index.php?option=com_menus&task=get_category_content&catid='+catid+'&directory='+directory,
                dataType: 'html',
                success: function (data) {
                    jQuery("#content").append(jQuery(data));
                    jQuery("#content").slideDown('slow');
                }
            });
        }

        function writeBossContent(){
            var content_id = jQuery('#content').val();
            jQuery('#content_id').val(content_id);
        }
		</script>
        
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="menus">
			<?php echo $menu->id?_EDITING.' -':_CREATION.' -'; ?> <?php echo _MENU_PUNKT ?>:: <?php echo $lists['directoryconf']->name ?> → <?php echo _MENU_BOSS_CONTENT?>
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
					<td width="10%" align="right" valign="top"><?php echo _NAME?>:</td>
					<td width="200px">
					<input type="text" name="name" size="30" maxlength="100" class="inputbox" value="<?php echo htmlspecialchars($menu->name,ENT_QUOTES); ?>"/>
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
					<td valign="top" align="right"><?php echo _CATEGORY ?>:</td>
					<td>
                        <select size="10" name="category" id="category" onclick="selectBossContent()">
                            <?php HTML_boss::selectCategories(0, 'Root' . " >> ", $lists['categories'], $lists['selected_categ'], -1, 1); ?>
                        </select>
					</td>
				</tr>
                <tr>
					<td valign="top" align="right"><?php echo _BOSS_CONTENT ?>:</td>
					<td>
                        <select name="content" id="content"  style="display: none;" onchange="writeBossContent()">
                            <option value=""><?php echo _BOSS_SELECT_CONTENT ?></option>
                        </select>
					</td>
				</tr>
				<tr>
					<td align="right">URL:</td>
					<td>
					<?php echo ampReplace($lists['link']); ?>
					</td>
				</tr>
				<tr>
					<td align="right"><?php echo _PARENT_MENU_ITEM?>:</td>
					<td>
					<?php echo $lists['parent']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right"><?php echo _ORDER_DROPDOWN?>:</td>
					<td>
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right"><?php echo _ACCESS?>:</td>
					<td>
					<?php echo $lists['access']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right"><?php echo _PUBLISHED?>:</td>
					<td>
					<?php echo $lists['published']; ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
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
        <input type="hidden" name="directory" id="directory" value="<?php echo $lists['directoryconf']->id; ?>" />
        <input type="hidden" name="link" value="" />
        <input type="hidden" name="content_id" id="content_id" value="<?php echo $lists['selected_content']; ?>" />
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