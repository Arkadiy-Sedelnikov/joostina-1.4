<div class="boss_pathway">
	<?php $this->displayPathway(); ?>
</div>
<h1 class="detailstitle">
	<?php $this->displayContentTitle($content, false); ?>
	&nbsp;<?php $this->PrintIcon($content, '<img src="/images/M_images/printButton.png" alt="Print" align="top" />');?>
	&nbsp;<?php $this->EmailIcon($content, '<img src="/images/M_images/emailButton.png" alt="Email" align="top" />');?>
</h1>
<?php if($this->displayTags()){ ?>
<div class="tags">
	<?php echo $this->displayTags(); ?>
</div>
<?php } ?>

<div class="details">
	<div class="boss_vote">
		<?php $this->rating->displayVoteForm($content, $this->directory, $this->conf); ?>
		<div>
			<?php
			if($this->isReviewAllowed()){
				$this->comments->displayNumReviews($content, $this->reviews, $this->conf);
				echo "<br/>";
			}
			?>
		</div>
		<div>
			<?php $this->displayContentEditDelete($content); ?><br/>
		</div>
	</div>
	<div class="details_profile">
		<?php $this->showProfile($content); ?>
	</div>
	<br style="clear: both"/>

	<div class="details_subtitle1">
		<?php
		if($this->countFieldsInGroup("DetailsSubtitle1")) $this->loadFieldsInGroup($content, "DetailsSubtitle1", "<br/>", null, null, 0);
		?>
	</div>
	<div class="details_subtitle2">
		<?php
		if($this->countFieldsInGroup("DetailsSubtitle2")) $this->loadFieldsInGroup($content, "DetailsSubtitle2", "<br/>", null, null, 0);
		?>
	</div>
	<div class="details_subtitle3">
		<?php
		if($this->countFieldsInGroup("DetailsSubtitle3")) $this->loadFieldsInGroup($content, "DetailsSubtitle3", "<br/>", null, null, 0);
		?>
	</div>


	<div class="other">
		<?php
		if($this->countFieldsInGroup("DetailsDescription")) $this->loadFieldsInGroup($content, "DetailsDescription", "&nbsp;");
		?>
	</div>
	<div class="boss_contents_image">
		<?php
		if($this->countFieldsInGroup("DetailsImage")) $this->loadFieldsInGroup($content, "DetailsImage", "<br/>");
		?>
	</div>
	<div class="other">
		<?php
		if($this->countFieldsInGroup("DetailsFullText")) $this->loadFieldsInGroup($content, "DetailsFullText", "<br/>");
		?>
	</div>

	<div class="details_bottom">
		<?php
		if($this->countFieldsInGroup("DetailsBottom")) $this->loadFieldsInGroup($content, "DetailsBottom", "<br/>");
		echo '<br>';
		$this->displayPms($content, 1); // 0 = public, 1 = private
		?>
	</div>
</div>
<?php if($this->isReviewAllowed()){ ?>
<?php if($this->conf->comment_sys == 1){ ?>
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
	<?php } ?>
<div>
	<?php $this->comments->displayAddReview($this->directory, $content, $this->conf); ?>
</div>
<?php
}
?>
