<?php
/**
 * @JoostFREE
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */
// запрет прямого доступа
defined('_VALID_MOS') or die();

if(!class_exists('mosMenuBar')){

	class mosMenuBar{

		/**
		 * Writes the start of the button bar table
		 */
		public static function startTable(){
			?>
			<div id="toolbar">
				<ul>
		<?php
		}

		/**
		 * Создание произвольных кнопок тулбара с параметром в виде ссылки и расширенным параметром extra
		 */
		public static function ext($alt = _BUTTON, $href = '', $class = '', $extra = ''){
			?>
		<li>
			<a class="tb-ext<?php echo $class; ?>" href="<?php echo $href; ?>" <?php echo $extra; ?>><span><?php echo $alt; ?></span></a>
		</li>
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
		public static function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true){
			if($listSelect){
				$href = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('" . _PLEASE_CHOOSE_ELEMENT . "');}else{submitbutton('$task')}";
			} else{
				$href = "javascript:submitbutton('$task')";
			}
			?>
		<li>
			<a class="tb-custom<?php echo $icon; ?>" href="<?php echo $href; ?>"><span><?php echo
			$alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a custom option and task button for the button bar.
		 * Extended version of custom() calling hideMainMenu() before submitbutton().
		 * @param string The task to perform (picked up by the switch($task) blocks
		 * @param string The image to display
		 * @param string The image to display when moused over
		 * @param string The alt text for the icon image
		 * @param boolean True if required to check that a standard list item is checked
		 */
		public static function customX($task = '', $class = '', $iconOver = '', $alt = '', $listSelect = true){
			if($listSelect){
				$href = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('" . _PLEASE_CHOOSE_ELEMENT . "');}else{hideMainMenu();submitbutton('$task')}";
			} else{
				$href = "javascript:hideMainMenu();submitbutton('$task')";
			}
			?>
		<li>
			<a class="tb-custom-x<?php echo $class; ?>" href="<?php echo $href; ?>"><span><?php echo
			$alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes the common 'new' icon for the button bar
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function addNew($task = 'new', $alt = _NEW){
			?>
		<li>
			<a class="tb-add-new" href="javascript:submitbutton('<?php echo $task; ?>');"><span><?php echo
			$alt; ?></span></a></li>
			<?php
		}

		/**
		 * Writes the common 'new' icon for the button bar.
		 * Extended version of addNew() calling hideMainMenu() before submitbutton().
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function addNewX($task = 'new', $alt = _NEW){
			?>
		<li><a class="tb-add-new-x" href="javascript:hideMainMenu();submitbutton('<?php echo $task; ?>');"><span><?php echo $alt; ?></span></a></li><?php
		}

		/**
		 * Writes a common 'publish' button
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function publish($task = 'publish', $alt = _SHOW){
			?>
		<li>
			<a class="tb-publish" href="javascript:submitbutton('<?php echo $task; ?>');"><span><?php echo
			$alt; ?></span></a></li>
			<?php
		}

		/**
		 * Writes a common 'publish' button for a list of records
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function publishList($task = 'publish', $alt = _SHOW){
			?>
		<li>
			<a class="tb-publish-list" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_FOR_PUBLICATION ?>'); } else {submitbutton('<?php echo
			$task; ?>', '');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'default' button for a record
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function makeDefault($task = 'default', $alt = _DEFAULT){
			?>
		<li>
			<a class="tb-makedefault" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_MAKE_DEFAULT ?>'); } else {submitbutton('<?php echo
			$task; ?>', '');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'assign' button for a record
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function assign($task = 'assign', $alt = _ASSIGN){
			?>
		<li>
			<a class="tb-assign" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_ASSIGN ?>'); } else {submitbutton('<?php echo
			$task; ?>', '');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'unpublish' button
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function unpublish($task = 'unpublish', $alt = _HIDE){
			?>
		<li>
			<a class="tb-unpublish" href="javascript:submitbutton('<?php echo $task; ?>');"><span><?php echo
			$alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'unpublish' button for a list of records
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function unpublishList($task = 'unpublish', $alt = _HIDE){
			?>
		<li>
			<a class="tb-unpublish-list" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_UNPUBLISH ?>'); } else {submitbutton('<?php echo
			$task; ?>', '');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'archive' button for a list of records
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function archiveList($task = 'archive', $alt = _TO_ARCHIVE){
			?>
		<li>
			<a class="tb-archive-list" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_ARCHIVE ?>'); } else {submitbutton('<?php echo $task; ?>', '');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes an unarchive button for a list of records
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function unarchiveList($task = 'unarchive', $alt = _FROM_ARCHIVE){
			?>
		<li>
			<a class="tb-unarchive-list" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_UNARCHIVE ?>'); } else {submitbutton('<?php echo $task; ?>', '');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'edit' button for a list of records
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function editList($task = 'edit', $alt = _EDIT){
			?>
		<li>
			<a class="tb-edit-list" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_EDIT ?>'); } else {submitbutton('<?php echo $task; ?>', '');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'edit' button for a list of records.
		 * Extended version of editList() calling hideMainMenu() before submitbutton().
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function editListX($task = 'edit', $alt = _EDIT){
			?>
		<li>
			<a class="tb-edit-list-x" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_EDIT ?>'); } else {hideMainMenu();submitbutton('<?php echo $task; ?>', '');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'edit' button for a template html
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function editHtml($task = 'edit_source', $alt = _EDIT_HTML){
			?>
		<li>
			<a class="tb-edit-html" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_EDIT ?>'); } else {submitbutton('<?php echo $task; ?>', '');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'edit' button for a template html.
		 * Extended version of editHtml() calling hideMainMenu() before submitbutton().
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function editHtmlX($task = 'edit_source', $alt = _EDIT_HTML){
			?>
		<li>
			<a class="tb-edit-html-x" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_EDIT ?>'); } else {hideMainMenu();submitbutton('<?php echo $task; ?>', '');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'edit' button for a template css
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function editCss($task = 'edit_css', $alt = _EDIT_CSS){
			?>
		<li>
			<a class="tb-edit-css" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_EDIT ?>'); } else {submitbutton('<?php echo $task; ?>', '');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'edit' button for a template css.
		 * Extended version of editCss() calling hideMainMenu() before submitbutton().
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function editCssX($task = 'edit_css', $alt = _EDIT_CSS){
			?>
		<li>
			<a class="tb-edit-css-x" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_EDIT ?>'); } else {hideMainMenu();submitbutton('<?php echo $task; ?>', '');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'delete' button for a list of records
		 * @param string Postscript for the 'are you sure' message
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function deleteList($msg = '', $task = 'remove', $alt = _DELETE){
			?>
		<li>
			<a class="tb-delete-list" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_DELETE ?>'); } else if (confirm('<?php echo _REALLY_WANT_TO_DELETE_OBJECTS ?> <?php echo $msg; ?>')){ submitbutton('<?php echo $task; ?>');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a common 'delete' button for a list of records.
		 * Extended version of deleteList() calling hideMainMenu() before submitbutton().
		 * @param string Postscript for the 'are you sure' message
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function deleteListX($msg = '', $task = 'remove', $alt = _DELETE){
			?>
		<li>
			<a class="tb-delete-list-x" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo _PLEASE_CHOOSE_ELEMENT_TO_DELETE ?>'); } else if (confirm('<?php echo _REALLY_WANT_TO_DELETE_OBJECTS ?> <?php echo $msg; ?>')){ hideMainMenu();submitbutton('<?php echo $task; ?>');}"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Write a trash button that will move items to Trash Manager
		 */
		public static function trash($task = 'remove', $alt = _REMOVE_TO_TRASH, $check = true){
			if($check){
				$js = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('" . _PLEASE_CHOOSE_ELEMENT_TO_TRASH . "'); } else { submitbutton('$task');}";
			} else{
				$js = "javascript:submitbutton('$task');";
			}
			?>
		<li>
			<a class="tb-trash" href="<?php echo $js; ?>"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a preview button for a given option (opens a popup window)
		 * @param string The name of the popup file (excluding the file extension)
		 */
		public static function preview(){
			global $task;
			?>
		<li>
			<script language="javascript" type="text/javascript">
				<!--
				function popup() {
					document.adminForm.target = '_blank';
					var action = document.adminForm.action;
					document.adminForm.action = '<?php echo JPATH_SITE; ?>/<?php echo JADMIN_BASE ?>/popups/contentwindow.php';
					submitbutton('<?php echo $task; ?>');
					document.adminForm.target = '_self';
					document.adminForm.action = action;
					return false;
				}
				//-->
			</script>
			<a class="tb-preview" href="#" onclick="popup();"><span><?php echo _PREVIEW ?></span></a></li>
			<?php
		}

		/**
		 * Writes a preview button for a given option (opens a popup window)
		 * @param string The name of the popup file (excluding the file extension for an xml file)
		 * @param boolean Use the help file in the component directory
		 */
		public static function help($ref, $com = false){
			global $mosConfig_disable_button_help;
			if($mosConfig_disable_button_help)
				return; // при активном отключении кнопки "Помощь" функция прерывается в самом начале
			$helpUrl = mosGetParam($GLOBALS, 'mosConfig_helpurl', 'http://wiki.joostina-cms.ru');


			if($com){
				// help file for 3PD Components
				$url = JPATH_SITE . '/' . JADMIN_BASE . '/components/' . $GLOBALS['option'] . '/help/';
				if(!preg_match('/\.html$/', $ref) && !preg_match('/\.xml$/', $ref)){
					$ref = $ref . '.html';
				}
				$url .= $ref;
			} else
				if($helpUrl){
					$url = 'http://wiki.joostinadev.ru/';
				}
			?>
		<li>
			<a class="tb-help" href="#" onclick="window.open('<?php echo $url; ?>', 'mambo_help_win', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');"><span><?php echo _HELP ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a save button for a given option
		 * Apply operation leads to a save action only (does not leave edit mode)
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function apply($task = 'apply', $alt = _APPLY){
			?>
		<li>
			<a class="tb-apply" href="javascript:submitbutton('<?php echo $task; ?>');"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a save button for a given option
		 * Save operation leads to a save and then close action
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function save($task = 'save', $alt = _SAVE){
			?>
		<li>
			<a class="tb-save" href="javascript:submitbutton('<?php echo $task; ?>');"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a save button for a given option (NOTE this is being deprecated)
		 */
		public static function savenew(){
			?>
		<li>
			<a class="tb-save-new" href="javascript:submitbutton('savenew');"><span><?php echo _SAVE ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a save button for a given option (NOTE this is being deprecated)
		 */
		public static function saveedit(){
			?>
		<li>
			<a class="tb-save-edit" href="javascript:submitbutton('saveedit');"><span><?php echo _SAVE ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a cancel button and invokes a cancel operation (eg a checkin)
		 * @param string An override for the task
		 * @param string An override for the alt text
		 */
		public static function cancel($task = 'cancel', $alt = _CANCEL){
			?>
		<li>
			<a class="tb-cancel" href="javascript:submitbutton('<?php echo $task; ?>');"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a cancel button that will go back to the previous page without doing
		 * any other operation
		 */
		public static function back($alt = _MENU_BACK, $href = ''){
			if($href){
				$link = $href;
			} else{
				$link = 'javascript:window.history.back();';
			}
			?>
		<li>
			<a class="tb-back" href="<?php echo $link; ?>"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Write a divider between menu buttons
		 */
		public static function divider(){
			?>
		<li>
			&nbsp;
		</li>
			<?php
		}

		/**
		 * Writes a media_manager button
		 * @param string The sub-drectory to upload the media to
		 */
		public static function media_manager($directory = '', $alt = _TASK_UPLOAD){
			$mainframe = mosMainFrame::getInstance();
			;
			$cur_template = JTEMPLATE;
			?>
		<li>
			<a class="tb-media-manager" href="#" onclick="popupWindow('popups/uploadimage.php?directory=<?php echo $directory; ?>&amp;t=<?php echo $cur_template; ?>','win1',250,100,'no');"><span><?php echo $alt; ?></span></a>
		</li>
			<?php
		}

		/**
		 * Writes a spacer cell
		 * @param string The width for the cell
		 */
		public static function spacer($width = '0'){
			return;
			?>
		<li style="width:<?php echo $width; ?>px;">&nbsp;</li>
			<?php
		}

		/**
		 * Writes the end of the menu bar table
		 */
		public static function endTable(){
			?>
				</ul>
			</div>
<?php
		}

	}

}
?>
