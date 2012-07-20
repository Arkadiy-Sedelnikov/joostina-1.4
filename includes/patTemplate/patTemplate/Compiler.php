<?php
/**
 * @package Joostina
 * @copyright ��������� ����� (C) 2008 Joostina team. ��� ����� ��������.
 * @license �������� http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, ��� help/license.php
 * Joostina! - ��������� ����������� ����������� ���������������� �� �������� �������� GNU/GPL
 * ��� ��������� ���������� � ������������ ����������� � ��������� �� ��������� �����, �������� ���� help/copyright.php.
 * @version        3.1.0
 * @package        patTemplate
 * @author        Stephan Schmidt <schst@php.net>
 * @license        LGPL
 * @link        http://www.php-tools.net
 */
// ������ ������� �������
defined('_JLINDEX') or die();

class patTemplate_Compiler extends patTemplate{
	var $_compiledTemplates = array();
	var $_fp;

	function patTemplate_Compiler($type = 'html'){
		$GLOBALS['patTemplate_Compiler'] = &$this;
		patTemplate::patTemplate($type);
	}

	function compile($compileName = null){
		$this->_varRegexp = '/' . $this->_startTag . '([^a-z:]+)' . $this->_endTag . '/U';
		$this->_depRegexp = '/' . $this->_startTag . 'TMPL:([^a-z:]+)' . $this->_endTag . '/U';
		$compileFolder = $this->getOption('compileFolder');
		$compileFile = sprintf('%s/%s', $compileFolder, $compileName);
		$this->_fp = fopen($compileFile, 'w');
		$this->_addToCode('<?PHP');
		$this->_addToCode('/**');
		$this->_addToCode('* compiled patTemplate file');
		$this->_addToCode('*');
		$this->_addToCode('* compiled on ' . date('Y-m-d H:i:s'));
		$this->_addToCode('*/');
		$this->_addToCode('class compiledTemplate {');
		foreach($this->_templates as $template => $spec){
			$this->compileTemplate($template);
		}
		$this->_addToCode('}');
		$this->_addToCode('?>');
		fclose($this->_fp);
		include_once $compileFile;
		return true;
	}

	function compileTemplate($template){
		$name = strtolower($template);
		if(!isset($this->_templates[$template])){
			return patErrorManager::raiseWarning(PATTEMPLATE_WARNING_NO_TEMPLATE, "Template '$name' does not exist.");
		}
		if($this->_templates[$template]['loaded'] !== true){
			if($this->_templates[$template]['attributes']['parse'] == 'on'){
				$result = $this->readTemplatesFromInput($this->_templates[$template]['attributes']['src'], $this->_templates[$template]['attributes']['reader'], null, $template);
			} else{
				$result = $this->loadTemplateFromInput($this->_templates[$template]['attributes']['src'], $this->_templates[$template]['attributes']['reader'], $template);
			}
			if(patErrorManager::isError($result)){
				return $result;
			}
		}
		$this->_addToCode('');
		$this->_addToCode('/**');
		$this->_addToCode('* Compiled version of ' . $template);
		$this->_addToCode('*');
		$this->_addToCode('* Template type is ' . $this->_templates[$template]['attributes']['type']);
		$this->_addToCode('*/');
		$this->_addToCode('function ' . $template . '()');
		$this->_addToCode('{');
		$this->_addToCode('$this->_prepareCompiledTemplate( "' . $template . '" );', 1);
		$this->_addToCode('$this->prepareTemplate( "' . $template . '" );', 1);
		$this->_addToCode('$this->_templates["' . $template . '"]["attributes"] = unserialize( \'' . serialize($this->_templates[$template]['attributes']) . '\' );', 1, 'Read the attributes');
		$this->_addToCode('$this->_templates["' . $template . '"]["copyVars"] = unserialize( \'' . serialize($this->_templates[$template]['copyVars']) . '\' );', 1, 'Read the copyVars');
		$this->_addToCode('if( $this->_templates["' . $template . '"]["attributes"]["visibility"] != "hidden" ) {', 1, 'Check, whether template is hidden');
		$this->_addToCode('$this->_templates["' . $template . '"]["iteration"] = 0;', 2, 'Reset the iteration');
		$this->_addToCode('$loop = count( $this->_vars["' . $template . '"]["rows"] );', 2, 'Get the amount of loops');
		$this->_addToCode('$loop = max( $loop, 1 );', 2);
		$this->_addToCode('$this->_templates["' . $template . '"]["loop"] = $loop;', 2);
		$this->_addToCode('for( $i = 0; $i < $loop; $i++ ) {', 2, 'Traverse all variables.');
		$this->_addToCode('unset( $this->_templates["' . $template . '"]["vars"] );', 3);
		$this->_addToCode('$this->_fetchVariables("' . $template . '");', 3);
		switch($this->_templates[$template]['attributes']['type']){
			case 'modulo':
				$this->_compileModuloTemplate($template);
				break;
			case 'simplecondition':
				$this->_compileSimpleConditionTemplate($template);
				break;
			case 'condition':
				$this->_compileConditionTemplate($template);
				break;
			default:
				$this->_compileStandardTemplate($template);
				break;
		}
		$this->_addToCode('$this->_templates["' . $template . '"]["iteration"]++;', 3);
		$this->_addToCode('}', 2);
		$this->_addToCode('}', 1);
		$this->_addToCode('}');
		array_push($this->_compiledTemplates, $template);
	}

