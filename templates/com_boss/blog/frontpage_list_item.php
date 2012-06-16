<div class="contentitem">
	<h3 class="contentheading">
		<?php $this->displayContentTitle($content); ?>
		<span class="boss_cat">
            (<?php $this->displayCategoryTitle($content, 1); ?>)
        </span>
	</h3>

	<div>
		<?php $this->displayContentDate($content); echo " - "; $this->displayContentBy($content);  ?>
	</div>
	<?php if($this->isReviewAllowed()){ ?>
	<div>
		<?php
		$this->comments->displayNumReviews($content, $this->reviews, $this->conf);
		?>
	</div>
	<?php } ?>
	<div class="content_list">

		<?php if($this->countFieldsInGroup("ListDescription")){
		$this->loadFieldsInGroup($content, "ListDescription", "&nbsp;");
	} ?>

	</div>
	<div class="tags">
		<?php $this->displayListTags($content); ?>
	</div>
	<div class="list_profile">
		<?php $this->showProfile($content); ?>
	</div>
</div>