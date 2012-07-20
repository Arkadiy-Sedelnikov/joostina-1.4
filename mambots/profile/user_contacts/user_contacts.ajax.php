<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

require_once (JPATH_BASE . DS . 'mambots' . DS . 'profile' . DS . 'user_contacts' . DS . 'user_contacts.class.php');

$act = mosGetParam($_REQUEST, 'act', '');

switch($act){
	case 'display_form':
		display_form();
		break;

	case 'user_sendmail':
		user_sendmail();
		break;

	default:
		echo 'error-act';
		return;
}

/**
 * Форма отправки сообщения пользователю
 */
function display_form(){
	$database = database::getInstance();
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();

	$ajax_handler = JPATH_SITE . '/ajax.index.php?option=com_users&task=request_from_plugin&plugin=user_contacts';
	$user_id = intval(mosGetParam($_REQUEST, 'user_id', 0));

	//Подключение плагина валидации форм
	echo mosCommonHTML::loadJqueryPlugins('jquery.validate', 1);
	//Подключение плагина ajax-форм
	echo mosCommonHTML::loadJqueryPlugins('jquery.form', 1);

	//Параметры формы для отправки сообщения пользователю
	$form_params = new UserContactsEmail();
	?><!--Валидация формы отправки сообщения пользователю ajax-отправка данных-->
<script type="text/javascript">
	$(document).ready(function () {

		var u_options = {
			beforeSubmit:u_validate_this,
			url:'<?php echo $ajax_handler;?>',
			//url:		 'components/com_users/plugins/user_contacts/user_sendmail.php',
			clearForm:false,
			success:showResponse
		};

		$('#UserContactsForm').submit(function () {
			$(this).ajaxSubmit(u_options);
			return false;
		});

		function showResponse(responseText, statusText) {
			$('#resp').html(responseText);
		}

		function u_validate_this() {
			$("#UserContactsForm").validate({
				errorElement:"span",
				messages:{
					from_uname:{
						required:""
					},
					from_uemail:{
						required:"",
						email:""
					},
					user_message:{
						required:""
					}
				}
			});
			if ($("#UserContactsForm").valid() == false) {
				/*alert('Проверьте правильность заполнения полей!');*/
				return false;
			}
		}

		;

	});
</script>
<div id="UserForm">
	<div id="pretext"><?php echo $form_params->pretext; ?></div>
	<form id="UserContactsForm" action="" class="validate" method="post" name="UserContactsForm">
		<div class="user_contact_form">
			<?php echo BOT_USER_CONTACTS_INTRODUCE?>:<br/>
			<input type="text" name="from_uname" value="<?php echo $my->name;?>" class="inputbox required"/>
			<br/>
			<?php echo BOT_USER_CONTACTS_YOUR_EMAIL?>:<br/>
			<input type="text" name="from_uemail" value="<?php echo $my->email;?>" class="inputbox required email"/>
			<br/>
			<?php echo BOT_USER_CONTACTS_MESSAGE?>:<br/>
			<textarea class="inputbox required" name="user_message" rows="5" cols="50"></textarea>
		</div>
		<div class="button"><input type="submit" class="button" name="button" value="<?php echo BOT_USER_CONTACTS_SEND?>"/></div>
		<input type="hidden" name="act" value="user_sendmail"/>
		<input type="hidden" name="user_id" value="<?php echo $user_id;?>"/>
	</form>

	<div id="posttext"><?php echo $form_params->posttext;?></div>
	<div id="resp"></div>
</div>
<?php
}

function user_sendmail(){
	$database = database::getInstance();

	$user_id = mosGetParam($_REQUEST, 'user_id', 0);
	$user = new mosUser($database);
	$user->load((int)$user_id);

	$form_params = new UserContactsEmail();
	$form_params->recipient = $user->email;
	$form_params->from = $_POST['from_uemail'];
	$form_params->fromname = $_POST['from_uname'];
	$form_params->message = $form_params->clean_message($_POST['user_message']);

	if($form_params->send_message()){
		echo '<div class="info">' . BOT_USER_CONTACTS_MESSAGE_SEND . '</div>';
	} else{
		echo '<div class="error">' . BOT_USER_CONTACTS_MESSAGE_NOT_SEND . ' ' . $form_params->_error . '</div>';
	}
}