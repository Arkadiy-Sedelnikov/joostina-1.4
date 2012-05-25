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
		<?php echo BOSS_FORM_CATEGORY; ?>
	</td>
	<td>
		<?php $this->displayCategoriesSelectSearch(); ?>
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
        <input type="submit" value="<?php echo BOSS_SUBMIT_BUTTON; ?>" />
    </span>
</div>