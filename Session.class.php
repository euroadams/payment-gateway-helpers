<?php


class Session{
	
	/*** Session member variables ***/
	/*********************
	JKOK = JESUS KING OF KINGS
	*********************/
	private $authenticated = false;
	private $sessUsername = null;
	private $sessUserId = null;
	private static $dbSessionLifetime = 172800; // 2 days (in seconds)
	private $message = array();
	private $timeoutMssg = 'Session Timeout ! <br/> Please login again !';	
	private static $sessionMessageKey = 'SESS_MSG_KEY';
	private static $messageRdrKey = 'SESS_MSG_RDR_KEY';
	private static $sessName = 'SESS_JKOK';
	private static $useOnlyCookies;		
	private static $rdrUrl;	
	
	/*** Generic member variables ***/
	protected $DBM;
	protected $ENGINE;
	
	
	
	/*** Constructor ***/
	public function __construct($sessName = '', $optionArr = array()){
		
		$this->_init_globals();
		
		$sessName? (self::$sessName = $sessName) : '';
		
		///PREFIX KEYS WITH SESSION NAME////
		self::$sessionMessageKey = self::$sessName.'_'.self::$sessionMessageKey;
		
		$DefaultLifetime = 604800; // 7 days(in seconds)
		
		// Get session parameters passed
		self::$useOnlyCookies = $useOnlyCookies = $this->ENGINE->is_assoc_key_set($optionArr, $K = "useOnlyCookies")? $this->ENGINE->get_assoc_arr($optionArr, $K) : true;
		$lifetime = $this->ENGINE->is_assoc_key_set($optionArr, $K = "lifetime")? $this->ENGINE->get_assoc_arr($optionArr, $K) : $DefaultLifetime;
		$path = $this->ENGINE->is_assoc_key_set($optionArr, $K = "path")? $this->ENGINE->get_assoc_arr($optionArr, $K) : '/';
		$domain = $this->ENGINE->is_assoc_key_set($optionArr, $K = "domain")? $this->ENGINE->get_assoc_arr($optionArr, $K) : '';
		$secure = $this->ENGINE->is_assoc_key_set($optionArr, $K = "secure")? $this->ENGINE->get_assoc_arr($optionArr, $K) : false;
		$httpOnly = $this->ENGINE->is_assoc_key_set($optionArr, $K = "httpOnly")? $this->ENGINE->get_assoc_arr($optionArr, $K) : true;
		$sameSite = $this->ENGINE->is_assoc_key_set($optionArr, $K = "sameSite")? $this->ENGINE->get_assoc_arr($optionArr, $K) : '';
		$sidLen = $this->ENGINE->is_assoc_key_set($optionArr, $K = "sidLen")? $this->ENGINE->get_assoc_arr($optionArr, $K) : 32;
		
		/* 
			Set session params in php.ini 
			NOTE: Session ini settings cannot be changed when a session is active
			so we check for active session first
		*/
		if(session_status() === PHP_SESSION_NONE){

			ini_set("session.use_strict_mode", 1); // Mandatory for general session security(see documentation on php.net for more)
			ini_set("session.gc_probability", 1); // Ensure garbage is collected
			ini_set("session.sid_length", $sidLen); // At least 32 chars recommended(see documentation on php.net for more)
			ini_set("session.use_only_cookies", $useOnlyCookies);
			ini_set("session.cookie_lifetime", $lifetime);
			ini_set("session.cookie_path", $path);
			ini_set("session.cookie_domain", $domain);
			ini_set("session.cookie_secure", $secure);
			ini_set("session.cookie_httponly", $httpOnly);
			ini_set("session.cookie_samesite", $sameSite); // possible values: Lax | Strict (or both)(see documentation on php.net for more)

		}
		// set session params also using session_set_cookie_params()
		/*
			NOTE: Attempting to change session_cookie_params when a session is already active 
			tends to throw an error in some versions of PHP. 
			hence, we test for the state of the session first
		*/
		
		if(session_status() === PHP_SESSION_NONE){
			
			if(PHP_VERSION_ID < 70300){
				
				session_set_cookie_params($lifetime, $path.'; samesite='.$sameSite, $domain, $secure, $httpOnly);
				
			}else{
				
				$paramArr = array("lifetime" => $lifetime, "path" => $path, "domain" => $domain, "secure" => $secure, "httponly" => $httpOnly, "samesite" => $sameSite);
				session_set_cookie_params($paramArr);
				
			}
			
		}
		
	}
	
	
	/*** Method for initializing global variables ***/
	public function _init_globals(){
		
		global $dbm, $ENGINE;
		
		$this->DBM = $dbm;
		$this->ENGINE = $ENGINE;
		
	}
	
	
	/*** Destructor ***/
	public function __destruct(){
		
		
	}
	

	
	
