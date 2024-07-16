<?php


class Account extends Session{
	
	/*** Generic member variables ***/
	public $SESS;
	
	/*** Member variables for loading user from database ***/
	private $userId;
	private $loginAttemps;
	private $unlockLogin;
	private $firstName;
	private $lastName;
	private $fullName;
	private $email;
	private $phone;
	private $username;
	private $sanitizedUsernameSlug;
	private $password;
	private $aboutYou;
	private $signature;
	private $antiPhishingCode;
	private $registrationTime;
	private $activationStatus;	
	private $avatar;
	private $avatarUploadTime;	
	private $avatarBg;
	private $lastSeenTime;
	private $dailyVisitCounter;
	private $lastDailyVisitDate;
	private $nextDailyVisitDate;
	private $dailyPmCounter;
	private $emailDispatchMonitorDate;
	private $banCounter;
	private $gender;
	private $maritalStatus;
	private $location;
	private $dateOfBirth;
	private $website;
	private $facebook;
	private $twitter;
	private $instagram;
	private $linkedIn;
	private $whatsApp;	
	private $privilege;
	private $signatureVisibility;
	private $quotesMentionsLastViewTime;
	private $followedTopicsLastViewTime;
	private $followedSectionsLastViewTime;
	private $followedMembersLastViewTime;
	private $sharedPostsLastViewTime;
	private $upvotedPostsLastViewTime;
	private $downvotedPostsLastViewTime;
	private $avatarLikesLastViewTime;
	private $adCreditAvailable;
	private $adCreditUsed;
	private $adPremiumPurse;
	private $bannerCampaignStatus;
	private $textCampaignStatus;
	private $campaignNotification;
	private $floatingPageSkip;			
	private $maxPaging;	
	private $crossPageMultiQuote;	
	private $badgeViews;	
	private $showBadge;	
	private $reputation;		
	private $showPostAvatars;	
	private $showPostImages;		
	private $showAgeInBirthday;		
	private $ultimateLevel;	
	private $onlineStatus;	
	private $preferredThemeMode;	
	
	
	/*** Constructor ***/
	
	public function __construct(){
		
		$this->_init_globals();
		
		//Multiple Constructor Listener 
		
		$args = func_get_args();
		$numOfArgs = func_num_args();
		
		if(method_exists($this, ($funcName = '__construct'.$numOfArgs)))
			call_user_func_array(array($this, $funcName), $args);
		
	}
	
	
	
	
	/*** Main constructor for instantiating user account binded to session user ***/
	public function __construct0(){
		
		/*************************
		INSTANTIATE/START SESSION
		*************************/
		
		$SESS = new Session();

		/*****START A SESS****/

		//1 SET SESS SAVE PATH IF DESIRED///////

		//$SESS->set_session_save_path(dirname(DOC_ROOT).'/../session');

		//2 START THE SESS///
		$SESS->startSession();

		//3 RECALL LOGGED USER ON FLY///
		$SESS->recallLogin();

		//4 RESTRICT LOGGED USERS FROM CERTAIN PAGES/////
		if(!isset($_GET["_LGP_VER"]))##DON'T CHECK IF DOING LOGIN VERIFICATION; IT REDIRECTS TO HOME
			$SESS->activeSessionRestrictedPages(array('login', 'signup'));
			
		$this->SESS = $this->loadUser($SESS->getSessUsername(), false, false);
			
		
	}
	
	
	
	/*** Auxiliary constructor for instantiating user account binded to a specific user ***/
	public function __construct1($username){
		
		$this->loadUser($username, false, false);
		
	}
	
	
	
	
	
	/*** Destructor ***/
	public function __destruct(){
		
	}
	
	
	
	/*** Getters method for fetching user variables loaded from database ***/
	
	public function getUserId(){
		
		return $this->userId;
	
	}	
	
	public function getLoginAttempts(){
		
		return $this->loginAttempts;
		
	}	
	
	public function getUnlockLogin(){
	
		return $this->unlockLogin;
	
	}
		
	public function getFirstName(){
	
		return $this->firstName;
	
	}
		
	public function getLastName(){
	
		return $this->lastName;
	
	}
		
	public function getFullName(){
	
		return $this->fullName;
	
	}
		
	public function getEmail(){
	
		return $this->email;
	
	}	
	
	public function getPhone(){
	
		return $this->phone;
	
	}
	
	public function getUsername(){
	
		return ucwords(strtolower($this->username ?: ''));
	
	}	
	
	public function getPassword(){
	
		return $this->password;
	
	}
		
	public function getAboutYou(){
	
		return $this->aboutYou;
	
	}
		
	public function getSignature(){
	
		return $this->signature;
	
	}
		
	public function getAntiPhishingCode(){
	
		return $this->antiPhishingCode;
	
	}
		
	public function getRegistrationTime(){
	
		return $this->registrationTime;
	
	}	
	
	public function getActivationStatus(){
	
		return $this->activationStatus;
	
	}	
		
	public function getAvatar(){
	
		return $this->avatar;
	
	}
		
	public function getAvatarUploadTime(){
	
		return $this->avatarUploadTime;
	
	}
		
	public function getAvatarBg(){
	
		return $this->avatarBg;
	
	}	
	
	public function getLastSeenTime(){
	
		return $this->lastSeenTime;
	
	}
		
	public function getDailyVisitCounter(){
	
		return $this->dailyVisitCounter;
	
	}	
	
	public function getLastDailyVisitDate(){
	
		return $this->lastDailyVisitDate;
	
	}	
	
	public function getNextDailyVisitDate(){
		
		return $this->nextDailyVisitDate;
	
	}
	
	public function getDailyPmCounter(){
			
		return $this->dailyPmCounter;
	
	}	
	
	public function getEmailDispatchMonitorDate($evalDenial = true){
	
		$emailDispatchMonitorDate = $this->emailDispatchMonitorDate;
	
		if($evalDenial){
			
			list($d,$h,$m,$s) = $this->ENGINE->time_difference('', $emailDispatchMonitorDate, true);
	
			if($m <= 10)//IF IT'S BEEN ABOUT THIS MINS SINCE LAST EMAIL
				return true;
	
			return false;
			
		}
	
		return $emailDispatchMonitorDate;
	}
				
	public function getBanCounter(){
		
		return $this->banCounter;
	
	}	
	
	public function getSex(){
		
		return $this->gender;
	
	}	
	
	public function getMaritalStatus(){
	
		return $this->maritalStatus;
	
	}	
	
	public function getLocation(){
	
		return $this->location;
	
	}	
	
	public function getDateOfBirth(){
	
		return $this->dateOfBirth;
	
	}	
	
	public function getWebsite(){
	
		return $this->website;
	
	}	
	
	public function getFacebook(){
	
		return $this->facebook;
	
	}	
	
	public function getTwitter(){
	
		return $this->twitter;
	
	}	
	
	public function getInstagram(){
	
		return $this->instagram;
	
	}	
	
	public function getLinkedIn(){
	
		return $this->linkedIn;
	
	}	
	
	public function getWhatsapp(){
	
		return $this->whatsApp;
	
	}		
	
	public function getPrivilege(){
	
		return $this->privilege;
	
	}	
	
	public function getReputation(){
	
		return $this->reputation;
	
	}				
	
	public function getUltimateLevel($ret_dbState = false){
		
		$ultimateLevel = $this->ultimateLevel;
	
		if($ret_dbState) 
			return $ultimateLevel;
		else
			return (($this->getUserPrivilege($this->getUsername(), MODERATOR) /*&& ($this->getReputation() >= ULTIMATE_MOD_REP)*/ && $ultimateLevel) || $this->isAdmin());
		
	}	
	
	public function getSignatureVisibility(){
	
		return $this->signatureVisibility;
	
	}	
	
	public function getQuotesMentionsLastViewTime(){
	
		return $this->quotesMentionsLastViewTime;
	
	}	
	
	public function getFollowedTopicsLastViewTime(){
	
		return $this->followedTopicsLastViewTime;
	
	}	
	
	public function getFollowedSectionsLastViewTime(){
	
		return $this->followedSectionsLastViewTime;
	
	}	
	
	public function getFollowedMembersLastViewTime(){
	
		return $this->followedMembersLastViewTime;
	
	}	
	
	public function getSharedPostsLastViewTime(){
	
		return $this->sharedPostsLastViewTime;
	
	}	
	
	public function getUpvotedPostsLastViewTime(){
	
		return $this->upvotedPostsLastViewTime;
	
	}	
	
	public function getDownvotedPostsLastViewTime(){
	
		return $this->downvotedPostsLastViewTime;
	
	}	
	
	public function getAvatarLikesLastViewTime(){
	
		return $this->avatarLikesLastViewTime;
	
	}	
	
	public function getAdCreditAvailable(){
		
		return $this->adCreditAvailable;
	
	}	
	
	public function getAdCreditUsed(){
	
		return $this->adCreditUsed;
	
	}	
	
	public function getAdPremiumPurse(){
	
		return $this->adPremiumPurse;
	
	}	
	
	public function getBannerCampaignStatus(){
	
		return $this->bannerCampaignStatus;
	
	}	
	
	public function getTextCampaignStatus(){
	
		return $this->textCampaignStatus;
	
	}	
	
	public function getCampaignNotification(){
	
		return $this->campaignNotification;
	
	}	
	
	public function getFloatingPageSkip(){
	
		return $this->floatingPageSkip;
	
	}					
	
	public function getMaxPaging(){
	
		return $this->maxPaging;
	
	}		
	
	public function getCrossPageMultiQuote(){
	
		return $this->crossPageMultiQuote;
	
	}		
	
	public function getBadgeViews(){
	
		return $this->badgeViews;
	
	}		
	
	public function getShowBadge(){
	
		return $this->showBadge;
	
	}		
	
	public function getShowPostAvatars(){
		
		return $this->showPostAvatars;
	
	}		
	
	public function getShowAgeInBirthday(){
	
		return $this->showAgeInBirthday;
	
	}		
	
	public function getOnlineStatus(){
		
		return $this->onlineStatus;
	
	}
	
	public function getPreferredThemeMode(){
		
		return $this->preferredThemeMode;
	
	}		
	
	public function getShowPostImages(){
	
		return $this->showPostImages;
	
	}		
		
	
	public function getUsernameSlug(){
	
		return $this->sanitizedUsernameSlug;
	
	}		
	
	public function isRegistered($param){
		
		$U = $this->loadUser($param);
		
		return ($U->getUsername() && $U->getFullName());
	
	}
	
	public function isAdmin($username=''){
		
		return $this->getUserPrivilege(($username? $username : $this->getUsername()), ADMIN);
	
	}
	
	public function isTopStaff(){
		
		return $this->getUltimateLevel();
	
	}	
		
	public function isStaff($username=''){
	
		return in_array($this->getUserPrivilege(($username? $username : $this->getUsername())), array(ADMIN, MODERATOR));
	
	}	
		
	public function isModerator($username=''){
	
		return $this->getUserPrivilege(($username? $username : $this->getUsername()), MODERATOR);
	
	}		
	
	
	/*** Method for updating user record in the database ***/
	public function updateUser($uid, $cols, $valArr=array()){
	
		$done = true;
		$U = $this->loadUser($uid);
		$uid = $U->getUserId();
		$valArr = !is_array($valArr)? (array)$valArr : $valArr;
		$valArr[] = $uid;	
		
		if($uid && $cols){
	
			///////////PDO QUERY////////
					
			$sql = "UPDATE users SET ".$cols." WHERE (ID=?) LIMIT 1";
			$valArr = $valArr;
			$done = $this->DBM->doSecuredQuery($sql, $valArr);
	
		}
	
		return $done;
	
	}
	
	


	
	
	
	
	/*** Method for toggling userId and username ***/
	public function memberIdToggle($param, $retId=false){
		
		$return="";
		
		if($param){
			
			$U = $this->loadUser($param);
			$id = $U->getUserId();
			$username = $U->getUsername();
			$return = $retId? $id : $this->ENGINE->title_case($username);			
							
		}
		
		return $return;

	}


	
	
	
	
	
	/*** Method for loading user details from database ***/
	public function loadUser($param, $retRow = false, $newInstance = true){
		
		$obj = $newInstance? new self() : $this; //can also use (new static)
		//$obj = $newInstance? new Account($param) : $this; //can also use (new static)
		
		$sql = 'SELECT *, CONCAT_WS(" ", FIRST_NAME, LAST_NAME) AS FULL_NAME, (LDVD + INTERVAL 1 DAY) AS NDVD  FROM users 
		WHERE ('.$obj->DBM->useStrLeadDigitIdField().'  OR EMAIL = ? OR (USERNAME = ? AND USERNAME != "")) LIMIT 1';
	
		$stmt = $obj->DBM->doSecuredQuery($sql, array($param, $param, $param), false);
		
		$row = $obj->DBM->fetchRow();
		$recFound = !empty($row);
		
		if($recFound){
		
			$obj->userId = $row["ID"];
			$obj->username = $row["USERNAME"];
			$obj->sanitizedUsernameSlug = $obj->sanitizeUserSlug($obj->getUsername());
			$obj->firstName = $row["FIRST_NAME"];
			$obj->lastName = $row["LAST_NAME"];
			$obj->fullName = $row["FULL_NAME"];
			$obj->email = $row["EMAIL"];
			$obj->phone = $row["PHONE"];
			$obj->password = $row["PASSWORD"];
			$obj->loginAttemps = $row["LOGIN_ATTEMPT"];
			$obj->unlockLogin = $row["UNLOCK_LOGIN"];
			$obj->aboutYou = $row["ABOUT_YOU"];
			$obj->signature = $row["SIGNATURE"];
			$obj->antiPhishingCode = $row["ANTI_PHISHING_CODE"];
			$obj->registrationTime = $row["TIME"];
			$obj->activationStatus = $row["CONFIRMED"];		
			$obj->avatar = $row["AVATAR"];
			$obj->avatarUploadTime = $row["AVATAR_UPLOAD_TIME"];		
			$obj->avatarBg = $row["AVATAR_BG"];
			$obj->lastSeenTime = $row["LAST_SEEN"];
			$obj->dailyVisitCounter = $row["DVC"];
			$obj->lastDailyVisitDate = $row["LDVD"];
			$obj->nextDailyVisitDate = $row["NDVD"];
			$obj->emailDispatchMonitorDate = $row["EDMD"];		
			$obj->banCounter = $row["BAN_COUNTER"];
			$obj->gender = $row["SEX"];
			$obj->maritalStatus = $row["MARITAL_STATUS"];
			$obj->location = $row["LOCATION"];
			$obj->dateOfBirth = $row["DOB"];
			$obj->website = $row["WEBSITE_URL"];
			$obj->facebook = $row["FACEBOOK_URL"];
			$obj->twitter = $row["TWITTER_URL"];
			$obj->instagram = $row["INSTAGRAM_URL"];
			$obj->linkedIn = $row["LINKEDIN_URL"];
			$obj->whatsApp = $row["WHATSAPP_URL"];		
			$obj->privilege = $row["USER_PRIVILEGE"];
			$obj->reputation = $row["REPUTATION"];		
			$obj->signatureVisibility = $row["SIGNATURE_VSIB"];
			$obj->quotesMentionsLastViewTime = $row["OLD_QM_COUNTER"];
			$obj->followedTopicsLastViewTime = $row["OLD_FT_COUNTER"];
			$obj->followedSectionsLastViewTime = $row["OLD_FS_COUNTER"];
			$obj->followedMembersLastViewTime = $row["OLD_FM_COUNTER"];
			$obj->sharedPostsLastViewTime = $row["OLD_SH_COUNTER"];
			$obj->upvotedPostsLastViewTime = $row["OLD_VTU_COUNTER"];
			$obj->downvotedPostsLastViewTime = $row["OLD_VTD_COUNTER"];
			$obj->avatarLikesLastViewTime = $row["OLD_AL_COUNTER"];
			$obj->adCreditAvailable = $row["ADS_CREDITS_AVAIL"];
			$obj->adCreditUsed = $row["ADS_CREDITS_USED"];
			$obj->adPremiumPurse = $row["ADS_PREMIUM_PURSE"];
			$obj->bannerCampaignStatus = $row["BANNER_CAMPAIGN_STATUS"];
			$obj->textCampaignStatus = $row["TEXT_CAMPAIGN_STATUS"];
			$obj->floatingPageSkip = $row["MPS_OPT"];
			$obj->campaignNotification = $row["CAMPAIGN_NTF"];				
			$obj->maxPaging = $row["MAX_PER_PAGE"];		
			$obj->crossPageMultiQuote = $row["XMQ_OPT"];		
			$obj->badgeViews = $row["MEMS_BADGE_OPT"];		
			$obj->showBadge = $row["BADGE_OPT"];		
			$obj->showPostAvatars = $row["POST_AVATARS"];		
			$obj->showPostImages = $row["POST_IMAGES"];				
			$obj->showAgeInBirthday = $row["SAIB"];				
			$obj->onlineStatus = $row["ONLINE_STATUS"];				
			$obj->preferredThemeMode = $row["PREFERRED_THEME_MODE"];				
			$obj->ultimateLevel = MODS_LOCKOUT? 0 : $row["ULTIMATE_LEVEL"];	
			
		}
		
		//Default to empty php object when no record is found.
		$obj = $recFound? $obj : new ObjectProtection(); // comment out this line to default to last initialized object when no record is found
		
		return ($retRow? $row : $obj);
		
	}








	/*** Method for customizing user avatar ***/
	public function getDp($user, $optArr=array()){

		$dp=$loc=$onlineIndicator="";

		global $GLOBAL_mediaRootAvt, $GLOBAL_mediaRootAvtXCL;

		$sessionUsername = $this->SESS->getUsername();
		$U = $this->loadUser($user);
		$loc = $U->getLocation();
		$user = $U->getUsername();
		$gender = $this->getGender($user, array('ret'=>'raw'));
		$isSess = (strtolower($user) == strtolower($sessionUsername));
		$title = ($isSess? 'My' : $user.'\'s').' Avatar';
		$anchor = $this->ENGINE->is_assoc_key_set($optArr, $K='anchor')? $this->ENGINE->get_assoc_arr($optArr, $K) : true;
		$url = $this->ENGINE->is_assoc_key_set($optArr, $K='url')? $this->ENGINE->get_assoc_arr($optArr, $K) : $user;
		$retArr = $this->ENGINE->is_assoc_key_set($optArr, $K='retArr')? $this->ENGINE->get_assoc_arr($optArr, $K) : false;
		$ipane = $this->ENGINE->is_assoc_key_set($optArr, $K='ipane')? $this->ENGINE->get_assoc_arr($optArr, $K) : true;
		$ipaneTop = $this->ENGINE->is_assoc_key_set($optArr, $K='ipaneTop')? $this->ENGINE->get_assoc_arr($optArr, $K) : true;
		$zoomCtrl = $this->ENGINE->is_assoc_key_set($optArr, $K='zoomCtrl')? $this->ENGINE->get_assoc_arr($optArr, $K) : false;
		$type = $this->ENGINE->get_assoc_arr($optArr, $K='type');
		$gcardClassic = (bool)$this->ENGINE->get_assoc_arr($optArr, 'gcardClassic');
		$avatarSize = $this->ENGINE->get_assoc_arr($optArr, 'avatarSize');
		$cardSize = $this->ENGINE->get_assoc_arr($optArr, 'cardSize');
		$bcls = $this->ENGINE->get_assoc_arr($optArr, 'bcls');
		$ocls = $this->ENGINE->get_assoc_arr($optArr, 'ocls');
		$icls = '_hv-react '.$this->ENGINE->get_assoc_arr($optArr, 'icls');
		$iAttr = $this->ENGINE->get_assoc_arr($optArr, 'iAttr');
		$zoomCtrl? ($icls = 'img-responsive zoom-ctrl') : '';
		($ipane && $ipaneTop)? ($ocls = 'ipane-top '.$ocls) : '';
		$wrpEl = $anchor? 'a' : 'div';
		$ocls .= ($cardSize? ' card-'.$cardSize : '');
		
		$acard = $gcard =  $vcard = false;
		
		$onlineNow = ($this->getUserActiveStatus($U->getUserId()) || $isSess);
		
		if($onlineNow){
			
			$onlineIndicator = '<span class="has-online-bullet" title="online now"></span>';
			
		}
		
		
		
		switch(strtolower(trim($type))){
			
			case 'gcard': $gcard = true; break;
			
			//	case 'acard': $acard = true; break;
			
			case 'vcard': $vcard = true; 
				$ocls = 'vcard-avatar '.$ocls; 
				break;
			
			default : //get plain;
			
		}
		
		
		if(!$avatarSize && !$cardSize && $vcard){
			
			$avatarSize = 'sm';
			
		} 
		
		$avatarSize? ($avatarSize = ' avatar-'.$avatarSize) : '';
		
		//PDO QUERY///////////
		
		$sql = "SELECT AVATAR FROM users WHERE USERNAME LIKE ? AND AVATAR != '' LIMIT 1";
		$valArr = array($user);		
		
		$avatarFound = $rawAvatarFile = $dp = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();
		
		
		$mediaRootAvt = $GLOBAL_mediaRootAvt;
		$lmk = $loc? '<i class="fas fa-map-marker"></i> ' : '';
		$dropPan = ($vcard && $ipane && $user)? '<span class="ipane">'.$user.'<br/>'.$lmk.$loc.'</span>' : '';
		
		if($dp && file_exists($GLOBAL_mediaRootAvtXCL.$dp)){
			
			$dp = '<img title="'.$title.'" class="'.$icls.'" alt="'.$title.'" src="'.$mediaRootAvt.$dp.'" '.$iAttr.' />'.$dropPan; 
			
		}else{
			
			$title = 'Default '.(($gender == "m")?	'Male ' : (($gender == "f")? 'Female ' : 'System ')).'Avatar';
			$imgSuf = $gender? '_'.$gender : '';			
			$rawAvatarFile = 'default_avatar'.$imgSuf.'.png';
			$dp = '<img title="'.$title.'" class="'.$icls.'" alt="'.$title.'" src="'.$mediaRootAvt.$rawAvatarFile.'" '.$iAttr.' />'.$dropPan;			
			$avatarFound = false;
			
		}
		
		//$dp = '<'.$wrpEl.' class="'.$ocls.'" '.($anchor? 'href="/'.$url.'"' : '').' >'.$dp.'</'.$wrpEl.'>';
		//$dp = '<'.$wrpEl.' class="'.$ocls.'" '.($anchor? 'href="/'.$url.'"' : '').' ><span class="avatar-base">'.$dp.$onlineIndicator.'</span></'.$wrpEl.'>';
		$dp = '<div class="avatar-base '.$bcls.$avatarSize.($gcardClassic? '' : ' avatar-l').'">'.$onlineIndicator.'<'.$wrpEl.' class="'.$ocls.'" '.($anchor? 'href="/'.$url.'"' : '').' >'.$dp.'</'.$wrpEl.'></div>';
		
		($dp && $gcard)? ($dp = '<div class="gcard'.($gcardClassic? ' gcard-classic' : '').' _hv-react-base"><div class="gcard-item">'.$dp.'</div></div>') : '';	
		
		return ($retArr? array($dp, $avatarFound, $rawAvatarFile) : $dp);
			 
	}
	 





	/*** Method for customizing user gender ***/
	public function getGender($user, $optArr=array()){
		
		$arrAcc=array();
		
		$retType = isset($optArr[$k='ret'])? strtolower($optArr[$k]) : 'html';
		$clsXtnd = (isset($optArr[$k='cls']) && $v = $optArr[$k])? ' '.$v : '';
		$wip = isset($optArr[$k='wip'])? $optArr[$k] : false;//wrap in parenthesis(wip)
				
		$U = $this->loadUser($user);
		$sex = strtolower($U->getSex());
		
		if(!$sex)
			return $sex;
		
		$isM = ($sex == "m");
		$cls = $isM? 'm' : 'f';
		$titlePronoun = $isM? 'his' : 'her';
		$arrAcc[] = $sex;
		
		if(in_array($retType, array('html', 'all'))){
		
			$sex = '<span class="'.$cls.$clsXtnd.'">'.($wip? '(' : '').$sex.($wip? ')' : '').'</span>';
			$arrAcc[] = $sex;
			$arrAcc[] = $cls;
			$arrAcc[] = $isM;
			$arrAcc[] = $titlePronoun;
		
		}elseif($retType == 'pronoun')
			$sex = $titlePronoun;
		

		return (($retType == 'all')? $arrAcc : $sex);
		
	}


	 
	 


	
	/*** Method for fetching/verifying user privilege ***/
	public function getUserPrivilege($user, $verify=false, $appendSeal=false){
		
		$U = $this->loadUser($user);
		$userId = $U->getUserId();		
		
		$privilege = GUEST;
		$verified = false;
			
		if($userId){
			
			is_bool($verify)? '' : ($verify = strtoupper($verify));
			
			$U = $this->loadUser($user);
			$privilege = $U->getPrivilege();				
			
			//////DECRYPT PRIVILEGE///////////
			$privilege = $this->privilegeEncoderDecoder($privilege, $userId, true);
			
			if($privilege == ($K=ADMIN))
				$verified = ($verify == $K);
			
			elseif($privilege == ($K=MODERATOR))		
				$verified = ($verify == $K);
			
			else{
			
				$privilege = $K = MEMBER;
				$verified = ($verify == $K);
			
			}
			
			//////CONTROL MODS LOCKOUT/////
			
			$modsLockOut = (MODS_LOCKOUT && $privilege != ADMIN);
			
			if(MODS_LOCKOUT && $privilege != ADMIN)
				$privilege = MEMBER;
			
			if($appendSeal)
				$privilege = $privilege.$this->getUserSeal($userId);
			
			
		}
		
	
		return ($verify? $verified : $privilege);
	

	}
	 
	 
	
	
	 


	
	/*** Method for promoting user to new ranks ***/
	public function updatePrivilege($uid, $newRank=MODERATOR, $downgradeAdmin=false){	
		
		$updated = $downgradeAdmin;
		$notAdmin_n_updated = false;
		
		/////CHECK IF THE USER HAS A HIGHER PRIVELEGE LIKE ADMIN///

		if((!($isAdmin=$this->getUserPrivilege($uid, ADMIN)) && !$downgradeAdmin) || ($isAdmin && $downgradeAdmin)){
			
			$priv = $this->privilegeEncoderDecoder($newRank, $uid);
			$isAdmin = ($newRank == ADMIN)? 1 : 0;
			$cols =  "USER_PRIVILEGE=?,IS_ADMIN=?";
			$this->updateUser($uid, $cols, array($priv,$isAdmin));													
			$notAdmin_n_updated = true;
		}
		
		return $notAdmin_n_updated;
		
	}

	 


	
	
