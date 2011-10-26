<?php
/**
* @version $Id: files.php 1111 2005-11-19 00:26:46Z Jinx $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
//Modifed for use with JCE Editor Core and plugins - Ryan Demmer ryandemmer@gmail.com 05/03/2006
//To be removed in migration to Joomla 1.5
/** boolean True if a Windows based host */
define( 'JPATH_ISWIN', (substr(PHP_OS, 0, 3) == 'WIN') );

if (!defined( 'JPATH_ROOT' )) {
	/** string The root directory of the file system in native format */
	define( 'JPATH_ROOT', JPath::clean( JPATH_BASE ) );
}

/**
 * A File handling class
 *
 * @package Joomla
 * @static
 * @since 1.1
 */
class JFile
{
	/**
	* Count the number of files in a given folder
	*/
	function countFiles( $path )
	{
		$total = 0;
		if( JFolder::exists( $path ) ){
			$files = JFolder::files( $path );
			$total = count( $files );
			foreach( $files as $file ){
				if( strtolower( $file ) == 'index.html' || strtolower( $file ) == 'thumbs.db'){
					$total = $total -1;
				}
			}
		}
		return $total;
	}

	/**
	 * Gets the extension of a file name
	 * @param string The file name
	 * @return string
	 */
	function getExt( $file ) {
		$dot = strrpos( $file, '.' ) + 1;
		return strtolower( substr( $file, $dot ) );
	}

	/**
	/**
	 * Strips the last extension off a file name
	 * @param string The file name
	 * @return string
	 */
	function stripExt( $file ) {
		return preg_replace( '#\.[^.]*$#', '', $file );
	}

	/**
	 * Makes file name safe to use
	 * @param string The name of the file (not full path)
	 * @return string The sanitised string
	 */
	function makeSafe( $file ) {
		$search = array('#[^A-Za-z0-9\.\_\-\s]#', '#\s#');
		$replace = array('', '_');
		return strtolower( preg_replace( $search, $replace, $file ) );
	}
	
	function getDate( $file ) {
		return JCEUtils::formatDate( @filemtime( $file ) );
	}
	
	function getSize( $file ) {
		return JCEUtils::formatSize( @filesize( $file ) );
	}

	/**
	* Rename a file
	* @param string The path to the source file
	* @param string The path to the destination file
	* @param string An optional base path to prefix to the file names
	* @return mixed
	*/
   	function copy( $src, $dest ) {

		global $cl;
		$error = false;

		JPath::check( $src );
		JPath::check( $dest );

		if ( JFile::exists( $src ) ) {
			if ( is_writable( dirname( $dest ) ) ) {
				if( !@JFile::exists( $dest ) ){
					if (!@copy( $src, $dest )) {
						$error = $cl['copy_err'];
			  		}
				}else{
					$error = $cl['file_exists_err'];
				}
			}else{
				$error = $cl['dir_write_err'];
			}
		}else{
			$error = $cl['no_source'];
		}
		return $error;
	}

	/**
	* Rename a file
	* @param string The path to the source file
	* @param string The path to the destination file
	* @param string An optional base path to prefix to the file names
	* @return mixed
	*/
	function rename( $src, $dest ) {

		global $cl;
		$error = false;

		JPath::check( $src );
		JPath::check( $dest );

		if ( JFile::exists( $src ) ) {
			if ( is_writable( dirname( $dest ) )) {
				if( !@JFile::exists( $dest ) ){
					if (!@rename( $src, $dest )) {
						$error = $cl['ren_err'];
			  		}
				}else{
					$error = $cl['file_exists_err'];
				}
			}else{
				$error = $cl['dir_write_err'];
			}
		}else{
			$error = $cl['no_source'];
		}
		return $error;
	}

	/**
	 * Delete a file
	 * @param mixed The file name or an array of file names
	 * @return boolean  True on success
	 */
	function delete( $file ) {
		if (is_array( $file )) {
			$files = $file;
		} else {
			$files[] = $file;
		}

		$failed = 0;
		foreach ($files as $file) {
			$file = JPath::clean( $file, false );
			JPath::check( $file );
			$failed |= !unlink( $file );
		}
		return !$failed;
	}