	/************************************************************************************/
	/************************************************************************************
									SESSION METHODS
	/************************************************************************************
	/************************************************************************************/
	
	/*** Method for setting folder path of session datas ***/
	public function setSessionSavePath($path=''){
		
		if($path)
			session_save_path(realpath($path));
		
		return session_save_path();
		
	}
	
	
	
	
	/*** Method for starting a session ***/
	public function startSession($rdrUrl = '/'){
		
		$rdrUrl? (self::$rdrUrl = $rdrUrl) : '';
		
		///IF USE ONLY COOKIES////
		if(self::$useOnlyCookies && !ini_get('session.use_only_cookies')){
			
			return false;
			
		}
		
		//START A SESSION IF NOT ALREADY STARTED//
		/*
			NOTE: Attempting to change session_name when a session is already active 
			tends to throw an error in some versions of PHP. 
			hence, we test for the state of the session first
		*/
		if(session_status() === PHP_SESSION_NONE){
		
			//SET THE SESSION NAME///
			session_name(self::$sessName);
			
			session_start();
			
		}
		
		return true;
		
	}
	
	
	
	
	/*** Method for fetching session id ***/
	public function getSessionId(){
		
		return session_id();
		
	}
	
	
	
	/*** Method for fetching the name of the current session ***/
	public function getSessionName(){
		
		return session_name();
		
	}		
	
	
	
	
	
	
	/*** Method for regenerating session id ***/
	public function regenerateSessionId($delOld=true){
		
		session_regenerate_id($delOld);
		
	}	
	
	
	
	
	
	
	
	/*** Method for fetching session cookie lifetime from php.ini ***/
	public function getPhpIniLifetime($toDays = false){
		
		$lifetime = ini_get("session.cookie_lifetime");
		$toDays? ($lifetime = ($lifetime / 86400)) : '';
		
		return $lifetime;
		
	}
	
	
	
	/*** Method for setting database session lifetime in seconds ***/
	public function setDbSessLifetime($seconds = 0){
		
		self::$dbSessionLifetime = ($seconds? $seconds : self::$dbSessionLifetime);
		
	}
	
	
	
	
	/*** Method for fetching database session lifetime ***/
	public function getDbSessLifetime(){
		
		return self::$dbSessionLifetime;
		
	}
	
	
	
	
	
	
	/*** Method for setting session username ***/
	private function setUsername($username){	
	
		$this->sessUsername = $username;
		
	}
	
	/*** Method for setting session user id ***/
	private function setUserId($userId){	
	
		$this->sessUserId = $userId;
		
	}
	
	
	
	
	
	
	/*** Method for fetching session username ***/
	public function getSessUsername(){	
	
		return $this->ENGINE->title_case($this->sessUsername);
		
	}
	
	/*** Method for fetching session user id ***/
	public function getSessUserId(){	
	
		return $this->sessUserId;
		
	}
	
	
	
	
	
