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
 * Parameters handler
 * @package Joostina
 */
class mosParameters {
    /**
     @var object*/
    var $_params = null;
    /**
     @var string The raw params string*/
    var $_raw = null;
    /**
     @var string Path to the xml setup file*/
    var $_path = null;
    /**
     @var string The type of setup file*/
    var $_type = null;
    /**
     @var object The xml params element*/
    var $_xmlElem = null;
    /**
     * Constructor
     * @param string The raw parms text
     * @param string Path to the xml setup file
     * @var string The type of setup file
     */
    function mosParameters($text,$path = '',$type = 'component') {
        //jd_inc('mosParameters');
        $this->_params = $this->parse($text);
        $this->_raw = $text;
        $this->_path = $path;
        $this->_type = $type;
    }

    /**
     * Returns the params array
     * @return object
     */
    function toObject() {
        return $this->_params;
    }

    /**
     * Returns a named array of the parameters
     * @return object
     */
    function toArray() {
        return mosObjectToArray($this->_params);
    }

    /**
     * @param string The name of the param
     * @param string The value of the parameter
     * @return string The set value
     */
    function set($key,$value = '') {
        $this->_params->$key = $value;
        return $value;
    }
    /**
     * Sets a default value if not alreay assigned
     * @param string The name of the param
     * @param string The value of the parameter
     * @return string The set value
     */
    function def($key,$value = '') {
        return $this->set($key,$this->get($key,$value));
    }
    /**
     * @param string The name of the param
     * @param mixed The default value if not found
     * @return string
     */
    function get($key,$default = '') {
        if(isset($this->_params->$key)) {
            return $this->_params->$key === '' ? $default:$this->_params->$key;
        } else {
            return $default;
        }
    }
    /**
     * Parse an .ini string, based on phpDocumentor phpDocumentor_parse_ini_file function
     * @param mixed The ini string or array of lines
     * @param boolean add an associative index for each section [in brackets]
     * @return object
     */
    public static function parse($txt,$process_sections = false,$asArray = false) {
        // если в параметрах ничего нет - не будем дальшепытаться его распатсить
        if(trim($txt)=='') {
            return $asArray ? array():new stdClass();
        };

        if(is_string($txt)) {
            $lines = explode("\n",$txt);
        } elseif(is_array($txt)) {
            $lines = $txt;
        } else {
            $lines = array();
        }

        if( (false==$process_sections) &&
                (false==$asArray) &&
                (is_string($txt)) &&
                (false===strpos($txt,'[')) &&
                (false===strpos($txt,'\\')) &&
                (false===strpos($txt,'"')) &&
                (false===strpos($txt,';')) ) {
            $obj = new stdClass();
            foreach($lines as $line) {
                $vars=explode('=',$line,2);
                if(count($vars)==2) {
                    $property = trim($vars[0]);
                    $value = trim($vars[1]);
                    if($value) {
                        if($value == 'false') {
                            $value = false;
                        }elseif($value == 'true') {
                            $value = true;
                        }
                    }
                    $obj->$property = $value;
                }
            }
            return $obj;
        }

        $obj = $asArray ? array():new stdClass();

        $sec_name = '';
        $unparsed = 0;
        if(!$lines) {
            return $obj;
        }
        foreach($lines as $line) {
            // ignore comments
            if($line && $line[0] == ';') {
                continue;
            }
            $line = trim($line);

            if($line == '') {
                continue;
            }
            if($line && $line[0] == '[' && $line[strlen($line) - 1] == ']') {
                $sec_name = substr($line,1,strlen($line) - 2);
                if($process_sections) {
                    if($asArray) {
                        $obj[$sec_name] = array();
                    } else {
                        $obj->$sec_name = new stdClass();
                    }
                }
            } else {
                if($pos = strpos($line,'=')) {
                    $property = trim(substr($line,0,$pos));

                    if(substr($property,0,1) == '"' && substr($property,-1) == '"') {
                        $property = stripcslashes(substr($property,1,count($property) - 2));
                    }
                    $value = trim(substr($line,$pos + 1));
                    if($value == 'false') {
                        $value = false;
                    }
                    if($value == 'true') {
                        $value = true;
                    }
                    if(substr($value,0,1) == '"' && substr($value,-1) == '"') {
                        $value = stripcslashes(substr($value,1,count($value) - 2));
                    }

                    if($process_sections) {
                        $value = str_replace('\n',"\n",$value);
                        if($sec_name != '') {
                            if($asArray) {
                                $obj[$sec_name][$property] = $value;
                            } else {
                                $obj->$sec_name->$property = $value;
                            }
                        } else {
                            if($asArray) {
                                $obj[$property] = $value;
                            } else {
                                $obj->$property = $value;
                            }
                        }
                    } else {
                        $value = str_replace('\n',"\n",$value);
                        if($asArray) {
                            $obj[$property] = $value;
                        } else {
                            $obj->$property = $value;
                        }
                    }
                } else {
                    if($line && trim($line[0]) == ';') {
                        continue;
                    }
                    if($process_sections) {
                        $property = '__invalid'.$unparsed++.'__';
                        if($process_sections) {
                            if($sec_name != '') {
                                if($asArray) {
                                    $obj[$sec_name][$property] = trim($line);
                                } else {
                                    $obj->$sec_name->$property = trim($line);
                                }
                            } else {
                                if($asArray) {
                                    $obj[$property] = trim($line);
                                } else {
                                    $obj->$property = trim($line);
                                }
                            }
                        } else {
                            if($asArray) {
                                $obj[$property] = trim($line);
                            } else {
                                $obj->$property = trim($line);
                            }
                        }
                    }
                }
            }
        }
        return $obj;
    }
    /**
     * @param string The name of the control, or the default text area if a setup file is not found
     * @return string HTML
     */
    function render($name = 'params') {

        if($this->_path) {
            if(!is_object($this->_xmlElem)) {
                require_once (JPATH_BASE.'/includes/domit/xml_domit_lite_include.php');
                $xmlDoc = new DOMIT_Lite_Document();
                $xmlDoc->resolveErrors(true);
                if($xmlDoc->loadXML($this->_path,false,true)) {
                    $root = $xmlDoc->documentElement;
                    $tagName = $root->getTagName();
                    $isParamsFile = ($tagName == 'mosinstall' || $tagName == 'mosparams');
                    if($isParamsFile && $root->getAttribute('type') == $this->_type) {
                        if($params = $root->getElementsByPath('params',1)) {
                            $this->_xmlElem = &$params;
                        }
                    }
                }
            }
        }
        if(is_object($this->_xmlElem)) {
            $html = array();
            $element = &$this->_xmlElem;
            $html[] = '<table width="100%" class="paramlist">';

            if($description = $element->getAttribute('description')) {
                $html[] = '<tr><td colspan="2">'.$description.'</td></tr>';
            }
            $this->_methods = get_class_methods(get_class($this));

            foreach($element->childNodes as $param) {
                $result = $this->renderParam($param,$name);

                switch ($result[5]) {
                    case 'newtable':
                        $html[] = '</table>';
                        $html[] = '<table width="100%" class="paramlist">';
                        break;

                    case 'tabs':
                        $html[] = $result[1];
                        break;

                    default:
                        $html[] = '<tr>';
                        $html[] = '<td width="40%" align="right" valign="top" class="pkey"><span class="editlinktip">'.$result[0].'</span></td>';
                        $html[] = '<td>'.$result[1].'</td>';
                        $html[] = '</tr>';
                        break;
                }

            }
            if(count($element->childNodes) < 1) {
                $html[] = "<tr><td colspan=\"2\"><i>"._NO_PARAMS."</i></td></tr>";
            }
            $html[] = '</table>';

            return implode("\n",$html);
        } else {
            return "<textarea name=\"$name\" cols=\"40\" rows=\"10\" class=\"text_area\">$this->_raw</textarea>";
        }
    }
    /**
     * @param object A param tag node
     * @param string The control name
     * @return array Any array of the label, the form element and the tooltip
     */
    function renderParam(&$param,$control_name = 'params') {
        $result = array();
        $name = $param->getAttribute('name');
        $label = $param->getAttribute('label');
        $value = $this->get($name,$param->getAttribute('default'));
        $description = $param->getAttribute('description');

        $result[0] = $label ? $label : $name;

        if($result[0] == '@spacer') {
            $result[0] = '&nbsp;';
        } else {
            $result[0] = mosToolTip(addslashes($description),addslashes($result[0]),'','',$result[0],'#',0);
        }
        $type = $param->getAttribute('type');
        if(in_array('_form_'.$type,$this->_methods)) {
            $result[1] = call_user_func(array(&$this,'_form_'.$type),$name,$value,$param,$control_name, $label);
        } else {
            //todo пытаемся добавить обработчик неизвестного поля из модуля
            if(mosGetParam($_REQUEST, 'option', '') == 'com_modules'){
                $id = mosGetParam($_REQUEST, 'id', 0);
                if($id>0){
                    $database = database::getInstance();
                    $query = "SELECT module"
				        ."\n FROM #__modules"
				        ."\n WHERE id = ".$id;
		            $database->setQuery($query);
		            $module = $database->loadResult();
                    if(is_file(JPATH_BASE.'/modules/'.$module.'/elements.php')){
                        require_once(JPATH_BASE.'/modules/'.$module.'/elements.php');
                        $className = $module.'_elements';
                        $methodName = 'load_'.$type;
                        if(method_exists($className, $methodName)){
                            //$result[1] = $className::$methodName($name);
                            $result[1] = call_user_func_array(array($className, $methodName), array($name));
                        }
                        else{
                            $result[1] = _HANDLER.' = '.$type;
                        }
                    }
                    else{
                        $result[1] = _HANDLER.' = '.$type;
                    }
                }
                else{
                    $result[1] = _HANDLER.' = '.$type;
                }
            }
            else{
                $result[1] = _HANDLER.' = '.$type;
            }
        }

        if($description) {
            $result[2] = mosToolTip($description,$result[0]);
            $result[2] = '';
        } else {
            $result[2] = '';
        }
        $result[3]=$description;
        $result[4]=$label;
        $result[5]=$type;
        return $result;
    }
    /**
     * @param string The name of the form element
     * @param string The value of the element
     * @param object The xml element for the parameter
     * @param string The control name
     * @return string The html for the element
     */
    function _form_text($name,$value,&$node,$control_name) {
        $size = $node->getAttribute('size');

        return '<input type="text" name="'.$control_name.'['.$name.']" value="'.htmlspecialchars($value).'" class="text_area" size="'.$size.'" />';
    }
    /**
     * @param string The name of the form element
     * @param string The value of the element
     * @param object The xml element for the parameter
     * @param string The control name
     * @return string The html for the element
     */
    function _form_list($name,$value,&$node,$control_name) {
        //$size = $node->getAttribute('size');

        $options = array();
        foreach($node->childNodes as $option) {
            $val = $option->getAttribute('value');
            $text = $option->gettext();
            $options[] = mosHTML::makeOption($val,$text);
        }

        return mosHTML::selectList($options,''.$control_name.'['.$name.']','class="inputbox"','value','text',$value);
    }
    /**
     * @param string The name of the form element
     * @param string The value of the element
     * @param object The xml element for the parameter
     * @param string The control name
     * @return string The html for the element
     */
    function _form_radio($name,$value,&$node,$control_name) {
        $options = array();
        foreach($node->childNodes as $option) {
            $val = $option->getAttribute('value');
            $text = $option->gettext();
            $options[] = mosHTML::makeOption($val,$text);
        }

        return mosHTML::radioList($options,''.$control_name.'['.$name.']','',$value);
    }
    /**
     * @param string The name of the form element
     * @param string The value of the element
     * @param object The xml element for the parameter
     * @param string The control name
     * @return string The html for the element
     */
    function _form_mos_section($name,$value,&$node,$control_name) {
        return '';
    }
    /**
     * @param string The name of the form element
     * @param string The value of the element
     * @param object The xml element for the parameter
     * @param string The control name
     * @return string The html for the element
     */
    function _form_mos_category($name,$value,&$node,$control_name) {
        $database = database::getInstance();

        $scope = $node->getAttribute('scope');
        if(!isset($scope)) {
            $scope = 'content';
        }

        if($scope == 'content') {
            // This might get a conflict with the dynamic translation - TODO: search for better solution
            $query = "SELECT c.id, c.title AS title FROM #__categories AS c WHERE c.published = 1 ORDER BY c.title";
        } else {
            $query = "SELECT c.id, c.title FROM #__categories AS c WHERE c.published = 1 AND c.section = ".$database->Quote($scope)." ORDER BY c.title";
        }
        $database->setQuery($query);
        $options = $database->loadObjectList();
        array_unshift($options,mosHTML::makeOption('0',_SEL_CATEGORY,'id','title'));

        return mosHTML::selectList($options,''.$control_name.'['.$name.']','class="inputbox"','id','title',$value);
    }
    /**
     * @param string The name of the form element
     * @param string The value of the element
     * @param object The xml element for the parameter
     * @param string The control name
     * @return string The html for the element
     */
    function _form_mos_menu($name,$value,$node,$control_name) {
        $menuTypes = mosAdminMenus::menutypes();

        foreach($menuTypes as $menutype) {
            $options[] = mosHTML::makeOption($menutype,$menutype);
        }
        array_unshift($options,mosHTML::makeOption('',_ET_MENU));

        return mosHTML::selectList($options,''.$control_name.'['.$name.']','class="inputbox"','value','text',$value);
    }
    /**
     * @param string The name of the form element
     * @param string The value of the element
     * @param object The xml element for the parameter
     * @param string The control name
     * @return string The html for the element
     */
    function _form_filelist($name,$value,&$node,$control_name) {

        // path to images directory
        $path = JPATH_BASE.$node->getAttribute('directory');
        $filter = $node->getAttribute('filter');
        $files = mosReadDirectory($path,$filter);

        $options = array();
        foreach($files as $file) {
            $options[] = mosHTML::makeOption($file,$file);
        }
        if(!$node->getAttribute('hide_none')) {
            array_unshift($options,mosHTML::makeOption('-1',_DONT_USE_IMAGE));
        }
        if(!$node->getAttribute('hide_default')) {
            array_unshift($options,mosHTML::makeOption('',_DEFAULT_IMAGE));
        }

        return mosHTML::selectList($options,''.$control_name.'['.$name.']','class="inputbox"','value','text',$value,"param$name");
    }
    /**
     * @param string The name of the form element
     * @param string The value of the element
     * @param object The xml element for the parameter
     * @param string The control name
     * @return string The html for the element
     */
    function _form_imagelist($name,$value,&$node,$control_name) {
        $node->setAttribute('filter','\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$');
        return $this->_form_filelist($name,$value,$node,$control_name);
    }
    /**
     * @param string The name of the form element
     * @param string The value of the element
     * @param object The xml element for the parameter
     * @param string The control name
     * @return string The html for the element
     */
    function _form_textarea($name,$value,&$node,$control_name) {
        $rows = $node->getAttribute('rows');
        $cols = $node->getAttribute('cols');
        // convert <br /> tags so they are not visible when editing
        $value = str_replace('<br />',"\n",$value);

        return '<textarea name="'.$control_name.'['.$name.']" cols="'.$cols.'" rows="'.$rows.'" class="text_area">'.htmlspecialchars($value).'</textarea>';
    }

