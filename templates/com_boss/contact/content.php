<div class="boss_tpl_content">
	<div class="boss_pathway">
		<?php $this->displayPathway(); ?>
	</div>
	<h1 class="detailstitle">
		<?php $this->displayContentTitle($content, false); ?>
		&nbsp;<?php $this->PrintIcon($content, '<img src="/images/M_images/printButton.png" alt="Print" align="top" />');?>
		&nbsp;<?php $this->EmailIcon($content, '<img src="/images/M_images/emailButton.png" alt="Email" align="top" />');?>
	</h1>

	<div class="boss_vote">
		<?php $this->rating->displayVoteForm($content, $this->directory, $this->conf); ?>
	</div>

	<div class="details">
		<div class="boss_tpl_img">
			<?php $this->loadFieldsInGroup($content, "conImage", ""); ?>
		</div>
		<div class="boss_tpl_txt">
			<h5><?php $this->displayCategoryTitle($content, 3); ?></h5>
			<div class="boss_tpl_subtitle"><?php $this->loadFieldsInGroup($content, "conSubtitle", "<br />"); ?></div>
		</div>

		<br style="clear: both"/>

		<?php  if($this->countFieldsInGroup("conDescription")){ ?>
			<div class="boss_tpl_subtitle"><?php $this->loadFieldsInGroup($content, "conDescription", "<br />"); ?></div>
		<?php } ?>

		<?php if($this->displayTags()){ ?>
		<div class="tags">
			<?php echo $this->displayTags(); ?>
		</div>
		<?php } ?>

		<div class="boss_tpl_edit"><?php $this->displayContentEditDelete($content); ?></div>
	</div>
</div>
