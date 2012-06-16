<?php if($this->isReviewCaptchaActivated()){
	$this->displayCaptchaImage(); ?>
<br/>
<?php echo BOSS_FORM_SECURITY_CODE_VERIFY; ?>
<br/>
<?php $this->displayCaptchaInput(); ?>
<br/>
<?php } ?>
<br/>
<?php echo BOSS_FIELD_TITLE; ?> <input id='title' type='text' name='title' maxlength='50' value=''/><br/><br/>
<textarea id='description' name='description' cols='40' rows='10' wrap='VIRTUAL'></textarea><br/>
<span class="button">
    <input type="button" value=<?php echo BOSS_SUBMIT; ?> onclick="submit()" />
</span>			