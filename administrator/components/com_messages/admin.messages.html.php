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

/**
 * @package Joostina
 * @subpackage Messages
 */
class HTML_messages{
	public static function showMessages($rows, $pageNav, $search, $option){
		?>
	<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th class="inbox"><?php echo _PRIVATE_MESSAGES?></th>
				<td><?php echo _SEARCH?>:</td>
				<td>
					<input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="inputbox" onChange="document.adminForm.submit();"/>
				</td>
			</tr>
		</table>

		<table class="adminlist">
			<tr>
				<th width="20">#</th>
				<th width="5%" class="title">
					<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);"/>
				</th>
				<th width="50%" class="title"><?php echo _SUBJECT?></th>
				<th width="20%" class="title"><?php echo _MAIL_FROM?></th>
				<th width="15%" class="title"><?php echo _DATE?></th>
				<th width="10%" class="title"><?php echo _COM_MESSAGES_STATUS?></th>
			</tr>
			<?php
			$k = 0;
			for($i = 0, $n = count($rows); $i < $n; $i++){
				$row = &$rows[$i];
				?>
				<tr class="row<?php echo $k ?>">
					<td width="20"><?php echo $i + 1 + $pageNav->limitstart; ?></td>
					<td width="5%"><?php echo mosHTML::idBox($i, $row->message_id); ?></td>
					<td width="50%">
						<a href="#edit" onClick="hideMainMenu();return listItemTask('cb<?php echo $i; ?>','view')"><?php echo $row->subject; ?></a>
					</td>
					<td width="20%"><?php echo $row->user_from; ?></td>
					<td width="15%"><?php echo $row->date_time; ?></td>
					<td width="10%">
						<?php
						if(intval($row->state) == "1"){
							echo _MAIL_READED;
						} else{
							echo _MAIL_NOT_READED;
						}
						?>
					</td>
				</tr>
				<?php $k = 1 - $k;
			}
			?>
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

	public static function editConfig($vars, $option){
		$tabs = new mosTabs(0);
		?>
	<form action="index2.php" method="post" name="adminForm">
		<script language="javascript" type="text/javascript">
			function submitbutton(pressbutton) {
				var form = document.adminForm;
				if (pressbutton == 'saveconfig') {
					if (confirm("<?php echo _ARE_YOU_SURE; ?>")) {
						submitform(pressbutton);
					}
				} else {
					document.location.href = 'index2.php?option=<?php echo $option; ?>';
				}
			}
		</script>

		<table class="adminheading">
			<tr>
				<th class="inbox"><?php echo _PRIVATE_MESSAGES_SETTINGS?></th>
			</tr>
		</table>

		<table class="adminform">
			<tr>
				<td width="25%"><?php echo _BLOCK_INCOMING_MAIL?>:</td>
				<td><?php echo $vars['lock']; ?></td>
			</tr>
			<tr>
				<td><?php echo _SEND_NEW_MESSAGES?>:</td>
				<td><?php echo $vars['mail_on_new']; ?></td>
			</tr>
			<tr>
				<td><?php echo _AUTO_PURGE_MESSAGES?>:</td>
				<td><?php echo _AUTO_PURGE_MESSAGES2?> <input type="text" name="vars[auto_purge]" size="5" value="<?php echo $vars['auto_purge']; ?>" class="inputbox"/> <?php echo _DAYS?></td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1"/>

	</form>
	<?php
	}

	public static function viewMessage($row, $option){
		?>
	<form action="index2.php" method="post" name="adminForm">

		<table class="adminheading">
			<tr>
				<th class="inbox"><?php echo _VIEW_PRIVATE_MESSAGES?></th>
			</tr>
		</table>

		<table class="adminform">
			<tr>
				<td width="100"><?php echo _MAIL_FROM?>:</td>
				<td width="85%" bgcolor="#ffffff"><?php echo $row->user_from; ?></td>
			</tr>
			<tr>
				<td><?php echo _MESSAGE_SEND_DATE?>:</td>
				<td bgcolor="#ffffff"><?php echo $row->date_time; ?></td>
			</tr>
			<tr>
				<td><?php echo _SUBJECT?>:</td>
				<td bgcolor="#ffffff"><?php echo htmlspecialchars($row->subject, ENT_QUOTES); ?></td>
			</tr>
			<tr>
				<td valign="top"><?php echo _MESSAGE?>:</td>
				<td width="100%" bgcolor="#ffffff"><?php echo htmlspecialchars($row->message, ENT_QUOTES); ?></td>
			</tr>
		</table>

		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="1"/>
		<input type="hidden" name="cid[]" value="<?php echo $row->message_id; ?>"/>
		<input type="hidden" name="userid" value="<?php echo $row->user_id_from; ?>"/>
		<input type="hidden" name="subject" value="<?php echo (substr($row->subject, 0, 4) != 'Re: ' ? 'Re: ' : '') . htmlspecialchars($row->subject, ENT_QUOTES); ?>"/>
		<input type="hidden" name="hidemainmenu" value="0"/>
		<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1"/>

	</form>
	<?php
	}

	public static function newMessage($option, $recipientslist, $subject){
		$mainframe = mosMainFrame::getInstance();
		$my = $mainframe->getUser();
		?>
	<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform(pressbutton);
				return;
			}

			// do field validation
			if (form.subject.value == "") {
				alert("<?php echo _PLEASE_ENTER_SUBJECT?>");
			} else if (form.message.value == "") {
				alert("<?php echo _PLEASE_ENTER_MESSAGE_BODY?>");
			} else if (getSelectedValue('adminForm', 'user_id_to') < 1) {
				alert("<?php echo _PLEASE_ENTER_USER?>");
			} else {
				submitform(pressbutton);
			}
		}
	</script>

	<table class="adminheading">
		<tr>
			<th class="inbox"><?php echo _NEW_PERSONAL_MESSAGE?></th>
		</tr>
	</table>

	<form action="index2.php" method="post" name="adminForm">
		<table class="adminform">
			<tr>
				<td width="100"><?php echo _MAIL_TO?>:</td>
				<td width="85%"><?php echo $recipientslist; ?></td>
			</tr>
			<tr>
				<td><?php echo _SUBJECT?>:</td>
				<td>
					<input type="text" name="subject" size="50" maxlength="100" class="inputbox" value="<?php echo htmlspecialchars($subject, ENT_QUOTES); ?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo _MESSAGE?>:</td>
				<td width="100%">
					<textarea name="message" style="width:100%" rows="30" class="inputbox"></textarea>
				</td>
			</tr>
		</table>

		<input type="hidden" name="user_id_from" value="<?php echo $my->id; ?>">
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1"/>

	</form>
	<?php
	}
}