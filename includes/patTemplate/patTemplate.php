<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*
* @version		3.1.0
* @package		patTemplate
* @author		Stephan Schmidt <schst@php.net>
* @license		LGPL
* @link		http://www.php-tools.net
*/
// запрет прямого доступа
defined('_VALID_MOS') or die();

require_once (dirname(__file__).'/patErrorManager.php');
define('PATTEMPLATE_ERROR_TEMPLATE_EXISTS',5010);
define('PATTEMPLATE_WARNING_NO_TEMPLATE',5011);
define('PATTEMPLATE_WARNING_UNKNOWN_TYPE',5012);
define('PATTEMPLATE_ERROR_BASECLASS_NOT_FOUND',5050);
define('PATTEMPLATE_ERROR_MODULE_NOT_FOUND',5051);
define('PATTEMPLATE_ERROR_EXPECTED_ARRAY',5052);
define('PATTEMPLATE_ERROR_NO_INPUT',6000);
define('PATTEMPLATE_ERROR_RECURSION',6010);
class patTemplate {
var $_systemVars = array('appName' => 'patTemplate','appVersion' => '3.1.0','author' => array('Stephan Schmidt <schst@php.net>'));
var $_defaultAttributes = array('type' => 'standard','visibility' => 'visible','loop' => 1,'unusedvars' => 'strip','whitespace' => 'keep','autoclear' => 'off','autoload' => 'on');
var $_options = array('startTag' => '{','endTag' => '}','root' => array('__default' => '.'),'namespace' => 'patTemplate','maintainBc' => true,'defaultFunction' => false);
var $_startTag = '{';
var $_endTag = '}';
var $_modules = array();
var $_moduleDirs = array();
var $_templateList = array();
var $_templates = array();
var $_globals = array();
var $_vars = array();
var $_root;
var $_outputFilters = array();
var $_inputFilters = array();
var $_tmplCache = null;
var $_discoveredPlaceholders = array();
function patTemplate($type = 'html') {
if(!defined('PATTEMPLATE_INCLUDE_PATH')) {
define('PATTEMPLATE_INCLUDE_PATH',dirname(__file__).'/patTemplate');
}
$this->setType($type);
}
function setOption($option,$value) {
$this->_options[$option] = $value;
}
function getOption($option) {
if(!isset($this->_options[$option])) {
return null;
}
return $this->_options[$option];
}
function setBasedir($basedir) {
$this->setRoot($basedir);
}
function setRoot($root,$reader = '__default') {
$this->_options['root'][$reader] = $root;
}
function getRoot($reader = '__default') {
return $this->_options['root'][$reader];
}
function setNamespace($ns) {
$this->_options['namespace'] = $ns;
}
function getNamespace() {
return $this->_options['namespace'];
}
function setDefaultAttribute($name,$value) {
$this->_defaultAttributes[$name] = $value;
}
function setDefaultAttributes($attributes) {
$this->_defaultAttributes = array_merge($this->_defaultAttributes,$attributes);
}
function getDefaultAttributes() {
return $this->_defaultAttributes;
}
function setType($type) {
switch(strtolower($type)) {
case "tex":
$this->setTags('<{','}>');
break;
case "html":
$this->setTags('{','}');
break;
default:
return patErrorManager::raiseWarning(PATTEMPLATE_WARNING_UNKNOWN_TYPE,"Unknown type '$type'. Please use 'html' or 'tex'.");
}
return true;
}
function setTags($startTag,$endTag) {
$this->_options['startTag'] = $startTag;
$this->_options['endTag'] = $endTag;
$this->_startTag = $startTag;
$this->_endTag = $endTag;
return true;
}
function getStartTag() {
return $this->_options['startTag'];
}
function getEndTag() {
return $this->_options['endTag'];
}
function addModuleDir($moduleType,$dir) {
if(!isset($this->_moduleDirs[$moduleType]))
$this->_moduleDirs[$moduleType] = array();
if(is_array($dir))
$this->_moduleDirs[$moduleType] = array_merge($this->_moduleDirs[$moduleType],$dir);
else
array_push($this->_moduleDirs[$moduleType],$dir);
}
function setAttribute($template,$attribute,$value) {
$template = strtolower($template);
if(!isset($this->_templates[$template])) {
return patErrorManager::raiseWarning(PATTEMPLATE_WARNING_NO_TEMPLATE,"Template '$template' does not exist.");
}
$attribute = strtolower($attribute);
$this->_templates[$template]['attributes'][$attribute] = $value;
return true;
}
function setAttributes($template,$attributes) {
if(!is_array($attributes)) {
return patErrorManager::raiseError(PATTEMPLATE_ERROR_EXPECTED_ARRAY,'patTemplate::setAttributes: Expected array as second parameter, '.gettype($attributes).' given');
}
$template = strtolower($template);
$attributes = array_change_key_case($attributes);
if(!isset($this->_templates[$template])) {
return patErrorManager::raiseWarning(PATTEMPLATE_WARNING_NO_TEMPLATE,"Template '$template' does not exist.");
}
$this->_templates[$template]['attributes'] = array_merge($this->_templates[$template]['attributes'],$attributes);
return true;
}
function getAttributes($template) {
$template = strtolower($template);
if(!isset($this->_templates[$template])) {
return patErrorManager::raiseWarning(PATTEMPLATE_WARNING_NO_TEMPLATE,"Template '$template' does not exist.");
}
return $this->_templates[$template]['attributes'];
}
function getAttribute($template,$attribute) {
$template = strtolower($template);
$attribute = strtolower($attribute);
if(!isset($this->_templates[$template])) {
return patErrorManager::raiseWarning(PATTEMPLATE_WARNING_NO_TEMPLATE,"Template '$template' does not exist.");
}
return $this->_templates[$template]['attributes'][$attribute];
}
function clearAttribute($template,$attribute) {
$template = strtolower($template);
$attribute = strtolower($attribute);
if(!isset($this->_templates[$template])) {
return patErrorManager::raiseWarning(PATTEMPLATE_WARNING_NO_TEMPLATE,"Template '$template' does not exist.");
}
$this->_templates[$template]['attributes'][$attribute] = '';
;
return true;
}
function prepareTemplate($name) {
$name = strtolower($name);
if(!isset($this->_vars[$name])) {
$this->_vars[$name] = array('scalar' => array(),'rows' => array());
}
}
function addVar($template,$varname,$value) {
$template = strtolower($template);
$varname = strtoupper($varname);
if(!is_array($value)) {
$this->_vars[$template]['scalar'][$varname] = $value;
return true;
}
$cnt = count($value);
for($i = 0; $i < $cnt; $i++) {
if(!isset($this->_vars[$template]['rows'][$i])) {
$this->_vars[$template]['rows'][$i] = array();
}
$this->_vars[$template]['rows'][$i][$varname] = $value[$i];
}
return true;
}
function getVar($template,$varname) {
$template = strtolower($template);
$varname = strtoupper($varname);
if(isset($this->_vars[$template]['scalar'][$varname]))
return $this->_vars[$template]['scalar'][$varname];
$value = array();
$cnt = count($this->_vars[$template]['rows']);
for($i = 0; $i < $cnt; $i++) {
if(!isset($this->_vars[$template]['rows'][$i][$varname]))
continue;
array_push($value,$this->_vars[$template]['rows'][$i][$varname]);
}
if(!empty($value))
return $value;
return null;
}
function clearVar($template,$varname) {
$template = strtolower($template);
$varname = strtoupper($varname);
if(isset($this->_vars[$template]['scalar'][$varname])) {
unset($this->_vars[$template]['scalar'][$varname]);
return true;
}
$result = false;
$cnt = count($this->_vars[$template]['rows']);
for($i = 0; $i < $cnt; $i++) {
if(!isset($this->_vars[$template]['rows'][$i][$varname])) {
continue;
}
unset($this->_vars[$template]['rows'][$i][$varname]);
$result = true;
}
return $result;
}
function addVars($template,$variables,$prefix = '') {
$template = strtolower($template);
$prefix = strtoupper($prefix);
$variables = array_change_key_case($variables,CASE_UPPER);
foreach($variables as $varname => $value) {
$varname = $prefix.$varname;
if(!is_array($value)) {
if(!is_scalar($value)) {
continue;
}
$this->_vars[$template]['scalar'][$varname] = $value;
continue;
}
$cnt = count($value);
for($i = 0; $i < $cnt; $i++) {
if(!isset($this->_vars[$template]['rows'][$i]))
$this->_vars[$template]['rows'][$i] = array();
$this->_vars[$template]['rows'][$i][$varname] = $value[$i];
}
}
}
function clearVars($template) {
$template = strtolower($template);
$this->_vars[$template] = array('scalar' => array(),'rows' => array());
return true;
}
function addRows($template,$rows,$prefix = '') {
$template = strtolower($template);
$prefix = strtoupper($prefix);
$cnt = count($rows);
for($i = 0; $i < $cnt; $i++) {
if(!isset($this->_vars[$template]['rows'][$i]))
$this->_vars[$template]['rows'][$i] = array();
$rows[$i] = array_change_key_case($rows[$i],CASE_UPPER);
foreach($rows[$i] as $varname => $value) {
$this->_vars[$template]['rows'][$i][$prefix.$varname] = $value;
}
}
}
function addObject($template,$object,$prefix = '',$ignorePrivate = false) {
if(is_array($object)) {
$rows = array();
foreach($object as $o) {
array_push($rows,$this->getObjectVars($o,$ignorePrivate));
}
return $this->addRows($template,$rows,$prefix);
} elseif(is_object($object)) {
return $this->addVars($template,$this->getObjectVars($object,$ignorePrivate),$prefix);
}
return false;
}
function getObjectVars($obj,$ignorePrivate = false) {
if(method_exists($obj,'getVars')) {
return $obj->getVars();
}
$vars = get_object_vars($obj);
if($ignorePrivate === false) {
return $vars;
}
foreach($vars as $var => $value) {
if($var{0} == '_') {
unset($vars[$var]);
}
}
return $vars;
}
function addGlobalVar($varname,$value) {
$this->_globals[strtoupper($varname)] = (string )$value;
return true;
}
function clearGlobalVar($varname) {
$varname = strtoupper($varname);
if(!isset($this->_globals[$varname])) {
return false;
}
unset($this->_globals[$varname]);
return true;
}
function clearGlobalVars() {
$this->_globals = array();
return true;
}
function addGlobalVars($variables,$prefix = '') {
$variables = array_change_key_case($variables,CASE_UPPER);
$prefix = strtoupper($prefix);
foreach($variables as $varname => $value) {
$this->_globals[$prefix.$varname] = (string )$value;
}
return true;
}
function getGlobalVars() {
return $this->_globals;
}
function exists($name) {
return in_array(strtolower($name),$this->_templateList);
}
function useTemplateCache($cache,$params = array()) {
if(!is_object($cache)) {
$cache = &$this->loadModule('TemplateCache',$cache,$params);
}
if(patErrorManager::isError($cache))
return $cache;
$this->_tmplCache = &$cache;
return true;
}
function applyOutputFilter($filter,$params = array(),$template = null) {
if(!is_object($filter)) {
$filter = &$this->loadModule('OutputFilter',$filter,$params);
}
if(patErrorManager::isError($filter)) {
return $filter;
}
if($template === null) {
$this->_outputFilters[] = &$filter;
return true;
}
$template = strtolower($template);
if(!$this->exists($template)) {
return patErrorManager::raiseWarning(PATTEMPLATE_WARNING_NO_TEMPLATE,'The selected template does not exist');
}
$this->_templates[$template]['attributes']['outputfilter'] = &$filter;
return true;
}
function applyInputFilter($filter,$params = array()) {
if(!is_object($filter)) {
$filter = &$this->loadModule('InputFilter',$filter,$params);
}
if(patErrorManager::isError($filter))
return $filter;
$this->_inputFilters[] = &$filter;
return true;
}
function readTemplatesFromFile($filename) {
return $this->readTemplatesFromInput($filename,'File');
}
function readTemplatesFromInput($input,$reader = 'File',$options = null,$parseInto = null) {
if((string )$input === '') {
return patErrorManager::raiseError(PATTEMPLATE_ERROR_NO_INPUT,'No input to read has been passed.');
}
if(is_array($options)) {
$options = array_merge($this->_options,$options);
} else {
$options = $this->_options;
}
if(!is_null($parseInto)) {
$parseInto = strtolower($parseInto);
}
$templates = false;
if($this->_tmplCache !== null) {
$key = $this->_tmplCache->getKey($input,$options);
$templates = $this->_loadTemplatesFromCache($input,$reader,$options,$key);
if(patErrorManager::isError($templates)) {
return $templates;
}
}
if($templates === false) {
if(!is_object($reader)) {
$reader = &$this->loadModule('Reader',$reader);
if(patErrorManager::isError($reader)) {
return $reader;
}
}
if($reader->isInUse()) {
$reader = &$this->loadModule('Reader',$reader->getName(),array(),true);
if(patErrorManager::isError($reader)) {
return $reader;
}
}
$reader->setOptions($options);
if(!is_null($parseInto)) {
$attributes = $this->getAttributes($parseInto);
if(!patErrorManager::isError($attributes)) {
$reader->setRootAttributes($attributes);
}
}
$templates = $reader->readTemplates($input);
if(patErrorManager::isError($templates))
return $templates;
if($this->_tmplCache !== null) {
$this->_tmplCache->write($key,$templates);
}
}
foreach($templates as $name => $spec) {
if($name == '__ptroot') {
if($parseInto === false) {
continue;
}
if(!in_array($parseInto,$this->_templateList))
continue;
$spec['loaded'] = true;
$spec['attributes'] = $this->_templates[$parseInto]['attributes'];
$name = $parseInto;
} else {
array_push($this->_templateList,$name);
}
if($this->_root === null && is_null($parseInto) && isset($spec['isRoot']) && $spec['isRoot'] == true) {
$this->_root = $name;
}
$spec['iteration'] = 0;
$spec['lastMode'] = 'w';
$spec['result'] = '';
$spec['modifyVars'] = array();
$spec['copyVars'] = array();
$spec['defaultVars'] = array();
$this->_templates[$name] = $spec;
$this->prepareTemplate($name);
foreach($spec['varspecs'] as $varname => $varspec) {
if(isset($varspec['modifier'])) {
$this->_templates[$name]['modifyVars'][$varname] = $varspec['modifier'];
}
if(isset($varspec['copyfrom'])) {
$this->_templates[$name]['copyVars'][$varname] = $varspec['copyfrom'];
}
if(!isset($varspec['default']))
continue;
$this->_templates[$name]['defaultVars'][$varname] = $varspec['default'];
if(!is_null($this->getVar($name,$varname)))
continue;
$this->addVar($name,$varname,$varspec['default']);
}
unset($this->_templates[$name]['varspecs']);
if(isset($this->_templates[$name]['attributes']['src']) && $this->_templates[$name]['attributes']['autoload'] == 'on') {
if($this->_templates[$name]['loaded'] !== true) {
if($this->_templates[$name]['attributes']['parse'] == 'on') {
$this->readTemplatesFromInput($this->_templates[$name]['attributes']['src'],$this->_templates[$name]['attributes']['reader'],$options,$name);
} else {
$this->loadTemplateFromInput($this->_templates[$name]['attributes']['src'],$this->_templates[$name]['attributes']['reader'],null,$name);
}
$this->_templates[$name]['loaded'] = true;
}
}
}
return true;
}
function _loadTemplatesFromCache($input,&$reader,$options,$key) {
if(is_object($reader))
$statName = $reader->getName();
else
$statName = $reader;
$stat = &$this->loadModule('Stat',$statName);
$stat->setOptions($options);
$modTime = $stat->getModificationTime($input);
$templates = $this->_tmplCache->load($key,$modTime);
return $templates;
}
function loadTemplateFromInput($input,$reader = 'File',$options = null,$parseInto = false) {
if(is_array($options))
$options = array_merge($this->_options,$options);
else
$options = $this->_options;
if(!is_null($parseInto))
$parseInto = strtolower($parseInto);
$reader = &$this->loadModule('Reader',$reader);
if(patErrorManager::isError($reader)) {
return $reader;
}
$reader->setOptions($options);
$result = $reader->loadTemplate($input);
if(patErrorManager::isError($result)) {
return $result;
}
$this->_templates[$parseInto]['content'] .= $result;
$this->_templates[$parseInto]['loaded'] = true;
return true;
}
function loadTemplate($template) {
$template = strtolower($template);
if(!isset($this->_templates[$template])) {
return patErrorManager::raiseWarning(PATTEMPLATE_WARNING_NO_TEMPLATE,"Template '$template' does not exist.");
}
if($this->_templates[$template]['loaded'] === true)
return true;
if($this->_templates[$template]['attributes']['parse'] == 'on') {
return $this->readTemplatesFromInput($this->_templates[$template]['attributes']['src'],$this->_templates[$template]['attributes']['reader'],null,$template);
} else {
return $this->loadTemplateFromInput($this->_templates[$template]['attributes']['src'],$this->_templates[$template]['attributes']['reader'],null,$template);
}
}
function &loadModule($moduleType,$moduleName,$params = array(),$new = false) {
if(!isset($this->_modules[$moduleType]))
$this->_modules[$moduleType] = array();
$sig = md5($moduleName.serialize($params));
if(isset($this->_modules[$moduleType][$sig]) && $new === false) {
return $this->_modules[$moduleType][$sig];
}
if(!class_exists('patTemplate_Module')) {
$file = sprintf("%s/Module.php",$this->getIncludePath());
if(!@include_once $file)
return patErrorManager::raiseError(PATTEMPLATE_ERROR_BASECLASS_NOT_FOUND,'Could not load module base class.');
}
$baseClass = 'patTemplate_'.$moduleType;
if(!class_exists($baseClass)) {
$baseFile = sprintf("%s/%s.php",$this->getIncludePath(),$moduleType);
if(!@include_once $baseFile)
return patErrorManager::raiseError(PATTEMPLATE_ERROR_BASECLASS_NOT_FOUND,"Could not load base class for $moduleType ($baseFile).");
}
$moduleClass = 'patTemplate_'.$moduleType.'_'.$moduleName;
if(!class_exists($moduleClass)) {
if(isset($this->_moduleDirs[$moduleType]))
$dirs = $this->_moduleDirs[$moduleType];
else
$dirs = array();
array_push($dirs,$this->getIncludePath().'/'.$moduleType);
$found = false;
foreach($dirs as $dir) {
$moduleFile = sprintf("%s/%s.php",$dir,str_replace('_','/',$moduleName));
if(@include_once $moduleFile) {
$found = true;
break;
}
}
if(!$found) {
return patErrorManager::raiseError(PATTEMPLATE_ERROR_MODULE_NOT_FOUND,"Could not load module $moduleClass ($moduleFile).");
}
}
if(!class_exists($moduleClass)) {
return patErrorManager::raiseError(PATTEMPLATE_ERROR_MODULE_NOT_FOUND,"Module file $moduleFile does not contain class $moduleClass.");
}
$this->_modules[$moduleType][$sig] = &new $moduleClass;
if(method_exists($this->_modules[$moduleType][$sig],'setTemplateReference')) {
$this->_modules[$moduleType][$sig]->setTemplateReference($this);
}
$this->_modules[$moduleType][$sig]->setParams($params);
return $this->_modules[$moduleType][$sig];
}
function moduleExists($moduleType,$moduleName) {
if(isset($this->_moduleDirs[$moduleType])) {
$dirs = $this->_moduleDirs[$moduleType];
} else {
$dirs = array();
}
array_push($dirs,$this->getIncludePath().'/'.$moduleType);
foreach($dirs as $dir) {
$moduleFile = sprintf("%s/%s.php",$dir,str_replace('_','/',$moduleName));
if(!file_exists($moduleFile)) {
continue;
}
if(!is_readable($moduleFile)) {
continue;
}
return true;
}
return false;
}
function parseTemplate($template,$mode = 'w') {
$template = strtolower($template);
if(!isset($this->_templates[$template])) {
return patErrorManager::raiseWarning(PATTEMPLATE_WARNING_NO_TEMPLATE,"Template '$template' does not exist.");
}
if($this->_templates[$template]['attributes']['visibility'] == 'hidden') {
$this->_templates[$template]['result'] = '';
$this->_templates[$template]['parsed'] = true;
return true;
}
if($this->_templates[$template]['loaded'] !== true) {
if($this->_templates[$template]['attributes']['parse'] == 'on') {
$result = $this->readTemplatesFromInput($this->_templates[$template]['attributes']['src'],$this->_templates[$template]['attributes']['reader'],null,$template);
} else {
$result = $this->loadTemplateFromInput($this->_templates[$template]['attributes']['src'],$this->_templates[$template]['attributes']['reader'],null,$template);
}
if(patErrorManager::isError($result)) {
return $result;
}
}
if(isset($this->_templates[$template]['attributes']['autoclear']) && $this->_templates[$template]['attributes']['autoclear'] == 'yes' && $mode === 'w' && $this->_templates[$template]['lastMode'] != 'a') {
$this->_templates[$template]['parsed'] = false;
}
if($this->_templates[$template]['parsed'] === true && $mode === 'w') {
return true;
}
$this->_templates[$template]['lastMode'] = $mode;
$this->_initTemplate($template);
if(!isset($this->_vars[$template]['rows'])) {
$this->_vars[$template]['rows'] = array();
}
$loop = count($this->_vars[$template]['rows']);
if($loop < 1) {
$loop = 1;
}
if(isset($this->_templates[$template]['attributes']['maxloop'])) {
$loop = ceil($loop / $this->_templates[$template]['attributes']['maxloop']) * $this->_templates[$template]['attributes']['maxloop'];
}
$this->_templates[$template]['loop'] = max($this->_templates[$template]['attributes']['loop'],$loop);
$start = 0;
if(isset($this->_templates[$template]['attributes']['limit'])) {
$p = strpos($this->_templates[$template]['attributes']['limit'],',');
if($p === false) {
$this->_templates[$template]['loop'] = min($this->_templates[$template]['loop'],$this->_templates[$template]['attributes']['limit']);
$start = 0;
} else {
$start = substr($this->_templates[$template]['attributes']['limit'],0,$p);
$end = substr($this->_templates[$template]['attributes']['limit'],$p + 1) + $start;
$this->_templates[$template]['loop'] = min($this->_templates[$template]['loop'],$end);
}
}
if($mode == 'w') {
$this->_templates[$template]['result'] = '';
$this->_templates[$template]['iteration'] = $start;
}
$loopCount = 0;
for($i = $start; $i < $this->_templates[$template]['loop']; $i++) {
$finished = false;
unset($this->_templates[$template]['vars']);
$this->_fetchVariables($template);
$result = $this->_fetchTemplate($template);
if($result === false) {
$this->_templates[$template]['iteration']++;
continue;
}
$this->_parseVariables($template);
$result = $this->_parseDependencies($template);
if(patErrorManager::isError($result)) {
return $result;
}
$this->_templates[$template]['result'] .= $this->_templates[$template]['work'];
$this->_templates[$template]['iteration']++;
++$loopCount;
if(isset($this->_templates[$template]['attributes']['maxloop'])) {
if($loopCount == $this->_templates[$template]['attributes']['maxloop'] && $i < ($loop - 1)) {
$loopCount = 0;
$finished = true;
$this->_templates[$template]['parsed'] = true;
$this->parseTemplate($this->_templates[$template]['attributes']['parent'],'a');
$this->_templates[$template]['parsed'] = false;
$this->_templates[$template]['result'] = '';
}
}
}
if(!$finished && isset($this->_templates[$template]['attributes']['maxloop'])) {
$this->_templates[$template]['parsed'] = true;
$this->parseTemplate($this->_templates[$template]['attributes']['parent'],'a',false);
$this->_templates[$template]['parsed'] = false;
$this->_templates[$template]['result'] = '';
$this->_templates[$this->_templates[$template]['attributes']['parent']]['work'] = '';
}
$this->_parseGlobals($template);
$this->_handleUnusedVars($template);
$this->_templates[$template]['parsed'] = true;
if(isset($this->_templates[$template]['attributes']['autoclear']) && $this->_templates[$template]['attributes']['autoclear'] == 'yes') {
$this->_vars[$template] = array('scalar' => array(),'rows' => array());
}
if(isset($this->_templates[$template]['attributes']['outputfilter'])) {
if(is_object($this->_templates[$template]['attributes']['outputfilter'])) {
$filter = &$this->_templates[$template]['attributes']['outputfilter'];
} else {
$filter = &$this->loadModule('OutputFilter',$this->_templates[$template]['attributes']['outputfilter']);
}
if(patErrorManager::isError($filter)) {
return $filter;
}
$this->_templates[$template]['result'] = $filter->apply($this->_templates[$template]['result']);
}
return true;
}
function _initTemplate($template) {
foreach($this->_templates[$template]['copyVars'] as $dest => $src) {
if(!is_array($src)) {
$srcTemplate = $template;
$srcVar = $src;
} else {
$srcTemplate = $src[0];
$srcVar = $src[1];
}
$copied = false;
if(isset($this->_vars[$srcTemplate])) {
if(isset($this->_vars[$srcTemplate]['scalar'][$srcVar])) {
$this->_vars[$template]['scalar'][$dest] = $this->_vars[$srcTemplate]['scalar'][$srcVar];
continue;
}
$rows = count($this->_vars[$srcTemplate]['rows']);
for($i = 0; $i < $rows; $i++) {
if(!isset($this->_vars[$srcTemplate]['rows'][$i][$srcVar]))
continue;
if(!isset($this->_vars[$template]['rows'][$i]))
$this->_vars[$template]['rows'][$i] = array();
$this->_vars[$template]['rows'][$i][$dest] = $this->_vars[$srcTemplate]['rows'][$i][$srcVar];
$copied = true;
}
}
if(!$copied && isset($this->_globals[$srcVar])) {
$this->_vars[$template]['scalar'][$dest] = $this->_globals[$srcVar];
}
}
return true;
}
function _parseVariables($template) {
$this->_applyModifers($template,$this->_templates[$template]['vars']);
foreach($this->_templates[$template]['vars'] as $key => $value) {
if(is_array($value)) {
if(count($this->_templates[$template]['currentDependencies']) == 1) {
$child = $this->_templates[$template]['currentDependencies'][0];
} else {
if(isset($this->_templates[$template]['attributes']['child']))
$child = $this->_templates[$template]['attributes']['child'];
else
continue;
}
$this->setAttribute($child,'autoclear','yes');
$this->addVar($child,$key,$value);
continue;
}
$var = $this->_startTag.$key.$this->_endTag;
$this->_templates[$template]['work'] = str_replace($var,$value,$this->_templates[$template]['work']);
}
return true;
}
function _parseGlobals($template) {
$globalVars = $this->_globals;
$this->_applyModifers($template,$globalVars);
foreach($globalVars as $key => $value) {
if(is_array($value)) {
continue;
}
$var = $this->_startTag.$key.$this->_endTag;
$this->_templates[$template]['result'] = str_replace($var,$value,$this->_templates[$template]['result']);
}
return true;
}
function _applyModifers($template,&$vars) {
foreach($this->_templates[$template]['modifyVars'] as $varname => $modifier) {
if(!isset($vars[$varname])) {
continue;
}
if(($modifier['type'] === 'php' || $modifier['type'] === 'auto') && is_callable($modifier['mod'])) {
$vars[$varname] = call_user_func($modifier['mod'],$vars[$varname]);
continue;
}
if($modifier['type'] === 'php') {
continue;
}
$mod = &$this->loadModule('Modifier',ucfirst($modifier['mod']));
$vars[$varname] = $mod->modify($vars[$varname],$modifier['params']);
}

if(isset($this->_templates[$template]['attributes']['defaultmodifier'])) {
$defaultModifier = $this->_templates[$template]['attributes']['defaultmodifier'];
if(is_callable($defaultModifier)) {
$type = 'php';
} else {
$type = 'custom';
$defaultModifier = &$this->loadModule('Modifier',ucfirst($defaultModifier));
}
foreach(array_keys($vars) as $varname) {
if(isset($this->_templates[$template]['modifyVars'][$varname])) {
continue;
}
if($type === 'php') {
$vars[$varname] = call_user_func($defaultModifier,$vars[$varname]);
} else {
$vars[$varname] = $defaultModifier->modify($vars[$varname],array());
}
}
}
return true;
}
function _parseDependencies($template) {
$countDep = count($this->_templates[$template]['currentDependencies']);
for($i = 0; $i < $countDep; $i++) {
$depTemplate = $this->_templates[$template]['currentDependencies'][$i];
if($depTemplate == $template) {
return patErrorManager::raiseError(PATTEMPLATE_ERROR_RECURSION,'You have an error in your template "'.$template.'", which leads to recursion');
}
$this->parseTemplate($depTemplate);
$var = $this->_startTag.'TMPL:'.strtoupper($depTemplate).$this->_endTag;
$this->_templates[$template]['work'] = str_replace($var,$this->_templates[$depTemplate]['result'],$this->_templates[$template]['work']);
}
return true;
}
function _fetchTemplate($template) {
switch($this->_templates[$template]['attributes']['type']) {
case 'condition':
$value = $this->_getConditionValue($template,$this->_templates[$template]['attributes']['conditionvar']);
if($value === false) {
$this->_templates[$template]['work'] = '';
$this->_templates[$template]['currentDependencies'] = array();
} else {
$this->_templates[$template]['work'] = $this->_templates[$template]['subtemplates'][$value]['data'];
$this->_templates[$template]['currentDependencies'] = $this->_templates[$template]['subtemplates'][$value]['dependencies'];
}
break;
case 'simplecondition':
foreach($this->_templates[$template]['attributes']['requiredvars'] as $var) {

if($var[0] !== $template) {
$this->_fetchVariables($var[0]);
}
$value = null;

if(isset($this->_templates[$var[0]]['vars'][$var[1]]) && strlen($this->_templates[$var[0]]['vars'][$var[1]]) > 0) {
$value = $this->_templates[$var[0]]['vars'][$var[1]];
}
if(isset($this->_templates[$template]['attributes']['useglobals'])) {
if(isset($this->_globals[$var[1]]) && strlen($this->_globals[$var[1]]) > 1) {
$value = $this->_globals[$var[1]];
}
}
if($value !== null) {
if($var[2] === null) {
continue;
} else {


$condition = $var[2];
if(substr($condition,0,1) == '#' && substr($condition,-1,1) == '#') {
if(preg_match($condition,$value)) {
continue;
}
} else
if($condition == $value) {
continue;
}
}
}
$this->_templates[$template]['work'] = '';
$this->_templates[$template]['currentDependencies'] = array();
break 2;
}
$this->_templates[$template]['work'] = $this->_templates[$template]['content'];
$this->_templates[$template]['currentDependencies'] = $this->_templates[$template]['dependencies'];
break;
case 'modulo':

if($this->_hasVariables($template)) {
$value = (string )($this->_templates[$template]['iteration'] + 1) % $this->_templates[$template]['attributes']['modulo'];
} else {
$value = '__empty';
}
$value = $this->_getConditionValue($template,$value,false);
if($value === false) {
$this->_templates[$template]['work'] = '';
$this->_templates[$template]['currentDependencies'] = array();
} else {
$this->_templates[$template]['work'] = $this->_templates[$template]['subtemplates'][$value]['data'];
$this->_templates[$template]['currentDependencies'] = $this->_templates[$template]['subtemplates'][$value]['dependencies'];
}
break;
default:
$this->_templates[$template]['work'] = $this->_templates[$template]['content'];
$this->_templates[$template]['currentDependencies'] = $this->_templates[$template]['dependencies'];
break;
}
return true;
}
function _hasVariables($template) {
if(!empty($this->_vars[$template]['scalar'])) {
return true;
}
if(isset($this->_vars[$template]['rows'][$this->_templates[$template]['iteration']])) {
return true;
}
return false;
}
function _getConditionValue($template,$value,$isVar = true) {
if($isVar === true) {
if(isset($this->_templates[$template]['attributes']['conditiontmpl'])) {
$_template = $this->_templates[$template]['attributes']['conditiontmpl'];
$this->_fetchVariables($_template);
} else {
$_template = $template;
}
if(!isset($this->_templates[$_template]['vars'][$value]) || strlen($this->_templates[$_template]['vars'][$value]) === 0) {
if($this->_templates[$template]['attributes']['useglobals'] == 'yes' || $this->_templates[$template]['attributes']['useglobals'] == 'useglobals') {
if(isset($this->_globals[$value]) && strlen($this->_globals[$value]) > 0) {
$value = $this->_globals[$value];
} else {
$value = '__empty';
}
} else {
$value = '__empty';
}
} else {
$value = $this->_templates[$_template]['vars'][$value];
}
} else {
$_template = $template;
}


if($value === '__empty' && isset($this->_templates[$template]['subtemplates']['__empty'])) {
return $value;
}

if($value !== '__empty' && $this->_templates[$_template]['loop'] === 1) {
if(isset($this->_templates[$template]['subtemplates']['__single'])) {
return '__single';
}
} else {

if($this->_templates[$_template]['iteration'] == 0) {
if(isset($this->_templates[$template]['subtemplates']['__first'])) {
return '__first';
}
}
if(isset($this->_templates[$_template]['loop'])) {
$max = $this->_templates[$_template]['loop'] - 1;
if($this->_templates[$_template]['iteration'] == $max) {
if(isset($this->_templates[$template]['subtemplates']['__last'])) {
return '__last';
}
}
}
}

foreach(array_keys($this->_templates[$template]['subtemplates']) as $key) {
if(isset($this->_templates[$template]['subtemplates'][$key]['attributes']['var'])) {
$var = $this->_templates[$template]['subtemplates'][$key]['attributes']['var'];
if(isset($this->_templates[$template]['vars'][$var])) {
$current = $this->_templates[$template]['vars'][$var];
} else {
$current = null;
}
} else {
$current = $key;
}
if((string )$value === (string )$current) {
return $key;
}
}
if(isset($this->_templates[$template]['subtemplates']['__default'])) {
return '__default';
}
return false;
}
function _fetchVariables($template) {
if(isset($this->_templates[$template]['vars'])) {
return true;
}
$iteration = $this->_templates[$template]['iteration'];
$vars = array();
if(isset($this->_templates[$template]['attributes']['varscope'])) {
if(!is_array($this->_templates[$template]['attributes']['varscope'])) {
$this->_templates[$template]['attributes']['varscope'] = array($this->_templates[$template]['attributes']['varscope']);
}
foreach($this->_templates[$template]['attributes']['varscope'] as $scopeTemplate) {
if($this->exists($scopeTemplate)) {
$this->_fetchVariables($scopeTemplate);
$vars = array_merge($this->_templates[$scopeTemplate]['vars'],$vars);
} else {
patErrorManager::raiseWarning(PATTEMPLATE_WARNING_NO_TEMPLATE,'Template \''.$scopeTemplate.'\' does not exist, referenced in varscope attribute of template \''.$template.'\'');
}
}
} else {
$vars = array();
}
if(isset($this->_vars[$template]) && isset($this->_vars[$template]['scalar'])) {
$vars = array_merge($vars,$this->_vars[$template]['scalar']);
}
if(isset($this->_vars[$template]['rows'][$iteration])) {
$vars = array_merge($vars,$this->_vars[$template]['rows'][$iteration]);
}
$currentRow = $iteration + $this->_templates[$template]['attributes']['rowoffset'];
$vars['PAT_ROW_VAR'] = $currentRow;
if($this->_templates[$template]['attributes']['type'] == 'modulo') {
$vars['PAT_MODULO_REP'] = ceil($currentRow / $this->_templates[$template]['attributes']['modulo']);
$vars['PAT_MODULO'] = ($this->_templates[$template]['iteration'] + 1) % $this->_templates[$template]['attributes']['modulo'];
}
if($this->_templates[$template]['attributes']['addsystemvars'] !== false) {
$vars['PATTEMPLATE_VERSION'] = $this->_systemVars['appVersion'];
$vars['PAT_LOOPS'] = $this->_templates[$template]['loop'];
switch($this->_templates[$template]['attributes']['addsystemvars']) {
case 'boolean':
$trueValue = 'true';
$falseValue = 'false';
break;
case 'integer':
$trueValue = '1';
$falseValue = '0';
break;
default:
$trueValue = $this->_templates[$template]['attributes']['addsystemvars'];
$falseValue = '';
break;
}
$vars['PAT_IS_ODD'] = ($currentRow % 2 == 1)?$trueValue:$falseValue;
$vars['PAT_IS_EVEN'] = ($currentRow % 2 == 0)?$trueValue:$falseValue;
$vars['PAT_IS_FIRST'] = ($currentRow == 1)?$trueValue:$falseValue;
$vars['PAT_IS_LAST'] = ($currentRow == $this->_templates[$template]['loop'])?$trueValue:$falseValue;
$vars['PAT_ROW_TYPE'] = ($currentRow % 2 == 1)?'odd':'even';
}
$this->_templates[$template]['vars'] = $vars;
return true;
}
function _handleUnusedVars($template) {
$regexp = '/([^\\\])('.$this->_startTag.'[^a-z]+[^\\\]'.$this->_endTag.')/U';
switch($this->_templates[$template]['attributes']['unusedvars']) {
case 'comment':
$this->_templates[$template]['result'] = preg_replace($regexp,'<!-- \\1\\2 -->',$this->_templates[$template]['result']);
break;
case 'strip':
$this->_templates[$template]['result'] = preg_replace($regexp,'\\1',$this->_templates[$template]['result']);
break;
case 'nbsp':
$this->_templates[$template]['result'] = preg_replace($regexp,'\\1&nbsp;',$this->_templates[$template]['result']);
break;
case 'ignore':
break;
default:
$this->_templates[$template]['result'] = preg_replace($regexp,'\\1'.$this->_templates[$template]['attributes']['unusedvars'],$this->_templates[$template]['result']);
break;
}

$regexp = '/[\\\]'.$this->_startTag.'([^a-z]+)[\\\]'.$this->_endTag.'/U';
$this->_templates[$template]['result'] = preg_replace($regexp,$this->_startTag.'\\1'.$this->_endTag,$this->_templates[$template]['result']);
return true;
}
function getParsedTemplate($name = null,$applyFilters = false) {
if(is_null($name)) {
$name = $this->_root;
}
$name = strtolower($name);
$result = $this->parseTemplate($name);
if(patErrorManager::isError($result)) {
return $result;
}
if($applyFilters === false) {
return $this->_templates[$name]['result'];
}
$result = $this->_templates[$name]['result'];
$cnt = count($this->_outputFilters);
for($i = 0; $i < $cnt; $i++) {
$result = $this->_outputFilters[$i]->apply($result);
}
return $result;
}
function displayParsedTemplate($name = null,$applyFilters = true) {
$result = $this->getParsedTemplate($name,$applyFilters);
if(patErrorManager::isError($result)) {
return $result;
}
echo $result;
return true;
}
function parseIntoVar($srcTmpl,$destTmpl,$var,$append = false) {
$srcTmpl = strtolower($srcTmpl);
$destTmpl = strtolower($destTmpl);
$var = strtoupper($var);
$result = $this->parseTemplate($srcTmpl);
if(patErrorManager::isError($result))
return $result;
if($append !== true || !isset($this->_vars[$destTmpl]['scalar'][$var]))
$this->_vars[$destTmpl]['scalar'][$var] = '';
$this->_vars[$destTmpl]['scalar'][$var] .= $this->_templates[$srcTmpl]['result'];
return true;
}
function clearTemplate($name,$recursive = false) {
$name = strtolower($name);
$this->_templates[$name]['parsed'] = false;
$this->_templates[$name]['work'] = '';
$this->_templates[$name]['iteration'] = 0;
$this->_templates[$name]['result'] = '';
$this->_vars[$name] = array('scalar' => array(),'rows' => array());
if(!empty($this->_templates[$name]['defaultVars'])) {
foreach($this->_templates[$name]['defaultVars'] as $varname => $value) {
$this->addVar($name,$varname,$value);
}
}
if($recursive === true) {
$deps = $this->_getDependencies($name);
foreach($deps as $dep) {
$this->clearTemplate($dep,true);
}
}
return true;
}
function clearAllTemplates() {
$templates = array_keys($this->_templates);
$cnt = count($templates);
for($i = 0; $i < $cnt; $i++) {
$this->clearTemplate($templates[$i]);
}
return true;
}
function freeTemplate($name,$recursive = false) {
$name = strtolower($name);
$key = array_search($name,$this->_templateList);
if($key === false) {
return patErrorManager::raiseWarning(PATTEMPLATE_WARNING_NO_TEMPLATE,"Template '$name' does not exist.");
}
unset($this->_templateList[$key]);
$this->_templateList = array_values($this->_templateList);
if($recursive === true) {
$deps = $this->_getDependencies($name);
foreach($deps as $dep) {
$this->freeTemplate($dep,true);
}
}
unset($this->_templates[$name]);
unset($this->_vars[$name]);
if(isset($this->_discoveredPlaceholders[$name])) {
unset($this->_discoveredPlaceholders[$name]);
}
return true;
}
function freeAllTemplates() {
$this->_templates = array();
$this->_vars = array();
$this->_templateList = array();
}
function _getDependencies($template) {
$deps = array();
if(isset($this->_templates[$template]['dependencies']))
$deps = $this->_templates[$template]['dependencies'];
if(isset($this->_templates[$template]['subtemplates'])) {
foreach($this->_templates[$template]['subtemplates'] as $sub) {
if(isset($sub['dependencies']))
$deps = array_merge($deps,$sub['dependencies']);
}
}
$deps = array_unique($deps);
return $deps;
}
function dump($restrict = null,$dumper = 'Html') {
if(is_string($restrict))
$restrict = array($restrict);
$dumper = &$this->loadModule('Dump',$dumper);
if(patErrorManager::isError($dumper)) {
return $dumper;
}
if(is_null($restrict)) {
$templates = $this->_templates;
$vars = $this->_vars;
} else {
$restrict = array_map('strtolower',$restrict);
$templates = array();
$vars = array();
foreach($this->_templates as $name => $spec) {
if(!in_array($name,$restrict))
continue;
$templates[$name] = $spec;
$vars[$name] = $this->_vars[$name];
}
}
$dumper->displayHeader();
$dumper->dumpGlobals($this->_globals);
$dumper->dumpTemplates($templates,$vars);
$dumper->displayFooter();
return true;
}
function getIncludePath() {
return PATTEMPLATE_INCLUDE_PATH;
}
function applyInputFilters($template) {
$cnt = count($this->_inputFilters);
for($i = 0; $i < $cnt; $i++) {
$template = $this->_inputFilters[$i]->apply($template);
}
return $template;
}
function placeholderExists($placeholder,$tmpl,$cached = true) {
$tmpl = strtolower($tmpl);
$placeholder = strtoupper($placeholder);
if(!$this->exists($tmpl)) {
return false;
}
if($cached === true) {
if(isset($this->_discoveredPlaceholders[$tmpl]) && isset($this->_discoveredPlaceholders[$tmpl][$placeholder])) {
return $this->_discoveredPlaceholders[$tmpl][$placeholder];
}
}
if(isset($this->_templates[$tmpl]['subtemplates'])) {
$content = '';
foreach($this->_templates[$tmpl]['subtemplates'] as $temp) {
if(!isset($temp['data'])) {
continue;
}
$content .= $temp['data'];
}
} else {
$content = $this->_templates[$tmpl]['content'];
}
$search = $this->_startTag.$placeholder.$this->_endTag;
if(strstr($content,$search) !== false) {
$this->_discoveredPlaceholders[$tmpl][$placeholder] = true;
return true;
}
$this->_discoveredPlaceholders[$tmpl][$placeholder] = false;
return false;
}
function __toString() {
return $this->getParsedTemplate();
}
}
class patFactory {
function &createTemplate($files = null) {
$mainframe = mosMainFrame::getInstance();;
$tmpl = new patTemplate;

if($GLOBALS['mosConfig_caching']) {
$tmpl->useTemplateCache('File',array('cacheFolder' => $GLOBALS['mosConfig_cachepath'],'lifetime' => 20));
}
$tmpl->setNamespace('mos');

$tmpl->setRoot(dirname(__file__).'/tmpl');
$tmpl->readTemplatesFromFile('page.html');
$tmpl->applyInputFilter('ShortModifiers');
if(is_array($files)) {
foreach($files as $file) {
$tmpl->readTemplatesFromInput($file);
}
}

$tmpl->addGlobalVar('option',$GLOBALS['option']);
$tmpl->addGlobalVar('self',$_SERVER['PHP_SELF']);
$tmpl->addGlobalVar('itemid',$GLOBALS['Itemid']);
$tmpl->addGlobalVar('siteurl',$GLOBALS['mosConfig_live_site']);
$tmpl->addGlobalVar('adminurl',$GLOBALS['mosConfig_live_site'].'/'.ADMINISTRATOR_DIRECTORY);
$tmpl->addGlobalVar('templateurl',$GLOBALS['mosConfig_live_site'].'/templates/'.$mainframe->getTemplate());
$tmpl->addGlobalVar('admintemplateurl',$GLOBALS['mosConfig_live_site'].'/'.ADMINISTRATOR_DIRECTORY.'/templates/'.$mainframe->getTemplate());
$tmpl->addGlobalVar('sitename',$GLOBALS['mosConfig_sitename']);
$tmpl->addGlobalVar('treecss','dtree.css');
$tmpl->addGlobalVar('treeimgfolder','img');
$iso = explode('=',_ISO);
$tmpl->addGlobalVar('page_encoding',$iso[1]);
$tmpl->addGlobalVar('version_copyright',$GLOBALS['_VERSION']->COPYRIGHT);
$tmpl->addGlobalVar('version_url',$GLOBALS['_VERSION']->URL);
$tmpl->addVar('form','formAction',$_SERVER['PHP_SELF']);
$tmpl->addVar('form','formName','adminForm');

$turl = $GLOBALS['mosConfig_live_site'].'/includes/js/tabs/';
$tmpl->addVar('includeTabs','taburl',$turl);
return $tmpl;
}
}
?>