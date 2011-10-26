<?php /**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die(); ?>
<div class="page_archive">
	<div class="componentheading"><h1><?php echo _ARCHIVE?></h1></div>
	<?php $link = ampReplace('index.php?option=com_content&task=archivecategory&id='.$id.'&Itemid='.$Itemid); ?>
	<form action="<?php echo sefRelToAbs($link); ?>" method="post">
		<?php if(!$params->get('module')) { ?>
		<div class="form">
				<?php echo mosHTML::monthSelectList('month', 'size="1" class="inputbox"', $params->get('month')); ?>
				<?php echo mosHTML::integerSelectList(2000, 2010, 1, 'year', 'size="1" class="inputbox"', $params->get('year'), "%04d"); ?>
			<input type="submit" class="button" value="<?php echo _SUBMIT_BUTTON; ?>" />
		</div>
			<?php } ?>
		<?php if($total) { ?>
		<div class="contentdescription">
				<?php echo $msg; ?>
		</div>
		<div class="blog">
				<?php if($leading) { ?>
			<div class="leading_block">
						<?php for ($z = 0; $z < $leading; $z++) {
							if($i >= ($total - $limitstart)) {
								break;
							} ?>
				<div class="intro leading" id="leading_<?php echo $i; ?>">
								<?php $params->set('page_type', 'item_intro_leading');
								_showItem($rows[$i], $params, $gid, $access, $pop, '[s]default.php');
								?>
				</div>
							<?php $i++;
						} ?>
			</div>
					<?php } ?>
				<?php if($intro && ($i < $total)) { ?>
			<table class="intro_table" width="100%"  cellpadding="0" cellspacing="0">
						<?php for ($z = 0; $z < $intro; $z++) {
							if($i >= ($total - $limitstart)) {
								break;
							}
							if(!($z % $columns) || $columns == 1) { ?>
				<tr>
									<?php } ?>
					<td valign="top" <?php echo $width; ?>>
									<?php if($z < $intro) { ?>
						<div class="intro" id="intro_<?php echo $i; ?>">
											<?php $params->set('page_type', 'item_intro_simple');
											_showItem($rows[$i], $params, $gid, $access, $pop, '[s]default.php');
											?>
						</div>
										<?php } else {
										echo '</td></tr>';
										break;
									} ?>
					</td>
								<?php $i++;
								if((!(($z + 1) % $columns) || $columns == 1) || ($i >= $total) || ((($z + 1) == $intro) && ($intro % $columns))) { ?>
				</tr>
								<?php } ?>
							<?php } ?>
			</table>
					<?php } ?>
				<?php if($display_blog_more) { ?>
			<div class="blog_more">
						<?php ContentView::showLinks($rows, $links, $total, $i, $showmore);?>
			</div>
					<?php } ?>
				<?php if($display_pagination) {
					echo $pageNav->writePagesLinks($link);
					if($display_pagination_results) {
						echo $pageNav->writePagesCounter();
					}
				} ?>
		</div>
			<?php } else { ?>
		<div class="contentdescription">
				<?php echo $msg; ?>
		</div>
			<?php } ?>
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
		<input type="hidden" name="task" value="archivecategory" />
		<input type="hidden" name="option" value="com_content" />
	</form>
	<?php mosHTML::BackButton($params); ?>
</div>