<?php /**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die(); ?>

<?php if($params->get('item_title')) : ?>
<div <?php echo $news_uid_css_title; ?> class="contentheading"><h2><?php echo $row->title; ?></h2></div>
<?php endif; ?>

<?php $loadbot_onAfterDisplayTitle;
$loadbot_onBeforeDisplayContent; ?>

<?php if($params->get('print') || $params->get('email')) : ?>
<div class="buttons_wrap">
		<?php if($params->get('print')) : ?>
			<?php mosHTML::PrintIcon($row, $params, $hide_js, $print_link); ?>
	<?php endif; ?>

		<?php if($params->get('email')) : ?>
			<?php ContentView::EmailIcon($row, $params, $hide_js); ?>
	<?php endif; ?>
</div>
<?php endif; ?>	

<?php if($params->get('createdate', 0)) : ?>
<span class="date"><?php echo $create_date; ?></span>
<?php endif; ?>

<?php if($params->get('modifydate') && $mod_date != '') : ?>
<span class="modified_date">
	(<strong><?php echo _LAST_UPDATED; ?> </strong> <?php echo $mod_date; ?>)
</span>
<?php endif; ?>

<?php if($params->get('author', 0)) : ?>
<span class="author"><?php echo $author; ?></span>
<?php endif; ?>

<?php if($params->get('section') || $params->get('category')) : ?>
<span class="section_cat">	
	<?php if($params->get('section')) : ?>
	<span class="section_name"><?php echo $row->section; ?></span>
	<?php endif; ?>

	<?php if($params->get('category')) : ?>
	<span class="cat_name"><?php echo $row->category; ?></span>
	<?php endif; ?>
</span>
<?php endif; ?>


<div <?php echo $news_uid_css_body; ?>class="item_body">

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

<?php if($params->get('rating')) : ?>		
	<div class="item_rating"><?php echo $row->rating; ?></div>
<?php endif; ?>


<?php if($params->get('readmore')) : ?>
	<span class="readmore"><?php echo $readmore; ?></span>
<?php endif; ?>

<?php if($access->canEdit) : ?>	
	<span class="edit_item"><?php echo $edit; ?></span>
<?php endif; ?>

</div>

<?php if($params->get('hits')) : ?>
<span class="hits"><strong><?php echo _HITS; ?></strong> <?php echo $row->hits?$row->hits : _HITS_NOT_FOUND; ?></span>
<?php endif; ?>

<?php if($params->get('view_tags')) : ?>
	<?php if(isset($row->tags)) : ?>
<span class="tags"><?php echo _TAGS ?> <?php echo $row->tags; ?></span>
	<?php else: ?>
<span class="tags"><?php echo _TAGS_NOT_DEFINED ?></span>
	<?php endif; ?>
<?php endif; ?>
<?php echo ContentView::afterDisplayContent($row, $params, $page); ?>