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

<div class="boss_vote">
	<?php if($this->isReviewAllowed()){ ?>
	<?php
	$this->comments->displayNumReviews($content, $this->reviews, $this->conf);
	?>

	<?php } ?>

</div>

<div class="content_desc">
	<?php
	if($this->countFieldsInGroup("DetailsDescription"))
		$this->loadFieldsInGroup($content, "DetailsDescription", "<br/>");
	echo '<br>';
	$this->displayPms($content, 1); // 0 = public, 1 = private
	?>
</div>
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
