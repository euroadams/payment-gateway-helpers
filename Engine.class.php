<?php

class Engine{
	
	private static $hrefUrlTxtSep = ':|';
	private static $urlProtocolArr = array('http', 'https', 'ftp', 'file', 'mailto', 'tel', 'sms');

	/*** Constructor ***/
	public function __construct(){
		
		
	}
	
	
	
	/*** Destructor ***/
	public function __destruct(){
		
		
	}
	

	
	/***
	 Method for checking is we are in production or development environment
	***/

	public static function is_dev_env(){

		return (isset($_SERVER[$K="HTTP_HOST"]) && preg_match('#\.test$#', $_SERVER[$K]));
		
	}
	
	
	
	/*** 
	
		Method for rendering content safely to html page from storage source;
		protects against cross site scripting (XSS) attacks 
		prevents contents loaded from a storage such as a database from breaking html page
	
	***/
	public function htmlProtectedRendering($content){
		
		return htmlentities($content, ENT_QUOTES|ENT_HTML5, 'UTF-8');
		
	}

	

	/*** 
	
		Method for stripping single or double quotation mark off a string
	
	***/
	public function strip_quotes($content){
		
		return trim($content, '\'"');
		
	}
	


	public function jsonify($content, $escSpecChars = true){
		
		/***
		 htmlspecialchars only accept strings in the first parameter; 
		 hence wen convert array contents to string objects first before encoding

		***/
		is_array($content)? ($content = json_encode($content)) : ''; 
		$content = json_decode(json_encode($content), true);
		return ($escSpecChars? htmlspecialchars($content, ENT_QUOTES, 'UTF-8') : $content);

	}


	
	
	
	
	
	/*** Method for matching all nested matches in regex replace ***/
	public function match_nested_regex($regex, $replace, $str, $meta=''){
		
		while(preg_match($regex, $str)){
			
			$str = preg_replace($regex, $replace, $str);
			
		}
		
		return $str;
		
	}




	
	
	
	/*** Method for escaping strings using a number of backslashes ***/
	public function escape_with_backslash($str, $escCounts = 1){
		
		$acc = '';
		
		if($str){
			
			$tmpArr = str_split($str, 1);
			$esc = str_repeat('\\', $escCounts);
			$acc = $esc.implode($esc, $tmpArr);
			
		}
		
		return $acc;
	}
	






	
	
	/*** Method for mimicking time delayed data capturing ***/
	public function data_capture_delayed($key, $pageOnView, $delay, $set = false){
		
		$status = false;
		$timerKey = $key.'_TIMER';
		
		if(!isset($_SESSION[$key]) || $set){
			
			$_SESSION[$key] = $pageOnView;
			$_SESSION[$timerKey] = (time() + $delay);
			
		}else{
			
			if(strtolower($_SESSION[$key]) == strtolower($pageOnView))
				$status = (time() >= $_SESSION[$timerKey]);
			
			else
				$this->data_capture_delayed($key, $pageOnView, $delay, true);
			
		}
		
		return $status;
		
	}
	
	
	
	
	
	
	
	
	/*** Method for fetching session cookie parameters ***/
	public function get_cookie_params($ret_val_arr = true){
		
		$ckp = session_get_cookie_params();
		
		if($ret_val_arr)
		 return array($ckp['lifetime'], $ckp['path'], $ckp['domain'], $ckp['secure'], $ckp['httponly']);
	 
		return $ckp;
		
	}
	
	
	
	
	
	
	
	/*** Method for setting cookie ***/
	public function set_cookie($k, $v = '', $xp = 0, $path = '/', $domain = '', $secure = false, $httpOnly = true){
		
		$done = false;
		
		if($k){
			
			list($lifeTime, $path, $domain, $secure, $httpOnly) = $this->get_cookie_params();
			
			$xpr = ($xp > 1)? time() + $xp : $xp;
			$done = $xp? setcookie($k, $v, $xpr, $path, $domain, $secure, $httpOnly) : setcookie($k, $v, false, $path, $domain, $secure, $httpOnly);
			
		}
		
		return $done;
		
	}
	
	
	
	
	
	
	
	/*** Method for setting time zone ***/
	public function set_time_zone($tz = 'Africa/Lagos'){
		
		$tz = $tz? $tz : 'Africa/Lagos';
		$done = date_default_timezone_set($tz);
		ini_set('date.timezone', $tz);
		return $done;

	}
	
	
	
	
	/*** Method for splitting time string ***/
	public function split_time_str($timeStr, $sep = ':'){
		
		return explode($sep, $timeStr);

	}
	
	
	
	
 
	
	
	
	
	/*** Method for fetching difference btw two time strings as (days, hrs, mins, secs) ***/
	public function time_difference($T1, $T2, $retTotalHourMinsSecs = false){
		
		//CONVERT DATETIME STRING TO TIMESTAMP//	
		$T1 = $T1? strtotime($T1) : time();
		$T2 = strtotime($T2);
		$diff = abs($T1 - $T2);
		$s = $diff;
		$m = floor($s / 60);
		$h = floor($m / 60);
		$d = floor($h / 24);
		
		if($retTotalHourMinsSecs)
			return array($d, $h, $m, $s);
		
		$h %= 24;
		$m %= 60;
		$s %= 60;
		
		return array($d, $h, $m, $s);
		
	}
	
	
 

	
	
	/*** Method for fetching initials from strings of word ***/
	public function get_initials($wordStrings, $doFirstWordOnly = false, $wordSeparator = ' '){
	
		$i_acc = '';
	
		if($wordStrings){
	
			$d_arr = explode($wordSeparator, $wordStrings);	
	
			foreach($d_arr as $w){
	
				$i_acc .= mb_substr($w, 0, 1);
	
				if($doFirstWordOnly)
					break;
	
			}
		}
	
		return $i_acc;
	
	}

	
	
	
	
	/*** Method for fetching random color with a token ***/
	public function get_random_color($tk = '', $useStd = true, $seed = 1000){
		
		$colors = array('1abc9c', '16a085', 'f1c40f', 'f39c12', '2ecc71', '27ae60', 'e67e22', 'd35400', '3498db', '2980b9', 'e74c3c', 'c0392b', '9b59b6', '8e44ad', 'bdc3c7', '34495e', '2c3e50', '95a5a6', '7f8c8d', 'ec87bf', 'd870ad', 'f69785', '9ba37e', 'b49255', 'b49255', 'a94136');
		$colorTk = '0123456789abcdef';
		$tk = strtoupper($tk);
		
		if($useStd){
			
			if($tk){
				
				$colorIndex = floor((ord($tk) + $seed) % count($colors));
				$tk = $colors[$colorIndex];
				
			}
			
		}else{
			
			if($tk = preg_replace("#[^".$colorTk."]#sU", '', strtolower($tk))){
				
				$tkLen = mb_strlen($tk);
				
				while($tkLen < 6){
					
					$tk .= mb_substr($colorTk, 0, 1);
					$tkLen = mb_strlen($tk);
					
				}
				
			}else
				$tk = mb_substr(str_shuffle($colorTk), 0, 6);
			
		}
		
		return '#'.$tk;
		
	}

	
	
	
	
	/*** Method for building letter avatar from a string ***/
	public function build_lavatar($string, $toff='', $cctk=true, $fw_only=false, $allScrn=false, $s=' ', $round=true){
	
		$la=$whl=$rnd=$datas='';//cctk => constant color token
	
		if($string){
	
			$ini = $this->get_initials($string, $fw_only, $s);
			$len = mb_strlen($ini);
			$whl = ($len > 2)? ($len - 0.7) : 2;
			$rnd = $round? '50%' : '';
			$datas = 'data-alphas="'.$ini.'" data-alphas-whl="'.$whl.'em" data-alphas-bg="'.$this->get_random_color(($cctk? $string : '')).'" 
					data-alphas-rnd="'.$rnd.'" data-alphas-toff="'.$toff.'"';
			$la = '<span class="lavatar '.($allScrn? '' : 'dsk-platform-dpn-i').'" '.$datas.' title="'.$string.'"></span>';
	
		}
	
		return $la;
	
	}
	
	
 
	 

	
	
	
	/*** Method for fetching alphabet characters ***/
	public function get_alphabets($retType='', $sep=''){
		
		$lowerAlphas = 'abcdefghijklmnopqrstuvwxyz';
		$upperAlphas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		
		switch($retType){
			
			case 'uc': $alphas = $upperAlphas; break;
			
			case 'lc_uc': $alphas = $lowerAlphas.$sep.$upperAlphas; break;
			
			case 'uc_lc': $alphas = $upperAlphas.$sep.$lowerAlphas; break;
			
			default: $alphas = $lowerAlphas;
			
		}
		
		return $alphas;
		
	}



	
 
	
 
	
	
	/*** Method for shuffling associative array ***/
	public function shuffle_assoc(&$arr){
		
		$keys = array_keys($arr);
		shuffle($keys);
		
		foreach($keys as $key){
			
			$new[$key] = $arr[$key];
			
		}
		
		$arr = $new;
		
		return true;
	}

 
	
 
	
 
	
