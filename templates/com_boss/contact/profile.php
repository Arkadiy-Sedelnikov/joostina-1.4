<div class="boss_pathway">
<?php $this->displayPathway(); ?>
</div>
<div class="contentheading">
<?php echo BOSS_EDIT_PROFILE; ?>
</div>
<br />
<table cellpadding="5" cellspacing="0" border="0" width="100%">
<?php $this->displayProfileField("username"); ?>
<tr>
	<td colspan="2">
		<?php echo BOSS_PROFILE_PASSWORD; ?>
	</td>
</tr>
<?php $this->displayProfileField("password"); ?>
<?php $this->displayProfileField("vpassword"); ?>
<tr>
	<td colspan="2">
		<?php echo BOSS_PROFILE_CONTACT; ?>
	</td>
</tr>
<?php $this->displayProfileField("name"); ?>
<?php $this->displayProfileField("email"); ?>
<?php $this->displayCustomProfileFields(); ?>
<tr>
	<td colspan="2">
            <span class="button">
		<input class="button" type="button" value="<?php echo BOSS_FORM_SUBMIT_TEXT; ?>" onclick="submitbutton()" />
            </span>
        </td>
</tr>
</table>