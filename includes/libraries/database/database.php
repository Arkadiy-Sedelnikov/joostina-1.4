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
 * Класс работы с базой данных
 * @subpackage Database
 * @package Joostina
 */
class database{

	private static $_instance;
	/**
	@var string Internal variable to hold the query sql*/
	public $_sql;
	/**
	@var int Internal variable to hold the database error number*/
	public $_errorNum = 0;
	/**
	@var string Internal variable to hold the database error message*/
	public $_errorMsg;
	/**
	@var string Internal variable to hold the prefix used on all database tables*/
	public $_table_prefix;
	/**
	@var Internal variable to hold the connector resource*/
	public $_resource;
	/**
	@var Internal variable to hold the last query cursor*/
	public $_cursor;
	/**
	@var boolean Debug option*/
	public $_debug;
	/**
	@var int The limit for the query*/
	public $_limit;
	/**
	@var int The for offset for the limit*/
	public $_offset;
	/**
	@var string The null/zero date string*/
	public $_nullDate = '0000-00-00 00:00:00';
	/**
	@var string Quote for named objects*/
	public $_nameQuote = '`';

	/**
	/**
	 * Database object constructor
	 * @param string Database host
	 * @param string Database user name
	 * @param string Database user password
	 * @param string Database name
	 * @param string Common prefix for all tables
	 * @param boolean If true and there is an error, go offline
	 */
	function database($host = 'localhost', $user = 'root', $pass = '', $db = '', $table_prefix = '', $goOffline = true, $debug = 0, $port = null, $socket = null){
		$this->_debug = $debug;
		$this->_table_prefix = $table_prefix;

		// perform a number of fatality checks, then die gracefully
		if(!function_exists('mysqli_connect')){
			$mosSystemError = 1;
			if($goOffline){
				include JPATH_BASE . '/templates/system/offline.php';
				exit();
			}
		}
		if(!($this->_resource = @mysqli_connect($host, $user, $pass, $db, $port, $socket))){
			$mosSystemError = 2;
			if($goOffline){
				include JPATH_BASE . '/templates/system/offline.php';
				exit();
			}
		}

    	if($this->_debug == 1){
			mysqli_query($this->_resource, 'set profiling=1');
			mysqli_query($this->_resource, 'set profiling_history_size=100');
		}

		mysqli_set_charset($this->_resource, 'utf8');
	}

	/**
	 * Get database Singleton-object. For what is Singleton,
	 * @see http://www.oodesign.com/singleton-pattern.html
	 * @param bool $ignoreMainframe if true, the result will always be
	 * an instance of Database class. If false (default), result will be taken
	 * from $mainframe->getDBO() which means it could be replaced with instance
	 * of other class elsewhere earlier in runtime
	 * @return object
	 */
	public static function getInstance($ignoreMainframe = false){

		// force using DBO from mosMainframe always when it does exist. This is
		// a workaround for calls of database::getInstance() while DBO should be
		// replaced in a runtime (particularly by JoomFish)
		if(!$ignoreMainframe && class_exists('mosMainframe') && mosMainframe::hasInstance()){
			self::$_instance = mosMainFrame::getInstance()->getDBO();
		}

		if(self::$_instance === NULL){
			$config = & Jconfig::getInstance();

			$instance = new database($config->config_host, $config->config_user, $config->config_password, $config->config_db, $config->config_dbprefix, true, $config->config_debug);
			if($instance->getErrorNum()){
				$mosSystemError = $instance->getErrorNum();
				include JPATH_BASE . DS . 'configuration.php';
				include JPATH_BASE . DS . 'templates/system/offline.php';
				exit();
			}
			self::$_instance = $instance;
		}
		return self::$_instance;
	}

	/**
	 * @param int
	 */
	function debug($level){
		$this->_debug = intval($level);
	}

	/**
	 * @return int The error number for the most recent query
	 */
	function getErrorNum(){
		return $this->_errorNum;
	}

	/**
	 * @return string The error message for the most recent query
	 */
	function getErrorMsg(){
		return str_replace(array("\n", "'"), array('\n', "\'"), $this->_errorMsg);
	}

	/**
	 * Get a database escaped string
	 * @return string
	 */
	function getEscaped($text, $extra = false){
		$string = mysqli_real_escape_string($this->_resource, $text);

		if($extra){
			$string = addcslashes($string, '%_');
		}
		return $string;
	}

	/**
	 * Get a quoted database escaped string
	 *
	 * @param	string	A string
	 * @param	boolean	Default true to escape string, false to leave the string unchanged
	 * @return	string
	 * @access public
	 */
	function Quote($text, $escaped = true){
		return '\'' . ($escaped ? $this->getEscaped($text) : $text) . '\'';
	}

