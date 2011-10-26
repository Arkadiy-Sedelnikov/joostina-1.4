<?php /**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */


// запрет прямого доступа
defined('_VALID_MOS') or die(); ?>
<div class="page_sectionblog<?php echo $sfx; ?>">
	<?php if($params->get('page_title')) { ?>
	<div class="componentheading"><?php echo $header; ?></div>
		<?php } ?>
	<?php if($total) { ?>
	<div class="blog">
			<?php if($display_desc) { ?>
		<div class="contentdescription">
					<?php if($display_desc_img) { ?>
			<img src="<?php echo JPATH_SITE; ?>/images/stories/<?php echo $obj->image; ?>" align="<?php echo $obj->image_position; ?>"  alt="" />
						<?php } ?>
					<?php if($display_desc_text) { ?>
			<p> <?php echo $obj->description; ?> </p>
						<?php } ?>
		</div>
				<?php } ?>
			<?php if($leading) { ?>
		<div class="leading_block">
					<?php for ($z = 0; $z < $leading; $z++) {
						if($i >= ($total - $limitstart)) {
							break;
						}
						if(array_key_exists($rows[$i]->id, $tags_arr)) {
							$rows[$i]->tags = $tags_arr[$rows[$i]->id];
						} ?>
			<div class="intro leading" id="leading_<?php echo $i; ?>">
							<?php $params->set('page_type', 'item_intro_leading');
							_showItem($rows[$i], $params, $gid, $access, $pop, '[s]frontpage.php',$mainframe);
							?>
			</div>
						<?php $i++;
					} ?>
		</div>
				<?php } ?>
			<?php if($intro && ($i < $total)) { ?>
		<table class="intro_table" width="100%"  cellpadding="0" cellspacing="0">
					<?php for ($z = 0; $z < $intro; $z++) {
						if(isset($rows[$i]->id) && array_key_exists($rows[$i]->id, $tags_arr)) {
							$rows[$i]->tags = $tags_arr[$rows[$i]->id];
						}
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
										_showItem($rows[$i], $params, $gid, $access, $pop, '[s]frontpage.php',$mainframe);
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
							<?php
						}
					}
					?>
		</table>
				<?php } ?>
			<?php if($display_blog_more) { ?>
		<div class="blog_more">
					<?php ContentView::showLinks($rows, $links, $total, $i, $showmore); ?>
		</div>
				<?php } ?>
			<?php if($display_pagination) {
				echo $pageNav->writePagesLinks($link);
				if($display_pagination_results) {
					echo $pageNav->writePagesCounter();
				}
			} ?>
	</div>
		<?php } else {
		echo _EMPTY_BLOG;
	}
	mosHTML::BackButton($params); ?>
</div>