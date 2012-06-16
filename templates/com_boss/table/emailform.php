<table cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td colspan="2">
			<?php echo BOSS_EMAIL_FRIEND; ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td width="130">
			<?php echo BOSS_EMAIL_FRIEND_ADDR; ?>
		</td>
		<td>
			<input type="text" name="email" class="inputbox" size="25"/>
		</td>
	</tr>
	<tr>
		<td height="27">
			<?php echo BOSS_EMAIL_YOUR_NAME; ?>
		</td>
		<td>
			<input type="text" name="yourname" class="inputbox" size="25"/>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo BOSS_EMAIL_YOUR_MAIL; ?>
		</td>
		<td>
			<input type="text" name="youremail" class="inputbox" size="25"/>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo BOSS_SUBJECT_PROMPT; ?>
		</td>
		<td>
			<input type="text" name="subject" class="inputbox" maxlength="100" size="40"/>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">
        <span class="button">
            <input type="submit" name="submit" class="button" value="<?php echo BOSS_BUTTON_SUBMIT_MAIL; ?>"/>
	</span>
			&nbsp;&nbsp;
        <span class="button">
            <input type="button" name="cancel" value="<?php echo BOSS_BUTTON_CANCEL; ?>" class="button" onclick="window.close();"/>
	</span>
		</td>
	</tr>
</table>