	/**
	 * Quote an identifier name (field, table, etc)
	 * @param string The name
	 * @return string The quoted name
	 */
	function NameQuote($s){
		$q = $this->_nameQuote;
		if(strlen($q) == 1){
			return $q . $s . $q;
		} else{
			return $q{0} . $s . $q{1};
		}
	}

	/**
	 * @return string The database prefix
	 */
	function getPrefix(){
		return $this->_table_prefix;
	}

	/**
	 * @return string Quoted null/zero date string
	 */
	function getNullDate(){
		return $this->_nullDate;
	}

	/**
	 * Sets the SQL query string for later execution.
	 *
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>_table_prefix</var> class variable.
	 *
	 * @param string The SQL query
	 * @param string The offset to start selection
	 * @param string The number of results to return
	 * @param string The common table prefix
	 */
	function setQuery($sql, $offset = 0, $limit = 0, $prefix = '#__'){
		$this->_sql = $this->replacePrefix(trim($sql), $prefix);
		$this->_limit = intval($limit);
		$this->_offset = intval($offset);
		return $this;
	}

	/**
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>_table_prefix</var> class variable.
	 *
	 * @param string The SQL query
	 * @param string The common table prefix
	 * @author thede, David McKinnis
	 */
	private function replacePrefix($sql, $prefix = '#__'){
		return str_replace('#__', $this->_table_prefix, $sql);
	}

	public function getResource(){
		return $this->_resource;
	}

	/**
	 * @return string The current value of the internal SQL vairable
	 */
	function getQuery(){
		return '<pre>' . htmlspecialchars($this->_sql) . '</pre>';
	}

	/**
	 * Execute the query
	 * @return mixed A database resource if successful, FALSE if not.
	 */
	function query(){
		if($this->_limit > 0 && $this->_offset == 0){
			$this->_sql .= "\nLIMIT $this->_limit";
		} elseif($this->_limit > 0 || $this->_offset > 0){
			$this->_sql .= "\nLIMIT $this->_offset, $this->_limit";
		}

		$this->_errorNum = 0;
		$this->_errorMsg = '';
		$this->_cursor = mysqli_query($this->_resource, $this->_sql);
		// для оптимизации расхода памяти можно раскомментировать следующие строки, но некоторые особенно кривые расширения сразу же отвалятся
		//unset($this->_sql);
		//return $this->_cursor;
		// /*
		if(!$this->_cursor){
			$this->_errorNum = mysqli_errno($this->_resource);
			$this->_errorMsg = mysqli_error($this->_resource) . " SQL=$this->_sql";
			if($this->_debug){
				$this->show_db_error(mysqli_error($this->_resource), $this->_sql);
			}
			return false;
		}

		// тут тоже раскомментировать, что бу верхнее условие оказалось в комментариях, или еще лучше его вообще удалить
		//*/
		return $this->_cursor;
	}

	/**
	 * @return int The number of affected rows in the previous operation
	 */
	function getAffectedRows(){
		return mysqli_affected_rows($this->_resource);
	}

	function query_batch($abort_on_error = true, $p_transaction_safe = false){
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		if($p_transaction_safe){
			$si = mysqli_get_server_info($this->_resource);
			preg_match_all("/(\d+)\.(\d+)\.(\d+)/i", $si, $m);
			if($m[1] >= 4){
				$this->_sql = 'START TRANSACTION;' . $this->_sql . '; COMMIT;';
			} else if($m[2] >= 23 && $m[3] >= 19){
				$this->_sql = 'BEGIN WORK;' . $this->_sql . '; COMMIT;';
			} else if($m[2] >= 23 && $m[3] >= 17){
				$this->_sql = 'BEGIN;' . $this->_sql . '; COMMIT;';
			}
		}
		$query_split = preg_split("/[;]+/", $this->_sql);
		$error = 0;
		foreach($query_split as $command_line){
			$command_line = trim($command_line);
			if($command_line != ''){
				$this->_cursor = mysqli_query($command_line, $this->_resource);
				if(!$this->_cursor){
					$error = 1;
					$this->_errorNum .= mysqli_errno($this->_resource) . ' ';
					$this->_errorMsg .= mysqli_error($this->_resource) . " SQL=$command_line <br />";
					if($abort_on_error){
						return $this->_cursor;
					}
				}
			}
		}
		return $error ? false : true;
	}

