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
 * @subpackage Categories
 */
class categories_html {

	/**
	 * Записывает список категорий для раздела
	 * @param  $rows - масссив данных
	 * @param  $section - раздел категорий
	 * @param  $section_name - название раздела
	 * @param  $pageNav - навигация
	 * @param string $type
	 * @return void
	 * @modification 18.02.2012 GoDr
	 */
	function show(&$rows,$section,$section_name,&$pageNav, $type = '') {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();

		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';
		mosCommonHTML::loadOverlib();
		?>
<form action="index2.php" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<?php if($section == 'content') { ?>
			<th class="categories"><?php echo _CONTENT_CATEGORIES?> <small>[ <?php echo _ALL_CONTENT?> ]</small></th>
			<?php } else { ?>
			<th class="categories"><?php echo _CATEGORIES?> <small>[ <?php echo $section_name; ?> ]</small></th>
			<?php } ?>
		</tr>
	</table>
	<table class="adminlist">
		<tr>
			<th width="10" align="left">#</th>
			<th width="20"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title"><?php echo _NAME?></th>
			<th width="8%"><?php echo _PUBLISHED?></th>
					<?php
					if($section != 'content') {
						?>
			<th colspan="2" width="5%"><?php echo _ORDERING?></th>
						<?php
					}
					?>
			<th width="2%"><?php echo _ORDER_DROPDOWN?></th>
			<th width="1%">
				<a href="javascript: saveorder( <?php echo count($rows) - 1; ?> )"><img src="<?php echo $cur_file_icons_path;?>/filesave.png" border="0" width="16" height="16" alt="Сохранить порядок" /></a>
			</th>
			<th width="8%"><?php echo _ACCESS?></th>
					<?php
					if($section == 'content') {
						?>
			<th width="12%" align="left"><?php echo _SECTION?></th>
						<?php
					}
					?>
					<?php
					if($type == 'content') {
						?>
			<th width="6%"><?php echo _ACTIVE?></th>
			<th width="6%"><?php echo _IN_TRASH?></th>
						<?php
					} else {
						?>
			<th width="20%"></th>
						<?php
					}
					?>
			<th width="5%" class="jtd_nowrap">ID</th>
		</tr>
				<?php
				$k = 0;
				$num = count($rows);
				for($i = 0,$n = $num; $i < $n; $i++) {
					$row = &$rows[$i];
					mosMakeHtmlSafe($row);
					$row->sect_link = 'index2.php?option=com_sections&task=editA&hidemainmenu=1&id='.$row->section;

					$link = 'index2.php?option=com_categories&section='.$section.'&task=editA&hidemainmenu=1&id='.$row->id;
					$link_aktiv = 'index2.php?option=com_content&sectionid=0&catid='.$row->id;
					if($row->checked_out_contact_category) {
						$row->checked_out = $row->checked_out_contact_category;
					}
					$access		= mosCommonHTML::AccessProcessing($row,$i,1);
					$checked	= mosCommonHTML::CheckedOutProcessing($row,$i);
					$img		= $row->published ? 'publish_g.png' : 'publish_x.png';
					?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo $pageNav->rowNumber($i); ?></td>
			<td><?php echo $checked; ?></td>
			<td align="left">
							<?php
							if($row->checked_out_contact_category && ($row->checked_out_contact_category !=$my->id)) {
								echo stripslashes($row->name).' ( '.stripslashes($row->title).' )';
							} else {
								?>
				<a href="<?php echo $link; ?>"><?php echo stripslashes($row->name).' ( '.stripslashes($row->title).' )'; ?></a>
								<?php
							}
							?>
			</td>
			<td align="center" <?php echo ($row->checked_out && ($row->checked_out != $my->id)) ? null : 'onclick="ch_publ('.$row->id.',\'com_categories\');" class="td-state"';?>>
				<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/<?php echo $img;?>" id="img-pub-<?php echo $row->id;?>" alt="<?php echo _PUBLISHING?>" />
			</td>
						<?php
						if($section != 'content') {
							?>
			<td><?php echo $pageNav->orderUpIcon($i); ?></td>
			<td><?php echo $pageNav->orderDownIcon($i,$n); ?></td>
							<?php
						}
						?>
			<td align="center" colspan="2">
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
			</td>
			<td align="center" id="acc-id-<?php echo $row->id;?>"><?php echo $access; ?></td>
						<?php
						if($section == 'content') {
							?>
			<td align="left" id="cat-id-<?php echo $row->id;?>">
				<a onclick="ch_get_sec(<?php echo $row->id;?>,<?php echo $row->section?>);" href="javascript: ch_get_sec(<?php echo $row->id;?>,<?php echo $row->section?>);"><?php echo $row->section_name; ?></a>
			</td>
							<?php
						}
						if($type == 'content') {
							?>
			<td align="center"><a href="<?php echo $link_aktiv;?>" title="<?php echo _VIEW_CATEGORY_CONTENT?>"><?php echo $row->active; ?></a></td>
			<td align="center"><?php echo $row->trash; ?></td>
							<?php
						} else {
							?>
			<td>&nbsp;</td>
							<?php
						}
						$k = 1 - $k;
						?>
			<td align="center"><?php echo $row->id; ?></td>
		</tr>
					<?php
				}
				?>
	</table>
			<?php echo $pageNav->getListFooter(); ?>
	<input type="hidden" name="option" value="com_categories" />
	<input type="hidden" name="section" value="<?php echo $section; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="chosen" value="" />
	<input type="hidden" name="act" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="type" value="<?php echo $type; ?>" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}

