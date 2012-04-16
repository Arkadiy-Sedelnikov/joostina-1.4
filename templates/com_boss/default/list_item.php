<div class="list_item">
    <div class="list_title">
        <h3>
            <?php $this->displayContentTitle($content); ?>
            <span class="boss_cat">(
                    <?php $this->displayCategoryTitle($content,1); ?>
            )</span>
        </h3>
    </div>
        <div class="list_header">
        	<?php if ($this->isRatingAllowed()||$this->isReviewAllowed()) { ?>
			<div>
				<?php $this->rating->displayVoteResult($content, $this->directory, $this->conf); ?>
        		<?php if ($this->isReviewAllowed()) {
        			   $this->comments->displayNumReviews($content, $this->reviews, $this->conf);
        			   echo "<br/><br/>";
        		}
        		?>
        	</div>
        	<?php } ?>

            <?php if ($this->displayListTags($content)) { ?>
                <div class="tags">
                    <?php echo $this->displayListTags($content); /*$this->displayListTags($content);*/ ?>
                </div>
            <?php } ?>

        	<?php $this->displayContentEditDelete($content); ?>
        </div>
        <div class="list_subtitle">
        	<?php
        	if ($this->countFieldsInGroup("ListSubtitle"))
        			$this->loadFieldsInGroup($content,"ListSubtitle"," <br/> ");
            ?>
        </div>
        <div>
            <div class="list_image">
            	<?php
            	if ($this->countFieldsInGroup("ListImage"))
            			$this->loadFieldsInGroup($content,"ListImage"," <br/> ");
                ?>
            </div>
            <div class="list_content">
            	<?php
            	if ($this->countFieldsInGroup("ListDescription"))
            			$this->loadFieldsInGroup($content,"ListDescription"," <br/> ");
                ?>
            </div>
        </div>
    <div class="list_bottom">
        	<?php
        	if ($this->countFieldsInGroup("ListBottom"))
        			$this->loadFieldsInGroup($content,"ListBottom"," <br/> ");
            ?>
        </div>
    <div class="list_footer">
        <div class="list_date">
    	    <?php $this->displayContentDate($content); echo " - "; $this->displayContentBy($content);  ?>
        </div>
        <div class="list_hits">
    	    <?php $this->displayContentHits($content); ?>
        </div>
        <div class="list_profile">
            <?php $this->showProfile($content); ?>
        </div>
    </div>
</div>