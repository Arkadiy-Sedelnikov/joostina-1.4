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
 * @subpackage Weblinks
 */
class HTML_weblinks {

	public static function showWeblinks($option,&$rows,&$lists,&$search,&$pageNav) {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();
		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';

		mosCommonHTML::loadOverlib();
		?>
<form action="index2.php" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th>
						<?php echo _LINKS_MANAGEMENT?>
			</th>
			<td>
						<?php echo _FILTER?>:
			</td>
			<td>
				<input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
			<td width="right">
						<?php echo $lists['catid']; ?>
			</td>
		</tr>
	</table>

	<table class="adminlist">
		<tr>
			<th width="5">
			#
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
			</th>
			<th class="title">
						<?php echo _CAPTION?>
			</th>
			<th width="5%">
						<?php echo _ON_SITE?>
			</th>
			<th colspan="2" width="5%">
						<?php echo _ORDERING?>
			</th>
			<th width="25%" align="left">
						<?php echo _CATEGORY?>
			</th>
			<th width="5%">
						<?php echo _LINKS_HITS?>
			</th>
		</tr>
				<?php
				$k = 0;
				for($i = 0,$n = count($rows); $i < $n; $i++) {
					$row = &$rows[$i];

					$link = 'index2.php?option=com_weblinks&task=editA&hidemainmenu=1&id='.$row->id;

					$task = $row->published?'unpublish':'publish';
					$img = $row->published?'publish_g.png':'publish_x.png';
					$alt = $row->published?_PUBLISHED:_UNPUBLISHED;

					$checked = mosCommonHTML::CheckedOutProcessing($row,$i);

					$row->cat_link = 'index2.php?option=com_categories&section=com_weblinks&task=editA&hidemainmenu=1&id='.$row->catid;
					?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
							<?php echo $pageNav->rowNumber($i); ?>
			</td>
			<td>
							<?php echo $checked; ?>
			</td>
			<td>
							<?php
							if($row->checked_out && ($row->checked_out != $my->id)) {
								echo $row->title;
							} else {
								?>
				<a href="<?php echo $link; ?>" title="<?php echo _CHANGE_WEBLINK?>">
									<?php echo $row->title; ?>
				</a>
								<?php
							}
							?>
			</td>
			<td align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task; ?>')">
					<img src="<?php echo $cur_file_icons_path;?>/<?php echo $img; ?>" border="0" alt="<?php echo $alt; ?>" />
				</a>
			</td>
			<td>
							<?php echo $pageNav->orderUpIcon($i,($row->catid == @$rows[$i - 1]->catid)); ?>
			</td>
			<td>
							<?php echo $pageNav->orderDownIcon($i,$n,($row->catid == @$rows[$i + 1]->catid)); ?>
			</td>
			<td>
				<a href="<?php echo $row->cat_link; ?>" title="<?php echo _CHANGE_CATEGORY?>">
								<?php echo $row->category; ?>
				</a>
			</td>
			<td align="center">
							<?php echo $row->hits; ?>
			</td>
		</tr>
					<?php
					$k = 1 - $k;
				}
				?>
	</table>
			<?php echo $pageNav->getListFooter(); ?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0">
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}

	/**
	 * Writes the edit form for new and existing record
	 *
	 * A new record is defined when <var>$row</var> is passed with the <var>id</var>
	 * property set to 0.
	 * @param mosWeblink The weblink object
	 * @param array An array of select lists
	 * @param object Parameters
	 * @param string The option
	 */
	public static function editWeblink(&$row,&$lists,&$params,$option) {
		mosMakeHtmlSafe($row,ENT_QUOTES,'description');

		mosCommonHTML::loadOverlib();
		?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		// do field validation
		if (form.title.value == ""){
			alert( "<?php echo _ENTER_WEBLINK_TITLE?>" );
		} else if (form.catid.value == "0"){
			alert( "<?php echo _PLEASE_CHOOSE_CATEGORY?>" );
		} else if (form.url.value == ""){
			alert( "<?php echo _PLEASE_ENTER_URL?>" );
		} else {
			submitform( pressbutton );
		}
	}
</script>
<form action="index2.php" method="post" name="adminForm" id="adminForm">
	<table class="adminheading">
		<tr>
			<th>
						<?php echo _WEBLINK_URL?>:
				<small>
							<?php echo $row->id?_EDITING:_CREATION; ?>
				</small>
			</th>
		</tr>
	</table>

	<table width="100%">
		<tr>
			<td width="60%" valign="top">
				<table class="adminform">
					<tr>
						<th colspan="2">
									<?php echo _DETAILS?>
						</th>
					</tr>
					<tr>
						<td width="30%" align="right">
									<?php echo _WEBLINK_NAME?>:
						</td>
						<td width="70%">
							<input class="text_area" type="text" name="title" size="50" maxlength="250" value="<?php echo $row->title; ?>" />
						</td>
					</tr>
					<tr>
						<td valign="top" align="right">
									<?php echo _CATEGORY?>:
						</td>
						<td>
									<?php echo $lists['catid']; ?>
						</td>
					</tr>
					<tr>
						<td valign="top" align="right">
					URL:
						</td>
						<td>
							<input class="text_area" type="text" name="url" value="<?php echo $row->url; ?>" size="50" maxlength="250" />
						</td>
					</tr>
					<tr>
						<td valign="top" align="right">
									<?php echo _DESCRIPTION?>:
						</td>
						<td>
							<textarea class="text_area" cols="50" rows="5" name="description" style="width:500px" width="500"><?php echo $row->description; ?></textarea>
						</td>
					</tr>

					<tr>
						<td valign="top" align="right">
									<?php echo _SORT_ORDER?>:
						</td>
						<td>
									<?php echo $lists['ordering']; ?>
						</td>
					</tr>
					<tr>
						<td valign="top" align="right">
									<?php echo _PUBLISHED?>:
						</td>
						<td>
									<?php echo $lists['published']; ?>
						</td>
					</tr>
				</table>
			</td>
			<td width="40%" valign="top">
				<table class="adminform">
					<tr>
						<th colspan="1">
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

	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}
}