	function _compileStandardTemplate($template){
		$content = $this->_templateToPHP($this->_templates[$template]['content'], $template);
		$this->_addToCode($content);
		return true;
	}

	function _compileModuloTemplate($template){
		$this->_compileBuiltinConditions($template);
		$this->_addToCode('if( !$_displayed ) {', 3, 'Builtin condition has been displayed?');
		$this->_addToCode('switch( ( $this->_templates["' . $template . '"]["iteration"] + 1 ) % ' . $this->_templates[$template]['attributes']['modulo'] . ' ) {', 4);
		foreach($this->_templates[$template]['subtemplates'] as $condition => $spec){
			$this->_addToCode('case "' . $condition . '":', 5);
			$content = $this->_templateToPHP($spec['data'], $template);
			$this->_addToCode($content);
			$this->_addToCode('break;', 6);
		}
		$this->_addToCode('}', 4);
		$this->_addToCode('}', 3);
		return true;
	}

	function _compileSimpleConditionTemplate($template){
		$conditions = array();
		foreach($this->_templates[$template]['attributes']['requiredvars'] as $var){
			array_push($conditions, 'isset( $this->_templates["' . $template . '"]["vars"]["' . $var . '"] )');
		}
		$this->_addToCode('if( ' . implode(' && ', $conditions) . ' ) {', 3, 'Check for required variables');
		$content = $this->_templateToPHP($this->_templates[$template]['content'], $template);
		$this->_addToCode($content);
		$this->_addToCode('}', 3);
		return true;
	}

	function _compileConditionTemplate($template){
		$this->_compileBuiltinConditions($template);
		$this->_addToCode('if( !$_displayed ) {', 3, 'Builtin condition has been displayed?');
		$this->_addToCode('switch( $this->_templates["' . $template . '"]["vars"]["' . $this->_templates[$template]["attributes"]["conditionvar"] . '"] ) {', 4);
		foreach($this->_templates[$template]['subtemplates'] as $condition => $spec){
			if($condition == '__default'){
				$this->_addToCode('default:', 5);
			} else{
				$this->_addToCode('case "' . $condition . '":', 5);
			}
			$content = $this->_templateToPHP($spec['data'], $template);
			$this->_addToCode($content);
			$this->_addToCode('break;', 6);
		}
		$this->_addToCode('}', 4);
		$this->_addToCode('}', 3);
		return true;
	}

	function _compileBuiltinConditions($template){
		$this->_addToCode('$_displayed = false;', 3);
		if(isset($this->_templates[$template]['subtemplates']['__first'])){
			$this->_addToCode('if( $this->_templates["' . $template . '"]["iteration"] == 0 ) {', 3, 'Check for first entry');
			$content = $this->_templateToPHP($this->_templates[$template]['subtemplates']['__first']['data'], $template);
			$this->_addToCode($content);
			$this->_addToCode('$_displayed = true;', 4);
			$this->_addToCode('}', 3);
		}
		if(isset($this->_templates[$template]['subtemplates']['__last'])){
			$this->_addToCode('if( $this->_templates["' . $template . '"]["iteration"] == ($this->_templates["' . $template . '"]["loop"]-1) ) {', 3, 'Check for last entry');
			$content = $this->_templateToPHP($this->_templates[$template]['subtemplates']['__last']['data'], $template);
			$this->_addToCode($content);
			$this->_addToCode('$_displayed = true;', 4);
			$this->_addToCode('}', 3);
		}
	}

	function _templateToPHP($content, $template){
		$content = preg_replace($this->_varRegexp, '<?PHP echo $this->_getVar( "' . $template . '", "$1"); ?>', $content);
		$content = preg_replace($this->_depRegexp, '<?PHP compiledTemplate::$1(); ?>', $content);
		$content = '?>' . $content . '<?PHP';
		return $content;
	}

	function displayParsedTemplate($name = null){
		if(is_null($name))
			$name = $this->_root;
		$name = strtolower($name);
		if(!is_callable('compiledTemplate', $name)){
			die('Unknown template');
		}
		compiledTemplate::$name();
	}

	function _addToCode($line, $indent = 0, $comment = null){
		if(!is_null($comment)){
			fputs($this->_fp, "\n");
			if($indent > 0)
				fputs($this->_fp, str_repeat("\t", $indent));
			fputs($this->_fp, "/* $comment*/\n");
		}
		if($indent > 0)
			fputs($this->_fp, str_repeat("\t", $indent));
		fputs($this->_fp, $line . "\n");
	}

	function _getVar($template, $varname){
		if(isset($this->_templates[$template]['vars'][$varname]))
			return $this->_templates[$template]['vars'][$varname];
		if(isset($this->_globals[$this->_startTag . $varname . $this->_endTag]))
			return $this->_globals[$this->_startTag . $varname . $this->_endTag];
		return '';
	}

	function _prepareCompiledTemplate($template){
		$this->_templates[$template] = array('attributes' => array(), 'copyVars' => array(),);
	}
}

?>