	/*** Method for registering login session to database ***/
	public function registerLogin($loginMetaArr){
	
		if(session_status() == PHP_SESSION_ACTIVE){
			
			$this->setUsername($loginMetaArr["username"]);
			$this->setUserId($loginMetaArr["userId"]);
			
			/*
				Give a maximum of php.ini lifetime days to users that explicitly request to stay logged in 
				otherwise default to the value of the $dbSessionLifetime static variable
			
			*/
				
			$dbSessionLifetime = $this->ENGINE->get_assoc_arr($loginMetaArr, "stayLoggedIn")? $this->getPhpIniLifetime() : null;
			$this->setDbSessLifetime($dbSessionLifetime);
			
			$sql = "REPLACE INTO user_sessions (SESSION_ID, USER_ID, IP, USER_AGENT, AUTHENTICATION_CODE, LOGIN_DATE, EXPIRY_DATE) VALUES(?, ?, ?, ?, ?, NOW(), (NOW() + INTERVAL ? SECOND) )";
			$valArr = array($this->getSessionId(), $this->getSessUserId(), $this->ENGINE->get_ip(), $this->ENGINE->get_user_agent(), $this->getAuthenticationToken(), $this->getDbSessLifetime());
			$this->authenticated = $this->DBM->doSecuredQuery($sql, $valArr);
			
			
		}
		
	
	}
	
	
	
	
	
	
	
	
	/*** Method for unregistering login session from database ***/
	public function unregisterLogin($optionArr = array()){
		
		$currSessId = $this->getSessionId();
		$currSessUid = $this->getSessUserId();
		$sessId = $this->ENGINE->get_assoc_arr($optionArr, "sessId");
		$sessId? '' : ($sessId = $currSessId);
		$sessUid = $this->ENGINE->get_assoc_arr($optionArr, "sessUid");
		$sessUid? '' : ($sessUid = $currSessUid);
		$logoutAll = $this->ENGINE->is_assoc_key_set($optionArr, $K = "logoutAll")? $this->ENGINE->get_assoc_arr($optionArr, $K) : false;
		$logoutAll = ($logoutAll && $sessUid);

		if($logoutAll || $sessId){
			
			$sql = "DELETE FROM user_sessions WHERE (".($logoutAll? "USER_ID" : "SESSION_ID")." = ? )";
			$valArr = array($logoutAll? $sessUid : $sessId);
			
			if($this->DBM->doSecuredQuery($sql, $valArr) && $sessUid == $currSessUid && $sessId == $currSessId){
				
				$this->setUsername(null);
				$this->setUserId(null);
				$this->authenticated = false;
				
			}
			
			
		}
		
	
	}
	
	
	
	
	
	
	
