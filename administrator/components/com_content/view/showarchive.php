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
		if (pressbutton == 'remove') {
			if (document.adminForm.boxchecked.value == 0) {
				alert('<?php echo _CHOOSE_OBJECTS_TO_TRASH?>');
			} else if ( confirm('<?php echo _WANT_TO_TRASH?>')) {
				submitform('remove');
			}
		} else {
			submitform(pressbutton);
		}
	}
</script>
<form action="index2.php" method="post" name="adminForm">

	<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2">
				<?php
				if($all) {
					?>
					<?php echo _ARCHIVE?> <small><small>[ <?php echo _ALL_SECTIONS?> ]</small></small>
					<?php
				} else {
					?>
					<?php echo _ARCHIVE?> <small><small>[ <?php echo _SECTION?>: <?php echo $section->title; ?> ]</small></small>
					<?php
				}
				?>
			</th>
			<?php
			if($all) {
				?>
			<td align="right" rowspan="2" valign="top"><?php echo $lists['sectionid']; ?></td>
				<?php
			}
			?>
			<td align="right" valign="top"><?php echo $lists['catid']; ?></td>
			<td valign="top"><?php echo $lists['authorid']; ?></td>
		</tr>
		<tr>
			<td align="right"><?php echo _FILTER?>:</td>
			<td>
				<input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
		</tr>
	</table>
	<table class="adminlist">
		<tr>
			<th width="5">#</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
			</th>
			<th class="title"><?php echo _CAPTION?></th>
			<th width="2%"><?php echo _ORDER_DROPDOWN?></th>
			<th width="1%">
				<a href="javascript: saveorder( <?php echo count($rows) - 1; ?> )"><img src="<?php echo $cur_file_icons_path;?>/filesave.png" border="0" width="16" height="16" alt="<?php echo _SAVE_ORDER?>" /></a>
			</th>
			<th width="15%" align="left"><?php echo _CATEGORY?></th>
			<th width="15%" align="left"><?php echo _AUTHOR?></th>
			<th align="center" width="10"><?php echo _DATE?></th>
		</tr>
		<?php
		$k = 0;
		for($i = 0,$n = count($rows); $i < $n; $i++) {
			$row = &$rows[$i];

			$row->cat_link = 'index2.php?option=com_categories&task=editA&hidemainmenu=1&id='.$row->catid;

			if($acl->acl_check('administration','manage','users',$my->usertype,'components','com_users')) {
				if($row->created_by_alias) {
					$author = $row->created_by_alias;
				} else {
					$linkA = 'index2.php?option=com_users&task=editA&hidemainmenu=1&id='.$row->created_by;
					$author = '<a href="'.$linkA.'" title="'._CHANGE_USER_DATA.'">'.$row->author.'</a>';
				}
			} else {
				if($row->created_by_alias) {
					$author = $row->created_by_alias;
				} else {
					$author = $row->author;
				}
			}

			$date = mosFormatDate($row->created,'%x');
			?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $pageNav->rowNumber($i); ?></td>
			<td width="20"><?php echo mosHTML::idBox($i,$row->id); ?></td>
			<td><?php echo $row->title; ?></td>
			<td align="center" colspan="2">
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
			</td>
			<td>
				<a href="<?php echo $row->cat_link; ?>" title="<?php echo _CHANGE_CATEGORY?>">
						<?php echo $row->name; ?>
				</a>
			</td>
			<td><?php echo $author; ?></td>
			<td><?php echo $date; ?></td>
		</tr>
			<?php
			$k = 1 - $k;
		}
		?>
	</table>
	<?php echo $pageNav->getListFooter(); ?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="sectionid" value="<?php echo $section->id; ?>" />
	<input type="hidden" name="task" value="showarchive" />
	<input type="hidden" name="returntask" value="showarchive" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>