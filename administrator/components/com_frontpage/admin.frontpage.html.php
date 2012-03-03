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
	function showList(&$rows,$search,$pageNav,$option,$lists, $directory) {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();
        $database = database::getInstance();
        $acl = &gacl::getInstance();
		mosCommonHTML::loadOverlib();
		$nullDate = $database->getNullDate();

		$mainframe = mosMainFrame::getInstance();
		$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';
		?>
<script type="text/javascript">
	// удаление содержимого с публикации на главной
	function ch_front(elID, directory){
		id('img-trash-'+elID).src = 'images/aload.gif';
		dax({
			url: 'ajax.index.php?option=com_frontpage&task=rem_front&id='+elID+'&directory='+directory,
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
			<th class="frontpage"><?php echo _C_FRONTPAGE_CONTENT?></th>
            <td align="right"><?php echo _FILTER?>:</td>
			<td>
				<input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
			<td width="right"><?php echo $lists['catid']; ?></td>
			<td width="right"><?php echo $lists['authorid']; ?></td>
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
			<th width="10%" align="left"><?php echo _CATEGORY?></th>
		</tr>
				<?php
				$k = 0;
				$num = count($rows);
				for($i = 0,$n = $num; $i < $n; $i++) {
					$row = &$rows[$i];
					mosMakeHtmlSafe($row);
					$link = 'index2.php?option=com_boss&act=contents&task=edit&hidemainmenu=1&layout=edit&directory='.$directory.'&tid[]='.$row->id;
					$row->cat_link = 'index2.php?option=com_boss&directory='.$directory.'&act=categories&hidemainmenu=1&layout=edit&task=edit&tid[]='.$row->catid;

					$now = _CURRENT_SERVER_TIME;
					if($now <= $row->date_publish && $row->published == '1') {
						$img = 'publish_y.png';
						$alt = _PUBLISHED;
					} else
					if(($now <= $row->date_unpublish || $row->date_unpublish == $nullDate) && $row->published =='1') {
						$img = 'publish_g.png';
						$alt = _PUBLISHED;
					} else
					if($now > $row->date_unpublish && $row->published == '1') {
						$img = 'publish_r.png';
						$alt = _PUBLISH_TIME_END;
					} elseif($row->published == "0") {
						$img = "publish_x.png";
						$alt = _UNPUBLISHED;
					}

					$times = '';
					if(isset($row->date_publish)) {
						if($row->date_publish == $nullDate) {
							$times .= '<tr><td>'._START.': '._ALWAYS.'</td></tr>';
						} else {
							$times .= '<tr><td>'._START.': '.$row->date_publish.'</td></tr>';
						}
					}
					if(isset($row->date_unpublish)) {
						if($row->date_unpublish == $nullDate) {
							$times .= '<tr><td>'._END.': '._WITHOUT_END.'</td></tr>';
						} else {
							$times .= '<tr><td>'._END.': '.$row->date_unpublish.'</td></tr>';
						}
					}



					if($acl->acl_check('administration','manage','users',$my->usertype,'components','com_users')) {
						if($row->author) {
							$author = $row->author;
						} else {
							$linkA = 'index2.php?option=com_users&task=editA&hidemainmenu=1&id='.$row->userid;
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
			<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>"
                               onclick="isChecked(this.checked);"/></td>
			<td align="left">

				<a href="<?php echo $link; ?>" class="abig" title="<?php echo _CHANGE_CONTENT?>"><?php echo $row->name; ?></a>
								<?php

							echo '<br />'.$row->userid.' : '.$author;
							?>
			</td>
			<td align="center" <?php echo 'onclick="ch_front('.$row->id.','.$directory.');" class="td-state"';?>>
				<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/trash_mini.png" id="img-trash-<?php echo $row->id;?>"/>
			</td>
						<?php
						if($times) {
							?>
			<td align="center" <?php echo 'onclick="ch_publ('.$row->id.',\'com_frontpage\','.$directory.');" class="td-state"';?>>
				<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/<?php echo $img;?>" id="img-pub-<?php echo $row->id;?>" alt="<?php echo _CANNOT_CHANGE_PUBLISH_STATE?>"/>
			</td>
							<?php
						}
						?>
			<td align="center"><?php echo $pageNav->orderUpIcon($i); ?></td>
			<td align="center"><?php echo $pageNav->orderDownIcon($i,$n); ?></td>
			<td align="center" colspan="2">
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
			</td>
			<td>
                <a href="<?php echo $row->cat_link; ?>" title="<?php echo _CHANGE_CATEGORY?>"><?php echo $row->catname; ?></a>
            </td>
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
	<input type="hidden" name="directory" value="<?php echo $directory; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}
    	function showConf($directorylist,$pageslist) {
		?>

<form action="index2.php" method="post" name="adminForm">

	<table class="adminlist">
		<tr class="row1">
            <td><?php echo _SELECT_DIRECTORY; ?></td>
            <td><?php echo $directorylist; ?></td>
            <td><?php echo _SELECT_DIRECTORY_DESC; ?></td>
		</tr>
        <tr class="row2">
            <td><?php echo _SELECT_VIEW; ?></td>
            <td><?php echo $pageslist; ?></td>
            <td><?php echo _SELECT_VIEW_DESC; ?></td>
		</tr>
	</table>
	<input type="hidden" name="option" value="com_frontpage" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}
}