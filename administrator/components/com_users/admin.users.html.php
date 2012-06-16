<?php /**
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
 * @subpackage Users
 */
class HTML_users{

	public static function showUsers(&$rows, $pageNav, $search, $option, $lists){
		$mainframe = mosMainFrame::getInstance();
		$my = $mainframe->getUser();
		$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';
		?>
	<form action="index2.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
			<tr>
				<th class="user"><?php echo _USERS ?></th>
				<td><?php echo _FILTER ?></td>
				<td>
					<input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="inputbox" onChange="document.adminForm.submit();"/>
				</td>
				<td><?php echo $lists['type']; ?></td>
				<td><?php echo $lists['logged']; ?></td>
			</tr>
		</table>
		<table class="adminlist">
			<tr>
				<th width="1%" class="title">#</th>
				<th width="1%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);"/></th>
				<th class="title" colspan="2"><?php echo _NAME ?></th>
				<th width="22%"><?php echo _USER_LOGIN_TXT ?></th>
				<th width="5%"><?php echo _LOGGED_IN ?></th>
				<th width="5%"><?php echo _ALLOWED ?></th>
				<th width="10%"><?php echo _GROUP ?></th>
				<th width="10%">E-Mail</th>
				<th width="13%"><?php echo _LAST_LOGIN ?></th>
				<th width="1%">ID</th>
			</tr>
			<?php
			$k = 0;
			$num = count($rows);
			for($i = 0, $n = $num; $i < $n; $i++){
				$row = &$rows[$i];
				$img = $row->block ? 'publish_x.png' : 'tick.png';
				$task = $row->block ? 'unblock' : 'block';
				$alt = $row->block ? _ALLOW : _DISALLOW;
				$link = 'index2.php?option=com_users&amp;task=editA&amp;id=' . $row->id . '&amp;hidemainmenu=1'; ?>
				<tr class="row<?php echo $k; ?>">
					<td><?php echo $i + 1 + $pageNav->limitstart; ?></td>
					<td><?php echo mosHTML::idBox($i, $row->id); ?></td>
					<td width="1%"><img width="25" class="miniavatar" src="<?php echo JPATH_SITE . '/' . mosUser::get_avatar($row); ?>"/></td>
					<td align="left"><a href="<?php echo $link; ?>">
						<?php echo $row->name; ?></a></td>
					<td align="left"><?php echo $row->username; ?></td>
					<td align="center"><?php echo $row->loggedin ? '<img src="' . $cur_file_icons_path . '/tick.png" border="0" alt="" />' : ''; ?></td>
					<td width="5%" align="center" <?php if($row->id != $my->id){ ?> class="td-state" onclick="ch_publ(<?php echo $row->id; ?>,'com_users');" <?php
					}
						; ?>>
						<img id="img-pub-<?php echo $row->id; ?>" class="img-mini-state" alt="<?php echo _USER_BLOCK ?>" src="<?php echo $cur_file_icons_path;?>/<?php echo $img; ?>"/>
					</td>
					<td><?php echo $row->groupname; ?></td>
					<td><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a></td>
					<td class="jtd_nowrap"><?php echo mosFormatDate($row->lastvisitDate, _CURRENT_SERVER_TIME_FORMAT); ?></td>
					<td><?php echo $row->id; ?></td>
				</tr>
				<?php $k = 1 - $k;
			} ?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="hidemainmenu" value="0"/>
		<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1"/>
	</form>
	<?php
	}

	/* редактирование пользователя */
	public static function edituser(&$row, &$contact, &$lists, $option, $uid, &$params){
		$mainframe = mosMainFrame::getInstance();
		$my = $mainframe->getUser();

		$acl = &gacl::getInstance();

		mosMakeHtmlSafe($row);

		$tabs = new mosTabs(1, 1);

		if(!defined('_JQUERY_LOADED')){
			define('_JQUERY_LOADED', 1);
			$mainframe = mosMainFrame::getInstance(true);
			$mainframe->addJS($mainframe->getCfg('live_site') . '/includes/js/jquery/jquery.js');
		}
		mosCommonHTML::loadOverlib();
		echo mosCommonHTML::loadJqueryPlugins('jquery.form', true, false);

		$canBlockUser = $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'user properties', 'block_user');
		$canEmailEvents = $acl->acl_check('workflow', 'email_events', 'users', $acl->get_group_name($row->gid, 'ARO'));