    /**
     * @param string The name of the form element
     * @param string The value of the element
     * @param object The xml element for the parameter
     * @param string The control name
     * @return string The html for the element
     */
    function _form_spacer($name,$value) {
        if($value) {
            return $value;
        } else {
            return '<hr />';
        }
    }

    function _form_tabs($name,$value,$param,$control_name, $label) {

        $css = '<link rel="stylesheet" type="text/css" media="all" href="'.JPATH_SITE.'/includes/js/tabs/tabpane.css" id="luna-tab-style-sheet" />';
        $js = '<script type="text/javascript" src="'.JPATH_SITE.'/includes/js/tabs/tabpane.js"></script>';

        $return = '';

        switch ($value) {
            case 'startPane':
                $return .= '<tr><td></td></tr></table>';
                $return .= $css;
                $return .= $js;
                $return .= '<div class="tab-page" id="'.$name.'">';
                $return .= '<script type="text/javascript">var tabPane1 = new WebFXTabPane( document.getElementById( "'.$name.'" ),0)</script>';
                break;

            case 'endPane':
                $return .= '</div><table>';
                break;

            case 'startTab':
                $return .= '<div class="tab-page" id="'.$name.'">';
                $return .= '<h2 class="tab">'.$label.'</h2>';
                $return .= '<script type="text/javascript">tabPane1.addTabPage( document.getElementById( "'.$name.'" ) );</script>';
                $return .= '<table width="100%" class="paramlist">';
                break;

            case 'endTab':
                $return .= '</table></div>';
                break;

            default:
                break;
        }

        return $return;
    }

