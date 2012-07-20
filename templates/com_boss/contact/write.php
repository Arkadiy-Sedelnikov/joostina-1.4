<div class="boss_pathway"><?php $this->displayPathway(); ?></div>
<div class="boss-warning"><?php $this->displayWarningNoAccount(); ?></div>
<div class="boss-rules">
	<a href="<?php echo JSef::getUrlToSef("index.php?option=com_boss&task=show_rules&directory=$this->directory"); ?>" target=_blank>
		<?php echo BOSS_RULESREAD; ?>
	</a>
</div>
<h1 class="contentheading"><?php $this->displayFormType(); ?></h1>
<div id="boss_writecontent_header">
	<div id="writecontent_header1"><?php echo BOSS_HEADER1; ?></div>
	<div id="writecontent_header2"><?php echo BOSS_HEADER2; ?></div>
</div>

<?php $this->displayErrorMsg(); ?>

<fieldset id="boss_fieldset" width="90%" align="center">
	<legend><?php $this->displayFormType(); ?></legend>
	<!-- category -->
	<label for="content_kindof"><?php echo BOSS_CONTENT_TYPES; ?></label>
	<?php $this->displayContentTypesSelect(); ?>
	<?php if($this->isContentTypeSelected()){ ?>
	<!--fields -->
	<?php $this->displayFormBegin(); ?>
	<?php if($this->isAccountCreation()){ ?>
		<br/>
		<h2 class="contentheading"><?php echo BOSS_FORM_ALERT; ?></h2>
		<?php $this->displayAccountCreationFields(); ?>
		<?php } ?>
	<br/>
	<h2 class="contentheading"><?php echo BOSS_FORM_INFORMATION; ?></h2>
	<!--fields -->
	<label for="category"><?php echo BOSS_FORM_CATEGORY; ?></label>
	<?php $this->displayCategoriesSelect(); ?>
	<br/>
	<table><?php $this->displayFormFields(); ?></table>
	<!-- security -->
	<?php if($this->isContentCaptchaActivated() && $my->id == 0){ ?>
		<br/>
		<label for="security"><?php echo BOSS_SECURITY; ?></label>
		<?php $this->displayCaptchaImage(); ?>
		<br/>
		<label for="copy"><?php echo BOSS_FORM_SECURITY_CODE_VERIFY; ?></label>
		<?php $this->displayCaptchaInput(); ?>
		<br/>
		<?php } ?>
	<!-- buttons -->
	<label for="content_dummy"></label>
	<span class="button"><input type="submit" value="<?php echo BOSS_FORM_SUBMIT_TEXT; ?>"/></span>
	<span class="button"><input type="button" value="<?php echo BOSS_FORM_CANCEL_TEXT; ?>" onclick="javascript:history.go(-2)"/></span>
	<?php $this->displayFormEnd(); ?>
	<?php } ?>
</fieldset>
