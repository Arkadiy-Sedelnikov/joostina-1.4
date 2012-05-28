<div class="dotteddiv">
<h3>
<?php $this->displayReviewTitle($review); ?>

</h3>
<div>
	<?php $this->displayReviewContent($review); ?>
</div>
<div align="right">
<i><?php echo BOSS_BY; ?><?php $this->displayReviewUser($review); ?> - <?php $this->displayReviewDate($review); ?></i>
</div>
</div>