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
 * @subpackage Content
 */
class HTML_typedcontent {

	/**
	 * Writes a list of the content items
	 * @param array An array of content objects
	 */
	public static function showContent(&$rows,&$pageNav,$option,$search,&$lists) {
		global $my,$acl,$database;
		$mainframe = mosMainFrame::getInstance();
		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';
		mosCommonHTML::loadOverlib();
		?>
<form action="index2.php" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th class="edit"><?php echo _STATIC_CONTENT?></th>
			<td><?php echo _FILTER?>:&nbsp;</td>
			<td><input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="text_area" onChange="document.adminForm.submit();" /></td>
			<td>&nbsp;<?php echo _ORDERING?>:&nbsp;</td>
			<td><?php echo $lists['order']; ?></td>
			<td width="right"><?php echo $lists['authorid']; ?></td>
		</tr>
	</table>
	<table class="adminlist">
		<tr>
			<th width="5">#</th>
			<th width="5px"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title"><?php echo _CAPTION?></th>
			<th width="5%"><?php echo _PUBLISHED?></th>
			<th width="2%"><?php echo _ORDERING?></th>
			<th width="1%"><a href="javascript: saveorder( <?php echo count($rows) - 1; ?> )"><img src="<?php echo $cur_file_icons_path;?>/filesave.png" border="0" width="16" height="16" alt="<?php echo _SAVE_ORDER?>" /></a></th>
			<th width="10%"><?php echo _ACCESS?></th>
			<th width="5%">ID</th>
			<th width="1%" align="left"><?php echo _LINKS_COUNT?></th>
		</tr>
				<?php
				$k = 0;
				$nullDate = $database->getNullDate();
				$_n = count($rows);
				for($i = 0,$n = $_n; $i < $n; $i++) {
					$row = &$rows[$i];
					mosMakeHtmlSafe($row);
					$now = _CURRENT_SERVER_TIME;
					if($now <= $row->publish_up && $row->state == 1) {
						// Published
						$img = 'publish_y.png';
						$alt = _PUBLISHED;
					} else
					if(($now <= $row->publish_down || $row->publish_down == $nullDate) && $row->state == 1) {
						// Pending
						$img = 'publish_g.png';
						$alt = _PUBLISHED;
					} else
					if($now > $row->publish_down && $row->state == 1) {
						// Expired
						$img = 'publish_r.png';
						$alt = _DATE_PUBL_END;
					} elseif($row->state == 0) {
						// Unpublished
						$img = 'publish_x.png';
						$alt = _UNPUBLISHED;
					}

					// correct times to include server offset info
					$row->publish_up = mosFormatDate($row->publish_up,_CURRENT_SERVER_TIME_FORMAT);
					if(trim($row->publish_down) == $nullDate || trim($row->publish_down) == '' || trim($row->publish_down) == '-') {
						$row->publish_down = _NEVER;
					}
					$row->publish_down = mosFormatDate($row->publish_down,_CURRENT_SERVER_TIME_FORMAT);

					$times = '';
					if($row->publish_up == $nullDate) {
						$times .= "<tr><td>"._START.": "._ALWAYS."</td></tr>";
					} else {
						$times .= "<tr><td>"._START.": $row->publish_up</td></tr>";
					}
					if($row->publish_down == $nullDate || $row->publish_down == _NEVER) {
						$times .= "<tr><td>"._END.": "._WITHOUT_END."</td></tr>";
					} else {
						$times .= "<tr><td>"._END.": $row->publish_down</td></tr>";
					}

					if(!$row->access) {
						$color_access = 'style="color: green;"';
						$task_access = 'accessregistered';
					} else
					if($row->access == 1) {
						$color_access = 'style="color: red;"';
						$task_access = 'accessspecial';
					} else {
						$color_access = 'style="color: black;"';
						$task_access = 'accesspublic';
					}
					$link = 'index2.php?option=com_typedcontent&task=edit&hidemainmenu=1&id='.$row->id;
					$checked = mosCommonHTML::CheckedOutProcessing($row,$i);
					$access = mosCommonHTML::AccessProcessing($row,$i,1);
					if($acl->acl_check('administration','manage','users',$my->usertype,'components','com_users')) {
						if($row->created_by_alias) {
							$author = $row->created_by_alias;
						} else {
							$linkA = 'index2.php?option=com_users&task=editA&hidemainmenu=1&id='.$row->created_by;
							$author = '<a href="'.$linkA.'" title="'._CHANGE_USER_DATA.'">'.$row->creator.'</a>';
						}
					} else {
						if($row->created_by_alias) {
							$author = $row->created_by_alias;
						} else {
							$author = $row->creator;
						}
					}

					?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $pageNav->rowNumber($i); ?></td>
			<td><?php echo $checked; ?></td>
			<td align="left">
							<?php
							if($row->checked_out && ($row->checked_out != $my->id)) {
								echo $row->title;
								if($row->title_alias) {
									echo ' (<i>'.$row->title_alias.'</i>)';
								}
							} else {
								?>
				<a href="<?php echo $link; ?>" class="abig" title="<?php echo _EDIT_CONTENT_TYPED?>">
									<?php
									echo $row->title;
									if($row->title_alias) {
										echo ' (<i>'.$row->title_alias.'</i>)';
									}
									?></a>
								<?php
								echo '<br />'.$row->created.' : '.$author;
							}
							?>
			</td>
						<?php
						if($times) {
							?>
			<td align="center" <?php echo ($row->checked_out && ($row->checked_out != $my->id))?null:'onclick="ch_publ('.$row->id.',\'com_typedcontent\');" class="td-state"'; ?>>
				<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/<?php echo $img; ?>" id="img-pub-<?php echo $row->id; ?>" alt="<?php echo _PUBLISHING?>" />
			</td>
							<?php
						}
						?>
			<td align="center" colspan="2">
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
			</td>
			<td align="center" id="acc-id-<?php echo $row->id; ?>"><?php echo $access; ?></td>
			<td align="center"><?php echo $row->id; ?></td>
			<td align="center"><?php echo $row->links; ?></td>
		</tr>
					<?php
					$k = 1 - $k;
				}
				?>
	</table>
			<?php echo $pageNav->getListFooter(); ?>
			<?php mosCommonHTML::ContentLegend(); ?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}

