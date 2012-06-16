<?php
defined('_VALID_MOS') or die();

$_MAMBOTS->registerFunction('onInitEditor', 'botElrteInit');
$_MAMBOTS->registerFunction('onGetEditorContents', 'botElrteGetContents');
$_MAMBOTS->registerFunction('onEditorArea', 'botElrteArea');

/**
 * Spaw WYSIWYG Editor - javascript initialisation
 */
function botElrteInit(){
	global $mosConfig_lang;
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();
	$html = '';

	//загружаем Jquery
	$html .= mosCommonHTML::loadJquery(true);
	//загружаем Jquery UI
	$html .= mosCommonHTML::loadJqueryUI(true);

	//обрубаем загрузку Fullajax
//    if(!defined('_FAX_LOADED'))
//        define('_FAX_LOADED',1);

	//исправляем неправильное определение ида группы на фронте
	if($my->id == 0){
		$user_gid = 29;
	} else if($my->gid < 17){
		$q = "SELECT aro_map.group_id FROM
            #__core_acl_groups_aro_map as aro_map,
            #__core_acl_aro as aro
            WHERE aro_map.aro_id = aro.aro_id
            AND aro.value = $my->id
        ";
		$user_gid = $database->setQuery($q)->loadResult();
	} else{
		$user_gid = $my->gid;
	}

	//elRTE
	$html .= '<script language="JavaScript" src="' . JPATH_SITE . '/mambots/editors/elrte/js/elrte.min.js" type="text/javascript"></script>' . "\n";
	$html .= '<link type="text/css" rel="stylesheet" href="' . JPATH_SITE . '/mambots/editors/elrte/css/elrte.min.css" />' . "\n";

	//elRTE translation messages
	$html .= '<script language="JavaScript" src="' . JPATH_SITE . '/mambots/editors/elrte/js/i18n/elrte.ru.js" type="text/javascript"></script>' . "\n";

	//elFinder
	$html .= '<script language="JavaScript" src="' . JPATH_SITE . '/mambots/editors/elrte/src/elfinder/js/elfinder.min.js" type="text/javascript"></script>' . "\n";
	$html .= '<script language="JavaScript" src="' . JPATH_SITE . '/mambots/editors/elrte/src/elfinder/js/i18n/elfinder.ru.js" type="text/javascript"></script>' . "\n";
	$html .= '<link type="text/css" rel="stylesheet" href="' . JPATH_SITE . '/mambots/editors/elrte/src/elfinder/css/elfinder.css" />' . "\n";

	//переменные по-умолчанию
	$cssfiles = $fm_allow = $panels = array();
	$select_toolbar = 'normal';
	$toolbar_metod = 0;
	$doctype = $css_class = $editor_height = $editor_width = '';
	$absolute_urls = $style_with_css = $allow_source = '1';

	//читаем конфиг, формируем настройки скрипта.
	require (JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_elrte' . DS . 'config_elrte.php');

	$doctype = "doctype : '<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\">',\n";
	$css_class = (!empty($css_class)) ? "cssClass : '$css_class',\n" : "cssClass : 'el-rte',\n";

	$cssfiles_string = '';
	if(($n = count($cssfiles)) > 0 && !empty($cssfiles[0])){
		$cssfiles_string .= "cssfiles : [";
		$i = 0;
		foreach($cssfiles as $cssfile){
			$separator = (substr($cssfile, 0, 1) == '/') ? '' : '/';
			$cssfiles_string .= "'" . JPATH_SITE . $separator . $cssfile . "'";
			if($i < ($n - 1)) $cssfiles_string .= ", ";
			$i++;
		}
		$cssfiles_string .= "],\n";
	} else{
		$cssfiles_string .= "cssfiles : ['" . JPATH_SITE . "/mambots/editors/elrte/css/elrte-inner.css'],\n";
	}

	$absolute_urls = (@$absolute_urls == 1) ? "absoluteURLs : true,\n" : "absoluteURLs : false,\n";
	$allow_source = (@$allow_source == 1) ? "allowSource : true,\n" : "allowSource : false,\n";

	$lang = (@$mosConfig_lang == '') ? "lang : 'ru'," : "lang : '" . substr($mosConfig_lang, 0, 2) . "',\n";

	$style_with_css = (@$style_with_css == 1) ? "styleWithCSS : true,\n" : "styleWithCSS : false,\n";
	$editor_height = (!empty($editor_height)) ? "height : '$editor_height',\n" : '';
	$editor_width = (!empty($editor_width)) ? "width : '$editor_width',\n" : '';
	$fmAllow = (in_array($user_gid, $fm_allow)) ? "fmAllow : true,\n" : "fmAllow : false,\n";
	//$toolbar
	if(isset($panels[$user_gid]) && @$toolbar_metod == 1){
		$toolbar = "toolbars :  {\n";
		$toolbar .= "UserToolbar : [" . $panels[$user_gid] . "]\n";
		$toolbar .= "},\n";
		//$toolbar = "elRTE.prototype.options.toolbars.UserToolbar = [".$panels[$user_gid]."];\n";
	} else{
		$toolbar = '';
	}
	$selected_toolbar = "toolbar  : '$select_toolbar',\n";

	$usedToolbar = (@$toolbar_metod == 1) ? "toolbar  : 'UserToolbar',\n" : $selected_toolbar;

	$html .= "<script type='text/javascript' charset='utf-8'>\n";
	$html .= "
        function insertAtCursor(obj_name, text){
            var area=document.getElementsByName(obj_name).item(0);
            area.elrte.history.add();
            area.elrte.selection.insertHtml(text, true);
            area.elrte.window.focus();
        }
            ";
	$html .= "var opts = {\n";
	$html .= $toolbar;
	$html .= $doctype;
	$html .= $css_class;
	$html .= $cssfiles_string;
	$html .= $absolute_urls;
	$html .= $allow_source;
	$html .= $lang;
	$html .= $style_with_css;
	$html .= $editor_height;
	$html .= $editor_width;
	$html .= $fmAllow;
	$html .= $usedToolbar;
	$html .= "fmOpen : function(callback) {
                    jQuery('<div id=\"myelfinder\" />').elfinder({
                        url : '" . JPATH_SITE . "/administrator/index2.php?option=com_elrte&task=connector',
                        lang : 'en',
                        dialog : { width : 900, modal : true, title : 'elFinder - file manager for web' },
                        closeOnEditorCallback : true,
                        editorCallback : callback
                    })
                }
            }
    </script>";
	echo $html;
}

