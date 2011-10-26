<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined( '_VALID_MOS' ) or die();

?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		if (!getSelectedValue( 'adminForm', 'sectcat' )) {
			alert( "<?php echo _SELECT_CAT_TO_MOVE_OBJECTS?>" );
		} else {
			submitform( pressbutton );
		}
	}
</script>
<form action="index2.php" method="post" name="adminForm">
	<br />
	<table class="adminheading">
		<tr>
			<th class="edit">
				<?php echo _COPYING_CONTENT_ITEMS?>
			</th>
		</tr>
	</table>

	<br />
	<table class="adminform">
		<tr>
			<td align="left" valign="top" width="40%">
				<strong><?php echo _COPY_INTO_CAT_SECT?>:</strong>
				<br />
				<?php echo $sectCatList; ?>
				<br /><br />
			</td>
			<td align="left" valign="top">
				<strong><?php echo _OBJECTS_TO_COPY?>:</strong>
				<br />
				<?php
				echo "<ol>";
				foreach($items as $item) {
					echo "<li>".$item->title."</li>";
				}
				echo "</ol>";
				?>
			</td>
		</tr>
	</table>
	<br /><br />

	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" />
	<input type="hidden" name="task" value="" />
	<?php
	foreach($cid as $id) {
		echo "\n<input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
	}
	?>
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>