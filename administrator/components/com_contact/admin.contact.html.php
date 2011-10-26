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
 * @subpackage Contact
 */
class HTML_contact {

	function showContacts(&$rows,&$pageNav,$search,$option,&$lists) {
		global $my;
		$mainframe = mosMainFrame::getInstance();
		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';

		mosCommonHTML::loadOverlib();
		?>
<form action="index2.php" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th><?php echo _CONTACT_MANAGEMENT?></th>
			<td><?php echo _FILTER?>:</td>
			<td>
				<input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="inputbox" onChange="document.adminForm.submit();" />
			</td>
			<td width="right"><?php echo $lists['catid']; ?></td>
		</tr>
	</table>
	<table class="adminlist">
		<tr>
			<th width="20">#</th>
			<th width="20" class="title">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
			</th>
			<th class="title"><?php echo _NAME?></th>
			<th width="5%" class="title"><?php echo _ON_SITE?></th>
			<th colspan="2" class="jtd_nowrap" width="5%"><?php echo _ORDERING?></th>
			<th width="15%" align="left"><?php echo _CATEGORY?></th>
			<th class="title" width="15%"><?php echo _RELATED_WITH_USER?></th>
		</tr>
				<?php
				$k = 0;
				$z =count($rows);
				for($i = 0,$n = $z; $i < $n; $i++) {
					$row = $rows[$i];
					mosMakeHtmlSafe($row);
					$link = 'index2.php?option=com_contact&task=editA&hidemainmenu=1&id='.$row->id;

					$img = $row->published?'tick.png':'publish_x.png';
					$task = $row->published?'unpublish':'publish';
					$alt = $row->published? _PUBLISHED : _UNPUBLISHED;

					$checked = mosCommonHTML::CheckedOutProcessing($row,$i);
					$row->cat_link = 'index2.php?option=com_categories&section=com_contact_details&task=editA&hidemainmenu=1&id='.$row->catid;
					$row->user_link = 'index2.php?option=com_users&task=editA&hidemainmenu=1&id='.$row->user_id;
					?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $pageNav->rowNumber($i); ?></td>
			<td><?php echo $checked; ?></td>
			<td>
							<?php
							if($row->checked_out && ($row->checked_out != $my->id)) {
								echo $row->name;
							} else {
								?>
				<a href="<?php echo $link; ?>" title="<?php echo _CHANGE_CONTACT?>"><?php echo $row->name; ?></a>
								<?php
							}
							?>
			</td>
			<td align="center">
				<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task; ?>')">
					<img src="<?php echo $cur_file_icons_path;?>/<?php echo $img; ?>" border="0" alt="<?php echo $alt; ?>" />
				</a>
			</td>
			<td><?php echo $pageNav->orderUpIcon($i,($row->catid == @$rows[$i - 1]->catid)); ?></td>
			<td><?php echo $pageNav->orderDownIcon($i,$n,($row->catid == @$rows[$i + 1]->catid)); ?></td>
			<td>
				<a href="<?php echo $row->cat_link; ?>" title="<?php echo _CHANGE_CATEGORY?>"><?php echo $row->category; ?></a>
			</td>
			<td>
				<a href="<?php echo $row->user_link; ?>" title="<?php echo _CHANGE_USER?>"><?php echo $row->user; ?></a>
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


	function editContact(&$row,&$lists,$option,&$params) {
		mosCommonHTML::loadOverlib();
		if($row->image == '') {
			$row->image = 'blank.png';
		}

		$tabs = new mosTabs(0);

		mosMakeHtmlSafe($row,ENT_QUOTES,'misc');
		?>
<script language="javascript" type="text/javascript">
	<!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		// do field validation
		if ( form.name.value == "" ) {
			alert( "<?php echo _ENTER_NAME_PLEASE?>" );
		} else if ( form.catid.value == 0 ) {
			alert( "<?php echo _WARNCAT?>" );
		} else {
			submitform( pressbutton );
		}
	}
	//-->
</script>
<form action="index2.php" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th>
						<?php echo _ENQUIRY; ?>:
				<small><?php echo $row->id? _EDIT_CATEGORY : _NEW; ?></small>
			</th>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td width="60%" valign="top">
				<table width="100%" class="adminform">
					<tr>
						<th colspan="2"><?php echo _CONTACT_DETAILS?></th>
					</tr>
					<tr>
						<td width="30%" align="right"><?php echo _CATEGORY?>:</td>
						<td width="40%"><?php echo $lists['catid']; ?></td>
					</tr>
					<tr>
						<td width="30%" align="right"><?php echo _RELATED_WITH_USER?>:</td>
						<td><?php echo $lists['user_id']; ?></td>
					</tr>
					<tr>
						<td width="30%" align="right"><?php echo _NAME?>:</td>
						<td>
							<input class="inputbox" type="text" name="name" size="100" maxlength="100" value="<?php echo $row->name; ?>" />
						</td>
					</tr>
					<tr>
						<td align="right"><?php echo _USER_POSITION?>:</td>
						<td>
							<input class="inputbox" type="text" name="con_position" size="100" maxlength="100" value="<?php echo $row->con_position; ?>" />
						</td>
					</tr>
					<tr>
						<td align="right"><?php echo _EMAIL?>:</td>
						<td>
							<input class="inputbox" type="text" name="email_to" size="100" maxlength="100" value="<?php echo $row->email_to; ?>" />
						</td>
					</tr>
					<tr>
						<td align="right"><?php echo _ADRESS_STREET_HOUSE?>:</td>
						<td>
							<input class="inputbox" type="text" name="address" size="100" value="<?php echo $row->address; ?>" />
						</td>
					</tr>
					<tr>
						<td align="right"><?php echo _CITY?>:</td>
						<td>
							<input class="inputbox" type="text" name="suburb" size="100" maxlength="100" value="<?php echo $row->suburb; ?>" />
						</td>
					</tr>
					<tr>
						<td align="right"><?php echo _STATE?>:</td>
						<td>
							<input class="inputbox" type="text" name="state" size="100" maxlength="100" value="<?php echo $row->state; ?>" />
						</td>
					</tr>
					<tr>
						<td align="right"><?php echo _COUNTRY?>:</td>
						<td>
							<input class="inputbox" type="text" name="country" size="100" maxlength="100" value="<?php echo $row->country; ?>" />
						</td>
					</tr>
					<tr>
						<td align="right"><?php echo _POSTCODE?>:</td>
						<td>
							<input class="inputbox" type="text" name="postcode" size="100" maxlength="100" value="<?php echo $row->postcode; ?>" />
						</td>
					</tr>
					<tr>
						<td align="right"><?php echo _C_USERS_CONTACT_PHONE?>:</td>
						<td>
							<input class="inputbox" type="text" name="telephone" size="100" maxlength="100" value="<?php echo $row->telephone; ?>" />
						</td>
					</tr>
					<tr>
						<td align="right"><?php echo _C_USERS_CONTACT_FAX?>:</td>
						<td>
							<input class="inputbox" type="text" name="fax" size="100" maxlength="100" value="<?php echo $row->fax; ?>" />
						</td>
					</tr>
					<tr>
						<td colspan="2"><?php echo _ADDITIONAL_INFO?>:</td>
					</tr>
					<tr>
						<td colspan="2">
									<?php editorArea('editor1',$row->misc,'misc','99%;','350','75','30'); ?>
						</td>
					</tr>
				</table>
			</td>
			<td width="40%" valign="top">
						<?php
						$tabs->startPane("content-pane");
						$tabs->startTab(_PUBLISHING,"publish-page");
						?>
				<table width="100%" class="adminform">
					<tr>
						<th colspan="2"><?php echo _PUBLISH_INFO?></th>
					<tr>
					<tr>
						<td valign="top" align="right"><?php echo _PUBLISHED?>:</td>
						<td><?php echo $lists['published']; ?></td>
					</tr>
					<tr>
						<td valign="top" align="right"><?php echo _POSITION?>:</td>
						<td><?php echo $lists['ordering']; ?></td>
					</tr>
					<tr>
						<td valign="top" align="right"><?php echo _ACCESS?>:</td>
						<td><?php echo $lists['access']; ?></td>
					</tr>
				</table>
						<?php
						$tabs->endTab();
						$tabs->startTab(_IMAGES,"images-page");
						?>
				<table width="100%" class="adminform">
					<tr>
						<th colspan="2"><?php echo _IMAGES_INFO?></th>
					<tr>
					<tr>
						<td align="left" width="20%"><?php echo _IMAGE?>:</td>
						<td align="left"><?php echo $lists['image']; ?></td>
					</tr>
					<tr>
						<td>
						</td>
						<td>
							<script language="javascript" type="text/javascript">
								if (document.forms[0].image.options.value!=''){
									jsimg='../images/stories/' + getSelectedValue( 'adminForm', 'image' );
								} else {
									jsimg='../images/M_images/blank.png';
								}
								document.write('<img src=' + jsimg + ' name="imagelib" width="100" height="100" border="2" alt="<?php echo _PREVIEW?>" />');
							</script>
						</td>
					</tr>
				</table>
						<?php
						$tabs->endTab();
						$tabs->startTab(_PARAMETERS,"params-page");
						?>
				<table class="adminform">
					<tr>
						<th><?php echo _PARAMETERS?></th>
					</tr>
					<tr>
						<td><?php echo _CONTACT_PARAMS?>
							<br /><br />
						</td>
					</tr>
					<tr>
						<td><?php echo $params->render(); ?></td>
					</tr>
				</table>
						<?php
						$tabs->endTab();
						$tabs->endPane();
						?>
			</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}
}