	/**
	 * Diagnostic function
	 */
	function explain(){
		$temp = $this->_sql;
		$this->_sql = 'EXPLAIN ' . $this->_sql;
		$this->query();

		if(!($cur = $this->query())){
			return null;
		}
		$first = true;

		$buf = '<table cellspacing="1" cellpadding="2" border="0" bgcolor="#000000" align="center">';
		$buf .= $this->getQuery();
		while($row = mysqli_fetch_assoc($cur)){
			if($first){
				$buf .= '<tr>';
				foreach($row as $k => $v){
					$buf .= '<th bgcolor="#ffffff">' . $k . '</th>';
				}
				$buf .= '</tr>';
				$first = false;
			}
			$buf .= '<tr>';
			foreach($row as $k => $v){
				$buf .= '<td bgcolor="#ffffff">' . $v . '</td>';
			}
			$buf .= '</tr>';
		}
		$buf .= '</table><br />';
		mysqli_free_result($cur);

		$this->_sql = $temp;

		return '<div style="background-color:#FFFFCC" align="left">' . $buf . '</div>';
	}

	/**
	 * @return int The number of rows returned from the most recent query.
	 */
	function getNumRows($cur = null){
		return mysqli_num_rows($cur ? $cur : $this->_cursor);
	}

	/**
	 * This method loads the first field of the first row returned by the query.
	 *
	 * @return The value returned in the query or null if the query failed.
	 */
	function loadResult(){
		if(!($cur = $this->query())){
			return null;
		}
		$ret = null;
		if($row = mysqli_fetch_row($cur)){
			$ret = $row[0];
		}
		mysqli_free_result($cur);
		return $ret;
	}

	/**
	 * Load an array of single field results into an array
	 */
	function loadResultArray($numinarray = 0){
		if(!($cur = $this->query())){
			return null;
		}
		$array = array();
		while($row = mysqli_fetch_row($cur)){
			$array[] = $row[$numinarray];
		}
		mysqli_free_result($cur);
		return $array;
	}

	/**
	 * Load a assoc list of database rows
	 * @param string The field name of a primary key
	 * @return array If <var>key</var> is empty as sequential list of returned records.
	 */
	function loadAssocList($key = ''){
		if(!($cur = $this->query())){
			return null;
		}
		$array = array();
		while($row = mysqli_fetch_assoc($cur)){
			if($key){
				$array[$row[$key]] = $row;
			} else{
				$array[] = $row;
			}
		}
		mysqli_free_result($cur);
		return $array;
	}

	public function loadAssocRow(){
		if(!($cur = $this->query())){
			return null;
		}
		$row = mysqli_fetch_assoc($cur);
		mysqli_free_result($cur);

		return $row;
	}

	/**
	 * This global function loads the first row of a query into an object
	 *
	 * If an object is passed to this function, the returned row is bound to the existing elements of <var>object</var>.
	 * If <var>object</var> has a value of null, then all of the returned query fields returned in the object.
	 * @param string The SQL query
	 * @param object The address of variable
	 */
	function loadObject(& $object){
		if($object != null){
			if(!($cur = $this->query())){
				return false;
			}
			if($array = mysqli_fetch_assoc($cur)){
				mysqli_free_result($cur);
				mosBindArrayToObject($array, $object, null, null, false);
				return true;
			} else{
				return false;
			}
		} else{
			if($cur = $this->query()){
				if($object = mysqli_fetch_object($cur)){
					mysqli_free_result($cur);
					return true;
				} else{
					$object = null;
					return false;
				}
			} else{
				return false;
			}
		}
	}

	/**
	 * Load a list of database objects
	 * @param string The field name of a primary key
	 * @return array If <var>key</var> is empty as sequential list of returned records.
	 * If <var>key</var> is not empty then the returned array is indexed by the value
	 * the database key.  Returns <var>null</var> if the query fails.
	 */
	function loadObjectList($key = ''){
		if(!($cur = $this->query())){
			return null;
		}
		$array = array();
		while($row = mysqli_fetch_object($cur)){
			if($key){
				$array[$row->$key] = $row;
			} else{
				$array[] = $row;
			}
		}
		mysqli_free_result($cur);
		return $array;
	}

	/**
	 * @return The first row of the query.
	 */
	function loadRow(){
		if(!($cur = $this->query())){
			return null;
		}
		$ret = null;
		if($row = mysqli_fetch_row($cur)){
			$ret = $row;
		}
		mysqli_free_result($cur);
		return $ret;
	}

	/**
	 * Load a list of database rows (numeric column indexing)
	 * @param int Value of the primary key
	 * @return array If <var>key</var> is empty as sequential list of returned records.
	 * If <var>key</var> is not empty then the returned array is indexed by the value
	 * the database key.  Returns <var>null</var> if the query fails.
	 */
	function loadRowList($key = null){
		if(!($cur = $this->query())){
			return null;
		}
		$array = array();
		while($row = mysqli_fetch_row($cur)){
			if(!is_null($key)){
				$array[$row[$key]] = $row;
			} else{
				$array[] = $row;
			}
		}
		mysqli_free_result($cur);
		return $array;
	}

	public function loadRowArray($key, $value){
		if(!($cur = $this->query())){
			return null;
		}
		$array = array();
		while($row = mysqli_fetch_object($cur)){
			$array[$row->$key] = $row->$value;
		}
		mysqli_free_result($cur);

		return $array;
	}