	/**
	 * Writes the edit form for new and existing categories
	 * @param mosCategory The category object
	 * @param string
	 * @param array
	 */
	function edit(&$row,&$lists,$redirect,$menus) {
		if($row->image == "") {
			$row->image = 'blank.png';
		}

		if($redirect == 'content') {
			$component = _CONTENT;
		} else {
			$component = ucfirst(substr($redirect,4));
			if($redirect == 'com_contact_details') {
				$component = _CONTACT;
			}
		}
		mosMakeHtmlSafe($row,ENT_QUOTES,'description');
		?>
<script language="javascript" type="text/javascript">
	function ch_apply(){
		SRAX.get('tb-apply').className='tb-load';
		<?php getEditorContents('editor1','description'); ?>
				dax({
					url: 'ajax.index.php?option=com_mambots&task=apply',
					id:'publ-1',
					method:'post',
					form: 'adminForm',
					callback:
						function(resp){
						log('Получен ответ: ' + resp.responseText);
						mess_cool(resp.responseText);
						SRAX.get('tb-apply').className='tb-apply';
					}});
			}
			function submitbutton(pressbutton, section) {
				var form = document.adminForm;
				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				}

				if ( pressbutton == 'menulink' ) {
					if ( form.menuselect.value == "" ) {
						alert( "<?php echo _CHOOSE_MENU_PLEASE?>" );
						return;
					} else if ( form.link_type.value == "" ) {
						alert( "<?php echo _CHOOSE_MENUTYPE_PLEASE?>" );
						return;
					} else if ( form.link_name.value == "" ) {
						alert( "<?php echo _ENTER_MENUITEM_NAME?>" );
						return;
					}
				}

				if ( form.name.value == "" ) {
					alert("<?php echo _CATEGORY_NAME_IS_BLANK?>");
				} else if (form.title.value ==""){
					alert("<?php echo _ENTER_CATEGORY_NAME?>");
				} else {
		<?php getEditorContents('editor1','description'); ?>
						submitform(pressbutton);
					}
				}
</script>
<form action="index2.php" method="post" name="adminForm" id="adminForm">
	<table class="adminheading">
		<tr>
			<th class="categories">
						<?php echo _CATEGORY?>:
				<small><?php echo $row->id ? _EDIT_CATEGORY : _NEW_CATEGORY; ?></small>
				<small><small>
			[ <?php echo $component; ?>: <?php echo stripslashes($row->name); ?> ]
					</small></small>
			</th>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td valign="top" width="60%">
				<table class="adminform">
					<tr>
						<th colspan="3"><?php echo _CATEGORY_PROPERTIES?></th>
					</tr>
					<tr>
						<td><?php echo _CATEGORY_TITLE?>:</td>
						<td colspan="2">
							<input class="text_area" type="text" name="title" value="<?php echo stripslashes($row->title); ?>" size="50" maxlength="50" title="" />
						</td>
					</tr>
					<tr>
						<td><?php echo _CATEGORY_NAME?>:</td>
						<td colspan="2">
							<input class="text_area" type="text" name="name" value="<?php echo stripslashes($row->name); ?>" size="50" maxlength="255" title="" />
						</td>
					</tr>
					<tr>
						<td><?php echo _SECTION?></td>
						<td colspan="2"><?php echo $lists['section']; ?></td>
					</tr>
					<tr>
						<td><?php echo _SORT_ORDER?>:</td>
						<td colspan="2"><?php echo $lists['ordering']; ?></td>
					</tr>
					<tr>
						<td><?php echo _IMAGE?>:</td>
						<td><?php echo $lists['image']; ?></td>
						<td rowspan="5" width="50%">
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
					<tr>
						<td><?php echo _IMAGE_POSTITION?>:</td>
						<td><?php echo $lists['image_position']; ?></td>
					</tr>
					<tr>
						<td><?php echo _ACCESS?>:</td>
						<td><?php echo $lists['access']; ?></td>
					</tr>
					<tr>
						<td><?php echo _PUBLISHED?>:</td>
						<td><?php echo $lists['published']; ?></td>
					</tr>
					<tr>
						<td valign="top" colspan="2"><?php echo _DESCRIPTION?>:</td>
					</tr>
					<tr>
						<td colspan="3">
									<?php
									// parameters : areaname, content, hidden field, width, height, rows, cols
									editorArea('editor1',$row->description,'description','100%;','300','60','20');
									?>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top" width="40%">
						<?php
						if($row->id > 0) {
							?>
				<table class="adminform">
					<tr>
						<th colspan="2"><?php echo _MENUITEM?></th>
					</tr>
					<tr>
						<td colspan="2"><?php echo _NEW_MENUITEM_IN_YOUR_MENU?>
							<br /><br />
						</td>
					</tr>
					<tr>
						<td valign="top" width="120"><?php echo _CHOOSE_MENU_PLEASE?>:</td>
						<td><?php echo $lists['menuselect']; ?></td>
					</tr>
					<tr>
						<td valign="top" width="120"><?php echo _CHOOSE_MENUTYPE_PLEASE?>:</td>
						<td><?php echo $lists['link_type']; ?></td>
					</tr>
					<tr>
						<td valign="top" width="120"><?php echo _MENU_NAME?>:</td>
						<td>
							<input type="text" name="link_name" class="inputbox" value="" size="25" />
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<input name="menu_link" type="button" class="button" value="<?php echo _CREATE_MENU_ITEM?>" onClick="submitbutton('menulink');" />
						</td>
					</tr>
					<tr>
						<th colspan="2"><?php echo _EXISTED_MENU_ITEMS?></th>
					</tr>
								<?php
								if($menus == null) {
									?>
					<tr>
						<td colspan="2"><?php echo _NOT_EXISTS?></td>
					</tr>
									<?php
								} else {
									mosCommonHTML::menuLinksSecCat($menus);
								}
								?>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
				</table>
							<?php
						} else {
							?>
				<table class="adminform" width="40%">
					<tr>
						<th>&nbsp;</th>
					</tr>
					<tr>
						<td><?php echo _MENU_LINK_AVAILABLE_AFTER_SAVE?></td>
					</tr>
				</table> <br />
							<?php
						}
						// content
						if($row->section > 0 || $row->section == 'content') {
							$c_templates = new ContentTemplate;
							?>
							<?php $curr_templates = $c_templates->parse_curr_templates($row->templates); ?>
				<table class="adminform">
					<tr>
						<th colspan="2"><?php echo _TEMPLATES?></th>
					</tr>
					<tr>
						<td width="200"><?php echo _CATEGORIES_BLOG?>:</td>
						<td><?php echo $c_templates->templates_select_list('category_blog', $curr_templates); ?>   </td>
					</tr>
					<tr>
						<td width="200"><?php echo _CATEGORIES_ARHIVE?>:</td>
						<td><?php echo $c_templates->templates_select_list('category_archive', $curr_templates); ?> </td>
					</tr>
					<tr>
						<td><?php echo _CATEGORIES_TABLE?>:</td>
						<td><?php echo $c_templates->templates_select_list('category_table', $curr_templates); ?>  </td>
					</tr>
					<tr>
						<td><?php echo _TEMPLATE_ITEM_SHOW?>:</td>
						<td><?php echo $c_templates->templates_select_list('item_full', $curr_templates); ?> </td>
					</tr>
				</table>
				<br />
				<table class="adminform">
					<tr>
						<th colspan="2"><?php echo _IMAGES_DIRS?></th>
					</tr>
					<tr>
						<td colspan="2"><?php echo $lists['folders']; ?></td>
					</tr>
				</table>
							<?php
						}
						?>
			</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="com_categories" />
	<input type="hidden" name="oldtitle" value="<?php echo $row->title; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="sectionid" value="<?php echo $row->section; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}

	/**
	 * Form to select Section to copy Category to
	 */
	function copyCategorySelect($option,$cid,$SectionList,$items,$sectionOld,$contents,
			$redirect) {
		?>
<form action="index2.php" method="post" name="adminForm">
	<br />
	<table class="adminheading">
		<tr>
			<th class="categories">
						<?php echo _CATEGORY_COPYING?>
			</th>
		</tr>
	</table>

	<br />
	<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (!getSelectedValue( 'adminForm', 'sectionmove' )) {
				alert( "<?php echo _CHOOSE_CAT_SECTION_TO_COPY?>" );
			} else {
				submitform( pressbutton );
			}
		}
	</script>
	<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="30%">
				<strong><?php echo _COPY_TO_SECTION?>:</strong>
				<br />
						<?php echo $SectionList ?>
				<br /><br />
			</td>
			<td align="left" valign="top" width="20%">
				<strong><?php echo _CATS_TO_COPY?>:</strong>
				<br />
						<?php
						echo "<ol>";
						foreach($items as $item) {
							echo "<li>".$item->name."</li>";
						}
						echo "</ol>";
						?>
			</td>
			<td valign="top" width="20%">
				<strong><?php echo _CONTENT_ITEMS_TO_COPY?>:</strong>
				<br />
						<?php
						echo "<ol>";
						foreach($contents as $content) {
							echo "<li>".$content->title."</li>";
							echo "\n <input type=\"hidden\" name=\"item[]\" value=\"$content->id\" />";
						}
						echo "</ol>";
						?>
			</td>
			<td valign="top">
						<?php echo _IN_SELECTED_SECTION_WILL_BE_COPIED_ALL?>
			</td>.
		</tr>
	</table>
	<br /><br />

	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="section" value="<?php echo $sectionOld; ?>" />
	<input type="hidden" name="boxchecked" value="1" />
	<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
	<input type="hidden" name="task" value="" />
			<?php
			foreach($cid as $id) {
				echo "\n <input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
			}
			?>
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}
}