	public static function edit(&$row,&$images,&$lists,&$params,$option,&$menus,$nullDate) {

		mosMakeHtmlSafe($row);
		$create_date = null;
		$mod_date = null;

		if($row->created != $nullDate) {
			$create_date = mosFormatDate($row->created,'%A, %d %B %Y %H:%M','0');
		}
		if($row->modified != $nullDate) {
			$mod_date = mosFormatDate($row->modified,'%A, %d %B %Y %H:%M','0');
		}
		$tabs = new mosTabs(1);
		// used to hide "Reset Hits" when hits = 0
		if(!$row->hits) {
			$visibility = "style='display: none; visibility: hidden;'";
		} else {
			$visibility = "";
		}
		mosCommonHTML::loadOverlib();
		mosCommonHTML::loadCalendar();
		?>
<script language="javascript" type="text/javascript">
	var folderimages = new Array;
		<?php
		$i = 0;
		foreach($images as $k => $items) {
			foreach($items as $v) {
				echo "folderimages[".$i++."] = new Array( '$k','".addslashes($v->value)."','".addslashes($v->text)."' );\t";
			}
		}
		?>
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	if ( pressbutton ==' resethits' ) {
		if (confirm('<?php echo _WANT_TO_RESET_HITCOUNT?>')){
			submitform( pressbutton );
			return;
		} else {
			return;
		}
	}
	if ( pressbutton == 'menulink' ) {
		if ( form.menuselect.value == "" ) {
			alert( "<?php echo _CHOOSE_MENU_PLEASE?>" );
			return;
		} else if ( form.link_name.value == "" ) {
			alert( "<?php echo _ENTER_MENUITEM_NAME?>" );
			return;
		}
	}
	var temp = new Array;
	for (var i=0, n=form.imagelist.options.length; i < n; i++) {
		temp[i] = form.imagelist.options[i].value;
	}
	form.images.value = temp.join( '\n' );
	try {
		document.adminForm.onsubmit();
	}
	catch(e){}
	if (trim(form.title.value) == ""){
		alert( "<?php echo _OBJECT_MUST_HAVE_TITLE?>" );
	} else if (trim(form.name.value) == ""){
		alert( "<?php echo _CONTENT_OBJECT_MUST_HAVE_NAME?>" );
	} else {
		if ( form.reset_hits.checked ) {
			form.hits.value = 0;
		} else {
		}
		<?php getEditorContents('editor1','introtext'); ?>
				submitform( pressbutton );
			}
		}
</script>
<table class="adminheading">
	<tr>
		<th class="edit"><?php echo _STATIC_CONTENT?>: <small><?php echo $row->id? _EDITING : _CREATION; ?></small></th>
	</tr>
</table>
<form action="index2.php" method="post" name="adminForm">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td width="100%" valign="top">
				<table class="adminform">
					<tr>
						<th colspan="3"><?php echo _CONTENT_INFO?></th>
					</tr>
					<tr>
						<td align="left"><?php echo _CAPTION?>:</td>
						<td width="90%">
							<input class="inputbox" type="text" name="title" size="30" maxlength="150" style="width:98%" value="<?php echo $row->title; ?>" />
						</td>
					</tr>
					<tr>
						<td align="left"><?php echo _ALIAS?>:</td>
						<td width="90%"><input class="inputbox" type="text" name="title_alias" size="30" maxlength="150" style="width:98%" value="<?php echo $row->title_alias; ?>" /></td>
					</tr>
					<tr>
						<td valign="top"><?php echo _AUTHOR?>:</td>
						<td><?php echo $lists['created_by']; ?>
							&nbsp;<?php echo _E_AUTHOR_ALIAS?>:
							<input type="text" name="created_by_alias" size="30" maxlength="100" value="<?php echo $row->created_by_alias; ?>" class="inputbox" />
						</td>
					</tr>
					<tr>
						<td valign="top" align="left" colspan="2">
									<?php echo _MAINTEXT_M?><br />
									<?php
									// parameters : areaname, content, hidden field, width, height, rows, cols
									editorArea('editor1',$row->introtext,'introtext','100%;','500','75','50');
									?>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top">
				<div id="params" style="width:410px">
							<?php
							$tabs->startPane("content-pane");
							$tabs->startTab(_PUBLISHING,"publish-page");
							?>
					<table class="adminform">
						<tr>
							<td valign="top" align="right" width="120"><?php echo _E_STATE?>:</td>
							<td><?php echo $row->state > 0? _PUBLISHED : _DRAFT_UNPUBLISHED; ?></td>
						</tr>
						<tr>
							<td valign="top" align="right"><?php echo _PUBLISHED?>:</td>
							<td><input type="checkbox" name="published" value="1" <?php echo $row->state?'checked="checked"':''; ?> /></td>
						</tr>
						<tr>
							<td valign="top" align="right"><?php echo _ACCESS?>:</td>
							<td><?php echo $lists['access']; ?></td>
						</tr>

