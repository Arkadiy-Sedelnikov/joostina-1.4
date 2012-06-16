<?php
/*<h1 class="contentheading"><?php echo BOSS_LAST_CONTENTS;?></h1>
<div class='boss_box_module'>
<div class='boss_inner_box'>
<?php $this->displayLastContents(); ?>
<div class="boss_spacer"></div>
</div>
</div>
*/
?>
<h1 class="contentheading">
	<?php $this->displayDirectoryName(); ?>
</h1>
<div class="boss_fronttext">
	<?php echo $this->displayFrontText(); ?>
</div>
<div align="center" class="boss_innermenu">
	<h2>
		<?php
		$this->displayWriteLink();
		echo " | ";
		$this->displayAllContentsLink();
		echo " | ";
		$this->displayProfileLink();
		echo " | ";
		$this->displayUserContentsLink();
		echo " | ";
		$this->displaySearchLink();
		echo " | ";
		$this->displayRulesLink(); ?>
	</h2>
</div>
<br/>
<div class="boss_categories" align="center">
	<?php $this->displayCategories(); ?>
</div>