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
		<div class="boss_tpl_image">
			<?php $this->loadFieldsInGroup($content, "ConImage", ""); ?>
		</div>
		<div class="boss_tpl_txt">
			<h5><?php $this->displayCategoryTitle($content, 3); ?></h5>

			<div class="boss_tpl_description"><?php $this->loadFieldsInGroup($content, "ConInfo", "<br />"); ?></div>
		</div>

		<br style="clear: both"/>

		<?php  if($this->countFieldsInGroup("ConDescription")){ ?>
		<div class="boss_tpl_description"><?php $this->loadFieldsInGroup($content, "ConDescription", "<br />"); ?></div>
		<?php } ?>

		<?php if($this->displayTags()){ ?>
		<div class="tags">
			<?php echo $this->displayTags(); ?>
		</div>
		<?php } ?>
	</div>
</div>

<div class="comments">
	<?php $this->displayContentHits($content); ?>
	<?php if($this->isReviewAllowed()){
	echo '&nbsp;&nbsp;';
	$this->comments->displayNumReviews($content, $this->reviews, $this->conf);
} ?>
</div>
<hr>
<?php if($this->isReviewAllowed()){ ?>
<h2 class="componentheading2">
	<?php echo BOSS_REVIEWS; ?>
</h2>

<div class="boss_reviews">
	<?php $this->comments->displayReviews($content, $this->directory, $this->conf, $this->reviews); ?>
</div>

<br/>

<h2 class="componentheading2">
	<?php echo BOSS_ADD_REVIEWS; ?>
</h2>
<div>
	<?php $this->comments->displayAddReview($this->directory, $content, $this->conf); ?>
</div>
<?php
}
?>

<div class="boss_tpl_edit"><?php $this->displayContentEditDelete($content); ?></div>
