<?php 


class DataBaseManager{
	
	/*** Generic member variables ***/
	private static $dsn; // Data source name
	private static $dbUser;
	private static $dbPwd;	
	private static $options = array(
	
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
		//PDO::ATTR_PERSISTENT => true,  
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
		PDO::ATTR_EMULATE_PREPARES => false 
			
	);
	protected static $dbh = null;
	private $executedStmt = null;				
	private $executedSql = '';	
	private static $attemptedSql = '';	
	private static $attemptedSqlValArr = array();	
	private $colEsc = '`';	
	private $colSep = ',';
    private $criticalExecTime = 0.1;
    private $executionTime = 0;
    private $executionMemUsed = 0;
    private $fetchTime = 0;
    private $fetchMemUsed = 0;
    private $recordCount = 0;
    private $selectCount = 0;
	
	
	
	
	/*** Constructor ***/
	public function __construct($dsn, $dbUser, $dbPwd, $options = array()){
		
		self::$dsn = $dsn;
		self::$dbUser = $dbUser;
		self::$dbPwd = $dbPwd;	
		self::$options = array_unique(array_merge(self::$options, $options), SORT_REGULAR);	
		
	}
	
	
	
	
	
	
	/*** Destructor ***/
	public function __destruct(){
	
		$this->closeConn();
	
	}
	
	
	
	
	
	
	/*** Method for initializing database connection ***/	
    private static function _initConn() {
		
        try{
	
           // echo "Opening new connection...";          
            self::$dbh = new PDO(self::$dsn, self::$dbUser, self::$dbPwd, self::$options);
	
        }catch(PDOException $e){
			
			self::getErrorMsg(array('connection' => $e));
	
        }
		
    }
	
	
	
	
	
	/*** Method for fetching database connection ***/
    public static function getConn(){
		
        // initialize $dbh on first call
        if (self::$dbh == null) {
			
            self::_initConn();
			
        }

        // now we should have a $dbh, whether it was initialized on this call or a previous one
        // but it could have experienced a disconnection so we try it
        try{
			
			//////MAKE SQL DATABASE FOLLOW PHP TIMEZONE/////
			$now = new DateTime();
			$mins = ($now->getOffset() / 60);
			$sign = ($mins < 0)? -1 : 1;
			$mins = abs($mins);
			$hrs = floor($mins / 60);
			$mins -= ($hrs * 60);
			$offset = sprintf('%+d:%02d', $hrs*$sign, $mins);
			
            self::$dbh->query("SET time_zone = '".$offset."'");
			
        }catch(PDOException $e){
			
			self::getErrorMsg(array('connection_lost' => $e));
			
        }
		
        return self::$dbh;
		
    }
	
	
	
	
	
	
	
	/*** Method for fetching database name ***/
	public function getDbName(){	
	
		return $this->query("SELECT DATABASE()")->fetchColumn();
		
	}
	
	
	
	
	/*** Method for fetching database connection id ***/
	public function getConnId(){	
	
		return $this->query("SELECT CONNECTION_ID()")->fetchColumn();
		
	}
	
	
	
	
	/*** Method for fetching current version of the database ***/
	public function getVersion(){	
	
		return $this->query("SELECT VERSION()")->fetchColumn();
		
	}
	
	
	
	/*** Method for fetching current database user name and host name ***/
	public function getUser(){	
	
		return $this->query("SELECT COALESCE(CURRENT_USER(), USER(), SESSION_USER(), SYSTEM_USER())")->fetchColumn();
		
	}
	
	
	/*** Method for fetching total counts from a given table ***/
	public function getTableCount($table){	
	
		return $this->query("SELECT COUNT(*) FROM ".$table)->fetchColumn();
		
	}
	
	
	
	
	/*** Method for closing database connection ***/
	public function closeConn(){
	
		self::$dbh = null;
	
	}	
	
	
	
	
	
	
	
	
	/*** Method for handling generic queries ***/
	public function doSecuredQuery($sql, $placeHolderValArr = array(), $buildMetas = false, $buildingResCount = false){
	
		list($buildResCount, $chain, $logQuery, $debugSql) = $this->_processBuildMetas($buildMetas);
		
		$valArr = array();
		
		if($placeHolderValArr){
			
			if(!is_array($placeHolderValArr)){
				
				self::getErrorMsg(array('invalid_data_type' => 'placeholder values must be passed as a one dimensional array'));
				
			}
		
			foreach($placeHolderValArr as $valKey => $val)
				$valArr[$valKey] = $val;				
		
		}
		
		$stmt = $this->_doQuery($sql, $valArr, true, array("origValArr" => $placeHolderValArr, "buildResCount" => $buildResCount, "buildingResCount" => $buildingResCount, "debugSql" => $debugSql));
		
		return $chain? $this : $stmt;
		
	}	
	
	
	
	
	
	
	
	
	
	
	/*** Method for doing queries without prepared statements ***/
	public function query($sql, $buildMetas = false){	
		
		list($buildResCount, $chain, $logQuery, $debugSql) = $this->_processBuildMetas($buildMetas);
		
		$stmt = $this->_doQuery($sql, array(), false, array("buildResCount" => $buildResCount, "debugSql" => $debugSql));
	
		return $chain? $this : $stmt;
		
	}	
	
	
	
	
	
	
	
