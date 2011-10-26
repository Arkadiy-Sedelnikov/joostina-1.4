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
?>
<div class="category_page<?php echo $sfx; ?>">
	<?php if ($page_title) { ?>
	    <div class="componentheading"><h1><?php echo $page_title; ?></h1></div>
<?php } ?>
    <div class="contentpane<?php echo $sfx; ?>">
			<?php if ($title_description || $title_image) { ?>
	        <div class="contentdescription">
				<?php if ($title_image) { ?>
		            <div class="desc_img"><?php echo $title_image; ?></div>
				<?php } ?>
				<?php if ($title_description) { ?>
		            <p><?php echo $title_description; ?></p>
			<?php } ?>
	        </div>
<?php } ?>
		<?php
		//Подключаем шаблон вывода таблицы с записями
		include_once (JPATH_BASE . '/components/com_content/view/item/table_of_items/default.php');
		?>
		<?php if ($add_button) { ?>
	        <div class="add_button"><?php echo $add_button; ?></div>
		<?php } ?>
		<?php
		if ($show_categories) {
			include_once (JPATH_BASE . '/components/com_content/view/category/show_categories/default.php');
		}
		?>
		<?php mosHTML::BackButton($params); ?>
    </div>
</div>