						<tr>
							<td valign="top" align="right"><?php echo _CREATED?>:</td>
							<td>
								<input class="inputbox" type="text" name="created" id="created" size="25" maxlength="19" value="<?php echo $row->created; ?>" />
								<input name="reset" type="reset" class="button" onClick="return showCalendar('created', 'y-mm-dd');" value="...">
							</td>
						</tr>
						<tr>
							<td align="right"><?php echo _START_PUBLICATION?>:</td>
							<td>
								<input class="inputbox" type="text" name="publish_up" id="publish_up" size="25" maxlength="19" value="<?php echo $row->publish_up; ?>" />
								<input type="reset" class="button" value="..." onclick="return showCalendar('publish_up', 'y-mm-dd');">
							</td>
						</tr>
						<tr>
							<td align="right"><?php echo _END_PUBLICATION?>:</td>
							<td>
								<input class="inputbox" type="text" name="publish_down" id="publish_down" size="25" maxlength="19" value="<?php echo $row->publish_down; ?>" />
								<input type="reset" class="button" value="..." onclick="return showCalendar('publish_down', 'y-mm-dd');">
							</td>
						</tr>
						<tr>
							<td><strong><?php echo _TAGS?>:</strong> </td>
							<td><input type="text" name="tags" style="width:90%" size="30" maxlength="100" value="<?php echo $row->tags; ?>" class="text_area" /></td>
						</tr>
					</table>
					<br />
					<table class="adminform" width="100%">
								<?php
								if($row->id) {
									?>
						<tr>
							<td>ID:</td>
							<td><?php echo $row->id; ?></td>
						</tr>
									<?php
								}
								?>
						<tr>
							<td width="120" valign="top" align="right"><?php echo _E_STATE?>:</td>
							<td><?php echo $row->state > 0? _PUBLISHED :($row->state < 0? _IN_ARCHIVE :_DRAFT_UNPUBLISHED); ?></td>
						</tr>
						<tr>
							<td valign="top" align="right"><?php echo _VIEW_COUNT?>:</td>
							<td>
										<?php echo $row->hits ? $row->hits : 0; ?>
								<div <?php echo $visibility; ?>>
									<input name="reset_hits" type="button" class="button" value="<?php echo _RESET?>" onClick="submitbutton('resethits');">
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" align="right"><?php echo _VERSION?>:</td>
							<td><?php echo $row->version; ?></td>
						</tr>
						<tr>
							<td valign="top" align="right"><?php echo _CREATED?>:</td>
							<td><?php echo $create_date ? $create_date : _NEW_DOCUMENT;?></td>
						</tr>
						<tr>
							<td valign="top" align="right"><?php echo _E_LAST_MOD?>:</td>
							<td><?php echo $mod_date ? $mod_date.'<br />'.$row->modifier : _NOT_CHANGED;?></td>
						</tr>
						<tr>
							<td valign="top" align="right"><?php echo _END_PUBLICATION?>:</td>
							<td><?php echo $row->publish_down; ?></td>
						</tr>
					</table>
							<?php
							$tabs->endTab();
							$tabs->startTab(_IMAGES,"images-page");
							?>
					<table class="adminform">
						<tr>
							<td colspan="2">
								<table width="100%">
									<tr>
										<td width="48%" valign="top">
											<div align="center">
														<?php echo _GALLERY_IMAGES?>:<br />
														<?php echo $lists['imagefiles']; ?>
											</div>
										</td>
										<td width="2%">
											<input class="button" type="button" value=">>" onclick="addSelectedToList('adminForm','imagefiles','imagelist')" title="<?php echo _ADD?>"/>
											<input class="button" type="button" value="<<" onclick="delSelectedFromList('adminForm','imagelist')" title="<?php echo _DELETE?>"/>
										</td>
										<td width="48%">
											<div align="center">
														<?php echo _CONTENT_IMAGES?>:
												<br />
														<?php echo $lists['imagelist']; ?>
												<br />
												<input class="button" type="button" value="<?php echo _TO_TOP?>" onclick="moveInList('adminForm','imagelist',adminForm.imagelist.selectedIndex,-1)" />
												<input class="button" type="button" value="<?php echo _TO_BOTTOM?>" onclick="moveInList('adminForm','imagelist',adminForm.imagelist.selectedIndex,+1)" />
											</div>
										</td>
									</tr>
								</table>
										<?php echo _SUBFOLDER?>: <?php echo $lists['folders']; ?>
							</td>
						</tr>
						<tr valign="top">
							<td>
								<div align="center">
											<?php echo _IMAGE_EXAMPLE?>:<br />
									<img name="view_imagefiles" src="../images/M_images/blank.png" width="100" />
								</div>
							</td>
							<td valign="top">
								<div align="center">
											<?php echo _ACTIVE_IMAGE?>:<br />
									<img name="view_imagelist" src="../images/M_images/blank.png" width="100" />
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
										<?php echo _EDITING_SELECTED_IMAGE?>:
								<table>
									<tr>
										<td align="right"><?php echo _SOURCE?></td>
										<td><input class="text_area" type="text" name= "_source" value="" /></td>
									</tr>
									<tr>
										<td align="right"><?php echo _ALIGN?></td>
										<td><?php echo $lists['_align']; ?></td>
									</tr>
									<tr>
										<td align="right"><?php echo _E_ALT?></td>
										<td><input class="text_area" type="text" name="_alt" value="" /></td>
									</tr>
									<tr>
										<td align="right"><?php echo _E_BORDER?></td>
										<td><input class="text_area" type="text" name="_border" value="" size="3" maxlength="1" /></td>
									</tr>
									<tr>
										<td align="right"><?php echo _CAPTION?>:</td>
										<td><input class="text_area" type="text" name="_caption" value="" size="30" /></td>
									</tr>
									<tr>
										<td align="right"><?php echo _CAPTION_POSITION?>:</td>
										<td><?php echo $lists['_caption_position']; ?></td>
									</tr>
									<tr>
										<td align="right"><?php echo _CAPTION_ALIGN?>:</td>
										<td><?php echo $lists['_caption_align']; ?></td>
									</tr>
									<tr>
										<td align="right"><?php echo _CAPTION_WIDTH?>:</td>
										<td><input class="text_area" type="text" name="_width" value="" size="5" maxlength="5" /></td>
									</tr>
									<tr>
										<td colspan="2"><input class="button" type="button" value="<?php echo _APPLY?>" onClick="applyImageProps()" /></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
							<?php
							$tabs->endTab();
							$tabs->startTab(_PARAMETERS,"params-page");
							?>
					<table class="adminform">
						<tr>
							<td><?php echo $params->render(); ?></td>
						</tr>
					</table>
							<?php
							$tabs->endTab();
							$tabs->startTab(_METADATA,"metadata-page");
							?>
					<table class="adminform">
						<tr>
							<td align="left"><?php echo _DESCRIPTION?>:<br />
								<textarea class="inputbox" cols="40" rows="5" name="metadesc" style="width:98%"><?php echo str_replace('&','&amp;',$row->metadesc); ?></textarea>
							</td>
						</tr>
						<tr>
							<td align="left"><?php echo _E_M_KEY?><br />
								<textarea class="inputbox" cols="40" rows="5" name="metakey" style="width:98%"><?php echo str_replace('&','&amp;',$row->metakey); ?></textarea>
							</td>
						</tr>
					</table>
							<?php
							$tabs->endTab();
							$tabs->startTab(_MENU_LINK,"link-page");
							?>
					<table class="adminform">
						<tr>
							<td colspan="2"><?php echo _MENU_LINK_3?></td>
						</tr>
						<tr>
							<td valign="top"><?php echo _CHOOSE_MENU_PLEASE?></td>
							<td><?php echo $lists['menuselect']; ?></td>
						</tr>
						<tr>
							<td valign="top"><?php echo _MENU_NAME?></td>
							<td><input type="text" name="link_name" class="inputbox" value="" size="30" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input name="menu_link" type="button" class="button" value="<?php echo _CREATE_MENU_ITEM?>" onClick="submitbutton('menulink');" /></td>
						</tr>
						<tr><th colspan="2"><?php echo _EXISTED_MENU_LINKS?></th>
						</tr>
								<?php
								if($menus == null) {
									?>
						<tr>
							<td colspan="2"><?php echo _NOT_EXISTS?></td>
						</tr>
									<?php
								} else {
									mosCommonHTML::menuLinksContent($menus);
								}
								?>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
					</table>
							<?php
							$tabs->endTab();
							$tabs->startTab(_TEMPLATES,"template-page");
							?>
					<table class="adminform">
						<tr>
							<th colspan="2"><?php echo _TEMPLATES?></th>
						</tr>
								<?php
								$templates = new ContentTemplate;
								$curr_templates = $templates->parse_curr_templates($row->templates);
								?>
						<tr>
							<td><?php echo _TEMPLATE_ITEM_SHOW?>: </td>
							<td><?php echo $templates->templates_select_list('item_static', $curr_templates); ?> </td>
						</tr>
					</table>
							<?php
							$tabs->endTab();
							$tabs->endPane();
							?>
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" name="images" value="" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="hits" value="<?php echo $row->hits; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}
}