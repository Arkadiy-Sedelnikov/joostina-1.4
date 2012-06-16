<tr>
	<td>
		<?php $this->displayContentTitle($content); ?>
	</td>
	<?php
	foreach($this->fieldsgroup as $fieldsgroup){
		$this->loadFieldsInGroup($content, $fieldsgroup[0]->gname, null, "<td>", "</td>", 0, 0, 0);
	}
	?>
</tr>