	/**
	 * @param string The full file path
	 * @param string The buffer to read into
	 * @return boolean True on success
	 */
	function read( $file, &$buffer ) {
		JPath::check( $file );

		if (file_exists( $file )) {
			$buffer = file_get_contents( $file );
			return true;
		}
		return false;
	}

	/**
	 * @param string The full file path
	 * @param string The buffer to write
	 * @return mixed The number of bytes on success, false otherwise
	 */
	function write( $file, $buffer ) {
		JPath::check( $file );

		if (!is_writable( $file )) {
			if (!is_writable( dirname( $file ))) {
				return false;
			}
		}
		return file_put_contents( $file, $buffer );
	}

	function createCopy( $dir, $file )
	{
		$filename = basename( $file );
		$ext = JFile::getExt( $filename );
		$base = JFile::stripExt( $filename );
		$filename = $base.'_copy'.'.'.$ext;

		return $filename;
	}
	/*
	* @param string The name of the php (temporary) uploaded file
	* @param string The name of the file to put in the temp directory
	* @param string The message to return
	*/
	function upload( $srcFile, $destFile, $overwrite ) {
		global $cl;
		$error = false;

		//$srcFile = JPath::clean( $srcFile, false );
		//$destFile = JPath::clean( $destFile, false );
		JPath::check( $destFile );

		$baseDir = dirname( $destFile );

		if( !$overwrite ){
			while( JFile::exists( $destFile ) ){
				$destFile = JPath::makePath( $baseDir, JFile::createCopy( $baseDir, $destFile ) );
			}
		}

		//File exists, overwrite & unique are false, return error
		if( JFile::exists( $destFile ) && !$overwrite ){
			$error = $cl['upload_exists_err'];
		}else{
			if( JFolder::exists( $baseDir ) ){
				if ( is_writable( $baseDir ) ){
					if ( move_uploaded_file( $srcFile, $destFile ) ){
   						if ( JPath::setPermissions( $destFile ) ){
							$error = $destFile;
	   					}else{
							$error = $cl['upload_perm_err'];
						}
					}else{
						$error = $cl['upload_err'];
					}
	 			}else{
					$error = $cl['upload_dest_err'];
	 			}
	  		}else{
				$error = $cl['upload_dest_err2'];
	  		}
		}
		return $error;
	}

	/** Wrapper for the standard file_exists function
	 * @param string filename relative to installation dir
	 * @return boolean
	 */
	function exists( $file ) {
   		$file = JPath::clean( $file, false );
		return is_file( $file );
	}
}

/**
 * A Folder handling class
 *
 * @package Joomla
 * @static
 * @since 1.1
 */
class JFolder
{
	/**
	* Count the number of folders in a given folder
	*/
	function countDirs( $path )
	{
		$total = 0;
		if( JFolder::exists( $path ) ){
			$folders = JFolder::folders( $path );
			$total = count( $folders );
		}
		return $total;
	}

