<div class="boss_pathway">
<?php $this->displayPathway(); ?>
</div>
<h1 class="detailstitle">
	<?php $this->displayContentTitle($content,false); ?>
	&nbsp;<?php $this->PrintIcon($content,'<img src="/images/M_images/printButton.png" alt="Print" align="top" />');?>
	&nbsp;<?php $this->EmailIcon($content,'<img src="/images/M_images/emailButton.png" alt="Email" align="top" />');?>
</h1>

<?php if ($this->displayTags()) { ?>
    <div class="tags">
        <?php echo $this->displayTags(); ?>
    </div>
<?php } ?>

<table class="details">
<tr>
	<td width ="30%">
		<div class="boss_vote">
			<div>
				<?php 
				if ($this->isReviewAllowed()) { 
					  echo " - ";
					  $this->comments->displayNumReviews($content, $this->reviews, $this->conf);
					  echo "<br/>";
				}
				?>
			</div>
		</div>
	</td>
	<td width ="30%" class="center">
	<?php
	if ($this->countFieldsInGroup("GroupDetails1") && $conf->show_contact == 1)
		$this->loadFieldsInGroup($content,"GroupDetails1","<br/>",null,null,0);
	if ($this->countFieldsInGroup("GroupDetails1") && $conf->show_contact == 0)
		$this->loadFieldsInGroup($content,"GroupDetails1","<br/>",null,null,1);
	?>
	</td>
	<td width ="40%" class="right">
	<?php 
	if ($this->countFieldsInGroup("GroupDetails2"))
		$this->loadFieldsInGroup($content,"GroupDetails2","<br/>",null,null,0);
	?>
	</td>
</tr>
</table>
<?php $this->displayContentEditDelete($content); ?><br />
<br/>
<table width="100%" border=0>
<tr>
<td width="80%">
<?php 
if ($this->countFieldsInGroup("GroupDetails3"))
	$this->loadFieldsInGroup($content,"GroupDetails3","<br/>");
?>
</td>
<td valign="top">
<div class="boss_contents_image">
<?php
if ($this->countFieldsInGroup("DetailsImage"))
	$this->loadFieldsInGroup($content,"DetailsImage","<br/>");
?>
</div>
</td>
</tr>
</table>
<div class="other">
<?php 
if ($this->countFieldsInGroup("GroupDetails4"))
	$this->loadFieldsInGroup($content,"GroupDetails4","<br/>");
  echo '<br>';
  $this->displayPms($content,1); // 0 = public, 1 = private
?>
</div>
<?php if ($this->isReviewAllowed()) { ?>
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
