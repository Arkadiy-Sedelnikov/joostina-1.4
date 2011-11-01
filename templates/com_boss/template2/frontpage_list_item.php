<div class="contentitem">
<h3>
	<?php $this->displayContentTitle($content); ?>
	<span class="boss_cat">(
		<?php $this->displayCategoryTitle($content,1); ?>
	)</span>
</h3>
<?php if ($this->isReviewAllowed()) { ?>
<div>
	<?php 
            $this->comments->displayNumReviews($content, $this->reviews, $this->conf);
	?>	
</div>
<?php }
	if ($this->countFieldsInGroup("GroupList1"))
		$this->loadFieldsInGroup($content,"GroupList1"," <br/> "); 
?>
<table width="100%" border="0">
<tr>
<td>
            	<?php
            	if ($this->countFieldsInGroup("ListImage"))
            			$this->loadFieldsInGroup($content,"ListImage"," <br/> ");
                ?>
	<?php $this->displayContentEditDelete($content); ?>
</td>
</tr>
</table>
<div>
	<?php 
	if ($this->countFieldsInGroup("GroupList2"))
			$this->loadFieldsInGroup($content,"GroupList2"," <br/>"); 
	?>
</div>
<div align="right">
	<?php $this->displayContentHits($content); ?>
</div>
</div>