	/*** Method for handling generic queries ***/
	private function _doQuery($sql, $valArr = array(), $prepStmt = true, $metaArr = array()){
	
		try{

			$buildResCount = isset($metaArr[($K = "buildResCount")])? $metaArr[$K] : false;
			$logQuery = isset($metaArr[($K = "logQuery")])? $metaArr[$K] : false;
			$buildingResCount = isset($metaArr[($K = "buildingResCount")])? $metaArr[$K] : false;
			$origValArr = isset($metaArr[($K = "origValArr")])? $metaArr[$K] : false;
			$debugSql = isset($metaArr[($K = "debugSql")])? $metaArr[$K] : false;						
			$debugSqlCriticalsOnly = false; //MANUALLY ADJUST ACCORDINGLY

			if($logQuery){

				$initLogState = $this->getServerVar(array('n' => "general_log"))["Value"];
				$this->setLogFile();
				$this->setLogState(1);

			}

			if($sql){

				$debugMsg = '<br/>					
				SQL QUERY STRING >>>> <br/>'					
				.$sql.
				'<br/><br/>
				SQL QUERY STRING VALUES >>>> <br/>
				'.var_export($valArr, true).'										
				<br/><br/>
				SQL QUERY STRING ORIGINAL VALUES >>>> <br/>
				'.var_export($origValArr, true).'
				<br/>';
				
				if($debugSql && !$debugSqlCriticalsOnly){

					echo $debugMsg;

				}


				//make sure connection is active///
				self::getConn();
				$executionStartTime = microtime(true);
				$executionStartMem = memory_get_usage();
				
				self::$attemptedSql = $sql;
				self::$attemptedSqlValArr = $valArr;
				
				if($prepStmt){
					
					$stmt = self::$dbh->prepare($sql);
					$stmt->execute($valArr);
					
				}else
					$stmt = self::$dbh->query($sql);
						
				$this->executionTime = microtime(true) - $executionStartTime;
				$this->executionMemUsed = memory_get_usage() - $executionStartMem;
				
				if(!$buildingResCount){
					
					$this->executedStmt = $stmt;
					$this->executedSql = $sql;
					
					// Make sure that if requested, every query has its total record/result count built
					$buildResCount? $this->_buildRecordCount($sql, $valArr) : ($this->recordCount = $this->selectCount = 0);
					
				}
				
				if($executionErr = $this->getExecutionError())
					self::getErrorMsg(array('execution' => $executionErr));

					
				if($logQuery){
						
					$this->setLogState($initLogState); //return to initial logging state
					
				}

				if($debugSqlCriticalsOnly && $this->executionTime >= $this->criticalExecTime){

					echo $debugMsg.'<br/>					
					EXECUTION TIME >>>> <br/>'.$this->executionTime.
					'<br/><br/>
					EXECUTION MEMORY CONSUMED >>>> <br/>'.$this->executionMemUsed;

				}
				
				return $stmt;
					
				
			}else
				self::getErrorMsg(array('null_sql' => ''));
			
		}catch(PDOException $e){
			
			self::getErrorMsg(array('execution' => $e));
			
		}	
		
		
	}	
	
		
	
	
	

	
	
	
	/*** 
	
		Method for processing query result building metas and return behavior
		@param $buildMetas: 
		when a boolean argument is passed it builds the result count
		when a string argument is passed it controls other behaviors
		
	***/
    private function _processBuildMetas($buildMetas){
		
		$chain = $logQuery = $debugSql = false;
		
		if(is_bool($buildMetas))
			$buildResCount = $buildMetas;
		
		else{
			
			$buildResCount = false;

			$buildMetasArr = explode('::', $buildMetas);
			$buildMetasCmd = isset($buildMetasArr[0])? $buildMetasArr[0] : '';
			$buildMetasCmd2 = isset($buildMetasArr[1])? $buildMetasArr[1] : '';

			switch(strtolower($buildMetasCmd2)){

				case 'debug': $debugSql = true; break;

			}
			
			switch(strtolower($buildMetasCmd)){
				
				case 'chain': $chain = true; break; // Allow chaining on the object method
				
				case 'chain_count': $chain = $buildResCount = true; break; // Allow chaining on the object method and also build result count
				
				case 'log': $logQuery = true; break; // log the query to the log file
				
				case 'chain_log': $chain = $logQuery = true; break; // Allow chaining on the object method and also log the query

				case 'chain_count_log': $chain = $buildResCount = $logQuery = true; break; // Allow chaining on the object method, query log, result count build							
				
			}
			
		}
		
		return array($buildResCount, $chain, $logQuery, $debugSql);
		
	}
	
	

	
	
