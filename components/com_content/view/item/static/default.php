<?php /**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die(); ?>
<?php if($params->get('item_title')) { ?>
<div <?php echo $news_uid_css_title; ?>class="item_title">
	<div class="contentheading"><h1><?php echo $row->title; ?></h1></div>
</div>
<?php }
$loadbot_onAfterDisplayTitle;
$loadbot_onBeforeDisplayContent;
?>
<div class="buttons_wrap">
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<?php if($params->get('createdate', 0)) { ?>
				<span class="date"><strong><?php echo _E_START_PUB; ?></strong> <?php echo $create_date; ?></span>
					<?php } ?>
				<?php if($params->get('author', 0)) { ?>
				<span class="author"><strong><?php echo _AUTHOR; ?>:</strong> <?php echo $author; ?></span>
					<?php } ?>
			</td>
			<td width="200"  align="right">
				<div class="icons_c">
					<?php if($params->get('print')) { ?>
						<?php mosHTML::PrintIcon($row, $params, $hide_js, $print_link); ?>
						<?php } ?>
					<?php if($params->get('email')) { ?>
						<?php ContentView::EmailIcon($row, $params, $hide_js); ?>
						<?php } ?>
				</div>
			</td>
		</tr>
	</table>
</div>
<div <?php echo $news_uid_css_body; ?>class="item_body">
	<?php if($params->get('url') && $row->urls) { ?>
	<div class="blog_urls">
		<a href="http://<?php echo $row->urls; ?>" target="_blank"><?php echo $row->urls; ?></a>
	</div>
		<?php } ?>
	<?php if(isset($row->toc)) { ?>
	<div class="toc"><?php echo $row->toc; ?></div>
		<?php } ?>
	<?php if($params->get('view_introtext', 1)) { ?>
	<div class="item_text"><?php echo ampReplace($row->text); ?></div>
		<?php } ?>
		<?php if($params->get('tags', 1)) { ?>
	<span class="tags"><strong><?php echo _TAGS; ?></strong> <?php echo isset($row->tags) ? $row->tags : _TAGS_NOT_DEFINED; ?></span>
		<?php } ?>
	<?php if($params->get('modifydate')) { ?>
	<div class="modified_date">
		<strong><?php echo _LAST_UPDATED; ?> </strong> <?php echo $mod_date; ?>
	</div>
		<?php } ?>
	<?php if($params->get('readmore')) { ?>
	<div class="readmore"><?php echo $readmore; ?></div>
		<?php } ?>
</div>
<?php echo ContentView::afterDisplayContent(&$row, &$params, $page); ?>
<div class="edit_item"><?php echo $edit; ?></div>
<?php echo $row->rating; ?>
<?php ContentView::Navigation($row, $params); ?>
<?php mosHTML::CloseButton($params, $hide_js); ?>
<?php mosHTML::BackButton($params, $hide_js); ?>