<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

/**
 * @package Joostina
 * @subpackage Contact
 */
class HTML_contact {

	function displaylist(&$categories,&$rows,$catid,$currentcat = null,&$params,$tabclass) {
		global $Itemid,$hide_js;

		?>
<div class="com_contact contacts_main_page <?php echo $params->get('pageclass_sfx'); ?>">

			<?php if($params->get('page_title')) { ?>
	<div class="componentheading"><h1><?php echo $currentcat->header; ?></h1></div>
				<?php } ?>

	<form action="index.php" method="post" name="adminForm">

				<?php if($currentcat->descrip) { ?>
		<div class="description info">
						<?php if($currentcat->img) {?>
			<img src="<?php echo $currentcat->img; ?>" align="<?php echo $currentcat->align; ?>" hspace="6" alt="<?php echo _LINKS; ?>" />
							<?php }?>

						<?php echo $currentcat->descrip; ?>
		</div>
					<?php }?>

				<?php if(count($rows)) { ?>
		<div class="contact_items">
						<?php HTML_contact::showTable($params,$rows,$catid,$tabclass); ?>
		</div>
					<?php } ?>

				<?php if(($params->get('type') == 'category') && $params->get('other_cat')) {?>
		<div class="contact_cats"><?php HTML_contact::showCategories($params,$categories,$catid); ?></div>
					<?php } else if(($params->get('type') == 'section') && $params->get('other_cat_section')) { ?>
		<div class="contact_cats"><?php HTML_contact::showCategories($params,$categories,$catid); ?></div>
					<?php }?>

	</form>

			<?php mosHTML::BackButton($params,$hide_js);?>

</div>
		<?php
	}

	/**
	 * Display Table of items
	 */
	function showTable(&$params,&$rows,$catid,$tabclass) {
		global $Itemid;

		?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">

			<?php if($params->get('headings')) {?>
	<tr>
		<th><?php echo _CONTACT_HEADER_NAME; ?></th>

					<?php if($params->get('position')) {?>
		<th><?php echo _CONTACT_HEADER_POS; ?></th>
						<?php } ?>

					<?php if($params->get('email')) { ?>
		<th> <?php echo _CONTACT_HEADER_EMAIL; ?> </th>
						<?php } ?>

					<?php if($params->get('telephone')) { ?>
		<th> <?php echo _C_USERS_CONTACT_PHONE; ?> </th>
						<?php } ?>

					<?php if($params->get('fax')) { ?>
		<th> <?php echo _C_USERS_CONTACT_FAX; ?> </th>
						<?php } ?>
	</tr>
				<?php } ?>


			<?php

			$k = 0;
			foreach($rows as $row) {
				$link = 'index.php?option=com_contact&amp;task=view&amp;contact_id='.$row->id.'&amp;Itemid='.$Itemid;

				?>
	<tr>

		<td height="20" class="<?php echo $tabclass[$k]; ?>">
			<a href="<?php echo sefRelToAbs($link); ?>" class="category<?php echo $params->get('pageclass_sfx'); ?>">
							<?php echo $row->name; ?>
			</a>
		</td>

					<?php if($params->get('position')) { ?>
		<td width="25%" class="<?php echo $tabclass[$k]; ?>">
							<?php echo $row->con_position; ?>
		</td>
						<?php } ?>

					<?php if($params->get('email')) { ?>
						<?php if($row->email_to) {
							$row->email_to = mosHTML::emailCloaking($row->email_to,1);
						} ?>
		<td width="20%" class="<?php echo $tabclass[$k]; ?>">
							<?php echo $row->email_to; ?>
		</td>
						<?php } ?>

					<?php if($params->get('telephone')) { ?>
		<td width="15%" class="<?php echo $tabclass[$k]; ?>">
							<?php echo $row->telephone; ?>
		</td>
						<?php } ?>

					<?php if($params->get('fax')) { ?>
		<td width="15%" class="<?php echo $tabclass[$k]; ?>">
							<?php echo $row->fax; ?>
		</td>
						<?php } ?>

	</tr>
				<?php $k = 1 - $k;
			} ?>

</table>

		<?php
	}

	/**
	 * Display links to categories
	 */
	function showCategories(&$params,&$categories,$catid) {
		global $Itemid;

		?>
<ul>

			<?php foreach($categories as $cat) { ?>
				<?php if($catid == $cat->catid) { ?>

	<li>
		<strong><?php echo $cat->title; ?></strong>
		<span class="small">(<?php echo $cat->numlinks; ?>)</span>
	</li>
					<?php } else {
					$link = 'index.php?option=com_contact&amp;catid='.$cat->catid.'&amp;Itemid='.$Itemid; ?>

	<li>
		<a href="<?php echo sefRelToAbs($link); ?>" class="category">
							<?php echo $cat->title; ?>
		</a>

						<?php if($params->get('cat_items')) { ?>
		<span class="small">(<?php echo $cat->numlinks; ?>)</span>
							<?php } ?>

						<?php if($params->get('cat_description')) { ?>
		<p><?php echo $cat->description; ?></p>
							<?php } ?>

	</li>
					<?php } ?>
				<?php } ?>

</ul>

		<?php
	}