    /**
     * special handling for textarea param
     */
	public static function textareaHandling(&$txt) {
        $total = count($txt);
        for($i = 0; $i < $total; $i++) {
            if(strstr($txt[$i],"\n")) {
                $txt[$i] = str_replace("\n",'<br />',$txt[$i]);
            }
        }
        return implode("\n",$txt);
    }

    /*
	* селектор выбора времени кэиширования
    */
    function _form_cache_list($name,$value,$param,$control_name) {
        $options[] = mosHTML::makeOption('0',_M_CACHE_0);
        $options[] = mosHTML::makeOption('60',_M_CACHE_60);
        $options[] = mosHTML::makeOption('300',_M_CACHE_300);
        $options[] = mosHTML::makeOption('600',_M_CACHE_600);
        $options[] = mosHTML::makeOption('900',_M_CACHE_900);
        $options[] = mosHTML::makeOption('1200',_M_CACHE_1200);
        $options[] = mosHTML::makeOption('1800',_M_CACHE_1800);
        $options[] = mosHTML::makeOption('3600',_M_CACHE_3600);
        $options[] = mosHTML::makeOption('7200',_M_CACHE_7200);
        $options[] = mosHTML::makeOption('9000',_M_CACHE_9000);
        $options[] = mosHTML::makeOption('7200',_M_CACHE_7200);
        $options[] = mosHTML::makeOption('18000',_M_CACHE_18000);
        $options[] = mosHTML::makeOption('43200',_M_CACHE_43200);
        $options[] = mosHTML::makeOption('86400',_M_CACHE_86400);
        $options[] = mosHTML::makeOption('172800',_M_CACHE_172800);
        $options[] = mosHTML::makeOption('604800',_M_CACHE_604800);

        return mosHTML::selectList($options,$control_name.'['.$name.']','class="inputbox"','value','text',$value);
    }

	/**
	 * Созданпе выпадающего списка из произвольного SQL запроса
	 * @param string $name - название элемента
	 * @param <type> $value - значение
	 * @param <type> $node - активная нода
	 * @param <type> $control_name - название управляющего элемента
	 * @return html - код выпадающего списка
	 */
    function _form_selectlist($name,$value,&$node,$control_name) {
		$sql = $node->getAttribute('sql');

        $options = database::getInstance()->setQuery($sql)->loadObjectList();
        return mosHTML::selectList($options,''.$control_name.'['.$name.']','class="inputbox" id="selectlist_'.$name.'"','id','title',$value);
    }
}

/**
 * @param string
 * @return string
 */
function mosParseParams($txt) {
    return mosParameters::parse($txt);
}

class mosEmpty {
    function def() {
        return 1;
    }
    function get() {
        return 1;
    }
}