	/**
	* @param string A path to create from the base path
	* @param int Directory permissions
	* @return boolean True if successful
	*/
	function create($path = '', $mode = 0755)
	{
		global $mainframe;

		JPath :: check($path);
		$path = JPath :: clean($path, false);

		// Check if dir already exists
		if (JFolder :: exists($path)) {
			return true;
		}
		// First set umask
		$origmask = @ umask(0);

		// We need to get and explode the open_basedir paths
		$obd = ini_get('open_basedir');

		// If open_basedir is et we need to get the open_basedir that the path is in
		if ($obd != null) {
			if (JPATH_ISWIN) {
				$obdSeparator = ";";
			} else {
				$obdSeparator = ":";
			}
			// Create the array of open_basedir paths
			$obdArray = explode($obdSeparator, $obd);
			$inOBD = false;
			// Iterate through open_basedir paths looking for a match
			foreach ($obdArray as $test) {
				if (!(strpos($path, $test) === false)) {
					$obdpath = $test;
					$inOBD = true;
					break;
				}
			}

			if ($inOBD == false) {
			// Return false for JFolder::create because the path to be created is not in open_basedir
				return false;
			}
		}

		// Just to make sure
		$inOBD = true;

		do {
			$dir = $path;

			while (!@ mkdir($dir, $mode)) {
				$dir = dirname($dir);

				if ($obd != null) {
					if (strpos($dir, $obdpath) === false) {
						$inOBD = false;
							break 2;
					}
				}
				if ($dir == '/' || is_dir($dir))
					break;
			}
		}
		while ($dir != $path);

		// Reset umask
		@ umask($origmask);

		// If there is no open_basedir restriction this should always be true
		if ($inOBD == false) {
			// Return false for JFolder::create -- could not create path without violating open_basedir restrictions
			$ret = false;
		} else {
			$ret = true;
		}
		return $ret;
	}
	/**
	 * Delete a folder
	 * @param mixed The folder name
	 * @return boolean True on success
	 */
	function delete( $path ) {
		$path = JPath::clean( $path, false );
		JPath::check( $path );

		// Remove all the files in folder
		$files = JFolder :: files( $path, '.', false, true );
		JFile :: delete( $files );

		// Remove sub-folders of folder
		$folders = JFolder :: folders($path, '.', false, true);
		foreach ( $folders as $folder ) {
			JFolder :: delete( $folder );
		}
		// remove the folders
		return rmdir( $path );
	}
	
	/**
	* Rename a folder
	* @param string The path to the source folder
	* @param string The path to the destination folder
	* @return mixed
	*/
	function rename( $src, $dest ) {

		global $cl;
		$error = false;

		JPath::check( $src );
		JPath::check( $dest );

		if ( JFolder::exists( $src ) ) {
			if ( is_writable( dirname( $dest ) )) {
				if( !@JFolder::exists( $dest ) ){
					if (!@rename( $src, $dest )) {
						$error = $cl['ren_err'];
			  		}
				}else{
					$error = $cl['dir_exists_err'];
				}
			}else{
				$error = $cl['dir_write_err'];
			}
		}else{
			$error = $cl['no_source'];
		}
		return $error;
	}

	/** Wrapper for the standard file_exists function
	 * @param string filename relative to installation dir
	 * @return boolean
	 */
	function exists( $path ) {
   		$path = JPath::clean( $path, false );
		return is_dir( $path );
	}

	/**
	* Utility function to read the files in a directory
	* @param string The file system path
	* @param string A filter for the names
	* @param boolean Recurse search into sub-directories
	* @param boolean True if to prepend the full path to the file name
	* @return array
	*/
	function files( $path, $filter='.', $recurse=false, $fullpath=false  ) {
		$arr = array();
		$path = JPath::clean( $path, false );
		if (!is_dir( $path )) {
			return $arr;
		}

		// prevent snooping of the file system
		//JPath::check( $path );

		// read the source directory
		$handle = opendir( $path );
		$path .= DS;
		while ($file = readdir( $handle )) {
			$dir = $path . $file;
			$isDir = is_dir( $dir );
			if ($file <> '.' && $file <> '..') {
				if ($isDir) {
					if ($recurse) {
						$arr2 = JFolder::files( $dir, $filter, $recurse, $fullpath );
						$arr = array_merge( $arr, $arr2 );
					}
				} else {
					if( preg_match( "/" . $filter . "/", strtolower( $file ) ) ){
						if ($fullpath) {
							$arr[] = $path . $file;
						} else {
							$arr[] = $file;
						}
					}
				}
			}
		}
		closedir( $handle );
		natcasesort( $arr );
		return array_values( $arr );
	}
	/**
	* Utility function to read the folders in a directory
	* @param string The file system path
	* @param string A filter for the names
	* @param boolean Recurse search into sub-directories
	* @param boolean True if to prepend the full path to the file name
	* @return array
	*/
	function folders( $path, $filter='.', $recurse=false, $fullpath=false  ) {
		$arr 	= array();
		$path 	= JPath::clean( $path, false );
		if (!is_dir( $path )) {
			return $arr;
		}

		// prevent snooping of the file system
		//mosFS::check( $path );

		// read the source directory
		$handle = opendir( $path );
		$path 	.= DS;
		while ( $file = readdir( $handle ) ) {
			$dir 	= $path . $file;
			$isDir 	= is_dir( $dir );
			if ( ( $file <> '.' ) && ( $file <> '..' ) && ( $file <> 'jce_cache' ) && $isDir ) {
				// removes CVS directores from list
				if ( preg_match( "/$filter/", $file ) && !( preg_match( "/CVS/", $file ) ) ) {
					if ( $fullpath ) {
						$arr[] = $dir;
					} else {
						$arr[] = $file;
					}
				}
				if ( $recurse ) {
					$arr2 = JFolder::folders( $dir, $filter, $recurse, $fullpath );
					$arr = array_merge( $arr, $arr2 );
				}
			}
		}
		closedir( $handle );
		natcasesort( $arr );
		return array_values( $arr );
	}