	/*** Method for fetching cookie/server/session/get/post global variable ***/
	public function get_global_var($type_arr='sv', $k_arr='HTTP_USER_AGENT', $kcase_i=true){
		
		$res_arr = array(); $multi_arr = false;
		$type_arr = is_array($type_arr)? $type_arr : (array)$type_arr;
		$k_arr = is_array($k_arr)? $k_arr : (array)$k_arr;
		empty($k_arr)? ($k_arr[] = '') : '';
		empty($type_arr)? ($type_arr[] = 'sv') : '';
		$k_arr = $kcase_i? array_change_key_case($k_arr, CASE_LOWER) : $k_arr;
		$kLen = count($k_arr);		
		
		for($i=0; $i < $kLen; $i++){
			
			$type = (isset($type_arr[$i]) && $i > 0)? $type_arr[$i] : $type_arr[0];
			$k = (isset($k_arr[$i]) && $i > 0)? $k_arr[$i] : $k_arr[0];			
			
			$glob = &self::engine_global_vars($type);
			
			if(is_array($glob) && $k && isset($glob[$k])){
				
				$res_arr[] = $glob[$k];
				$multi_arr = is_array($glob[$k])? true : false;
				
			}
		}
		
		if($kLen > 1 || $multi_arr)
			return $res_arr;
		
		return (implode('', $res_arr));
		
	}
	
	
	
	
	
	
	
	/*** Method for setting cookie/server/session/get/post global variable ***/
	public function set_global_var($type_arr='ck', $k_arr='', $valArr=''){
		
		$type_arr = is_array($type_arr)? $type_arr : (array)$type_arr;
		$k_arr = is_array($k_arr)? $k_arr : (array)$k_arr;
		$valArr = is_array($valArr)? $valArr : (array)$valArr;
		empty($k_arr)? ($k_arr[] = '') : '';
		empty($valArr)? ($valArr[] = '') : '';
		empty($type_arr)? ($type_arr[] = 'ck') : '';
		$kLen = count($k_arr);
		
		for($i=0; $i < $kLen; $i++){
			
			$type = (isset($type_arr[$i]) && $i > 0)? $type_arr[$i] : $type_arr[0];
			$k = (isset($k_arr[$i]) && $i > 0)? $k_arr[$i] : $k_arr[0];
			$v = (isset($valArr[$i]) && $i > 0)? $valArr[$i] : $valArr[0];
			
			$glob = &self::engine_global_vars($type);
			
			if(is_array($glob) && $k)
				$glob[$k] = $v;			
			
		}
		
	}
	
	
	
	
	
	
	
	/*** Method for unsetting cookie/server/session/get/post global variable ***/
	public function unset_global_var($type_arr='ck', $k_arr=''){
		
		$type_arr = is_array($type_arr)? $type_arr : (array)$type_arr;
		$k_arr = is_array($k_arr)? $k_arr : (array)$k_arr;	
		empty($k_arr)? ($k_arr[] = '') : '';
		empty($type_arr)? ($type_arr[] = '') : '';
		$kLen = count($k_arr);	
		
		for($i=0; $i < $kLen; $i++){
			
			$type = (isset($type_arr[$i]) && $i > 0)? $type_arr[$i] : $type_arr[0];
			$k = (isset($k_arr[$i]) && $i > 0)? $k_arr[$i] : $k_arr[0];			
			
			$glob = &self::engine_global_vars($type);
			
			if(is_array($glob) && $k && isset($glob[$k]))
				unset($glob[$k]);	
			
		}		
	}
	
	
	
	
	
	
	
	/*** Method for checking if a cookie/server/session/get/post global variable is set ***/
	public function is_global_var_set($type_arr='ck', $k_arr='', $and=true, $kcase_i=true){	
	
		$res_arr = array();
		$type_arr = is_array($type_arr)? $type_arr : (array)$type_arr;
		$k_arr = is_array($k_arr)? $k_arr : (array)$k_arr;
		empty($k_arr)? ($k_arr[] = '') : '';
		empty($type_arr)? ($type_arr[] = '') : '';
		$k_arr = ($kcase_i? array_flip(array_change_key_case(array_flip($k_arr), CASE_LOWER)) : $k_arr);
		$kLen = count($k_arr);
		
		if(empty($k_arr))
			return false;
		
		for($i=0; $i < $kLen; $i++){
			
			$type = (isset($type_arr[$i]) && $i > 0)? $type_arr[$i] : $type_arr[0];
			$k = (isset($k_arr[$i]) && $i > 0)? $k_arr[$i] : $k_arr[0];			
			
			$glob = &self::engine_global_vars($type);
			
			if(is_array($glob) && $k && !isset($glob[$k]) && $and)
				return false;
			
			elseif(is_array($glob) && $k && isset($glob[$k]))
				$res_arr[] = true;
				
			else
				$res_arr[] = false;
			
		}
			
		if($kLen > 1 && !$and)
			return $res_arr;
		
		return (implode('', $res_arr));
		
	}
	
	
	
	
	
	
	
	/*** Method for referencing cookie/server/session/get/post global variable ***/
	private function &engine_global_vars($type){
		
		/******************
		returns a reference
		******************/		
		switch(strtolower($type)){	
		
			case 'ck' : $glob =& $_COOKIE; break;
			
			case 'ss' : $glob =& $_SESSION; break;
			
			case 'sv' : $glob =& $_SERVER; break;
			
			case 'get' : $glob =& $_GET; break;
			
			case 'post' : $glob =& $_POST; break;
			
			default : $glob = '';
			
		}
		
		return $glob;
		
	}
	
	
	
	/* Method for merging key value pair params */
	public function merge_params($userParams = [], $defaultParams = [], $buildQstr = true){
	
		foreach($userParams as $userParamKey => $userParamVal){
			
			$defaultParams[$userParamKey] = $userParamVal;
			
		}
		
		if($buildQstr){
			
			$defaultParams = '?'.http_build_query($defaultParams);
			
		}
		
		return $defaultParams;
	
	}
	

	
	/* Alias Method for only merging key value pair params without building query string */
	public function extend_params($param1 = [], $param2 = []){

		return $this->merge_params($param1, $param2, false);

	}
	
	
	/*** Method for merging and returning http encoded query string ***/
	public function merge_qstr($qstrArr = array()){
		
		$qstrDel = '?';		

		foreach($qstrArr as $qstr){				
			
			$keyValArr = explode('=', $qstr);
			$qstrKey = $this->get_assoc_arr($keyValArr, 0);
			$qstrVal = $this->get_assoc_arr($keyValArr, 1);
			$qstrKey = str_replace($qstrDel, '', $qstrKey);

			if(!$qstrKey)
				continue;

			$qstrResArr[$qstrKey] = $qstrVal;
			
		}
		
		return (isset($qstrResArr)? $qstrDel.http_build_query($qstrResArr) : '');

	}

	
	
	
	
	
	
	/*** Method for checking if an array is nested (multi dimensional) ***/
	public function is_nested_arr($arr){
		
		$arr = (array)$arr;
		return (isset($arr[0]) && is_array($arr[0]));
		
	}
	
	


	/*** Method for checking if an array value contains all or part of a string ***/
	public function find_in_arr_val($str, $arr, $retKey = false){
		
		$arr = (array)$arr;
		
		foreach($arr as $k => $v){

			if(strpos($v, $str) !== false)
				return ($retKey? $k : true);

		}

		return '';
		
	}
	
	
	
	
	
	
	
	
	/*** Method for checking if an associative array key is set ***/
	public function is_assoc_key_set($arr=array(), $k_arr='', $and=true, $kcase_i=true){	
	
		$res_arr = array();
		$arr = is_array($arr)? $arr : (array)$arr;
		$arr = $kcase_i? array_change_key_case($arr, CASE_LOWER) : $arr;
		$k_arr = is_array($k_arr)? $k_arr : (array)$k_arr;		
		$k_arr = ($kcase_i? array_flip(array_change_key_case(array_flip($k_arr), CASE_LOWER)) : $k_arr);
		$kLen = count($k_arr);
		
		if(empty($k_arr))
			return false;
		
		foreach($k_arr as $k){
			
			if(!isset($arr[$k]) && $and)
				return false; 
			
			elseif(isset($arr[$k]))
				$res_arr[] = true;
				
			else
				$res_arr[] = false;	
			
		}
			
		if($kLen > 1 && !$and)
			return $res_arr;
		
		return (implode('', $res_arr));
		
	}
	
	
	
	
	
	
	
	/*** Method for fetching associative array ***/
	public function get_assoc_arr($arr=array(), $k_arr='',  $key_indexes_arr_always=false, $kcase_i=true, $strict=false){
		
		/******************
		IMPORTANT: $key_indexes_arr_always => (LIST PROTECT)
		By default this method will return same structural type as the $arr passed;
		If any key in $k_arr points to a array itself then an array will be returned
		if a key in $k_arr points to a non existing index in $arr, it assigns '' as the value of that key
		
		E.G if we have; $arr = array('a'=>'Fil','b'=>'Edith','c'=>array(1,2,3))
		key 'c' is an array(); calling this method to fetch key 'c' will return an array of array (nested array)
		thus to un_nest and get the real value of key 'c' we can use list($key_c_arr) on the returned value.
		calling count($key_c_arr) is ok;
		
		Now imagine if for some reason key 'c' => ''; $arr = array('a'=>'Fil','b'=>'Edith','c'=>'')
		key 'c' is no longer an array; calling this method to fetch key 'c' will return an array
		Remember we expected an array as value of key 'c' but now we have '';  
		list($key_c_arr) on the returned value.
		calling count($key_c_arr) is not ok since the listed variable contains a '' which cannot be counted;
		
		To solve this problem we added $key_indexes_arr_always boolean that explicitly allow users to specify when a key is
		pointing to value that should always be an array
		******************/
		
		$res_arr = array();	$multi_arr = false;	
		$arr = is_array($arr)? $arr : (array)$arr;		
		$k_arr = is_array($k_arr)? $k_arr : (array)$k_arr;	
		$arr = $kcase_i? array_change_key_case($arr, CASE_LOWER) : $arr;
		$k_arr = ($kcase_i? array_flip(array_change_key_case(array_flip($k_arr), CASE_LOWER)) : $k_arr);
		
		$noKey_defaultVal = $key_indexes_arr_always? array() : '';		
		$kLen = count($k_arr);
		$multi_key = ($kLen > 1);		
		
		foreach($k_arr as $k){
			
			$k_val = ($strict? $arr[$k] : (isset($arr[$k])? $arr[$k] : $noKey_defaultVal));
			$multi_key? ($res_arr[] = $k_val) : ($res_arr = $k_val); //maintain the expected value data structure			
			
		}
		
		if($multi_key)
			return $res_arr;
		
		return $res_arr;
		
	}
	
	

	
	