	/*** Method for building result row count ***/
    private function _buildRecordCount($sql, $valArr = array()){
		
		/*
			Since we cannot explicitly run SELECT COUNT(*) on an INSERT, UPDATE AND DELETE statements
			we check for them and use the PDO default rowCount() 
		
		*/
		
		$sqlMain = $sql;
		$valArrMain = $valArr;
		$crudKeyword = strtolower(strchr(trim($sql), ' ', true));
		
		if(in_array($crudKeyword, array('insert', 'update', 'delete'))){
			
			$this->recordCount = $this->getRowCount(); 
			
		}else{
			
			if(preg_match('/(.*)?LIMIT/is', $sql, $match))
				$sql = $match[1];

			$sql = 'SELECT '.sprintf('(SELECT COUNT(*) FROM (%s) _tmp1) AS RECORD_COUNT', $sql).'
					, (SELECT COUNT(*) FROM ('.$sqlMain.') _tmp2) AS SELECT_COUNT';
			
			if(($placeholders = mb_substr_count($sql, '?')) < count($valArr))
				$valArr = array_slice($valArr, 0, $placeholders);
			
			$valArr = array_merge($valArr, $valArrMain);
			
			$stmt = $this->doSecuredQuery($sql, $valArr, true, true);
			$row = $this->fetchRow($stmt);
			$this->recordCount = $row["RECORD_COUNT"];
			$this->selectCount = $row["SELECT_COUNT"];
			
		}
	
    }

	
	
	
	

	
	
	/*** Method for protecting against false database query result 
	 * 	due to a two way paramater query structure
	 * 
	 * 	E.g imagine the given function below
	 * 
	 * 	function fetchResult($twoWayparam){
	 * 
			$sql = "SELECT FIELD FROM table WHERE (ID=? OR FIELD_NAME=?) LIMIT 1"
			$valArr = array($twoWayparam, $twoWayparam);
			$res = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();
	 * 		return $res;
	 * 
	 * }
	 * 
	 * ID is meant to be of type Integer and FIELD_NAME of type String
	 * The given function parameter @param => $twoWayparam can hold two way values
	 * (integer || string), imagine if $twoWayparam is assigned a string value with 
	 * a leading digit => "9ja"
	 * the function will return undesirable false result where (ID = 9 OR FIELD_NAME = "9ja")
	 * 
	 * hence we created this following two method to securely prevent the use of leading digits in a string value
	 * as partial values for query strings
	 * 
	 * Method 1 uses the SQL LIKE operator for comparison instead of EQUAL ( = ) operator
	 *  to enforce it directly on the database field/column
	 * 
	 * While Method 2 enforces it directly on the field/column comparison value
	 * ***/

    public function useStrLeadDigitIdField($fieldName = 'ID', $fieldValue = '?', $fieldOperator = ''){

		return $fieldName.' LIKE '.$fieldValue;

	}

    public function secureStrLeadDigitParam($param){
		
		$startIndex = 0; 
		$acc='';
		
		if(preg_match("#[0-9]+[A-Z]+#i", $param)){
	
			while(is_numeric($leadStr = substr($param, $startIndex, 1))){

				$startIndex++;
				$acc .= $leadStr;

			}
			
			if(strlen($param) != strlen($acc))
				$param = '';

		}		
		
		return $param;
	
	}
	
	

	
	