	/**
	 * Document::db_insertObject()
	 * @param string $table This is expected to be a valid (and safe!) table name
	 * @param [type] $keyName
	 * @param [type] $verbose
	 */
	function insertObject($table, $object, $keyName = null, $verbose = false){

		$fmtsql = "INSERT INTO $table ( %s ) VALUES ( %s ) ";

		$fields = array();
		foreach(get_object_vars($object) as $k => $v){
			if(is_array($v) or is_object($v) or $v === null){
				continue;
			}
			if($k[0] == '_'){ // internal field
				continue;
			}
			$fields[] = $this->NameQuote($k);
			$values[] = $this->Quote($v);
		}
		$this->setQuery(sprintf($fmtsql, implode(",", $fields), implode(",", $values)));
		($verbose) && print"$fmtsql<br />\n";
		if(!$this->query()){
			return false;
		}
		$id = mysqli_insert_id($this->_resource);
		($verbose) && print "id=[$id]<br />\n";
		if($keyName && $id){
			$object->$keyName = $id;
		}
		return true;
	}

	/**
	 * Document::db_updateObject()
	 * @param string $table This is expected to be a valid (and safe!) table name
	 * @param [type] $updateNulls
	 */
	function updateObject($table, $object, $keyName, $updateNulls = true){

		$fmtsql = "UPDATE $table SET %s  WHERE %s";
		$tmp = array();
		foreach(get_object_vars($object) as $k => $v){
			if(is_array($v) or is_object($v) or $k[0] == '_'){ // internal or NA field
				continue;
			}
			if($k == $keyName){ // PK not to be updated
				$where = $keyName . '=' . $this->Quote($v);
				continue;
			}
			if($v === null && !$updateNulls){
				continue;
			}
			if($v == ''){
				$val = "''";
			} else{
				$val = $this->Quote($v);
			}
			$tmp[] = $this->NameQuote($k) . '=' . $val;
		}
		$this->setQuery(sprintf($fmtsql, implode(",", $tmp), $where));

		return $this->query();
	}

	/**
	 * @param boolean If TRUE, displays the last SQL statement sent to the database
	 * @return string A standised error message
	 */
	function stderr($showSQL = false){
		return "DB function failed with error number $this->_errorNum <br /><span style=\"color:#ff0000\">$this->_errorMsg</span>" . ($showSQL ? "<br />SQL = <pre>$this->_sql</pre>" : '');
	}

	function insertid(){
		return mysqli_insert_id($this->_resource);
	}

	function getVersion(){
		return mysqli_get_server_info($this->_resource);
	}

	/**
	 * @return array A list of all the tables in the database
	 */
	function getTableList(){
		$this->setQuery('SHOW TABLES');
		return $this->loadResultArray();
	}

	/**
	 * @param array A list of valid (and safe!) table names
	 * @return array A list the create SQL for the tables
	 */
	function getTableCreate($tables){
		$result = array();

		foreach($tables as $tblval){
			$this->setQuery('SHOW CREATE table ' . $this->getEscaped($tblval));
			$rows = $this->loadRowList();
			foreach($rows as $row){
				$result[$tblval] = $row[1];
			}
		}

		return $result;
	}

	/**
	 * @param array A list of valid (and safe!) table names
	 * @return array An array of fields by table
	 */
	function getTableFields($tables){
		$result = array();

		foreach($tables as $tblval){
			$this->setQuery('SHOW FIELDS FROM ' . $tblval);
			$fields = $this->loadObjectList();
			foreach($fields as $field){
				$result[$tblval][$field->Field] = preg_replace("/[(0-9)]/", '', $field->Type);
			}
		}

		return $result;
	}

	function show_db_error($message, $sql = null){
		echo '<div style="display:block;width:100%;"><b>DB::error:</b> ';
		echo $message;
		echo $sql ? '<pre>' . $sql . '</pre><b>UseFiles</b>::' : '';
		if(function_exists('debug_backtrace')){
			foreach(debug_backtrace() as $back){
				if(@$back['file']){
					echo '<br />' . $back['file'] . ':' . $back['line'];
				}
			}
		}
		echo '</div>';
	}

	/**
	 * Fudge method for ADOdb compatibility
	 */
	function GenID(){
		return 0;
	}

	public function getCursor(){
		return $this->_cursor;
	}
}

/**
 * mosDBTable Abstract Class.
 * @abstract
 * @package Joostina
 * @subpackage Database
 *
 * Parent classes to all database derived objects.  Customisation will generally
 * not involve tampering with this object.
 * @author Andrew Eddie <eddieajau@users.sourceforge.net
 */
class mosDBTable{

	public $_tbl;
	public $_tbl_key;
	public $_error;
	public $_db;