	/*** Method for converting delimited strings to associative array ***/
	public function str_to_assoc($str, $buildUrl=false, $isPhoneUrl=false, $seperatorDelimeter=",", $keyDelimeter="::"){
		
		$assocArr = $assocUrlArr = array();
		$arr = explode($seperatorDelimeter, $str);			
		$fallBackKeyIndex = 0;

		foreach($arr as $v){
			
			$kv = explode($keyDelimeter, $v);
			
			if(!isset($kv[0]) || !isset($kv[1])){

				$Key = $fallBackKeyIndex++;
				$valIndex = 0;

			}else{
				
				$Key = $kv[0];
				$valIndex = 1;

			}
			
			$assocArr[$Key] = ($val = $kv[$valIndex]);

			if($buildUrl){
			
				if($isPhoneUrl){

					$val = $val.self::$hrefUrlTxtSep.'tel:'.preg_replace('#[^+0-9]#', '', $val);

				}elseif(self::email_validate($val)){

					$val = $val.self::$hrefUrlTxtSep.'mailto:'.$val;

				}

				$assocUrlArr[$Key] = $this->add_http_protocol($val);

			}
			
		}
		
		return ($buildUrl? array($assocArr, $assocUrlArr) : $assocArr);
		
	}
	
	

	
	
	/*** Method for resetting an associative array to default value ***/
	public function assoc_arr_reset($arr, $defaultVal = ''){
		
		if(!is_array($arr)) 
			return $arr;
		
		$arr = array_keys($arr); //Get the assoc arr keys
		return array_fill_keys($arr, $defaultVal);

	}
	
	

	
	
	/*** Method for checking if the value of a datetime string evaluates to PHP boolean TRUE ***/
	public function datetime_true($datetime){
				
		return preg_match("/[1-9]/", $datetime);

	}
	
	
	
	
	/*** Method for zipping/archiving folder or directory ***/
	public function zip($d2z, $zn='', $download=true, $delODF=true, $dndODFArr=array()){
	
		// Get real path for directory to zip
		$d2z = realpath($d2z) or die('<div>Ooops! It seems the directory path specified for zipping is incorrect!</div>');
		
		// Genearate a name for the new zip if none is passed
		$zn = ($zn? $zn : 'file_'.$this->generate_token(30)).'.zip';
		$zipSaveDir = 'static/archives/';
		$zipSavePath = $zipSaveDir.$zn;
		
		// ensure do not delete array is array
		$dndODFArr = is_array($dndODFArr)? $dndODFArr : (array)$dndODFArr;
		
		// Initialize empty "delete list"
		$filesToDelete = array();
		
		// Initialize archive object
		$zip = new ZipArchive();
		$zip->open($zipSavePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);


		// Create recursive directory iterator
		/** @var SplFileInfo[] $files */
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($d2z),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file){
			
			// Skip directories (they would be added automatically)
			if (!$file->isDir()){
				
				// Get real and relative path for current file
				$filePath = $file->getRealPath();
				$fn = mb_substr($filePath, mb_strlen($d2z) + 1);

				// Add current file to archive
				$zip->addFile($filePath, $fn);
				
				/*** 
					Add current file to "delete list"
					delete it later cause ZipArchive create archive only after calling close 
					method and ZipArchive lock files until archive is created)
				***/
				if (!in_array($file->getFilename(), $dndODFArr)){
					
					$filesToDelete[] = $filePath;
					
				}
			}
		}

		// Zip archive will be created only after closing object
		$zip->close();

		// Delete all files from "delete list"
		if($delODF)
			foreach ($filesToDelete as $file){
				
				unlink($file);
				
			}
					
