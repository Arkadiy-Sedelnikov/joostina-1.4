<table class="table_of_items" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr class="tr-second">

		<th><?php echo BOSS_FORM_MESSAGE_TITLE; ?></th>

		<?php  foreach($this->fieldsgroup as $fieldsgroup){ ?>
		<th><?php echo $fieldsgroup[0]->title; ?></th>
		<?php } ?>

	</tr>
	<?php $this->displayContents('frontpage'); ?>
</table>
<p align="center">
	<?php echo $this->displayPagesLinks(); ?>
</p>