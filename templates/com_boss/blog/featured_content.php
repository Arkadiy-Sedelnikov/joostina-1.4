<table class="contentitem">
<tr class="firstline" >
	<td colspan="3" >
	<?php $this->displayContentTitle($content); ?>
	<span class="boss_cat">(
		<?php $this->displayCategoryTitle($content,1); ?>
	)</span>
	<td>
</tr>
<tr>
<td width = '70%' valign="top">
    	<?php if ($this->isReviewAllowed()) { ?>
	<div>
		<?php 
                $this->comments->displayNumReviews($content, $this->reviews, $this->conf);
		?>
	</div>
	<?php } ?>
    <div class="tags">
        <?php $this->displayListTags($content); ?>
    </div>
	<?php $this->displayContentEditDelete($content); ?>
</td>
<td width = '25%' valign="top">
            	<?php
            	if ($this->countFieldsInGroup("ListImage"))
            			$this->loadFieldsInGroup($content,"ListImage"," <br/> ");
                ?>
</td>
<td width = '50%' valign="top">
	<?php 
	if ($this->countFieldsInGroup("GroupList1"))
			$this->loadFieldsInGroup($content,"GroupList1"," <br/> ");
        ?>
        <br />
        <?php
	if ($this->countFieldsInGroup("GroupList2"))
			$this->loadFieldsInGroup($content,"GroupList2"," <br/>"); 
	?>
</td>
</tr>
<tr>
<td colspan='2'>
	<?php $this->displayContentDate($content); echo " - "; $this->displayContentBy($content);  ?>
</td>
<td>
	<?php $this->displayContentHits($content); ?>
</td>
</tr>
</table>
<br/>