	/*** Method for binding parameter values ***/
    public function bindValues($paramsArr = array()){
		
		if($stmt = $this->executedStmt){
			
			$paramsArr = array_values($paramsArr);
			
			foreach($paramsArr as $i => $value){
		
				$varType = is_null($value)? PDO::PARAM_NULL : (is_bool($value)? PDO::PARAM_BOOL : (is_int($value)? PDO::PARAM_INT : PDO::PARAM_STR));

				if(!$stmt->bindValue(++$i, $value, $varType))
					self::getErrorMsg(array('binding' => ''));
		
			}

			return true;
			
		}else
			self::getErrorMsg();
	
    }

	
	
	
	
	
	
	/*** Method for starting query transaction ***/
    public function beginTransaction() {
		
        return self::getConn()->beginTransaction();
		
    }
	
	
	
	
	
	
	/*** Method for ending and committing query transaction ***/
    public function endTransaction() {
		
        return self::getConn()->commit();
		
    }
	
	
	
	
	
	
	/*** Method for canceling and rolling back query transaction ***/
    public function cancelTransaction() {
		
        return self::getConn()->rollBack();
		
    }
	
	
	
	
	
	
	/*** 
	
	Method for locking tables:
	
	@param $tableLockString 
	a string of the table name followed by the lock type READ | WRITE
	the variable may be comma separated for multiple tables
	
	**/
    public function lockTables($tableLockString = '') {
		
		if($tableLockString)
			$this->query("LOCK TABLES ".$tableLockString);
		
    }
	
	
	
	
	/*** Method for unlocking tables ***/
    public function unlockTables() {
		
        $this->query("UNLOCK TABLES");
		
    }
	
	
	
	
	
	
	
	
	/*** Method for fetching the first column from a single row result set ***/
	public function fetchColumn($stmt = null){
		
		is_null($stmt)? ($stmt = $this->executedStmt) : '';
		return $stmt->fetchColumn();
		
	}
	
	
	
	
	
	
	/*** Method for fetching queries result sets one row at a time ***/
	public function fetchRow($stmt = null, $how = "assoc"){
		
		return $this->_fetch($how, $stmt);
		
	}
	
	
	
	
	
	
	
	
	/*** Method for fetching all queried result sets at a ago ***/
	public function fetchRows($fetchAllUnpack = false, $how = "assoc", $stmt = null){
		
		return $this->_fetch($how, $stmt, true, $fetchAllUnpack);
	
	}
	
	
	
	
	
	
	/*** Method for handling generic fetches ***/
	private function _fetch($how, $stmt = null, $fetchAll = false, $fetchAllUnpack = false){
		
		is_null($stmt)? ($stmt = $this->executedStmt) : '';
		
		if($stmt){
			
			$how = $this->_getFetchHow($how);
			
			$fetchStartTime = microtime(true);
			$fetchStartMem = memory_get_usage();
			
			$resultSet = $fetchAll? $stmt->fetchAll($how) : $stmt->fetch($how);
			($fetchAll && $fetchAllUnpack && isset($resultSet[0]))? (list($resultSet) = $resultSet) : '';
			
			$this->fetchTime = microtime(true) - $fetchStartTime;
			$this->fetchMemUsed = memory_get_usage() - $fetchStartMem;
			
			return $resultSet;
			
		}else
			self::getErrorMsg();	
		
	}
	
	
	
	
	
	
	
	/*** Method for returning PDO $how to fetch result ***/
	private function _getFetchHow($how){
		
		switch(strtolower(trim($how))){
			
			case "num": $how = PDO::FETCH_NUM; break;
			
			case "both": $how = PDO::FETCH_BOTH; break;
			
			case "obj": $how = PDO::FETCH_OBJ; break;
			
			case "assoc":
			default: $how = PDO::FETCH_ASSOC;
			
		}
	
		return $how;
	
	}
	
	
	
	
	/* Getter method for fetching maximum number of rows returned per database select query */
	public function getMaxRowPerSelect(){
		
		return 100;
		
	}
	
	
	
	
	
	/*** Method for fetching selected record count (limited by the LIMIT clause) ***/	
	public function getSelectCount(){

		return $this->selectCount;
		
	}
	
	
	
