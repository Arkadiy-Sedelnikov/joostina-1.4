<div class="boss_content">
	<h1>
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
	<div class="cf"></div>
</div>