		$bday_date = mosFormatDate($row->user_extra->birthdate, '%d', '0');
		$bday_month = mosFormatDate($row->user_extra->birthdate, '%m', '0');
		$bday_year = mosFormatDate($row->user_extra->birthdate, '%Y', '0'); ?>
	<script language="javascript" type="text/javascript">
		$(document).ready(function () {
			$("#save").click(function () {
				$("input#task").val('saveUserEdit');
				$("#mosUserForm").submit();
			});
			$("#cancel").click(function () {
				$("input#task").val('cancel');
				$("#mosUserForm").submit();
			});
		});

		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform(pressbutton);
				return;
			}
			var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

			// do field validation
			if (trim(form.name.value) == "") {
				alert("<?php echo _ENTER_NAME_PLEASE ?>");
			} else if (form.username.value == "") {
				alert("<?php echo _ENTER_LOGIN_PLEASE ?>");
			} else if (r.exec(form.username.value) || form.username.value.length < 3) {
				alert("<?php echo _BAD_USER_LOGIN ?>");
			} else if (trim(form.email.value) == "") {
				alert("<?php echo _ENTER_EMAIL_PLEASE ?>");
			} else if (form.gid.value == "") {
				alert("<?php echo _ENTER_GROUP_PLEASE ?>");
			} else if (trim(form.password.value) != "" && form.password.value != form.password2.value) {
				alert("<?php echo _BAD_PASSWORDWORD ?>");
			} else if (form.gid.value == "29") {
				alert("<?php echo _BAD_GROUP_1 ?>");
			} else if (form.gid.value == "30") {
				alert("<?php echo _BAD_GROUP_2 ?>");
			} else {
				submitform(pressbutton);
			}
		}
		function gotocontact(id) {
			var form = document.adminForm;
			form.contact_id.value = id;
			submitform('contact');
		}
	</script>
	<table class="adminheading">
		<tr>
			<th class="user">
				<small><?php echo $row->id ? _C_USERS_USER_EDIT . ': ' . $row->name : _C_USERS_USER_NEW; ?></small>
			</th>
		</tr>
	</table>
	<br clear="all">
	<?php $tabs->startPane("userInfo"); ?>
	<form action="index2.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<?php $tabs->startTab(_USER_INFO, "general"); ?>
		<table width="100%">
			<tr>
				<td width="400" class="key"><?php echo _YOUR_NAME ?>:</td>
				<td><input type="text" name="name" class="inputbox" size="40" value="<?php echo $row->name; ?>" maxlength="50"/></td>
			</tr>
			<tr>
				<td class="key"><?php echo _USER_LOGIN_TXT ?>:</td>
				<td><input type="text" name="username" class="inputbox" size="40" value="<?php echo $row->username; ?>" maxlength="25"/></td>
			<tr>
				<td class="key">E-mail:</td>
				<td><input class="inputbox" type="text" name="email" size="40" value="<?php echo $row->email; ?>"/></td>
			</tr>
			<tr>
				<td class="key"><?php echo _NEW_PASSWORDWORD ?>:</td>
				<td><input class="inputbox" type="password" name="password" size="40" value=""/></td>
			</tr>
			<tr>
				<td class="key"><?php echo _REPEAT_PASSWORDWORD ?>:</td>
				<td><input class="inputbox" type="password" name="password2" size="40" value=""/></td>
			</tr>
			<tr>
				<td valign="top" class="key"><?php echo _GROUP ?>:</td>
				<td><?php echo $lists['gid']; ?></td>
			</tr>
			<?php if($canBlockUser){ ?>
			<tr>
				<td class="key"><?php echo _BLOCK_USER ?>:</td>
				<td><?php echo $lists['block']; ?></td>
			</tr>
			<?php
		}
			if($canEmailEvents){
				?>
				<tr>
					<td class="key"><?php echo _RECEIVE_EMAILS ?>:</td>
					<td colspan="2">
						<?php echo $lists['sendEmail']; ?>
					</td>
				</tr>
				<?php
			}
			if($uid){
				?>
				<tr>
					<td class="key"><?php echo _REGISTRATION_DATE ?>:</td>
					<td colspan="2"><?php echo $row->registerDate; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo _LAST_LOGIN ?>:</td>
					<td colspan="2"><?php echo $row->lastvisitDate; ?></td>
				</tr>
				<?php } ?>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
		</table>
		<?php $tabs->endTab(); ?>

		<?php $tabs->startTab(_PARAMETERS, "params"); ?>
		<table>
			<tr>
				<td><?php echo $params->render('params'); ?></td>
			</tr>
		</table>
		<?php $tabs->endTab(); ?>

		<?php $tabs->startTab(_ADDITIONAL_INFO, "user_info_extra"); ?>
		<table width="100%">
			<tr>
				<td width="400" class="key"><label for="gender"><?php echo _C_USERS_GENDER ?></label></td>
				<td><?php echo mosHTML::genderSelectList('gender', 'class="inputbox"', $row->user_extra->gender); ?> </td>
			</tr>
			<tr>
				<td class="key"><label><?php echo _C_USERS_B_DAY ?></label></td>
				<td>
					<?php echo mosHTML::daySelectList('birthdate_day', 'class="inputbox"', $bday_date); ?>
					<?php echo mosHTML::monthSelectList('birthdate_month', 'class="inputbox"', $bday_month, 1); ?>
					<?php echo mosHTML::yearSelectList('birthdate_year', 'class="inputbox"', $bday_year); ?>
				</td>
			</tr>
			<tr>
				<td class="key"><label><?php echo _C_USERS_DESCRIPTION ?></label></td>
				<td>
					<textarea cols="56" rows="7" class="inputbox" name="about" id="about"><?php echo $row->user_extra->about ?></textarea>
				</td>
			</tr>
			<tr>
				<td class="key"><label><?php echo _USERS_LOCATION ?></label></td>
				<td>
					<input size="100" class="inputbox" type="text" name="location" id="location" value="<?php echo $row->user_extra->location ?>"/>
				</td>
			</tr>
		</table>
		<?php $tabs->endTab(); ?>
		<?php $tabs->startTab(_C_USERS_CONTACT_INFO, "user_info_contacts"); ?>
		<table width="100%">
			<tr>
				<td width="400" class="key"><label><?php echo _C_USERS_CONTACT_SITE ?></label></td>
				<td><input size="100" class="inputbox" type="text" name="url" id="url" value="<?php echo $row->user_extra->url ?>"/></td>
			</tr>
			<tr>
				<td class="key"><label>ICQ</label></td>
				<td><input size="100" class="inputbox" type="text" name="icq" id="icq" value="<?php echo $row->user_extra->icq ?>"/></td>
			</tr>
			<tr>
				<td class="key"><label>Skype</label></td>
				<td><input size="100" class="inputbox" type="text" name="skype" id="skype" value="<?php echo $row->user_extra->skype ?>"/></td>
			</tr>
			<tr>
				<td class="key"><label>Jabber</label></td>
				<td><input size="100" class="inputbox" type="text" name="jabber" id="jabber" value="<?php echo $row->user_extra->jabber ?>"/></td>
			</tr>
			<tr>
				<td class="key"><label>MSN</label></td>
				<td><input size="100" class="inputbox" type="text" name="msn" id="msn" value="<?php echo $row->user_extra->msn ?>"/></td>
			</tr>
			<tr>
				<td class="key"><label>Yahoo</label></td>
				<td><input size="100" class="inputbox" type="text" name="yahoo" id="yahoo" value="<?php echo $row->user_extra->yahoo ?>"/></td>
			</tr>
			<tr>
				<td class="key"><label><?php echo _C_USERS_CONTACT_PHONE ?></label></td>
				<td><input size="100" class="inputbox" type="text" name="phone" id="phone" value="<?php echo $row->user_extra->phone ?>"/></td>
			</tr>
			<tr>
				<td class="key"><label><?php echo _C_USERS_CONTACT_FAX ?></label></td>
				<td><input size="100" class="inputbox" type="text" name="fax" id="fax" value="<?php echo $row->user_extra->fax ?>"/></td>
			</tr>
			<tr>
				<td class="key"><label><?php echo _C_USERS_CONTACT_PHONE_MOBILE ?></label></td>
				<td><input size="100" class="inputbox" type="text" name="mobil" id="mobil" value="<?php echo $row->user_extra->mobil ?>"/></td>
			</tr>
		</table>
		<?php $tabs->endTab(); ?>
		<?php $tabs->startTab(_CONTACT_INFO_COM_CONTACT, "user_info_com_contact"); ?>
		<?php if(!$contact){ ?>
		<table class="adminform">
			<tr>
				<td>
					<br/>
					<?php echo _NO_USER_CONTACTS ?>
					<br/>
				</td>
			</tr>
		</table>
		<?php } else{ ?>
		<table class="adminform">
			<tr>
				<td width="400" class="key"><?php echo _FULL_NAME ?>:</td>
				<td><strong><?php echo $contact[0]->name; ?></strong></td>
			</tr>
			<tr>
				<td class="key"><?php echo _USER_POSITION ?>:</td>
				<td><strong><?php echo $contact[0]->con_position; ?></strong></td>
			</tr>
			<tr>
				<td class="key"><?php echo _C_USERS_CONTACT_PHONE ?>:</td>
				<td><strong><?php echo $contact[0]->telephone; ?></strong></td>
			</tr>
			<tr>
				<td class="key"><?php echo _C_USERS_CONTACT_FAX ?>:</td>
				<td><strong><?php echo $contact[0]->fax; ?></strong></td>
			</tr>
			<tr>
				<td></td>
				<td><strong><?php echo $contact[0]->misc; ?></strong></td>
			</tr>
			<?php if($contact[0]->image){ ?>
			<tr>
				<td></td>
				<td valign="top">
					<img src="<?php echo JPATH_SITE; ?>/images/stories/<?php echo $contact[0]->image; ?>" align="middle" alt=""/>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="2">
					<br/><br/>
					<input class="button" type="button" value="<?php echo _CHANGE_CONTACT_INFO ?>" onclick="javascript: gotocontact( '<?php echo $contact[0]->id; ?>' )">
					<i><br/><?php echo _CONTACT_INFO_PATH_URL ?>.</i>
				</td>
			</tr>
		</table>
		<?php } ?>
		<?php $tabs->endTab(); ?>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
		<input type="hidden" name="user_id" value="<?php echo $row->id; ?>"/>
		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="task" id="task" value=""/>
		<input type="hidden" name="contact_id" value=""/>
		<?php if(!$canEmailEvents){ ?>
		<input type="hidden" name="sendEmail" value="0"/>
		<?php } ?>
		<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1"/>
	</form>

	<?php $tabs->startTab(_C_USERS_AVATARS, "avatar"); ?>
	<table class="adminform">
		<tr>
			<td>
				<?php
				$form_params = new stdClass();
				$form_params->id = 'avatar_uploadForm';
				$form_params->img_field = 'avatar';
				$form_params->img_path = 'images/avatars';
				$form_params->default_img = 'images/avatars/none.jpg';
				$form_params->img_class = 'user_avatar';
				$form_params->ajax_handler = 'ajax.index.php?option=com_users';

				if(!$row->avatar){
					userHelper::_build_img_upload_area($row, $form_params, 'upload');
				} else{
					userHelper::_build_img_upload_area($row, $form_params, 'reupload');
				} ?>
			</td>
		</tr>
	</table>
	<?php $tabs->endTab(); ?>
	<?php $tabs->endPane(); ?>
	<?php
	}
}