	/**
	 * Lists folder in format suitable for tree display
	 */
	function listFolderTree( $path, $filter, $maxLevel=999, $level=0, $parent=0 ) {
		$dirs = array();
		if ($level == 0) {
			$GLOBALS['_JFolder_folder_tree_index'] = 0;
		}

		if ($level < $maxLevel) {
			JPath::check( $path );

			$folders = JFolder::folders( $path, $filter );

			// first path, index foldernames
			for ($i = 0, $n = count( $folders ); $i < $n; $i++) {
				$id = ++$GLOBALS['_JFolder_folder_tree_index'];
				$name = $folders[$i];
				$fullName = JPath::clean( $path . '/' . $name, false );
				$dirs[] = array(
					'id' => $id,
					'parent' => $parent,
					'name' => $name,
					'fullname' => $fullName,
					'relname' => str_replace( JPATH_ROOT, '', $fullName )
				);
				$dirs2 = JFolder::listFolderTree( $fullName, $filter, $maxLevel, $level+1, $id );
				$dirs = array_merge( $dirs, $dirs2 );
			}
		}
		return $dirs;
	}
	/**
	* New folder base function. A wrapper for the JFolder::create function
	* @param string $folder The folder to create
	* @return boolean true on success
	*/
	function createFolder( $folder, $mode=0755 )
	{
		if( @JFolder::create( $folder, $mode ) ){
			$html = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
			$file = $folder."/index.html";
			@JFile::write( $file, $html );
		}else{
			return false;
		}
		return true;
	}
}

/**
 * A Path handling class
 * @package Joomla
 * @since 1.1
 */
class JPath {

	/**
	* Append a / to the path if required.
	* @param string $path the path
	* @return string path with trailing /
	*/
	function fixPath( $path )
	{
		//append a slash to the path if it doesn't exists.
		if( !( substr( $path, -1 ) == '/' ) )
			$path .= '/';
		return $path;
	}
   	/**
	* Concat two paths together. Basically $pathA+$pathB
	* @param string $pathA path one
	* @param string $pathB path two
	* @return string a trailing slash combinded path.
	*/
	function makePath( $pathA, $pathB )
	{
		$pathA = JPath::fixPath( $pathA );
		if( substr( $pathB, 0, 1 )  ==  '/' )
			$pathB = substr( $pathB, 1 );

		return $pathA.$pathB;
	}

	/**
	 * Checks if a files permissions can be changed
	 * @param string The file path
	 * @return boolean
	 */
	function canCHMOD( $file ) {
		$perms = fileperms( $file );
		if ($perms !== false)
			if (@chmod( $file, $perms ^ 0001 ) ) {
				@chmod( $file, $perms );
				return true;
			}
		return false;
	}

