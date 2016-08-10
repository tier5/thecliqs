<?


/**
 * DB.php
 * 
 */


Class DB {
	var $conn;
	var $lastSql;
    var $dumpSql;
	var $currentDB;
    
    function setDumpSql($dumpSql) {
        $this->dumpSql = $dumpSql;
    }
	
    function getLastSql() {
		return $this->lastSql;
	}
	
	function DB() {
	}
	
	/**
	* Used to check if an identifier is valid --
	* table names, database names, column names, etc.
	*
	* @params	$identifier		The identifier to check
	*/
	function isValidMySqlIdentifier($identifier) {
		$result = TRUE;
		if(preg_match("/^[a-zA-Z_0-9\.]*$/",$identifier)) {
			$result = TRUE;
		}
		return($result);
	}
	
	/**
	* Escape a string of SQL so it is safe to place within a query
	*
	* This function must be called on every untrusted value that is used within a query.
	* It will encode the string by calling mysql_escape_string
	*
	* This will not escape _ and % so it is unsuitable for use in a LIKE query
	*
	* @param	$string		String to be escaped
	*/
	function escapeSql($string) {
		// Escape all characters except % and _
		$escaped_string = mysql_escape_string($string);
		
		return ($escaped_string);
	}
	
	/**
	* Add appropriate quoting for MySQL
	*
	* @param	$string		String to be escaped and quoted
	*/
	function quoteSql($string) {
		$escaped_string = $this->escapeSql($string);
		$quoted_string = "'" . $escaped_string . "'";
		return($quoted_string);
	}
	
	/**
	* Escape a string of SQL so it is safe to use in a LIKE query
	*
	* This function is identical to escapeSQL except it also escapes % and _
	*
	* @param 	$string	String to be escaped
	*/
	function escapeSqlLike($string) {
		$escaped_string = $this->escapeSQL($string);
		
		$escaped_string = str_replace("%","\%",$escaped_string);
		$escaped_string = str_replace("_","\_",$escaped_string);
		
		return ($escaped_string);
	}
	
	
	/**
	* Converts a date in UNIX epoch-seconds format to YYYY-MM-DD HH:MM:SS
	* suitable for using in a DATETTIME field
	*
	* @param	$unixtime	Unix epoch seconds
	*/
	function unixToSqlDate($unixtime) {
		
		// getdate returns the date as an associative array
		$d = getdate($unixtime);
		
		// Assemble the array into something MySQL can understand
		$mysql_date = sprintf("%04d-%02d-%02d %02d:%02d:%02d", $d['year'], $d['mon'], $d['mday'], $d['hours'], $d['minutes'], $d['seconds']);
		
		return($mysql_date);
	}
	
    /**
     * Converts a date in UNIX epoch-seconds format to HH:MM:SS
     * throwing away the date portion
     *
     * @param   $unixtime   Unix epoch seconds
     */
    function unixToSqlTime($unixtime) {
    	// getdate returns the date as an associative array
		$d = getdate($unixtime);
		
		// Assemble the array into something MySQL can understand
		$mysql_time = sprintf("%02d:%02d:%02d", $d['hours'], $d['minutes'], $d['seconds']);
		return($mysql_time);
    }
    
    /**
     * Converts a time in the format HH:MM:SS to seconds after midnight today
     * This is required to incorporate the correct time zone into the time
     */
    function sqlToUnixTime($mysql_time) {
        if (preg_match("/^(\d\d):(\d\d):(\d\d)$/",$mysql_time,$matches)) {
            $hour = $matches[1];
            $minute = $matches[2];
            $second = $matches[3];
            
            $seconds = $second + 60*($minute+60*$hour);
            return mktime($hour,$minute,$second,date("n"),date("d"),date("Y"));
        }
        return FALSE;
    }
    
   /**
	* Converts a date in the form YYYY-MM-DD HH:MM:SS or YYYY-MM-DD into UNIX epoch-seconds
	* If no time is specified using HH:MM:SS, midnight is used
    *
	* Checks that the $mysql_date is a valid date and returns FALSE if invalid
	*
	* @param		$mysql_date 	MySQL-formatted date
	* @returns		UNIX timestamp or FALSE if passed an invalid format
	*/
	function sqlToUnixDate($mysql_date){
		if (preg_match("/^(\d\d\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)$/",$mysql_date,$matches)) {
			$year = $matches[1];
			$mnum = $matches[2];
			$day = $matches[3];
			$hour = $matches[4];
			$minute = $matches[5];
			$second = $matches[6];
			
			$unix_time = mktime($hour,$minute,$second,$mnum,$day,$year);
			return($unix_time);
		} elseif (preg_match("/^(\d\d\d\d)-(\d\d)-(\d\d)$/",$mysql_date,$matches)) {
			$year = $matches[1];
			$mnum = $matches[2];
			$day = $matches[3];
			
			$unix_time = mktime(0,0,0,$mnum,$day,$year);
			return($unix_time);
        }
		return(FALSE);
	}
	

	/** 
	* Set the current MySQL database 
	*
	* @param	$database 	The name of the database to use
	* @returns	void
	*/	
	function set_database($database) {
		//$this->doSQL("USE " . $database);
		mysql_select_db($database);
		$this->currentDB = $database;
	}
	
	
	/** 
	* Connect to a specified database
	* 
	* @param	$host	The host
	* @param	$port	The port
	* @param	$username	The username
	* @param 	$password	The password
	* @param	$database	The specific database to connect to (optional)
	* @returns	a database connection.  Triggers an error if could not connect
	*/
	function connect($host, $port, $username, $password, $database = "") {

		$tries = 10;
		do {
			$conn = @mysql_connect($host . ":" . $port, $username, $password);
			$tries--;
		} while ($conn === FALSE && $tries > 0);
		
		if ($conn === FALSE) {
			$error = mysql_error();
			trigger_error ("DB.php failed to connect to MySQL database: $error",E_USER_ERROR);
		}

		$this->conn = $conn;
		if ($database) {
			$this->set_database($database);
		} 
		
		return($conn);
	}
		
	/**
	* Internal function to execute a query and return a result set.
	*
	* If an error occurs, it will attempt to unlock all tables, and will
	* trigger an error using trigger_error.  This will be displayed to the
	* browser depending on the display_errors parameter in php.ini
	*
	* @param	$query 	The query to execute
	* @returns	The result set, or FALSE if none
	*/
	function mySqlSafeQuery($query) {
		$this->lastSql = $query;
		$result = FALSE;
        if ($this->dumpSql === TRUE) {
            echo "$query<br>";
        }
		$rs = @mysql_query($query, $this->conn);
		$errno = mysql_errno($this->conn);
		if ($errno > 0) {
			$error_text = mysql_error($this->conn);
			@mysql_query("unlock tables");  # Clear any locked tables

			trigger_error($error_text . ": " . $query, E_USER_ERROR);
		} else {
			$result = $rs;
		}
		return $result;
	}
	
	/**
	* Executes a query and returns a result set (recordset)
	*
	* @param $sql		Query to execute
	* @returns 	Result set or FALSE
	*/
	function createRecordset($sql) {
		$rs = $this->mySqlSafeQuery($sql);
		return($rs);
	}
	
	/**
	* Executes a query
	*
	* @param $sql		Query to execute
	* @returns	TRUE or FALSE depending on success of query
	*/
	function doSql($sql) {
		return $this->mySqlSafeQuery($sql)? TRUE : FALSE;
	}
	
	/**
	* Returns the identity of the last insert
	* on the given connection
	*
	*/
	function getIdentity() {
		$identity = mysql_insert_id($this->conn);
		return $identity;
	}
	
	/**
	* Gets the record count of a recordset
	*
	* @params	$rs		the recordset
	*/
	function getRecordCount($rs) {
		return mysql_num_rows($rs);
	}
	
	/**
	* Gets a single field (column) from a row
	* in table $table where column $keys has value $values,
	* also available for $keys and $value specified as arrays
	*
	* @returns	The value or FALSE
	*/
	function getField($table, $keys, $values, $field) {
		$result = FALSE;
		if(!is_array($keys) && !is_array($values)){
			$sql = "SELECT $field FROM $table WHERE $keys = " . $this->quoteSql($values);
		} else {
		    $sql = "SELECT $field FROM $table WHERE ";
			for($i=0; $i < sizeof($keys); $i++){
				if($i > 0){ $sql .= " AND "; }
			    $sql .= $keys[$i]." = ".$this->quoteSql($values[$i]);
			}
		}
		$rs = $this->createRecordset($sql);
		$row = mysql_fetch_array($rs);
		if ($row) {
			$result = $row[$field];
		}
		return $result;
	}
	
	function getRowFields($table, $keys, $values, $field) {
		$result = FALSE;
		if(!is_array($keys) && !is_array($values)){
			$sql = "SELECT $field FROM $table WHERE $keys = " . $this->quoteSql($values);
		} else {
		    $sql = "SELECT $field FROM $table WHERE ";
			for($i=0; $i < sizeof($keys); $i++){
				if($i > 0){ $sql .= " AND "; }
			    $sql .= $keys[$i]." = ".$this->quoteSql($values[$i]);
			}
		}
		$rs = $this->createRecordset($sql);
		$row = mysql_fetch_array($rs);
		if ($row) {
			$result = $row;
		}
		return $result;
	}
	
	/**
	* Gets multiple fields (columns) from table $table
	* If $key is specified, limits the results to rows where
	* $key = $value
	*
	* @returns	A result set or FALSE
	*/
	function getFields($table, $key, $value, $field_names) {
		$field_names = $this->parseFields($field_names);
		$connection = $this->conn;
		$sql = "SELECT " . join(",", $field_names) . " FROM $table WHERE $key = " . $this->quoteSQL($value);
		return $this->createRecordset($sql);
	}
	
	function getRows($table, $key, $value, $field_names) {
		$field_names = $this->parseFields($field_names);
		$connection = $this->conn;
		$sql = "SELECT " . join(",", $field_names) . " FROM $table WHERE $key = " . $this->quoteSQL($value);
		$rs = $this->createRecordset($sql);
		return $this->fetchAll($rs);
	}
	

	/**
	* Updates a single field (column) from a row
	* in table $table where column $key has value $value
	*
	* Note that no quoting is done on $table, $key, or $field,
	* so these must be validated to prevent sql injection
	*
	* @returns 	FALSE if error, or TRUE
	*/
	function updateField($table, $key, $value, $field, $updateValue) {
		$sql = "UPDATE $table SET $field = " . $this->quoteSql($updateValue);
		$sql .= " WHERE $key = " . $this->quoteSQL($value);
		return $this->doSQL($sql);
	}
	
	
	/**
	* Split lists of fields -- so they can be specified conveniently in a string
	*
	* An internal function that takes one parameter, $list
	* If it is an array, returns it as is.  If it is a string,
	* splits it on commas and returns an array
	*
	*/
	function parseFields($list) {
		if (is_array($list)) {
			return($list);
		}
		$list = explode(",",$list);
		for ($i=0; $i<sizeof($list); $i++) {
			$list[$i] = trim($list[$i]);
		}
		return($list);
	}
	
	/**
	* Insert or update fields
	*
	* If a row exists in table $table where column $key = $value, updates the row
	* Otherwise, inserts a new row
	*
	* Note that no quoting is done on $table, $key, or $field_names,
	* so these must be validated to prevent sql injection
	*
	* @returns TRUE or FALSE, if error
	*/
	function insertOrUpdateFields($table, $key, $value, $field_names, $field_values) {
		$rs = $this->getFields($table,$key,$value,$field_names);
		if($this->getRecordCount($rs)>0) {
			return $this->updateFields($table,$key,$value,$field_names,$field_values);
		} else {
			return $this->insertFields($table,$field_names,$field_values);
		}
	}
	
	/**
	* Updates fields in a table
	*
	* Updates fields in table $table where column $key has value $value
	*
	* Note that no quoting is done on $table, $key, or $field_names,
	* so these must be validated to prevent sql injection
	*
	* @param	$table	Table
	* @param	$key	Column to search for $value in
	* @param	$value	Value ot search for
	* @param 	$field_names	A string or array of field names for updating
	* @param	$field_values	An array of field values to store
	* @returns TRUE, or FALSE if error
	*/
	function updateFields($table, $key, $value, $field_names, $field_values) {
		$sql = "UPDATE $table SET ";
		$field_names = $this->parseFields($field_names);
		if (!is_array($field_values)) {
			trigger_error("field_values must be an array",E_USER_ERROR);
		}
		
		for ($i=0; $i<sizeof($field_names); $i++) {
			if ($i > 0) {
				$sql .= ", ";
			}
			$v = $field_values[$i];
			if($v != "NULL"){
				$sql .= $field_names[$i] . " = " . $this->quoteSQL($v);
			}else{
				$sql .= $field_names[$i] . " = " . $v;
			}
		}
		$sql .= " WHERE $key = " . $this->quoteSQL($value);
		return $this->doSQL($sql);
	}
	
	/**
	* Deletes the row(s) from table $table where column $field_name = $field_value
	*
	* Note $table and $field_name are not quoted -- check if using user input!
	*
	* @returns	TRUE, or FALSE if error
	*/
	function deleteRow($table, $field_name, $field_value) {
		if(!is_array($field_name) && !is_array($field_values)){
		    $sql = "DELETE FROM $table WHERE $field_name = " . $this->quoteSQL($field_value);
		}else{
		    $sql = "DELETE FROM $table WHERE ";
			for($i=0; $i < sizeof($field_name); $i++){
				if($i > 0){ $sql .= " AND "; }
			    $sql .= $field_name[$i]." = ".$this->quoteSql($field_value[$i]);
			}
		}
		return $this->doSQL($sql);
	}
	
	/**
	* Inserts fields in a table
	*
	* Inserts fields in table $table where column $key has value $value
	*
	* Note that no quoting is done on $table or $field_names,
	* so these must be validated to prevent sql injection
	*
	* @param	$table	Table
	* @param 	$field_names	A string or array of field names for inserting
	* @param	$field_values	An array of field values to store
	* @returns TRUE, or FALSE if error
	*/
	function insertFields($table, $field_names, $field_values) {
		$sql = "INSERT INTO $table (";
		$valuesClause = "VALUES (";
		$field_names = $this->parseFields($field_names);
		if (!is_array($field_values)) {
			trigger_error("field_values must be an array",E_USER_ERROR);
		}
		
		for ($i = 0; $i<sizeof($field_names); $i++) {
			if ($i > 0) {
				$sql .= ", ";
				$valuesClause .= ", ";
			}
			$sql .= "`".$field_names[$i]."`";
			$value = $field_values[$i];
			if($value != "NULL"){
				$valuesClause .= $this->quoteSQL($value);
			}else{
				$valuesClause .= $value;
			}
		}
		$valuesClause .= ")";
		$sql .= ") " . $valuesClause;
		return $this->doSQL($sql);

	}
	
	/**
	* Skips records
	*
	* @param	$rs 	the recordset
	* @param	$numrecords 	the number of records to skip
	* @returns	the nunmber of records skipped
	*/
	function skipRecords($rs, $numrecords) {
		$skipped = 0;
		while ($numrecords > 0) {
			$row = mysql_fetch_array($rs);
			if (!$row) {
				break;
			}
			$skipped++;
			$numrecords--;
			
		}
		return($skipped);
	}
	
    /** 
     * Reads a column into an array
     */
     function &fetchColumn($rs,$column) {
        $result = array();
        while ($row = mysql_fetch_array($rs)) {
            $value = $row[$column];
            array_push($result,$value);
        }
        return $result;
     }
     
    /**
     * Reads several columns into an array of hashes
     */
    function &fetchColumns($rs,$columns) {
        $columns = $this->parseFields($columns);
        $result = array();
        while ($row = mysql_fetch_array($rs)) {
            $resultRow = array();
            for ($i=0; $i<sizeof($columns); $i++) {
                $resultRow[$columns[$i]] = $row[$i];
            }
            array_push($result,$resultRow);
        }
        return $result;
    }
     
	/** 
	* Reads all records into an array of hashes
	* @param	$rs	the recordset
	* @returns 	an array of arrays containing all the records
	*/
	function &fetchAll($rs) {
		$records = array();

		while ($row = mysql_fetch_array($rs)) {
			array_push($records, $row);
		}
		return $records;
	}
	
	/** 
	 * Returns the current row as a hash
	 */
	function &fetchArray($rs) {
		$row = mysql_fetch_array($rs);
		return $row;
	}
	
	/** 
	 * Returns an array of all the field names in a recordset
	 */
	function getFieldNames($rs) {
		$fieldNames = array();
		while ($field = mysql_fetch_field($rs)) {
			array_push($fieldNames, $field->name);
		}
		return $fieldNames;
	}
	
	/** 
	 * Returns an array of all the field names in a table
	 */
	function getTableFieldNames($table) {
		$fieldNames = array();
		
		$fields = mysql_list_fields($this->currentDB, $table, $this->conn);
		$columns = mysql_num_fields($fields);
		for ($i = 0; $i < $columns; $i++) {
			array_push($fieldNames, mysql_field_name($fields, $i));
		}
		return $fieldNames;
	}
	
	function getFieldNo($query, $field_no){
	    $rs = $this->createRecordset($query);
		$row = mysql_fetch_array($rs);
		return $row[$field_no];
	}
	
	function updateConnectionTable($table, $field, $field_fix, $value_fix, $values){
	    // Delete old values
		$sql            = "SELECT $field FROM $table WHERE $field_fix =".$this->escapeSQL($value_fix);
		$rs_curr_values = $this->createRecordset($sql);
		$curr_values    = $this->fetchColumn($rs_curr_values, $field);
		foreach($curr_values as $cv){
		    if(!in_array($cv, $values)){
			    $this->deleteRow($table, array($field, $field_fix), array($cv, $value_fix));
			}
		}
		
	    // Add new values in table
		foreach($values as $v){
			if(!$this->getField($table, array($field, $field_fix), array($v, $value_fix), $field)){
			    $this->insertFields($table, "$field, $field_fix", array($v, $value_fix));
			}
		}
    }
	
	function UpdateRecordInfos_Insert($table, $field, $value, $person){
		$this->doSql("UPDATE ".$table." SET created_on = UNIX_TIMESTAMP(NOW()) WHERE $field ='$value'");
		$this->doSql("UPDATE ".$table." SET created_by = '".$this->escapeSql($person)."' WHERE $field ='$value'");
	}
	
	function tableExist($table_name){
		$rs = mysql_list_tables($this->currentDB);
		$tables = $this->fetchAll($rs);
		foreach($tables as $row){
			if($row[0] == $table_name){
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	function getAllTables(){
		$rs = mysql_list_tables($this->currentDB);
		$tables = $this->fetchColumn($rs, 0);
		
		return $tables;
	}
	
	function getAllRows($sql){
		$rs = $this->createRecordset($sql);
		return $this->fetchAll($rs);
	}
	function getRow($sql){
		$rs = $this->createRecordset($sql);
		$rows = $this->fetchAll($rs);
		return $rows[0];
	}
	
}





// An error handler that does nothing
function DB_doNothing() {};

?>
