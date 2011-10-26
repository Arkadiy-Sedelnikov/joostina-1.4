<?php
defined( '_VALID_MOS' ) or die( 'Restricted Access.' );
class JCE{
	var $id;
	var $usertype;
	var $username;
	var $plugin;
	var $tiny_url;
	var $tiny_path;
	var $lib_url;
	var $lib_path;
	var $plugin_path;
	var $plugin_url;
	var $language;
	var $request;
	//joostina
	var $params;
	//Constructor
	function JCE(){
		global $my;
		$database = database::getInstance();
		$this->usertype = strtolower( $my->usertype );
		$this->username = $my->username;

		if( !$my->id ){
			$this->id = 0;
		}else{
			$query = "SELECT gid FROM #__users WHERE id = '".$my->id."' LIMIT 1";
			$database->setQuery( $query );
			$this->id = $database->loadResult();
		}
	}
	function getUserName(){
		return $this->username;
	}
	function getUserType(){
		return $this->usertype;
	}
	function isAdmin(){
		return ( $this->usertype == 'superadministrator' || $this->usertype == 'super administrator' || $this->id == 25 ) ? true : false;
	}
	function authCheck( $lvl ){
		return ( $this->isAdmin() || ( intval( $lvl ) != 99 && $this->id >= intval( $lvl ) ) ) ? true : false;
	}
	//Return JCE mambot paramters
	function getParams(){
		$database = database::getInstance();
		$query = "SELECT params FROM #__mambots WHERE element = 'jce' AND folder = 'editors'";
		$database->setQuery( $query );
		$params = $database->loadResult();
		$params = new mosParameters( $params );
		$this->params = $params;
		return $params;
	}
	//Return a list of published plugins
	function getPlugins( $exclude ){
		$database = database::getInstance();
		$query = "SELECT plugin FROM #__jce_plugins WHERE access <= '".$this->id."' AND published = 1 AND type = 'plugin'";
		$database->setQuery( $query );
		$plugins = $database->loadResultArray();
		if( $exclude ){
			foreach( $exclude as $excluded ){
				if( in_array( $excluded, $plugins ) && in_array( $excluded . '_ext', $plugins )){
					unset( $plugins[array_search( $excluded, $plugins )] );
				}
			}
		}
		return implode( ',', $plugins );
	}
	// получение списка установленных языков
	function getLangs(){
		return '';
	}
	//Return a the published language
	function getLanguage(){
		$database = database::getInstance();
		$query = "SELECT lang FROM #__jce_langs WHERE published = 1";
		$database->setQuery( $query );
		$this->language = $database->loadResult();
		return $this->language;
	}
	function getPluginLanguage(){	
		$l = ( $this->language ) ? $this->language : $this->getLanguage();		
		if( file_exists( $this->getPluginPath()  . "/langs/" . $l . ".php" ) ){
			return $l;
		}else{
			return 'ru';
		}
	}
	function removeKey( $array, $key ){
		if( in_array( $key, $array ) ){
			unset( $array[$key] );
		}
	}
	function addKey( $string, $key, $separator ){
		if( $string ){
			$array = explode( $separator, $string );
			$array[] = $key;
			return implode( $separator, $array );
		}else{
			return $key;
		}
	}
	function getBool( $string ){
		return intval( $string ) ? 'true' : 'false';
	}
	function cleanParam( $param ){		
		$search = array ('@<[\/\!]*?[^<>]*?>@si','@/\n|\r|(\r\n)/m[\s]+@','@([\r\n])[\s]+@');
		$replace = array ('','','');

		return preg_replace($search, $replace, $param);
	}
	//Return a string of commands to be removed
	function getRemovePlugins(){
		$database = database::getInstance();
		$query = "SELECT plugin FROM #__jce_plugins WHERE type = 'command' AND published = 0 AND access > '" . $this->id . "'";
		$database->setQuery( $query );
		$remove = $database->loadResultArray();
		if( $remove ){
			return implode( ',', $remove );
		}else{
			return '';
		}
	}
	//Return a an array of buttons for a specified row
	function getRow( $row ){
		static $all_rows;
		if(!is_array($all_rows)){
			$database = database::getInstance();
			$query = "SELECT row,icon FROM #__jce_plugins WHERE access <= '".$this->id."' AND published = 1 AND icon != '' ORDER BY ordering ASC";
			$database->setQuery( $query );
			$r = $database->loadObjectList();
			foreach($r as $all){
				$all_rows[$all->row][]=$all->icon;
			}
		}
		$ret = isset($all_rows[$row]) ? $all_rows[$row] : array();
		return implode( ',', $ret);
	}
	//Return a string of extended elements for a plugin
	function getElements(){
		$database = database::getInstance();
		
		$params = $this->params;
		$jce_elements = explode( ',', $this->cleanParam( $params->get( 'extended_elements', '' ) ) );
		$query = "SELECT elements"
		. "\n FROM #__jce_plugins"
		. "\n WHERE elements != ''"
		. "\n AND published = 1"
		. "\n AND access <= '".$this->id."'"
		;
		$database->setQuery( $query );
		$plugin_elements = $database->loadResultArray();
		$elements = array_merge( $jce_elements, $plugin_elements );
		return implode( ',', $elements );		
	}
	function getPluginParams( $plugin='' ){
		static $al_plugins;

		if( !$plugin ) $plugin = $this->plugin;

		if(!isset($al_plugins)){
			$database = database::getInstance();
			$query = "SELECT plugin,params FROM #__jce_plugins WHERE published = 1 AND params<>''";
			$database->setQuery( $query );
			$al_plugins = $database->loadObjectList('plugin');
		}

		if(isset($al_plugins[$plugin])){
			return new mosParameters( $al_plugins[$plugin]->params );
		} else{
			return new mosParameters( '' );
		}
	}
	//Boolean - is a plugin loaded?
	function isLoaded( $plugin ){
		// интересное место, boston тут сделал большииие глаза и запретил фукнцию :)
		return true;
		$database = database::getInstance();
		$query = "SELECT id FROM #__jce_plugins WHERE plugin = '" . $plugin . "' AND published = 1 LIMIT 1";
		$database->setQuery( $query );
		$id = $database->loadResult();
		if( $id ){
			return true;
		}else{
			return false;
		}
	}
	//Set plugin as current
	function setPlugin( $plugin ){
		$this->plugin = $plugin;
	}
	//Return current plugin
	function getPlugin(){
		return $this->plugin;
	}
	function getAuthOption( $key, $def, $type='bool' ){
		$params = $this->getPluginParams();
		if( $type == 'int' ){
			return $this->authCheck( $params->get( $key, $def ) ) == true ? 1 : 0;
		}
		return $this->authCheck( $params->get( $key, $def ) );
	}
	function getUserPath(){
		$params = $this->getPluginParams();
		$t = $params->get( 'dir_type', 'level' );
		//Default
		$path = JFile::makeSafe( $this->usertype );
		switch( $t ){
			case 'level' :
				$path = JFile::makeSafe( $this->usertype );
				break;
			case 'name' :
				$path = JFile::makeSafe( $this->username );
				break;
			case 'level_name' :
				$path = JPath::makePath( JFile::makeSafe( $this->usertype ), JFile::makeSafe( $this->username ) );
				break;
			case 'folder' :
				$path = $params->get( 'user_folder', '' );
				break;
		}
		return $path;
	}
	function getUserDir( $base_dir ){
		global $mainframe;
		
		$path = $this->getUserPath();
		$folder = JPath::makePath( $base_dir, $path );
		$full = JPath::makePath( $mainframe->getCfg( 'absolute_path' ), $folder );
		if( !JFolder::exists( $full ) ){
			JFolder::createFolder( $full );
		}
		return $folder;
	}
	function getBaseDir( $create ){
		$params = $this->getPluginParams();
		$base_dir = $params->get( 'dir', '/images/stories' );
				
		if( $params->get( 'user_dir', '0' ) && !$this->authCheck( intval( $params->get( 'user_dir_level', '18' ) ) ) ){
			if( $create ){
				$base_dir = $this->getUserDir( $base_dir );
			}else{
				$base_dir = JPath::makePath( $base_dir, $this->getUserPath() );
			}
		}	
		return $base_dir;
	}
	function getTinyUrl(){
		global $mainframe;
		if( !$this->tiny_url ){
			$this->tiny_url =  JPATH_SITE . "/mambots/editors/jce/jscripts/tiny_mce";
		}	
		return $this->tiny_url;
	}
	function getTinyPath(){
		global $mainframe;
		$this->tiny_path = JPATH_BASE . "/mambots/editors/jce/jscripts/tiny_mce";
		return $this->tiny_path;
	}
	function getLibUrl(){
		$this->lib_url = $this->getTinyUrl() . "/libraries";
		return $this->lib_url;
	}
	function getLibPath(){
		$this->lib_path = $this->getTinyPath() . "/libraries";
		return $this->lib_path;
	}
	function getPluginUrl(){
		$this->plugin_url = $this->getTinyUrl() . "/plugins/" .$this->plugin;
		return $this->plugin_url;
	}
	function getPluginPath(){
		$this->plugin_path = $this->getTinyPath() . "/plugins/" .$this->plugin;
		return $this->plugin_path;
	}
	function getPluginFile( $file ){
		return 'index2.php?option=com_jce&no_html=1&task=plugin&plugin=' . $this->plugin . '&file=' . $file;
	}
	function getParamsPath(){
		global $mainframe;
		return JPATH_BASE . '/'.JADMIN_BASE.'/components/com_jce/plugins';
	}
	function printTinyJs( $file ){
		$url = $this->getTinyUrl() . "/" . $file . ".js";
		echo "<script language=\"javascript\" type=\"text/javascript\" src=\"" . $url . "\"></script>\n";
	}
	function printPluginJs( $file ){
		$url = $this->getPluginUrl() . "/jscripts/" . $file . ".js";
		echo "<script language=\"javascript\" type=\"text/javascript\" src=\"" . $url . "\"></script>\n";
	}
	function printLibJs( $file ){
		$url = $this->getLibUrl() . "/jscripts/" . $file . ".js";
		echo "<script language=\"javascript\" type=\"text/javascript\" src=\"" . $url . "\"></script>\n";
	}
	function printPluginCss( $file, $ie=false ){
		$url = $this->getPluginUrl() . "/css/" . $file . ".css";
		echo "<link href=\"" . $url . "\" rel=\"stylesheet\" type=\"text/css\" />\n";
		if( $ie ){
			echo "<!--[if IE 6]><link href=\"" . $this->getPluginUrl() . "/css/" . $file . "_ie6.css\" rel=\"stylesheet\" type=\"text/css\" /><![endif]-->\n";
			echo "<!--[if IE 7]><link href=\"" . $this->getPluginUrl() . "/css/" . $file . "_ie7.css\" rel=\"stylesheet\" type=\"text/css\" /><![endif]-->\n";
		}
	}
	function printLibCss( $file, $ie=false ){
		$url = $this->getLibUrl() . "/css/" . $file . ".css";
		echo "<link href=\"" . $url . "\" rel=\"stylesheet\" type=\"text/css\" />\n";
		if( $ie ){
			echo "<!--[if IE 6]><link href=\"" . $this->getLibUrl() . "/css/" . $file . "_ie6.css\" rel=\"stylesheet\" type=\"text/css\" /><![endif]-->\n";
			echo "<!--[if IE 7]><link href=\"" . $this->getLibUrl() . "/css/" . $file . "_ie7.css\" rel=\"stylesheet\" type=\"text/css\" /><![endif]-->\n";
		}
	}
	function getTinyImg( $image ){
		return $this->getTinyUrl() . "/themes/advanced/images/" . $image;
	}
	function getLibImg( $image ){
		return $this->getLibUrl() . "/images/" . $image;
	}
	function getPluginImg( $image ){
		return $this->getPluginUrl() . "/images/" . $image;
	}
	function getHelpImg( $image ){
		return $this->getPluginUrl() . "/docs/" . $this->getHelpLang() . "/images/" . $image;
	}
	function getFileIcon( $ext ){
		if( JFile::exists( $this->getLibPath() . '/images/icons/' . $ext . '.gif' )){
			return $this->getLibImg( 'icons/' . $ext . '.gif' );
		}elseif( JFile::exists( $this->getPluginPath() . '/images/icons/' . $ext . '.gif' )){
			return $this->getPluginImg( 'icons/' . $ext . '.gif' );
		}else{
			return $this->getLibImg( 'icons/def.gif' );
		}
	}
	function translate( $v, $def='' ){
		global $cl, $pl;
		if( isset( $cl[$v] ) ){
			return $cl[$v];
		}
		if( isset( $pl[$v] ) ){
			return $pl[$v];
		}
		if( $def ){
			return $def;
		}else{
			return $v;
		}
	}
	function sortType(){
		global $cl;
		echo "<div id=\"sortTypeDiv\" onselectstart=\"return false\" class=\"sortDesc\">\n";
		echo "<div class=\"sortLabel\">" . $cl['type'] . "</div>\n";
		echo "</div>\n";	
	}
	function sortName(){
		global $cl;
		echo "<div id=\"sortNameDiv\" onselectstart=\"return false\" class=\"sortDesc\">\n";
		echo "<div class=\"sortLabel\">" . $cl['name'] . "</div>\n";
		echo "</div>\n";	
	}
	function searchDiv(){
		echo "<div id=\"searchDiv\">";
		echo "<div id=\"searchValueLabel\"><img src=\"" . $this->getLibImg('search.gif') . "\" width=\"16\" height=\"16\" alt=\"" . $this->translate('search') . "\" title=\"" . $this->translate('search') . "\" style=\"vertical-align:middle;\" /></div>";
		echo "<input type=\"text\" id=\"searchValue\" style=\"width: 170px;\" ONKEYUP=\"searchFile(this.value);\" />";
		echo "</div>";
	}
	function colorPicker( $name, $def, $func='' ){	
		$html = '<table border="0" cellpadding="0" cellspacing="0">'."\n";
		$html .= 	'<tr>'."\n";
		$html .= 		'<td><input type="text" size="10" id="' . $name . '" name="' . $name . '" value="' . $def . '" onChange="document.getElementById(\'' . $name . '_pick\').style.backgroundColor=this.value;" /></td>'."\n";
		$html .= 		'<td>&nbsp;<img onclick="colorPicker.show(\'' . $name . '\', \'' . $func . '\');" class="colorpicker" style="background-color:' . $def . ';" name="' . $name . '_pick" id="' . $name . '_pick" src="' . $this->getLibImg('color.gif') . '" width="20" height="20" border="0" /></td>'."\n";
		$html .= 	'</tr>'."\n";
		$html .='</table>'."\n";
		return $html;	
	}
	function editTools(){
		global $cl;
		$html = '';
			if( $this->getAuthOption('file_rename', '18') ){
				$html .= '<div class="editIcon" id="renIcon"><a href="javascript:void(0)" id="renLink" class="tools" onClick="renameFile();"><img src="' . $this->getLibImg('rename.gif') . '" alt="' . $cl['rename'] . '" title="' . $cl['rename'] . '" width="20" height="20" /></a></div>'."\n";
			}
			if( $this->getAuthOption('file_delete', '18') ){
				$html .= '<div id="delIcon" class="editIcon"><a href="javascript:void(0)" id="delLink" title="' . $cl['delete'] . '" onClick="deleteFile();" class="tools"><img src="' . $this->getLibImg('delete.gif') . '" height="20" width="20" border="0" alt="' . $cl['delete'] . '" /></a> </div>'."\n";
			}
			if( $this->getAuthOption('file_move', '18') ){
				$html .= '<div id="copyIcon" class="editIcon"><a href="javascript:void(0)" id="copyLink" title="' . $cl['copy'] . '" onClick="copyFile();" class="tools"><img src="' . $this->getLibImg('copy.gif') . '" height="20" width="20" border="0" alt="' . $cl['copy'] . '" /></a></div>'."\n";
				$html .= '<div id="cutIcon" class="editIcon"><a href="javascript:void(0)" id="cutLink" title="' . $cl['cut'] . '" onClick="cutFile();" class="tools"><img src="' . $this->getLibImg('cut.gif') . '" height="20" width="20" border="0" alt="' . $cl['cut'] . '" /></a></div>'."\n";
				$html .= '<div id="pasteIcon" class="editIcon"><a href="javascript:void(0)" id="pasteLink" title="' . $cl['paste'] . '" onClick="pasteFile();" class="tools"><img src="' . $this->getLibImg('paste.gif') . '" height="20" width="20" border="0" alt="' . $cl['paste'] . '" /></a> </div>'."\n";		
			}
			if( $this->getAuthOption('folder_delete', '18') ){
				$html .= '<div id="delDirIcon" class="editIcon"><a href="javascript:void(0)" id="delDirLink" title="' . $cl['delete'] . '" onClick="deleteFolder();" class="tools"><img src="' . $this->getLibImg('delete.gif') . '" height="20" width="20" border="0" alt="' . $cl['delete'] . '" /></a> </div>'."\n";
			}
			if( $this->getAuthOption('folder_rename', '18') ){
				$html .= '<div id="renDirIcon" class="editIcon"><a href="javascript:void(0)" id="renDirLink" title="' . $cl['rename'] . '" onClick="renameFolder();" class="tools"><img src="' . $this->getLibImg('rename.gif') . '" height="20" width="20" border="0" alt="' . $cl['rename'] . '" /></a> </div>'."\n";
			}
		return $html;
	}
	//Ajax functions
	function setAjax( $function ){
		if( is_array( $function ) ){
			$this->request[$function[0]] = array( $function[1], $function[2] ); 
		}else{
			$this->request[$function] = $function;
		}
	}
	function ajaxhtmlentities( $html ){
		global $cl;
		if(function_exists( 'jcehtmlentities' )){
			return jcehtmlentities( $html );
		}else{
			return htmlentities( $html, ENT_QUOTES, $cl['iso'] );
		}
	}
	function ajaxHTML( $html ){
		global $cl;
		$params = $this->getParams();
		if( strtolower( $params->get( 'charset', $cl['iso'] ) ) == 'utf-8' ){
			return addslashes( $html );
		}else{
			$html = addslashes( $this->ajaxhtmlentities( $html ) );
			return str_replace( array( '&lt;', '&gt;' ), array( '<', '>' ), $html );
			//return str_replace( '&', '&#38;', addslashes( $this->ajaxhtmlentities( $html ) ) );
		}
	}
	function processAjax(){
		global $cl;
		$params = $this->getParams();
		header("Content-Type: text/html; charset=". $params->get('charset', $cl['iso']) ."");
		$GLOBALS['ajaxErrorHandlerText'] = "";
		set_error_handler('ajaxErrorHandler');
						
		$fn = mosGetParam( $_POST, 'ajaxfn' );
		$args = mosGetParam( $_POST, 'ajaxargs', '' );
		$txt = '';
		if( $fn ){
			if( array_key_exists( $fn, $this->request ) ){	
				$txt = call_user_func_array( $this->request[$fn], $args );
				if( !empty( $GLOBALS['ajaxErrorHandlerText'] ) ){			
					$txt = "<script>alert('**PHP Error Messages:**" . $this->ajaxHTML( $GLOBALS['ajaxErrorHandlerText'] ) . "');</script>";
				}
			}else{
				$txt = "<script>alert('Cannot call function ". $this->ajaxHTML( $fn ) .". Function not registered!');</script>";
			}
			print $txt;
			exit();
		}
	}
}
function ajaxErrorHandler( $errno, $errstr, $errfile, $errline ){
	$errorReporting = error_reporting();
	if ( ( $errno & $errorReporting ) == 0 ) return;
	
	if ( $errno == E_NOTICE ){
		$errTypeStr = "NOTICE";
	}else if ( $errno == E_WARNING ){
		$errTypeStr = "WARNING";
	}else if ( $errno == E_USER_NOTICE ){
		$errTypeStr = "USER NOTICE";
	}else if ( $errno == E_USER_WARNING ){
		$errTypeStr = "USER WARNING";
	}else if ( $errno == E_USER_ERROR ){
		$errTypeStr = "USER FATAL ERROR";
	}else if ( $errno == E_STRICT ){
		return;
	}else{
		$errTypeStr = "UNKNOWN: $errno";
	}
	$GLOBALS['ajaxErrorHandlerText'] .= "$errTypeStr $errstr Error in line $errline of file $errfile";
}
class Manager{
		//Configuration array.
		var $base_dir;
		var $base_url;
		/**
		 * Get the base directory.
		 * @return string base dir, see config.inc.php
		 */
		function getBaseDir(){
			global $mainframe;
			return JPath::makePath( JPATH_BASE, $this->base_dir );
		}
		/**
		 * Get the base URL.
		 * @return string base url, see config.inc.php
		 */
		function getBaseURL(){
			global $mainframe;
			return JPath::makePath( JPATH_SITE, $this->base_url );
		}
		/**
		 * Get a list of dirs in the base dir
		 * @return array $dirs
		*/
		function getDirs(){
			$list = JFolder::listFolderTree( $this->getBaseDir(), '.' );

			$dirs = array();

			if( $list ){
				foreach( $list as $dir ){
					$dir['relname'] = str_replace( "\\", "/", $dir['relname']);
					$dirs[] = str_replace( $this->base_dir, '', '/'.$dir['relname'] );
				}
			}
			return $dirs;
		}
		function getFiles( $relative, $filter ){
			$path = JPath::makePath( $this->getBaseDir(), $relative );
			$list = JFolder::files( $path, $filter );
			if( !empty( $list ) ){
				for ($i = 0; $i < count( $list ); $i++) {
					$file = $list[$i];
					$fullpath = JPath::makePath( $path, $file );
					$files[] = array(
						'name' => $file,
						'relative' => JPath::makePath( $relative, $file ),
						'ext' => JFile::getExt( $file ),
						'short_name' => JFile::stripExt( $file )
					);
				}
				return $files;
			}else{
				return $list;
			}
		}
		function getFolders( $relative ){
			$path = JPath::makePath( $this->getBaseDir(), $relative );
			$list = JFolder::folders( $path );
			if( !empty( $list ) ){
				for ($i = 0; $i < count( $list ); $i++) {
					$folder = $list[$i];
					$fullpath = JPath::makePath( $path, $folder );
					$folders[] = array(
						'name' => $folder,
						'relative' => JPath::makePath( $relative, $folder )
					);
				}
				return $folders;
			}else{
				return $list;
			}
		}
		function dirTree(){
			$dirs = JFolder::listFolderTree( $this->getBaseDir(), '.' );
			$d = "";
			foreach( $dirs as $tree ){
				$id = $tree['id'];
				$parent = $tree['parent'];
				$name = $tree['name'];
				$relative = str_replace( "\\", "/", $tree['relname']);
				$relative = str_replace( $this->base_dir, '', '/' . $relative );
				
				$did = $id + 1;
				
				$d .= "parent.jce.tree.add('i" . $id . "','i" . $parent . "','" . $name . "', 'javascript:changeDir(\'". $relative ."\')', '". $relative ."');\n";
			}
			return $d;
		}
		function doUpload( $dir, $file, $name, $ext, $max_size, $overwrite ){
			global $cl;
			$error = false;

			$allowable = explode( ',', $ext );
			$fileExt = JFile::getExt( $file['name'] );
			$match = in_array( $fileExt, $allowable );

			if( $file['size'] > $max_size )
			{
				$error = $cl['upload_size_err'];
			}elseif( !$match ){
				$error = $cl['upload_ext_err'];
			}else{
				$path = JPath::makePath( $this->getBaseDir(), $dir );
				$file_path = JPath::makePath( $path, JFile::makeSafe( $name . '.' . $fileExt ) );
				$result = JFile::upload( $file['tmp_name'], $file_path, $overwrite );

				if( !JFile::exists( $result ) ){
					$error = $result;
				}
			}
			return $error;
		}
		/**
		 * Delete the relative file(s).
		 * @param $files the relative path to the file name or comma seperated list of multiple paths.
		 * @return string $error on failure.
		 */
		function deleteFiles( $files ){
			global $cl;
			$error = false;
			$files = explode( ",", $files );
			foreach( $files as $file ){
				$fullpath = JPath::makePath( $this->getBaseDir(), $file );

				if( JFile::exists( $fullpath ) ){
					if( @!JFile::delete( $fullpath ) ){
						$error = $cl['del_file_err'];
					}
				}
			}
		}
		/**
		 * Delete a folder
		 * @param string $relative The relative path of the folder to delete
		 * @return string $error on failure
		 */
		function deleteFolder( $relative ){
			global $cl;
			$error = false;
			$folder = rawurldecode( $relative );
			$folder = JPath::makePath( $this->getBaseDir(), $folder );
			if( JFile::countFiles( $folder, '^[(index.html)]' ) != 0 || JFolder::countDirs( $folder ) != 0 ){
				$error =  $cl['not_empty_err'];
			}else{
				if( @!JFolder::delete( $folder ) ){
					$error = $cl['del_dir_err'];
				}
			}
			return $error;
		}
		/*
		* Rename a file.
		* @param string $src The relative path of the source file
		* @param string $dest The name of the new file
		* @return string $error
		*/
		function renameFile( $src, $dest ){
			global $cl;
			$error = false;

			$src = JPath::makePath( $this->getBaseDir(), $src );

			$dir = dirname( $src );
			$ext = JFile::getExt( $src );

			$dest = JPath::makePath( $dir, $dest.'.'.$ext );
			$error = JFile::rename( $src, $dest );

			return $error;
		}
		/*
		* Rename a file.
		* @param string $src The relative path of the source file
		* @param string $dest The name of the new file
		* @return string $error
		*/
		function renameDir( $src, $dest ){
			global $cl;
			$error = false;

			$src = JPath::makePath( $this->getBaseDir(), $src );

			$dir = dirname( $src );

			$dest = JPath::makePath( $dir, $dest );
			$error = JFolder::rename( $src, $dest );

			return $error;
		}
		/*
		* Copy a file.
		* @param string $files The relative file or comma seperated list of files
		* @param string $dest The relative path of the destination dir
		* @return string $error on failure
		*/
		function copy( $files, $dest_dir ){
			global $cl;
			$error = false;
			
			$files = explode( ",", $files );
			foreach( $files as $file ){
				$filepath = JPath::makePath( $dest_dir, basename( $file ) );
				$src = JPath::makePath( $this->getBaseDir(), $file );
				$dest = JPath::makePath( $this->getBaseDir(), $filepath );
				$error = JFile::copy( $src, $dest );
			}
			return $error;
		}
		/*
		* Copy a file.
		* @param string $files The relative file or comma seperated list of files
		* @param string $dest The relative path of the destination dir
		* @return string $error on failure
		*/
		function move( $files, $dest_dir ){
			global $cl;
			$error = false;

			$files = explode( ',', $files );
			foreach( $files as $file ){
				$filepath = JPath::makePath( $dest_dir, basename( $file ) );
				$src = JPath::makePath( $this->getBaseDir(), $file );
				$dest = JPath::makePath( $this->getBaseDir(), $filepath );
				$error = JFile::rename( $src, $dest );
			}
			return $error;
		}
		/**
		* New folder
		* @param string $dir The base dir
		* @param string $new_dir The folder to be created
		* @return string $error on failure
		*/
		function newFolder( $dir, $new_dir ){
			global $cl;
			$error = false;
			
			$folder = JPath::makePath( $dir, JFile::makeSafe( $new_dir ) );
			$folder = JPath::makePath( $this->getBaseDir(), $folder );
			if( !JFolder::createFolder( $folder ) ){
				$error = $cl['new_dir_err'];
			}
			return $error;
		}
}
?>