	public static function viewcontact(&$contact,&$params,$count,&$list,&$menu_params) {
		global $Itemid;
        $mainframe = mosMainFrame::getInstance();
		$template = JTEMPLATE;
		$sitename = $mainframe->getCfg('sitename');
		$hide_js = intval(mosGetParam($_REQUEST,'hide_js',0));

		$print_link = JPATH_SITE.'/index2.php?option=com_contact&amp;task=view&amp;contact_id='.$contact->id.'&amp;Itemid='.$Itemid.'&amp;pop=1';

		?>
<script language="JavaScript" type="text/javascript">
	<!--
	function validate(){
		if ( 	(document.emailForm.text.value == "") ||
			(document.emailForm.email.value.search("@") == -1) ||
			(document.emailForm.email.value.search("[.*]") == -1) )
		{
			alert( "<?php echo addslashes(_CONTACT_FORM_NC); ?>" );
		} else if (
		(document.emailForm.email.value.search(";") != -1) ||
			(document.emailForm.email.value.search(",") != -1) || (document.emailForm.email.value.search(" ") != -1) )
		{
			alert( "<?php echo addslashes(_CONTACT_ONE_EMAIL); ?>" );

		<?php if($mainframe->getCfg('captcha_cont')==1) { ?>
				} else if ( (document.emailForm.captcha.value=="" )) {
					alert( "<?php echo addslashes(_NO_CAPTCHA_CODE); ?>" );
			<?php } ?>


					} else {
						document.emailForm.action = "<?php echo sefRelToAbs("index.php?option=com_contact&Itemid=$Itemid"); ?>"
						document.emailForm.submit();
					}
				}
				//-->
</script>


<script type="text/javascript">
	<!--
	function ViewCrossReference( selSelectObject ){
		var links = new Array();

		<?php
		$n = count($list);
		for($i = 0; $i < $n; $i++) {
			echo "\nlinks[".$list[$i]->value."]='".sefRelToAbs('index.php?option=com_contact&task=view&contact_id='.$list[$i]->value.'&Itemid='.$Itemid)."';";
		}
		?>

				var sel = selSelectObject.options[selSelectObject.selectedIndex].value
				if (sel != "") {
					location.href = links[sel];
				}
			}
			//-->
</script>


		<?php
		// For the pop window opened for print preview
		if($params->get('popup')) {
			$mainframe->setPageTitle($contact->name);
			$mainframe->addCustomHeadTag('<link rel="stylesheet" href="templates/'.$template.'/css/template_css.css" type="text/css" />');
		} ?>

<div class="com_contact contact_page <?php echo $menu_params->get('pageclass_sfx'); ?>">

			<?php if($menu_params->get('page_title')) { ?>
	<div class="componentheading"><h1><?php echo $menu_params->get('header'); ?></h1></div>
				<?php } ?>

			<?php if($params->get('page_title') && !$params->get('popup')) { ?>
	<h2><?php echo $params->get('header'); ?></h2>
				<?php } ?>

			<?php if(($count > 1) && !$params->get('popup') && $params->get('drop_down')) {?>
	<div class="select_box">
		<form action="<?php echo sefRelToAbs('index.php?option=com_contact&amp;Itemid='.$Itemid); ?>" method="post" name="selectForm" target="_top" id="selectForm">
			<strong><?php echo (_CONTACT_SEL); ?></strong>
						<?php echo $contact->select; ?>
		</form>
	</div>
				<?php } ?>

			<?php mosHTML::PrintIcon($contact,$params,$hide_js,$print_link);?>


			<?php if($contact->name && $params->get('name')) { ?>
	<h4 class="contact_name"><?php echo $contact->name; ?></h4>
				<?php } ?>

			<?php if($contact->con_position && $params->get('position')) { ?>
				<?php echo $contact->con_position; ?>
				<?php } ?>

			<?php if($contact->image && $params->get('image')) { ?>
	<div class="thumb">
		<img src="<?php echo JPATH_SITE; ?>/images/stories/<?php echo $contact->image; ?>" align="middle" alt="<?php echo _CONTACT_TITLE; ?>" />
	</div>
				<?php } ?>

	<dl>

				<?php if($params->get('address_check') > 0) { ?>
		<dt><?php echo $params->get('marker_address'); ?></dt>

		<dd>
						<?php if($contact->postcode && $params->get('postcode')) { ?>
			<span><?php echo $contact->postcode; ?></span>
							<?php } ?>

						<?php if($contact->country && $params->get('country')) { ?>
			<span> <?php echo $contact->country; ?></span>
							<?php } ?>

						<?php if($contact->state && $params->get('state')) { ?>
			<span><?php echo $contact->state; ?></span>
							<?php } ?>

						<?php if($contact->suburb && $params->get('suburb')) { ?>
			<span><?php	echo $contact->suburb;?> </span>
							<?php } ?>

						<?php if($contact->address && $params->get('street_address')) { ?>
			<span><?php echo $contact->address; ?></span>
							<?php } ?>
		</dd>
					<?php } ?>

				<?php if($contact->email_to && $params->get('email')) { ?>
		<dt><?php echo $params->get('marker_email'); ?> </dt>
		<dd><?php echo $contact->email; ?></dd>
					<?php } ?>

				<?php if($contact->telephone && $params->get('telephone')) { ?>
		<dt> <?php echo $params->get('marker_telephone'); ?> </dt>
		<dd> <?php echo $contact->telephone; ?> </dd>
					<?php } ?>

				<?php if($contact->fax && $params->get('fax')) { ?>
		<dt><?php echo $params->get('marker_fax'); ?> </dt>
		<dd><?php echo $contact->fax; ?></dd>
					<?php } ?>

				<?php if($contact->misc && $params->get('misc')) { ?>
		<dt><?php echo $params->get('marker_misc');?></dt>
		<dd><?php echo $contact->misc;?></dd>
					<?php } ?>

	</dl>

			<?php if($params->get('vcard')) { ?>
	<span class="vcard">
					<?php echo (_CONTACT_DOWNLOAD_AS); ?>
		<a href="index2.php?option=com_contact&amp;task=vcard&amp;contact_id=<?php echo $contact->id; ?>&amp;no_html=1">
						<?php echo (_VCARD); ?>
		</a>
	</span>
				<?php } ?>

			<?php if($contact->email_to && !$params->get('popup') && $params->get('email_form')) { ?>
	<div class="email_form">
					<?php HTML_contact::_writeEmailForm($contact,$params,$sitename,$menu_params); ?>
	</div>
				<?php }?>

			<?php mosHTML::CloseButton($params,$hide_js);?>

			<?php mosHTML::BackButton($params,$hide_js); ?>

</div>
		<?php
	}