/**
 * Spaw WYSIWYG Editor - copy editor contents to form field
 * @param string The name of the editor area
 * @param string The name of the form field
 */
function botElrteGetContents($editorArea, $hiddenField){
	$return = "jQuery('#$hiddenField').elrte('updateSource'); \n";
	return $return;
}

/**
 * @param $name
 * @param $content
 * @param $hiddenField
 * @param $width
 * @param $height
 * @param $col
 * @param $row
 * @param $delayInit позволяет инициализировать редактор немедленно, или приостановить
 *                   инициализацию и использовать ее в другом месте. Чтобы включить
 *                   задержку необходимо передать этому параметру 1
 * @return void
 */
function botElrteArea($name, $content, $hiddenField, $width, $height, $col, $row, $delayInit = null){
	$_MAMBOTS = mosMambotHandler::getInstance();
	$results = $_MAMBOTS->trigger('onCustomEditorButton');
	$buttons = array();
	foreach($results as $result){
		if($result[0]){
			$buttons[] = '<img src="' . JPATH_SITE . '/mambots/editors-xtd/' . $result[0] . '" onclick="insertAtCursor( \'' . $hiddenField . '\', \'' . $result[1] . '\' )" alt="' . $result[1] . '"/>';
		}
	}
	$buttons = implode("", $buttons);

	if(!$delayInit){
		?>
	<script type='text/javascript' charset='utf-8'>
		jQuery().ready(function () {
			jQuery('#<?php echo $hiddenField;?>').elrte(opts);
		});
	</script>
	<?php } ?>

<textarea id="<?php echo $hiddenField;?>" name="<?php echo $hiddenField;?>" cols="<?php echo $col;?>" rows="<?php echo $row;?>" style="display: none;width:<?php echo $width;?>px; height:<?php echo $height;?>px;" class="mceEditor"><?php echo $content;?></textarea>
<div style="text-align: right;"><?php echo $buttons;?></div>
<?php
}

?>