	/**
	 * Chmods files and directories recursivly to given permissions
	 *
	 * @param string $path Root path to begin changing mode [without trailing slash]
	 * @param string $filemode Octal representation of the value to change file mode to [null = no change]
	 * @param string $foldermode Octal representation of the value to change folder mode to [null = no change]
	 * @return boolean True if successful [one fail means the whole operation failed]
	 * @since 1.1
	 */
	function setPermissions($path, $filemode = '0644', $foldermode = '0755') {

		// Initialize return value
		$ret = true;

		if (is_dir($path)) {
			$dh = opendir($path);
			while ($file = readdir($dh)) {
				if ($file != '.' && $file != '..') {
					$fullpath = $path.'/'.$file;
					if (is_dir($fullpath)) {
						if (!JPath::setPermissions($fullpath, $filemode, $foldermode)) {
							$ret = false;
						}
					} else {
						if (isset ($filemode)) {
							if (!@ chmod($fullpath, octdec($filemode))) {
								$ret = false;
							}
						}
					} // if
				} // if
			} // while
			closedir($dh);
			if (isset ($foldermode))
				if (!@ chmod($path, octdec($foldermode))) {
					$ret = false;
				}
		} else {
			if (isset ($filemode))
				$ret = @ chmod($path, octdec($filemode));
		} // if
		return $ret;
	}

	/**
	 * Get the permissions of the file/folder at a give path
	 *
	 * @param string $path The path of a file/folder
	 * @return string Filesystem permissions
	 * @since 1.1
	 */
	function getPermissions($path) {
		$path = JPath::clean($path, false);
		JPath::check($path);
		$mode = @ decoct(@ fileperms($path) & 0777);

		if (strlen($mode) < 3) {
			return '---------';
		}
		$parsed_mode = '';
		for ($i = 0; $i < 3; $i ++) {
			// read
			$parsed_mode .= ($mode {
				$i }
			& 04) ? "r" : "-";
			// write
			$parsed_mode .= ($mode {
				$i }
			& 02) ? "w" : "-";
			// execute
			$parsed_mode .= ($mode {
				$i }
			& 01) ? "x" : "-";
		}
		return $parsed_mode;
	}

	/**
	 * Checks for snooping outside of the file system root
	 * @param string A file system path to check
	 */
	function check( $path ) {
		if (strpos( $path, '..' ) !== false) {
			JCEUtils::BackTrace();
			die( 'JPath::check use of relative paths not permitted' ); // don't translate
		}
		if (strpos( JPath::clean($path), JPATH_ROOT ) !== 0) {
			JCEUtils::BackTrace();
			die( 'JPath::check snooping out of bounds @ '.$path ); // don't translate
		}
	}

	/**
	 * Function to strip additional / or \ in a path name
	 * @param string The path
	 * @param boolean Add trailing slash
	 */
	function clean( $p_path, $p_addtrailingslash=true ) {
		$retval = '';
		$path = trim( $p_path );

		if (empty( $p_path )) {
			$retval = JPATH_ROOT;
		} else {
			if (JPATH_ISWIN)	{
				$retval = str_replace( '/', DS, $p_path );
				// Remove double \\
				$retval = str_replace( '\\\\', DS, $retval );
			} else {
				$retval = str_replace( '\\', DS, $p_path );
				// Remove double //
				$retval = str_replace('//',DS,$retval);
			}
		}
		if ($p_addtrailingslash) {
			if (substr( $retval, -1 ) != DS) {
				$retval .= DS;
			}
		}

		return $retval;
	}
}
//Utilities Class
class JCEUtils{	
	/**
	* Format the file size, limits to Mb.
	* @param int $size the raw filesize
	* @return string formated file size.
	*/
	function formatSize( $size ){
		if( $size < 1024 )
			return $size.' bytes';
		else if( $size >= 1024 && $size < 1024*1024 )
			return sprintf('%01.2f',$size/1024.0).' Kb';
		else
			return sprintf( '%01.2f', $size/(1024.0*1024) ).' Mb';
	}
   	/**
  	* Format the date.
	* @param int $date the unix datestamp
	* @return string formated date.
	*/
   	function formatDate( $date ){
		return date( "d/m/Y,H:i", $date );
   	}
   	/**
	 * Format a backtrace error
	 * @since 1.1
	 */
	function BackTrace(){
		if (function_exists( 'debug_backtrace' )) {
			echo '<div align="left">';
			foreach( debug_backtrace() as $back) {
				if (@$back['file']) {
					echo '<br />' . str_replace( JPATH_ROOT, '', $back['file'] ) . ':' . $back['line'];
				}
			}
			echo '</div>';
		}
	}
}
?>
