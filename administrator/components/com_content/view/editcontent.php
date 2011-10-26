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
	<!--
	var sectioncategories = new Array;
<?php
$i = 0;
foreach($sectioncategories as $k => $items) {
	foreach($items as $v) {
		echo "sectioncategories[".$i++."] = new Array( '$k','".addslashes($v->id)."','".addslashes($v->name)."' );\t";
	}
}
?>
<?php
// отключение вкладки "Изображения"
if(!$mainframe->getCfg('disable_image_tab')) { ?>
	var folderimages = new Array;
	<?php
	$i = 0;
	foreach($images as $k => $items) {
		foreach($items as $v) {
			echo "folderimages[".$i++."] = new Array( '$k','".addslashes(ampReplace($v->value))."','".addslashes(ampReplace($v->text))."' );\t";
		}
	}
}
?>
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if ( pressbutton == 'menulink' ) {
			if ( form.menuselect.value == "" ) {
				alert( "<?php echo _CHOOSE_MENU_PLEASE?>" );
				return;
			} else if ( form.link_name.value == "" ) {
				alert( "<?php echo _ENTER_MENUITEM_NAME?>" );
				return;
			}
		}

		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
<?php
// отключение вкладки "Изображения"
if(!$mainframe->getCfg('disable_image_tab')) {
	?>
			var temp = new Array;
			for (var i=0, n=form.imagelist.options.length; i < n; i++) {
				temp[i] = form.imagelist.options[i].value;
			}
			form.images.value = temp.join( '\n' );
	<?php } ?>
			// do field validation
			if (form.title.value == ""){
				alert( "<?php echo _OBJECT_MUST_HAVE_TITLE?>" );
			} else if (form.sectionid.value == "-1"){
				alert( "<?php echo _PLEASE_CHOOSE_SECTION?>" );
			} else if (form.catid.value == "-1"){
				alert( "<?php echo _PLEASE_CHOOSE_CATEGORY?>" );
			} else if (form.catid.value == ""){
				alert( "<?php echo _PLEASE_CHOOSE_CATEGORY?>" );
			} else {
<?php getEditorContents('editor1','introtext'); ?>
<?php getEditorContents('editor2','fulltext'); ?>
<?php getEditorContents('editor3','notetext'); ?>
			submitform( pressbutton );
		}
	}
	function ch_apply(){
		var form = document.adminForm;
		SRAX.get('tb-apply').className='tb-load';
<?php getEditorContents('editor1','introtext'); ?>
<?php getEditorContents('editor2','fulltext'); ?>
<?php getEditorContents('editor3','notetext'); ?>
<?php
// отключение вкладки "Изображения"
if(!$mainframe->getCfg('disable_image_tab')) {
	?>
			var temp = new Array;
			for (var i=0, n=form.imagelist.options.length; i < n; i++) {
				temp[i] = form.imagelist.options[i].value;
			}
			form.images.value = temp.join( '\n' );
	<?php } ?>
			dax({
				url: 'ajax.index.php?option=com_content&task=apply',
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
		function ch_metakey(){
<?php getEditorContents('editor1','introtext'); ?>
<?php getEditorContents('editor2','fulltext'); ?>
<?php getEditorContents('editor3','notetext'); ?>
		dax({
			url: 'ajax.index.php?option=com_content&task=metakey',
			id:'publ-1',
			method:'post',
			form: 'adminForm',
			callback:
				function(resp){
				log('Получен ответ: ' + resp.responseText);
				SRAX.get('metakey').value = (resp.responseText);
			}});
	}
	function ntreetoggle(){
		$('#ncontent').toggle();
	}
	function x_resethits(){
		id = SRAX.get('id').value;
		dax({
			url: 'ajax.index.php?option=com_content&task=resethits&id='+id,
			id:'resethits',
			method:'post',
			callback:
				function(resp){
				log('Получен ответ: ' + resp.responseText);
				mess_cool(resp.responseText);
				SRAX.get('count_hits').innerHTML='0';
			}});
	}
	//-->
</script>
<table class="adminheading">
	<tr><th class="edit"><?php echo _CONTENT?>: <small><?php echo $row->id ? _EDITING: _CREATION; ?></small></th></tr>
</table>

<form action="index2.php" method="post" name="adminForm" id="adminForm">
	<table class="adminform" cellspacing="0" cellpadding="0" width="100%"><tr>

			<!--Основная область с редактором:BEGIN-->
			<td class="main_area" width="100%" valign="top">

				<table width="100%">
					<tr>
						<th colspan="4"><?php echo _OBJECT_DETAILS?></th>
					</tr>
					<tr>
						<td width="15"><?php echo _CAPTION?>:</td>
						<td width="50%">
							<input class="text_area" type="text" name="title" size="30" maxlength="255" style="width:99%" value="<?php echo $row->title; ?>" />
						</td>
						<td width="15"><?php echo _PUBLISHED?>:</td>
						<td width="50%"><?php echo mosHTML::yesnoRadioList('published','',$row->state);?></td>
					</tr>
					<tr>
						<td><?php echo _ALIAS?>:</td>
						<td>
							<input name="title_alias" type="text" class="text_area" id="title_alias" value="<?php echo $row->title_alias; ?>" size="30" style="width:99%" maxlength="255" />
						</td>
						<td><?php echo _ON_FRONTPAGE?>:</td>
						<td><?php echo mosHTML::yesnoRadioList('frontpage','',$row->frontpage ? 1:0);?></td>
					</tr>
					<tr>
						<td><?php echo _SECTION?>:</td>
						<td><?php echo $lists['sectionid']; ?></td>
						<td><?php echo _CATEGORY?>:</td>
						<td><?php echo $lists['catid']; ?></td>
					</tr>

					<tr>
						<td colspan="4" width="100%">
							<?php echo _INTROTEXT_M?>
							<div id="intro_text"><?php editorArea('editor1',$row->introtext,'introtext','99%;','350','75','30'); ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="4"  width="100%">
							<?php echo _MAINTEXT_M?>
							<div id="full_text"><?php editorArea('editor2',$row->fulltext,'fulltext','99%;','400','75','30'); ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="4"  width="100%">
							<?php echo _NOTETEXT_M?>
							<div id="note_text"><?php editorArea('editor3',$row->notetext,'notetext','99%;','150','75','10'); ?></div>
						</td>
					</tr>
				</table>

			</td>
			<!--Основная область с редактором:END-->

			<!--кнопка скрытия правой колонки:BEGIN-->
			<td onclick="ntreetoggle();" width="1" id="tdtoogle" class="tdtoogleon">
				<img border="0" alt="<?php echo _HIDE_PARAMS_PANEL?>" src="<?php echo $cur_file_icons_path;?>/tgl.png" />
			</td>
			<!--кнопка скрытия правой колонки:END-->

			<!--правая колонка:BEGIN-->
			<td valign="top" id="ncontent">

				<table width="100%"><tr><th><?php echo _INFO?></th></tr></table>

				<table class="params" width="100%">
					<tr>
						<td><?php echo _E_STATE?></td>
						<td><?php echo $row->state > 0? _PUBLISHED :($row->state < 0? _IN_ARCHIVE : _DRAFT_UNPUBLISHED); ?></td>
					</tr>
					<tr <?php echo $visibility; ?>>
						<td><?php echo _HEADER_HITS?>:</td>
						<td id="count_hits">
							<?php echo $row->hits; ?>&nbsp;&nbsp;&nbsp;<input name="reset_hits" type="button" class="button" value="<?php echo _RESET?>" onclick="return x_resethits();" />
						</td>
					</tr>
					<tr>
						<td><?php echo _CHANGED?>:</td>
						<td><?php echo $row->version; ?> <?php echo _TIMES?></td>
					</tr>
					<tr>
						<td><?php echo _CREATED?></td>
						<td><?php echo $create_date ? $create_date : _NEW_DOCUMENT;?></td>
					</tr>
					<tr>
						<td><?php echo _LAST_CHANGE?>:</td>
						<td><?php echo $mod_date ? $mod_date.' '.$row->modifier : _NOT_CHANGED;?></td>
					</tr>
					<tr>
						<td><?php echo _AUTHOR?>:</td>
						<td><?php echo $lists['created_by'];?></td>
					</tr>
					<tr>
						<td><?php echo _E_AUTHOR_ALIAS?></td>
						<td><input type="text" name="created_by_alias" style="width:99%" size="30" maxlength="100" value="<?php echo $row->created_by_alias; ?>" class="text_area" /></td>
					</tr>
					<?php if($row->id) {?>
					<tr>
						<td><?php echo _OBJECT_ID?>:</td>
						<td><?php echo $row->id; ?></td>
					</tr>
						<?php } ?>
				</table>

				<table class="params" width="100%">
					<tr><th colspan="2"><?php echo _PUBLISHING?></th></tr>
					<tr>
						<td valign="top" align="right"><?php echo _ACCESS?>:</td>
						<td><?php echo $lists['access']; ?></td>
					</tr>

					<tr>
						<td valign="top" align="right"><?php echo _ORDER_DROPDOWN?>:</td>
						<td><?php echo $lists['ordering']; ?></td>
					</tr>
					<tr>
						<td valign="top" align="right"><?php echo _CREATED?></td>
						<td>
							<input class="text_area" type="text" name="created" id="created" size="25" maxlength="19" value="<?php echo $row->created; ?>" />
							<input name="reset" type="reset" class="button" onclick="return showCalendar('created', 'y-mm-dd');" value="..." />
						</td>
					</tr>
					<tr>
						<td valign="top" align="right"><?php echo _START_PUBLICATION?>:</td>
						<td>
							<input class="text_area" type="text" name="publish_up" id="publish_up" size="25" maxlength="19" value="<?php echo $row->publish_up; ?>" />
							<input type="reset" class="button" value="..." onclick="return showCalendar('publish_up', 'y-mm-dd');" />
						</td>
					</tr>
					<tr>
						<td valign="top" align="right"><?php echo _END_PUBLICATION?>:</td>
						<td>
							<input class="text_area" type="text" name="publish_down" id="publish_down" size="25" maxlength="19" value="<?php echo $row->publish_down; ?>" />
							<input type="reset" class="button" value="..." onclick="return showCalendar('publish_down', 'y-mm-dd');" />
						</td>
					</tr>
				</table>

				<table class="params" width="100%">
					<tr><th colspan="2"><?php echo _METADATA?></th></tr>
					<tr>
						<td><?php echo _TAGS?>
							<br />
							<textarea class="text_area" cols="60" rows="2" style="width:98%" name="tags"><?php echo str_replace('&','&amp;',$row->tags); ?></textarea>
						</td>
					</tr>

					<tr>
						<td><?php echo _DESCRIPTION?>:
							<br />
							<textarea class="text_area" cols="60" rows="3" style="width:98%" name="metadesc"><?php echo str_replace('&','&amp;',$row->metadesc); ?></textarea>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo _E_M_KEY?>
							<br />
							<textarea class="text_area" cols="60" rows="3" style="width:98%" name="metakey" id="metakey"><?php echo str_replace('&','&amp;',$row->metakey); ?></textarea>
						</td>
					</tr>
					<tr>
						<td>
							<input type="button" class="button" value="<?php echo _CC_ADD_S_C_T?>" onclick="f=document.adminForm;f.metakey.value=document.adminForm.sectionid.options[document.adminForm.sectionid.selectedIndex].text+', '+getSelectedText('adminForm','catid')+', '+f.title.value+', '+f.metakey.value;" />
							<input type="button" class="button" value="<?php echo _CC_AUTO?>"onclick="return ch_metakey();" />
						</td>
					</tr>
					<tr>
						<td><?php echo _ROBOTS_PARAMS?>: <br /><?php echo $lists['robots'] ?></td>
					</tr>
				</table>

				<br />

				<table class="params" width="100%">
					<tr><th colspan="2"><?php echo _ADVANCED?></th></tr>
				</table>
				<?php $tabs->startPane("content-pane"); ?>
				<?php $tabs->startTab(_MENU_LINK,"link-page"); ?>
				<table class="adminform">
					<tr>
						<td colspan="2"><?php echo _MENU_LINK2?></td>
					</tr>
					<tr>
						<td valign="top" width="90"><?php echo _CHOOSE_MENU_PLEASE?></td>
						<td><?php echo $lists['menuselect']; ?></td>
					</tr>
					<tr>
						<td valign="top" width="90"><?php echo _MENU_NAME?></td>
						<td><input style="width:90%" type="text" name="link_name" class="text_area" value="" size="30" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input name="menu_link" type="button" class="button" value="<?php echo _CC_LINK_TO_MENU?>" onclick="submitbutton('menulink');" /></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo _EXISTED_MENUITEMS?></th>
					</tr>
					<?php if($menus == null) { ?>
					<tr>
						<td colspan="2"><?php echo _NOT_EXISTS?></td>
					</tr>
						<?php } else {
						mosCommonHTML::menuLinksContent($menus);
					}?>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
				</table>
				<?php $tabs->endTab(); ?>
				<?php $tabs->startTab(_PARAMETERS,"params-page");?>
				<table class="adminform">
					<tr>
						<td>
							<?php echo _PARAMS_IN_VIEW?>
							<br />
						</td>
					</tr>
					<tr>
						<td><?php echo $params->render(); ?></td>
					</tr>
				</table>
				<?php $tabs->endTab(); ?>
				<?php
				// отключение вкладки "Изображения"
				if(!$mainframe->getCfg('disable_image_tab')) {
					$tabs->startTab(_IMAGES,"images-page");
					?>
				<table class="adminform" width="100%">
					<tr>
						<td colspan="2">
							<table width="100%">
								<tr>
									<td width="48%" valign="top">
										<div align="center">
												<?php echo _E_GALLERY_IMAGES?>:
											<br />
												<?php echo $lists['imagefiles']; ?>
										</div>
									</td>
									<td width="2%">
										<input class="button" type="button" value=">>" onclick="addSelectedToList('adminForm','imagefiles','imagelist')" title="Добавить" />
										<br />
										<input class="button" type="button" value="<<" onclick="delSelectedFromList('adminForm','imagelist')" title="Удалить" />
									</td>
									<td width="48%">
										<div align="center">
												<?php echo _USED_IMAGES?>:
											<br />
												<?php echo $lists['imagelist']; ?>
											<br />
											<input class="button" type="button" value="<?php echo _TO_TOP?>" onclick="moveInList('adminForm','imagelist',adminForm.imagelist.selectedIndex,-1)" />
											<input class="button" type="button" value="<?php echo _TO_BOTTOM?>" onclick="moveInList('adminForm','imagelist',adminForm.imagelist.selectedIndex,+1)" />
										</div>
									</td>
								</tr>
							</table>
								<?php echo _SUBDIRECTORY?>: <?php echo $lists['folders']; ?>
						</td>
					</tr>
					<tr valign="top">
						<td>
							<div align="center">
									<?php echo _IMAGE_EXAMPLE?>:<br />
								<img name="view_imagefiles" src="../images/M_images/blank.png" alt="<?php echo _IMAGE_EXAMPLE?>" width="100" />
							</div>
						</td>
						<td valign="top">
							<div align="center">
									<?php echo _ACTIVE_IMAGE?>:<br />
								<img name="view_imagelist" src="../images/M_images/blank.png" alt="Активное изображение" width="100" />
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
								<?php echo _EDIT_IMAGE?>:
							<table>
								<tr>
									<td align="right"><?php echo _SOURCE?>:</td>
									<td><input style="width:99%" class="text_area" type="text" name= "_source" value="" /></td>
								</tr>
								<tr>
									<td align="right"><?php echo _ALIGN?>:</td>
									<td><?php echo $lists['_align']; ?></td>
								</tr>
								<tr>
									<td align="right"><?php echo _E_ALT?>:</td>
									<td><input style="width:99%" class="text_area" type="text" name="_alt" value="" /></td>
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
									<td colspan="2"><input class="button" type="button" value="<?php echo _APPLY?>" onclick="applyImageProps()" /></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
					<?php $tabs->endTab(); ?>
					<?php }else { ?>
				<input type="hidden" name="images" id="images" value="" />'; ?>
					<?php } ?>
				<?php $tabs->endPane(); ?>

				<table class="params" width="100%">
					<tr><th colspan="2"><?php echo _TEMPLATES?></th></tr>
					<?php
					$templates = new ContentTemplate;
					$curr_templates = $templates->parse_curr_templates($row->templates);
					?>
					<tr>
						<td><?php echo _TEMPLATE_ITEM_SHOW?>: </td>
						<td><?php echo $templates->templates_select_list('item_full', $curr_templates); ?> </td>
					</tr>
				</table>

			</td>
			<!--правая колонка:END-->

		</tr></table>

	<input type="hidden" name="id" id="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="version" value="<?php echo $row->version; ?>" />
	<input type="hidden" name="mask" value="0" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="images" value="" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>