	/*** Method for recalling returning user login session from database ***/
	public function recallLogin(){
		
		if(session_status() == PHP_SESSION_ACTIVE){
			
			$sql = "SELECT s.*, TIMEDIFF(EXPIRY_DATE, NOW()) TIME_LEFT_TO_EXPIRY, u.ID AS USER_ID, u.USERNAME 
					FROM user_sessions s JOIN users u ON s.USER_ID = u.ID WHERE SESSION_ID = ? LIMIT 1";
			$valArr = array($this->getSessionId());
			$stmt = $this->DBM->doSecuredQuery($sql, $valArr);
			$row = $this->DBM->fetchRow($stmt);
				
			if(!empty($row)){
				
				$this->setUsername($row["USERNAME"]);
				$this->setUserId($row["USER_ID"]);
				$loginAuthCode = $row["AUTHENTICATION_CODE"];
				
				// Do not allowed Ajax calls to extend or destroy session
				if($this->ENGINE->is_ajax())
					return true;
				
				list($hoursLeftToExpiry) = $this->ENGINE->split_time_str($row["TIME_LEFT_TO_EXPIRY"]);
				// An expired session will have negative hour time string
				$sessExpired = (strpos($hoursLeftToExpiry, '-') !== false);
				
				if(!$sessExpired && $this->validateToken($loginAuthCode)){
					
					//If a user return 3 days (72 hrs) or less before expiry date, then gracefully extend the expiry date
					if($hoursLeftToExpiry <= 72){
						
						$sql = "UPDATE user_sessions SET EXPIRY_DATE = (NOW() + INTERVAL ? SECOND) WHERE SESSION_ID=? LIMIT 1";
						$valArr = array($this->getDbSessLifetime(), $this->getSessionId());
						$this->DBM->doSecuredQuery($sql, $valArr);
						
					}
					
				}else{
					
					$this->destroy();
					$this->captureMessageAndRedirect($sessExpired);
					
				
				}
				
			}
			
			
		}
		
	
	}
	
	
	
	
	
	
	
	
	
	
	/*** Garbage collection method for deleting expired sessions from database (cron job) ***/
	public function collectGarbage(){
		
		$maxPerDbRow = $this->DBM->getMaxRowPerSelect();
		
		for($i=0; ; $i += $maxPerDbRow){
		
			$sql = "SELECT SESSION_ID, USER_ID FROM user_sessions WHERE EXPIRY_DATE < NOW() LIMIT ".$i.",".$maxPerDbRow;
			$valArr = array();
			$stmt = $this->DBM->doSecuredQuery($sql, $valArr, true);
			
			/////IMPORTANT INFINITE LOOP CONTROL ////
			if(!$this->DBM->getSelectCount())
				break;
						
			while($row = $this->DBM->fetchRow($stmt)){
				
				$this->unregisterLogin(array("sessId" => $row["SESSION_ID"]));
					
			}
		
		}
			
	
	}
	
	
	
	
	
	
	/*** Method for capturing messages into cookies before any redirection ***/
	public function captureMessageAndRedirect($sessExpired = false){
	
		if($sessExpired){	
		
			array_push($this->message, $this->timeoutMssg);
		
		}else
			array_push($this->message, 'oops! your session has been revoked. Please login again to re-validate it and continued surfing');
		
		$this->ENGINE->set_cookie(self::$sessionMessageKey, json_encode($this->message));
		$this->ENGINE->set_cookie(self::$messageRdrKey, true);
		
		if(($this->ENGINE->get_global_var('ck', self::$messageRdrKey) && !$this->ENGINE->is_ajax())){
			
			$this->ENGINE->set_cookie(self::$messageRdrKey, '', 1);
			
			header("Location:".self::$rdrUrl);
			exit();
			
		}
	
	}
	
	
	
	
	
	
	
	
	/*** Method for destroying session datas ***/
	public function destroy($optionArr = array()){
		
		$regen = $this->ENGINE->is_assoc_key_set($optionArr, $K = "regenerate")? $this->ENGINE->get_assoc_arr($optionArr, $K) : true;
		$delOld = $this->ENGINE->is_assoc_key_set($optionArr, $K = "delOld")? $this->ENGINE->get_assoc_arr($optionArr, $K) : true;
		$forceInvalidate = $this->ENGINE->is_assoc_key_set($optionArr, $K = "forceInvalidate")? $this->ENGINE->get_assoc_arr($optionArr, $K) : false;
		$logoutAll = $this->ENGINE->is_assoc_key_set($optionArr, $K = "logoutAll")? $this->ENGINE->get_assoc_arr($optionArr, $K) : false;
		
		$this->unregisterLogin(array("logoutAll" => $logoutAll));
		
		list($lifetime, $path, $domain, $secure, $httpOnly) = $this->ENGINE->get_cookie_params();
		setcookie(session_name(), '', 1, $path, $domain, $secure, $httpOnly);
		
		//EMPTY SESSION DATAS//
		$_SESSION = array();
		session_unset();
		
		///REGENERATE SESSION SO A NEW COOKIE WITH EMPTY DATAS IS SENT BACK TO CLIENT///
		if($regen)
			$this->regenerateSessionId($delOld);
		
		////NOW DESTROY THE EMPTY DATA SESSION////
		session_destroy();
		session_write_close();
		
		if($forceInvalidate)
			$this->captureMessageAndRedirect();
		
	}
	
	
		
	
	
	
	
