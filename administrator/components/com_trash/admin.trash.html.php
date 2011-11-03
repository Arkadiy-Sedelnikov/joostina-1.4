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
 * HTML class for all trash component output
 * @package Joostina
 * @subpackage Trash
 */
class HTML_trash {
	/**
	 * Writes a list of the Trash items
	 */
	public static function showList($option,$content,$pageNav) {
		global $my;
		?>
<script language="javascript" type="text/javascript">
	/**
	 * Toggles the check state of a group of boxes
	 *
	 * Checkboxes must have an id attribute in the form cb0, cb1...
	 * @param The number of box to 'check'
	 */
	function checkAll_xtd ( n ) {
		var f = document.adminForm;
		var c = f.toggle1.checked;
		var n2 = 0;
		for ( i=0; i < n; i++ ) {
			cb = eval( 'f.cb1' + i );
			if (cb) {
				cb.checked = c;
				n2++;
			}
		}
		if (c) {
			document.adminForm.boxchecked.value = n2;
		} else {
			document.adminForm.boxchecked.value = 0;
		}
	}
</script>
<form action="index2.php?option=com_trash" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th class="trash"><?php echo _TRASH?></th>
		</tr>
	</table>

	<table class="adminlist" width="90%">
		<tr>
			<th width="20">#</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($content); ?>);" />
			</th>
			<th width="20px">&nbsp;</th>
			<th class="title">
						<?php echo _CAPTION?>
			</th>
			<th>
						<?php echo _SECTION?>
			</th>
			<th>
						<?php echo _CATEGORY?>
			</th>
			<th width="70px">
			ID
			</th>
		</tr>
				<?php
				$k = 0;
				$i = 0;
				$n = count($content);
        if($n != 0){
				foreach($content as $row) {
					?>
		<tr class="<?php echo "row".$k; ?>">
			<td align="center" width="30px">
							<?php echo $i + 1 + $pageNav->limitstart; ?>
			</td>
			<td width="20px" align="center">
                <?php echo "<input type=\"checkbox\" id=\"cb$i\" name=\"mid[]\" value=\"$row->id\" onclick=\"isChecked(this.checked);\" />"; ?>
            </td>
			<td width="20px"></td>
			<td class="jtd_nowrap" align="left">
							<?php
							echo $row->title;
							?>
			</td>
			<td align="center" width="20%">
							<?php
							echo $row->sectname;
							?>
			</td>
			<td align="center" width="20%">
							<?php
							echo $row->catname;
							?>
			</td>
			<td align="center">
							<?php
							echo $row->id;
							?>
			</td>
		</tr>
					<?php
					$k = 1 - $k;
					$i++;
				}
    }
				?>
	</table>
			<?php echo $pageNav->getListFooter(); ?>
	<input type="hidden" name="option" value="com_trash" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}


	/**
	 * A delete confirmation page
	 * Writes list of the items that have been selected for deletion
	 */
	public static function showDelete($option,$cid,$items,$type) {

		$mainframe = mosMainFrame::getInstance();
		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/file_ico';
		?>
<form action="index2.php" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th><?php echo _OBJECT_DELETION?></th>
		</tr>
	</table>
	<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="20%">
				<strong><?php echo _COM_TRASH_OBJECT_COUNT?>:</strong>
				<br />
				<font color="#000066"><strong><?php echo count($cid); ?></strong></font>
				<br /><br />
			</td>
			<td align="left" valign="top" width="25%">
				<strong><?php echo _OBJECTS_TO_DELETE?>:</strong>
				<br />
						<?php
						echo "<ol>";
						foreach($items as $item) {
							echo "<li>".$item->name."</li>";
						}
						echo "</ol>";
						?>
			</td>
			<td valign="top">
						<?php echo _THIS_ACTION_WILL_DELETE_O_FOREVER?>
				<br /><br /><br />
				<div style="border: 1px dotted gray; width: 70px; padding: 10px; margin-left: 50px;">
					<a class="toolbar" href="javascript:if (confirm('<?php echo _REALLY_DELETE_OBJECTS?>')){ submitbutton('delete');}">
						<img name="remove" src="<?php echo $cur_file_icons_path;?>/delete.png" alt="<?php echo _DELETE?>" border="0" align="middle" />
						&nbsp;<?php echo _DELETE?>
					</a>
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="1" />
	<input type="hidden" name="type" value="<?php echo $type; ?>" />
			<?php
			foreach($cid as $id) {
				echo "\n<input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
			}
			?>
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}


	/**
	 * A restore confirmation page
	 * Writes list of the items that have been selected for restore
	 */
	public static function showRestore($option,$cid,$items,$type) {

		$mainframe = mosMainFrame::getInstance();
		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';
		?>
<form action="index2.php" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th><?php echo _OBJECT_RESTORE?></th>
		</tr>
	</table>
	<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="20%">
				<strong><?php echo _COM_TRASH_OBJECT_COUNT?>:</strong>
				<br />
				<font color="#000066"><strong><?php echo count($cid); ?></strong></font>
				<br /><br />
			</td>
			<td align="left" valign="top" width="25%">
				<strong><?php echo _OBECTS_TO_RESTORE?>:</strong>
				<br />
						<?php
						echo "<ol>";
						foreach($items as $item) {
							echo "<li>".$item->name."</li>";
						}
						echo "</ol>";
						?>
			</td>
			<td valign="top">
						<?php echo _THIS_ACTION_WILL_RESTORE_O_FOREVER?>
				<br /><br /><br />
				<div style="border: 1px dotted gray; width: 120px; height:25px; padding: 10px; margin-left: 50px;">
					<a class="toolbar" href="javascript:if (confirm('<?php echo _REALLY_RESTORE_OBJECTS?>')){ submitbutton('restore');}">
						<img name="restore" src="<?php echo $cur_file_icons_path;?>/restore.png" alt="<?php echo _RESTORE?>" border="0" align="left" />
						&nbsp;<?php echo _RESTORE?>
					</a>
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="1" />
	<input type="hidden" name="type" value="<?php echo $type; ?>" />
			<?php
			foreach($cid as $id) {
				echo "\n<input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
			}
			?>
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}
}