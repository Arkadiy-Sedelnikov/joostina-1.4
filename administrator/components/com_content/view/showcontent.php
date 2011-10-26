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
<script type="text/javascript">
	// смена статуса отображения на главной странице
	function ch_fpage(elID){
		log('Смена отображения на главной: '+elID);
		SRAX.get('img-fpage-'+elID).src = 'images/aload.gif';
		dax({
			url: 'ajax.index.php?option=com_content&task=frontpage&id='+elID,
			id:'fpage-'+elID,
			callback:
				function(resp, idTread, status, ops){
				log('Получен ответ: ' + resp.responseText);
				SRAX.get('img-fpage-' + elID).src = '<?php echo $cur_file_icons_path;?>/'+resp.responseText;
			}});
	}
	// перемещение содержимого в корзину
	function ch_trash(elID){
		log('Удаление в корзину: '+elID);
		if(SRAX.get('img-trash-'+elID).src == '<?php echo $cur_file_icons_path;?>/trash_mini.png'){
			SRAX.get('img-trash-'+elID).src = '<?php echo $cur_file_icons_path;?>/help.png';
			return null;
		}

		SRAX.get('img-trash-'+elID).src = 'images/aload.gif';
		dax({
			url: 'ajax.index.php?option=com_content&task=to_trash&id='+elID,
			id:'trash-'+elID,
			callback:
				function(resp, idTread, status, ops){
				log('Получен ответ: ' + resp.responseText);
				if(resp.responseText=='1') {
					log('Перемещение в корзину успешно: ' + elID);
					SRAX.remove('tr-el-'+elID);
				}else{
					log('Ошибка перемещения в корзину: ' + elID);
					SRAX.get('tr-el-'+elID).style.background='red';
				}
			}});
	}
	/* скрытие дерева навигации по структуре содержимого */
	function ntreetoggle(){
		if(SRAX.get('ntdree').style.display =='none'){
			SRAX.get('ntdree').style.display ='block';
			SRAX.get('tdtoogle').className='tdtoogleoff';
			setCookie('j-ntree-hide','0');
		}else{
			SRAX.get('ntdree').style.display ='none';
			SRAX.get('tdtoogle').className='tdtoogleon';
			setCookie('j-ntree-hide','1');
		}
	}
