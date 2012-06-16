<table cellpadding="5" cellspacing="0" border="0" width="200px">
	<td colspan="2">
		<?php echo BOSS_PROFILE_CONTACT; ?>
	</td>
	</tr>
	<?php $this->showProfileField("username", $content); ?>
	<?php $this->showProfileField("name", $content); ?>
	<?php $this->showProfileField("email", $content); ?>
	<?php $this->showCustomProfileFields($content, $this->profileFields); ?>
</table>