	/*** Method for fetching query string record count(total count in database ignoring LIMIT clause) ***/	
	public function getRecordCount(){
		
		return $this->recordCount;
		
	}
	
	
	
	
	/*** Method for fetching query record counts(row counts in the current query possibly limited by LIMIT clause) ***/	
	public function getRowCount($stmt = null){
		
		is_null($stmt)? ($stmt = $this->executedStmt) : '';
		
		if($stmt){
			
			return $stmt->rowCount();
			
		}else
			self::getErrorMsg();
		
	}
	
	
	
	/*** Method for fetching column counts ***/	
	public function getColumnCount($stmt = null){
		
		is_null($stmt)? ($stmt = $this->executedStmt) : '';
		
		if($stmt){
			
			return $stmt->columnCount();
			
		}else
			self::getErrorMsg();
		
	}
	
	
	
	
	
	
	/*** Method for fetching last inserted record id ***/
	public function getLastInsertId(){
		
		return self::$dbh->lastInsertId();
		
	}
	
	
	
	
	
	/*** Method for fetching last executed PDO statement ***/
	public function getLastExecutedStmt(){	
	
		return $this->executedStmt;
		
	}
	
	
	
	
	/*** Method for fetching last executed query string ***/
	public function getLastQueryString(){	
	
		return $this->executedSql;
		
	}
	
	
	
	
	
	/*** Method for fetching mysql variables and their states ***/
	public function getServerVar($metaArr = array(), $print = false){
		
		$name = isset($metaArr[($K = "n")])? $metaArr[$K] : '';
		$val = isset($metaArr[($K = "v")])? $metaArr[$K] : '';

		$filterCnd = ''; 
		$valArr = array();

		if($name){

			$filterCnd = ' VARIABLE_NAME = ?';
			$valArr[] = $name;

		}

		if($val){

			$filterCnd = ($filterCnd? $filterCnd.' AND ' : '').' VALUE = ?';
			$valArr[] = $val;

		}
		
		$filterCnd? ($filterCnd = "WHERE ".$filterCnd) : '';

		$list = $this->doSecuredQuery("SHOW VARIABLES ".$filterCnd, $valArr, 'chain')->fetchRow();

		if($print)
			print_r($list);
		else
			return $list;
		
	}
	
	
	
	
	/*** Method for enabling/disabling mysql log ***/
	public function setLogState($val = 0){	
		
		$this->doSecuredQuery("SET GLOBAL GENERAL_LOG = ?", array($val));

	}
	
	

	
	/*** Method for setting mysql log File ***/
	public function setLogFile($file = ''){	
		
		!$file? ($file = strtoupper($this->getDbName())) : '';

		if($file)
			$this->doSecuredQuery("SET GLOBAL GENERAL_LOG_FILE = ?", array(rtrim($file, '.log').'.log'));

	}
	
	
	
	
	
	
	
	
	
	/*** Method for fetching query execution time ***/
    public function getExecutionTime($numberFormat = false, $decPoint = '.', $thousandsSep = ','){
		
        if(is_numeric($numberFormat))
            return number_format($this->executionTime, $numberFormat, $decPoint, $thousandsSep);
        
        return $this->executionTime;
		
    }
	
	
	
	/*** Method for fetching execution memory used ***/
    public function getExecutionMemUsed($numberFormat = false, $decPoint = '.', $thousandsSep = ','){
		
        if (is_numeric($numberFormat))
            return number_format($this->executionMemUsed, $numberFormat, $decPoint, $thousandsSep);
        
        return $this->executionMemUsed;
		
    }
		
		
	

	
	
	
	
	
	/*** Method for returning result fetch time ***/
    public function getFetchTime($numberFormat = false, $decPoint = '.', $thousandsSep = ','){
		
        if (is_numeric($numberFormat))
            return number_format($this->fetchTime, $numberFormat, $decPoint, $thousandsSep);
        
        return $this->fetchTime;
		
    }
	
	
	
	/*** Method for fetching result fetch time ***/
    public function getFetchMemUsed($numberFormat = false, $decPoint = '.', $thousandsSep = ','){
		
        if (is_numeric($numberFormat))
            return number_format($this->fetchMemUsed, $numberFormat, $decPoint, $thousandsSep);
        
        return $this->fetchMemUsed;
		
    }
		
	
		
		
	
	
	
	
	/*** Method for escaping string values ***/	
	public function escapeStrVal($valStr){

		return '"'.$valStr.'"';

	}


	
	
