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
class ContentView {
	/**
	 * Writes a list of the content items
	 * @param array An array of content objects
	 */
	function showList(&$rows,$search,$pageNav,$option,$lists) {
		global $my,$acl,$database;
		mosCommonHTML::loadOverlib();
		$nullDate = $database->getNullDate();

		$mainframe = mosMainFrame::getInstance();
		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';
		?>
<script type="text/javascript">
	// удаление содержимого с публикации на главной
	function ch_front(elID){
		id('img-trash-'+elID).src = 'images/aload.gif';
		dax({
			url: 'ajax.index.php?option=com_frontpage&task=rem_front&id='+elID,
			id:'trash-'+elID,
			callback:
				function(resp, idTread, status, ops){
				log('Получен ответ: ' + resp.responseText);
				if(resp.responseText=='1') {
					SRAX.remove('tr-el-'+elID);
				}else{
					id('tr-el-'+elID).style.background='red';
				}
			}});
	}
</script>
<form action="index2.php" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th class="frontpage" rowspan="2"><?php echo _C_FRONTPAGE_CONTENT?></th>
			<td width="right"><?php echo $lists['sectionid']; ?></td>
			<td width="right"><?php echo $lists['catid']; ?></td>
			<td width="right"><?php echo $lists['authorid']; ?></td>
		</tr>
		<tr>
			<td align="right" colspan="2"><?php echo _FILTER?>:</td>
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
			<th align="center"><?php echo _REMOVE_FROM_FRONT?></th>
			<th width="10%" class="jtd_nowrap"><?php echo _PUBLISHED?></th>
			<th colspan="2" class="jtd_nowrap" width="5%"><?php echo _ORDERING?></th>
			<th width="2%"><?php echo _ORDER_DROPDOWN?></th>
			<th width="1%">
				<a href="javascript: saveorder( <?php echo count($rows) - 1; ?> )"><img src="<?php echo $cur_file_icons_path;?>/filesave.png" border="0" width="16" height="16" alt="<?php echo _SAVE_ORDER?>" /></a>
			</th>
			<th width="8%" class="jtd_nowrap"><?php echo _ACCESS?></th>
			<th width="10%" align="left"><?php echo _SECTION?></th>
			<th width="10%" align="left"><?php echo _CATEGORY?></th>
		</tr>
				<?php
				$k = 0;
				$num = count($rows);
				for($i = 0,$n = $num; $i < $n; $i++) {
					$row = &$rows[$i];
					mosMakeHtmlSafe($row);
					$link = 'index2.php?option=com_content&sectionid=0&task=edit&hidemainmenu=1&id='.$row->id;
					$row->sect_link = 'index2.php?option=com_sections&task=editA&hidemainmenu=1&id='.$row->sectionid;
					$row->cat_link = 'index2.php?option=com_categories&task=editA&hidemainmenu=1&id='.$row->catid;

					$now = _CURRENT_SERVER_TIME;
					if($now <= $row->publish_up && $row->state == '1') {
						$img = 'publish_y.png';
						$alt = _PUBLISHED;
					} else
					if(($now <= $row->publish_down || $row->publish_down == $nullDate) && $row->state =='1') {
						$img = 'publish_g.png';
						$alt = _PUBLISHED;
					} else
					if($now > $row->publish_down && $row->state == '1') {
						$img = 'publish_r.png';
						$alt = _PUBLISH_TIME_END;
					} elseif($row->state == "0") {
						$img = "publish_x.png";
						$alt = _UNPUBLISHED;
					}

					$times = '';
					if(isset($row->publish_up)) {
						if($row->publish_up == $nullDate) {
							$times .= '<tr><td>'._START.': '._ALWAYS.'</td></tr>';
						} else {
							$times .= '<tr><td>'._START.': '.$row->publish_up.'</td></tr>';
						}
					}
					if(isset($row->publish_down)) {
						if($row->publish_down == $nullDate) {
							$times .= '<tr><td>'._END.': '._WITHOUT_END.'</td></tr>';
						} else {
							$times .= '<tr><td>'._END.': '.$row->publish_down.'</td></tr>';
						}
					}

					$access = mosCommonHTML::AccessProcessing($row,$i,1);
					$checked = mosCommonHTML::CheckedOutProcessing($row,$i);

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
					?>
		<tr class="row<?php echo $k; ?>" id="tr-el-<?php echo $row->id;?>">
			<td><?php echo $pageNav->rowNumber($i); ?></td>
			<td><?php echo $checked; ?></td>
			<td align="left">
							<?php
							if($row->checked_out && ($row->checked_out != $my->id)) {
								echo $row->title;
							} else {
								?>
				<a href="<?php echo $link; ?>" class="abig" title="<?php echo _CHANGE_CONTENT?>"><?php echo $row->title; ?></a>
								<?php
							}
							echo '<br />'.$row->created.' : '.$author;
							?>
			</td>
			<td align="center" <?php echo $row->checked_out ? null : 'onclick="ch_front('.$row->id.');" class="td-state"';?>>
				<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/trash_mini.png" id="img-trash-<?php echo $row->id;?>"/>
			</td>
						<?php
						if($times) {
							?>
			<td align="center" <?php echo ($row->checked_out && ($row->checked_out != $my->id)) ? null : 'onclick="ch_publ('.$row->id.',\'com_frontpage\');" class="td-state"';?>>
								<?php
								if ( !$row->checked_out ) {
									?>
				<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/<?php echo $img;?>" id="img-pub-<?php echo $row->id;?>" alt="<?php echo _PUBLISHING?>" />
									<?php
								}else {
									?>
				<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/<?php echo $img;?>" id="img-pub-<?php echo $row->id;?>" alt="<?php echo _CANNOT_CHANGE_PUBLISH_STATE?>"/>
									<?php
								}
								?>
			</td>
							<?php
						}
						?>
			<td align="center"><?php echo $pageNav->orderUpIcon($i); ?></td>
			<td align="center"><?php echo $pageNav->orderDownIcon($i,$n); ?></td>
			<td align="center" colspan="2">
				<input type="text" name="order[]" size="5" value="<?php echo $row->fpordering; ?>" class="text_area" style="text-align: center" />
			</td>
			<td align="center" id="acc-id-<?php echo $row->id;?>"><?php echo $access; ?></td>
			<td><a href="<?php echo $row->sect_link; ?>" title="<?php echo _CHANGE_SECTION?>"><?php echo $row->sect_name; ?></a></td>
			<td><a href="<?php echo $row->cat_link; ?>" title="<?php echo _CHANGE_CATEGORY?>"><?php echo $row->name; ?></a></td>
		</tr>
					<?php
					$k = 1 - $k;
				}
				?>
	</table>

			<?php
			echo $pageNav->getListFooter();
			mosCommonHTML::ContentLegend();
			?>

	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}
}