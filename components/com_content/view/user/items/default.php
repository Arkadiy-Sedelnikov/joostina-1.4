<?php /**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die(); ?>

<?php
if(!$user_items || !$user_items->items) {
	//Случай, если содержимое пользователя не найдено
	?>
<div class="page_user_items<?php echo $params->get('pageclass_sfx'); ?>">
		<?php if($params->get('title')) { ?>
	<div class="componentheading"><h1><?php echo $params->title; ?></h1></div>
			<?php } ?>
	<div class="error"><?php echo _COM_CONTENT_USERCONTENT_NOT_FOUND ?></div>
		<?php mosHTML::BackButton($params); ?>
</div>
	<?php
	return;
}
?>
<?php mosCommonHTML::loadFullajax(); ?>
<script type="text/javascript">
	// смена статуса публикации, elID - идентификатор объекта у которого меняется статус публикации
	function ch_publ(elID){
		id('img-pub-'+elID).src = '<?php echo JPATH_SITE ?>/images/system/aload.gif';
		dax({
			url: '<?php echo JPATH_SITE ?>/ajax.index.php?option=com_content&task=publish&id='+elID,
			id:'publ-'+elID,
			callback:
				function(resp, idTread, status, ops){
				id('img-pub-'+elID).src = '<?php echo JPATH_SITE.'/'.JADMIN_BASE ?>/images/'+resp.responseText;
			}
		});
	}
</script>
<div class="page_user_items<?php echo $params->get('pageclass_sfx'); ?>">
	<?php if($params->get('title')) { ?>
	<div class="componentheading"><h1><?php echo $params->title; ?> <?php echo mosContent::Author($items[0]); ?></h1></div>
		<?php } ?>
	<?php if(!$items) {
		echo _YOU_HAVE_NO_CONTENT;
		mosHTML::BackButton($params);
		return;
	} ?>
	<form action="<?php echo sefRelToAbs($page_link); ?>" method="post" name="adminForm" id="adminForm">
		<!--Таблица с полем фильтра, выбора сортировки и количества отображаемых записей :BEGIN-->
		<?php if($params->get('filter') || $params->get('order_select') || $params->get('display')) { ?>
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<!--Фильтр-->
					<?php if($params->get('filter')) { ?>
				<td align="right" width="80%"><?php echo _FILTER; ?>
					<br />
					<input type="text" name="filter" size="50" value="<?php echo $lists['filter']; ?>" class="inputbox" onchange="document.adminForm.submit();" />
				</td>
						<?php } ?>
				<!--Сортировка-->
					<?php if($params->get('order_select')) { ?>
				<td align="right" width="20%">
							<?php echo _ORDER_DROPDOWN; ?>
					<br />
							<?php echo $lists['order']; ?>
				</td>
						<?php } ?>
				<!--Количество отображаемых записей-->
					<?php if($params->get('display')) { ?>
				<td align="right" width="80%">
							<?php echo _PN_DISPLAY_NR; ?>
					<br />
							<?php echo $pageNav->getLimitBox($page_link); ?>
				</td>
						<?php } ?>
			</tr>
		</table>
		<!--Таблица с полем фильтра, выбора сортировки и количества отображаемых записей :END-->
			<?php } ?>
		<!--Таблица с записями :BEGIN-->
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<!--Заголовок таблицы-->
			<?php if($params->get('headings')) { ?>
			<tr>
				<th class="sectiontableheader">&nbsp;</th>
				<th class="sectiontableheader" width="60%"><?php echo _CAPTION; ?></th>
				<th class="sectiontableheader"><?php echo _PUBLISHING; ?></th>
					<?php if($params->get('date')) { ?>
				<th class="sectiontableheader" width="20%"><?php echo _DATE; ?></th>
						<?php } ?>
					<?php if($params->get('hits')) { ?>
				<th class="sectiontableheader"><?php echo _HEADER_HITS; ?></th>
						<?php } ?>
			</tr>
				<?php } ?>
			<?php //foreach:begin
			foreach ($items as $row) {
				$row->Itemid_link = '&amp;Itemid='.$Itemid;
				$row->_Itemid = $Itemid;
				$row->created = mosFormatDate($row->created, $config->config_form_date_full, '0');
				$link = sefRelToAbs('index.php?option=com_content&amp;task=view&amp;id='.$row->id.'&amp;Itemid='.$Itemid);
				$img = $row->published?'publish_g.png' : 'publish_x.png';
				$img = JPATH_SITE.'/'.JADMIN_BASE.'/images/'.$img;

				// раздел / категория
				$section_cat = $row->section.' / '.$row->category;
				if($row->sectionid == 0) {
					$section_cat = _CONTENT_TYPED;
				} ?>
			<tr class="sectiontableentry<?php echo ($k + 1); ?>">
				<td>
						<?php ContentView::EditIcon($row, $params, $access); ?>
				</td>
				<td>
					<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
						<?php if($params->get('section')) { ?>
					<br />
					<span class="small"><?php echo $section_cat; ?></span>
							<?php } ?>
				</td>
				<td align="center" <?php echo ($access->canPublish)?'onclick="ch_publ('.$row->id.');" class="td-state"' : null; ?>>
					<img class="img-mini-state" src="<?php echo $img; ?>" id="img-pub-<?php echo $row->id; ?>" alt="<?php echo _PUBLISHING?>" />
				</td>
					<?php if($params->get('date')) { ?>
				<td><?php echo $row->created; ?></td>
						<?php } ?>
					<?php if($params->get('hits')) { ?>
				<td align="center"><?php echo $row->hits?$row->hits : 0; ?></td>
						<?php } ?>
			</tr>
				<?php $k = 1 - $k; ?>
				<?php } //foreach:end ?>
		</table>
		<!--Таблица с записями :END-->
		<!--Постраничная навигация-->
		<?php if($params->get('navigation')) { ?>
			<?php echo $pageNav->writePagesLinks($page_link); ?>
			<?php } ?>
		<input type="hidden" name="task" value="user_content" />
		<input type="hidden" name="option" value="com_content" />
	</form>
	<?php mosHTML::BackButton($params); ?>
</div>