	/**
	 *	Object constructor to set table and key field
	 *
	 *	Can be overloaded/supplemented by the child class
	 * @param string $table name of the table in the db schema relating to child class
	 * @param string $key name of the primary key field in the table
	 */
	function mosDBTable($table, $key, $db = null){
		$this->_tbl = $table;
		$this->_tbl_key = $key;
		$this->_db = $db ? $db : database::getInstance();
	}

	/**
	 * Returns an array of public properties
	 * @return array
	 */
	function getPublicProperties(){
		static $cache = null;
		if(is_null($cache)){
			$cache = array();
			foreach(get_class_vars(get_class($this)) as $key => $val){
				if(substr($key, 0, 1) != '_'){
					$cache[] = $key;
				}
			}
		}
		return $cache;
	}

	/**
	 * Filters public properties
	 * @access protected
	 * @param array List of fields to ignore
	 */
	function filter($ignoreList = null){
		$ignore = is_array($ignoreList);

		$iFilter = new InputFilter();
		foreach($this->getPublicProperties() as $k){
			if($ignore && in_array($k, $ignoreList)){
				continue;
			}
			$this->$k = $iFilter->process($this->$k);
		}

	}

	/**
	 * @return string Returns the error message
	 */
	function getError(){
		return $this->_error;
	}

	/**
	 * Gets the value of the class variable
	 * @param string The name of the class variable
	 * @return mixed The value of the class var (or null if no var of that name exists)
	 */
	function get($_property){
		return isset($this->$_property) ? $this->$_property : null;
	}

	/**
	 * Set the value of the class variable
	 * @param string The name of the class variable
	 * @param mixed The value to assign to the variable
	 */
	function set($_property, $_value){
		$this->$_property = $_value;
	}

	/**
	 * Resets public properties
	 * @param mixed The value to set all properties to, default is null
	 */
	function reset($value = null){
		$keys = $this->getPublicProperties();
		foreach($keys as $k){
			$this->$k = $value;
		}
	}

	/**
	 *	binds a named array/hash to this object
	 *
	 *	can be overloaded/supplemented by the child class
	 * @param array $hash named array
	 * @return null|string	null is operation was satisfactory, otherwise returns an error
	 */
	function bind($array, $ignore = ''){
		if(!is_array($array)){
			$this->_error = strtolower(get_class($this)) . '::ошибка выполнения bind.';
			return false;
		} else{
			return mosBindArrayToObject($array, $this, $ignore);
		}
	}

	/**
	 *	binds an array/hash to this object
	 * @param int $oid optional argument, if not specifed then the value of current key is used
	 * @return any result from the database operation
	 */
	function load($oid = null){
		$k = $this->_tbl_key;

		if($oid !== null){
			$this->$k = $oid;
		}

		$oid = $this->$k;

		if($oid === null){
			return false;
		}

		$class_vars = get_class_vars(get_class($this));
		foreach($class_vars as $name => $value){
			if(($name != $k) and ($name != '_db') and ($name != '_tbl') and ($name != '_tbl_key')){
				$this->$name = $value;
			}
		}

		$this->reset();

		$query = 'SELECT * FROM ' . $this->_tbl . ' WHERE ' . $this->_tbl_key . ' = ' . $this->_db->Quote($oid);
		return $this->_db->setQuery($query)->loadObject($this);
	}

	/**
	 *	generic check method
	 *
	 *	can be overloaded/supplemented by the child class
	 * @return boolean True if the object is ok
	 */
	function check(){
		return true;
	}

	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * Can be overloaded/supplemented by the child class
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	function store($updateNulls = false){
		$k = $this->_tbl_key;

		if($this->$k != 0){
			$ret = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
		} else{
			$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
		}

		if(!$ret){
			$this->_error = strtolower(get_class($this)) . "::ошибка выполнения store<br />" . $this->_db->getErrorMsg();
			return false;
		} else{
			return true;
		}
	}

	/**
	 *	Default delete method
	 *
	 *	can be overloaded/supplemented by the child class
	 * @return true if successful otherwise returns and error message
	 */
	function delete($oid = null){
		$k = $this->_tbl_key;

		if($oid){
			$this->$k = intval($oid);
		}

		$query = "DELETE FROM $this->_tbl" . "\n WHERE $this->_tbl_key = " . $this->_db->Quote($this->$k);
		$this->_db->setQuery($query);

		if($this->_db->query()){
			return true;
		} else{
			$this->_error = $this->_db->getErrorMsg();
			return false;
		}
	}

	function delete_array($oid = array(), $key = false, $table = false){
		$key = $key ? $key : $this->_tbl_key;
		$table = $table ? $table : $this->_tbl;

		$query = "DELETE FROM $table WHERE $key IN (" . implode(',', $oid) . ')';

		if($this->_db->setQuery($query)->query()){
			return true;
		} else{
			$this->_error = $this->_db->getErrorMsg();
			return false;
		}
	}

