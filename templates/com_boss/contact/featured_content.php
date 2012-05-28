<!-- Вывод выделенного материала в категории -->
<div class="boss_tpl_featured">
	<div class="boss_tpl_img">
		<?php $this->loadFieldsInGroup($content, "catImage", ""); ?>
	</div>
	<div class="boss_tpl_txt">
		<h3><?php $this->displayContentTitle($content); ?></h3>
		<h5><?php $this->displayCategoryTitle($content, 3); ?></h5>
		<div class="boss_tpl_subtitle"><?php $this->loadFieldsInGroup($content, "catSubtitle", "<br />"); ?></div>
	</div>
	<div class="boss_tpl_edit"><?php $this->displayContentEditDelete($content); ?></div>
</div>
