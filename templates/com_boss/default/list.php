<div align="center" class="boss_innermenu">
</div>
<h1 class="contentheading">
<?php $this->displayCatTitle(); ?>
</h1>
<div class="boss_pathway">
<?php $this->displayPathway(); ?>
</div>
<div class="alphaindex">
      <?php $this->displayAlphaIndex($this->directory, $this->itemid); ?>
</div>
<div class="boss_filter">
    <?php $this->displayFilter(1); ?>
</div>
<div class="boss_subcats">
<?php $this->displaySubcats(); ?>
</div>
<div class="boss_description">
<?php $this->displayCatDescription(); ?>
</div>
<div align="right"><h2><?php $this->displayWriteLink(); ?></h2></div>
<br />
<div align="left">
	<?php $this->displayPagesCounter(); ?>
</div>
<div align="center">
    <?php $this->displayOrderOption(); ?>
</div>
<br/>
<?php $this->displayContents(); ?>
<p align="center">
<?php echo $this->displayPagesLinks(); ?>
</p>