	/**
	 * Generic save function
	 * @param array Source array for binding to class vars
	 * @param string Filter for the order updating. This is expected to be a valid (and safe!) SQL expression
	 * @returns TRUE if completely successful, FALSE if partially or not succesful
	 * NOTE: Filter will be deprecated in verion 1.1
	 */
	function save($source, $order_filter = ''){
		if(!$this->bind($source)){
			return false;
		}
		if(!$this->check()){
			return false;
		}
		if(!$this->store()){
			return false;
		}

		$this->_error = '';
		return true;
	}

	/**
	 * @deprecated As of 1.0.3, replaced by publish
	 */
	function publish_array($cid = null, $publish = 1, $user_id = 0){
		$this->publish($cid, $publish, $user_id);
	}

	/**
	 * Generic Publish/Unpublish function
	 * @param array An array of id numbers
	 * @param integer 0 if unpublishing, 1 if publishing
	 * @param integer The id of the user performnig the operation
	 * @since 1.0.4
	 */
	function publish($cid = null, $publish = 1, $user_id = 0){
		mosArrayToInts($cid, array());
		$user_id = (int)$user_id;
		$publish = (int)$publish;
		if(count($cid) < 1){
			$this->_error = "No items selected.";
			return false;
		}

		$cids = $this->_tbl_key . '=' . implode(' OR ' . $this->_tbl_key . '=', $cid);

		$query = "UPDATE $this->_tbl SET published = " . (int)$publish . " WHERE ($cids) AND (checked_out = 0 OR checked_out = " . (int)$user_id . ")";

		if(!$this->_db->setQuery($query)->query()){
			$this->_error = $this->_db->getErrorMsg();
			return false;
		}

		$this->_error = '';
		return true;
	}


	/**
	 * Checks out an object
	 * @param int User id
	 * @param int Object id
	 */
	function checkout($user_id, $oid = null){
		global $mosConfig_disable_checked_out;
		// отключение блокировок
		if($mosConfig_disable_checked_out)
			return true;
		if(!array_key_exists('checked_out', get_class_vars(strtolower(get_class($this))))){
			$this->_error = "ВНИМАНИЕ: " . strtolower(get_class($this)) . " не поддерживает проверку.";
			return false;
		}
		$k = $this->_tbl_key;
		if($oid !== null){
			$this->$k = $oid;
		}

		$time = date('Y-m-d H:i:s');

		if(intval($user_id)){
			$user_id = intval($user_id);
			// new way of storing editor, by id
			$query = "UPDATE $this->_tbl SET checked_out = $user_id, checked_out_time = " . $this->_db->Quote($time) . " WHERE $this->_tbl_key = " . $this->_db->Quote($this->$k);
			$this->_db->setQuery($query);

			$this->checked_out = $user_id;
			$this->checked_out_time = $time;
		} else{
			$user_id = $this->_db->Quote($user_id);
			// old way of storing editor, by name
			$query = "UPDATE $this->_tbl SET checked_out = 1, checked_out_time = " . $this->_db->Quote($time) . ", editor = $user_id WHERE $this->_tbl_key = " . $this->_db->Quote($this->$k);
			$this->_db->setQuery($query);

			$this->checked_out = 1;
			$this->checked_out_time = $time;
			$this->checked_out_editor = $user_id;
		}

		return $this->_db->query();
	}

	/**
	 * Checks in an object
	 * @param int Object id
	 */
	function checkin($oid = null){
		global $mosConfig_disable_checked_out;
		// отключение блокировок
		if($mosConfig_disable_checked_out)
			return true;
		if(!array_key_exists('checked_out', get_class_vars(strtolower(get_class($this))))){
			$this->_error = "WARNING: " . strtolower(get_class($this)) . " does not support checkin.";
			return false;
		}

		$k = $this->_tbl_key;
		$nullDate = $this->_db->getNullDate();

		if($oid !== null){
			$this->$k = intval($oid);
		}
		if($this->$k == null){
			return false;
		}

		$query = "UPDATE $this->_tbl SET checked_out = 0, checked_out_time = " . $this->_db->Quote($nullDate) . " WHERE $this->_tbl_key = " . $this->_db->Quote($this->$k);
		$this->_db->setQuery($query);

		$this->checked_out = 0;
		$this->checked_out_time = '';

		return $this->_db->query();
	}