	/*** Method for customizing user online availability status ***/
	public function getUserActiveStatus($uid, $retIcon=false){
	
		$cBullet = 'circular-bullet';
		$U = $this->loadUser($uid);
		$lastSeen = $U->getLastSeenTime();
		$label = '<span class="flab">Currently: </span>';
	
		if($lastSeen && $U->getOnlineStatus()){
	
			list($d, $h, $m, $s) = $this->ENGINE->time_difference('', $lastSeen, true);
	
			if($m <= 3){///IF USER WAS LAST SEEN WITHIN 3MINS AGO THEN HE'S PROBABLY ONLINE
	
				if($retIcon)
					return '<span class="pill-follower">'.$label.'<i class="'.$cBullet.' bg-green"></i><span class="green">Online</span></span>';
			
				return true;
	
			}
	
			if($retIcon)
				return '<span class="pill-follower">'.$label.'<i class="'.$cBullet.' bg-red"></i><span class="red">Off-line</span></span>';
			
		}
	
		return false;
	
	}


 

 

 

 

	/*** Method for fetching user email conditionally ***/
	public function getUserEmail($username, $cond=""){

		$subqry="";
		
		$cond = strtolower($cond);
		
		if($cond == "campaign_ntf")
			$subqry = ' AND CAMPAIGN_NTF=1';
		
		//PDO QUERY//
		
		$sql = "SELECT EMAIL FROM users WHERE USERNAME=? ".$subqry."  LIMIT 1";
		$valArr = array($username);
		$email = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();
				
		return $email;
			 
	}
	
 
 

 

 

	
	/*** Method for encrypting user password ***/
	public function passwordEncrypt($pwd){
		
		$options = ['cost' => 8];
		$hashedPwd = password_hash($pwd, PASSWORD_DEFAULT, $options);
		
		return $hashedPwd;

	}

	
	
	
	/*** Method for validating user password ***/
	public function passwordVerify($pwd, $hash){
		
		return password_verify($pwd, $hash);
		
	}

	
	
	/*** Method for checking and doing user password rehash ***/
	public function checkRehashDoRehash($pwd, $hash, $sql){	
		
		$ret=true;
		
		$options = ['cost' => 8];
		if(password_needs_rehash($hash, PASSWORD_DEFAULT, $options)){
			
			$newHash = $this->passwordEncrypt($pwd);
			$valArr = array($newHash);
			$ret = $this->DBM->doSecuredQuery($sql, $valArr);
						
		}	
		
		return $ret;
	}


	
	
	
	/*** Method for encoding and decoding user privilege ***/
	public function privilegeEncoderDecoder($priv, $salt, $decode=false, $returnHash=false){	

		$salt? ($salt = $this->memberIdToggle($salt, true)) : '';//ENSURE SALT IS USER ID ONLY
		
		if($decode){
				
			switch($priv){
				
				case $this->privilegeEncoderDecoder(($K=MODERATOR), $salt, false, true): $priv = $K; break;
				
				case $this->privilegeEncoderDecoder(($K=ADMIN), $salt, false, true): $priv = $K; break;
				
				default: $priv = MEMBER;
				
			} 
				
			$priv = strtoupper($priv);
				
		}else{
				
			($salt && !$priv)? ($priv = MEMBER) : '';
			$secretKey = 'JKOK';
			$privCombination = $salt.$priv.$secretKey.$salt;
			$priv = hash('sha256', $privCombination, false);
				
		}
		
		return $priv;
				
	}



	

	


	
	/*** Method for fetching user ban status ***/
	public function getBanStatus($uid){
		
		////PDO QUERY//////
			
		$sql = "SELECT BAN_STATUS FROM spam_controls WHERE (USER_ID=? AND BAN_STATUS=1)";
		$valArr = array($uid);
		$spamBan = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();
				
		////PDO QUERY/////
			
		$sql = "SELECT ID FROM moderation_bans WHERE (BAN_DURATION >= NOW() AND  USER_ID=? AND BAN_STATUS=1)";	
		$modsBan = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();
		
		$isBanned = ($spamBan || $modsBan);
		
		return array($spamBan, $modsBan, $isBanned);
		
	}





	

	
	
	/*** 
	
	Method for sending visitors back to homepage (so they can see notice of ongoing crucial maintenance)
	when they try to access any page during active site maintenance
	NOTE: This method must not be called from the homepage itself to avoid infinite redirection loop
	
	***/
	public function authorizeSiteTakeDownAccess(){
		
		if(TAKE_DOWN_SITE && !$this->SESS->isAdmin()){
			
			header("Location:/");
			exit();		
			
		}
		
	}


	

	
	
	/*** Method for authorizing access to sensitive pages ***/
	public function authorizeTopAccess($staffsOnlyAccess=true, $trustedUsersAccess=false){
		
		$admin = $this->SESS->isAdmin();
		$mod = $this->SESS->isModerator();
		
		if(($staffsOnlyAccess && !$admin && !$mod) || (!$staffsOnlyAccess && !$admin && !$trustedUsersAccess)){
		
			header("Location:/");
			exit();	
					
		}
		
	}



	
	
	/*** Method for forcing session access to sensitive pages ***/
	public function forceSessAccess(){
	
		global $GLOBAL_rdr;
		
		if(!$this->SESS->getUserId()){
			
			header("Location:/login?_rdr=".$GLOBAL_rdr."#loginUsername");
			exit();
					
		}
		
	}








	
	/*** Method for sanitizing username slug ***/
	public function sanitizeUserSlug($slug, $optArr=array()){
	
		$genderHtml='';
	
		$sessionUsernameSlug = is_null($this->SESS)? $this->getUsernameSlug() : $this->SESS->getUsernameSlug();
		$anchor = isset($optArr[$k="anchor"])? $optArr[$k] : false;
		$appendXtn = isset($optArr[$k="appendXtn"])? $optArr[$k] : true;
		$gender = isset($optArr[$k="gender"])? $optArr[$k] : false;
		$genderProp = isset($optArr[$k="genderProp"])? $optArr[$k] : true;
		$validateSlug = isset($optArr[$k="validateSlug"])? $optArr[$k] : false;
		$cls = (isset($optArr[$k="cls"]) && $v = $optArr[$k])? ' '.$v : '';
		$wrapArr = (isset($optArr[$k="wrap"]) && $v = $optArr[$k])? explode('.', $v) : '';
		$wrap = isset($wrapArr[0])? $wrapArr[0] : '';
		$wrapCls = isset($wrapArr[1])? $wrapArr[1] : '';
		$youRef = isset($optArr[$k="youRef"])? $optArr[$k] : true;
		$isRel = isset($optArr[$k="isRel"])? $optArr[$k] : true;
		$titleCase = isset($optArr[$k="titleCase"])? $optArr[$k] : true;
		$urlText = isset($optArr[$k="urlText"])? $optArr[$k] : $slug;
		$urlAttr = isset($optArr[$k="urlAttr"])? $optArr[$k] : '';
		$rel = $isRel? '/' : '';
		$preUrl = isset($optArr[$k="preUrl"])? $optArr[$k] : '';
		$preUrl? ($preUrl .= '/') : '';
		$postUrl = isset($optArr[$k="postUrl"])? $optArr[$k] : '';
		$postUrl? ($postUrl = '/'.$postUrl) : '';
		//Sanitize Here
		$slugSan = $preTitle = strtolower($slug);
		$slugSan = $slugSan.($appendXtn? (USE_USER_URL_EXTENSION? DOC_EXTENSION : '') : '');
		
		if($validateSlug)
			return (($slugSan == $validateSlug)? array($slugSan, true) :  array($slugSan, false));
		
		$preTitle = $this->ENGINE->title_case($preTitle);
		$urlText = ($youRef && $sessionUsernameSlug && $sessionUsernameSlug == $slugSan)? 'You' : $urlText;
		$titleCase? ($urlText = $this->ENGINE->title_case($urlText)) : '';
		(strpos($urlAttr, 'title=') === false)? ($urlAttr .= 'title="'.$preTitle.'"') : 
		($urlAttr = preg_replace("#title=('|\")?#i", 'title="'.$preTitle.' | ', $urlAttr));
		$gender? (list($gender, $genderHtml, $genderCls) = $this->getGender($slug, array('ret'=>'all', 'wip'=>true, 'cls'=>($gender === true)? '' : $gender))) : '';
		$cls .= ($gender && $genderProp)? ' '.$genderCls : '';
		
		return ($anchor? ($wrap? '<'.$wrap.($wrapCls? ' class="'.$wrapCls.'"' : '').'>' : '').'<a class="links'.$cls.'" '.$urlAttr.' href="'.$rel.$preUrl.$slugSan.$postUrl.'">'.$urlText.'</a>'.$genderHtml.($wrap? '</'.$wrap.'>' : '') : $slugSan);
	
	}
		
	
	





	
	/*** Method for fetching customized Virtual user card ***/
	public function getUserVCard($user, $meta_arr=''){
		
		global $badgesAndReputations;
		
		$sessionUsername = $this->SESS->getUsername();
		$U = $this->loadUser($user);
		$user = $U->getUsername();
		$isSess = (strtolower($user) == strtolower($sessionUsername));
		$time = $this->ENGINE->get_assoc_arr($meta_arr, 'time');	
		$append2url = $this->ENGINE->get_assoc_arr($meta_arr, 'append2url');	
		$append = $this->ENGINE->get_assoc_arr($meta_arr, 'append');	
		$dateFmt = $this->ENGINE->get_assoc_arr($meta_arr, 'dateFmt');	
		$minVer = (bool)$this->ENGINE->get_assoc_arr($meta_arr, 'minVer');	
		!$dateFmt? ($dateFmt = 'jS M Y') : '';	
		$icls = $this->ENGINE->get_assoc_arr($meta_arr, 'icls');	
		$ocls = $this->ENGINE->get_assoc_arr($meta_arr, 'ocls');	
		$time = $time? $this->ENGINE->get_date_safe($time, $dateFmt, array('xActiveYearFmt'=>'M jS')) : '';
		$dp = $this->getDp($user, array('type'=>'vcard', 'ocls'=>$ocls, 'icls'=>$icls, 'ipane'=>!$minVer, 'cardSize'=>($minVer? 'xs' : '')));
		$userUrlAnchor = $this->sanitizeUserSlug($user, array('anchor'=>true, 'gender'=>true));
		$userSeal = $this->getUserSeal($user);
		$userBadges = $badgesAndReputations->loadUserBadges(array('uid'=>$user, 'medalsOnly'=>true));
		$unflwCls = strtolower($user).'-unfld';
	
		return $minVer? '<div class="pill-follower '.$unflwCls.'">'.$dp.$userUrlAnchor.'</div>' : '<div class="align-l clear text-ws-normal"><small class="prime-1">'.$time.'</small><div class="clear">'.$dp.$userUrlAnchor.$userSeal.$append2url.'</div>'.$userBadges.$append.'</div>';
		
	}


	




	
	/*** Method for fetching customized user seal ***/
	public function getUserSeal($u, $ret_arr=false){
		
		global $SITE;
		
		$seal="";
		
		$user = $this->loadUser($u);
		$username = $user->getUsername();	
		$admin = $user->isAdmin();
		$mod = $user->isModerator();
		$privilege = $user->getUserPrivilege($username);
		$modSeal = '<img '.STAFF_SEAL.' title="Moderator Seal" />';
		$adminSeal = '<img '.STAFF_SEAL.' title="Administrator Seal" />';
		$trustSeal = (($user->getUltimateLevel() || $admin)?  '<img '.TRUSTED_SEAL.' title="Trusted User Seal" />' : "");
		
		if($admin)
			$seal = $adminSeal.$adminSeal.$adminSeal;
			
		elseif($mod && !MODS_LOCKOUT){
		
			if($SITE->moderatedSectionCategoryHandler(array('uid'=>$username,'action'=>'isMod','level'=>2)))
				$seal = $modSeal.$modSeal.$trustSeal;	
							
			else
				$seal = $modSeal.$trustSeal;
					
		}
		
		if($ret_arr)
			return array($seal, $privilege);
		
		return $seal;
	}
	 

	 
	 
	


	
	/*** Method for fetching customized user signature ***/
	public function getUserSignature($username, $vsibChk=true, $incPriv=false, $sep=''){

		$signature="";
		
		list($modStar, $privilege) = $this->getUserSeal($username, true);
		
		//PDO QUERY//
		
		$sql = "SELECT SIGNATURE FROM users WHERE USERNAME=? ".($vsibChk? 'AND SIGNATURE_VSIB = 1' : '')." LIMIT 1";
		$valArr = array($username);
			
		$signature = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();
		
		if($signature || $modStar)
			$signature = '<div class="user-seal  post-body-ctrl">'.($incPriv? '<span class="prime">'.$privilege.'</span>' : '').$modStar.($signature? $sep : '').' '.$signature.'</div>';
				
		
		return $signature;
			 
	}








	
	
	
	/*** Method for comparing session ranks of users(very sensitive and vital to moderation) ***/
	public function sessionRanksHigher($rivalUsername){
		
		global $SITE;
		
		$sessRanksHigher = true; //Important: by default the session user is assumed to rank higher until proven otherwise	
		$sessUsername = $this->SESS->getUsername();
		$sessPrivilege = $this->getUserPrivilege($sessUsername);
		$sessReputation = $this->SESS->getReputation();
		$sessIsTopStaff = $this->SESS->getUltimateLevel();
		$sessIsSuperMod = $SITE->moderatedSectionCategoryHandler(array('uid'=>$sessUsername,'action'=>'isMod','level'=>2));
		
		if($rivalUsername){
		
			$rivalUser = $this->loadUser($rivalUsername);
			$rivalPrivilege = $rivalUser->getUserPrivilege($rivalUsername);
			$rivalReputation = $rivalUser->getReputation();
			$rivalIsTopStaff = $rivalUser->getUltimateLevel();
			$rivalUsername = $rivalUser->getUsername();
			$rivalIsSuperMod = $SITE->moderatedSectionCategoryHandler(array('uid'=>$rivalUsername,'action'=>'isMod','level'=>2));
			
		}else
			$rivalPrivilege = $rivalReputation = $rivalIsTopStaff = $rivalIsSuperMod = '';
			
		
		$admin = ADMIN;
		$moderator = MODERATOR;
		$member = MEMBER;
		
		if(strtolower($sessUsername) == strtolower($rivalUsername))
			return array(($sessRanksHigher = true), ($equal = false));
		
		if($sessPrivilege == $admin || $rivalPrivilege == $admin){
		
			if($sessPrivilege == $admin && in_array($rivalPrivilege, array($moderator, $member)))
				$sessRanksHigher = true;
		
			elseif(in_array($sessPrivilege, array($moderator, $member)) && $rivalPrivilege == $admin)
				$sessRanksHigher = false;	
			
			elseif($sessPrivilege == $admin && $rivalPrivilege == $admin){
				
				$sessRanksHigher = ($sessReputation > $rivalReputation);
				$equal = ($sessReputation == $rivalReputation);
		
			}
						
		}elseif($sessIsTopStaff || $rivalIsTopStaff){
			
			if($sessIsTopStaff && !$rivalIsTopStaff)
				$sessRanksHigher = true;
		
			elseif(!$sessIsTopStaff && $rivalIsTopStaff)
				$sessRanksHigher = false;
		
			elseif($sessIsTopStaff && $rivalIsTopStaff){
				
				$sessRanksHigher = ($sessReputation > $rivalReputation);
				$equal = ($sessReputation == $rivalReputation);
		
			}
			
		}elseif($sessPrivilege == $moderator || $rivalPrivilege == $moderator){
		
			if($sessPrivilege == $moderator && $rivalPrivilege == $member)
				$sessRanksHigher = true;
		
			elseif($sessPrivilege == $member && $rivalPrivilege == $moderator)
				$sessRanksHigher = false;
		
			elseif($sessIsSuperMod && !$rivalIsSuperMod)
				$sessRanksHigher = true;
		
			elseif(!$sessIsSuperMod && $rivalIsSuperMod)
				$sessRanksHigher = false;
		
			elseif(($sessPrivilege == $moderator && $rivalPrivilege == $moderator) || ($sessIsSuperMod && $rivalIsSuperMod)){
				
				$sessRanksHigher = ($sessReputation > $rivalReputation);
				$equal = ($sessReputation == $rivalReputation);
		
			}
			
		}elseif($sessPrivilege == $member || $rivalPrivilege == $member){
		
			$sessRanksHigher = ($sessReputation > $rivalReputation);
			$equal = ($sessReputation == $rivalReputation);
		
		}
		
		$equal = isset($equal)? $equal : false;
		
		return array($sessRanksHigher, $equal);
		
	}





	

	
	
