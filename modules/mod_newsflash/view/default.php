<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined( '_VALID_MOS' ) or die();

if ($params->get('numrows',0)) {
	?>
<div class="mod_newsflash<?php echo $params->get('moduleclass_sfx', '');?>">
	<ul>
			<?php foreach ($items as $row): ?>
				<?php $module->helper->prepare_row($row, $params);?>
		<li>
					<?php if($params->get('image',1)): ?>
						<?php echo $row->image;?>
					<?php endif; ?>

					<?php if($params->get('createdate',1)): ?>
			<span class="date"><?php echo mosFormatDate($row->created); ?></span>
					<?php endif; ?>

					<?php if($params->get('show_author',0)): ?>
			<span class="author"><?php echo $row->author;?></span>
					<?php endif; ?>

					<?php if($params->get('item_title',1)): ?>
			<h4><?php echo $row->title;?></h4>
					<?php endif; ?>

					<?php if($params->get('text',1)): ?>
						<?php echo $row->text;?>
					<?php endif; ?>

					<?php if($params->get('readmore', 0)):?>
			<div class="readmore"><?php echo $row->readmore ;?></div>
					<?php endif; ?>

		</li>
			<?php endforeach; ?>
	</ul>
</div>
	<?php } ?>