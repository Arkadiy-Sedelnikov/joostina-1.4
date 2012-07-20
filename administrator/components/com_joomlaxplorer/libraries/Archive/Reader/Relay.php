<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

defined('_JLINDEX') or die();
require_once dirname(__file__) . "/../Reader.php";
class File_Archive_Reader_Relay extends File_Archive_Reader{
	var $source;

	function File_Archive_Reader_Relay(&$source){
		$this->source = &$source;
	}

	function next(){
		return $this->source->next();
	}

	function getFilename(){
		return $this->source->getFilename();
	}

	function getStat(){
		return $this->source->getStat();
	}

	function getMime(){
		return $this->source->getMime();
	}

	function getDataFilename(){
		return $this->source->getDataFilename();
	}

	function getData($length = -1){
		return $this->source->getData($length);
	}

	function skip($length = -1){
		return $this->source->skip($length);
	}

	function rewind($length = -1){
		return $this->source->rewind($length);
	}

	function tell(){
		return $this->source->tell();
	}

	function close(){
		if($this->source !== null){
			return $this->source->close();
		}
	}

	function makeAppendWriter(){
		$writer = $this->source->makeAppendWriter();
		if(!PEAR::isError($writer)){
			$this->close();
		}
		return $writer;
	}

	function makeWriterRemoveFiles($pred){
		$writer = $this->source->makeWriterRemoveFiles($pred);
		if(!PEAR::isError($writer)){
			$this->close();
		}
		return $writer;
	}

	function makeWriterRemoveBlocks($blocks, $seek = 0){
		$writer = $this->source->makeWriterRemoveBlocks($blocks, $seek);
		if(!PEAR::isError($writer)){
			$this->close();
		}
		return $writer;
	}
}

?>
