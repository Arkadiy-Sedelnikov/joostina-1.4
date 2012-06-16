<?php
/**
 * JoiEditor - Joostina WYSIWYG Editor
 * Backend content viewer. Config-page.
 * @version 1.0 beta 3
 * @package JoiEditor
 * @subpackage    Admin
 * @filename config.php
 * @author JoostinaTeam
 * @copyright (C) 2008-2010 Joostina Team
 * @license see license.txt
 **/

defined('_VALID_MOS') or die();
$mainframe = &mosMainFrame::getInstance();
include_once(JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_elrte' . DS . 'config_elfinder.php');

//обрубаем загрузку стандартных jQuery и ui если вызвано раньше чем загружено
if(!defined('_FAX_LOADED'))
	define('_FAX_LOADED', 1);

//jQuery and jQuery UI
mosCommonHTML::loadJquery();
mosCommonHTML::loadJqueryUI();
$mainframe->addCSS(JPATH_SITE . "/mambots/editors/elrte/css/smoothness/jquery-ui-1.8.13.custom.css");

//elFinder
$mainframe->addJS(JPATH_SITE . "/mambots/editors/elrte/src/elfinder/js/elfinder.min.js");
$mainframe->addJS(JPATH_SITE . "/mambots/editors/elrte/src/elfinder/js/i18n/elfinder.ru.js");
$mainframe->addCSS(JPATH_SITE . "/mambots/editors/elrte/src/elfinder/css/elfinder.css");

//настройки
$places = (!empty($places)) ? $places : '';
$places_first = (@$places_first == 1) ? 1 : 0;
$view = (!empty($view)) ? $view : 'list';
$remember_last_dir = (@$remember_last_dir == 1) ? 1 : 0;

?>
<script type="text/javascript" charset="utf-8">
	var opts = {
		places:'<?php echo $places ?>',
		placesFirst:'<?php echo $places_first ?>',
		view:'<?php echo $view ?>',
		rememberLastDir:'<?php echo $remember_last_dir ?>'
	}
	elFinder.prototype.options = $.extend({}, elFinder.prototype.options, opts);

	jQuery().ready(function () {
		jQuery('#my-div').elfinder({
			url:'<?php echo JPATH_SITE ?>/administrator/index2.php?option=com_elrte&task=connector',
			lang:'ru'
		})
	});
</script>

<div id="my-div"></div>