	/*** Method for escaping fields/columns ***/	
	public function escapeField($colArr, $colAliasArr = [''], $retArr = false){
		
		$resArr=array();	
		
		!is_array($colArr)? ($colArr = (array)$colArr) : '';	
		!is_array($colAliasArr)? ($colAliasArr = (array)$colAliasArr) : '';		
			
		if(!empty($colArr)){
			
			$colCount = count($colArr);
			
			for($i=0; $i < $colCount; $i++){
				
				$alias = isset($colAliasArr[$i])? $colAliasArr[$i] : current($colAliasArr);	
				$col = $colArr[$i];
				
				if($col){
					
					// Handle cases when column name is prefixed by table name
					$tmp = explode('.', $col);
					$col = (isset($tmp[0]) && $tmp[0] && isset($tmp[1]) && $tmp[1])? $tmp[1] : $tmp[0];
						
					$col = $this->_escapeField($col);
					$alias = $this->_escapeField($alias);
							
					$resArr[] = ' '.$col.' '.($alias? ' AS '.$alias : '');
					
				}				
			}
									
		}
		
		if($retArr)
			return $resArr;
		
		return implode($this->colSep, $resArr);
		
	}
	
	
	
	
	
	
	
	
	/*** Method for escaping a field or col ***/	
	private function _escapeField($f){	
	
		if($f && !$this->_isFieldEscaped($f))
			$f = $this->colEsc.$f.$this->colEsc;
		
		return $f;
		
	}
	
	
	
	
	
	
	/*** Method for checking if a field is escaped ***/	
	private function _isFieldEscaped($f){	
	
		$colEsc = $this->colEsc;
		$f = trim($f);
		$opEsc = substr($f, 0, 1);
		$clEsc = substr($f, -1, 1);
		
		if(($colEsc == $opEsc && $colEsc == $clEsc) || $f == '*')
			return true;
		
		return false;
		
	}
	
	
	
	
		
	
	
	
	
	/*** Method for dumping debug parameters ***/
	public function debugDumpParams($echo = true){
		
		$debugParams = ($stmt = $this->executedStmt)? $stmt->debugDumpParams() : 'debugDumpParams: No statement has been executed yet';
		
		if($echo)
			echo $debugParams;

		else
			return $debugParams;
	
	}
		
	
	
	
	
	
	
	/*** Method for fetching query execution errors ***/
    public function getExecutionError($stmt = null, $errorIndex = 2) {
		
		is_null($stmt)? ($stmt = $this->executedStmt) : '';
		
		if($stmt){
			
			$executionError = $stmt->errorInfo();

			if(isset($executionError[$errorIndex]))
				return 'Driver Specific Error Message: '.$executionError[2].
						'<br/>Driver Specific Error Code: '.$executionError[1].
						'<br/>SQLSTATE Error Code: '.$executionError[0];

			return '';
			
		}else
			self::getErrorMsg();
		
    }
	
	
	
	
	
	/*** Method for fetching/throwing error messages ***/
	public static function getErrorMsg($errType = array('null_stmt' => ''), $throwException = true){	
		
		$prefix = 'DATABASE MANAGER CLASS: ';
		$suffix = '<br/>ATTEMPTED SQL: '.self::$attemptedSql.'<br/>ATTEMPTED SQL VALUES: '.var_export(self::$attemptedSqlValArr, true);
		
		foreach($errType as $errKey => $e){
			
			$exceptionMsg = ' => '.(is_object($e)? $e->getMessage().' '.$e->getCode() : $e).$suffix;
			
			switch(strtolower($errKey)){
				
				case 'connection_lost': $errMsg = $prefix.'The database connection was lost'.$exceptionMsg; break;
				
				case 'connection': $errMsg = $prefix.'The database could not establish a connection'.$exceptionMsg; break;
				
				case 'execution': $errMsg = $prefix.'The database query execution failed'.$exceptionMsg; break;
			
				case 'binding': $errMsg = $prefix.'The database value binding failed'.$exceptionMsg; break;
				
				case 'invalid_data_type': $errMsg = $prefix.'The data type specified is invalid'.$exceptionMsg; break;
				
				case 'null_sql': $errMsg = $prefix.'No database query string was specified'.$exceptionMsg; break;
				
				default: $errMsg = $prefix.'Please pass an executed PDO statement object or resource'.$exceptionMsg;
				
			}
			
		}
		
		if($throwException){
			
			throw new Exception($errMsg);
			return false;
			
		}
			
		return $errMsg;
		
	}		
	
	
	
}


?>