	/*** Method for checking moderator session access ***/
	public function sessionAccess($meta_arr){
		
		global $FORUM;
		
		$sessUsername =  $this->SESS->getUsername();
		$sessUid = $this->SESS->getUserId();
		$tops = $this->SESS->isTopStaff();
		$mod = $this->SESS->isModerator();
		$id = $this->ENGINE->get_assoc_arr($meta_arr, "id");	
		$cond = $this->ENGINE->get_assoc_arr($meta_arr, "cond");	
		$isSid = $this->ENGINE->get_assoc_arr($meta_arr, "isSid");
		$cond = trim(strtolower($cond));
		$access = false;	
				
		$sid = ($id && !$isSid)? $FORUM->getTopicDetail($id) : $id;						
		$cid = $FORUM->getSectionField($sid);		
		
		if($sid){
			
			////RESTRICT MODERATORS AND RECYCLE BIN SECTION ACCESS TO STAFFS ONLY////////
			$staffsOnlySidArr = getExceptionParams('sidsstaffsonly');
			
			////PDO QUERY/////
			
			$sql =  "SELECT ID FROM moderators WHERE (USER_ID = ? AND ((SC_ID=? AND LEVEL=1) OR (SC_ID=? AND LEVEL=2))) LIMIT 1";
			$valArr = array($sessUid, $sid, $cid);
			$granted = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();
			
			if($cond == 'staffsonly'){
				
				$access = true;
				
				if(in_array($sid, $staffsOnlySidArr)){
					
					if(!$granted && !$tops && !$mod)
						$access = false;
					
				}	
				
			}elseif($granted || $tops)
				$access = true;									
			
		}			
				
		return $access;	
		
	}








	
	/*** Method for checking if it's user birthday ***/
	public function isUserBirthday($username=""){
		
		if($username)
			$userDob = $this->loadUser($username)->getDateOfBirth();
		
		else{
		
			$username = $this->SESS->getUsername();
			$userDob = $this->SESS->getDateOfBirth();
		
		}
		
		
		$ret = ($username && $this->ENGINE->get_date_safe("", "d-M", array('cmpRef'=>$userDob)));
		
		return $ret;
				
	}
	
	
	
	
	
	
	


	
	/*** Method for fetching birthday celebrants ***/
	public function getBirthdays($today=true, $retArr=true){
		
		$birthdays=""; $mCelbArr=$fCelbArr=array();
		
		$sql =  "SELECT USERNAME, SAIB, SEX, DOB, (YEAR(CURDATE()) - YEAR(DOB)) AS AGE,
				DATE_ADD(
					DOB, 
					INTERVAL IF(DAYOFYEAR(DOB) >= DAYOFYEAR(CURDATE()),
						YEAR(CURDATE()) - YEAR(DOB),
						YEAR(CURDATE()) - YEAR(DOB) /*+ 1*/
					) YEAR
				) AS BIRTHDAY
			FROM users 
			WHERE 
				DOB IS NOT NULL
			HAVING 
				BIRTHDAY ".($today? " = CURDATE()" : "BETWEEN (CURDATE() + INTERVAL 1 DAY) AND DATE_ADD(CURDATE(), INTERVAL 4 WEEK)" )."
			ORDER BY BIRTHDAY
			LIMIT 100";
			
		$valArr = array();
		$stmt = $this->DBM->doSecuredQuery($sql, $valArr);
		
		while($row = $this->DBM->fetchRow($stmt)){
			
			$age = $row["AGE"];
			$celb = $row["USERNAME"];
			$saib = $row["SAIB"];
			$dob = $row["DOB"];
			list($gender, $genderHtml, $genderCls, $genderIsM) = $this->getGender($celb, array('ret'=>'all'));
	
			for($i=0; $i<2; $i++){
	
				$vcard  = ($i == 1);
				$sep  = $vcard? '' : ' | ';
				$ageStr = ($age && $saib)? ($vcard? '<br/>' : ',').' <span class="prime-sc1">'.$age.($vcard? ' Years' : '').'</span>' : '';
				$dobStr = $vcard? $dob : ($today? '' : ' - <span class="lblack">'.$this->ENGINE->get_date_safe($dob, "jS M").'</span>');
	
				$tmpAcc = '<div class="inline-block'.($vcard? ' col-md-w-8' : '').'">'.
								($vcard? $this->getUserVCard($celb, array('time'=>$dobStr, 'append2url'=>$ageStr, 'dateFmt'=>'M jS', 'ocls'=>'vcard-md')) : $this->sanitizeUserSlug($celb, array('anchor'=>true, 'cls'=>$genderCls)).
									'(<span class="'.$genderCls.'">'.$gender.'</span>'.$ageStr.$dobStr.')'
								).'
							</div>'.$sep;
				if($vcard)
					$birthdaysArrVcard[] = $tmpAcc;
	
				else
					$birthdaysArr[] = $tmpAcc;
				
			}	
		}		
	
		
		$birthdays = isset($birthdaysArr)? trim(implode('', $birthdaysArr), " | ") : '';
		$birthdaysVcard = isset($birthdaysArrVcard)? implode('', $birthdaysArrVcard) : '';
		$birthdaysAcc = '<noscript><div class="panel-body">'.$birthdays.'</div></noscript><div class="panel-body sides-padless"><div class="slide-show allow-ov" data-auto-play="true"  data-adapt-white="true" data-scale-full="true" data-hover-pause="true" data-pager-numbers="false" data-pager-crumbs="true" data-speed="'.($today? 9000 : 7000).'" data-animate="slideInRight">'.$birthdaysVcard.'</div></div>';
		
		if($birthdays)
			$birthdays = '<div class="side-widget widget birthdays hide-overflow">'.
								($today? '<div class="panel panel-orange">
										<h1 class="panel-head page-title head-bg-classic-r"><img '.BIRTHDAY_ICON.' />  Birthdays<small>('.$this->ENGINE->get_date_safe("", "jS M").')</small></h1>'.$birthdaysAcc.'
									</div>' 
									: 
									'<div class="panel panel-gray">
										<h1 class="panel-head page-title head-bg-classic-r">Upcoming Birthdays</h1>'.$birthdaysAcc.'
									</div>'
								).
						'</div>'; 
						
		return $birthdays;
			
	}
	
	

	

	


	
	
	
	/*** Method for handling user account signup/registration request ***/
	public function handleUserAccountRegistrationRequest(){
		
		global $SITE, $siteName, $GLOBAL_rdr, $pageSelf, $mediaRootFav;
		
		/*****VARIABLE DECLARATION******/
		$regForm=$emailRqd=$dob=$sex=$dobDay=$dobMonth=$dobYear=
		$usernameLenErr=$pwdLenErr=$maleSelected=$femaleSelected=$spaceInPwd=$spaceInUsername=$usernameCharsErr=
		$maritalStatOpt=$regEmailForm=$alert=$newRegEmailLabelText=$pwdPatternErr=
		$blanks=$usernameRqd=$pwd1Rqd=$pwd2Rqd=$firstNameRqd=$lastNameRqd=$emailRqd=$pwdFieldMismatch=$blankEmail=
		$dusername=$dpassword=$dpassword2=$dfname=$dlname=$maritalStat=$username=$pwd1=$pwd2=$firstName=
		$lastName=$email=$hideRegForm=$emailFieldErr=$usernameFieldErr=$pwdFieldErr=$firstNameFieldErr=
		$lastNameFieldErr=$qstrAuthCode=$qstrEmail=$authCode="";

		$pwdMssg = $SITE->getMeta('valid-password-tip');
		$signUpPolicyAccept = '<div class="prime">By signing up You agree to our <a class="links sky-blue no-hover-bg" href="/policies" target="_blank">rules, policies & terms of service</a></div>';
		$eye = $SITE->getFA($KK='pointer red fas fa-eye', array('attr'=>($K='data-toggle-password-plain-target=').'"reg-pwd1"'));
		$eye2 = $SITE->getFA($KK, array('attr'=>$K.'"reg-pwd2"'));
		$asterix = '<span class="asterix">*</span>';
		$fErr = 'field-error';			

		if(isset($_GET[$K="email"]))
			$email = $this->ENGINE->sanitize_user_input($_GET[$K]);

		$newRegEmailLabelText = "E-mail";

		foreach(MARITAL_ARR as $mstat){
			
			if(isset($_POST[$K="marital_stat"]) && $_POST[$K] == $mstat)
				$maritalStatOpt .= '<option selected>'.$mstat.'</option>';
			else
				$maritalStatOpt .= '<option>'.$mstat.'</option>';
					
		}
			
		$maritalStatOpt = '<select id="mstat" class="field" name="marital_stat">'.$maritalStatOpt.'</select>';
			

		/***CLEAR ALL UNCOMPLETED REGISTRATIONS OLDER THAN 24HRS THAT HAS'NT CONFIRMED THEIR EMAIL
					AND THEIR LINKS HAS EXPIRED SO THAT THEY ARE ABLE TO REUSE SAME EMAIL 
								SHOULD THEY OPT TO START A NEW REGISTRATION****/

		//PDO QUERY///////
			
		$sql =  "DELETE FROM users WHERE (USERNAME='' AND (TIME + INTERVAL 24 HOUR) < NOW())";
		$this->DBM->query($sql);
	
		//////ON SUBMIT OF THE EMAIL FORM FIELD OR THE FULL FORM//////////

		if(isset($_POST['submit'])){
			
			$formSubmit = true;
			
			/////////HANDLE FOR PROCESSING THE EMAIL FORM FIELD ONLY//////

			if(isset($_POST["email_form_fields"])){
				
				$email=$authCode="";
				
				$email = $this->ENGINE->sanitize_user_input($_POST["email"]);
				
				$resendEmailCfmLink = '<a class="resend-code links" href="/resend_confirmation_code?email='.$email.'&_rdr='.$GLOBAL_rdr.'" data-email="'.$email.'">here</a>';
				
				if($email){
										
					/////CHECK IF THE EMAIL IS VALID/////	
					
					if(!$this->ENGINE->email_validate($email))
						$alert = '<span class="alert alert-danger">Sorry the email address you entered appears to be invalid! please try again</span>';
					/////CHECK IF THE EMAIL IS ALREADY REGISTERED/////														
					elseif(!$SITE->emailExist($email)){
													
						$authCode = $this->ENGINE->generate_token();												
							
						/////PDO QUERY///////
							
						$sql =  "INSERT INTO users (EMAIL, TIME) VALUES(?, NOW()) ";
						$valArr = array($email);
			
						//LOG EMAIL AUTHENTICATION CODE//	
						if($this->DBM->doSecuredQuery($sql, $valArr) &&
							$SITE->logAuthentication($email, AUTH_CODE_KEY_CONFIRM_REG_EMAIL, $authCode)){					
							
							$SITE->mailByTemplate(array('template'=>'confirm-reg-email', 'to'=>$email, 'code'=>$authCode));
										
							$alert = '<span class="alert alert-success">Thank you for choosing to register an account with us.
										<br/>A confirmation link has been dispatched to your email: <a href="mailto:'.$email.'" class="blue">'.$email.
										'</a>,<br/> please click on the link inside to confirm your email and continue with your registration.</span>
										<span class="text-info">If after a while, you do not get from us an email containing your confirmation link, please click  
										'.$resendEmailCfmLink.' to resend it</span>';
			
						}else
							$alert = '<span class="alert alert-danger">Sorry your registration failed! please try again</span>';
						
										
					}else
						$alert = '<span class="alert alert-danger">Sorry the Email: <a href="mailto:'.$email.'">'.$email.'</a> is already registered. '.($this->isRegistered($email)? '' : 'Click '.$resendEmailCfmLink.' to resend confirmation link').'</span>';
							
				}else{
					
					$emailRqd = $asterix;
					$blankEmail = '<span class="alert alert-danger">Please enter your Email !</span>';
					$emailFieldErr = $fErr;	
					
				}
				
			}
			///////END OF HANDLE FOR EMAIL FORM FIELDS ONLY/////
				
				
				
			////HANDLE FOR PROCESSING THE FULL FORM FIELDS/////////////
				
			if(isset($_POST[$K="username"])){

				$username = $this->ENGINE->title_case($_POST[$K]);
				$pwd1 = $_POST['pwd1'];
				$pwd2 = $_POST['pwd2'];
				$firstName = $this->ENGINE->title_case($this->ENGINE->sanitize_user_input($_POST['firstName']));
				$lastName = $this->ENGINE->title_case($this->ENGINE->sanitize_user_input($_POST['lastName']));
				$email = $this->ENGINE->sanitize_user_input($_POST['email']);
				$dobDay = $this->ENGINE->sanitize_user_input($_POST['date_day']);
				$dobMonth = $this->ENGINE->sanitize_user_input($_POST['date_month']);
				$dobYear = $this->ENGINE->sanitize_user_input($_POST['date_year']);
				$maritalStat = $this->ENGINE->sanitize_user_input($_POST['marital_stat']);
				$sex = $this->ENGINE->sanitize_user_input($_POST['sex']);

				if($sex == "Male"){
					
					$maleSelected = "selected";
					$sex = 'M';
					
				}elseif($sex == "Female"){
				
					$femaleSelected = "selected";
					$sex = 'F';
				
				}

				$fullname = $firstName.' '.$lastName;

				$spaceInPwd = $this->ENGINE->has_white_space(array($pwd1, $pwd2));
						 
				$spaceInUsername = $this->ENGINE->has_white_space(array($username));
				
				if($SITE->getAuthentication($email, AUTH_CODE_KEY_CONFIRM_REG_EMAIL)){
					
					if($username && $pwd1  && $pwd2 && $firstName && $lastName && $email){

						if(!$spaceInUsername){
							
							if(mb_strlen($username) >= MIN_USERNAME && mb_strlen($username) <= MAX_USERNAME){																				

								if(!($SITE->pageSlugConflicts($username))){
									
									if(preg_match(USERNAME_PATTERN, $username)){
										
										if(preg_match(PWD_PATTERN, $pwd1)){
											
											if(!$spaceInPwd){

												if($pwd1 == $pwd2){
				
													//REDNDANT CHECK; SINCE PASSWORD PATTERN MATCH ABOVE ALREADY TOOK CARE OF IT, STILL NO HARM IN CHECKING IT AGAIN RIGHT?
													if(mb_strlen($pwd1) >= MIN_PWD && mb_strlen($pwd1) <= MAX_PWD){
															
														//HANDLE FOR DATE CONTROL //////

														$dob = $SITE->ConvertToDatabaseDate($dobDay, $dobMonth, $dobYear);
																				
														////GENERATE CONFIRMATION CODE//////

														$confirmcode = $this->ENGINE->generate_token();

														$pwd1 = $this->passwordEncrypt($pwd1);

														$activationStatus = 0;
														$privilege = "";
														
														/* LET's TRY TO DO A REGISTRATION TRANSACTION */
														
														try{
															
															// Run new registration transaction
															$this->DBM->beginTransaction();
															
															///////IF THIS IS THE FIRST PERSON REGISTERING THEN HE PROBABLY IS THE ADMIN UPDATE HIS PRIVILEGES ACCORDINGLY//////////
															///////PDO QUERY/////
														
															$sql = "SELECT COUNT(*) FROM users";	
															
															if($this->DBM->query($sql)->fetchColumn() == 1)
																$privilege = $this->privilegeEncoderDecoder(ADMIN, $username);													
															
															///PDO QUERY//////
																
															$sql = "UPDATE users SET USERNAME=?, PASSWORD=?, EMAIL=?, FIRST_NAME=?, LAST_NAME=?, TIME=NOW(), CONFIRMED=?
																	,SEX=?, MARITAL_STATUS=?, DOB=?, USER_PRIVILEGE=?, LAST_SEEN=NOW(), REPUTATION=0 WHERE EMAIL=? LIMIT 1";
															$valArr = array($username, $pwd1, $email, $firstName, $lastName, $activationStatus, $sex, $maritalStat, $dob, $privilege, $email);
															$this->DBM->doSecuredQuery($sql, $valArr);
																											
															/***AFTER SUCCESSFUL REGISTRATION EXPIRE EMAIL AUTHENTICATION CODE***/
															$SITE->expireAuthentication($email, AUTH_CODE_KEY_CONFIRM_REG_EMAIL);
															
															//LOG ACTIVATION AUTHENTICATION CODE//											
															$SITE->logAuthentication($email, AUTH_CODE_KEY_ACTIVATE_USER, $confirmcode);

																	
															$alert = '<span class="alert alert-success"><img alt="icon" class="icon-md" src="'.$mediaRootFav.'ok.png" /> <span class="blue">REGISTRATION SUCCESSFUL!!! </span><br/>Your account activation code has been dispatched to your email address: <a class="links" href="mailto:'.$email.'">'.$email.'</a>, it will arrive shortly <br/>
																		Please click on the link inside to activate your account and then proceed to the <a class="links" href="/login">login</a> page<br/>Thank you<br/>If you do not get the activation code after a few minutes, 
																		<a href="/resend_confirmation_code?user='.$username.'&_rdr=signup" class="resend-code links" data-user="'.$username.'" >please click on this link to resend your activation email</a></span><hr/>';

																	
															///SEND CONFIRMATION EMAIL TO USER

															$SITE->mailByTemplate(array('template'=>'confirm-account', 'to'=>$email.'::'.$firstName, 'code'=>$confirmcode, 'username'=>$username));
																																					
															$hideRegForm = true;	 
															
															// If we arrived here then our registration transaction was a success, we simply end the transaction
															$this->DBM->endTransaction();
															
															
														}catch(Throwable $e){
															
															// Rollback if new registration transaction fails
															$this->DBM->cancelTransaction();																
															$alert = '<span class="alert alert-danger">REGISTRATION FAILED! Please try again</span>';
															
														}
														
													}else{
														
														$pwdFieldErr = $fErr;
														$pwdLenErr = $asterix;													
														echo '<script>location.assign("#p")</script>';
															
													}												

												}else{	
																								
													$pwdFieldErr = $fErr;
													$pwdFieldMismatch = $asterix;
													echo '<script>location.assign("#p")</script>';											
														
												}

											}else{
																									
												$pwdFieldErr = $fErr;
												$spaceInPwd = $asterix;											
												echo '<script>location.assign("#p")</script>';
														
											}
											
										}else{		
																			
											$pwdFieldErr = $fErr;
											$pwdPatternErr = $asterix;											
											echo '<script>location.assign("#p")</script>';
												
										}

									}else{
																					
										$usernameFieldErr = $fErr;
										$usernameCharsErr = $asterix;
										echo '<script>location.assign("#u")</script>';

									}
									
								}else{
									
									$alert = '<span class="alert alert-danger">sorry the username: <span class="blue">'.$username.'</span> is not available, please choose another username</span>';

									$usernameFieldErr = $fErr;
									echo '<script>location.assign("#u")</script>';
							
								}
								
							}else{
								
								$usernameFieldErr = $fErr;
								$usernameLenErr = $asterix;
								echo '<script>location.assign("#u")</script>';
												
							}

						}else{			
																	
							$usernameFieldErr = $fErr;
							$spaceInUsername = $asterix;									
							echo '<script>location.assign("#u")</script>';
												
						}

					}else{
																			
						if(!$username && !$pwd1 && !$firstName && !$lastName && !$email && !$pwd2){
																				
							$usernameRqd=$pwd1Rqd=$pwd2Rqd=$firstNameRqd=$lastNameRqd=$asterix;
							$usernameFieldErr=$pwdFieldErr=$firstNameFieldErr=$lastNameFieldErr=$fErr;
							$blanks = true;	

						}	
																																
						if(($username || $pwd1 || $pwd2 || $firstName || $lastName || $email)
							&& (!$username || !$pwd1 || !$pwd2 || !$firstName || !$lastName || !$email)){
									
							if(!$username){	
																				
								$usernameFieldErr = $fErr;
								$usernameRqd = $asterix;
												
							}	
																			
							if(!$pwd1){	
																				
								$pwdFieldErr = $fErr;
								$pwd1Rqd = $asterix;
												
							}
												
							if(!$pwd2){	
																				
								$pwdFieldErr = $fErr;
								$pwd2Rqd = $asterix;
												
							}
												
							if(!$firstName){	
																				
								$firstNameFieldErr = $fErr;
								$firstNameRqd = $asterix;
												
							}		
																		
							if(!$lastName){	
																				
								$lastNameFieldErr = $fErr;
								$lastNameRqd = $asterix;
												
							}		
																		
							if(!$email){	
																				
								$emailFieldErr = $fErr;
								$emailRqd = $asterix;
												
							}

							$blanks = (bool)($usernameRqd.$pwd1Rqd.$pwd2Rqd.$firstNameRqd.$lastNameRqd.$emailRqd);

						}	
						
					}
					
				}else{	
							
					$alertLinkUsed = '<span class="alert alert-danger">Sorry this link has either been already used, altered or expired</span>';

					$qstrEmail = '';

					$beginNewReg = true;

					$newRegEmailLabelText = 'Enter your E-mail to start a new registration: ';
						
				}
			}

		}



		/////CONFIRM A USER EMAIL WHEN THEY CLICK ON LINKS SENT TO THEIR EMAIL//////////

		if( isset($_GET[$KE="email"]) && isset($_GET[$KC="code"])){
				
			if($_GET[$KE] && $_GET[$KC] ){
				
				$qstrAuthCode = $_GET[$KC];
				$qstrEmail = $this->ENGINE->sanitize_user_input($_GET[$KE]);
				
				//////CHECK IF A USER HAS ALREADY USED OR ALTERED THE CONFIRMATION LINK//////
				
				//GET EMAIL AUTHENTICATION CODE//
				$authCode = $SITE->getAuthentication($qstrEmail, AUTH_CODE_KEY_CONFIRM_REG_EMAIL);
				

				if($authCode && !($this->loadUser($qstrEmail)->getUsername())){			

					if($authCode != $qstrAuthCode){
						
						$alertLinkUsed = '<span class="alert alert-danger">Sorry this link has either been already used, altered or expired</span>';
							
						$qstrEmail = '';
						
						$beginNewReg = true;
						
						$newRegEmailLabelText = 'Enter your E-mail to start a new registration: ';
						
					}							
					////CONFIRM THE USER IF CODES MATCH/////
					elseif($authCode == $qstrAuthCode){
											
						$alert = '<span class="alert alert-success">Thank you for confirming your Email please complete the form below to finish your registration</span>'
								.($pwdPatternErr? '' : $pwdMssg).$alert;
						$loadSignUpForm = true;
						
					}
								
				}else{		
													
					$alertLinkUsed = '<span class="alert alert-danger">Sorry this link has either been already used, altered or expired</span>';
					$qstrEmail = '';
					$beginNewReg = true;
					$newRegEmailLabelText = 'Enter your E-mail to start a new registration: ';
								
				}
											
			}else					
				$alert .= '<span class="alert alert-danger"> An unexpected error has occured <br/>We are sorry about this</span>';
								
		}

		////END OF HANDLE FOR CONFIRMING EMAIL////////


		////STORE EMAIL FORM FIELDS IN A VARIABLE TO HIDE OR DISPLAY IT ACCORDINGLY////////

		$regEmailForm = '<div class="form-ui form-ui-basic">		
							<form data-field-validation="true" class="" name="reg" method="post" action="/'.$pageSelf.'">
								<fieldset>					
									<div class="field-ctrl col-ov-sm-7">
										<b id="blue">(We have to confirm your Email before you can proceed with the registration)</b><br/>
										<label for="cem">'.$newRegEmailLabelText.$emailRqd.'</label>
										<input data-validation-name="email" id="cem" class="field '.$emailFieldErr.'" placeholder="Please Enter Your Email Here" type="email"  value="'. $email .'" name="email" />
										<input type="hidden" value="" name="email_form_fields" /> 
									</div>
									<div class="field-ctrl">
										<input  class="form-btn" type="submit" name="submit" value="submit" />
										<p>'.$signUpPolicyAccept.'</p>
										<p>Already Registered?&nbsp;&nbsp; <a class="links sky-blue no-hover-bg" href="/login">login now &raquo;</a></p>					
									</div>
								</fieldset>												
							</form>
						</div>';
		

		////MAJOR AND POPS ACTIVE DURING FORM PROCESSING AFTER A USER HAS CONFIRMED HIS EMAIL/////

		if((isset($_POST["username"]) || isset($loadSignUpForm)) && !isset($beginNewReg)){
			
			$regEmailForm = '';				
				
			$regForm = '		
						<form data-field-validation="true" class="inline-form inline-form-default block-label" name="reg" method="post" action="/'.$pageSelf.'">
							<fieldset>						
								<div class="field-ctrl">
									<label for="un">Username<span class="red"> *</span></label>
									<input maxlength="'.MAX_USERNAME.'" placeholder="example: euroadams" data-validation-name="username" id="un" class="field '.$usernameFieldErr.'" type="text" value="'. $username.'" id="u"    name="username" /><span>'.$usernameRqd.$usernameLenErr.$spaceInUsername.$usernameCharsErr.'</span>
								</div>
								<div class="field-ctrl">
									<label for="pwd1">Password<span class="red"> *</span> '.$eye.'</label>
									<input maxlength="'.MAX_PWD.'" placeholder="Type your password here" data-validation-name="password-twin" data-twin-id="pwd2" id="pwd1" class="reg-pwd1 field '.$pwdFieldErr.'" type="password" value="'.$pwd1.'" name="pwd1" /><span>'.$pwd1Rqd.$pwdLenErr.$pwdPatternErr.$pwdFieldMismatch.$spaceInPwd.'</span>
								</div>
								<div class="field-ctrl">
									<label for="pwd2">Confirm password<span class="red"> *</span> '.$eye2.'</label>
									<input maxlength="'.MAX_PWD.'" placeholder="Re-type your password here" data-validation-name="password-twin" data-twin-id="pwd1" id="pwd2" class="reg-pwd2 field '.$pwdFieldErr.'" type="password" value="'.$pwd2.'" name="pwd2" /><span>'.$pwd2Rqd.$pwdLenErr.$pwdPatternErr.$pwdFieldMismatch.$spaceInPwd.'</span>
								</div>
								<div class="field-ctrl">
									<label for="fn">First name<span class="red"> *</span></label>
									<input maxlength="'.MAX_FN.'" placeholder="example: Isabel" id="fn" class="field '.$firstNameFieldErr.'" type="text" value="'. $firstName.'" name="firstName" /><span>'. $firstNameRqd.'</span>
								</div>
								<div class="field-ctrl">
									<label for="ln">Last name<span class="red"> *</span></label>
									<input  maxlength="'.MAX_LN.'" placeholder="example: Jason" id="ln" class="field '.$lastNameFieldErr.'" type="text" value="'. $lastName.'" name="lastName" /><span>'. $lastNameRqd.'</span>
								</div>
								<div class="field-ctrl">
									<label for="em">E-mail<span class="red"> *</span></label>
									<input id="em" class="field '.$emailFieldErr.'" placeholder="example: isabeljason@yahoo.com" type="email" readonly="readonly" value="'. $email.'" name="email" /><span>'. $emailRqd.'</span>
								</div>
								<div class="field-ctrl">
									<label for="gnd">Gender<span class="red"> *</span></label>				
									<select id="gnd" class="field" name="sex" >
										<option  '.$maleSelected.'>Male</option>
										<option  '.$femaleSelected.'>Female</option>
									</select>
								</div>
								<div class="field-ctrl">
									<label for="mstat">Marital Status<span class="red"> *</span></label>
									'.$maritalStatOpt.'
								</div>
								<div class="field-ctrl">
									<label for="dob">Date of Birth</label>
									'.$SITE->generateDateSelectField($SITE->ConvertToDatabaseDate($dobDay, $dobMonth, $dobYear), SITE_ACCESS_MIN_AGE).'
									<input type="hidden" value=""  name="full_form_fields" />
								</div>
								<div class="field-ctrl btn-ctrl">
									<input  class="form-btn btn-lg" type="submit" name="submit" value="submit" />
								</div>
							'.$signUpPolicyAccept.'											
							</fieldset>												
						</form>';	
						
			

		}

		//////HIDE FORM AFTER SUCCESSFUL REGISTRATION/////
		if($hideRegForm )				
			$regForm=$regEmailForm='';
		

		$SITE->buildPageHtml(array("pageTitle"=>'Join',
					"preBodyMetas"=>$SITE->getNavBreadcrumbs('<li><a href="/signup" title="">Join '.$siteName.'</a></li>'),
					"pageBody"=>'
					<div class="single-base blend">
						<div class="base-ctrl base-rad">
							<h2 class="page-title pan">REGISTRATION:</h2>				
							<div class="base-container base-b-pad">									
								<p>
									'.$alert.
									((isset($alertLinkUsed) && !isset($formSubmit))? $alertLinkUsed : '').
									(isset($_GET["code-resend"])?  $this->ENGINE->get_global_var('ss', 'SESS_ALERT') : '').'
								</p>
								<p class="red">
									'.$blankEmail. 
									($spaceInUsername? ' <span class="alert alert-danger">Spaces are not allowed in the username field ! </span>' : '').
									($spaceInPwd? ' <span class="alert alert-danger">Spaces are not allowed in the password fields ! </span>' : '').											 
									($blanks? '<span class="alert alert-danger">Fields marked * are required !</span>' : '').											 
									($pwdFieldMismatch? '<span class="alert alert-danger">The password fields did not match !</span>' : '').											
									($pwdPatternErr? $pwdMssg : '').											 
									($pwdLenErr? '<span class="alert alert-danger">The password you entered is too short; it must be within a minimum of '.MIN_PWD.'  and maximum of '.MAX_PWD.' characters with no spaces !</span>' : '').											 
									($usernameLenErr? '<span class="alert alert-danger">The username you entered  is too short it must be within a minimum of '.MIN_USERNAME.' and maximum of '.MAX_USERNAME.' characters with no spaces! !</span>' : '').											 
									($usernameCharsErr? '<span class="alert alert-danger">The username field must contain at least one alphabet and optionally Numbers, Dashes(-) and Underscores(_)</span> ' : '').											
									((isset($dobErr) && $dob)? '<span class="alert alert-danger">The birthday format is incorrect. please follow this format (yyyy/mm/dd) example 1988/05/23</span>' : '').											 										 
								'</p>																
								'.$regEmailForm.$regForm.'										
							</div>
						</div>
					</div>'
					
		));
					

	}

	


	
	
	
	/*** Method for handling user account activation request ***/
	public function handleUserAccountActivationRequest(){
	
		global $SITE, $badgesAndReputations, $siteDomain;
		
		$sessUsername = $this->SESS->getUsername();
		$sessUid = $this->SESS->getUserId();
		
		/////IF ACTIVATION LINK IS CLICKED VIA EMAIL/////
		
		$username = 'username';
		$authCode = 'code';
		
		if(isset($_GET[$username], $_GET[$authCode]) || $sessUsername){
			
			$username = isset($_GET[$username])? $this->ENGINE->sanitize_user_input($_GET[$username]) : $sessUsername;
			$authCode = isset($_GET[$authCode])? $_GET[$authCode] : 1;
			$letsRoll = 'You are all set, <a href="/" class="links">Now Let`s Dive in</a>';
			
			if($username && $authCode){
						
				//GET ACTIVATION AUTHENTICATION CODE//
				$U = $this->loadUser($username);
				$email = $U->getEmail();		
				$dbAuthCode = $SITE->getAuthentication($email, AUTH_CODE_KEY_ACTIVATE_USER);
				
				if($authCode == $dbAuthCode){
					
					 ///////ACTIVATE THE USER AND AWARD THE STUDENT BADGE AND ONE POINT REPUTATION//////////						
					$cols = "CONFIRMED=1";
																	
					if($this->updateUser($username, $cols) && $SITE->expireAuthentication($email, AUTH_CODE_KEY_ACTIVATE_USER)
						&& $badgesAndReputations->awardBadge($username, 'STUDENT') && $badgesAndReputations->awardReputation($username, 1)){
					
						$passwordMailed = 'NONE_OF_YOUR_BUSINESS';
						
						///SEND CONFIRMATION EMAIL USER		

						$to = $email;	
						$subject = 'Your Login Details';

						$message = 'Hello '.$username.'\n Thank you for activating your account.\n your account is now fully active and community privilege restrictions have been lifted.\n Please <a href="'.$siteDomain.'/login">Login</a> into your account with your details. \n <span '.EMS_PH_PRE.'BLUE>USERNAME:</span> '.$username.'\n<span '.EMS_PH_PRE.'BLUE>PASSWORD:</span> '.$this->ENGINE->cloak($passwordMailed,100,15).'\nPlease do keep your details safe. \n\n\nThank you\n\n\n\n';
								
						$footer = 'NOTE: this email was sent to you because you registered an account at <a href="'.$siteDomain.'">'.$siteDomain.'</a>. If you
						did not make such registration, please kindly ignore this message.\n\n\n Please do not reply to this email.';
						
						$SITE->sendMail(array('to'=>$to.'::'.$U->getFirstName(), 'subject'=>$subject, 'body'=>$message, 'footer'=>$footer));
						
						$alertUser = '<span class="alert alert-success">Thank you for activating your account.<br/>your account is now fully active. '.($username? $letsRoll : 'please click <a href="/login"> here</a> to login now<br/>
									Also an E-mail containing your login details has been dispatched to your E-mail address: <a href="mailto:'.$email.'">'.$email.'</a>').'</span>';
						
					}else
						$alertUser = '<span class="alert alert-danger">Sorry the system encountered an error!</a>.<br/>Please try again.</span>';
											
				}else{
					
					if(!$dbAuthCode)
						$alertUser = '<span class="alert alert-success">Hey'.($username? ' '.$username : '').'!<br/> your account has already been activated. '.($username? $letsRoll : 'please click <a href="/login">here</a> to login').'</span>';
						
					elseif($dbAuthCode){
						 
						$alertUser = '<span class="alert alert-danger">It seems your activation link was altered!!!<br/>Please kindly go back to your email and click on the link again</br>
						and please do not alter the link to enable you activate your account without any issues<br/>Thank you.</span>';

					}

				}
			
			}else
				$alertUser = '<span class="alert alert-danger">An unexpected error has occurred<br/>We are sorry about this.</span>';
						

		}else
			$alertUser = '<span class="alert alert-danger">An unexpected error has occurred<br/>We are sorry about this.</span>';
				

		$SITE->buildPageHtml(array("pageTitle"=>'Account Activation',
					"preBodyMetas"=>$SITE->getNavBreadcrumbs('<li><a href="/activate-account" title="">Account Activation</a></li>'),
					"pageBody"=>'			
					<div class="single-base blend">			
						<div class="base-ctrl base-rad">
							<h1 class="page-title bg-limex pan">ACCOUNT ACTIVATION</h1>				
							<div class="cyan base-container">'.(isset($alertUser)? $alertUser : '').'</div>				
						</div>
					</div>'							

		));	
						
	
	}
	

	


	
	
	/*** Method for fetching users profile link tabs ***/
	private function getProfileTabs($username, $tabType, $meta_arr){
		
		global $badgesAndReputations;
		
		$postActive=$topicActive=$lTopicActive=$profActive=$bdgActive=$typeHeader=$typeSubNavTitle='';
		$activeCls = 'active';	
		$badge = $this->ENGINE->get_assoc_arr($meta_arr, 'badge');		
		$refTitle = $this->ENGINE->get_assoc_arr($meta_arr, 'refTitle');		
		$refTitleUC = strtoupper($refTitle);
		$usernameSlug = $this->sanitizeUserSlug($username);
		$tabType = strtolower($tabType);
		
		switch($tabType){
		
			case 'all-posts': $postActive = $activeCls; $typeHeader = 'ALL '.$refTitleUC.' POSTS'; break;
		
			case 'all-topics': $topicActive = $activeCls; $typeHeader = 'ALL '.$refTitleUC.' TOPICS'; break;
		
			case 'latest-topics': $lTopicActive = $activeCls; $typeHeader = $refTitleUC.' LATEST TOPICS'; break;
		
			case 'badges': $bdgActive = $activeCls; $typeHeader = $refTitleUC.' '.strtoupper($badge).' BADGES';
				 $typeSubNavTitle = 'BADGES'; break;
		
			default: $profActive = $activeCls;
		
		}

		$refTitleSubNav = stripos($refTitle, 'my') !== false ? $refTitle.' Profile' : stristr($refTitle, "'s", true);
		$typeSubNavTitle = $typeSubNavTitle? $typeSubNavTitle : str_ireplace($refTitle.' ', '', $typeHeader);		
		
		$tab = '<li class="'.$profActive.'"><a href="/'.$usernameSlug.'" class="links">'.$refTitle.' Profile</a></li>					
				<li class="'.$bdgActive.'"><a href="/'.$username.'/badges" class="links">'.$refTitle.' Badges(<b class="cyan">'.$badgesAndReputations->getBadgeCount(array('uid'=>$username)).'</b>)</a></li>					
				<li class="'.$lTopicActive.'"><a href="/'.$username.'/latest-topics" class="links">'.$refTitle.' Latest Topics(<b>'.$this->ENGINE->get_assoc_arr($meta_arr, 'tlt').'</b>)</a></li>					
				<li class="'.$topicActive.'"><a href="/'.$username.'/all-topics" class="links">All '.$refTitle.' Topics(<b>'.$this->ENGINE->get_assoc_arr($meta_arr, 'ta').'</b>)</a></li>
				<li class="'.$postActive.'"><a  href="/'.$username.'/all-posts">All '.$refTitle.' Posts(<b>'.$this->ENGINE->get_assoc_arr($meta_arr, 'ap').'</b>)</a></li>';
		
		$subNav = '<li><a href="/'.$usernameSlug.'" title="">'.$refTitleSubNav.'</a></li>
					'.($typeHeader? '<li><a href="/'.$username.'/'.$tabType.'">'.$this->ENGINE->title_case($typeSubNavTitle).'</a></li>' : '').$this->ENGINE->get_assoc_arr($meta_arr, 'xtrasubnav');				
			
		$typeHeader = $typeHeader? $typeHeader : '';
		
		return array($tab, $subNav, $typeHeader);
		
	}
			






	
	
	
	
	/*** Method for handling user profile loading request ***/
	public function handleUserProfileLoadRequest(){
		
		global $SITE, $badgesAndReputations, $FORUM, $siteDomain, $pageSelf, $mediaRootAvt, $mediaRootAvtXCL, $FA_phone, $FA_thumbsUp, $FA_cog, $FA_user, $GLOBAL_rdr;
		
		////////VARABLE INITIALIZATION////////////////
		$pageCounter=$badgeName=$badgeSubNav=$externalLinks=$fullNameFields=$email=$tabQUDS=$data=$badgeSortedTotal=$sortNav=
		$timeAvatarUploaded=$totalAvatarLikes=$memberFollowLink=$contactByMessage=$embeddedAvatarBgStyles="";
		$totalAvatarLikeCounts=0;

		/***************************BEGIN URL CONTROLLER****************************/
		
		$sessUsername = $this->SESS->getUsername();
		$sessUid = $this->SESS->getUserId();
				
		$path = $this->ENGINE->get_page_path('page_url', '', true, true);

		$pagePathArr = explode('/', $path);
			
		$path2 = isset($pagePathArr[1])? strtolower($pagePathArr[1]) : '';
		$path3 = isset($pagePathArr[2])? strtolower($pagePathArr[2]) : '';
		$path4 = isset($pagePathArr[3])? $this->ENGINE->sanitize_number($pagePathArr[3]) : '';
		$page_self_srt = $this->ENGINE->get_page_path('page_url', 2);
		$expectedPaths_arr = array("latest-topics", "all-posts", "all-topics");
		$pathKeysArr = array("username", "tab", "pageId");
		$appendXtn = false;
		
		if(in_array($path2, $expectedPaths_arr))	
			$maxPath = 3;
		
		elseif($path2 == 'badges' && in_array($path3, array('general', 'tags', ''))){
										
			$pathKeysArr = array("username", "tab", "sort", "sort2", "pageId");
			$maxPath = 5;
		
		}elseif($path2 == 'badges' && $path3 == 'award-history' && $path4){	
									
			$pathKeysArr = array("username", "tab", "award-history", 'bid', 'pageId');
			$maxPath = 5; $badgeHistoryTab = true;
		
		}elseif($path2 == 'avatar'){
										
			$pathKeysArr = array("username", "tab");
			$maxPath = 2; $avatarRequest = true;
		
		}else{
		
			$maxPath = 1;
			$appendXtn = true;
		
		}
		
		$this->ENGINE->url_controller(array('pathKeys'=>$pathKeysArr, 'maxPath'=>$maxPath));

		/*******************************END URL CONTROLLER***************************/


		///////GET THE URL DATAS/////////////////
		
		$username = isset($_GET[$K="username"])? $_GET[$K] : '';			
		$tabType = isset($_GET[$K="tab"])? $this->ENGINE->sanitize_user_input($_GET[$K], array('lowercase' => true)) : '';					
		$getPage = isset($_GET[$K="pageId"])? $_GET[$K] : '';								
		$bid = isset($_GET[$K="bid"])? $this->ENGINE->sanitize_number($_GET[$K]) : '';				
		$sort = isset($_GET[$K="sort"])? $this->ENGINE->sanitize_user_input($_GET[$K], array('lowercase' => true)) : '';				
		$sort2 = isset($_GET[$K="sort2"])? $this->ENGINE->sanitize_user_input($_GET[$K], array('lowercase' => true)) : '';				
		
		//ENFORCE PROPER PROFILE URL 
		list($usernameSlug, $slugEnforced) = $this->sanitizeUserSlug($username, array('appendXtn'=>$appendXtn, 'validateSlug'=>$this->ENGINE->get_page_path('page_url', 1, true, false)));
		
		if(!$slugEnforced){
		
			$pagePathArr[0] = $usernameSlug;
			header("Location:/".implode('/', $pagePathArr), true, 301);
			exit();
		
		}
		
		$userId = $this->memberIdToggle($username, true);							
		
		$viewAsKey = 'view_as'; $viewAsVal = $this->ENGINE->generate_token();
		
		if(isset($_GET[$viewAsKey]) && isset($_SESSION[$viewAsKey]) && $_GET[$viewAsKey] == $_SESSION[$viewAsKey]) 
			$viewAs = true;
		
		else{
		
			$viewAs = false;
			$_SESSION[$viewAsKey] = $viewAsVal;
		
		}
		
		$rdr =  $GLOBAL_rdr;
		$_isSess = strtolower($sessUsername) == strtolower($username);
		$isSess = $_isSess && !$viewAs;
		$refTitle = ucwords($isSess? 'My' : $username.'\'s');					
		$CU = $this->loadUser($username);
		$viewAsLink = '<div class="clear"><span class="pull-r"><a title="view your profile as seen by public" role="button" href="/'.$usernameSlug.'?'.$viewAsKey.'='.$viewAsVal.'" class="btn btn-sc btn-classic">View As</a></span></div>';
		$exitViewAsLink = $_isSess? '<div class="clear"><span class="pull-r"><a role="button" href="/'.$usernameSlug.'" class="btn btn-infox btn-classic-sc">Exit View As</a></span></div>' : '';
		
		//////AWARD autobiographer///////////
		$badgesAndReputations->badgeAwardFly($sessUid, 'autobiographer');
		
		/**VIEW FULL AVATAR**/
		if(isset($avatarRequest)){
			
			$title = $isSess? 'My Avatar' : $this->ENGINE->title_case($username);							
			$title2 = $isSess? 'My Profile' : $this->ENGINE->title_case($username);							
			
			if($username){

				//$vdp = $CU->getAvatar();
				list($dp, $dpFound, $vdp) =  $this->getDp($username, array('retArr'=>true));
				$fullDp = '<div  class="base-b-mg" ><h2 class="page-title pan bg-limex">'.$this->sanitizeUserSlug($username, array('anchor'=>true, 'youRef'=>false, 'cls'=>'green no-hover-bg', 'urlText'=>$title)).'</h2></div><div class="base-pad"><a href="'.$SITE->getDownloadURL($vdp, "profile").'"><img class="img-responsive" alt="'.$title.' avatar" src="'.$mediaRootAvt.$vdp.'" /></a></div>';
				
			}else{
		
				header("Location:/");
				exit();	
					
			}
			
			$SITE->buildPageHtml(array("pageTitle"=>$title,																					
						"preBodyMetas"=>$SITE->getNavBreadcrumbs('<li>'.$this->sanitizeUserSlug($username, array('anchor'=>true, 'youRef'=>false, 'urlText'=>$title2)).'</li><li><a href="/'.$pageSelf.'" title=""> Avatar</a></li>'),
						"pageBody"=>'
						<div class="single-base blend">			
							<div class="base-ctrl">							
								'.$fullDp.'
							</div>
						</div>'
			));
				
			
		}elseif($sessUsername || $username){

			if($tabType == "badges" ){
								
				if(!isset($badgeHistoryTab)){
		
					list($sortNav, $cid, $clid, $sort, $sort2) = $badgesAndReputations->getBadgeSortNav(array('s1'=>$sort, 's2'=>$sort2, 'pid'=>$getPage, 'baseUrl'=>$page_self_srt));
					$badgeCountMetas = array('uid'=>$username, 'clid'=>$clid, 'cid'=>$cid);					
					$pageUrl =  $username.'/'.$tabType;						
					$qstrVal_arr = array($sort, $sort2);
					
				}else{
		
					$badgeSubNav = '<li><a href="/'.$pageSelf.'">'.($badgeName = $badgesAndReputations->getBadgeDetail($bid, 'BADGE_NAME')).'</a></li>';
					$data = '<b><a href="/badges/awardees/'.$bid.'?xuser='.$username.'" class="links">see other users with this badge >>></a></b>';
					$badgeCountMetas = array('uid'=>$username, 'bid'=>$bid);						
					$pageUrl = $username.'/'.$tabType.'/award-history/'.$bid;
					$qstrVal_arr = array();
		
				}
		
				$total_badges = $badgesAndReputations->getBadgeCount($badgeCountMetas);					
				$totalRecords = $total_badges;
				
				/**********CREATE THE PAGINATION*************/																								
				$paginationArr = $SITE->paginationHandler(array('totalRec'=>$totalRecords,'url'=>$pageUrl,'qstrVal'=>$qstrVal_arr,'hash'=>'ptab'));					
				$pagination = $paginationArr["pagination"];
				$totalPage = $paginationArr["totalPage"];
				$n = $paginationArr["perPage"];
				$i = $paginationArr["startIndex"];
				$pageId = $paginationArr["pageId"];
				
				$userBadgeMetas = array('uid'=>$username, 'userBadgeCms'=>true,'panelBlock'=>false, 'reportErr'=>true, 'i'=>$i, 'n'=>$n);
								
				if(isset($badgeHistoryTab))
					$userBadgeMetas["bid"] = $bid;
		
				else{
		
					$userBadgeMetas["cid"] =  $cid;
					$userBadgeMetas["clid"] =  $clid;
		
				}
												
				$data = '<div class="base-b-pad base-lr-pad">'.$badgesAndReputations->loadUserBadges($userBadgeMetas).$data.'</div>';
				
				
				if($totalPage)
					$pageCounter = '<span>(page <span class="cyan">'.$pageId.'</span> of '.$totalPage.')</span>';
				 
				$badgeSortedTotal = '<h3 class="prime">Badges found('.$total_badges.')</h3>';
		
			}	
			
				
			////////////CURRENT USER LATEST TOPICS///////////

			$assumedLatest = ' INTERVAL '.ASSUMED_INTERVAL_LATEST;

			///////////PDO QUERY///////
				
			$sql = "SELECT COUNT(*) FROM topics WHERE TIME >= (NOW() - ".$assumedLatest.") AND TOPIC_AUTHOR_ID=?";
			$valArr = array($userId);
			$totalRecords = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();
			
			if($tabType == "latest-topics" ){
											
				/**********CREATE THE PAGINATION*************/																									
				$pageUrl = $username;
				$qstrVal_arr = array($tabType);
				$paginationArr = $SITE->paginationHandler(array('totalRec'=>$totalRecords,'url'=>$pageUrl,'qstrVal'=>$qstrVal_arr,'hash'=>'ptab'));					
				$pagination = $paginationArr["pagination"];
				$totalPage = $paginationArr["totalPage"];
				$perPage = $paginationArr["perPage"];
				$startIndex = $paginationArr["startIndex"];
				$pageId = $paginationArr["pageId"];

				//////////////END OF PAGINATION//////////

				 
				if($totalRecords){
					 
					 
					///////////PDO QUERY//////
					
					$sql = $SITE->composeQuery(array('type' => 'for_topic', 'start' => $startIndex, 'stop' => $perPage, 'uniqueColumns' => '', 'filterCnd' => 'topics.TIME >= (NOW() - '.$assumedLatest.') AND TOPIC_AUTHOR_ID=?', 'orderBy' => ''));
					
					list($data) = $FORUM->loadThreads($sql, array($userId), $type="profile");		

				}else{		 
										 
					$data = '<span class="alert alert-danger">sorry '.($isSess? 'you have' : $username.' has').' no latest topics</span>';					 
									 
				}		 
					 
				if($totalPage)
					$pageCounter = '<span>(page <span class="cyan">'.$pageId.'</span> of '.$totalPage.')</span>';
					 
			}
						
			$LatestUserTopicCountView = '<span class="cyan">'.$totalRecords.'</span>';
				
				
			//////////////////ALL CURRENT USER'S TOPICS////////////
									
			///////////PDO QUERY//////////
				
			$sql = "SELECT COUNT(*) FROM topics WHERE  TOPIC_AUTHOR_ID=?";
			$valArr = array($userId);
			$totalRecords = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();
			
			
			if($tabType == "all-topics"){		
									
											
				/**********CREATE THE PAGINATION*********/						
				$pageUrl = $username;
				$qstrVal_arr = array($tabType);				
				$paginationArr = $SITE->paginationHandler(array('totalRec'=>$totalRecords,'url'=>$pageUrl,'qstrVal'=>$qstrVal_arr,'hash'=>'ptab'));
				$pagination = $paginationArr["pagination"];
				$totalPage = $paginationArr["totalPage"];
				$perPage = $paginationArr["perPage"];
				$startIndex = $paginationArr["startIndex"];
				$pageId = $paginationArr["pageId"];

				//////////END OF PAGINATION//////////
				
				if($totalRecords){
					
					///PDO QUERY////////////
					
					$sql = $SITE->composeQuery(array('type' => 'for_topic', 'start' => $startIndex, 'stop' => $perPage, 'uniqueColumns' => '', 'filterCnd' => 'TOPIC_AUTHOR_ID=?', 'orderBy' => ''));
					
					
					list($data) = $FORUM->loadThreads($sql, array($userId), $type="profile");
								

				}else{
										
					$data = '<span class="alert alert-danger">sorry '.($isSess? 'you have' : $username.' has').' not created any topic yet</span>';					
								 
				}		 
					 
				if($totalPage)
					$pageCounter = '<span>(page <span class="cyan">'.$pageId.'</span> of '.$totalPage.')</span>';
					
					
			}
				
			$allUserTopics = '<span class="cyan">'.$totalRecords.'</span>';
			
					
			//ALL CURRENT USER'S POST/////

			///////////PDO QUERY/////////
				
			$sql = "SELECT COUNT(*) FROM posts WHERE  POST_AUTHOR_ID= ?";
			$valArr = array($userId);
			$allUserPosts = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();

			
			if($tabType == "all-posts")	{		
				
				if($allUserPosts){

					$totalRecords = $allUserPosts;
															
					/**********CREATE THE PAGINATION************/																
					$pageUrl = $username;
					$qstrVal_arr = array($tabType);
					$paginationArr = $SITE->paginationHandler(array('totalRec'=>$totalRecords,'url'=>$pageUrl,'qstrVal'=>$qstrVal_arr,'hash'=>'ptab'));						
					$pagination = $paginationArr["pagination"];
					$totalPage = $paginationArr["totalPage"];
					$perPage = $paginationArr["perPage"];
					$startIndex = $paginationArr["startIndex"];
					$pageId = $paginationArr["pageId"];

					//////END OF PAGINATION///////////
							
					///////GET ALL POSTS/////////////

					///////////PDO QUERY/////////
							
					$sql = $SITE->composeQuery(array('type' => 'for_post', 'start' => $startIndex, 'stop' => $perPage, 'uniqueColumns' => '', 'filterCnd' => 'POST_AUTHOR_ID=?', 'orderBy' => 'TIME DESC'));
					
					///////////DISPLAY THE POSTS/////
					list($data) = $FORUM->loadPosts($sql, array($userId));						
				   
				}else{	
										
					$data = '<span class="alert alert-danger">sorry '.($isSess? 'you have' : $username.' has').' not made any post yet</span>';						
					$data = '<div class="user_topics_in_prof">'.$data.'</div>';	
								 
				}		
						 
				 if($totalPage)
					$pageCounter = '<span>(page <span class="cyan">'.$pageId.'</span> of '.$totalPage.')</span>';
				 
				
			}

			$allUserPosts = '<span class="cyan">'.$allUserPosts.'</span>';								
			
			/****CREATE TAB*****/
			$meta_arr = array('badge'=>$badgeName, 'refTitle'=>$refTitle, 'xtrasubnav'=>$badgeSubNav, 'pc' => $pageCounter, 'tlt' => $LatestUserTopicCountView, 'ta' => $allUserTopics, 'ap' => $allUserPosts);
			list($tab, $subNav, $header) = $this->getProfileTabs($username, $tabType, $meta_arr);
				
			/////////CALL FUNCTIONS TO COLLECT PROFILE VIEWERS/////////

			$SITE->collectSiteTraffic();	

													
			$clrBotCls = $isSess? ' base-b-pad ' : '';
			
			list($rightWidget, $pageTopAds, $pageBottomAds, $leftWidgetClass) = $SITE->getWidget(ELITE_SID);
			
			
			if($isSess)
				$tabQUDS = $FORUM->getPostQUDS($sessUid);
			
			
			if($tabType){
				
				$pageCounter? ($pageCounter = '<div class="cpop">'.$pageCounter.'</div>') : '';
				$tabContents =	'<div class="base-container base-ctrl base-rad base-b-pad">	
									'.$pageTopAds.'
									<div class="row">
										<div class="'.$leftWidgetClass.' base-ctrl base-rad">
											<h1 class="page-title pan bg-limex">'.$header.'</h1>
											'.$pageCounter.$badgeSortedTotal.$pagination.$sortNav.$data.$pagination.'
										</div>																							
										'.$rightWidget.' 																				
									</div>
									'.$pageBottomAds.'									
								</div>';
			
			}								
			//////PROFILE///////////
			elseif(!$tabType){
				
				$header = $isSess? $refTitle.' Profile' : $username;
				////////////GET SECTIONS MOST ACTIVE//////////

				$sectionsMostActive="";
				 
				/***GET EACH TOPIC THE USER HAS POSTED TO AND CHECK IF THE NUMBER OF POSTS IN 
				EACH OF THEM IS SATISFACTORY BEFORE INCLUDING THE TOPIC SECTION IN THE ACTIVE LIST**/
				///////////PDO QUERY//
				$minActivePost = 50;
				
				$sql= "SELECT categories.CATEG_NAME, sections.SECTION_NAME, sections.ID AS SID
						FROM posts JOIN topics ON posts.TOPIC_ID = topics.ID JOIN 
						sections ON topics.SECTION_ID = sections.ID JOIN categories ON 
						sections.CATEG_ID = categories.ID WHERE POST_AUTHOR_ID = ? GROUP BY SECTION_NAME
						HAVING COUNT(*) >= ? LIMIT 15";
				
				$valArr = array($userId, $minActivePost);
				$stmt = $this->DBM->doSecuredQuery($sql, $valArr);
			 
				while($row = $this->DBM->fetchRow($stmt)){
									
					$activeScatName = $row["SECTION_NAME"];		
					$activeCatName = $row["CATEG_NAME"];
					
					$sectionsMostActive .= /*$this->ENGINE->sanitize_slug($activeCatName, array('ret'=>'url'))' >*/
										$this->ENGINE->sanitize_slug($activeScatName, array('ret'=>'url', 'ignoreHref'=>(HOMEPAGE_SID == $row["SID"]))).'<b>, </b>';
									
				}
				
				$sectionsMostActive = substr($sectionsMostActive, 0, -9);


				//////FETCH THE INTENDED USER FROM DB////////////
				
				$row = $this->loadUser($username, true);
				
				if($row['ID']){
						
					$avatarBg = $row['AVATAR_BG'];	
						
					$embeddedAvatarBgStyles = $avatarBg?
					'
					@media only screen and (min-width: 570px) {

						.gcard.gcard-classic:before{
						
							background-image: url("'.$siteDomain.'/'.$mediaRootAvtXCL.$avatarBg.'");
							background-position: center;
							background-size: cover

						}
					}
					' : '';
					
					$hrDividerCls = 'hr-divider';
					$profilePillCls = 'pill-follower lblack';
					
					list($avatar, $dpFound) = $this->getDp($username, array('url'=>$username.'/avatar', 'icls'=>'zoom-ctrl', 'type'=>'gcard', 'gcardClassic'=>true, 'ipane'=>false, 'cardSize'=>'lg', 'iAttr'=>'data-scale-xy="true"', 'retArr'=>true));		
					
					////GET SESS AVATAR LIKES FOR SESS/EXTERNAL VIEW////////////
					
					$totalAvatarLikeCounts = $FORUM->avatarLikesHandler(array('uid'=>$userId,'count'=>true));
									
					if($dpFound && $sessUsername){
							
						$totalAvatarLikeCounts = $totalAvatarLikeCounts? $totalAvatarLikeCounts.' Like'.(($totalAvatarLikeCounts == 1)? '' : 's').' ' : '';
							
						if($isSess){
							
							$totalAvatarLikes = ' <label class="'.$profilePillCls.'"><span class="cyan">'.$totalAvatarLikeCounts.' </span><a href="/avatar-likes" class="links">view all</a></label> ';
							
						}else{
								
							//////CHECK IF THE SESS USER HAS LIKED THE USER'S AVATAR AND PRESENT OPTION TO UNLIKE///						
							
							$avatarLikeText = $FORUM->avatarLikesHandler(array('uid'=>$userId, 'liker'=>$sessUid, 'check'=>true))? 'Unlike '.$SITE->getFA('far fa-thumbs-down active-done-state') : 'Like';
					
							$isAvatarLikeAction = (trim(strtolower($avatarLikeText)) == "like");
							$avatarLikeAction = $isAvatarLikeAction? 'like' : 'unlike';
							
							$totalAvatarLikes = ' <label class="'.$profilePillCls.'"><span id="alc-'.$userId.'" class="cyan">'.$totalAvatarLikeCounts.'</span><a href="/avatars/'.$avatarLikeAction.'/'.$username.'/?_rdr='.$rdr.'" class="links no-hover-bg sky-blue dp_like" data-user="'.$username.'" data-action="'.$avatarLikeAction.'" data-count="'.$totalAvatarLikeCounts.'" data-disp="alc-'.$userId.'" >'.$avatarLikeText.($isAvatarLikeAction? $FA_thumbsUp : '').'</a></label> ';
							
						}
							
					}


	
					//SHOW DP ONLY THE USER ACTUALLY HAVE UPLOADED ONE///////
						
					$dpFound? ($timeAvatarUploaded = '<label class="'.$profilePillCls.'">Avatar uploaded: <span class="prime">'.$this->ENGINE->time_ago($row["AVATAR_UPLOAD_TIME"]).$totalAvatarLikes.'</span></label>') : '';
			
					$timeRegistered = '<label class="'.$profilePillCls.'">Registered: <span class="prime-1">'.$this->ENGINE->time_ago($row["TIME"]).'</span></label>';
					$timeLastSeen = '<label class="'.$profilePillCls.'">Last Seen: <span class="prime-2">'.$this->ENGINE->time_ago($row["LAST_SEEN"]).'</span></label>';
					
					$unqUid = !$isSess? $userId : '';
					$isBirthday = $this->isUserBirthday($username);
					$birthdayGreetings = $isBirthday? $SITE->getPops($username, $fixed=false, $unqUid, POP_FOR_BIRTHDAYS) : '';
					//$birthdayGreetings = getBirthdayGreetings($username, '', $unq);
					$birthdayGreetingsXT = $isBirthday? '<div class="alert alert-success"><img '.BIRTHDAY_ICON.' /> Today is <b>'.$username.'</b>\'s Birthday. <a role="button" class="btn btn-classic" href="/pm/'.$usernameSlug.'?bd_msg=true">Send Birthday Wishes</a> </div>' : "";			
					
					//////REWRITE DOB TO YOUR OWN STYLE////////////

					$userDob = ($dob = $row["DOB"])? $SITE->customDateDisplay($dob, $username, $viewAs) : '';
														
					
					///CHECK IF U ARE FOLLOWING THE USER U ARE VIEWING OR HE IS FOLLOWING YOU AND GIVE OPTIONS/////						
																	
					$membersFollowText=$membersFollowComment="";						
					
					////CHECK IF U ARE FOLLOWING THE USER//////							
					$youFollowMember = $FORUM->followedMembersHandler(array('uid'=>$userId, 'follower'=>$sessUid, 'check'=>true));						
					
					////CHECK IF THE USER IS FOLLOWING U//////						
					$membersFollowsYou = $FORUM->followedMembersHandler(array('uid'=>$sessUid, 'follower'=>$userId, 'check'=>true));
							
					if($youFollowMember){
						
						$membersFollowText = 'Unfollow';				
						$membersFollowComment = '<a href="/'.$this->SESS->getUsernameSlug().'" class="links">You</a> are following this member';
						
					}else
						$membersFollowText = 'Follow this member';
						
					if($membersFollowsYou && !$youFollowMember){
						
						$membersFollowText = 'Follow Back';
						$membersFollowComment = '<a href="/'.$username.'" class="links">'.$username.'</a> is following you';		
					
					}
					
					///////DECIDE HOW LIKE, UNLIKE, FOLLOW AND UNFOLLOW LINKS DISPLAY //////

					if($sessUsername){
						
						$isMemberUnfollowAction = (stripos($membersFollowText, 'unfollow') !== false);
						$memberFollowAction = $isMemberUnfollowAction? 'unfollow' : 'follow';
			
						$memberFollowLink = '<label class="'.$profilePillCls.'" ><span id="mf-disp">'.$membersFollowComment.'</span> (<a class="follow-member links" data-user="'.$username.'" data-action="'.$memberFollowAction.'" data-count-holder="fm-count-disp" href="/members-follows/'.$memberFollowAction.'/'.$username.'/?_rdr='.$rdr.'" >'.$membersFollowText.'</a>)<span class="alertUser"></span></label>';
						
					}	

					
					///GET THE PPLE FOLLOWING AND FOLLOWERS OF THE CURRENT USER ON VIEW//////////////
											
					$followingFollowersArr = $FORUM->followedMembersHandler(array('uid'=>$username,'getFF'=>true,'vcardMin'=>true));
					
					if(isset($followingFollowersArr[$K="followers"]))
						$allFollowers = $followingFollowersArr[$K];
					
					if(isset($followingFollowersArr[$K="following"]))
						$allYouFollow = $followingFollowersArr[$K];
					
					if(isset($followingFollowersArr[$K="nFollowers"]))
						$totalFollowers = $followingFollowersArr[$K];
					
					if(isset($followingFollowersArr[$K="nFollowing"]))
						$totalYouFollow = $followingFollowersArr[$K];
						
						
					/////GET THE SECTIONS THE CURRENT USER MODERATES///////
					
					$sectionsModerated = $SITE->moderatedSectionCategoryHandler(array('uid'=>$username,'action'=>'scm-names', 'sep'=>'<b>, </b>'));
					
					$userBadgesDivider=$alertScheduledTermination='';
					 
					list($badges, $userBadgeCounts) = $badgesAndReputations->loadUserBadges($meta_arr=array('uid'=>$username, 'n'=>10, 'retArr'=>true));
											
					$userBadgesDivider = ' hr-divider ';

					if($userBadgeCounts){
					 
						$badges .= '<h4><a href="/'.$username.'/badges" class="links">'.($isSess? 'Show' : 'See').' All '.$refTitle.' Badges</a></h4>';	
					 
					}
					
					$lavatar = ' '.$this->ENGINE->build_lavatar($username, '5px');
					
					if($isSess){								
						
						if($sectionsMostActive)	 
							$sectionsMostActive = '<div class="'.$hrDividerCls.'"><label class="flab">Sections You are most active:</label>
												'.$sectionsMostActive.'</div>';
						if($allYouFollow)	 
							$allYouFollow = '<div class="'.$hrDividerCls.'"><label class="flab">People you Follow'.$totalYouFollow.':</label>
											'.$allYouFollow.'</div>';
										
						if($allFollowers)	 
							$allFollowers = '<div class="'.$hrDividerCls.'"><label class="flab">Your Followers'.$totalFollowers.':</label>
										'.$allFollowers.'</div>';
						

						if($sectionsModerated)						
							$sectionsModerated = '<div class="'.$hrDividerCls.'"><label  class="flab" > You moderate in:</label>
													'.$sectionsModerated.'</div>';																			
					 
						if($this->ENGINE->datetime_true(($scheduledTerminationTime = $row["SCHEDULED_TIME_FOR_DELETE"])))
							$alertScheduledTermination = '<div class="alert alert-danger">'.strtoupper($username).'<br/> your account is currently scheduled for termination and will be processed in the next '.$SITE->getCountDownClockBox($scheduledTerminationTime).'<br/>
															If you did not initiate this request or wish to roll back the action, please navigate to <a href="/edit-profile#cus_tab" class="links">edit profile</a> page, under "personal preferences" section, uncheck the "scheduled termination" box and click on "update profile" button.
														</div>';													
						
						$disabledOrReadonlyAttr = 'disabled="disabled" title="This field is not editable"';
						
						$userprofile = '
						
						<div class="base-ctrl base-rad profile-box" data-has-avatar-like="true">					
							<h2 class="userondp" id="userondp"><a class="links" href="/'.$username.'">'.$FA_user.' My Profile:</a> </h2>
							'.$this->getUserSignature($username, false, true, '<br/>').
							$viewAsLink.$SITE->getMeta('unactivated-user-poster').$alertScheduledTermination.'
							<div class="base-container">'.$birthdayGreetings.$lavatar.'</div>
							'.$avatar.$timeAvatarUploaded.$timeRegistered.$timeLastSeen.'<br/><br/>
					
							<form data-form-field-tip="true" class="inline-form inline-form-default block-label base-container"  name="user_prof" action="/'.$username.'" >
								<fieldset>
									<div class="field-ctrl">
										<label>Username:</label>
										<input class="field" type="text" id="" '.$disabledOrReadonlyAttr.' value="'.$row["USERNAME"].'" name="username" />
									</div>
									<div class="field-ctrl">
										<label>First name:</label>
										<input class="cap-first field" type="text" '.$disabledOrReadonlyAttr.' id="" value="'.$row["FIRST_NAME"].'" name="firstName" />
									</div>
									<div class="field-ctrl">
										<label>Last name:</label>
										<input class="cap-first field"  type="text" '.$disabledOrReadonlyAttr.' id="" value="'.$row["LAST_NAME"].'" name="lastName" />
									</div>
									<div class="field-ctrl">
										<label>'.$FA_phone.' Phone:</label>
										<input class="field" type="text" '.$disabledOrReadonlyAttr.' id="" value="'.$row["PHONE"].'" name="phone" />
									</div>
									<div class="field-ctrl">
										<label>E-mail:</label>
										<input class="field" type="email" '.$disabledOrReadonlyAttr.' id="" value="'.$row["EMAIL"].'" name="email" />
									</div>
									<div class="field-ctrl">
										<label>Gender:</label>
										<input class="field" '.$disabledOrReadonlyAttr.' type="text" value="'.$row["SEX"].'" />
									</div>
									<div class="field-ctrl">
										<label>Marital Status:</label>
										<input class="field" '.$disabledOrReadonlyAttr.' type="text" value="'.$row["MARITAL_STATUS"].'" />
									</div>
									<div class="field-ctrl">
										<label>Location:</label>
										<input class="field" '.$disabledOrReadonlyAttr.' type="text" value="'.$row["LOCATION"].'" />
									</div>
									<div class="field-ctrl">
										<label>Date of Birth:</label>
										<input class="field" '.$disabledOrReadonlyAttr.' type="text" name="dob" value="'.$userDob.'" />
									</div>
									<div class="field-ctrl">
										<label>Website/URL:</label>
										<input class="field" type="text" '.$disabledOrReadonlyAttr.' name="website" value="'.$row["WEBSITE_URL"].'" />
									</div>
									<div class="field-ctrl">
										<label>Facebook:</label>
										<input class="field" type="text" '.$disabledOrReadonlyAttr.' name="facebook" value="'.$row["FACEBOOK_URL"].'" />
									</div>
									<div class="field-ctrl">
										<label>Twitter:</label>
										<input class="field" type="text" '.$disabledOrReadonlyAttr.' name="twitter" value="'.$row["TWITTER_URL"].'" />
									</div>
									<div class="field-ctrl">
										<label>Instagram:</label>
										<input class="field" type="text" '.$disabledOrReadonlyAttr.' name="instagram" value="'.$row["INSTAGRAM_URL"].'" />
									</div>
									<div class="field-ctrl">
										<label>LinkedIn:</label>
										<input class="field" type="text" '.$disabledOrReadonlyAttr.' name="linkedIn" value="'.$row["LINKEDIN_URL"].'" />
									</div>
									<div class="field-ctrl">
										<label>WhatsApp:</label>
										<input class="field" type="text" '.$disabledOrReadonlyAttr.' name="whatsapp" value="'.$row["WHATSAPP_URL"].'" />
									</div>
									<div class="field-ctrl">
										<label>Signature:</label>
										<textarea class="field" '.$disabledOrReadonlyAttr.' name="signature" >'.$row["SIGNATURE"].'</textarea>
									</div>
									<div class="field-ctrl">
										<label>About You:</label>
										<textarea class="field" '.$disabledOrReadonlyAttr.' name="aboutyou" >'.$row["ABOUT_YOU"].'</textarea>
									</div><b class="clear"></b>
									<div class="'.$userBadgesDivider.' align-l">
										'.$badges.'
									</div>
									<div class="btn-group-ctrl"><br/><br/>
										<div class="field-ctrl btn-ctrl">								
											<a  class="form-btn btn-lg" role="button" href="/edit-profile" >'.$SITE->getFA('fas fa-edit', array("title"=>'Edit')).' Edit Profile</a>
										</div>&nbsp;&nbsp;&nbsp;
										<div class="field-ctrl btn-ctrl">				
											<a  href="/cancelaccount" role="button" class="form-btn btn-lg btn-danger" data-toggle="smartToggler" data-id-targets="modal-1" >Cancel Account</a>									
										</div>
										<div class="hide" id="modal-1">						
											<div class="alert alert-danger" >
												<b> WARNING!!!<hr/>
													<p>' .strtoupper($sessUsername). '</p> 
													you are about to initiate your account termination procedure with us at <a href="/" class="links"> '.$SITE->getSiteName().'</a>
													<p>please confirm</p>													
													<div class="" >
														<input type="button" class="confirm_cancel btn btn-sm btn-danger" value="OK" /> 
														<input class="btn btn-sm close-toggle" type="button" value="CLOSE" />
													</div>
												</b>
											</div>
										</div>
									</div>
								</fieldset>	
							</form>																		
							 <br/><br/>'. $sectionsModerated.$sectionsMostActive.$allYouFollow.$allFollowers.'																						
						</div>';

					}



					
					if(!$isSess){
							
						if($sectionsMostActive)	 
							$sectionsMostActive = '<div class="hr-divider"><label class="flab">Sections <a class="links" href="/'.$username.'">'.$username.'</a> is most active:</label>
											 '.$sectionsMostActive.'</div>';

						if($allYouFollow)	 
							$allYouFollow = '<div class="hr-divider"><label class="flab">People this member follow'.$totalYouFollow.':</label>
											'.$allYouFollow.'</div>';				
						
						//if($allFollowers)	 
						$allFollowers = '<div class="hr-divider mem-unf-base '.($allFollowers? '' : 'hide fm-n1').'"><label class="flab">People following this member'.$totalFollowers.($allFollowers? '' : '(<span class="cyan fm-count-disp">0</span>)').':</label>
											<span class="flws-base">'.$allFollowers.'</span></div>';					
							

						/*if($row["FIRST_NAME"] ||  $row["LAST_NAME"])
							$fullNameFields = '<div class="per-vo-base clear"><label>First name:</label>
											<span>'.$row['FIRST_NAME'].'</span></div>
											<div class="vo_wrap"><label>Last name:</label>
											<span>'.$row['LAST_NAME'].'</span></div>';*/

						/*if($row["EMAIL"])
								$email =	'<div class="per-vo-base clear"><label>E-mail:</label>
											<span><a href="mailto:'.$row['EMAIL'].'" >'.$row['EMAIL'].'</a></span></div>';*/
					
						$perVoCls = ''; //'per-vo-base clear';
						
						if($gender = $row["SEX"])					
							$gender =	'<div class="'.$perVoCls.'">
											<label>Gender:</label>
											<span>'.$gender.'</span>
										</div>';

				
						if($maritaStat = $row["MARITAL_STATUS"])					
							$maritaStat =	'<div class="'.$perVoCls.'">
												<label>Marital Status:</label>
												<span>'.$maritaStat.'</span>
											</div>';

				
						if($location = $row["LOCATION"])					
							$location =	'<div class="'.$perVoCls.'">
											<label>Location:</label>
											<span>'.$location.'</span>
										</div>';

						if($dob)
							$dob =	'<div class="'.$perVoCls.'">
										<label>Birthday:</label>
										<span>'.$userDob.'</span>
									</div>';

						if($website = $row["WEBSITE_URL"])			
							$externalLinks .= '<div class="'.$perVoCls.'">
												<label>Website/URL:</label>
												<span>'.$SITE->idCsvToNameString($website, 'xurl').'</span>
											</div>';

						if($facebook = $row["FACEBOOK_URL"])			
							$externalLinks .= '<div class="'.$perVoCls.'">
												<label>Facebook:</label>
												<span><a href="http://'.$facebook.'" class="links">'.$facebook.'</a></span>
											</div>';

						if($twitter = $row["TWITTER_URL"])			
							$externalLinks .= '<div class="'.$perVoCls.'">
												<label>Twitter:</label>
												<span><a href="http://'.$twitter.'" class="links">'.$twitter.'</a></span>
											</div>';

						if($instagram = $row["INSTAGRAM_URL"])			
							$externalLinks .= '<div class="'.$perVoCls.'">
												<label>Instagram:</label>
												<span><a href="http://'.$instagram.'" class="links">'.$instagram.'</a></span>
											</div>';

						if($linkedIn = $row["LINKEDIN_URL"])			
							$externalLinks .= '<div class="'.$perVoCls.'">
												<label>Instagram:</label>
												<span><a href="http://'.$linkedIn.'" class="links">'.$linkedIn.'</a></span>
											</div>';

						if($whatsapp = $row["WHATSAPP_URL"])						
							$externalLinks .= '<div class="'.$perVoCls.'">
												<label>WhatsApp:</label>
												<span><a href="http://'.$whatsapp.'" class="links">'.$whatsapp.'</a></span>
											</div>';

						if($signature = $row["SIGNATURE"])
							$signature = '<div class="'.$perVoCls.'">
											<label> <a class="links" href="/'.$username.'">'.ucfirst($username).'</a>\'s signature:</label>
											<span>'.nl2br($signature).'</span>
										</div>';


						if($aboutyou = $row["ABOUT_YOU"])
							$aboutyou =	'<div class="'.$perVoCls.'">
											<label>About  <a class="links" href="/'.$username.'">'.ucfirst($username).'</a>:</label>
											<span>'.nl2br($aboutyou).'</span>
										</div>';

						if($sectionsModerated)						
							$sectionsModerated = '<div class="hr-divider">
													<label class="flab" ><a class="links" href="/'.$username.'">'.ucfirst($username).'</a> moderates in:</label>
													'.$sectionsModerated.'
												</div>';
											
						if($sessUsername){

				
							$blacklisted = $SITE->pmBlacklistHandler(array('action'=>'check','buid'=>$userId));
							$blacklistIsStaff = $this->isStaff($username);
							$retLink = '?_rdr='.$rdr;

				
							$contactByMessage = '<div class="align-c">
														<div class="clear">
															<a title="compose and send a private message(PM) to '.$username.'" class="btn btn-sc btn-sm" href="/pm/'.$username.'" role="button" >PM '.$username.' </a>
															<a title="compose and send an E-mail to '.$username.'" class="btn btn-sc btn-sm" href="/email/'.$username.'" >'.$SITE->getFA('fas fa-envelope', array("title"=>'Mail')).' Mail '.$username.' </a>
														</div>
														'.($blacklistIsStaff? '' : 
														'<div id="prof-pmb" class="hash-focus">
															<div>'.$this->ENGINE->get_global_var("ss", "SESS_ALERT").'</div>
															<div class="alert alert-'.($blacklisted? 'danger' : 'warning').'">'.($blacklisted? $username.' is currently on your blacklist,  <a title="Remove '.$username.' from your blacklist" class="btn btn-success" href="/pm-blacklist/remove/'.$username.$retLink.'" >'.$SITE->getFA('fas fa-check').' click here </a> to remove this user from your blacklist' :
															'If you do not wish to receive private messages or emails from '.$username.', <a title="Blacklist '.$username.'" class="btn btn-success" href="/pm-blacklist/add/'.$username.$retLink.'" >'.$SITE->getFA('fas fa-ban').' click here </a> to blacklist this user').'</div>
														</div>').'
													</div>';

				

						}				
						
						
						$userprofile = '

						<div class="base-ctrl base-rad profile-box" data-has-avatar-like="true">
						
							<h2 id="userondp"><a class="links" href="/'.$username.'">'.$SITE->getFA('fa-user').$username.'\'s Profile:</a>  </h2>
							'.$this->getUserSignature($username, false, true, '<br/>').'
							'.$exitViewAsLink.$birthdayGreetingsXT.$lavatar.'
							<div id="dpl">'.$avatar.'</div>

							<div>'.$timeAvatarUploaded.'</div>
							
							<div>'.$timeRegistered.($CU->getOnlineStatus()? $timeLastSeen : '').$this->getUserActiveStatus($userId, true).'</div>
							
							<div>'.$memberFollowLink.'</div>
							
							<div class="vo-base" >	
								<form  name="user_prof" action="/'.$username.'" onsubmit="return false;">
									<fieldset class="hr-dividers">
										<div class="'.$perVoCls.'">
											<label>Username:</label>
											<span>'.$row["USERNAME"].'</span>
										</div>
										'.$fullNameFields. $email. $gender. $maritaStat. $location. $dob. $externalLinks. $signature. $aboutyou.								
										 $sectionsModerated.$sectionsMostActive.$allFollowers.$allYouFollow.'
										<div class="'.$userBadgesDivider.' align-l">
											'.$badges.'
										</div>
									</fieldset>
								</form>			
							</div>
							'.$contactByMessage
						.'<br/></div>';

					}

				}else		
					$alert = '<div class="alert alert-danger">Sorry that user was not found</div>';
				
			}

		}else
			$alert = '<div class="alert alert-danger">An unexpected error has occurred, we are sorry about this</div>';

		$SITE->buildPageHtml(array("pageTitle"=>$header,
					"preBodyMetas"=>$SITE->getNavBreadcrumbs($subNav),
					"pageHeaderMetas"=>'<style>'.($embeddedAvatarBgStyles).'</style>',
					"pageBody"=>'
						<div class="single-base blend">	
							<div class="base-ctrl '.(isset($clrBotCls)? $clrBotCls : '').'">
								<div class="base-ctrl base-radX bg-img2">
									<nav class="nav-base"><ul class="nav nav-tabs justified justified-bom">'.$tab.'</ul></nav>
									'.$tabQUDS.'
								</div>'.
								(isset($tabContents)? $tabContents : '').'												
								<div id="ajax-res">'.(isset($alert)? $alert : '').'</div>'.
								(isset($userprofile)? $userprofile : '').									
								($isSess? $SITE->displaySiteTraffic("Your Profile") : '').'
							</div>
						</div>'
			));


	
	}

	
	
	
	
	
	
	
	
	
	