</script>
<form action="index2.php?option=com_content" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th class="edit" colspan="3" class="jtd_nowrap">
				<?php if($all) { ?>
					<?php echo _ALL_CONTENT?>
					<?php } else { ?>
					<?php echo _SITE_CONTENT?>, <?php echo $section->params['name'];?>: <a href="<?php echo $section->params['link'];?>" title="<?php echo _GOTO_EDIT?>"><?php echo $section->title; ?></a>
					<?php } ?>
			</th>
		</tr>
		<tr>
			<td>
				<?php echo _FILTER?>:<br /><input type="text" style="width: 99%;" name="search" value="<?php echo htmlspecialchars($search); ?>" class="inputbox" onChange="document.adminForm.submit();" />
			</td>
			<td><?php echo _SORT_BY?>:<br /><?php echo $lists['order']; ?></td>
			<td><?php echo _ORDER_DROPDOWN?>:<br /><?php echo $lists['order_sort']; ?></td>
		</tr>
	</table>

	<table class="adminlisttop adminlist">
		<tr>
			<td valign="top" class="jtd_nowrap" align="left" id="ntdree"><?php echo $lists['sectree'];?></td>
			<td onclick="ntreetoggle();" width="1" id="tdtoogle" <?php echo $lists['sectreetoggle'];?>><img border="0" alt="<?php echo _HIDE_NAV_TREE?>" src="<?php echo $cur_file_icons_path2;?>/tgl.png" /></td>
			<td valign="top" width="100%">
				<table class="adminlist" width="100%">
					<thead>
						<tr>
							<th width="5">
								<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
							</th>
							<th class="title"><?php echo _CAPTION?></th>
							<th><?php echo _PUBLISHED?></th>
							<th class="jtd_nowrap"><?php echo _ON_FRONTPAGE?></th>
							<th width="2%"><?php echo _ORDER_DROPDOWN?></th>
							<th width="1%">
								<a href="javascript: saveorder( <?php echo count($rows) - 1; ?> )"><img src="<?php echo $cur_file_icons_path;?>/filesave.png" border="0" width="16" height="16" alt="<?php echo _SAVE_ORDER?>" /></a>
							</th>
							<th width="10%"><?php echo _ACCESS_RIGHTS?></th>
							<th align="center"><?php echo _TO_TRASH?></th>
							<th width="5">ID</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$k = 0;
						$nullDate = $database->getNullDate();
						$now = _CURRENT_SERVER_TIME;
						$_c = count($rows);
						for($i = 0,$n = $_c; $i < $n; $i++) {
							$row = &$rows[$i];
							mosMakeHtmlSafe($row);

							$link = 'index2.php?option=com_content&sectionid='.$redirect.'&task=edit&hidemainmenu=1&id='.$row->id;
							$row->sect_link = 'index2.php?option=com_sections&task=editA&hidemainmenu=1&id='.$row->sectionid;
							$row->cat_link = 'index2.php?option=com_categories&task=editA&hidemainmenu=1&id='.$row->catid;

							if($now <= $row->publish_up && $row->state == 1) {
								// опубликовано
								$img = 'publish_y.png';
								//$alt = 'Опубликовано';
							} else if(($now <= $row->publish_down || $row->publish_down == $nullDate) && $row->state ==1) {
								// Доступно
								$img = 'publish_g.png';
								//$alt = 'Опубликовано';
							} else if($now > $row->publish_down && $row->state == 1) {
								// Истекло
								$img = 'publish_r.png';
								//$alt = 'Просрочено';
							} elseif($row->state == 0) {
								// Не опубликовано
								$img = 'publish_x.png';
								//$alt = 'Не опубликовано';
							}elseif($row->state == -1) {
								$img = 'publish_x.png';
							}
							// корректировка и проверка времени
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

							$access		= mosCommonHTML::AccessProcessing($row,$i,1);
							$checked	= mosCommonHTML::CheckedOutProcessing($row,$i);
							// значок отображения на главной странице
							$front_img = $row->frontpage ? 'tick.png' : 'publish_x.png';

							?>
						<tr class="row<?php echo $k; ?>" id="tr-el-<?php echo $row->id;?>">
							<td align="center"><?php echo $checked; ?></td>
							<td align="left">
									<?php
									if($row->checked_out && ($row->checked_out != $my->id)) {
										echo $row->title;
									} else {
										?>
								<a class="abig" href="<?php echo $link; ?>" title="<?php echo _CHANGE_CONTENT?>"><?php echo $row->title; ?></a>
										<?php
									}
									?>
								<br />
									<?php echo $row->created;?>, <?php echo $row->hits;?> <?php echo _HEADER_HITS?> : <?php echo $author; ?>
							</td>
								<?php
								if($times) {
									?>
							<td align="center" <?php echo ($row->checked_out && ($row->checked_out != $my->id)) ? null : 'onclick="ch_publ('.$row->id.',\'com_content\');" class="td-state"';?>>
								<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/<?php echo $img;?>" id="img-pub-<?php echo $row->id;?>" alt="<?php echo _PUBLISHING?>" />
							</td>
									<?php
								}
								?>
							<td align="center" <?php echo ($row->checked_out && ($row->checked_out != $my->id)) ? null : 'onclick="ch_fpage('.$row->id.');" class="td-state"';?>>
								<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/<?php echo $front_img;?>" id="img-fpage-<?php echo $row->id;?>" alt="<?php echo _ON_FRONTPAGE?>" />
							</td>
							<td align="center" colspan="2">
									<?php echo $pageNav->orderUpIcon($i,($row->catid == @$rows[$i - 1]->catid)); ?>
								<input type="text" name="order[]" size="3" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
									<?php echo $pageNav->orderDownIcon($i,$n,($row->catid == @$rows[$i + 1]->catid)); ?>
							</td>
							<td align="center" id="acc-id-<?php echo $row->id;?>"><?php echo $access; ?></td>
							<td align="center" <?php echo $row->checked_out ? null : 'onclick="ch_trash('.$row->id.');" class="td-state"';?>>
								<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/trash_mini.png" id="img-trash-<?php echo $row->id;?>"/>
							</td>
							<td align="center"><?php echo $row->id; ?></td>
						</tr>
							<?php

							$k = 1 - $k;
						}
						?>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
	<?php echo $pageNav->getListFooter(); ?>
	<?php mosCommonHTML::ContentLegend(); ?>
	<input type="hidden" name="option" value="com_content" />
	<input type="hidden" name="sectionid" value="<?php echo $section->section; ?>" />
	<input type="hidden" name="catid" value="<?php echo $selected_cat; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="showarchive" value="<?php echo $showarchive ?>" />
	<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>