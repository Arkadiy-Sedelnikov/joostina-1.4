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
if(!count($other_categories)) {
	return;
}
?>
<ul class="cat_list">
	<?php foreach ($other_categories as $row) {
		$row->name = htmlspecialchars(stripslashes(ampReplace($row->name)), ENT_QUOTES);
		if($catid != $row->id) { ?>
	<li>
				<?php if($row->access <= $gid) {
					$link = sefRelToAbs('index.php?option=com_content&amp;task=category&amp;sectionid='.$row->section.'&amp;id='.$row->id.'&amp;Itemid='.$Itemid); ?>
		<a href="<?php echo $link; ?>" class="category" title="<?php echo $row->name; ?>"><?php echo $row->name; ?></a>
					<?php if($params->get('cat_items')) { ?>
		&nbsp;<i>( <?php echo $row->numitems; ?> )</i>
						<?php } ?>
					<?php if($params->get('cat_description') && $row->description) { ?>
		<br />
						<?php echo $row->description; ?>
						<?php } ?>
					<?php } else { ?>
					<?php echo $row->name; ?>
		<a href="<?php echo sefRelToAbs('index.php?option=com_registration&amp;task=register'); ?>">( <?php echo _E_REGISTERED; ?> )</a>
					<?php } ?>
	</li>
			<?php }
	} ?>
</ul>