		// Download the zip file
		if($download){
			
			//$this->download_handler(array("file"=>$zn, "dir"=>$zipSaveDir));
			header("Location:/downloads/archives/".$zn);
			exit();
			
		}
	}
		
		

	
	
	
	
	
	
	
	/*** Method for fetching mime content types ***/
	public function mime_content_type($filename){
		
		/*
		//OUT OF MEMORY ISSUES
		if(function_exists('mime_content_type'))
			return mime_content_type($filename);
		*/
		
		$mime_types = array(
		
			// text
			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'csv' => 'text/csv',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',

			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);

		$tmpArr = explode('.',$filename);
		$ext = strtolower(array_pop($tmpArr));
		
		if (array_key_exists($ext, $mime_types)){
			
			return $mime_types[$ext];
			
		}elseif (function_exists('finfo_open')){

			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;

		}else {

			return 'application/octet-stream';

		}

	}


	
	
	/*** Method for fetching file extension from the mime type ***/
	public function get_ext_from_mime_type($mimeType, $prefixDot = false){
		
		$tmp = explode('/', $mimeType);
		
		return (($prefixDot? '.' : '').array_pop($tmp));


	}
	
	
	
	
	
	/*** Method for downloading files ***/
	public function download_handler($optArr){
		
		$dir = $this->get_assoc_arr($optArr, "dir");
		$file = $this->get_assoc_arr($optArr, "file");
		$customFileName = $this->get_assoc_arr($optArr, "customName");
		$ctype = $this->get_assoc_arr($optArr, "ctype");
		$cleanUp = (bool)$this->get_assoc_arr($optArr, "cleanUp");
		$pausable = (bool)$this->get_assoc_arr($optArr, "pausable");
		$stream = (bool)($this->is_assoc_key_set($optArr, "stream")? $this->get_assoc_arr($optArr, "stream") : false);
			
		$file_path = $dir.$file;
				
		/////DOWNLOAD FILES//////////
		if(file_exists($file_path) && is_file($file_path)){
			
			$file_size  = filesize($file_path);
			
			if(is_readable($file_path) && ($openedFile = @fopen($file_path, "rb"))){
			
				//////DETERMINE THE CONTENT TYPE////////
				if(!$ctype)
					$ctype = $this->mime_content_type($file_path);
					
				/////SET RQD HTTP HEADERS//////////	
					
				header("Content-Type: ".$ctype);
				header("Content-Disposition: ".($stream? "inline" : "attachment")."; filename=".($customFileName? $customFileName : $file));
				if($stream)
					header("Content-Transfer-Encoding: binary");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0,  pre-check=0");
				header("Pragma: public");
				
				$range = $seek_start = $seek_end = 0;
				
				//CHECK IF HTTP_RANGE WAS SENT BY UA/CLIENT
				if(/*$pausable && */isset($_SERVER['HTTP_RANGE'])){
					
					list($size_unit, $orig_range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
					
					if($size_unit == 'bytes'){
						
						//multiple ranges could be specified at the same time, but for simplicity only serve the first range
						//http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
						list($range, $extra_ranges) = explode(',', $orig_range, 2);
						
					}else{
						
						header('HTTP/1.1 416 Requested Range Not Satisfiable');
						exit();
						
					}
				}
				
				$maxChunkByte = ($file_size - 1);
				
				//SEEK FROM RANGE (IF SET)
				if($range){
					
					list($seek_start, $seek_end) = explode('-', $range, 2);
					$seek_start = intval($seek_start);
					$seek_end = intval($seek_end);
					
				}
				
				//set start and end based on range (if set), else set defaults
				//also check for invalid ranges.
				$seek_end   = !$seek_end? $maxChunkByte : min(abs($seek_end), $maxChunkByte);
				$seek_start = (!$seek_start || $seek_end < abs($seek_start))? 0 : max(abs($seek_start), 0);
				 
				//Only send partial content header if downloading a piece of the file (IE workaround)
				if ($seek_start > 0 || $seek_end < $maxChunkByte){
					
					header('HTTP/1.1 206 Partial Content');
					header('Content-Range: bytes '.$seek_start.'-'.$seek_end.'/'.$file_size);
					header('Content-Length: '.($seek_end - $seek_start + 1));
					
				}else
				  header("Content-Length: ".$file_size);

				header('Accept-Ranges: bytes');
				
				set_time_limit(0);
				fseek($openedFile, $seek_start);
					
				while(!feof($openedFile)){
					
					print(@fread($openedFile, 1024*8));
					ob_flush();
					flush();
					
					if(connection_status() != 0){
						
						@fclose($openedFile);
						exit;
						
					}			
				}
					
				// file save was a success
				@fclose($openedFile);
				
				//readfile($file_path);
				
				if($cleanUp && realpath($file_path))
					unlink($file_path);
			}else{
				
				header($tmp="HTTP/1.0 500 Internal Server Error");
				die("Error: ".$tmp);
				
			}
			
		}else{
			
			header($tmp="HTTP/1.0 404 Not Found");
			die("Error: ".$tmp);
			
		}	
		
		exit();
		
	}
	
	
	
	
	
	
	
	
	/*** Method for looping through a directory ***/
	public function loop_dir($dirPath, $recursive=true, $limit=100){
		
		$dirContentAcc = array(); $limCounter=0;
		
		if($recursive){
			
			$rdi = new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::KEY_AS_PATHNAME);
			
			foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST) as $file){
				
				$fileName = $file->getFileName();
				$pathName = $file->getPathName();
				
				if(mb_substr($pathName, -1, 1) === '.' || !is_file($pathName)) 
					continue;
					
				$dirContentAcc[] = $pathName;
				
				if(($limCounter++) >= $limit) 
					break;
				
			}
			
		}else{
			
			if ($dirPathOpened = opendir($dirPath)) {
				
				while(($file = readdir($dirPathOpened)) !== false){
					
					if(mb_substr($file, -1, 1) === '.') 
						continue;
						
					$dirContentAcc[] = $file;
					
					if(($limCounter++) >= $limit) 
						break;
					
				}
				
				closedir($dirPathOpened);
				
			}
		}
		
		return $dirContentAcc;
		
	}
	
	
	
	
	
	
	
	
	/*** Method for deleting a directory ***/
	public function del_dir($dir){
		
		if(!$dir) 
			return false;
		
		// Get real path for the directory to delete
		$dir = realpath($dir) or die('<div class="warn-alert">Ooops! It seems the directory path specified for delete is incorrect!</div>');
		
		$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
		
		foreach($files as $file) {
			
			if ($file->isDir()){
				
				rmdir($file->getRealPath());
				
			}else{
				
				unlink($file->getRealPath());
				
			}
		}
		
		return rmdir($dir);
		
	}



	
	
	
	
	/*** Method for checking if a string is alpha numeric ***/
	public function str_is_alpha_num($str){

		return preg_match("#[0-9]+[A-Z]+#i", $str);
	
	}
	
	
	
	/*** Method for formating numbers as comma separated thousands ***/
	public function format_number($num, $dcp=0, $kfmt=true){
		
		$numFmtd="";
		
		if($kfmt){		
			
			$num = (float)$num;
			$numFmtd = number_format(round($num), $dcp);
			$units_arr = array("K", "M", "B", "T");
			$thsd_arr = explode(",", $numFmtd);
			$thsd_len = count($thsd_arr);
			$numFmtd = $thsd_arr[0].((int)(isset($thsd_arr[1][0]) && $thsd_arr[1][0] != 0)? '.'.$thsd_arr[1][0] : "");
			$numFmtd .= ($thsd_len >= 2)? ($units_arr[($thsd_len - 2)]) : "";
			
		}else
			$numFmtd = number_format($num, $dcp);
		
		return $numFmtd;
		
	}


	 
	
	
	
	/*** Method for automatic conversion of memory sizes ***/ 
	public function mem_auto_convert($mem_size){

		$unit_arr = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');

		$mem_base_num = 1024;

		return @round($mem_size / pow($mem_base_num, ($exponent = floor(log($mem_size, $mem_base_num)))), 2).$unit_arr[$exponent];

	}


	
	
	
	/*** Method for adding a string to or checking if a string is in generator session store ***/ 
	private function sess_unq_gen_store($str, $addToStore = false){

		$unqGenStore = (array)self::get_global_var('ss', ($unqGenStoreKey = '_UNQ_GENERATOR_CHAR_NUM_TOKEN_SESS_STORE_'));

		$isInStore = in_array($str, $unqGenStore);

		if($addToStore){

			if(!$isInStore){
			
				$unqGenStore[] = $str;
				self::set_global_var('ss', $unqGenStoreKey, $unqGenStore);

			}

		}else{

			return $isInStore;

		}							

	}
	
	
	
	/*** Method for generating session unique token ***/ 
	public function generate_sess_unq_token($prefix = '', $type = 'num', $len = 5){
		
		$maxLoopRetry = 20;

		do{
			switch(strtolower($type)){

				case 'char': $token = $prefix.self::generate_fixed_length_char($len);
							self::sess_unq_gen_store($token, true); // Add to store
							break;

				case 'token': $token = $prefix.self::generate_token($len);
							self::sess_unq_gen_store($token, true); // Add to store
							break;

				default: $token = $prefix.self::generate_fixed_length_num($len);
						self::sess_unq_gen_store($token, true); // Add to store

			}

			$maxLoopRetry--; //decrement loop retry

		}while(self::sess_unq_gen_store($token) && $maxLoopRetry != 0);
		
		return $token;
		
	}



	
	/*** Method for generating fixed length random numbers ***/ 
	public function generate_fixed_length_num($len = 10, $salt = null){
			
		$rand=$min=$max="";
		///////SALT IS UNIQUE ID PASSED WHICH CAN NEVER BE ZERO////
		if(is_null($salt)) $salt = mt_rand(1, 100);
		$salt = (int)$salt;
		
		while(mb_strlen($rand) < $len ){
			
			$min = ($salt + ($len * $len));
			$max = ($len * $len * $salt);
			$maxCopy = $max;
			
			if($min > $max){
				
				$max = $min;
				$min = $maxCopy;
				
			}
			
			$rand .= mt_rand($min, $max);		

			if(mb_strlen($rand) > $len)
				$rand = mb_substr($rand, 0, $len);
			 
		}
		
		return $rand;
	 
	}


	
	
	
	/*** Method for generating fixed length random characters ***/ 
	public function generate_fixed_length_char($len = 10, $chr = "", $uniquePerSession = false){
		 
		$rand="";
		/////NOTE ASCII CHARS LIES BTW DECIMALS 65-90(A-Z) AND 97-122(a-z) IN ASCII TABLE///////
		
		while(mb_strlen($rand) < $len ){
			
			if(!$chr){
				
				$seed = mt_rand(1, 2);
				
				if($seed == 1)
					$rand .= chr(mt_rand(65, 90));
				
				elseif($seed == 2)
					$rand .= chr(mt_rand(97, 122));
					
			}else{
				
				$rand .= $chr;
				
			}
			
			if(mb_strlen($rand) > $len)
				$rand = mb_substr($rand, 0, $len);
			 
		}
		
		return $rand;
	 
	}
	 


	
	
	/*** Method for generating random tokens ***/ 
	public function generate_token($len = '', $alphaNumOnly = false, $uniquePerSession = false){
		
		$dash = $saltDash = '-';
		$underScore = $saltUnderScore = '_';
		$matchArr = array($dash.$dash, $underScore.$underScore, $dash.$underScore, $underScore.$dash);
		$replaceArr = array($dash, $underScore, $dash, $dash);
		
		if($alphaNumOnly)
			$saltDash=$saltUnderScore='';
		
		
		$len = $len? $len : mt_rand(100, 240);
		$salt = $saltDash.'0123456789'.$saltDash.$this->get_alphabets('lc_uc', $saltDash).$saltUnderScore;
		$c='';
		
		while(mb_strlen($c) < $len){
			
			$c .= str_replace($matchArr, $replaceArr, str_shuffle($salt));
			$c = trim(mb_substr($c, 0, $len), $dash.$underScore);
			
		}
		
		//return (mb_substr(md5(mt_rand(1,1000)), 0, $len));		 		 
		return $c;		 		 
			 
	}
		
	

	 
	
	
	
	/*** Method for validating an email address ***/	
	public function email_validate($email){

		return preg_match('#^[a-z0-9-_\.]+\@[a-z0-9-_]+\.[a-z]{1,}$#i', $email);


	}
	
	
	
	/*** Method for hiding designated parts of a sensitive string like an email address ***/	
	public function cloak($str, $cloakPercent=60, $maskLen=0, $cipher='x'){	
		
		$maskedStr = ""; 
		$defCipher = 'x';
		$defCloakPerc = 60; 
		$maxCloakPerc = 100; 
		$rvsDelim = '-'; 
		$midDelim = '.';
		$cloakLenCondDelim = ':';
		$cloakPosDelim = '|';
		$cloakStartEndDelim = ',';
		!$cipher? ($cipher = $defCipher) : '';	

		$cloakPercentArg = $cloakPercent;
		$cloakPercentArr = explode($cloakLenCondDelim, $cloakPercent);
		$cloakPercent = isset($cloakPercentArr[0])? (int)$cloakPercentArr[0] : $defCloakPerc;	
		$cloakLenCond = isset($cloakPercentArr[1])? (int)strstr($cloakPercentArr[1], $cloakPosDelim, true) : '';
		
		$cloakPosArr = explode($cloakPosDelim, $cloakPercentArg);
		$cloakPos = isset($cloakPosArr[1])? $cloakPosArr[1] : '';
		$cloakStartEndArr = explode($cloakStartEndDelim, $cloakPos);	
		$cloakStartPos = isset($cloakStartEndArr[0])? (int)$cloakStartEndArr[0] : false;	
		$cloakPosLen = isset($cloakStartEndArr[1])? (int)$cloakStartEndArr[1] : false;
	
		$cloakPercent = !$cloakPercent? $defCloakPerc : (($cloakPercent > $maxCloakPerc)? $maxCloakPerc : $cloakPercent);		
	
		$rvsDir = (mb_strpos($maskLen, $rvsDelim) !== false);
		$midDir = (mb_strpos($maskLen, $midDelim) !== false);
		$maskLen = (int)ltrim(ltrim($maskLen, $midDelim), $rvsDelim);

		if($cloakLenCond && ($strLen < $cloakLenCond))
			return $str;
				
		$strLen = mb_strlen($str);
		
		if($cloakStartPos){
			
			$txt2Replace = mb_substr($str, $cloakStartPos, $cloakPosLen);
			$cipherLen = $maskLen? $maskLen : mb_strlen($txt2Replace);
			$replacementCipher = str_repeat($cipher, $cipherLen);			
			$maskedStr = substr_replace($str, $replacementCipher, $cloakStartPos, $cloakPosLen);
			
		}elseif($strLen > 1){
						
			$cloakPercent = ($cloakPercent / 100);			
			$cloakLen = round($cloakPercent * $strLen);	
			$cloakLen = ($cloakLen < 1)? 1 : $cloakLen;				
			$mid2SideLen = (int)(($strLen - $cloakLen) / 2);			
			$strCloaked = $rvsDir? mb_substr($str, '-'.$cloakLen) : ($midDir? mb_substr($str, $mid2SideLen - 1, $cloakLen) : mb_substr($str, 0, $cloakLen));				
			$strCloakedLen = mb_strlen($strCloaked);
			
			if($midDir){

				$lStrUncloaked = mb_substr($str, 0, $mid2SideLen);
				$RStrUncloaked = mb_substr($str, '-'.$mid2SideLen);
				$strUncloakedLen = mb_strlen($lStrUncloaked.$RStrUncloaked);

			}else{
	
				$strUncloaked = $rvsDir? mb_substr($str, 0, '-'.$strCloakedLen) : mb_substr($str, $strCloakedLen);	
				$strUncloakedLen = mb_strlen($strUncloaked);

			}
			
			if($maskLen)
				$cipherLen = $maskLen;

			else{

				$combinedCloakLen = ($strCloakedLen + $strUncloakedLen);
				$lenDeviation = ($strLen - $combinedCloakLen);

				$cipherLen = ($cloakLen + $lenDeviation);				

			}
			
			
			$mask = $this->generate_fixed_length_char($cipherLen, $cipher);
			$maskedStr = $rvsDir? $strUncloaked.$mask : ($midDir? $lStrUncloaked.$mask.$RStrUncloaked : $mask.$strUncloaked);
	
		}
	
		return $maskedStr;
	}

	

	

	
	
	/*** Method for checking if a HTTP Request was made via AJAX ***/	
	public function is_ajax(){
 
		return (isset($_SERVER[$K="HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER[$K]) == 'xmlhttprequest');
		
	}
	
	
	
	
	/*** Method for formating a string using titled case ***/
	public function title_case($str){
		
		//We are not interested in all characters after an apostrophe
		$fmtStr = [];
		$apostrophe = "'";
		!$str? ($str = '') : '';
		$wordsArr = explode(' ', $str);
		
		foreach($wordsArr as $word){
			
			$wordArr = explode($apostrophe, $word);
			$eligbleStr = array_shift($wordArr);
			$apostropheSide = strtolower(implode($apostrophe, $wordArr));
			$apostropheSide? ($apostropheSide = $apostrophe.$apostropheSide) : '';
			$fmtStr[] = (preg_replace_callback("#[a-zA-Z]+#", function($m){return ucwords(strtolower($m[0]));}, $eligbleStr)).$apostropheSide;
			
		}
		
		
		return implode(' ', $fmtStr);
		
	}
	
	

	 
	
	
	/*** Method for converting HTML tags to square bracket tags ***/
	public function html_to_square_tags($str){
		
		$str = str_ireplace("</", "[/", $str);
		$str = str_ireplace("<", "[", $str);
		$str = str_ireplace(">", "]", $str);	
		return $str;
		
	}

	
	
	
	
	/*** Method for filtering out new lines and line breaks ***/
	public function filter_line_chars($str, $newVal=''){
		
		return (str_ireplace(array("\n","\r","\r\n"), array($newVal), $str));
		
	}
	
	
	
	
	/*** Method for decoding global variable contents ***/
	public function url_decode_global_var(&$globalVar){
		
		foreach($globalVar as $k => $v){
			
			$_POST[$k] = urldecode($v);
			
		}
		
	}





	
	/*** Method for preserving specific number of whitespace in a string ***/
	public function preserve_n_whitespace($input, $nWhitespace){
		
		$origStrLen = mb_strlen($input);
		$leftSideWhitespaceCount = ($origStrLen - mb_strlen(ltrim($input)));
		$rightSideWhitespaceCount = ($origStrLen - mb_strlen(rtrim($input)));
		
		if($leftSideWhitespaceCount > $nWhitespace){
			
			$input = mb_substr($input, ($leftSideWhitespaceCount - $nWhitespace));
			
		}
		
		if($rightSideWhitespaceCount > $nWhitespace){
			
			$input = mb_substr($input, 0, -($rightSideWhitespaceCount - $nWhitespace));
			
		}
		
		return $input;

	}


	
	/*** Method for doing basic input sanitation ***/
	public function sanitize_user_input($input, $metaArr=array()){

		$urlDecode = isset($metaArr[$K="urlDecode"])? $metaArr[$K] : false;
		$preserveTags = isset($metaArr[$K="preserveTags"])? $metaArr[$K] : false;
		$preserveWhitespace = (int)(isset($metaArr[$K="preserveWhitespace"])? $metaArr[$K] : false);
		$preserveSlashes = isset($metaArr[$K="preserveSlashes"])? $metaArr[$K] : false;
		$lowercase = isset($metaArr[$K="lowercase"])? $metaArr[$K] : false;
		
		if($preserveWhitespace){
			
			$input = $this->preserve_n_whitespace($input, $preserveWhitespace);
			
		}else
			$input = trim($input);
			
		$input = $preserveSlashes? $input : stripslashes($input);
		$input = $preserveTags? $input : strip_tags($input);
		
		if($urlDecode)
			$input = urldecode($input);
		
		if($lowercase)
			$input = strtolower($input);

		return $input;
		
	}



	
	
	
	
	/*** Method for checking for white spaces in a string ***/
	public function has_white_space($arr){
		
		if(!is_array($arr))
			$arr = (array)$arr;
		
		$ws = false;
		
		foreach($arr as $p){
							
			/////////SPACE CHECKING IN PASSWORD//////
			$hws = mb_strpos($p, " ");		
			$wsi = mb_substr($p, 0, 1);
			
			if($wsi == " " || $hws){
				
				$ws = true;
				break;
				
			}
		}
		
		return $ws;
		
	}

	




	
	
	
	/*** Method for sanitizing numbers (removes non numeric characters from a string) ***/
	public function sanitize_number($val, $specialChar=''){
		
		return preg_replace("#[^0-9".$specialChar."]#", "", $val);
		
	}
	
	
	


	
	
	/*** Method for generating/sanitizing url safe slug for a string ***/
	public function sanitize_slug($str, $meta=array()) {
		
		$origSlug = $str;
		$oldLocale = setlocale(LC_CTYPE, 0);
		setlocale(LC_CTYPE, 'en_US.UTF8');
		//setlocale(LC_ALL, 'en_US.UTF8');
		$defDelimiter = '-';
		$defDocXtn = '';
		$delimiter = isset($meta[$k="delimiter"])? $meta[$k] : $defDelimiter;
		!$delimiter? ($delimiter = $defDelimiter) : '';
		$replace = (array)(isset($meta[$k="replace"])? $meta[$k] : '');
		$ret = isset($meta[$k="ret"])? strtolower($meta[$k]) : '';
		$relUrl = (bool)(isset($meta[$k="relUrl"])? $meta[$k] : true);
		$appendXtn = isset($meta[$k="appendXtn"])? $meta[$k] : $defDocXtn;
		$urlAttr = isset($meta[$k="urlAttr"])? $meta[$k] : '';
		$slugSanitized = isset($meta[$k="slugSanitized"])? $meta[$k] : false;
		$preUrl = (isset($meta[$k="preUrl"]) && $meta[$k])? $meta[$k].'/' : '';
		$postUrl = (isset($meta[$k="postUrl"]) && $meta[$k])? '/'.$meta[$k] : '';
		$urlQstr = (array)(isset($meta[$k="urlQstr"])? $meta[$k] : '');
		$qstrEncode = (bool)(isset($meta[$k="qstrEncode"])? $meta[$k] : true);
		$ignoreHref = (bool)(isset($meta[$k="ignoreHref"])? $meta[$k] : false);
		$urlHash = (isset($meta[$k="urlHash"]) && $meta[$k])? '#'.$meta[$k] : '';
		$urlText = (isset($meta[$k="urlText"]) && $meta[$k])? $meta[$k] : $str;
		$cssClass = isset($meta[$k="cssClass"])? ' '.$meta[$k] : '';
		$domain = $this->get_domain();
		
		if(!empty($replace))
			$str = str_replace($replace, ' ', $str);

		$clean = iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $str);
		$clean = preg_replace("#[^a-zA-Z0-9/_|()\[\]\{\},+ -]#", '', $clean);
		$clean = preg_replace("#[/_|+ -(),\[\]\{\}]+#", $delimiter, $clean);
		$clean = strtolower(trim($clean, ' -'));
		$clean = str_ireplace(array("---","--"), $delimiter, $clean);
		
		setlocale(LC_CTYPE, $oldLocale);
		
		$sanSlug = $clean? $clean.$appendXtn : '';
		
		if(!empty($urlQstr))
			foreach($urlQstr as $qk => $qv){
				
				if($qk && $qv)
					$qstrAcc[] = $qk.'='.($qstrEncode? urlencode($qv) : $qv);
				
			}
			
		$qstr = (isset($qstrAcc) && !empty($qstrAcc))? '?'.implode('&', $qstrAcc) : '';
		$href = $preUrl.($slugSanitized? $origSlug : ($relUrl? '/' : '').$sanSlug).$postUrl.$qstr.$urlHash;
		$ignoreHref? ($href = '/') : '';
		(strpos($urlAttr, 'title=') === false)? ($urlAttr .= ' title="'.((stripos($href, $domain) !== false)? '' : $domain.'/').ltrim($href, '/').'"') : '';
		
		if($ret)
			switch($ret){
				
				case 'url': $sanSlug = '<a class="links'.$cssClass.'" href="'.$href.'" '.$urlAttr.'>'.$urlText.'</a>';
				
			}
			
		return $sanSlug;
		
	}
	
	
	
	
	
	
	/*** Method for manipulating/controlling single page application urls ***/
	public function url_controller($paramsArr){	
	
		/*****************************
		NOTES:
		$paramsArr is a 2 dimensional array.
		*****************************/
		
		$paramsArr = is_array($paramsArr)? $paramsArr : (array)$paramsArr;
		!isset($paramsArr[$K="pathKeys"])? ($paramsArr[$K] = '') : '';		
		!isset($paramsArr[$K="maxPath"])? ($paramsArr[$K] = 1) : '';		
		!isset($paramsArr[$K="fallBack"])? ($paramsArr[$K] = '') : '';	
		!isset($paramsArr[$K="rdrFallBack"])? ($paramsArr[$K] = false) : '';	
		!isset($paramsArr[$K="responseCode"])? ($paramsArr[$K] = 302) : '';	
		$paramKeysArr = array('pathKeys', 'maxPath', 'fallBack', 'rdrFallBack', 'responseCode');
		list($pathKeysArr, $maxPath, $fallBack, $rdrFallBack, $responseCode) = $this->get_assoc_arr($paramsArr, $paramKeysArr);
		$pathKeysArr = is_array($pathKeysArr)? $pathKeysArr : (array)$pathKeysArr;							
		
		/*****SET DEFAULT PARAMS*****/					
		$fallBack =  (!$fallBack)? 'page-error.php' : $fallBack;	
		$maxPath = (!$maxPath && $maxPath !== 0)? 1 : $maxPath;
		$rdrFallBack =  (bool)((!$rdrFallBack)? false : $rdrFallBack);			
		$responseCode = (!$responseCode)? 302 : (int)$responseCode;
		
		$full_path = $this->get_page_path('page_url', '', true, true);		
	
		/***HANDLE THE  REQUEST URL***/
		$full_path_arr = explode("/", $full_path);				
		
		for($idx=0; $idx < count($pathKeysArr); $idx++){
			
			/***SET EACH PATH INTO SUPER GET GLOBALS USING THE RESPECTIVE KEYS PASSED***/		
			if(isset($full_path_arr[$idx]) && $full_path_arr[$idx]){
				
				$_GET[$pathKeysArr[$idx]] = $full_path_arr[$idx];	
				
			}	
			
		}		
		
		######USE QSA FROM .HTACCESS INSTEAD
		/***HANDLE ATTACHED QUERY STRINGS****/
		/*$full_path_all = $this->get_page_path('page_url');	
		
		if(strpos($full_path_all, "?") !== false){			
		
			$paths_and_qstr_arr = explode('?', $full_path_all);
			
			if(isset($paths_and_qstr_arr[1])){
				
				$qstrs = $paths_and_qstr_arr[1];
				$qstr_arr = explode("&", $qstrs);
				
				foreach($qstr_arr as $qstr){
					
					$qstr_kv_arr = explode("=", $qstr);
					
					if(isset($qstr_kv_arr[0])){
						
						$qstr_k = $qstr_kv_arr[0];
						$qstr_v = isset($qstr_kv_arr[1])? $qstr_kv_arr[1] : '';
						
						if(!isset($_GET[$qstr_k]))
							$_GET[$qstr_k] = urldecode($qstr_v);
						
					}
				}
			}		
		}*/
		
		/***EXECUTE FALLBACK IF NEED BE***/
		if(count($full_path_arr) > $maxPath){
			
			if($rdrFallBack)
				header("Location:/".$fallBack, true, $responseCode);
			else
				include_once($this->get_doc_root().$fallBack);				
			exit();
			
		}
		
	}

	
	
	
	
	
	/*** Method for fetching the root of server document/folder tree ***/
	public function get_doc_root($appendSlash = true){
		
		return ($_SERVER['DOCUMENT_ROOT'].(($appendSlash)? '/' : ''));
		
	}
	
	
	
	
	
	/*** Method for formating date as durations(secs, mins, hrs, days, wks, mnths, years) ago ***/
	public function time_ago($timestamp, $stopAtWks = true, $appendTime = true){
		
		$SEC = 'sec';		
		$MIN = 'min';
		$HR = 'hr';	
		$DAY = 'day';	
		$WK = 'wk';
		$MONTH = 'mnth';
		$YR = 'yr';	
		$UNIT = ' ago';
		$timestamp = is_numeric($timestamp)? $timestamp : strtotime($timestamp);
		$T = $appendTime? ' at '.date('g:ia',$timestamp) : '';
		$diff = abs(time() - $timestamp);
		
		//note that diff is in seconds		
		$ret="";
		
		if($diff < 60){
			
			if($diff <= 10){
				
				$ret = "just now";
				
			}else{
				
				$ret = $diff." ".$SEC.(($diff == 1)? '' : 's').$UNIT;
				
			}
			
		}elseif($diff >= 60 && $diff < 3600 ){
			
			$rem = round($diff / 60);						
			$ret = $rem.' '.$MIN.(($rem == 1)? '' : 's').$UNIT;
			
		}elseif($diff >= 3600 && $diff < 86400 ){
			
			$rem = round($diff / 3600);
			$ret = $rem.' '.$HR.(($rem == 1)? '' : 's').$UNIT;
			
		}elseif($diff >= 86400 && $diff < 604800){
			
			$rem = round($diff / 86400);
			//$ret = ($rem.' '.$DAY.(($rem == 1)? '' : 's').$UNIT.$T);
			$ret = (($rem == 1)? 'yesterday' : $rem.' '.$DAY.'s'.$UNIT).$T;
			
		}elseif($diff >= 604800 && $diff < 2419200){
			
			$rem = round($diff / 604800);
			$ret = $rem.' '.$WK.(($rem == 1)? '' : 's').$UNIT.$T;
			
		}elseif($stopAtWks){
			
			$date_fmt = date('jS M Y', $timestamp);	
			$ret = ' on '.date('D', $timestamp).', '.$date_fmt.$T;
			
		}elseif($diff >= 2419200 && $diff < 31536000){
			
			$rem = round($diff / 2419200);
			$ret = $rem.' '.$MONTH.(($rem == 1)? '' : 's').$UNIT.$T;
			
		}elseif($diff >= 31536000){
			
			$rem = round($diff / 31536000);
			$ret = $rem.' '.$YR.(($rem == 1)? '' : 's').$UNIT.$T;
			
		}
		
		return $ret;
		
	}
	
	
	
	
	
	
	/*** Method for fetching dates safe (no Y2K38 bug) ***/
	public function get_date_safe($refDateTime1="", $fmt='', $meta_arr=''){
		
		//$ret = $refTime? Date($fmt, strtotime($refTime)) : Date($fmt, time());//HAS Y2K38 bug///
		$mdfMeta = $this->get_assoc_arr($meta_arr, 'mdfMeta');
		$xActiveYearFmt = $this->get_assoc_arr($meta_arr, 'xActiveYearFmt');
		$cmpRef = $this->get_assoc_arr($meta_arr, 'cmpRef');
		$cmpRef? ($cmpRef = new \DateTime($cmpRef)) : '';
		
		$subtract = (mb_substr($mdfMeta, 0, 2) == '--')? true : false;
		$refDateTime2 = $subtract? mb_substr($mdfMeta, 2) : '';
		$fmt = $fmt? $fmt : 'Y-m-d H:i:s';
		$dToday = new \DateTime();
		$dT1 = $dRef = $refDateTime1? new \DateTime($refDateTime1) : $dToday;
		
		if($mdfMeta && !$refDateTime2){
			
			$dRef = $dT1->modify($mdfMeta);
			
		}elseif($mdfMeta && $refDateTime2){
			
			$dT2 = new \DateTime($refDateTime2);
			$dRef = $dT1->diff($dT2);
			$isDiff = true;
			
		}
		
		//$ret = ($meta && !$refDateTime2)? $ret->modify($meta) : (($meta && $diff)? $ret->diff($diff): $ret);
		
		if($xActiveYearFmt){
			
			$fmt = ($dToday->format('Y') == $dRef->format('Y'))? $xActiveYearFmt : $fmt;
			
		}
		
		///force FMT to conform to diff()(for some reason raw fmt(e.g d-y-m) does'nt work with result from diff() method)
		//fmt using either %Y = years,%m = months,%a = days, %H or %h,%i,%s
		isset($isDiff)? ($fmt = preg_replace("#([a-z])+#i", "%$1", $fmt)) : '';
		$ret = $dRef->format($fmt);
		
		if($cmpRef){
			
			$ret = ($dRef->format($fmt) == $cmpRef->format($fmt))? true : false;
			
		}	
		 
		return $ret;
		
	}


	
	


	
	
	
	/*** Method for fetching ip address ***/
	public function get_ip(){
		
		$ip = '';
		
		/*NOTE: This conditional flow arrangement is very important 
		HTTP_FORWARDED should be checked first before other FORWARDED variant
		REMOTE_ADDR should be last fallback since it's almost never empty
		*/		
		
        if(isset($_SERVER[($k = 'HTTP_CLIENT_IP')]) && $_SERVER[$k])
            $ip = $_SERVER[$k];
		
        elseif(isset($_SERVER[($k = 'HTTP_FORWARDED')]) && $_SERVER[$k])
			$ip = $_SERVER[$k]; 
			
        elseif(isset($_SERVER[($k = 'HTTP_X_FORWARDED_FOR')]) && $_SERVER[$k])
			$ip = $_SERVER[$k];
			
        elseif(isset($_SERVER[($k = 'HTTP_X_FORWARDED')]) && $_SERVER[$k])
			$ip = $_SERVER[$k];
			
        elseif(isset($_SERVER[($k = 'HTTP_FORWARDED_FOR')]) && $_SERVER[$k])
			$ip = $_SERVER[$k];
			
        elseif(isset($_SERVER[($k = 'REMOTE_ADDR')]) && $_SERVER[$k])
			$ip = $_SERVER[$k];
        
		//HTTP_FORWARDED on Google Chrome V71.0.3578.99 prepends 'for=' so we let's filter off non-ip xters
        return preg_replace("#[^\.0-9]+#", '', $ip);
		
	}
	
	
	
	
	
	
	/*** Method for fetching ip by block number ***/
	public function get_ip_block($ip, $ret_nBlocks=2){
		
		$nBlock = ''; $sep = '.';
		
		if($ip){
			
			$ipArr = explode($sep, $ip);
			$tot_blocks = count($ipArr);
			$n = abs($ret_nBlocks);
			$fromEnd = (mb_strlen($n) == mb_strlen($ret_nBlocks))? false : true;
			$tot_i = ($tot_blocks - 1);
			$n = ($n > $tot_blocks)? $tot_blocks : $n;
			
			if($tot_blocks > 0){
				
				for(($i = $fromEnd? $tot_i : 0); ($fromEnd? ($i > $tot_i - $n) : ($i < $n)); ($fromEnd? $i-- : $i++)){
					
					$nBlockArr[] = $ipArr[$i];
					
				}
				
				if(isset($nBlockArr)){
					
					$nBlockArr = $fromEnd? array_reverse($nBlockArr, false) : $nBlockArr;
					$nBlock = implode($sep, $nBlockArr);
					
				}
			}
		}
		
		return $nBlock;
		
	}
	
	
	
	
	/*** Method for checking if string content is a url ***/
	public function str_is_url($str){

		return preg_match("#('http://'|'https://')?.+\..{1,}$#i", $str);

	}
	
	
	/*** Method for fetching page/path slug ***/
	public function get_page_path($type='page_url', $len="", $noqstr=false, $noXtsn=false){
		
		$current_page="";$ret_arr=$acc_arr=array();
		$getDomain = $this->get_domain();
		$i_n_arr = explode(':', $len);
		$i = isset($i_n_arr[0])? $i_n_arr[0] : '';
		$n = isset($i_n_arr[1])? $i_n_arr[1] : '';
		$len = $n? $n : $i;
		$i = ($i && $n)? $i : 0;
		$type = trim(strtolower($type));
		$sep = '/';
		$rURL = $_SERVER["REQUEST_URI"];
		$rURL = $noqstr? preg_replace("#\?.*#", "", $rURL) : $rURL;
		
		if($noXtsn && stripos($rURL, '.') !== false){
			
			$docExtArr = array('phtml','php','php3','php4','asp','aspx','axd','asx','asmx','ashx',
			'html','htm','xhtml','jhtml','rhtml','shtml','jsp','jspx');
			$post_rURL = stristr($rURL, '.', false);
			$pre_rURL = stristr($rURL, '.', true);
			$post_rURLArr = explode($sep, $post_rURL);
			
			if(in_array(strtolower(ltrim(current($post_rURLArr), '.')), $docExtArr)){
				
				array_shift($post_rURLArr);
				$post_rURL = (!empty($post_rURLArr)? $sep : '').implode($sep, $post_rURLArr);
				$rURL = $pre_rURL.$post_rURL;
				
			}
		}
		
		if(in_array($type, array('page_url', 'rel_page_url', 'all'))){
			
			$pageUrl = trim($rURL, $sep);					
			$request_url_arr = explode($sep, $pageUrl);
			
			if(is_array($request_url_arr)){
				
				for($idx=$i; $idx < count($request_url_arr); $idx++){
					
					if(isset($request_url_arr[$idx]))
						$acc_arr[] = $request_url_arr[$idx];
					
					if(($idx + 1) == $len)
						break;
					
				}
				
				$current_page = implode($sep, $acc_arr);
				$ret_arr[] = $current_page;
				
				if(in_array($type, array('rel_page_url', 'all'))){
					
					if($current_page){
						
						$current_page = $sep.$current_page;
						$ret_arr[] = $current_page;
						
					}else
						$ret_arr[] = '';
					
				}
			}
		}	
		
		if(in_array($type, array('http_url', 'all'))){
			
			$current_page = urlencode($getDomain.$rURL);		
			$ret_arr[] = $current_page;
			
		}	
		
		if(!$current_page)
			$current_page = $_SERVER['HTTP_HOST'];
		
		if($type == 'all')
			return $ret_arr;
		else
			return $current_page;
		
	}
	

	
	
	
	
	
	/*** Method for fetching client user agent ***/
	public function get_user_agent(){
		 			 	
		return self::get_global_var();
	
	}
		
	
	
	
	
	
	
	
	
	/*** Method for checking if a client browser is requesting desktop version of a web page using the user agent ***/
	public function ua_desktop_request(){
		 			 	
		return (stripos($this->get_user_agent(), 'x11; Linux x86_64') !== false);
	
	}
		
	
	
	
	
	/*** Method for validating client browser ***/
	public function validate_browser($browserKey='FireFox'){
		
		$ua = self::get_global_var();
		$browserKey = strtolower($browserKey);
		
		$browsers = array(
			// @reference: https://developers.google.com/chrome/mobile/docs/user-agent
			$K='Chrome'          => $K/*'\bCrMo\b|CriOS|Android.*Chrome/[.0-9]* (Mobile)?'*/,
			$K='Dolfin'          => $K/*'\bDolfin\b'*/,
			$K='Opera'           => $K/*'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR/[0-9.]+|Coast/[0-9.]+'*/,
			$K='Skyfire'         => $K,
			'IE'              => 'IEMobile'/*'IEMobile|MSIEMobile'*/, // |Trident/[.0-9]+
			$K='FireFox'         => $K/*'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile'*/,
			$K='Bolt'            => $K,
			$K='TeaShark'        => $K,
			$K='Blazer'          => $K,
			// @reference: http://developer.apple.com/library/safari/#documentation/AppleApplications/Reference/SafariWebContent/OptimizingforSafarioniPhone/OptimizingforSafarioniPhone.html#//apple_ref/doc/uid/TP40006517-SW3
			$K='Safari'          => $K/*'Version.*Mobile.*Safari|Safari.*Mobile'*/,
			// @ref: http://en.wikipedia.org/wiki/Midori_(web_browser)
			$K='Midori'          => $K,
			$K='Tizen'           => $K,
			$K='UCBrowser'       => $K/*'UC.*Browser|UCWEB'*/,
			// @ref: https://github.com/serbanghita/Mobile-Detect/issues/7
			$K='DiigoBrowser'    => $K,
			// http://www.puffinbrowser.com/index.php
			$K='Puffin'            => $K,
			// @ref: http://mercury-browser.com/index.html
			$K='Mercury'          => $K/*'\bMercury\b'*/,
			// @reference: http://en.wikipedia.org/wiki/Minimo
			// http://en.wikipedia.org/wiki/Vision_Mobile_Browser
			'GenericBrowser'  => 'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger'
		);
		
		//CHANGE KEYS TO LOWERCASE
		$browsers = array_change_key_case($browsers);
		
		return (isset($browsers[$browserKey])? (stripos($ua, $browsers[$browserKey]) !== false) : false);
		
	}
	
	
	
	
	
	
	/*** Method for validating client operating system ***/
	public function validate_os($osKey='AndroidOs'){
		
		$ua = $this->get_user_agent();
		$osKey = strtolower($osKey);
		
		$operatingSystems = array(
			
			'AndroidOS'         => 'Android',
			'BlackBerryOS'      => 'blackberry'/*'blackberry|\bBB10\b|rim tablet os'*/,
			'PalmOS'            => 'PalmOS'/*'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino'*/,
			'SymbianOS'         => 'Symbian'/*'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b'*/,
			// @reference: http://en.wikipedia.org/wiki/Windows_Mobile
			'WindowsMobileOS'   => 'Windows Mobile'/*'Windows CE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window Mobile|Windows Phone [0-9.]+|WCE;'*/,
			// @reference: http://en.wikipedia.org/wiki/Windows_Phone
			// http://wifeng.cn/?r=blog&a=view&id=106
			// http://nicksnettravels.builttoroam.com/post/2011/01/10/Bogus-Windows-Phone-7-User-Agent-String.aspx
			'WindowsPhoneOS'   => 'Windows phone'/*'Windows Phone 8.0|Windows Phone OS|XBLWP7|ZuneWP7'*/,
			'Windows'   => 'Windows'/*'Windows Phone 8.0|Windows Phone OS|XBLWP7|ZuneWP7'*/,
			'iOS'               => 'iphone'/*'\biPhone.*Mobile|\biPod|\biPad'*/,
			'MeeGoOS'           => 'MeeGo',
			'MaemoOS'           => 'Maemo',
			'JavaOS'            => 'J2ME'/*'J2ME/|\bMIDP\b|\bCLDC\b'*/,
			'webOS'             => 'webOS'/*'webOS|hpwOS'*/,
			'badaOS'            => 'Bada'/*'\bBada\b'*/,
			'BREWOS'            => 'BREW'
		);
		
		//CHANGE KEYS TO LOWERCASE
		$operatingSystems = array_change_key_case($operatingSystems);
		
		return (isset($operatingSystems[$osKey])? (stripos($ua, $operatingSystems[$osKey]) !== false) : false);
		
	}
	

	
	
	
	
	
	/*** Method for returning error message when maximum file upload size limit is exceeded ***/
	public function get_large_upload_limit_error(){
		
		if(isset($_SERVER["REQUEST_METHOD"]) && strtolower($_SERVER["REQUEST_METHOD"]) === "post" &&
				empty($_POST) && empty($_FILES) && isset($_SERVER["CONTENT_LENGTH"]) ){
					
			$max_post_size = ini_get("post_max_size");
			$content_length = ceil($_SERVER["CONTENT_LENGTH"]/(1024 * 1024));
			
			if($content_length > $max_post_size){
				 
				return '<span class="alert alert-danger"><b>Sorry the file you are trying to upload is too large. <br/>Please limit to the required standards and try again.</b></span>';
		 
			}else
				return '';
					
		}	
		
		
	}
	
	
	
	
	
	
	/*** Method for fetching HTTP host ***/
	public function get_http_host(){
		
		return $_SERVER["HTTP_HOST"];
		
	}
	
	
	/*** Method for fetching site domain address ***/
	public function get_domain($retArr=false){
		
		$host = $this->get_http_host();
		$domain = 'http'.($this->is_secured_protocol()? 's' : '').'://'.$host;
		
		return ($retArr? array($domain, $host) : $domain);
		
	}
	
	
	
	
	
	/*** Method for extracting site domain name ***/
	public function extract_domain_name($str, $hasSubDomain = false){
		
		$urlDelimeterPos = strpos($str, '://');

		if($urlDelimeterPos){

			$urlPart = mb_substr($str, $urlDelimeterPos + 3);
			$tmpArr = explode('.', $urlPart);
			
			if(strtolower(current($tmpArr)) == 'www' || $hasSubDomain)
				array_shift($tmpArr);
				
			return array_shift($tmpArr);

		}

		return '';
		
	}
	
	
	
	
	
	
	/*** Method for checking if request protocol used is secured(HTTPS) ***/
	public function is_secured_protocol(){
		
		$https=false;
		
		if((isset($_SERVER[$K="HTTPS"]) && !empty($_SERVER[$K]) && strtolower($_SERVER[$K]) != "off")
			|| (isset($_SERVER[$K="SERVER_PORT"]) && $_SERVER[$K] == 443))
			$https = true;
			
		elseif((isset($_SERVER[$K="HTTP_X_FORWARDED_PROTO"]) && !empty($_SERVER[$K]) && strtolower($_SERVER[$K]) == "https")
				|| (isset($_SERVER[$K="HTTP_X_FORWARDED_SSL"]) && !empty($_SERVER[$K]) && strtolower($_SERVER[$K]) == "on"))
			$https = true;
				
		return $https;
		 
	}	 
	
	



	/*** Method for checking if a url string is prefixed with a protocol ***/
	public function has_protocol_prefix($url, $match = ''){

		$protocolPrefix = trim(mb_stristr($url, ':', true));
		
		if($match)
			return (strtolower($protocolPrefix) == strtolower($match));
		
		return in_array($protocolPrefix, self::$urlProtocolArr);

	}




	
	/*** Method for voice speech using OS SAPI VOICE technology ***/
	public function speak($word2Speak){

		/*
			Attempt to load the required extension for speech;
			[COM_DOT_NET]
			extension=php_com_dotnet.dll
		*/
		
		$voice = (new \COM('SAPI.SpVoice'))->speak($word2Speak);
		

	}





	
	/*** Method for prepending HTTP protocol to a url string ***/
	public function add_http_protocol($url, $anchor=true, $external=true){
	
		$url = trim($url);
		$urlNameValArr = explode(self::$hrefUrlTxtSep, $url);
		$url = isset($urlNameValArr[1])? $urlNameValArr[1] : $urlNameValArr[0];
		$urlText = isset($urlNameValArr[0])? $urlNameValArr[0] : $url;
							
		if(!$this->has_protocol_prefix($url))
			$url = "http://".$url;
	
		return ($anchor? '<a class="links" href="'.$url.'" '.($external? ' target="_blank" rel="nofollow noopener"' : '').' >'.$urlText.'</a>' : $url);
	
	}







	
	/*** 
	 * Method for dynamic loading of php extensions and libraries 
	 * @param $n => extension name
	 * @param $n2 => extension other name
	 * ***/
	public function load_lib($n, $n2 = null){				

		if(!extension_loaded($extName)){	

			/*
				php ver >= 5.3.0  has dl() disabled by default due to performance issues
				Meaning we can not load extensions dynamically 
				so we check if the function dl() exist first
			*/
			if(function_exists('dl')){

				if(!dl(((PHP_SHLIB_SUFFIX === 'dll')? 'php_' : '').($n2? $n2 : $n).'.'.PHP_SHLIB_SUFFIX))
					die('Failed to load extension '.$extName.' dynamically');

			}else
				die('Your php version can not load extensions dynamically');
						
		}			


	}



	
	/*** Method for auto loading PHP classes and traits ***/
	public static function autoload_classes($lookUpBaseDirArr = array(), $myDataTypeArr = array()){
		
		$lookUpBaseDirArr = (array)$lookUpBaseDirArr;

		/*
			NOTE: To explicitly specify the class Base Namespace 
			use the following syntax when specifying the $lookUpBaseDir:
			namespace::path2File
			e.g
			'PHPMailer/PHPMailer::'plugins/PHPMailer/src/'


		*/

		///AUTOLOAD NECESSARY CLASSES
		spl_autoload_register(function($className) use ($lookUpBaseDirArr) {

			$debug = 0; //change to true or 1 to echo out debug infos

			$fileExist = false;
			$myDataTypesPathAcc = array();
			$dirSep = '/';
			$namespaceSep = '\\';
			empty($myDataTypeArr)? ($myDataTypeArr = array('', 'class', 'trait', 'handler')) : '';

			
			if($debug)
				echo 'class Name Passed >>> '.$className.'<br/>';

			foreach($lookUpBaseDirArr as $lookUpBaseDir){

				$classBaseNamespace = '';
				
				$lookUpBaseDirArr = explode("::", $lookUpBaseDir);

				if(count($lookUpBaseDirArr) > 1){

					$classBaseNamespace = isset($lookUpBaseDirArr[0])? $lookUpBaseDirArr[0] : '';
					$lookUpBaseDir = isset($lookUpBaseDirArr[1])? $lookUpBaseDirArr[1] : '';


				}else{

					$lookUpBaseDir = isset($lookUpBaseDirArr[0])? $lookUpBaseDirArr[0] : '';

				}

				//Implicitly check for namespace if not specified
				if(!$classBaseNamespace){

					$classNameArr = explode($namespaceSep, $className);
					
					if(count($classNameArr) >= 4){

						$classBaseNamespace = implode($dirSep, array_slice($classNameArr, 0, 2));
						$className = implode($dirSep, array_slice($classNameArr, 2));

					}else{

						$className = array_pop($classNameArr);
						$classBaseNamespace = implode($dirSep, array_slice($classNameArr, 0, 2));

					}

					if($debug)
						echo 'class Base Namespace >>> '.$classBaseNamespace.'<br/>';

				}

				$className = trim(str_replace(array($namespaceSep, $classBaseNamespace), array($dirSep, ''), $className), $dirSep);
				
				if($debug)
					echo 'class Name >>> '.$className.'<br/>';

				foreach($myDataTypeArr as $myDataType){
					
					$myDataTypesFilePath = $lookUpBaseDir.$className.($myDataType? '.'.$myDataType : '').'.php';
					array_push($myDataTypesPathAcc, $myDataTypesFilePath);
					
					if(file_exists($myDataTypesFilePath)){

						require_once($myDataTypesFilePath);
						
						$fileExist |= true;
						
						if($debug)
							echo 'class Path >>> '.$myDataTypesFilePath.'<br/>';				

					}

				}

			}
			
			if(!$fileExist)
				die('SPL_AUTOLOAD_REGISTER FAILED<br/> FILE PATH(S) COULD NOT BE RESOLVED USING ANY OF MY CUSTOM DATA TYPES<br/> ATTEMPTED FILE PATH(S):<br/>'.implode('<br/>', $myDataTypesPathAcc));
			
		});
	
	}







	
}



?>