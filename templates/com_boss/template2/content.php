<div class="boss_pathway">
<?php $this->displayPathway(); ?>
</div>
<h1 class="detailstitle">
	<?php $this->displayContentTitle($content,false); ?>
	&nbsp;<?php $this->PrintIcon($content,'<img src="/images/M_images/printButton.png" alt="Print" align="top" />');?>
	&nbsp;<?php $this->EmailIcon($content,'<img src="/images/M_images/emailButton.png" alt="Email" align="top" />');?>
</h1>
<div class="details">
	<div class="description">
	<?php $this->displayContentEditDelete($content); ?><br />
	<?php 
	if ($this->countFieldsInGroup("GroupDetails2"))
		$this->loadFieldsInGroup($content,"GroupDetails2","<br/>");
	?>
	<br/>
	<div class="other">
	<?php 
	if ($this->countFieldsInGroup("GroupDetails3"))
		$this->loadFieldsInGroup($content,"GroupDetails3","<br/>");
	?>
	</div>
	</div>
	<div class="right">
	<?php 
	if ($this->countFieldsInGroup("GroupDetails1"))
		$this->loadFieldsInGroup($content,"GroupDetails1","<br/>");
    echo '<br>';
    $this->displayPms($content,1); // 0 = public, 1 = private
	?>
	</div>
	<div class="boss_spacer">
</div>
</div>
<h2 class="componentheading2">
	<?php echo BOSS_GALLERY; ?>
</h2>
<div class="boss_contents_image">
<?php
if ($this->countFieldsInGroup("DetailsImage"))
	$this->loadFieldsInGroup($content,"DetailsImage","<br/>");
?>
</div>	
<div class="boss_spacer">
</div>	
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