	/**
	 * Writes Email form
	 */
	public static function _writeEmailForm(&$contact,&$params,$sitename,&$menu_params) {
		global $Itemid,$mosConfig_captcha_cont;

		// used for spoof hardening
		$validate = josSpoofValue();

		?>

		<?php if($params->get('email_description', '')) : ?>
<div class="info description">
				<?php echo $params->get('email_description') ?>
</div>
		<?php endif; ?>

<form action="<?php echo sefRelToAbs('index.php?option=com_contact&amp;Itemid='.$Itemid); ?>" method="post" name="emailForm" target="_top" id="emailForm">

	<label id="lbl_contact_name" for="contact_name"><?php echo (_EMAIL_YOUR_NAME); ?></label>
	<input type="text" name="name" id="contact_name" size="30" class="inputbox" value="" />

	<label id="lbl_contact_email" for="contact_email"><?php echo (_EMAIL_YOUR_MAIL); ?></label>
	<input type="text" name="email" id="contact_email" size="30" class="inputbox" value="" />

	<label id="lbl_contact_subject" for="contact_subject"><?php echo (_SUBJECT); ?>:</label>
	<input type="text" name="subject" id="contact_subject" size="30" class="inputbox" value="" />

	<label id="lbl_contact_text" for="contact_text"><?php echo (_MESSAGE_PROMPT); ?></label>
	<textarea cols="50" rows="10" name="text" id="contact_text" class="inputbox"></textarea>
<?php if($params->get('email_copy')) { ?>
	<br />
	<label id="lbl_contact_email_copy" for="contact_email_copy"><?php echo (_EMAIL_A_COPY); ?></label>
	<input type="checkbox" name="email_copy" id="contact_email_copy" value="1" />
	<br /><br /><br />
<?php } ?>

<?php if($mosConfig_captcha_cont) { ?>
	<div class="captcha">
		<img id="captchaimg" alt="<?php echo _PRESS_HERE_TO_RELOAD_CAPTCHA?>" onclick="document.emailForm.captchaimg.src='<?php echo JPATH_SITE; ?>/includes/libraries/kcaptcha/index.php?session=<?php echo mosMainFrame::sessionCookieName() ?>&' + new String(Math.random())" src="<?php echo JPATH_SITE; ?>/includes/libraries/kcaptcha/index.php?session=<?php echo mosMainFrame::sessionCookieName() ?>" />
		<label for="captcha" id="lbl_captcha"><?php echo _PLEASE_ENTER_CAPTCHA; ?></label>
		<input name="captcha" type="text" class="inputbox" size="30" />
	</div>
<?php } ?>

	<br />
	<span class="button"><input type="button" name="send" value="<?php echo (_SEND_BUTTON); ?>" class="button" onclick="validate()" /></span>

	<input type="hidden" name="option" value="com_contact" />
	<input type="hidden" name="con_id" value="<?php echo $contact->id; ?>" />
	<input type="hidden" name="sitename" value="<?php echo $sitename; ?>" />
	<input type="hidden" name="op" value="sendmail" />
	<input type="hidden" name="<?php echo $validate; ?>" value="1" />
</form>

		<?php
	}

	public static function nocontact(&$params) {
		?>
<div class="com_contact contact_page no_contact">
	<div class="info"><?php echo _CONTACT_NONE; ?></div>
			<?php mosHTML::BackButton($params); ?>
</div>
		<?php }
}