	/*** Method for validating session token ***/
	public function validateToken($token){
		
		return ($token == $this->getAuthenticationToken());
		
	}
	
	
	
	
	
	/*** Method for fetching session authentication token ***/
	private function getAuthenticationToken(){
		
		//HTTP_ACCEPT seem to change on AJAX call so we can't use it
		$httpAccept = '';//$this->get_global_var('sv', 'HTTP_ACCEPT');
		
		/*HTTP_ACCEPT_CHARSET seems to be empty so we won't bother using it as 
		we can't ascertain if it returns fixed value*/
		$httpAcceptCharset = '';//$this->get_global_var('sv', 'HTTP_ACCEPT_CHARSET');
		
		$ip = $this->ENGINE->get_ip();			
		///USE ONLY FIRST TWO BLOCKS OF IP (INCASE OF PROXY)////
		$netMask = '255.255.0.0'; 
		$loose_ip = long2ip(ip2long($ip) & ip2long($netMask));
		$ua = $this->ENGINE->get_user_agent();
		$httpAcceptEncoding = $this->ENGINE->get_global_var('sv','HTTP_ACCEPT_ENCODING');
		$httpAcceptLang = $this->ENGINE->get_global_var('sv','HTTP_ACCEPT_LANGUAGE');
		$domainAuthCode = '_MY_HOPE_IS_ON_YOU_LORD_JESUS_KOK!_';
		$tk = $ua.$domainAuthCode.$loose_ip.'_'.$httpAccept.'_'.$httpAcceptCharset.'_'.$httpAcceptLang.'_'.$httpAcceptEncoding.'_'.self::$sessName;
		$authToken = hash('sha256', $tk, false);
	
		return $authToken;
		
	}

	
	
	
	
	
	
	
	
	/*** Method for setting active session restricted pages ***/
	public function activeSessionRestrictedPages($rpArr=array(), $sendBackUrl='/'){
		
		//LOGGED USER RESTRICTED PAGES//
		$rpArr = is_array($rpArr)? $rpArr : (array) $rpArr;
		$currPage = $this->ENGINE->get_page_path('page_url', '', true);
		
		if($this->getSessUserId() && in_array($currPage, $rpArr)){
			
			header('Location:'.$sendBackUrl);
			exit();
			
		}
			
	}
	
	
	
	
	
	
	/*** Method for setting session timeout message ***/
	public function setSessionTimeoutMessage($m){
		
		$this->timeoutMssg = $m;
		
	}
	
	
	
	
	
	
	
	
	/*** Method for fetching messages logged by this class ***/
	public function getMessage($echo=false, $pre='<span>', $suf='</span>'){
		
		$message = implode('', $this->message);
		$mCookie = json_decode($this->ENGINE->get_global_var('ck', self::$sessionMessageKey));///GET MESSAGE COOKIE///
		$mCookie = is_array($mCookie)? implode('', $mCookie) : $mCookie;
		$m = ($message? $message : ($mCookie? $mCookie : $message));
		$m = $m? $pre.$m.$suf : $m;
		
		if($mCookie && !$this->ENGINE->is_ajax()) $this->ENGINE->set_cookie(self::$sessionMessageKey, '', 1); ///UNSET MESSAGE COOKIE IF NOT SENT BY AJAX////
		
		if($echo) 
			echo $m;
		
		else
			return $m;
		
	}
	
	
	
	
	
	
	/*** Method for debugging session variables (dumps the entire datas in $_SESSION variable) ***/
	public function dump($ret=false){
		
		$sess =  '<pre>'.print_r($_SESSION, true).'</pre>';
		
		if($ret) 
			return $sess;
		
		else 
			echo $sess;
		
	}
	

	
	
	
}






?>