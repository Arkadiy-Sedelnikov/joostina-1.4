<div class="boss_pathway">
	<?php $this->displayPathway(); ?>
</div>
<table width="100%" border="0" align="center" cellpadding="4" cellspacing="0" class="contentpane">
	<tr>
		<td colspan="2">
			<div class="contentheading">
				<?php echo BOSS_LOGIN; ?>
			</div>
			<div>
				<?php echo '<img src="/images/stories/key.jpg" align="right" hspace="10" alt="" />'; ?>
				<?php echo BOSS_LOGIN_DESCRIPTION; ?>
				<br/><br/>
			</div>
		</td>
	</tr>
	<tr>
		<td align="center" width="50%">
			<br/>
			<table>
				<tr>
					<td align="center">
						<?php echo BOSS_USERNAME; ?>
						<br/>
					</td>
					<td align="center">
						<?php echo BOSS_PASSWORD; ?>
						<br/>
					</td>
				</tr>
				<tr>
					<td align="center">
						<input name="username" type="text" class="inputbox" size="20"/>
					</td>
					<td align="center">
						<input name="passwd" type="password" class="inputbox" size="20"/>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<br/>
						<?php echo BOSS_REMEMBER_ME; ?>
						<input type="checkbox" name="remember" class="inputbox" value="yes"/>
						<br/>
						<?php $this->displayLostPasswordLink(); ?>
						<br/>
						<?php echo BOSS_NO_ACCOUNT; ?>
						<?php $this->displayCreateAccountLink(); ?>
						<br/><br/><br/>
					</td>
				</tr>
			</table>
		</td>
		<td>
			<div align="center">
            <span class="button">
                <input type="submit" name="submit" class="button" value="<?php echo BOSS_BUTTON_LOGIN; ?>"/>
            </span>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<noscript>
				<?php echo BOSS_CMN_JAVASCRIPT; ?>
			</noscript>
		</td>
	</tr>
</table>