	/*** Method for handling user profile edit request ***/
	public function handleUserProfileEditRequest(){
		
		global $SITE, $GLOBAL_page_self_rel, $GLOBAL_sessionUrl_unOnly, $mediaRootFav, $mediaRootAvtXCL, $siteName, $siteDomain, $FA_phone, $FA_cog, $FA_infoCircle;
		
		$notLogged=$err=$err2=$autofocus=$aboutyouErr=$signatureErr=$signatureFieldErr=$antiPhishingCodeErr=$antiPhishingCodeFieldErr=
		$aboutyouFieldErr=$badgeOption=$maleSelected=$femaleSelected=$prefPerPageOpt=$maritalStatOpt="";
	
		$maxAvatarUploadSize = MAX_POST_UPLOAD_SIZE_STR;
		$cusTabK="ext_cus_tab"; $cusTabV="ext_cus_more";
		$extCusTab = isset($_GET[$cusTabK]) && (strtolower($_GET[$cusTabK]) == $cusTabV);
		
		$sessUsername = $this->SESS->getUsername();
		$sessUid = $this->SESS->getUserId();
		
		if($sessUsername){

			$fileTooLarge = $this->ENGINE->get_large_upload_limit_error();

			/////////ON SUBMITTING YOUR EDITED PROFILE/////

			if(isset($_POST['submit'])){
						
				$username = $this->ENGINE->sanitize_user_input($_POST['username']);
				//$pwd1 = $this->ENGINE->sanitize_user_input($_POST['pwd1']);
				//$pwd2 = $this->ENGINE->sanitize_user_input($_POST['pwd2']);
				$firstName = $this->ENGINE->sanitize_user_input($_POST['firstName']);
				$lastName = $this->ENGINE->sanitize_user_input($_POST['lastName']);
				$fullname = $firstName." ".$lastName;
				$phone = $this->ENGINE->sanitize_user_input($_POST['phone']);
				$email = $this->ENGINE->sanitize_user_input($_POST['email']);
				$dobDay = $_POST['date_day'];
				$dobMonth = $_POST['date_month'];
				$dobYear = $_POST['date_year'];
				$sex = $_POST['sex'];
				$maritaStat = $_POST['marital_stat'];
				$location = $this->ENGINE->sanitize_user_input($_POST['location']);
				$url = $this->ENGINE->sanitize_user_input($_POST["website"]);
				$fb = $this->ENGINE->sanitize_user_input($_POST["facebook"]);
				$twitter = $this->ENGINE->sanitize_user_input($_POST["twitter"]);
				$instagram = $this->ENGINE->sanitize_user_input($_POST["instagram"]);
				$linkedIn = $this->ENGINE->sanitize_user_input($_POST["linkedIn"]);
				$whatsapp = $this->ENGINE->sanitize_user_input($_POST["whatsapp"]);
				$signature = $this->ENGINE->sanitize_user_input($_POST["signature"]);
				$antiPhishingCode = $this->ENGINE->sanitize_user_input($_POST["anti_phishing_code"]);
				$aboutyou = $this->ENGINE->sanitize_user_input($_POST['aboutyou']);		
				$prefPerPage = $this->ENGINE->sanitize_user_input($_POST['prefppp']);
				$prefPerPageDb = (strtolower($prefPerPage) == "default")? 0 : $prefPerPage;			
				$prefPerPageDb = $this->ENGINE->sanitize_number($prefPerPageDb);		
				$signatureVsib = (isset($_POST["sign_vsib"]))? 1 : 0;			 
				$floatingPageScrollOpt = (isset($_POST["floating_scroll"]))? 1 : 0;		
				$saib = (isset($_POST["saib"]))? 1 : 0;	////Show Age In Birthdays
				$onlineStatus = (isset($_POST["online_status"]))? 1 : 0;
				$showMembersBadge = (isset($_POST["badge_opt"]))? 1 : 0;
				$showMyBadges = (isset($_POST["mem_badge_opt"]))? 1 : 0;
				$pavatars = (isset($_POST["pavatars"]))? 1 : 0;
				$pimages = (isset($_POST["pimages"]))? 1 : 0; ///SHOW ME POST IMAGES////
				$xmqOpt = (isset($_POST["xmq_opt"]))? 1 : 0; ////ALLOW CROSS MULTIQUOTE////
				$unsheduleTerminationSubQry = (!isset($_POST["unshedule_termination"]))? 'SCHEDULED_TIME_FOR_DELETE = 0,' : '';
					
				
				
				//LIMIT PHONE LENGTH////
				if($phone && (mb_strlen($phone) > MAX_PHONE || mb_strlen($phone) < MIN_PHONE)){

					$fieldValErr = $phoneLenErr = true;
					$phoneErr = '<span class="asterix">*</span>';
					$phoneFieldErr = 'field-error';

				}

				
				//LIMIT SIGNATURE LENGTH////
				if($signature && mb_strlen($this->ENGINE->filter_line_chars($signature)) > MAX_SIGNATURE){

					$fieldValErr = $signatureLenErr = true;
					$signatureErr = '<span class="asterix">*</span>';
					$signatureFieldErr = 'field-error';

				}
				
				//LIMIT SIGNATURE LENGTH////
				if($antiPhishingCode && mb_strlen($this->ENGINE->filter_line_chars($antiPhishingCode)) > MAX_SIGNATURE){

					$fieldValErr = $antiPhishingCodeLenErr = true;
					$antiPhishingCodeErr = '<span class="asterix">*</span>';
					$antiPhishingCodeFieldErr = 'field-error';

				}

				
				//LIMIT ABOUT YOU LENGTH////
				if($aboutyou && mb_strlen($this->ENGINE->filter_line_chars($aboutyou)) > MAX_ABOUT_YOU){

					$fieldValErr = $aboutyouLenErr = true;
					$aboutyouErr = '<span class="asterix">*</span>';
					$aboutyouFieldErr = 'field-error';

				}
				
				$avatar=$avatarBg="";////VERY IMPORTANT///
				
				
				////HANDLE FOR DATE CONTROL//////
				
				$dob = $SITE->ConvertToDatabaseDate($dobDay, $dobMonth, $dobYear);
				
				///GENDER///////		
				if($sex == "Male"){

					$maleSelected = "selected";
					$sex = 'M';

				
				}elseif($sex == "Female"){
				
					$femaleSelected = "selected";
					$sex = 'F';
				
				}


				////FETCH AN EXISTING DP SO THAT IF NO NEW UPLOAD WAS MADE DURING EDIT IT RETAINS OLD DP///

				///////////PDO QUERY///////
				
				$sql = "SELECT AVATAR, AVATAR_BG, AVATAR_UPLOAD_TIME FROM users WHERE USERNAME=? LIMIT 1";
				$valArr = array($username);
				$stmt = $this->DBM->doSecuredQuery($sql, $valArr);
				$row = $this->DBM->fetchRow($stmt);

				$oldAvatarFile = $row['AVATAR'];
				$oldAvatarBgFile = $row['AVATAR_BG'];
				
				$uok = 1; //JUST INCASE THE USER IS'NT UPLOADING AN AVATAR
			
				//////UPLOADING A PROFILE AVATAR///////////
									
				##PRECONFIG UPLOADER
				$htmlName = 'avatar';
				$uploadpath = $mediaRootAvtXCL;	
				$allowedExtArr = array("jpg","jpeg","png");
				$sizeLimit =  5242880;/**5mb**/
				$widthLimit = "";/****/
				$heightLimit = "";/****/					
				$imgTerm = 'avatar';
				$uploadType = 'single'; //UPLOADER INTELLIGENTLY DECODES THE TYPES
				
				$FU = new FileUploader($htmlName, $uploadpath, $allowedExtArr, $sizeLimit, $widthLimit, $heightLimit, $imgTerm);
									
				//EXPLICITLY SET OVERWRITE TO FALSE(THOUGH DEFAULT == false)
				$FU->setOverwrite(false);	
									
				//RENAME FILES
				$FU->setRename(true);					
				
				if($FU->fileIsSelected()){
						
					$FU->upload();
					$uok = $FU->getUploadStatus();	
					$err = $FU->getErrors();												
					list($userUploadedAvatar) = $FU->getUploadedFiles(true);//RETURN UPLOADED FILE AS AN ARRAY										
					
					if($uok){
						
						$avatar = $userUploadedAvatar;

						$path2del = $uploadpath.$oldAvatarFile;

						if(realpath($path2del) && $oldAvatarFile)
							unlink($path2del);
						
					}elseif(!$uok)									
						$autofocus = "autofocus";		
				}

				if(!$avatar)
					$avatar = $oldAvatarFile;

				/*******UPDATE AVATAR_UPLOAD_TIME ONLY WHEN NEW AVATAR IS UPLOADED**********/
																					
				$avatarUploadTime = ($avatar && $avatar != $oldAvatarFile)? 'AVATAR_UPLOAD_TIME=NOW(),' : '';
													
				
				//////UPLOADING A AVATAR BG///////////
									
				##PRECONFIG UPLOADER
				$htmlName = 'avatar_bg';
				$uploadpath = $mediaRootAvtXCL;	
				$allowedExtArr = array("jpg","jpeg","png");
				$sizeLimit =  5242880;/**5mb**/
				$widthLimit = "";/****/
				$heightLimit = "";/****/					
				$imgTerm = 'avatar background';
				$uploadType = 'single'; //UPLOADER INTELLIGENTLY DECODES THE TYPES
				
				$FU = new FileUploader($htmlName, $uploadpath, $allowedExtArr, $sizeLimit, $widthLimit, $heightLimit, $imgTerm);
									
				//EXPLICITLY SET OVERWRITE TO FALSE(THOUGH DEFAULT == false)
				$FU->setOverwrite(false);	
									
				//RENAME FILES
				$FU->setRename(true);					
				
				if($FU->fileIsSelected()){
						
					$FU->upload();
					$uok &= $FU->getUploadStatus();	
					$err2 = $FU->getErrors();												
					list($userUploadedAvatarBg) = $FU->getUploadedFiles(true);//RETURN UPLOADED FILE AS AN ARRAY										
					
					if($uok){
						
						$avatarBg = $userUploadedAvatarBg;

						$path2del = $uploadpath.$oldAvatarBgFile;

						if(realpath($path2del) && $oldAvatarBgFile)
							unlink($path2del);
						
					}elseif(!$uok)									
						$autofocus = "autofocus";		
				}

				if(!$avatarBg)
					$avatarBg = $oldAvatarBgFile;
				

				if($uok && !isset($fieldValErr)){

					///////////PDO QUERY//////
					
					//NON EDITABLE FIELDS: USERNAME, FIRST_NAME, LAST_NAME & EMAIL are excluded from the DB update query
					
					$sql = "UPDATE users SET ".$unsheduleTerminationSubQry."PHONE=?, 
							ABOUT_YOU =?, AVATAR=?, AVATAR_BG=?, ".$avatarUploadTime." SEX=?, MARITAL_STATUS=?, LOCATION=?, DOB=?, WEBSITE_URL=?,
							FACEBOOK_URL=?, TWITTER_URL=?, INSTAGRAM_URL=?, LINKEDIN_URL=?, WHATSAPP_URL=?, SIGNATURE=?, ANTI_PHISHING_CODE=?, SIGNATURE_VSIB=?, MPS_OPT=?, BADGE_OPT=?, 
							MEMS_BADGE_OPT=?, MAX_PER_PAGE=?, XMQ_OPT=?, POST_AVATARS=?, POST_IMAGES=?, SAIB=?, ONLINE_STATUS=?  WHERE USERNAME=? LIMIT 1";


					$valArr = array(
					
								$phone, $aboutyou, $avatar, $avatarBg, $sex, $maritaStat, $location, $dob, $url, $fb, $twitter, $instagram, 
								$linkedIn, $whatsapp, $signature, $antiPhishingCode, $signatureVsib, $floatingPageScrollOpt, $showMembersBadge, $showMyBadges, $prefPerPageDb, $xmqOpt, $pavatars, $pimages, $saib, 
								$onlineStatus, $username
								
							);
							
					$stmt = $this->DBM->doSecuredQuery($sql, $valArr);
					
					$alertUser = '<p class="alert alert-success">'.$GLOBAL_sessionUrl_unOnly.' your profile has been updated<br/> successfully</p>';

					header("Location:/".$this->SESS->getUsernameSlug());
					exit();


				}


			}


			///PDO QUERY///////
			
			$sql = "SELECT * FROM users WHERE USERNAME=? LIMIT 1";
			$valArr = array($sessUsername);
			$stmt = $this->DBM->doSecuredQuery($sql, $valArr);
				
			while($row = $this->DBM->fetchRow($stmt)){					
						
				//////GENDER///////
				if(!isset($sex))
					$sex = $row["SEX"];
				
				$sex = strtolower($sex);
				
				if($sex == 'm')
					$maleSelected = 'selected';
				
				elseif($sex == 'f')
					$femaleSelected = 'selected';
				
				///MARITAL STATUS///

				if(!isset($maritaStat))
					$maritaStat = $row["MARITAL_STATUS"];
				
				foreach(MARITAL_ARR as $mstat)
					$maritalStatOpt .= '<option '.(($maritaStat == $mstat)? 'selected' : '').'>'.$mstat.'</option>';
				
				
				$maritalStatOpt = '<select id="mstat" class="field" name="marital_stat">'.$maritalStatOpt.'</select>';
				
				$prefPerPage = $row["MAX_PER_PAGE"];
				$signatureVsib = $row["SIGNATURE_VSIB"];
				$floatingPageScrollOpt = $row["MPS_OPT"]; // MID PAGE FLOATING SCROLL
				$showMembersBadge = $row["BADGE_OPT"];
				$showMyBadges = $row["MEMS_BADGE_OPT"];
				$xmqOpt = $row["XMQ_OPT"];
				$pavatars = $row["POST_AVATARS"];
				$pimages = $row["POST_IMAGES"];
				$saib = $row["SAIB"]; // SHOW AGE IN BIRTHDAY
				$onlineStatus = $row["ONLINE_STATUS"];
				
				for($idx=10; $idx <= 50; $idx += 10 )
					$prefPerPageOpt .= '<option '.(($idx == $prefPerPage)? 'selected' : '').'>'.$idx.'</option>';
				
				$prefPerPageOpt = '<label>Pagination Count:</label><select class="field col-w-10" name="prefppp"><option>Default</option>'.$prefPerPageOpt.'</select>';		
				
				$onlineStatus = $SITE->getHtmlComponent($componentType='switch-slider', array('label'=>'Show my online status to others:', 'fieldName'=>'online_status', 'on'=>$onlineStatus));
				
				$signatureVsibOption = $SITE->getHtmlComponent($componentType, array('label'=>'Show my signature in my posts:', 'fieldName'=>'sign_vsib', 'on'=>$signatureVsib));
				
				$badgeOption = $SITE->getHtmlComponent($componentType, array('label'=>'Show my badges in my posts:', 'fieldName'=>'badge_opt', 'on'=>$showMembersBadge));
				
				$showMyBadges = $SITE->getHtmlComponent($componentType, array('label'=>'Show me badges in posts:', 'fieldName'=>'mem_badge_opt', 'on'=>$showMyBadges));
				
				$pavatars = $SITE->getHtmlComponent($componentType, array('label'=>'Show me avatars in posts:', 'fieldName'=>'pavatars', 'on'=>$pavatars));
				
				$saib = $SITE->getHtmlComponent($componentType, array('label'=>'Show my age in site birthdays:', 'fieldName'=>'saib', 'on'=>$saib));
				
				$pimages = $SITE->getHtmlComponent($componentType, array('label'=>'Show me images in posts:', 'fieldName'=>'pimages', 'on'=>$pimages));
				
				$mpsOption = $SITE->getHtmlComponent($componentType, array('label'=>'Enable page skip floating button:', 'title'=>'floating button to skip to top or bottom of current page', 'fieldName'=>'floating_scroll', 'on'=>$floatingPageScrollOpt));
				
				$xmqOpt = $SITE->getHtmlComponent($componentType, array('label'=>'Enable cross thread multi-quote:', 'fieldName'=>'xmq_opt', 'on'=>$xmqOpt));
				
				
				///////SCHEDULED FOR TERMINATION///////						
				$unscheduleTerminationCheckBox = $this->ENGINE->datetime_true($row["SCHEDULED_TIME_FOR_DELETE"])? $SITE->getHtmlComponent('iconic-checkbox', array('label'=>'Sheduled Termination:', 'title'=>'Your account has been scheduled for termination, please uncheck this box and update your profile to opt out of your account termination request', 'wrapClass'=>'red', 'fieldName'=>'unshedule_termination', 'on'=>true)).'<hr/>' : '';					
				
				///////PHONE///////		
				if(!isset($phone))
					$phone = $row["PHONE"];
				
				///////DOB///////		
				if(!isset($dob))
					$dob = $row["DOB"];		
				
				///LOCATION///
				if(!isset($location))
					$location = $row["LOCATION"];
				
				///WEBSITE_URL///
				if(!isset($url))
					$url = $row["WEBSITE_URL"];
				
				///FACEBOOK_URL///
				if(!isset($fb))
					$fb = $row["FACEBOOK_URL"];
					
				///TWITTER_URL///
				if(!isset($twitter))
					$twitter = $row["TWITTER_URL"];
					
				///INSTAGRAM_URL///
				if(!isset($instagram))
					$instagram = $row["INSTAGRAM_URL"];
					
				///LINKEDIN_URL///
				if(!isset($linkedIn))
					$linkedIn = $row["LINKEDIN_URL"];
					
				///WHATSAPP_URL///
				if(!isset($whatsapp))
					$whatsapp = $row["WHATSAPP_URL"];
					
				///SIGNATURE///
				if(!isset($signature))
					$signature = $row["SIGNATURE"];
					
				///EMAIL DISPATCH SIGNATURE///
				if(!isset($antiPhishingCode))
					$antiPhishingCode = $row["ANTI_PHISHING_CODE"];
				
				///ABOUT_YOU///
				if(!isset($aboutyou))
					$aboutyou = $row["ABOUT_YOU"];
				
				///AVATAR////
				if(!isset($avatar))	
					$avatar = $row["AVATAR"];
					
				if($avatar)
					$avatar = $SITE->getMeta('file-preview-tip').'<label>Current Avatar File: <span class="has-file-preview"><a class="links file-preview" data-file="'.$avatar.'" href="'.$SITE->getDownloadURL($avatar, "profile").'">'.$avatar.'</a> <a href="/remove-file?file='.$avatar.'&tgt=avatar" data-remove-file="avatar" data-file="'.$avatar.'" rel="nofollow" ><img class="delete" src="'.$mediaRootFav.'delete.png" alt="'.($tmp='remove your avatar').'" title="'.$tmp.'" /></a> </span></label>';
				
				///AVATAR BG////
				if(!isset($avatarBg))	
					$avatarBg = $row["AVATAR_BG"];
				
				if($avatarBg)
					$avatarBg = $SITE->getMeta('file-preview-tip').'<label>Current Avatar Background File: <span class="has-file-preview"><a class="links file-preview" data-file="'.$avatarBg.'" href="'.$SITE->getDownloadURL($avatarBg, "profile").'">'.$avatarBg.'</a> <a href="/remove-file?file='.$avatarBg.'&tgt=avatar_bg" data-remove-file="avatar_bg" data-file="'.$avatarBg.'" rel="nofollow" ><img class="delete" src="'.$mediaRootFav.'delete.png" alt="'.($tmp='remove your avatar background image').'" title="'.$tmp.'" /></a> </span></label>';
				
				$disabledOrReadonlyAttr = 'readonly="readonly" title="This field is not editable"';
				
				$editProfile = '
						<form name="edit_prof" class="inline-form inline-form-default block-label" action="/edit-profile" method="post" enctype="multipart/form-data">	
							<fieldset>
								<div class="field-ctrl">
									<label for="un">Username:</label>
									<input id="un" class="field" type="text" '.$disabledOrReadonlyAttr.' value="'.$row['USERNAME'].'"  name="username" />
								</div>
								<div class="field-ctrl">
									<label for="fn">First Name:</label>
									<input id="fn" class="field" placeholder="example: John" '.$disabledOrReadonlyAttr.' type="text"  value="'.$row['FIRST_NAME'].'"  name="firstName" />
								</div>
								<div class="field-ctrl">
									<label for="ln">Last Name:</label>
									<input id="ln" class="field" placeholder="example: Smith" '.$disabledOrReadonlyAttr.' type="text"  value="'.$row['LAST_NAME'].'" name="lastName" />
								</div>
								<div class="field-ctrl">
									<label for="phone">'.$FA_phone.' Phone:</label>
									<input id="phone" class="field" data-field-count="true" maxlength="'.MAX_PHONE.'" type="text"  value="'.$phone.'"  name="phone" />
								</div>
								<div class="field-ctrl">
									<label for="em">E-mail:</label>
									<input id="em" class="field" '.$disabledOrReadonlyAttr.' type="email"  value="'.$row['EMAIL'].'"  name="email" />
									<div class="base-tb-mg">
										<a role="button" href="/change-email" class="btn btn-sc btn-classic-sc">Change Email</a>
									</div>
								</div>
								<div class="field-ctrl">
									<label for="gnd">Gender:</label>
									<select id="gnd" class="field" name="sex">
										<option '.$maleSelected.' >Male</option>
										<option '.$femaleSelected.'>Female</option>
									</select>				
								</div>
								<div class="field-ctrl">
									<label for="mstat">Marital Status:</label>
									'.$maritalStatOpt.'
								</div>
								<div class="field-ctrl">
									<label for="loc">Location:</label>
									<input id="loc" class="field"  maxlength="'.MAX_EXT_URL.'" placeholder="example: Lagos, Nigeria" type="text" name="location" value="'.$location.'" />
								</div>
								<div class="field-ctrl">
									<label for="dob">Date of Birth:</label>
									'.$SITE->generateDateSelectField($dob, SITE_ACCESS_MIN_AGE).'
								</div>
								<div class="field-ctrl">
									<label for="web">Website/URL:</label>
									<input id="web" class="field" maxlength="'.MAX_EXT_URL.'" placeholder="example:'.$siteDomain.'" type="text" name="website" value="'.$url.'" />
								</div>
								<div class="field-ctrl">
									<label for="fb">Facebook:</label>
									<input  id="fb" class="field"  maxlength="'.MAX_EXT_URL.'" placeholder="example: www.facebook.com/'.$sessUsername.'" type="text" name="facebook" value="'.$fb.'" />
								</div>
								<div class="field-ctrl">
									<label for="twt">Twitter:</label>
									<input id="twt" class="field"  maxlength="'.MAX_EXT_URL.'" placeholder="example: www.twitter.com/'.$sessUsername.'"  type="text" name="twitter" value="'.$twitter.'" />
								</div>
								<div class="field-ctrl">
									<label for="instagram">Instagram:</label>
									<input id="instagram" class="field"  maxlength="'.MAX_EXT_URL.'" placeholder="example: www.instagram.com/'.$sessUsername.'"  type="text" name="instagram" value="'.$instagram.'" />
								</div>
								<div class="field-ctrl">
									<label for="linkedIn">LinkedIn:</label>
									<input id="linkedIn" class="field"  maxlength="'.MAX_EXT_URL.'" placeholder="example: www.linkedin.com/in/'.$sessUsername.'-23456"  type="text" name="linkedIn" value="'.$linkedIn.'" />
								</div>
								<div class="field-ctrl">
									<label for="wa">WhatsApp:</label>
									<input id="wa" class="field" maxlength="'.MAX_EXT_URL.'" placeholder="example: +234-XXXXXXXXXX" type="text" name="whatsapp" value="'.$whatsapp.'" />
								</div>
								<div class="field-ctrl">
									<label for="sign">'.$signatureErr.'Signature:</label>
									<textarea id="sign" maxlength="'.MAX_SIGNATURE.'" data-field-count="true" class="field '.$signatureFieldErr.'" placeholder="example: Success is driven by determination" name="signature" >'.$signature.'</textarea>
								</div>
								<div class="field-ctrl">
									<label for="abtu">'.$aboutyouErr.'About You:</label>
									<textarea id="abtu" maxlength="'.MAX_ABOUT_YOU.'" data-field-count="true" class="field '.$aboutyouFieldErr.'" placeholder="example: Am very friendly and loves technological creativities and designs" name="aboutyou" >'.$aboutyou.'</textarea>
								</div>
								<div class="field-ctrl field-w-ov col-lg-w-4">
									<label for="dp">upload/change profile picture: (maximum allowed file size is 5MB)</label>				
									<span>'.$err.($err? $fileTooLarge : '').'</span>
									<input '.$autofocus.' id="dp" class="field upload-field" accept="image/*" type="file" name="avatar" value="" />
									<p>'.$avatar.'</p>
								</div>
								<div class="field-ctrl field-w-ov col-lg-w-4">
									<label for="dpBg">upload/change avatar background image: (maximum allowed file size is 5MB)</label>				
									<span>'.$err2.($err2? $fileTooLarge : '').'</span>
									<input '.$autofocus.' id="dpBg" class="field upload-field" accept="image/*" type="file" name="avatar_bg" value="" />
									<p>'.$avatarBg.'</p>
								</div><i class="clear-sides"></i>	
								<div class="row cols-pad base-tb-pad">
									<div class="field-ctrl">										
										<div class="align-lg-l">										
											<h3 class="page-title head-bg-classic-r pan bg-gold align-c">SECURITY OPTIONS:</h3><br/>											
											<label for="anti-phishing-code">'.$antiPhishingCodeErr.'Anti Phishing Code:</label>	
											<em class="pointer dark-red" data-toggle="smartToggler" title="click to learn more">'.$FA_infoCircle.'</em>
											<div class="alert alert-danger hide has-close-btn font-default">
												Anti Phishing Code is a security measure adopted to help protect '.$siteName.' members against email phishing attacks.<br/>
												you can specify a personalized and recognizable code or phrase in this field and it will be used to sign <b>every email</b> dispatched to your email address from '.$siteName.'.
												<p>
													Please note however that this security measure does not guarantee that all emails that contains this dispatch signature
													originated from '.$siteName.' or that it is intended for you.
												</p>
												<p>
													Please always take extra security measures to protect yourself against Phishing attempts.
													<br/>We also recommend you change your Anti Phishing Code as often as possible to help reinforce 
													this security measure.
												</p>	
											</div>
											<textarea id="anti-phishing-code" maxlength="'.MAX_SIGNATURE.'" data-field-count="true" class="field '.$antiPhishingCodeFieldErr.'" placeholder="example: Alaba is my secret anti phishing code" name="anti_phishing_code" >'.$antiPhishingCode.'</textarea>										
										</div>
									</div>
									<div class="field-ctrl">
										<div class="align-l hash-focus" id="cus_tab">
											<h3 class="page-title head-bg-classic-r pan bg-gold align-c">PERSONAL PREFERENCES:</h3><br/>'.
											$unscheduleTerminationCheckBox.'<hr/>'.$prefPerPageOpt.
											'<div class="v-constrict v-constrict-sm '.($extCusTab? '' : 'hide').'" '.$SITE->embedCustomScrollbar().'>
												<hr/>'.$onlineStatus.'<hr/>'.$signatureVsibOption.'<hr/>'.$saib.'<hr/>'.$badgeOption.'<hr/>'.$showMyBadges.'<hr/>'.$pavatars.'<hr/>'.$pimages.'<hr/>'.$mpsOption.'
												<hr/>'.$xmqOpt.'
											</div>'.($extCusTab? '' : '<a href="'.$GLOBAL_page_self_rel.'?'.$cusTabK.'='.$cusTabV.'" class="btn btn-info btn-classic" data-toggle="smartToggler" data-target-prev="true" data-toggle-attr="title|See less" data-toggle-child-attr-class="prof_cus" data-toggle-child-attr="text|Less" title="See more personal custom settings"><span class="prof_cus">More</span>'.$FA_cog.'</a>').'									
										</div>
									</div>
								</div>
								<div class="btn-group-ctrl">
									<div class="field-ctrl btn-ctrl col-xxs-w-10 col-sm-w-4">
										<button  class="form-btn btn-lg" type="submit" name="submit" >'.$SITE->getFA('far fa-save', array("title"=>'Save')).' update profile</button>
									</div>
									<div class="field-ctrl btn-ctrl col-xxs-w-10 col-sm-w-4">
										<a role="button" href="/changepassword" class="form-btn btn-lg btn-danger" >'.$SITE->getFA('fas fa-edit', array("title"=>'Edit')).' change password</a>
									</div>
								</div>	
							</fieldset>							
						</form>	';
					
			}


		}else
			$notLogged = $SITE->getMeta('not-logged-in-alert');
		
		
		$subNav = $sessUsername? '<li>'.$GLOBAL_sessionUrl_unOnly.'</li>' : '';
		
		$SITE->buildPageHtml(array("pageTitle"=>$sessUsername.' - Edit Profile',
						"preBodyMetas"=>$SITE->getNavBreadcrumbs($subNav.'<li><a href="/edit-profile" title="">Edit Profile</a></li>'),
						"pageBody"=>'				
						<div class="single-base blend">
							<div class="base-ctrl">
							'.$notLogged.
							($sessUsername? '													
									<div class="panel panel-limex">	
										<h1 class="panel-head page-title">Edit My Profile</h1>
										<div class="panel-body">'.
											(isset($alertUser)? $alertUser : '').  
											(isset($fileTooLarge)? $fileTooLarge : '').  
											(isset($err)? $err : '').   
											(isset($signatureLenErr) || isset($antiPhishingCodeLenErr)? '<span class="alert alert-danger">Your '.(isset($antiPhishingCodeLenErr)? 'Anti Phishing Code' : 'Signature').' is too long. Only '.MAX_SIGNATURE.' Characters are allowed (including spaces).</span>' : '').
											(isset($aboutyouLenErr)? '<span class="alert alert-danger">Your description for Field "About You" is too long. Only '.MAX_ABOUT_YOU.' Characters are allowed (including spaces).</span>' : '').
											(isset($phoneLenErr)? '<span class="alert alert-danger">Phone number must be between '.MIN_PHONE.' to '.MAX_PHONE.' digits.</span>' : '').												
											(isset($editProfile)? $editProfile : '').'
										</div>
									</div>'
								: '').'
							</div>
						</div>'
		));	
	
	}
	
	
	
	
	
	
	
	
	/*** Method for fetching authentication code form for user email edit request validation  ***/
	private function getUserEmailEditAuthForm($username, $newEmail, $authCode, $pageSelf){
		
		return '<form class="inline-form block-label" method="post" action="/'.$pageSelf.'">
				<fieldset>
					<div class="field-ctrl">
						<label>CODE:</label>
						<input data-field-tip="true" class="field" type="text" name="confirm_code" value="'.$authCode.'" placeholder="Enter code" />																	
						<input type="hidden" name="email" value="'.$newEmail.'" />
						<input type="hidden" name="uid" value="'.$username.'" />
				  	</div>
				 	 <div class="field-ctrl">
						<input type="submit" class="form-btn" name="confirm_change" value="Validate Code" />
					</div>
				</fieldset>				
			  </form>';
	
	}
	
	
	
	
	