	/**
	 * Increments the hit counter for an object
	 * @param int Object id
	 */
	function hit($oid = null){
		global $mosConfig_enable_log_items, $mosConfig_content_hits;

		if(!$mosConfig_content_hits)
			return false;

		$k = $this->_tbl_key;
		if($oid !== null){
			$this->$k = intval($oid);
		}

		$query = "UPDATE $this->_tbl SET hits = ( hits + 1 ) WHERE $this->_tbl_key = " . $this->_db->Quote($this->id);
		$this->_db->setQuery($query)->query();

		if(@$mosConfig_enable_log_items){
			$now = date('Y-m-d');
			$query = "SELECT hits FROM #__core_log_items WHERE time_stamp = " . $this->_db->Quote($now) . " AND item_table = " . $this->_db->Quote($this->_tbl) . " AND item_id = " . $this->_db->Quote($this->$k);
			$this->_db->setQuery($query);
			$hits = intval($this->_db->loadResult());
			if($hits){
				$query = "UPDATE #__core_log_items SET hits = ( hits + 1 ) WHERE time_stamp = " . $this->_db->Quote($now) . " AND item_table = " . $this->_db->Quote($this->_tbl) . " AND item_id = " . $this->_db->Quote($this->$k);
				$this->_db->setQuery($query);
				$this->_db->query();
			} else{
				$query = "INSERT INTO #__core_log_items VALUES ( " . $this->_db->Quote($now) . ", " . $this->_db->Quote($this->_tbl) . ", " . $this->_db->Quote($this->$k) . ", 1 )";
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
	}

	/**
	 * @param string $where This is expected to be a valid (and safe!) SQL expression
	 */
	function move($dirn, $where = ''){
		$k = $this->_tbl_key;

		$sql = "SELECT $this->_tbl_key, ordering FROM $this->_tbl";

		if($dirn < 0){
			$sql .= "\n WHERE ordering < " . (int)$this->ordering;
			$sql .= ($where ? ' AND ' . $where : '');
			$sql .= "\n ORDER BY ordering DESC";
			$sql .= "\n LIMIT 1";
		} else if($dirn > 0){
			$sql .= "\n WHERE ordering > " . (int)$this->ordering;
			$sql .= ($where ? "\n AND $where" : '');
			$sql .= "\n ORDER BY ordering";
			$sql .= "\n LIMIT 1";
		} else{
			$sql .= "\nWHERE ordering = " . (int)$this->ordering;
			$sql .= ($where ? "\n AND $where" : '');
			$sql .= "\n ORDER BY ordering";
			$sql .= "\n LIMIT 1";
		}

		$this->_db->setQuery($sql);

		$row = null;
		if($this->_db->loadObject($row)){
			$query = "UPDATE $this->_tbl SET ordering = " . (int)$row->ordering . " WHERE $this->_tbl_key = " . $this->_db->Quote($this->$k);
			$this->_db->setQuery($query);

			if(!$this->_db->query()){
				$err = $this->_db->getErrorMsg();
				die($err);
			}

			$query = "UPDATE $this->_tbl SET ordering = " . (int)$this->ordering . " WHERE $this->_tbl_key = " . $this->_db->Quote($row->$k);
			$this->_db->setQuery($query);

			if(!$this->_db->query()){
				$err = $this->_db->getErrorMsg();
				die($err);
			}

			$this->ordering = $row->ordering;
		} else{
			$query = "UPDATE $this->_tbl SET ordering = " . (int)$this->ordering . " WHERE $this->_tbl_key = " . $this->_db->Quote($this->$k);
			$this->_db->setQuery($query);
			//echo 'D: ' . $this->_db->getQuery();


			if(!$this->_db->query()){
				$err = $this->_db->getErrorMsg();
				die($err);
			}
		}
	}

	/**
	 * Compacts the ordering sequence of the selected records
	 * @param string Additional where query to limit ordering to a particular subset of records. This is expected to be a valid (and safe!) SQL expression
	 */
	function updateOrder($where = ''){
		$k = $this->_tbl_key;

		if(!array_key_exists('ordering', get_class_vars(strtolower(get_class($this))))){
			$this->_error = "ВНИМАНИЕ: " . strtolower(get_class($this)) . " не поддерживает сортировку.";
			return false;
		}

		$order2 = '';

		$query = "SELECT $this->_tbl_key, ordering" . "\n FROM $this->_tbl" . ($where ? "\n WHERE $where" : '') . "\n ORDER BY ordering$order2 ";
		$this->_db->setQuery($query);
		if(!($orders = $this->_db->loadObjectList())){
			$this->_error = $this->_db->getErrorMsg();
			return false;
		}
		// first pass, compact the ordering numbers
		for($i = 0, $n = count($orders); $i < $n; $i++){
			if($orders[$i]->ordering >= 0){
				$orders[$i]->ordering = $i + 1;
			}
		}

		$shift = 0;
		$n = count($orders);
		for($i = 0; $i < $n; $i++){
			//echo "i=$i id=".$orders[$i]->$k." order=".$orders[$i]->ordering;
			if($orders[$i]->$k == $this->$k){
				// place 'this' record in the desired location
				$orders[$i]->ordering = min($this->ordering, $n);
				$shift = 1;
			} else if($orders[$i]->ordering >= $this->ordering && $this->ordering > 0){
				$orders[$i]->ordering++;
			}
		}
		//echo '<pre>';print_r($orders);echo '</pre>';
		// compact once more until I can find a better algorithm
		for($i = 0, $n = count($orders); $i < $n; $i++){
			if($orders[$i]->ordering >= 0){
				$orders[$i]->ordering = $i + 1;
				$query = "UPDATE $this->_tbl" . "\n SET ordering = " . (int)$orders[$i]->ordering . "\n WHERE $k = " . $this->_db->Quote($orders[$i]->$k);
				$this->_db->setQuery($query);
				$this->_db->query();
				//echo '<br />'.$this->_db->getQuery();
			}
		}

		// if we didn't reorder the current record, make it last
		if($shift == 0){
			$order = $n + 1;
			$query = "UPDATE $this->_tbl" . "\n SET ordering = " . (int)$order . "\n WHERE $k = " . $this->_db->Quote($this->$k);
			$this->_db->setQuery($query);
			$this->_db->query();
			//echo '<br />'.$this->_db->getQuery();
		}
		return true;
	}

	/**
	 * Tests if item is checked out
	 * @param int A user id
	 * @return boolean
	 */
	function isCheckedOut($user_id = 0){
		if($user_id){
			return ($this->checked_out && $this->checked_out != $user_id);
		} else{
			return $this->checked_out;
		}
	}

	//  число записей в таблице по условию
	public function count($where = ''){
		$sql = "SELECT count(*) FROM $this->_tbl " . $where;
		return $this->_db->setQuery($sql)->loadResult();
	}

	// получение списка значений
	public function get_list(array $params = array()){

		$select = isset($params['select']) ? $params['select'] : '*';
		$where = isset($params['where']) ? ' WHERE ' . $params['where'] : '';
		$order = isset($params['order']) ? ' ORDER BY ' . $params['order'] : '';
		$offset = isset($params['offset']) ? intval($params['offset']) : 0;
		$limit = isset($params['limit']) ? intval($params['limit']) : 0;

		return $this->_db->setQuery("SELECT $select FROM $this->_tbl " . $where . $order, $offset, $limit)->loadObjectList();
	}

	// получение списка значений для селектора
	public function get_selector(array $key_val, array $params = array()){

		$key = isset($key_val['key']) ? $key_val['key'] : 'id';
		$value = isset($key_val['value']) ? $key_val['value'] : 'title';

		$select = $key . ',' . $value;
		$where = isset($params['where']) ? 'WHERE ' . $params['where'] : '';
		$order = isset($params['order']) ? 'ORDER BY ' . $params['order'] : '';
		$offset = isset($params['offset']) ? intval($params['offset']) : 0;
		$limit = isset($params['limit']) ? intval($params['limit']) : 0;
		$tablename = isset($params['table']) ? $params['table'] : $this->_tbl;

		$opts = $this->_db->setQuery("SELECT $select FROM $tablename " . $where, $offset, $limit)->loadAssocList();

		$return = array();
		foreach($opts as $opt){
			$return[$opt[$key]] = $opt[$value];
		}

		return $return;
	}

	// отношение один-ко-многим, список выбранных значений из многих
	public function get_select_one_to_many($table_values, $table_keys, $key_parent, $key_children, array $params = array()){

		$select = isset($params['select']) ? $params['select'] : 't_val.*';
		$where = isset($params['where']) ? 'WHERE ' . $params['where'] : "WHERE t_key.$key_parent = $this->id ";
		$order = isset($params['order']) ? 'ORDER BY ' . $params['order'] : '';
		$offset = isset($params['offset']) ? intval($params['offset']) : 0;
		$limit = isset($params['limit']) ? intval($params['limit']) : 0;
		$join = isset($params['join']) ? intval($params['join']) : 'LEFT JOIN';

		$sql = "SELECT $select FROM $table_values AS t_val $join $table_keys AS  t_key ON t_val.id=t_key.$key_children $where ";
		return $this->_db->setQuery($sql, $offset, $limit)->loadAssocList('id');
	}

	// сохранение значение одного ко многим
	public function save_one_to_many($name_table_keys, $key_name, $value_name, $key_value, $values){

		//сначала чистим все предыдущие связи
		$this->_db->setQuery("DELETE FROM $name_table_keys WHERE $key_name=$key_value ")->query();

		$vals = array();
		foreach($values as $value){
			$vals[] = " ($key_value, $value  ) ";
		}

		$values = implode(', ', $vals);

		$sql = "INSERT IGNORE INTO $name_table_keys ( $key_name,$value_name ) VALUES $values";
		return $this->_db->setQuery($sql)->query();
	}

}