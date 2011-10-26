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
 * Utility class for the button bar
 * @package Joostina
 */
class mosToolBar {

	/**
	 * Writes the start of the button bar table
	 */
	function startTable() {
		?>
<style type="text/css">
	table#toolbar {
		margin-right: 10px;
	}

	table#toolbar a.toolbar {
		color : #808080;
		text-decoration : none;
		display: block;
		border: 1px solid #DDD;
		width: 40px;
		padding: 2px 5px 2px 5px;
	}
	table#toolbar a.toolbar:hover {
		color : #C64934;
		cursor: pointer;
		border: 1px solid #c24733;
		background-color: #f1e8e6;
		padding: 3px 5px 1px 5px;
	}
	table#toolbar a.toolbar:active {
		color : #FF9900;
	}
</style>
<table cellpadding="0" cellspacing="3" border="0" id="toolbar">
	<tr height="60" valign="middle" align="center">
				<?php
			}

			/**
			 * Writes a custom option and task button for the button bar
			 * @param string The task to perform (picked up by the switch($task) blocks
			 * @param string The image to display
			 * @param string The image to display when moused over
			 * @param string The alt text for the icon image
			 * @param boolean True if required to check that a standard list item is checked
			 */
			function custom($task = '',$icon = null,$iconOver = '',$alt = '',$listSelect = true) {
				if($listSelect) {
					$href = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для $alt');}else{submitbutton('$task')}";
				} else {
					$href = "javascript:submitbutton('$task')";
				}
				?>
		<td>
			<a class="toolbar" href="<?php echo $href; ?>" ><img name="<?php echo $task; ?>" src="images/system/<?php echo $iconOver; ?>" alt="<?php echo $alt; ?>" title="<?php echo $alt; ?>" border="0" /></a>
		</td>
				<?php
			}

			/**
			 * Writes the common 'new' icon for the button bar
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function addNew($task = 'new',$alt = _NEW) {
				$image = mosAdminMenus::ImageCheck('new_f2.png','/images/system/',null,null,$alt,$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:submitbutton('<?php echo $task; ?>');" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a common 'publish' button
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function publish($task = 'publish',$alt = _PUBLISHED) {
				$image = mosAdminMenus::ImageCheck('publish_f2.png','/images/system/',null,null,$alt,$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:submitbutton('<?php echo $task; ?>');" >
						<?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a common 'publish' button for a list of records
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function publishList($task = 'publish',$alt = _PUBLISHED) {
				$image = mosAdminMenus::ImageCheck('publish_f2.png','/images/system/',null,null,$alt,$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его публикации'); } else {submitbutton('<?php echo $task; ?>', '');}" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a common 'unpublish' button
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function unpublish($task = 'unpublish',$alt = _UNPUBLISHED) {
				$image = mosAdminMenus::ImageCheck('unpublish_f2.png','/images/system/',null,null,$alt,
						$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:submitbutton('<?php echo $task; ?>');" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a common 'unpublish' button for a list of records
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function unpublishList($task = 'unpublish',$alt = _UNPUBLISHED) {
				$image = mosAdminMenus::ImageCheck('unpublish_f2.png','/images/system/',null,null,$alt,
						$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для отмены его публикации'); } else {submitbutton('<?php echo $task; ?>', '');}" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a common 'archive' button for a list of records
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function archiveList($task = 'archive',$alt = _CMN_ARCHIVE) {
				$image = mosAdminMenus::ImageCheck('archive_f2.png','/images/system/',null,null,$alt,$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для перемещения в архив'); } else {submitbutton('<?php echo $task; ?>', '');}" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes an unarchive button for a list of records
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function unarchiveList($task = 'unarchive',$alt = _CMN_UNARCHIVE) {
				$image = mosAdminMenus::ImageCheck('unarchive_f2.png','/images/system/',null,null,$alt,$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите материал для восстановления его из архива'); } else {submitbutton('<?php echo $task; ?>', '');}" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a common 'edit' button for a list of records
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function editList($task = 'edit',$alt = _EDIT) {
				$image = mosAdminMenus::ImageCheck('edit_f2.png','/images/system/',null,null,$alt,$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его редактирования'); } else {submitbutton('<?php echo $task; ?>', '');}" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a common 'edit' button for a template html
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function editHtml($task = 'edit_source',$alt = _CMN_EDIT_HTML) {
				$image = mosAdminMenus::ImageCheck('edit_f2.png','/images/system/',null,null,$alt,$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его редактирования'); } else {submitbutton('<?php echo $task; ?>', '');}" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a common 'edit' button for a template css
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function editCss($task = 'edit_css',$alt = _CMN_EDIT_CSS) {
				$image = mosAdminMenus::ImageCheck('css_f2.png','/images/system/',null,null,$alt,$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его редактирования'); } else {submitbutton('<?php echo $task; ?>', '');}" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a common 'delete' button for a list of records
			 * @param string  Postscript for the 'are you sure' message
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function deleteList($msg = '',$task = 'remove',$alt = _DELETE) {
				$image = mosAdminMenus::ImageCheck('delete_f2.png','/images/system/',null,null,$alt,$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его удаления'); } else if (confirm('Вы действительно хотите удалить выбранные объекты? <?php echo $msg; ?>')){ submitbutton('<?php echo $task; ?>');}"><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a preview button for a given option (opens a popup window)
			 * @param string The name of the popup file (excluding the file extension)
			 */
			function preview($popup = '') {
				global $database;
				$sql = "SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0";
				$database->setQuery($sql);
				$cur_template = $database->loadResult();

				$image = mosAdminMenus::ImageCheck('preview_f2.png','images/system/',null,null,'Просмотр','preview',1);
				?>
		<td>
			<a class="toolbar" href="#" onclick="window.open('popups/<?php echo $popup; ?>.php?t=<?php echo $cur_template; ?>', 'win1', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a save button for a given option
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function save($task = 'save',$alt = _SAVE) {
				$image = mosAdminMenus::ImageCheck('save_f2.png','/images/system/',null,null,$alt,$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:submitbutton('<?php echo $task; ?>');" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a save button for a given option
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function apply($task = 'apply',$alt = _APPLY) {
				$image = mosAdminMenus::ImageCheck('apply_f2.png','/images/system/',null,null,$alt,$task,1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:submitbutton('<?php echo $task; ?>');" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a save button for a given option (NOTE this is being deprecated)
			 */
			function savenew() {
				$image = mosAdminMenus::ImageCheck('save_f2.png','/images/system/',null,null,'save','save',1);
				?>
		<td>
			<a class="toolbar" href="javascript:submitbutton('savenew');" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a save button for a given option (NOTE this is being deprecated)
			 */
			function saveedit() {
				$image = mosAdminMenus::ImageCheck('save_f2.png','/images/system/',null,null,'save','save',1);
				?>
		<td>
			<a class="toolbar" href="javascript:submitbutton('saveedit');" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a cancel button and invokes a cancel operation (eg a checkin)
			 * @param string An override for the task
			 * @param string An override for the alt text
			 */
			function cancel($task = 'cancel',$alt = _CANCEL) {
				$image = mosAdminMenus::ImageCheck('cancel_f2.png','/images/system/',null,null,$alt,$task,	1,'middle',$alt);
				?>
		<td>
			<a class="toolbar" href="javascript:submitbutton('<?php echo $task; ?>');" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a cancel button that will go back to the previous page without doing
			 * any other operation
			 */
			function back() {
				$image = mosAdminMenus::ImageCheck('back_f2.png','/images/system/',null,null,'back','cancel',1);
				?>
		<td>
			<a class="toolbar" href="javascript:window.history.back();" ><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Write a divider between menu buttons
			 */
			function divider() {
				$image = mosAdminMenus::ImageCheck('menu_divider.png','/images/system/');
				?>
		<td>
					<?php echo $image; ?>
		</td>
				<?php
			}

			/**
			 * Writes a media_manager button
			 * @param string The sub-drectory to upload the media to
			 */
			function media_manager($directory = '') {
				$image = mosAdminMenus::ImageCheck('upload_f2.png','/images/system/',null,null,_UPLOAD_FILE,'uploadPic',1);
				?>
		<td>
			<a class="toolbar" href="#" onclick="popupWindow('popups/uploadimage.php?directory=<?php echo $directory; ?>','win1',250,100,'no');"><?php echo $image; ?></a>
		</td>
				<?php
			}

			/**
			 * Writes a spacer cell
			 * @param string The width for the cell
			 */
			function spacer($width = '') {
				if($width != '') {
					?>
		<td width="<?php echo $width; ?>">&nbsp;</td>
					<?php
				} else {
					?>
		<td>&nbsp;</td>
					<?php
				}
			}

			/**
			 * Writes the end of the menu bar table
			 */
			function endTable() {
				?>
	</tr>
</table>
		<?php
	}
}