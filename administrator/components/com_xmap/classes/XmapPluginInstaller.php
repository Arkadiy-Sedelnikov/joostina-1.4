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

require_once (JPATH_BASE . '/includes/domit/xml_domit_lite_parser.php');

/**
 * Plugin installer
 * @package Xmap
 */
class XmapPluginInstaller extends mosInstaller{
	/**
	 * Custom install method
	 * @param boolean True if installing from directory
	 */
	function install($p_fromdir = null){
		$database = database::getInstance();

		if(!$this->preInstallCheck($p_fromdir, 'xmap_ext')){
			return false;
		}

		$xmlDoc = $this->xmlDoc();
		$mosinstall =& $xmlDoc->documentElement;

		// Set some vars
		$e = &$mosinstall->getElementsByPath('name', 1);
		$this->elementName($e->getText());
		if(!is_null($e)){
			if($e->getAttribute('published') == '1'){
				$published = 1;
			} else{
				$published = 0;
			}
		} else{
			$published = 0;
		}

		$this->elementDir(mosPathName(JPATH_BASE . '/' . JADMIN_BASE . '/components/com_xmap/extensions/'));

		if($this->parseFiles('files', 'xmap_ext', 'No file is marked as extension file') === false){
			return false;
		}

		$this->parseFiles('images');

		// Insert extension in DB
		$query = "SELECT id FROM #__xmap_ext"
			. "\n WHERE extension = " . $database->Quote($this->elementSpecial());
		$database->setQuery($query);
		if(!$database->query()){
			$this->setError(1, 'SQL error: ' . $database->stderr(true));
			return false;
		}

		$id = $database->loadResult();

		if(!$id){
			// Insert extension in DB
			$query = "SELECT id FROM #__xmap_ext"
				. "\n WHERE extension = " . $database->Quote($this->elementSpecial() . '.bak');
			$database->setQuery($query);
			if(!$database->query()){
				$this->setError(1, 'SQL error: ' . $database->stderr(true));
				return false;
			}
			$id = $database->loadResult();

			require_once(JPATH_BASE . '/' . JADMIN_BASE . '/components/com_xmap/classes/XmapPlugin.php');

			$row = new XmapPlugin($database, $id);
			$row->published = $published;
			if(!$id){
				$row->params = '';
			}
			$row->extension = $this->elementSpecial();
			$row->store();

		} else{
			$this->setError(1, 'Plugin "' . $this->elementName() . '" already exists!');
			return false;
		}
		if($e = &$mosinstall->getElementsByPath('description', 1)){
			$this->setError(0, $this->elementName() . '<p>' . $e->getText() . '</p>');
		}

		return $this->copySetupFile('front');
	}

	/**
	 * Custom install method
	 * @param int The id of the extension
	 */
	function uninstall($clientID, $id){
		$database = database::getInstance();

		$id = intval($id);

		$row = new XmapPlugin($database, $id);

		$basepath = JPATH_BASE . DS . JADMIN_BASE . '/components/com_xmap/extensions/';

		$xmlfile = $basepath . $row->extension . '.xml';

		// see if there is an xml install file, must be same name as element
		if(file_exists($xmlfile)){
			$this->i_xmldoc = new DOMIT_Lite_Document();
			$this->i_xmldoc->resolveErrors(true);

			if($this->i_xmldoc->loadXML($xmlfile, false, true)){
				$mosinstall =& $this->i_xmldoc->documentElement;
				// get the files element
				$files_element =& $mosinstall->getElementsByPath('files', 1);
				if(!is_null($files_element)){
					$files = $files_element->childNodes;
					foreach($files as $file){
						// delete the files
						$filename = $file->getText();
						if(file_exists($basepath . $filename)){
							$parts = pathinfo($filename);
							$subpath = $parts['dirname'];
							if($subpath != '' && $subpath != '.' && $subpath != '..'){
								$result = deldir(mosPathName($basepath . $subpath . '/'));
							} else{
								$result = unlink(mosPathName($basepath . $filename, false));
							}
						}
					}

					// remove XML file from front
					@unlink(mosPathName($xmlfile, false));
					$row->extension = $row->extension . '.bak';
					if(!$row->store()){
						$msg = $database->stderr;
						die($msg);
					}
					return true;
				}
			}
		}

	}
}