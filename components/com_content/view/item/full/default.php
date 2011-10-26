<?php /**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die(); ?>

<div class="item_full">
	<?php if($params->get('item_title', 1)) : ?>
	<div <?php echo $news_uid_css_title; ?>class="item_title">
		<div class="contentheading"><h1><?php echo $row->title; ?></h1></div>
	</div>
	<?php endif; ?>

	<?php 
	$loadbot_onAfterDisplayTitle;
	$loadbot_onBeforeDisplayContent;
	?>

	<div class="item_info">
		<?php if($params->get('createdate', 0)) : ?>
		<span class="date"><strong><?php echo _E_START_PUB; ?></strong> <?php echo $create_date; ?></span>
		<?php endif; ?>

		<?php if($params->get('author', 0)) : ?>
		<span class="author"><strong><?php echo _AUTHOR; ?>:</strong> <?php echo $author; ?></span>
		<?php endif; ?>


		<div class="buttons_wrap">
			<?php if($params->get('print')) : ?>
				<?php mosHTML::PrintIcon($row, $params, $hide_js, $print_link); ?>
			<?php endif; ?>
			<?php if($params->get('email')) : ?>
				<?php ContentView::EmailIcon($row, $params, $hide_js); ?>
			<?php endif; ?>
		</div>

		<?php echo $row->rating; ?>
	</div>

	<div <?php echo $news_uid_css_body; ?>class="item_body">
		<?php if($params->get('section') || $params->get('category')) : ?>
		<div class="section_cat">
				<?php if($params->get('section')) : ?>
			<span class="section_name"><?php echo $row->section; ?></span>
				<?php endif; ?>

				<?php if($params->get('category')) : ?>
			<span class="cat_name"><?php echo $row->category; ?></span>
				<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php if($params->get('url') && $row->urls) : ?>
		<div class="blog_urls">
			<a href="http://<?php echo $row->urls; ?>" target="_blank"><?php echo $row->urls; ?></a>
		</div>
		<?php endif; ?>

		<?php if(isset($row->toc)) : ?>
		<div class="toc"><?php echo $row->toc; ?></div>
		<?php endif; ?>

		<?php if($params->get('view_introtext', 1)) : ?>
		<div class="item_text"><?php echo ampReplace($row->text); ?></div>
		<?php endif; ?>
	</div>

	<?php if($access->canEdit) : ?>	<span class="edit_item"><?php echo $edit; ?></span><?php endif; ?>

	<div class="item_info_bottom">
		<?php if($params->get('tags')) : ?>
		<span class="tags"><strong><?php echo _TAGS; ?></strong> <?php echo isset($row->tags)?$row->tags : _TAGS_NOT_DEFINED; ?></span>
		<?php endif; ?>

		<?php if($params->get('hits')) : ?>
		<span class="hits"><strong><?php echo _HITS; ?></strong> <?php echo $row->hits?$row->hits : _HITS_NOT_FOUND; ?></span>
		<?php endif; ?>

		<?php if($params->get('modifydate') && $mod_date != '') : ?>
		<span class="modified_date">
			<strong><?php echo _LAST_UPDATED; ?> </strong> <?php echo $mod_date; ?>
		</span>
		<?php endif; ?>
	</div>
	<?php echo ContentView::afterDisplayContent($row, $params, $page); ?>
	<br />
	<?php ContentView::Navigation($row, $params); ?>
	<?php mosHTML::CloseButton($params, $hide_js); ?>
	<?php mosHTML::BackButton($params, $hide_js); ?>
</div>