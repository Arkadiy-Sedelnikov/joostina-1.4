<table>
    <tr>
        <td>
            <?php if ($this->isReviewCaptchaActivated($conf) && $my->id == 0) {
	        $this->displayCaptchaImage(); ?>
            <br/>
            <?php echo BOSS_FORM_SECURITY_CODE_VERIFY; ?>
            <br/>
            <?php $this->displayCaptchaInput(); ?>
            <br/>
            <?php } ?>

            <?php echo BOSS_FNAME; ?>
            <br/><input id='title' type='text' name='title' maxlength='50' value='<?php echo $name; ?>' />
        </td>
        <td>
            <textarea id='description' name='description' cols='40' rows='10' wrap='VIRTUAL'></textarea><br/>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align:center;">
            <span class="button">
                <input type="button" value=<?php echo BOSS_SUBMIT; ?> onclick="submit()" />
            </span>
        </td>
    </tr>
</table>


