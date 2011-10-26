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
 * @subpackage Newsfeeds
 */
class HTML_newsfeeds {

	function showNewsFeeds(&$rows,&$lists,$pageNav,$option) {
		global $my,$mosConfig_cachepath;

		$mainframe = mosMainFrame::getInstance();
		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';

		mosCommonHTML::loadOverlib();
		?>
<form action="index2.php" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th>
						<?php echo _NEWSFEEDS_MANAGEMENT?>
			</th>
			<td width="right">
						<?php echo $lists['category']; ?>
			</td>
		</tr>
	</table>

	<table class="adminlist">
		<tr>
			<th width="20">#</th>
			<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title"><?php echo _NEWSFEED_TITLE?></th>
			<th width="5%"><?php echo _NEWSFEED_ON_SITE?></th>
			<th colspan="2" width="5%"><?php echo _ORDERING?></th>
			<th class="title" width="20%"><?php echo _CATEGORY?></th>
			<th width="5%" class="jtd_nowrap"><?php echo _NEWSFEEDS_NUM_OF_CONTENT_ITEMS?></th>
			<th width="10%"><?php echo _NEWSFEED_CACHE_TIME?></th>
		</tr>
				<?php
				$k = 0;
				$f = count($rows);
				for($i = 0,$n = $f; $i < $n; $i++) {
					$row = &$rows[$i];
					mosMakeHtmlSafe($row);
					$link = 'index2.php?option=com_newsfeeds&task=editA&hidemainmenu=1&id='.$row->id;

					$img = $row->published?'tick.png':'publish_x.png';
					$task = $row->published?'unpublish':'publish';
					$alt = $row->published?_PUBLISHED:_UNPUBLISHED;
					$checked = mosCommonHTML::CheckedOutProcessing($row,$i);

					$row->cat_link = 'index2.php?option=com_categories&section=com_newsfeeds&task=editA&hidemainmenu=1&id='.
							$row->catid;
					?>
		<tr class="<?php echo 'row'.$k; ?>">
			<td align="center"><?php echo $pageNav->rowNumber($i); ?></td>
			<td><?php echo $checked; ?></td>
			<td>
							<?php
							if($row->checked_out && ($row->checked_out != $my->id)) {
								?>
								<?php echo $row->name; ?>
				&nbsp;[ <i><?php echo _CHECKED_OUT;?></i> ]
								<?php
							} else {
								?>
				<a href="<?php echo $link; ?>" title="<?php echo _CHANGE_NEWSFEED?>"><?php echo $row->name; ?></a>
								<?php
							}
							?>
			</td>
			<td width="10%" align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task; ?>')">
					<img src="<?php echo $cur_file_icons_path;?>/<?php echo $img; ?>" border="0" alt="<?php echo $alt; ?>" />
				</a>
			</td>
			<td align="center"><?php echo $pageNav->orderUpIcon($i); ?></td>
			<td align="center"><?php echo $pageNav->orderDownIcon($i,$n); ?></td>
			<td>
				<a href="<?php echo $row->cat_link; ?>" title="<?php echo _CHANGE_CATEGORY?>"><?php echo $row->catname; ?></a>
			</td>
			<td align="center"><?php echo $row->numarticles; ?></td>
			<td align="center"><?php echo $row->cache_time; ?></td>
		</tr>
					<?php
					$k = 1 - $k;
				}
				?>
	</table>
			<?php echo $pageNav->getListFooter(); ?>

	<table class="adminform">
		<tr>
			<td>
				<table align="center">
							<?php
							$visible = 0;
							// check to hide certain paths if not super admin
							if($my->gid == 25) {
								$visible = 1;
							}
							mosHTML::writableCell($mosConfig_cachepath,0,'<strong>'._CACHE_DIR.'</strong> ',$visible);
							?>
				</table>
			</td>
		</tr>
	</table>

	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0">
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}


	function editNewsFeed(&$row,&$lists,$option) {
		mosMakeHtmlSafe($row,ENT_QUOTES);
		?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		// do field validation
		if (form.name.value == '') {
			alert( "<?php echo _PLEASE_ENTER_NEWSFEED_NAME?>" );
		} else if (form.catid.value == 0) {
			alert( "<?php echo _WARNCAT?>" );
		} else if (form.link.value == '') {
			alert( "<?php echo _PLEASE_ENTER_NEWSFEED_LINK?>" );
		} else if (getSelectedValue('adminForm','catid') < 0) {
			alert( "<?php echo _WARNCAT?>" );
		} else if (form.numarticles.value == "" || form.numarticles.value == 0) {
			alert( "<?php echo _PLEASE_ENTER_NEWSFEED_NUM_OF_CONTENT_ITEMS?>" );
		} else if (form.cache_time.value == "" || form.cache_time.value == 0) {
			alert( "<?php echo _PLEASE_ENTER_NEWSFEED_CACHE_TIME?>" );
		} else {
			submitform( pressbutton );
		}
	}
</script>

<form action="index2.php" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th class="edit"><?php echo _NEWSFEED_TITLE?>: <small><?php echo $row->id?_EDITING:_CREATION; ?></small> <small><small>[ <?php echo $row->name; ?> ]</small></small></th>
		</tr>
	</table>

	<table class="adminform">
		<tr>
			<th colspan="2"><?php echo _DETAILS?></th>
		</tr>
		<tr>
			<td><?php echo _NAME?></td>
			<td><input class="inputbox" type="text" size="40" name="name" value="<?php echo $row->name; ?>"></td>
		</tr>
		<tr>
			<td><?php echo _CATEGORY?></td>
			<td><?php echo $lists['category']; ?></td>
		</tr>
		<tr>
			<td><?php echo _NEWSFEED_LINK?></td>
			<td><input class="inputbox" type="text" size="60" name="link" value="<?php echo $row->link; ?>"></td>
		</tr>
		<tr>
			<td><?php echo _NEWSFEEDS_NUM_OF_CONTENT_ITEMS?></td>
			<td><input class="inputbox" type="text" size="2" name="numarticles" value="<?php echo $row->numarticles; ?>"></td>
		</tr>
		<tr>
			<td><?php echo _NEWSFEED_CACHE_TIME?></td>
			<td><input class="inputbox" type="text" size="4" name="cache_time" value="<?php echo $row->cache_time; ?>"></td>
		</tr>
		<tr>
			<td><?php echo _ORDER_DROPDOWN?></td>
			<td><?php echo $lists['ordering']; ?></td>
		</tr>
		<tr>
			<td valign="top" align="right"><?php echo _NEWSFEED_DECODE_FROM_UTF?>:</td>
			<td><?php echo $lists['code']; ?></td>
		</tr>
		<tr>
			<td valign="top" align="right"><?php echo _PUBLISHED?>:</td>
			<td><?php echo $lists['published']; ?></td>
		</tr>
	</table>

	<input type="hidden" name="id" value="<?php echo $row->id; ?>">
	<input type="hidden" name="option" value="<?php echo $option; ?>">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}
}