	/*** Method for handling user email change request ***/
	public function handleUserEmailEditRequest(){
	
		global $SITE, $GLOBAL_sessionUrl_unOnly, $siteDomain, $pageSelf;
	
		$oldEmail=$newEmail=$rqd=$authForm=$code=$emailChangeForm=$fieldError=
		$authCode=$changesucc=$uid=$error="";
		
		$sessUsername = $this->SESS->getUsername();
		$sessUid = $this->SESS->getUserId();		
		
		if($sessUsername){

			//////////GET FORM-GATE RESPONSE//////////	
			$changesucc = $SITE->formGateRefreshResponse();

			//PDO QUERY//////
				
			$sql = "SELECT ID, FIRST_NAME, EMAIL FROM users WHERE USERNAME=? LIMIT 1 ";
			$valArr = array($sessUsername);
			$stmt = $this->DBM->doSecuredQuery($sql, $valArr);
			$row = $this->DBM->fetchRow($stmt);

			$oldEmail = $row["EMAIL"];
			$firstName = $row["FIRST_NAME"];

			/////IF CHANGE EMAIL IS SET///////

			if(isset($_POST["change_email"])){
				
				if($_POST[$K="new_email"]){
							
					$newEmail = $this->ENGINE->sanitize_user_input($_POST[$K]);		
					
					if(strtolower($oldEmail) != strtolower($newEmail)){		
						
						if(!$this->ENGINE->email_validate($newEmail))
							$alertUser = '<span class="alert alert-danger">Sorry the email address you entered appears to be invalid! please try again</span>';
							
						elseif(!$SITE->emailExist($newEmail)){
						
							$code = $this->ENGINE->generate_token();
							
							//LOG CHANGE OF EMAIL AUTHENTICATION CODE//
							$done = $SITE->logAuthentication($oldEmail, AUTH_CODE_KEY_CHANGE_EMAIL, $code);				
							
							if($done){
												
								$to = $newEmail;

								$subject = 'Change of Account Email Address';
										
								$message = 'Hello '.$sessUsername.'\n You made a request at <a href="'.$siteDomain.'">'.$siteDomain.'</a> to change your account email address: <a href="mailto:'.$oldEmail.'">'.$oldEmail.'</a> to <a href="mailto:'.$newEmail.'">'.$newEmail.'</a> \nYour validation code is: <div '.EMS_PH_PRE.'BLUE_BOX_1>'.$code.'</div> Please copy this code and paste it in the validation box provided or simply click on the following button to confirm and validate your request. <a '.EMS_PH_PRE.'SUCCESS_BTN href="'.$siteDomain.'/change-email?uid='.$sessUsername.'&email='.$newEmail.'&code='.$code.'">VALIDATE YOUR REQUEST</a>\nNOTE: This confirmation code or link can only be used once. \nThank you.\n\n\n\n';
											
								$footer = 'NOTE: This email was sent to you because a change of account email request was made at <a href="'.$siteDomain.'">'.$siteDomain.'</a> using this email address. If you did not make such request, please kindly ignore this message.\n\n\n Please do not reply to this email.';									 							 
										 
								$SITE->sendMail(array('to'=>$to.'::'.$firstName, 'subject'=>$subject, 'body'=>$message, 'footer'=>$footer));
								
								$authForm = '<p class="alert alert-success">
														A confirmation Code has been dispatched to your New Email Address: <a href="mailto:'.$newEmail.'">'.$newEmail.'
														</a><br/>please click on the link inside or copy the code sent and paste it in the box below
														to validate your Change of Email
													</p>'.$this->getUserEmailEditAuthForm($sessUsername, $newEmail, $authCode, $pageSelf);
													
													
							}else
								$alertUser = '<span class="alert alert-danger">Ooops! something went wrong please try again</span>';								
							
						}else						
							$alertUser = '<span class="alert alert-danger">Sorry! The email address: <a href="mailto:'.$newEmail.'">'.$newEmail.'</a> is already registered to a different account<br/>Please choose another email address.</span>';													
				
					}else						
						$alertUser = '<span class="alert alert-danger"> Sorry ! your old email address is the same as the new email address you entered.</span>';													
						
				}else{					
															
					$rqd = '<span class="alert alert-danger">Please Enter Your New Email !</span>';
					$fieldError = 'field-error';					
															
				}
							
			}


			///IF CONFIRM CODE IS SET/////

			if(isset($_POST["confirm_change"]) || isset($_GET["email"])){									
						
				if(isset($_GET[$K="code"]))
					$authCode = $_GET[$K];						
													
				elseif(isset($_POST[$K="confirm_code"]))
					$authCode = $_POST[$K];					
									
				if(isset($_GET[$K="email"]))
					$newEmail = $_GET[$K];					
									
				elseif(isset($_POST[$K]))
					$newEmail = $_POST[$K];					
														
				if(isset($_GET[$K="uid"]))
					$username = $this->ENGINE->sanitize_user_input($_GET[$K]);					
																															
				elseif(isset($_POST[$K]))						
					$username = $_POST[$K];							
																															
				else						
					$username = $sessUsername;						
													
				if($username && $authCode && $newEmail){
				
					if($authCode){				
								
						//GET CHANGE OF EMAIL AUTHENTICATION CODE//						
						$dbAuthCode = $SITE->getAuthentication($username, AUTH_CODE_KEY_CHANGE_EMAIL);
				
						if($dbAuthCode != $authCode){
						
							$rqd = '<span class="alert alert-danger" >Incorrect Validation Code !</span>';
												
							$authForm = $this->getUserEmailEditAuthForm($username, $newEmail, $authCode, $pageSelf);					
									
						}else{

							$cols = "EMAIL=?";									
							//EXPIRE CHANGE OF EMAIL AUTHENTICATION CODE//
							$done = $SITE->expireAuthentication($username, AUTH_CODE_KEY_CHANGE_EMAIL);				
								
							if($this->updateUser($username, $cols, array($newEmail)) && $done){
																
								$changesucc = '<p class="alert alert-success">'.$GLOBAL_sessionUrl_unOnly.' Your Email has been Changed Successfully</p>';
								
								$to = $newEmail;

								$subject = 'Your Account Email Address Has Changed';
										
								$message = 'Hello '.$username.'\n You have successfully changed your account Email address at <a href="'.$siteDomain.'">'.$siteDomain.'</a> from <a href="mailto:'.$oldEmail.'">'.$oldEmail.'</a> to <a href="mailto:'.$newEmail.'">'.$newEmail.'</a>\n\n\n\n';
																				
								$footer = 'NOTE: This email was sent to you because a change of account E-mail address was made at <a href="'.$siteDomain.'">'.$siteDomain.'</a> using this email address. If you did not make such request, please kindly ignore this message.\n\n\n Please do not reply to this email.';										 								 						
										 
								$SITE->sendMail(array('to'=>$to.'::'.$firstName, 'subject'=>$subject, 'body'=>$message, 'footer'=>$footer));
								
								////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////
								$SITE->formGateRefresh($changesucc);
								
							}else
								$alertUser = '<span class="alert alert-danger">Ooops! something went wrong please try again</span>';								
					
						}
							
					}else{					
									
						$rqd = '<span class="alert alert-danger" >Please Enter the validation code sent to your new email !</span>';						
						$authForm = $this->getUserEmailEditAuthForm($username, $newEmail, $authCode, $pageSelf);			
						
					}
				
				}else{			
												
					$rqd = '<span class="alert alert-danger" >An unexpected error has occurred !<br/> We are sorry about this</span>';
					$error = true;			
													
				}
				
			}


			/////FULL EMAIL CHANGE FORM///////

			$emailChangeForm = '
			<form data-field-validation="true" class="inline-form block-label" name="change_email" method="post" action="/change-email">
				<fieldset>
					<div class="field-ctrl">
						<label for="oem">Old Email:</label>
						<input data-field-tipX="true" id="oem" class="field" type="email" readonly="readonly" name="old_email" value="' .$oldEmail .'" />
					</div>
					<div class="field-ctrl">
						<label for="nem">New Email:</label>
						<input id="nem" data-field-tipX="true" data-validation-name="email" class="field '.$fieldError.'" type="email" name="new_email" value="'. $newEmail .'" placeholder="Enter new email" /><br/>
					</div>
					<div class="field-ctrl btn-ctrl col-xs-w-7">
						<input type="submit" class="form-btn btn-success" name="change_email" value="Change Email" />
					</div>
				</fieldset>				
			</form>';

					
					
		}else
			$notLogged = $SITE->getMeta('not-logged-in-alert');
		
		
		$subNav = "";
		if($sessUsername) $subNav = '<li>'.$GLOBAL_sessionUrl_unOnly.'</li>';

		$SITE->buildPageHtml(array("pageTitle"=>'Change Email',
					"preBodyMetas"=>$SITE->getNavBreadcrumbs($subNav.'<li><a href="/edit-profile">Edit Profile</a></li><li><a href="/change-email">Change Email</a></li>'),
					"pageBody"=>'				
						<div class="single-base">
							<div class="base-ctrl base-container">'.
								(isset($notLogged)? $notLogged : '').

								($sessUsername?											
									'<h1 class="">CHANGE E-MAIL</h1>'.				
									(isset($rqd)?  $rqd : '').(isset($changesucc)? $changesucc : '').										
									(isset($alertUser)? $alertUser : '').
									((!$authForm && !$changesucc && !$error)? $emailChangeForm : '').
									$authForm
								: '').'
							</div>
						</div>'

		));
	
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/*** Method for handling user password change request ***/
	public function handleUserPasswordEditRequest(){
		
		global $SITE, $GLOBAL_sessionUrl_unOnly, $siteDomain;
		
		$newPwdMismatch=$incorrectOldPwd=$blankNewPwd1=
		$blankNewPwd2=$blankOldPwd=$spaceInPwd=$nullFields=$newPassShort=$pwdPatternErr=
		$oldPwdFieldErr=$newPwd1FieldErr=$newPwd2FieldErr="";			

		$asterix = '<span class="asterix">*</span>';
		$fErr = 'field-error';		
		
		$sessUsername = $this->SESS->getUsername();
		$sessUid = $this->SESS->getUserId();	
						
		//////////GET FORM-GATE RESPONSE//////////	
		$alert = $SITE->formGateRefreshResponse();
						
		$oldPassCpi = 'oldpass_cpi';

		if((isset($_GET[$cpi="cpi"]) && isset($_GET[$email="email"])) || isset($_POST[$oldPassCpi])){
			
			if(isset($_GET[$cpi]) && isset($_GET[$email])){
					
				$holdOldPwd = $this->ENGINE->sanitize_user_input($_GET[$cpi]);
				$token = $this->ENGINE->sanitize_user_input($_GET[$email]);
				$oldpass_cpi = true;			
						
			}
			
			
			if(isset($_POST[$oldPassCpi])){			
						
				$holdOldPwd = $this->ENGINE->sanitize_user_input($_POST[$oldPassCpi]);
				$token = $this->ENGINE->sanitize_user_input($_POST["token"]);
				$oldpass_cpi = true;
				
			}
			
			///////////PDO QUERY////

			$sql =  "SELECT USERNAME FROM users WHERE EMAIL=? AND TMP_PASS=? LIMIT 1";
			$valArr = array($token, $holdOldPwd);			
						
			if($username = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn())
				'';
			else
				$err_CPI = true;			
						
		}else
			$username = $sessUsername;


		if(isset($_POST['changePwd'])){
					
			$newPwd1 = $_POST['newPwd1'];

			$newPwd2 = $_POST['newPwd2'];

			if(isset($_POST["oldPwd"]))
				$oldPwd = $this->ENGINE->sanitize_user_input($_POST['oldPwd']);			
						
			if(isset($_POST["oldpass_cpi"]))
				$oldPwd = $this->ENGINE->sanitize_user_input($_POST['oldpass_cpi']);
	 
			$spaceInPwd = $this->ENGINE->has_white_space(array($newPwd1, $newPwd2));
			 
			 
			if($username){
								 
				if($newPwd1 && $newPwd2 && $oldPwd ){
					
					if(preg_match(PWD_PATTERN, $newPwd1)){			 
					
						if(!$spaceInPwd){
																							
							//PDO QUERY////
								
							$sql =  "SELECT PASSWORD, TMP_PASS, EMAIL, FIRST_NAME FROM users WHERE USERNAME=? LIMIT 1";
							$valArr = array($username);
							$stmt = $this->DBM->doSecuredQuery($sql, $valArr);
							$row = $this->DBM->fetchRow($stmt);
							$dbOldPwd = $row['PASSWORD'];
							$dbTmpPwd = $row['TMP_PASS'];
							$userEmail = $row['EMAIL'];
							$firstName = $row['FIRST_NAME'];

							$newpass1mailed = $newPwd1;				

							if($newPwd1 == $newPwd2){

								if(mb_strlen($newPwd1) >= MIN_PWD && mb_strlen($newPwd1) <= MAX_PWD){
							 
									if($this->passwordVerify($oldPwd, $dbOldPwd) || $oldPwd == $dbTmpPwd){								
										
										////////////ENCRYPT PASSWORD
										$newPwd1 = $this->passwordEncrypt($newPwd1);
										
										if($this->updateUser($username, 'PASSWORD = ?, TMP_PASS = "" ', array($newPwd1))){
										 
											$to = $userEmail;
											$subject = 'Password Reset';
											$message = 'Hello '.$username.'\n your password has been successfully changed.\n\n You can now proceed to <a href="'.$siteDomain.'/login">Login</a> \n\nPlease do keep your details safe\nThank you\n\n\n\n\n';
													
											$footer = 'NOTE: This email was sent to you because your account password registered with this Email at <a href="'.$siteDomain.'">'.$siteDomain.'</a> was changed. If you
														did not initiate this request, kindly take actions now to reinforce your account security by changing your account password.\n\n\n Please do not reply to this email.';
											
											$SITE->sendMail(array('to'=>$to.'::'.$firstName, 'subject'=>$subject, 'body'=>$message, 'footer'=>$footer));
											
											///CLOAK//////
											$userEmail = $this->ENGINE->cloak($userEmail);
											$alert = '<div class="alert alert-success">'.$this->sanitizeUserSlug($username, array('anchor'=>true, 'youRef'=>false)).'<span class=""> your password has been changed successfully.
											'.(!$sessUsername? '<a class="links" href="/login">click here to login</a>' : '').'<br/>Thank you</span></div>';
											
											////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////
											$SITE->formGateRefresh($alert);
										 
										}else
											$alert = '<span class="alert alert-danger">Ooops! something went wrong please try again</span>';
										

									}																	
									////IF OLD PASSWORD ENTERED IS NOT EQUAL TO OLD PASSWORD IN DB///////////
									else{

										$oldPwdFieldErr = $fErr;
										$incorrectOldPwd = $asterix;							

										///////FOCUS ON OLD PASSWORD FIELD/////
									 
										echo '<script> location.assign("#oldPwd")</script>';

									}
								 
								}								 
								//IF NEW PASSWORD LENGTH IS TOO SHORT////
								else{
								  
									$newPwd1FieldErr = $newPwd2FieldErr = $fErr;												
									$newPassShort = $asterix;

									/////FOCUS ON FIRST NEW PASSWORD FIELD/////
									 
									echo '<script> location.assign("#newPwd1")</script>';
								 
								}

							}								 
							/////IF NEW PASSWORD FIELDS DID NOT MATCH///////

							else{
							  
								$newPwd1FieldErr = $newPwd2FieldErr =  $fErr;										
								$newPwdMismatch = $asterix;

								/////FOCUS ON FIRST NEW PASSWORD FIELD/////

								echo '<script> location.assign("#newPwd1")</script>';

							}
						  
						}
						///IF THERE ARE SPACES IN THE NEW PASSWORD//////
						else{

							$newPwd1FieldErr = $newPwd2FieldErr =  $fErr;
							$spaceInPwd = $asterix;				
							
							////FOCUS ON FIRST NEW PASSWORD FIELD//////								 
							echo '<script> location.assign("#newPwd1")</script>';
													
						}
						
					}else{	 

						$newPwd1FieldErr = $newPwd2FieldErr =  $fErr;
						$pwdPatternErr = $asterix;				
						
						////FOCUS ON FIRST NEW PASSWORD FIELD//////								 
						echo '<script> location.assign("#newPwd1")</script>';
							
					}						
				}
				/////IF ANY OF THE FIELDS ARE BLANK///////
				else{
					
					if(!$oldPwd && !$newPwd1 && !$newPwd2){
						
						$newPwd1FieldErr = $oldPwdFieldErr = $newPwd2FieldErr = $fErr;
						$nullFields = $asterix;
						
						echo '<script> location.assign("#oldPwd")</script>';
						
					}elseif((!$oldPwd || !$newPwd1 || !$newPwd2) && ($oldPwd || $newPwd1 || $newPwd2)){
						
						//handle when null
						
						if(!$oldPwd){
							
							$oldPwdFieldErr = $fErr;
							$blankOldPwd = $asterix;
							
							//FOCUS ON OLD PASSWORD FIELD////////
							
							echo '<script> location.assign("#oldPwd")</script>';
							
						}
												
						if(!$newPwd1){
							
							$newPwd1FieldErr = $fErr;
							$blankNewPwd1 = $asterix;
						
							//FOCUS ON FIRST NEW PASSWORD FIELD/////
						
							echo '<script> location.assign("#newPwd1")</script>';	
						
						}
						
						if(!$newPwd2){
							
							$newPwd2FieldErr = $fErr;
							$blankNewPwd2 = $asterix;
						
							///FOCUS ON SECOND NEW PASSWORD FIELD///////
						
							if($newPwd1  && $oldPwd )					
								echo '<script> location.assign("#newPwd2")</script>';
						
						}
					
					}
					
				}
				
				//////HANDLES FOR WHEN ANY FIELD IS NOT NULL///
				
				if($oldPwd)
					$holdOldPwd = $oldPwd;					
				if($newPwd1)
					$holdNewPwd1 = $newPwd1;			
				if($newPwd2)
					$holdNewPwd2 = $newPwd2;			
				 
			}else
				$notLogged = $SITE->getMeta('not-logged-in-alert');

			
		}

		$subNav = "";
		$pwd2TextCls = 'edit-pwd-plain';		 
		$pwdFieldMeta = ' type="password" autocomplete="off" maxlength="'.MAX_PWD.'" ';

		if($sessUsername) 
			$subNav = '<li>'.$GLOBAL_sessionUrl_unOnly.'</li>';
		
		 $SITE->buildPageHtml(array("pageTitle"=>'Change Password',
					"preBodyMetas"=>$SITE->getNavBreadcrumbs($subNav.'<li><a href="/edit-profile">Edit Profile</a></li><li><a href="/changepassword" title="">Change Password</a></li>'),
					"pageBody"=>'				
						<div class="single-base">		
							<div class="base-ctrl base-container">
								<h1 class="">CHANGE PASSWORD</h1>
								<div class="">'.
									(isset($alert)? $alert : '').
									(isset($notLogged)? $notLogged : '').'
									<div class="red">'.
										(($incorrectOldPwd && !isset($oldpass_cpi))? 'Sorry your old password did not match our record!<br/>' 
										:($incorrectOldPwd? 'Sorry this link has either been altered or expired !<br/>' : '')). 
										($newPwdMismatch? 'New password fields did not match !' : ''). 
										($newPassShort? 'The new password you entered was too short; it must be within a minimum of '.MIN_PWD.'  and maximum of '.MAX_PWD.' characters with no spaces! !<br/>' : '').
										(($blankNewPwd1  || $blankNewPwd2)? 'New password fields cannot be blank !<br/>' : '').
										($blankOldPwd?  ' Old password field cannot be blank !<br/>' : '').  
										($nullFields? 'Fields marked * cannot be blank !' : ''). 
										($spaceInPwd? 'Spaces are not allowed in the new password !<br/>' : '').
										($pwdPatternErr? $SITE->getMeta('valid-password-tip') : '').
										(isset($err_CPI)? '<span class="alert alert-danger">Sorry this link has either been already used, altered or expired!</span>' : '').'
									 </div>'.
									(!isset($err_CPI)? '
										<form data-field-validation="true" class="inline-form block-label" name="changePwd" method="POST" action="/changepassword">
											<fieldset>
												<div class="field-ctrl">'.
													(!isset($oldpass_cpi)? '<label for="oldPwd">Enter old password'.(isset($nullFields)? $nullFields : '').(isset($incorrectOldPwd)? $incorrectOldPwd : ''). 
														(isset($blankOldPwd)? $blankOldPwd : '').':</label>
														<input '.$pwdFieldMeta.' id="oldPwd" class="field '.$pwd2TextCls.' '.$oldPwdFieldErr.'" value="'.(isset($holdOldPwd)? $holdOldPwd : '').'" name="oldPwd" placeholder="Enter old password" />'
														: '<input id="oldPwd" type="hidden" value="'.(isset($holdOldPwd)? $holdOldPwd : '').'" type="password" name="oldpass_cpi" />
														<input type="hidden" class="field" value="'.(isset($token)? $token : '').'" type="hidden" name="token" />').'															
												</div>
												<div class="field-ctrl">
													<label for="newPwd1">Enter new password'.(isset($nullFields)? $nullFields : '').(isset($spaceInPwd)? $spaceInPwd : '').    
													(isset($blankNewPwd1)? $blankNewPwd1 : '').(isset($newPwdMismatch)? $newPwdMismatch : ''). 
													(isset($newPassShort)? $newPassShort : '').':</label>							
													<input '.$pwdFieldMeta.' id="newPwd1" data-validation-name="password-twin" data-twin-id="newPwd2" class="'.$pwd2TextCls.' field '.$newPwd1FieldErr.'"  value="'.(isset($holdNewPwd1)? $holdNewPwd1 : '').'" name="newPwd1" placeholder="Enter new password" />	
												</div>
												<div class="field-ctrl">
													<label for="newPwd2">Confirm new password'.(isset($nullFields)? $nullFields : '').(isset($spaceInPwd)? $spaceInPwd : '').   
													(isset($blankNewPwd2)? $blankNewPwd2 : '').(isset($newPwdMismatch)? $newPwdMismatch : ''). 
													(isset($newPassShort)? $newPassShort : '').':</label>						
													<input '.$pwdFieldMeta.' id="newPwd2" data-validation-name="password-twin" data-twin-id="newPwd1" class="'.$pwd2TextCls.' field '.$newPwd2FieldErr.'"  value="'.(isset($holdNewPwd2)? $holdNewPwd2 : '').'" name="newPwd2" placeholder="Re-enter new password" />
												</div>
												<br/>
												<div class="field-ctrl">
													'.$SITE->getHtmlComponent('iconic-checkbox', array('label'=>'show all password fields', 'title'=>'For security reasons, please make sure there are no prying eyes before you check this box', 'fieldData'=>'data-toggle-password-plain-target="'.$pwd2TextCls.'"', 'wrapClass'=>'text-danger', 'fieldName'=>$K='showpass', 'on'=>isset($_POST[$K]))).'
												</div><br/>
												<div class="field-ctrl">
													<button name="changePwd"  class="form-btn">change password</button>
												</div>
											</fieldset>																
										</form>'
									: '').'
								</div>
							</div>
						</div>'
						
		));
		
	}
	
	
	
	
	
	
	
	
	
	
	
	/*** Method for handling user password forgot request ***/
	public function handleUserPasswordForgotRequest(){
		
		global $SITE, $GLOBAL_rdr, $siteDomain;
			
		$incorrectPwd=$nullFields=$temppassword=$dbemail=$dbuname="";
		
		$sessUsername = $this->SESS->getUsername();
		$sessUid = $this->SESS->getUserId();	
		
		//////////GET FORM-GATE RESPONSE//////////	
		list($alertUser, $resetDone) = $SITE->formGateRefreshResponse(true);						

		if(isset($_POST['submit'])){

			$userDetails = $this->ENGINE->sanitize_user_input($_POST['email/username']);

			if($userDetails){
						
				///////PDO QUERY///////
						
				$sql = "SELECT ID, USERNAME, EMAIL, FIRST_NAME FROM users WHERE EMAIL=? LIMIT 1";
				$valArr = array($userDetails);
				$stmt = $this->DBM->doSecuredQuery($sql, $valArr);
				$row = $this->DBM->fetchRow($stmt);

				if(!empty($row)){
					
					$dbuname = $row['USERNAME'];
					$dbemail = $row['EMAIL'];
					$firstName = $row['FIRST_NAME'];
					//$randompwd = $row['PASSWORD'];
					$randompwd = $this->ENGINE->generate_token();
					
					$done  = $this->updateUser($dbuname, 'TMP_PASS=?, TMP_PASS_TIME=NOW()', array($randompwd));
					
					if($randompwd && $done){
							 
						///EMAIL USER

						$to = $dbemail;
						$subject = 'Temporary Password Reset';
						$message = 'Hello '.$dbuname.'\n Owing to the request for your login password and having completed the forgot password form at <a href="'.$siteDomain.'">'.$siteDomain.'</a>, a temporary new password has been generated for you.\n Please click the button below to change your password. <a '.EMS_PH_PRE.'WARN_BTN href="'.$siteDomain.'/changepassword?cpi='.$randompwd.'&email='.$dbemail.'">CHANGE YOUR PASSWORD NOW</a> \n\n NOTE: This new temporary password generated for you is only valid for '.TMP_PWD_LIFE_MINS.' minutes, hence we advise you change your password as soon as you click on the button above.\n\nThank you\n\n\n\n';
									
						$footer = 'NOTE: This email was sent to you because you completed a password reset form at <a href="'.$siteDomain.'">'.$siteDomain.'</a>. If you
									did not initiate such a request, please kindly ignore this message.\n\n\n please do not reply to this email.';
						 
						$SITE->sendMail(array('to'=>$to.'::'.$firstName, 'subject'=>$subject, 'body'=>$message, 'footer'=>$footer));
						
						//CLOAK EMAIL//
						$cloackedEmail = $this->ENGINE->cloak($dbemail);
						$alertUser = '<span class="alert alert-success"> 
						A reset link has been dispatched to your email address: <a href="mailto:'.$cloackedEmail.'" class="links">'.$cloackedEmail.'</a>,
						It will arrive shortly<br/>Please click on it to reset your password
						<br/> Thank you</span>';

						////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////
						$SITE->formGateRefresh(array($alertUser, $resetDone = true));

					}else
						$alertUser = '<span class="alert alert-danger">Ooops! something went wrong please try again</span>';
						
				}else{
					
					$alertUser = '<span class="alert alert-danger">sorry we could not find your record <br>please verify the email you entered and try again</span>';
					/*	
					$getDetails = explode("@",$userDetails) ;
					
					if (count($getDetails) == 2)
					 $alertUser = '<span class="alert alert-danger">sorry no such user with the email: <span class="blue">'.$getDetails[0].'@'.$getDetails[1].'</span> was found<br>please try again</span>';

					else
						 $alertUser = '<span class="alert alert-danger">sorry no such user with the username:   <span class="blue">'.$getDetails[0].'</span> was found<br>please try again</span>';
				 */
					
				}


			}else
				$alertUser = '<span class="alert alert-danger">please fill out the E-mail field </span>';		
				
			
		}


		$SITE->buildPageHtml(array("pageTitle"=>'Password Reset',
					"preBodyMetas"=>$SITE->getNavBreadcrumbs('<li><a href="/forgotpassword" title="">Password Reset</a></li>'),
					"pageBody"=>'							
						<div class="single-base">
							<div class="base-ctrl base-container">
								<h1>PASSWORD RESET</h1>'.
								($sessUsername? '<p class="prime">You must be <a href="/logout?_rdr='.$GLOBAL_rdr.'" class="links">logged out</a> to gain access to this page.</p>' :
								'<div class="form-ui form-ui-basic">
									<form class="inline-form" method="post" action="/forgotpassword">
										<div>'.
											(!$resetDone? '<p class="prime">Please enter the email address you used in registering your account with us.</p>' : '').
											(isset($alertUser)? $alertUser : '')
											.'												
										</div>
										<div class="field-ctrl col-lg-w-4">
											<label>E-mail:</label>
											<input data-field-tip="true" class="field" value="'.(isset($pvalue)? $pvalue : '').'" type="email"  name="email/username" /><span class="red">'.(isset($incorrectPwd)? $incorrectPwd : '').'</span>
										</div>
										<div class="field-ctrl btn-ctrl">
											<button name="submit" class="form-btn">submit</button>
										</div>
									</form>
								</div>'
								).'
							</div>
						</div>'							
		));		
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*** Method for invalidating user account expired temporary password ***/
	public function expireUserTempPassword(){
	
		////PDO QUERY/////////
		$sql = "UPDATE users SET TMP_PASS='' WHERE ((TMP_PASS_TIME + INTERVAL ".TMP_PWD_LIFE_MINS." MINUTE) <= NOW() AND TMP_PASS !='')";
		$this->DBM->query($sql);
		
	
	}
	
	
	
	
	
	
	
	/*** Method for scheduling user account termination ***/
	public function scheduleTermination($username){

		$done = $alertUser = '';

		if(strtolower($username) != strtolower($this->SESS->getUsername())){

			list($ranksHigher, $ranksEqual) = $this->sessionRanksHigher($username);
					
			if($ranksHigher)
				$done = $this->updateUser($username, 'SCHEDULED_TIME_FOR_DELETE=(NOW() + INTERVAL '.ACCOUNT_TERMINATION_WAIT_DAYS.' DAY)');
			else
				$alertUser = 'sorry you do not have enough privilege to terminate this user ('.$username.')';
		}else
			$alertUser = 'Sorry you cannot terminate yourself';

		$alertUser? ($alertUser = '<span class="alert alert-danger">'.$alertUser.'</span>') : '';

		return array($done, $alertUser);

	}
	
	
	
	/*** Method for handling user account cancel request ***/
	public function handleUserAccountCancelRequest(){
		
		global $SITE, $siteDomain, $pageSelf;
		
		$authCode="";
	
		$sessUsername = $this->SESS->getUsername();
		$sessUid = $this->SESS->getUserId();	

		if($username = $sessUsername){
				
			//////////GET FORM-GATE RESPONSE//////////	
			$alertUser = $SITE->formGateRefreshResponse();

				
			///////////ON SEND VALIDATION CODE//////
			if(isset($_POST["send_deactivation_code"])){
				
				$U = $this->loadUser($username);
				$email = $U->getEmail();					
						
				if($email){	
					
					$code = $this->ENGINE->generate_token();
					
					//LOG DEACTIVATION AUTHENTICATION CODE//											
					$done = $SITE->relogAuthentication($email, AUTH_CODE_KEY_CANCEL_USER, $code);
					
					if($done){
												
						$to = $email;

						$subject = 'Account deactivation code';
								
						$message = 'Hello '.$username.'\n You made a request at <a href="'.$siteDomain.'">'.$siteDomain.'</a> to deactivate your account.\nTo confirm this request and continue with the deactivation process, Please copy this code: <h4 '.EMS_PH_PRE.'GREEN>'.$code.'</h4> and paste it in the validation box provided to you or simply click on the following link to confirm and validate your request.\n <a '.EMS_PH_PRE.'DANGER_BTN href="'.$siteDomain.'/cancelaccount?code='.$code.'">VALIDATE YOUR REQUEST</a>\nThank you.\n\n\n\n';						
						
						$footer = 'NOTE: This email was sent to you because an Account Deactivation request was made at <a href="'.$siteDomain.'">'.$siteDomain.'</a> using this email address. If you did not make such request, please kindly ignore this message.\n\n\n Please do not reply to this email.';
								
						$SITE->sendMail(array('to'=>$to.'::'.$U->getFirstName(), 'subject'=>$subject, 'body'=>$message, 'footer'=>$footer));
						
						$alertUser = '<span class="alert alert-success">A confirmation code has been dispatched to your email. It will arrive shortly.<br/>Thank you.</span>';
									
						////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////
						$SITE->formGateRefresh($alertUser);
																
					}
						
				}else
					$alertUser = '<span class="alert alert-danger">Sorry user not found</span>';
							
			}
			
			//////////ON VALIDATE REQUEST/////
			
			$uidByAdmin = 'uid_by_admin';
			$code = 'code';
			
			if(isset($_POST[$code]) || isset($_GET[$code]) || (isset($_POST[$uidByAdmin]) && $_POST[$uidByAdmin] )){
				
				$sentByAdmin = false;
				
				if(isset($_POST[$code]))
					$authCode = $_POST[$code];

				elseif(isset($_GET[$code]))
					$authCode = $_GET[$code];
					
				elseif(isset($_POST[$uidByAdmin])){
					
					$username = $this->ENGINE->sanitize_user_input($_POST[$uidByAdmin]);
					$sentByAdmin = true;
					
				}						
					
				if($authCode || $sentByAdmin){
								
					$U = $this->loadUser($username);
					$email = $U->getEmail();					
															
					if($email){
																
						//GET DEACTIVATION AUTHENTICATION CODE//
						$dbCode = $SITE->getAuthentication($email, AUTH_CODE_KEY_CANCEL_USER);				

						if(($dbCode == $authCode) || $sentByAdmin){
						
							$this->scheduleTermination($username);														
							//EXPIRE DEACTIVATION AUTHENTICATION CODE//
							$SITE->expireAuthentication($email, AUTH_CODE_KEY_CANCEL_USER);
							
							$alertUser = '<span class="alert alert-danger">'.($sentByAdmin? $username.'`s' : 'Hello '.$sessUsername.'<br/>Your ').'
							account has been scheduled for termination, It will be processed after '.ACCOUNT_TERMINATION_WAIT_HOURS.'.'.($sentByAdmin? '' : '
							<br/> Should incase You decide not to terminate your account anymore within this time period, then simply login into your account 
							and navigate to <a href="/edit-profile#cus_tab" class="links">edit profile</a> page, under "personal preferences" section, uncheck the "scheduled termination" box and click on "update profile" button').'</span>';
							
							if($sentByAdmin){
						
								echo $alertUser;
								exit();
						
							}
						
						}else
							$alertUser = '<span class="alert alert-danger"><b>Validation failed !!!</b><br/>Please verify the code you entered and try again</span>';
			
					}else
						$alertUser = '<span class="alert alert-danger">Sorry user not found</span>';

				}else
					$rqd = true;
			
			}
			
			$authForm = '<form class="horizontal-form" method="post" action="/'.$pageSelf.'">
							<div class="field-ctrl">
								<label class="red">VALIDATE CODE:</label>
								<input data-field-tip="true" placeholder="Enter the confirmation code you received here" class="field" type="text" name="code" value="'.$authCode.'" />
							</div>
							<div class="field-ctrl btn-ctrl">
								<input type="submit" class="form-btn btn-danger" data-toggle="smartToggler" data-id-targets="fwarn" name="validate" value="Terminate My Account" />									
							</div>
							<div id="fwarn" class="modal-drop red bold hide has-close-btn">
								<p>FINAL WARNING!<br/>ARE YOU SURE YOU WANT TO SCHEDULE YOUR ACCOUNT FOR TERMINATION?<br/> 
									NOTE: YOU WILL LOSE ALL RECORDS ASSOCIATED WITH THE ACCOUNT INCLUDING AD CAMPAIGN RECORDS IF ANY
								</p>
								<input type="submit" class="btn btn-danger" name="validate" value="YES" />												
								<input type="button" class="btn close-toggle" value="NO" />
							</div>
					 </form>';
			
			
			$authCodeSendForm = '<form class="inline-form" method="post" action="/cancelaccount">
									<div class="field-ctrl">
										<label>Please click on the button below to send a confirmation CODE to your email:</label>
									</div>
									<div class="field-ctrl btn-ctrl">
										<input type="submit" class="form-btn btn-warning" name="send_deactivation_code" value="SEND CODE" />
									</div>
							 </form><hr/>';

		}else
			$notLogged = $SITE->getMeta('not-logged-in-alert');
		
		
		$subNav = "";

		if(!isset($notLogged))
			$subNav = '<li>'.$this->sanitizeUserSlug($username, array('anchor'=>true, 'youRef'=>false, 'urlText'=>'my profile')).'</li>';

		$subNav .= '<li><a href="/cancelaccount" title="">Deactivate Account</a></li>';

		$SITE->buildPageHtml(array("pageTitle"=>'Deactivate Account',
					"preBodyMetas"=>$SITE->getNavBreadcrumbs($subNav),
					"pageBody"=>'				
						<div class="single-base blend">
							<div class="base-ctrl">'.
								(isset($notLogged)? $notLogged : '').
								
								($sessUsername? '													
									<div class="panel panel-limex">
										<h1 class="panel-head page-title">ACCOUNT DEACTIVATION</h1>
										<div class="panel-body">
											<span class="alert alert-warning">Please be informed that all account termination request will be processed after '.ACCOUNT_TERMINATION_WAIT_HOURS.' upon submitting the validation code that will be sent to your registered email address,
											You will also lose all data relating to your personal account including campaign data(if any).</span><hr/>													
											'.(isset($authCodeSendForm)? $authCodeSendForm : '').  
											(isset($alertUser)? $alertUser : '').
											(isset($rqd)? '<span class="alert alert-danger">Please enter the validation code that was sent to your email in order to proceed</span>' : '').
											(isset($authForm)? $authForm : '').'   
										</div>
									</div>'
								: '').'
							</div>
						</div>'
						
		));


	}


	
	
	
	
	
	
	
	/*** Method for executing scheduled users account termination request after a specific time period (cron job) ***/
	public function terminateScheduledAccounts(){
		
		//////////GET DATABASE CONNECTION//////
		global $FORUM, $SITE, $GLOBAL_mediaRootBannerXCL, $GLOBAL_mediaRootAvtXCL, $GLOBAL_mediaRootPostXCL;
		
		$adsCampaign = new AdsCampaign();
		$mediaRootBannerXCL = $GLOBAL_mediaRootBannerXCL;
		$mediaRootAvtXCL = $GLOBAL_mediaRootAvtXCL;
		$mediaRootPostXCL =  $GLOBAL_mediaRootPostXCL;
		
		$cnd = " WHERE (SCHEDULED_TIME_FOR_DELETE != 0 AND SCHEDULED_TIME_FOR_DELETE <= NOW()) ";
		
		///////////PDO QUERY//////
		
		$n = $this->DBM->getMaxRowPerSelect();
		
		for($i=0; ; $i += $n){
			
			$sql = "SELECT USERNAME FROM users ".$cnd." LIMIT ".$i.",".$n;
			$valArr = array();
			$stmtx = $this->DBM->doSecuredQuery($sql, $valArr, true);
			
			/////IMPORTANT INFINITE LOOP CONTROL ////
			if(!$this->DBM->getSelectCount())
				break;
							
			while($rowx = $this->DBM->fetchRow($stmtx)){
				
				$user = $this->loadUser($rowx["USERNAME"]);
				
				$oldAvatar = $user->getAvatar();
				$userEmail = $user->getEmail();
				
				$userId = $user->getUserId();
				
				try{
					
					
					// Run scheduled account termination transaction
					$this->DBM->beginTransaction();
				
					/* 
					
						DELETE THE USER PRIVATE MESSAGES, PRIVATE MESSAGES BLACKLIST, FOLLOWED SECTIONS, AVATAR LIKES, 
						FOLLOWED MEMBERS, DND_EMAIL_LIST, TOPICS, UPVOTES, DOWNVTES, SHARES
						
					*/
					
					///////////PDO QUERY////
					
					
					$udsSql = '';
					$tmpArr = array('upvotes', 'downvotes', 'shares');
				
					foreach($tmpArr as $table){
						
						$udsSql .= "DELETE FROM ".$table." WHERE POST_ID IN(SELECT p.ID FROM posts p WHERE ".$table.".POST_ID=p.ID AND p.POST_AUTHOR_ID=?);";
				
					}
					
					
					$sql =  "
								DELETE FROM private_messages WHERE USER_ID = ?; 
								DELETE FROM pm_blacklists WHERE USER_ID = ?; 
								DELETE FROM section_follows WHERE USER_ID = ?; 
								DELETE FROM avatar_likes WHERE USER_ID = ? OR LIKER_ID = ?;
								DELETE FROM members_follows WHERE USER_ID = ? OR FOLLOWER_ID = ?; 
								DELETE FROM dnd_mail_lists WHERE EMAIL = ? LIMIT 1;
								/* DELETE FROM topics WHERE TOPIC_AUTHOR_ID=?;
									".$udsSql."
								*/
							";
					
					$valArr = array(
									$userId, $userId, $userId, $userId, $userId, $userId, $userId, $userEmail
									/*, $userId, $userId, $userId, $userId */
							);
							
					$stmt = $this->DBM->doSecuredQuery($sql, $valArr);

					

									
					/*		

					/////DELETE THE USER's POST UPLOADS FROM SERVER////
					
					for($i=0; ; $i += $n){
				
						///////////PDO QUERY///////
						$sql = $SITE->composeQuery(array('type' => 'for_post', 'start' => $i, 'stop' => $n, 'postColsOnly' => true, 'uniqueColumns' => '', 'filterCnd' => 'POST_AUTHOR_ID=?', 'orderBy' => ''));
						$valArr = array($userId);
						$stmt = $this->DBM->doSecuredQuery($sql, $valArr, true);
						
						/////IMPORTANT INFINITE LOOP CONTROL ////
						if(!$this->DBM->getSelectCount())
							break;
							
						while($row = $this->DBM->fetchRow($stmt)){
											
							$pid = $row["PID"];
							$postedFiles = $row["UPLOADS"];			
							////DELETE THE RELATED FILES FROM SERVER///////																																		
							$FORUM->postedFilesHandler(array('pid'=>$pid, 'files'=>$postedFiles, 'del'=>true));
							
						}
						
					}
					
						
					//DELETE ALL THE USER POSTS/////////

					///////////PDO QUERY/////////

					$sql = "DELETE FROM posts WHERE POST_AUTHOR_ID = ?";
					$valArr = array($userId);
					$this->DBM->doSecuredQuery($sql, $valArr);

					*/

					for($i=0; ; $i += $n){
			
						//REMOVE THE USER's BANNER ADS FROM ALL SECTIONS AND ALL HIS BANNER CAMPAIGN UPLOADS TO SERVER ///
						///////////PDO QUERY//////

						$sql = "SELECT ID, AD_IMAGE FROM banner_campaigns WHERE USER_ID = ? LIMIT ".$i.",".$n;
						$valArr = array($userId);
						$stmt = $this->DBM->doSecuredQuery($sql, $valArr, true);	
						
						/////IMPORTANT INFINITE LOOP CONTROL ////
						if(!$this->DBM->getSelectCount())
							break;
									
						while($row = $this->DBM->fetchRow($stmt)){
								
							/////////DELETE THE USER CAMPAIGN BANNERS FROM SERVER////////

							$adBanner = $row["AD_IMAGE"];
							
							$path2del = $mediaRootBannerXCL.$adBanner;
							
							if(realpath($path2del) && $adBanner)
								unlink($path2del);
								
						}
									
					}				
					
					//REMOVE THE USER's BANNER AND TEXT ADS FROM ALL SECTIONS///
					$adsCampaign->removeFromActiveAdSlots(array("uid"=>$userId, "del"=>true, "ignoreCampaignAdType"=>true));
					$adsCampaign->adPlacementHandler(array('uid'=>$userId, 'action'=>'del-by-uid', 'ignoreCampaignType'=>true));
					
					////DELETE THE USER DP///		
					$path2del = $mediaRootAvtXCL.$oldAvatar;

					if(realpath($path2del) && $oldAvatar)
						unlink($path2del);
					
					$FORUM->followedTopicsHandler(array('uid'=>$userId, 'action'=>'unfollow'));
					
					/* 
						DELETE THE USER's BANNER AND TEXT CAMPAIGNS, META_DATAS, BADGES, AD REPORT,  AD BILLING
						AND FINALLY DELETE THE USER ACCOUNT
						
					*/
					
					///////////PDO QUERY/////////

					##AD REPORT AND BILLING SUB DELETE QRY
					$subDelQry = "SELECT ID FROM banner_campaigns WHERE USER_ID=?";
					$subDelQry2 = "SELECT ID FROM text_campaigns WHERE USER_ID=?";
				
					//DELETE THE USER AD REPORT RECORDS/////
					///////////PDO QUERY///////	
					$sqlTmp = "
								DELETE FROM ad_traffic_reports WHERE (AD_ID IN(".$subDelQry.") OR AD_ID IN(".$subDelQry2."));
								DELETE FROM ad_billings WHERE (AD_ID IN(".$subDelQry.") OR AD_ID IN(".$subDelQry2."));
								";
								
					$sql =  "
								DELETE FROM banner_campaigns WHERE USER_ID=?; 
								DELETE FROM text_campaigns WHERE USER_ID=?;
								DELETE FROM users_metas WHERE USER_ID=?;
								DELETE FROM awarded_badges WHERE USER_ID=?;
								".$sqlTmp."
								DELETE FROM users WHERE ID=?;
							";
					$valArr = array($userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId);
					$stmt = $this->DBM->doSecuredQuery($sql, $valArr);
									
					// If we arrived here then our scheduled account termination transaction was a success, we simply end the transaction
					$this->DBM->endTransaction();
					
					
				}catch(Throwable $e){
					
					// Rollback if scheduled account termination transaction fails
					$this->DBM->cancelTransaction();																
					return false;
					
				}
				
			}
			
		}
		
		return true;
		
	}


	




}






?>