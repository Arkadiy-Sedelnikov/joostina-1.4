<!-- Главная страница: вывод каталогов -->
<div class="boss_tpl_front">
	<h1 class="contentheading"><?php $this->displayDirectoryName(); ?></h1>

	<div class="boss_tpl_fronttext"><?php echo $this->displayFrontText(); ?></div>
	<div class="boss_tpl_innermenu">
		<ul>
			<li><?php $this->displayWriteLink(); ?>
			<li><?php $this->displayAllContentsLink(); ?>
			<li><?php $this->displayProfileLink(); ?>
			<li><?php $this->displayUserContentsLink(); ?>
			<li><?php $this->displaySearchLink(); ?>
			<li><?php $this->displayRulesLink(); ?>
		</ul>
	</div>
	<div class="clear"></div>
	<div class="boss_tpl_categories"><?php $this->displayCategories(1, 1, 1); ?></div>
</div>