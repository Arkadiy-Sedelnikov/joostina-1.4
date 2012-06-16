<table class="show_profile">
	<tr>
		<td colspan="2">
			<?php echo BOSS_PROFILE_CONTACT; ?>
		</td>
	</tr>
	<?php $this->showProfileField("username", $content); ?>
	<?php $this->showProfileField("name", $content); ?>
	<?php $this->showProfileField("email", $content); ?>
	<?php $this->showCustomProfileFields($content, $this->profileFields); ?>
</table>