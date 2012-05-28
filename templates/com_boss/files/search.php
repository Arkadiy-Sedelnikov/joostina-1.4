<div class="boss_pathway">
	<?php $this->displayPathway(); ?>
</div>
<h1 class="contentheading">
	<?php echo BOSS_SEARCH; ?>
</h1>
<div class="boss_search_box">
	<div class="boss_inner_box">
		<div align="left">
			<table>
				<tr>
					<td>
						<?php echo BOSS_CONTENT_TYPES; ?>
					</td>
					<td>
						<?php $this->displayContentTypesSelect('search'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo BOSS_FORM_CATEGORY; ?>
					</td>
					<td>
						<?php $this->displayCategoriesSelect('search'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo BOSS_NAME_DIR; ?>
					</td>
					<td>
						<input class="boss_required" mosreq="1" id="name_search" type="text" moslabel="Название"
							   name="name_search" size="20" maxlength="20" value="">
					</td>
				</tr>
				<?php $this->displaySearchFields(); ?>
			</table>
		</div>
	</div>
</div>
<br/>
<div align="center">
    <span class="button">
        <input type="submit" value="<?php echo BOSS_SUBMIT_BUTTON; ?>"/>
    </span>
</div>