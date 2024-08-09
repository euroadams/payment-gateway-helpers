<?php



class MonnifyPaymentGateway extends PaymentGateway{
	
	/*** Generic member variables ***/
	
	private $gatewayName = 'monnify';
	private $API_BASE_URL = 'https://sandbox.monnify.com/api/v1/';
	
	//Replace this with your own live and test secret key and encryption key
	private $API_DETAILS_ARR = [
	
		'api_k' => MNFY_API_KEYS['api_k'], 
		'api_sk' => MNFY_API_KEYS['api_sk'], 
		'contract_code' => MNFY_API_KEYS['contract_code'], 
		
	];
			
	private $API_SECRET_KEY;
	private $API_CONTRACT_CODE;		
	
	
	private $authSlug = 'auth';
	private $authLoginSubSlug = 'login';
	
	
	private $reservedAccSlug = 'reserved-accounts';
	private $reservedAccTrxSubSlug = 'transactions';
	private $reservedAccLimitSubSlug = 'limit';
	
	
	private $merchantSlug = 'merchant';
	private $trxSubSlug = 'transactions';
	private $bankTransferSubSlug = 'bank-transfer';
	
	
	private $invoiceSlug = 'invoice';
	
	
	private $subaccountSlug = 'sub-accounts';
	
	
	private $limitProfileSlug = 'limit-profile';
	
	
	private $disbursementSlug = 'disbursements';
	private $disbursementSingleSubSlug = 'single';
	private $disbursementBatchSubSlug = 'batch';
	private $disbursementBulkSubSlug = 'bulk';
	private $disbursementTrxSubSlug = 'transactions';
	private $disbursementAuthSubSlug = 'validate-otp';
	private $disbursementResendOtpSubSlug = 'resend-otp';
	private $disbursementSummarySubSlug = 'summary';
	private $disbursementWalletSubSlug = 'wallet-balance';
	private $disbursementAccountSubSlug = 'account';
	
	
	
	
	
	/*** Constructor ***/
	public function __construct($trxCustomizations = 'title::Store,desc::Service Payment,logo::', $minAcceptablePayAmount = 0){
		
		$this->API_SECRET_KEY = $this->API_DETAILS_ARR['api_sk'];
		$this->API_CONTRACT_CODE = $this->API_DETAILS_ARR['contract_code'];

		parent::__construct($this->gatewayName, $this->merchantSlug.'/'.$this->trxSubSlug.'/'.$this->initializeSubSlug, $trxCustomizations, $minAcceptablePayAmount);
		
	}
	
	
	
	/*** Destructor ***/
	public function __destruct(){
		
		
	}

	
	
	/************************************************************************************/
	/************************************************************************************
									SITE METHODS
	/************************************************************************************
	/************************************************************************************/
		
		


	/* Method for linking Monnify inline popup JS  */
	private function linkPopupJs($alertUser=''){
		
		return '<script type="text/javascript" src="https://sandbox.sdk.monnify.com/plugin/monnify.js"></script>
				<button onclick="payWithMonnify()">Pay with Monnify</button>';
		
	}



	

	/* Method for processing payment transaction request */
	public function handlePaymentGatewayRequest(){
		
		global $siteDomain, $pageSelf, $rdr;
		
		
		/***************************BEGIN URL CONTROLLER****************************/
		
		$path = $this->ENGINE->get_page_path('page_url', '', true, true);

		$pagePathArr = explode('/', $path);
		
		$tabTabSubSlug = isset($pagePathArr[3])? $pagePathArr[3] : '';
				
		if(isset($pagePathArr[1]) && 
			in_array(($requestSlug = strtolower($pagePathArr[1])), 
				array(
					$this->reservedAccSlug, $this->authSlug, $this->merchantSlug, $this->invoiceSlug, $this->subaccountSlug, $this->limitProfileSlug, 
					$this->disbursementSlug, $this->verifyTrxGiveValSlug, $this->cancelTrxSlug, $this->webhookGatewaySlug,
				)
			)
		){
		
			$pathKeysArr = array('pageUrl', 'requestSlug');
			$maxPath = 2;
			
			
			//Authorization Tab
			
			if($requestSlug == $this->authSlug){
				
				$subTabsArr = array(
					$this->authLoginSubSlug, 
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Transaction Tab
			
			if($requestSlug == $this->merchantSlug){
				
				$subTabsArr = array(
					$this->trxSubSlug, $this->bankTransferSubSlug, 
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 4;
				else
					$maxPath = 0;
				
			}
			
			//Invoice Tab
			
			if($requestSlug == $this->invoiceSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->fetchSubSlug, $this->listSubSlug, $this->cancelSubSlug, 
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Subaccount Tab
			
			if($requestSlug == $this->subaccountSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->updateSubSlug, $this->removeSubSlug, 
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Limit Profile Tab
			
			if($requestSlug == $this->limitProfileSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->updateSubSlug
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Disbursement Tab
			
			if($requestSlug == $this->disbursementSlug){
				
				$subTabsArr = array(
					$this->disbursementSingleSubSlug, $this->disbursementBatchSubSlug, $this->disbursementBulkSubSlug,
					$this->disbursementWalletSubSlug, $this->disbursementAccountSubSlug, 
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Reserved Accounts Tab
			
			if($requestSlug == $this->reservedAccSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->fetchSubSlug, $this->removeSubSlug, $this->reservedAccTrxSubSlug, 
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			
		}else{
		
			$pathKeysArr = array();
			$maxPath = 0;
		
		}

		$this->ENGINE->url_controller(array('pathKeys'=>$pathKeysArr, 'maxPath'=>$maxPath));
		
		$requestSlug = $_GET['requestSlug'];
		
		/*******************************END URL CONTROLLER***************************/
		
	
		$requestSlug = $this->ENGINE->sanitize_user_input($requestSlug, array('lowercase' => true));
		
		$isPayInitialization = $useBearerAuth = $log2PaymentsAwaitingVerif = false;
		
		//Grab Request Query Strings Passed For API Calls
		$pathParam = urlencode(isset($_GET[($K="path_param")])? $_GET[$K] : (isset($_POST[($K)])? $_POST[$K] : ''));
		$pathParam2 = urlencode(isset($_GET[($K="path_param2")])? $_GET[$K] : (isset($_POST[($K)])? $_POST[$K] : ''));
		$postedUid = isset($_POST[($K="uid")])? $_POST[$K] : '';
		$postedCardSignature = isset($_POST[($K="cardSignature")])? $_POST[$K] : '';
		
		$postedAmount = isset($_POST[($K=$this->amountKey)])? $_POST[$K] : '';
		$this->preferredPayCurrency = isset($_POST[($K=$this->currencyKey)])? $_POST[$K] : $this->preferredPayCurrency;
		
		$normalizedAmount = $this->normalizeAmount($postedAmount);
						
		$postRequestParams = $this->filterPostData($normalizedAmount);
		$getRequestParams = $this->filterGetData();
		$apiRequestQstr = '?'.http_build_query($getRequestParams);
		
		$U = $this->ACCOUNT->loadUser($postedUid? $postedUid : $this->SESS->getUserId());
		$trxReference = isset($_POST[($K='transactionReference')])? $_POST[$K] : (isset($_GET[($K)])? $_GET[$K] : '');
		
		if(isset($_POST[($K=$this->trxCustomKey)]))
			$curlPostFields[$K] = $_SESSION[$this->keyUnique($K)] = $trxCustomizations = $_POST[$K];
			
		elseif(isset($_SESSION[($this->keyUnique($K))]))
			$trxCustomizations = $_SESSION[$this->keyUnique($K)];
		
		else
			$trxCustomizations = $this->trxCustomizations;
			
		$trxCustomizations = $this->ENGINE->str_to_assoc($trxCustomizations);
		$trxCustomTitle = $this->ENGINE->get_assoc_arr($trxCustomizations, 'title');
		$trxCustomDesc = $this->ENGINE->get_assoc_arr($trxCustomizations, 'desc');
		$trxCustomLogo = $this->ENGINE->get_assoc_arr($trxCustomizations, 'logo');
		$customerEmail = $U->getEmail();
		
		//HANDLE API REQUESTS CALLS FROM SLUG TYPE
		

		//HANDLE PAYMENTS/TRANSACTION ENDPOINT CALLS
		if($requestSlug == $this->merchantSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "POST";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->trxSubSlug: 
				
					switch($tabTabSubSlug){
						
						case $this->verifySubSlug:
						$curlRequestMethod = "GET";
						$flatSlugQstr = $this->trxSubSlug.'/query';
						break;
						
						
						default: //$this->initializeSubSlug
						
						$isPayInitialization = $log2PaymentsAwaitingVerif = true;
						$this->echoResponse = false;
						// url to go to after payment
						$callbackUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->verifyTrxGiveValSlug;
						$_SESSION[$this->keyUnique($this->internalRdrKey)] = preg_replace("#\#.*#", "", $rdr).'#'.$this->gatewayResponseDisplayId;
							
					
						if(isset($_POST[$this->payFormSubmitKey])){
							
							if($postedAmount){
								
								$trxReference = $this->generateReference();
								
								/*
									PAYMENT REQUEST PARAMS
									
									'paymentReference' => '', //Merchant's unique reference for the transaction
									'amount' => '', //The amount to be paid by the customer
									'currencyCode' => '', //Currency Code for the transaction. Value should be NGN
									'contractCode' => '', //Contract Code (See your Monnify dashboard)
									'customerEmail' => '', //Email address of the customer. This is the unique identifier for each customer
									'redirectUrl' => '', //The URL Monnify should redirect to when the transaction is completed
									
									//OPTIONAL
									'customerName' => '', //Full name of the customer
									'customerPhoneNumber' => '', //Phone Number of the customer
									'paymentDescription' => '', //A description of the transaction. Will be used as account name for pay with transfer
									'incomeSplitConfig' => //Object containing specifications on how payments to this reserve account should be split
										'subAccountCode' => '', //The unique reference identifying the sub account that should receive the split
										'feeBearer' => '', //Boolean to determine if the sub account should bear transaction fees or not
										'feePercentage' => '', //The percentage of the transaction fee to be borne by the sub account
										'splitPercentage' => '', //The percentage of the amount paid to be credited into the sub account
							
									
								
								*/
								
								
								$payCardDetails = isset($_POST[($this->payCardDetailsKey)])? $_POST[$this->payCardDetailsKey] : '';
								
								if($payCardDetails){
									
									$isTokenizedCharge = true;
									$payCardDetailsArr = $this->ENGINE->str_to_assoc($payCardDetails);
									$cardSignature = $this->ENGINE->get_assoc_arr($payCardDetailsArr, 'cardSignature');
									list($authorizationToken, $authorizationEmail, $authorizationCountry) = $this->getUserSavedPaymentCard($U->getUserId(), $cardSignature, true);
									
									//Ensure we use the card authorization email to charge the customer
									$customerEmail = $authorizationEmail;
									$curlPostFields['country'] = $authorizationCountry;
									$curlPostFields['email'] = $customerEmail;
									$curlPostFields['token'] = $authorizationToken;
									
									
								}
								
								//Keep track of customer's card save option for verification endpoint
								elseif(isset($_POST[$this->savePayCardKey])){
									
									$savePayCard = true;
									
								}
								
								$curlPostFields['paymentReference'] = $trxReference;
								$curlPostFields['amount'] = $normalizedAmount;
								$curlPostFields['currencyCode'] = $this->preferredPayCurrency;
								$curlPostFields['contractCode'] = $this->API_CONTRACT_CODE;
								$curlPostFields['customerEmail'] = $customerEmail;
								$curlPostFields['redirectUrl'] = $callbackUrl;
								
								if(isset($isTokenizedCharge)){
									
									$jsonDecodedResponseData = $this->chargeWithToken($curlPostFields);
									$this->giveValue($jsonDecodedResponseData->tx_ref);
									
								}
								
								
							}else{
								
								$alertUser = $this->errorAlertPrefix.'The amount you want to pay was not specified; please go back to enter the amount and try again</span>';
								
							}
							
						}
						
						$flatSlugQstr = $this->trxSubSlug.'/init-transaction';
						
				
					}
				
				break;
				
				
				case $this->bankTransferSubSlug:
				
					switch($tabTabSubSlug){
						
						default: //$this->initializeSubSlug
						
						$flatSlugQstr = $this->bankTransferSubSlug.'/init-payment';
						
					}
					
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->merchantSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			
		}
		
		
				

		//HANDLE VERIFY TRANSACTION AND GIVE VALUES ENDPOINT CALLS
		if($requestSlug == $this->verifyTrxGiveValSlug){
			
			if(isset($_GET[$K="paymentReference"]) && ($paymentReference = $_GET[$K])){
				
				//Verify the transaction and give value
				$this->giveValue($paymentReference);
				
				
			}else{
				
				$alertUser = $this->errorAlertPrefix.'Sorry we could not verify that transaction as no reference was found</span>';
				$this->returnToPaymentCallingPage($alertUser);
				
			}
		
		}
		
		
				
 
		//HANDLE RESERVED ACCOUNTS ENDPOINT CALLS
		if($requestSlug == $this->reservedAccSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams; 
				break;
				
				
				case $this->fetchSubSlug: 
				case $this->reservedAccTrxSubSlug:
				$flatSlugQstr = $pathParam;
				break; 
				
				
				case $this->removeSubSlug:
				$curlRequestMethod = "DELETE";
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->reservedAccLimitSubSlug:
				$flatSlugQstr = $this->reservedAccLimitSubSlug;
				$curlPostFields = $postRequestParams;
				
				switch($tabTabSubSlug){
					
					case $this->updateSubSlug:
					$curlRequestMethod = "PUT"; 
					break;
			
			
					default: //$this->createSubSlug
					$curlRequestMethod = "POST"; 
					
				}
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : 'bank-transfer/'.$this->reservedAccSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			
		}
		
		
				
				
 
		//HANDLE INVOICE ENDPOINT CALLS
		if($requestSlug == $this->invoiceSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams; 
				$flatSlugQstr = 'create';
				break;
				
				
				case $this->fetchSubSlug: 
				$flatSlugQstr = 'details';
				break; 
				
				
				case $this->listSubSlug:
				$flatSlugQstr = 'all'; 
				break;
				
				
				case $this->cancelSubSlug:
				$curlRequestMethod = "DELETE";
				$flatSlugQstr = 'cancel'; 
				break;
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->invoiceSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			
		}
		
		
			
		
				
				
 
		//HANDLE SUBACCOUNT ENDPOINT CALLS
		if($requestSlug == $this->subaccountSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT";
				$curlPostFields = $postRequestParams; 
				break;
				
				
				case $this->removeSubSlug:
				$curlRequestMethod = "DELETE"; 
				break;
				
				
				default: //$this->listSubSlug:
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->subaccountSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			
		}
		
		
				
			
		
				
				
 
		//HANDLE LIMIT TRANSACTION ENDPOINT CALLS
		if($requestSlug == $this->limitProfileSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam; 
				break;
				
				
				default: //$this->listSubSlug:
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->limitProfileSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			
		}
		
		
				
 
		//HANDLE DISBURSEMENT ENDPOINT CALLS
		if($requestSlug == $this->disbursementSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->disbursementSingleSubSlug: 
				$flatSlugQstr = $this->disbursementSingleSubSlug;
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
					
				switch($tabTabSubSlug){
					
					case $this->disbursementAuthSubSlug:
					$flatSlugQstr .= '/'.$this->disbursementAuthSubSlug; 
					break;
					
					case $this->disbursementResendOtpSubSlug:
					$flatSlugQstr .= '/'.$this->disbursementResendOtpSubSlug; 
					break;
					
					case $this->disbursementSummarySubSlug:
					$curlRequestMethod = "GET";
					$curlPostFields = '';
					$flatSlugQstr .= '/'.$this->disbursementSummarySubSlug; 
					break;
					
					case $this->disbursementTrxSubSlug:
					$curlRequestMethod = "GET";
					$curlPostFields = '';
					$flatSlugQstr .= '/'.$this->disbursementTrxSubSlug; 
					break;
			
			
					default: //$this->initializeSubSlug 
					
				}
				
				break;
				
				
				case $this->disbursementBatchSubSlug: 
				$flatSlugQstr = $this->disbursementBatchSubSlug;
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				
				switch($tabTabSubSlug){
					
					case $this->disbursementAuthSubSlug:
					$flatSlugQstr .= '/'.$this->disbursementAuthSubSlug; 
					break;
					
					case $this->disbursementSummarySubSlug:
					$curlRequestMethod = "GET";
					$curlPostFields = '';
					$flatSlugQstr .= '/'.$this->disbursementSummarySubSlug; 
					break;
			
			
					default: //$this->initializeSubSlug 
					
				}
				
				break;
				
				
				case $this->disbursementBulkSubSlug:
				$flatSlugQstr = $this->disbursementBulkSubSlug;
				$curlRequestMethod = "GET";
				
				switch($tabTabSubSlug){
					
					default: //$this->disbursementTrxSubSlug
					$flatSlugQstr .= '/'.$this->disbursementTrxSubSlug; 
					
				}
				
				break; 
				
				
				case $this->disbursementWalletSubSlug:
				$flatSlugQstr = $this->disbursementWalletSubSlug;
				break; 
				
				
				case $this->disbursementAccountSubSlug:
				$flatSlugQstr = $this->disbursementAccountSubSlug;
				$curlRequestMethod = "GET";
				
				switch($tabTabSubSlug){
					
					default: //$this->validateSubSlug
					$flatSlugQstr .= '/'.$this->validateSubSlug; 
					
				}
				
				break;
				
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->disbursementSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			
		}
		
		
				
				
 
				
 
		//HANDLE AUTHORIZATION ENDPOINT CALLS
		if($requestSlug == $this->authSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "POST";
			$this->echoResponse = $useBearerAuth = true;
			
			switch($tabSubSlug){
				
				case $this->authLoginSubSlug:
				$curlPostFields = $postRequestParams; 
				break;
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->authSlug.'/'.$this->authLoginSubSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			
		}
		
		
		

		
		//HANDLE CANCEL CALLBACK
		elseif($requestSlug == $this->cancelTrxSlug){
			
			$canceledTrxDetails='';
			
			if(isset($_SESSION[$K=$this->keyUnique($this->trxMetaKey)])){
				
				$trxMeta = json_decode($_SESSION[$K]);
				$this->expungeFromPaymentAwaitingVerif($trxMeta->tx_ref, $U->getUserId(), $trxMeta->amount);
				$canceledTrxDetails = '';//'<br/>Details:<br/> amount: '.$trxMeta->amount.', reference: '.$trxMeta->tx_ref.', status: canceled';
				
			}
			
			$alertUser = $this->errorAlertPrefix.'You canceled the payment'.$canceledTrxDetails.'</span>';
			$this->returnToPaymentCallingPage($alertUser);
			
		}
		
		
		//HANDLE FORGET PAYMENT CARD CALLS
		elseif($requestSlug == $this->forgetSavedPayCardSlug){
			
			$this->forgetUserPaymentCard($U->getUserId(), $postedCardSignature, false, true);
			exit(); //IMPORTANT
			
		}
		

		
		//HANDLE WEBHOOK CALLBACKS
		elseif($requestSlug == $this->webhookGatewaySlug){
			
			//Retrieve the request body 
			$input = $this->SITE->file_get_contents("http://input");
			
			//Log the retrieved event body for reference 
			$this->logWebhookEvent($input);				
			
			//Parse the webhook body
			$webhookBody = json_decode($input);
			
			
			//Handle each webhook events
			
			if(isset($webhookBody->settlementReference)){
				
				//do stuff for when you receive a settlement notification here
				
			}elseif(isset($webhookBody->paymentReference)){
				
				//Only a request with monnify transaction signature is allowed
				if(!$this->apiSignatureValidated($webhookBody)){

					//Respond to webhook calling endpoint with a unauthorized status code
					http_response_code(401);					
					exit();			

				}
			
				//Respond to webhook calling endpoint with a status code, we do this early enough to avoid timeout 
				http_response_code(200);

				//Verify the transaction and give value if not already given
				$this->giveValue($webhookBody->paymentReference, false);
				
			}
			
			//For webhook event we stop the script here
			exit(); //IMPORTANT
			
			
		}
		 
		 
		 
		 
		
		
		//POINT ENDPOINT CALLS TO APPROPRIATE API URL
		if(isset($curlUrl)){
			
			$curlHttpHeader = [
				"authorization: ".($useBearerAuth? 'Bearer '.$this->getBearerToken() : 'Basic '.$this->getBasicToken()),
				"content-type: application/json",
				"cache-control: no-cache"
			  ];
			  
			!isset($curlPostFields)? ($curlPostFields = '') : '';
			
			list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'method' => $curlRequestMethod, 'header' => $curlHttpHeader, 'postFields' => $curlPostFields, 'jsonData' => true, 'sameOrigin' => false));
			
			if($err){
				
				// there was an error contacting the API
				$alertUser = $this->errorAlertPrefix.'Error: '.$err.'</span>';
				//die('Curl returned error: '.$err);
				//print_r($alertUser);
				
			}else{
				
				$trx = $jsonDecodedResponse;
				
				//print_r($trx); // uncomment this line for debug
				
				if(strtolower($trx->responseMessage) != 'success'){
					
					// there was an error from the API
					$alertUser = $this->errorAlertPrefix.'Error: '.$trx->responseMessage.'</span>';
					
					/*
						Monnify does'nt seem to have callback URL for cancel alone, 
						so we implemented this to force cancel of all transaction 
						whose reference could'nt be found.

					*/
					if(stripos($trx->responseMessage, 'Could not find transaction with payment reference') !== false){

						echo json_encode(array("forceCancel" => true));
						exit();

					}

					//print_r('Error: '.$trx->message);
				  
				}

				else{
					
					$trxBody = $trx->responseBody;
					
					/*
						Log the payment reference before going to the payment page so that we can query it later from the webhook endpoint
						to give value if the trx was successful, just in case the customer had network issues that prevented redirection back 
						to the callBackUrl to get value immediately
					
					*/
					
					if($log2PaymentsAwaitingVerif && !$this->isPaymentAwaitingVerifLogged($trxReference, $U->getUserId(), $postedAmount))
						$this->logPaymentAwaitingVerif($trxCustomTitle, $customerEmail, $trxReference, $U->getUserId(), $postedAmount, $this->preferredPayCurrency, isset($savePayCard));
					
					
					//Save the initialized transaction metadatas into session for subsequent retrieval after redirections
					$_SESSION[$this->keyUnique($this->trxMetaKey)] = json_encode(['tx_ref' => $trxReference, 'amount' => $postedAmount]);
					
					if($isPayInitialization){
						
						// redirect to payment page
						header('Location: '.$trxBody->checkoutUrl);
						exit();
						
					}elseif($this->echoResponse){
						
						echo $rawResponse;
						exit();
						
					}
					
				}
					 
				
			}
			
			
		}else{
			
			$alertUser = $this->errorAlertPrefix.'An unexpected error has occurred please try again</span>';
			
		}
		
		$this->returnToPaymentCallingPage(isset($alertUser)? $alertUser : '');
		
		
		$title = 'Payment Gateway';

		$this->SITE->buildPageHtml(array("pageTitle"=>$title,
				"preBodyMetas"=>$this->SITE->getNavBreadcrumbs('<li><a href="/'.$pageSelf.'" class="links" >'.$title.'</a></li>'),
				"pageBody"=>'
					<div class="single-base blend">
						<div class="base-ctrl">					
							<div class="panel panel-orange">
								<h1 class="panel-head page-title">'.strtoupper($title).'</h1>
								<div class="panel-body">
									'.(isset($alertUser)? $alertUser : '').$this->getPaymentGatewayForm($this->SESS->getUserId()).'
								</div>
							</div>
						</div>
					</div>'
					
		));
		

	}







	/* Method for given value for service purchased */
	private function giveValue($paymentReferencePassed, $returnToCaller = true){
	 
		/* 
			After Payment, we call the transaction verification endpoint to verify the status of the transaction
			and if successful, we proceed to give value.
			In giving value, we proceed as follows;
			Query for the transaction with the paymentReferencePassed from our payments awaiting verification and if found;
			==> we confirm that the response status from the verification endpoint was a success
			==> match the following details from verification endpoint response against the ones queried from payments awaiting verification
				and if everything checks out, then give value accordingly.
				transaction amount, reference and currency.
			
		*/
		
		
		//Call verification endpoint
		
		$jsonDecodedResponse = $this->verifyTransaction(array('paymentReference' => $paymentReferencePassed));

		/*
			Monnify does'nt seem to have callback URL for cancel alone, 
			so we implemented this to force cancel of all transaction 
			whose reference could'nt be found.

		*/
		
		if(isset($jsonDecodedResponse->forceCancel)){

			header("Location: ".$this->domainPrefixedBasePaymentGatewayUrl.$this->cancelTrxSlug);
			exit();

		}
		
		$trxMssg = $jsonDecodedResponse->responseMessage;
		$trxBody = $jsonDecodedResponse->responseBody;
		$trxStatus = $trxBody->paymentStatus;
		$trxAmount = $trxAmountFmtd = $this->rollbackNormalizedAmount($trxBody->amount); //Remember to present back the real amount paid by user by stripping the padded lowest denomination digit
		$trxCurrency = $trxBody->currencyCode;
		$gatewayTrxId = $trxBody->transactionReference;
		$trxReference = $trxBody->paymentReference;
		$trxCustomerEmail = $trxBody->customerEmail;
		$trxCustomerId = isset($trxBody->customerId)? $trxBody->customerId : '';
		$trxCard = isset($trxBody->card)? $trxBody->card : '';
		$trxCardToken = isset($trxCard->token)? $trxCard->token : '';
		$trxCardBin = isset($trxCard->first_6digits)? $trxCard->first_6digits : '';
		$trxCardLast4Digits = isset($trxCard->last_4digits)? $trxCard->last_4digits : '';
		$trxCardExpiryDate = isset($trxCard->expiry)? $trxCard->expiry : '';
		
		
		//Query from payment awaiting verification
		
		$awaitingValRow = $this->getPaymentAwaitingVerif($paymentReferencePassed);
		$awaitingValTrxRef = $awaitingValRow["REFERENCE"];
		$awaitingValCustomerEmail = $awaitingValRow["CUSTOMER_EMAIL"];
		$awaitingValAmount = $awaitingValRow["VALUE_AMOUNT"];
		$awaitingValCurrency = $awaitingValRow["TRANX_CURRENCY"];
		$storeType = $awaitingValRow["STORE_NAME"];
		$savePayCard = $awaitingValRow["SAVE_CARD"];
		$U = $this->ACCOUNT->loadUser($awaitingValRow["USER_ID"]);
		
		$paymentReferenceIsLogged = $this->isPaymentReferenceLogged($U->getUserId(), $trxReference, $trxCustomerEmail);
		
		//$trxStatusesArr = array('paid', 'overpaid', 'partially_paid', 'pending', 'expired', 'failed', 'cancelled',);
		
		if(strtolower($trxMssg) == 'success' && strtolower($trxStatus) == 'paid' && $awaitingValAmount == $trxAmount &&  
		$awaitingValTrxRef == $trxReference && $awaitingValCurrency == $trxCurrency && 
		$awaitingValCustomerEmail == $trxCustomerEmail){
			
			// Give value if not already given for this transaction reference
		  
			if(!$paymentReferenceIsLogged){
				
				//Give value to the customer and log the transaction for reference
				
				$trxAmountFmtd = $this->giveVerifiedTrxVal($trxAmount, $storeType, $U);
				
				$this->logPaymentReference($storeType, $trxReference, $gatewayTrxId, $U->getUserId(), $trxCustomerId, $trxCustomerEmail, $trxAmount);

				//Save the card if customer asked for it
				if($savePayCard && $trxCardToken && $trxCustomerEmail){
					
					$trxCardSignature = $this->buildCardSignature($trxCardToken, $trxCardBin, $trxCardLast4Digits, $trxCardExpiryDate);
					
					if(!$this->isUserPaymentCardSaved($trxCardSignature, $trxCustomerEmail)){
						
						$this->saveUserPaymentCard($trxCardSignature, $U->getUserId(), $trxCustomerEmail, $trxCard);
						
					}
					
				}
				
				//Remove all payments that has been given value from transactions_awaiting_verification table
				$this->expungeFromPaymentAwaitingVerif($trxReference, $U->getUserId(), $trxAmount);
			  
			}
			
			$alertUser = $this->getSuccessMsg($trxAmountFmtd, $trxCurrency, $storeType);
							
			$paySuccess = true;
		  
		}elseif($paymentReferenceIsLogged){
			
			$alertUser = $this->getSuccessMsg($trxAmountFmtd, $trxCurrency, $storeType, 'completed');
			
		}else{
			
			$alertUser = $this->errorAlertPrefix.'Sorry the transaction could not be verified! please try again</span>';
			
		}
		
		if($returnToCaller)
			$this->returnToPaymentCallingPage(isset($alertUser)? $alertUser : '');

		//return array($alertUser, $paySuccess);
		
	}




	/* Method for building payment card signature */
	public function buildCardSignature($cardToken, $cardBin, $CardLast4Digits, $expiryDate){
	
		$data = $cardBin.$cardToken.$CardLast4Digits.'-'.$expiryDate;
		return 'SIG_'.hash('sha256', $data); 

	}



	/* Method for validating request api signature */
	private function apiSignatureValidated($data){
		
		$calculatedHash = hash('sha512', $this->API_SECRET_KEY.'|'.$data->paymentReference.'|'.$data->amountPaid.'|'.$data->paidOn.'|'.$data->transactionReference);
		
		return ($calculatedHash == $data->transactionHash);
		
	}




	
	
	/* Method for normalizing amount to API payment gateway required format */
	public function normalizeAmount($amount){
	
		return $amount;
		
	}



	/* Method for rolling back API normalized amount */
	public function rollbackNormalizedAmount($normalizedAmount){
	
		return $normalizedAmount;
		
	}

	
	
	
	
	
	
	/**********************************
	AUTHORIZATION API METHODS
	/**********************************
	

	/* Method for fetching API endpoint calls authentication token */
	public function getBasicToken(){
		
		$authUser = $this->API_DETAILS_ARR['api_k'].':'.$this->API_SECRET_KEY;
		
		return base64_encode($authUser);
		
	}
	
	
	/* Method for fetching api bearer authorization token */
	public function getBearerToken($userParams = []){
		
		
		$defaultParams = [
		
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->authSlug.'/'.$this->authLoginSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		return (isset($jsonDecodedResponse->responseBody->accessToken)? $jsonDecodedResponse->responseBody->accessToken : '');
		
		
	}

	

	
	
	
	
	/**********************************
	RESERVED ACCOUNTS API METHODS
	
	
	Attributes:
	
	'accountReference' => '', //Your unique reference used to identify this reserved account e.g ref12345
	'accountName' => '', //The name you want to be attached to the reserved account. This will be displayed during name enquiry e.g John Doe
	'currencyCode' => '', //Currency Code for reserved account. Value should be NGN e.g NG
	'contractCode' => '', //Contract Code, See your Monnify dashboard e.g J938018318
	'customerEmail' => '', //Email address of the customer who the account is being reserved for. This is the unique identifier for each customer
	'customerName' => '', //Full name of the customer who the account is being reserved for
	'customerBVN' => '', //BVN of the customer the account is being reserved for. Please note that if BVN is not supplied there will be low limits on the reserved account
	
	incomeSplitConfig:
		Object containing specifications on how payments to this reserve account should be split
		
		'subAccountCode' => '', //The unique reference identifying the sub account that should receive the split
		'feeBearer' => '', //Boolean to determine if the sub account should bear transaction fees or not
		'feePercentage' => '', //The percentage of the transaction fee to be borne by the sub account
		'splitPercentage' => '', //The percentage of the amount paid to be credited into the sub account
	
	
	
	***********************************/

	/* Method for creating a reserved account */
	public function createReservedAccount($userParams = []){
		
		$defaultParams = [
			/************ONLY SPECIFY FOR TYPE SPECIFIC RESERVED ACCOUNTS*********************/
			'reservedAccountType' => '', //This differentiates reserved account by Type. e.g INVOICE
			/*********************************/
			'accountReference' => '', //Your unique reference used to identify this reserved account e.g ref12345
			'accountName' => '', //The name you want to be attached to the reserved account. This will be displayed during name enquiry e.g John Doe
			'currencyCode' => '', //Currency Code for reserved account. Value should be NGN e.g NG
			'contractCode' => '', //Contract Code, See your Monnify dashboard e.g J938018318
			'customerEmail' => '', //Email address of the customer who the account is being reserved for. This is the unique identifier for each customer
			'customerName' => '', //Full name of the customer who the account is being reserved for
			'customerBVN' => '', //BVN of the customer the account is being reserved for. Please note that if BVN is not supplied there will be low limits on the reserved account
			
			'incomeSplitConfig' => '', //Object containing specifications on how payments to this reserve account should be split
				'subAccountCode' => '', //The unique reference identifying the sub account that should receive the split
				'feeBearer' => '', //Boolean to determine if the sub account should bear transaction fees or not
				'feePercentage' => '', //The percentage of the transaction fee to be borne by the sub account
				'splitPercentage' => '', //The percentage of the amount paid to be credited into the sub account
	
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = '';
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->reservedAccSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}

	
	
	
	
	/* Method for creating a reserved limit account */
	public function createReservedLimitAccount($userParams = []){
		
		$defaultParams = [
			
			'limitProfileCode' => '',
			'accountReference' => '', //Your unique reference used to identify this reserved account e.g ref12345
			'accountName' => '', //The name you want to be attached to the reserved account. This will be displayed during name enquiry e.g John Doe
			'currencyCode' => '', //Currency Code for reserved account. Value should be NGN e.g NG
			'contractCode' => '', //Contract Code, See your Monnify dashboard e.g J938018318
			'customerEmail' => '', //Email address of the customer who the account is being reserved for. This is the unique identifier for each customer
			'customerName' => '', //Full name of the customer who the account is being reserved for
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = '';
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->reservedAccSlug.'/'.$this->reservedAccLimitSubSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}

	
	
	


	
	
	/* Method for updating a reserved limit account */
	public function updateReservedLimitAccount($userParams = []){
		
		$defaultParams = [
			
			'limitProfileCode' => '',
			'accountReference' => '', //Your unique reference used to identify this reserved account e.g ref12345
			'accountName' => '', //The name you want to be attached to the reserved account. This will be displayed during name enquiry e.g John Doe
			'currencyCode' => '', //Currency Code for reserved account. Value should be NGN e.g NG
			'contractCode' => '', //Contract Code, See your Monnify dashboard e.g J938018318
			'customerEmail' => '', //Email address of the customer who the account is being reserved for. This is the unique identifier for each customer
			'customerName' => '', //Full name of the customer who the account is being reserved for
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = '';
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->reservedAccSlug.'/'.$this->reservedAccLimitSubSlug.'/'.$this->updateSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}

	
	
	


	/* Method for fetching a reserved account */
	public function fetchReservedAccount($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //accountReference; Account Reference of the reserved account to be retrieved 
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->reservedAccSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	
	
	
	/* Method for fetching reserved Accounts transaction */
	public function getReservedAccountTransaction($userParams){
		
		$defaultParams = [
		
			'accountReference' => '', //Account Reference of the reserved account whose transactions are to be retrieved 
			'page' => '', //Page to be returned. First page is 0
			'size' => '', //Number of records to be returned in a single page. Default value 10. 
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->reservedAccSlug.'/'.$this->reservedAccTrxSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}


	


	/* Method for deleting a reserved account */
	public function deleteReservedAccount($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //accountReference; Account Reference of the reserved account of interest 
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->reservedAccSlug.'/'.$this->removeSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	
	
	
	/**********************************
	TRANSACTION API METHODS
	***********************************/

	/* Method for charging via bank transfer */
	public function chargeViaBankTransfer($userParams = []){
		
		$defaultParams = [
		
			'transactionReference' => '', //Transaction reference returned by Monnify when the transaction was initialized
			//optional
			'bankCode' => '', //Bank Code for the bank's USSD string to be returned.
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = '';
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->merchantSlug.'/'.$this->bankTransferSubSlug.'/'.$this->initializeSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->data;
		
		
	}

	
	

	
	/* Method for verifying transaction */
	public function verifyTransaction($userParams){
		
		$defaultParams = [
			
			/** PARAMS **/
			'transactionReference' => '', 
			//OR 
			'paymentReference' => '',
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->merchantSlug.'/'.$this->trxSubSlug.'/'.$this->verifySubSlug.$qstr;
		
		return json_decode($this->SITE->file_get_contents($curlUrl));
		
	}
	
	
	
	
	
	
	/**********************************
	INVOICE API METHODS
	***********************************/
	
	/* Method for creating an invoice */
	public function createInvoice($userParams = []){
		
		$defaultParams = [
			
			/************ONLY SPECIFY WHEN ATTACHING A SPECIFIC RESERVED ACCOUNTS TO THE INVOICE*********************/
			'accountReference' => '', //Your unique reference used to identify this invoice type reserved account
			/*********************************/
			'amount' => '', //The amount to be paid by the customer
			'invoiceReference' => '', //Merchant's Unique reference for the invoice
			'description' => '', //Description of the transaction. Will be used as the account name for bank transfer payments
			'currencyCode' => '', //Currency Code for reserved account. Value should be NGN
			'contractCode' => '', //Contract Code, check your Monnify dashboard on the settings page
			'customerEmail' => '', //The email address of the customer
			'customerName' => '', //Full name of the customer
			//optional
			'expiryDate' => '', //10-30 12:00:00 (string, required) - The expiry date for the invoice. After this date, the customer will no longer be able to pay for that invoice. The format is YYYY-MM-DD HH:MM:SS
			'redirectUrl' => '', //A URL which customer will be redirected to when payment is successfully completed on the Web SDK.
			'paymentMethod' => '', //Object containing specifications of the payment method which the customer will use to make the payments. This can be set as "ACCOUNT_TRANSFER" or "CARD" . If not set, this would default to the enabled methods in the contract detail.
			'incomeSplitConfig' => '', //Object containing specifications on how payments to this reserve account should be split
				'subAccountCode' => '', //The unique reference identifying the sub account that should receive the split
				'feeBearer' => '', //Boolean to determine if the sub account should bear transaction fees or not
				'feePercentage' => '', //The percentage of the transaction fee to be borne by the sub account
				'splitPercentage' => '', //The percentage of the amount paid to be credited into the sub account
							
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = '';
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->data;
		
		
	}

	
	
	


	/* Method for fetching an invoice */
	public function fetchInvoice($userParams){
		
		$defaultParams = [
		
			'invoiceReference' => '', //Merchant's Unique reference for the invoice
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		return $jsonDecodedResponse;
		
	}
	
	


	/* Method for listing all invoices */
	public function getInvoiceList($userParams){
		
		$defaultParams = [
		
			'invoiceReference' => '', //Merchant's Unique reference for the invoice
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		return $jsonDecodedResponse;
		
	}
	


	/* Method for canceling an invoice */
	public function cancelInvoice($userParams){
		
		$defaultParams = [
		
			'invoiceReference' => '', //Merchant's Unique reference for the invoice
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->cancelSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		return $jsonDecodedResponse;
		
	}
	

	



	/**********************************
	SUBACCOUNTS API METHODS
	***********************************/
	
	
	/* Method for creating a subaccount */
	public function createSubaccount($userParams){
		
		$defaultParams = [
		
			[
				'currencyCode' => '', //Settlement currency. "NGN"
				'accountNumber' => '', //The account number that should be created as a sub account.
				'bankCode' => '', //The 3 digit bank code of the bank where the account number is domiciled
				'email' => '', //The email tied to the sub account. This email will receive settlement reports for settlements into the sub account.
				'defaultSplitPercentage' => '', //The default percentage to be split into the sub account on any transaction. Only applies if a specific amount is not passed during transaction initialization.
				
				//optional
				'accountName' => '', //The name attached to the account number.
				'subAccountCode' => '', //The unique reference for the sub account will be returned in the response.
				
			],
			
			[
				'currencyCode' => '', //Settlement currency. "NGN"
				'accountNumber' => '', //The account number that should be created as a sub account.
				'bankCode' => '', //The 3 digit bank code of the bank where the account number is domiciled
				'email' => '', //The email tied to the sub account. This email will receive settlement reports for settlements into the sub account.
				'defaultSplitPercentage' => '', //The default percentage to be split into the sub account on any transaction. Only applies if a specific amount is not passed during transaction initialization.
				
				//optional
				'accountName' => '', //The name attached to the account number.
				'subAccountCode' => '', //The unique reference for the sub account will be returned in the response.
				
			]
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subaccountSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	
	
	
	
	/* Method for listing your integration subaccounts */
	public function getSubaccountList($userParams = []){
		
		$defaultParams = [
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subaccountSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}





	/* Method for updating a subaccount */
	public function updateSubaccount($userParams){
		
		$defaultParams = [
		
			'currencyCode' => '', //Settlement currency. "NGN"
			'accountNumber' => '', //The account number that should be created as a sub account.
			'bankCode' => '', //The 3 digit bank code of the bank where the account number is domiciled
			'email' => '', //The email tied to the sub account. This email will receive settlement reports for settlements into the sub account.
			'defaultSplitPercentage' => '', //The default percentage to be split into the sub account on any transaction. Only applies if a specific amount is not passed during transaction initialization.
			
			//optional
			'accountName' => '', //The name attached to the account number.
			'subAccountCode' => '', //The unique reference for the sub account will be returned in the response.
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subaccountSlug.'/'.$this->updateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	


	/* Method for removing a subaccount */
	public function removeSubaccount($userParams){
		
		$defaultParams = [
		
			'subAccountCode' => '', //The unique reference for the sub account will be returned in the response.
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subaccountSlug.'/'.$this->removeSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	



	
	
	
	
	
	/**********************************
	DISBURSEMENT API METHODS
	***********************************/

	/* Method for initiating single transfer */
	public function transferSingle($userParams = []){
		
		$defaultParams = [
		
			'amount' => '', //The amount to be disbursed to the beneficiary
			'reference' => '', //The unique reference for a transaction. Also to be specified for each transaction in a bulk transaction request.
			'narration' => '', //The Narration for the transactions being processed.
			'bankCode' => '', //The 3 digit bank code representing the destination bank.
			'currency' => '', //The currency of the transaction being initialized. "NGN".
			'accountNumber' => '', //The beneficiary account number.
			'walletId' => '', //Unique reference to identify the wallet to be debited.
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = '';
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disbursementSlug.'/'.$this->disbursementSingleSubSlug.'/'.$this->initializeSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->data;
		
		
	}

	
	

	/* Method for initiating bulk transfer */
	public function transferBulk($userParams = []){
		
		$defaultParams = [
		
			'batchReference' => '', //The unique reference for the entire batch of transactions being sent.
			'walletId' => '', //Unique reference to identify the wallet to be debited.
			//optional
			'totalTransactions' => '', //The total number of transactions in the batch.
			'totalAmount' => '', //The total amount deducted for all the transactions in the batch.
			'totalFee' => '', //The total transaction fees deducted for all the transactions in the batch.
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = '';
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disbursementSlug.'/'.$this->disbursementBatchSubSlug.'/'.$this->initializeSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->data;
		
		
	}

	
	

	/* Method for authorizing single transfer */
	public function authorizeSingleTransfer($userParams = []){
		
		$defaultParams = [
		
			'reference' => '', //The unique reference for a single transaction Also to be specified for each transaction in a bulk transaction request.
			'authorizationCode' => '', //The One Time Password sent to the specified email to be used to authenticate the transaction.
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = '';
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disbursementSlug.'/'.$this->disbursementSingleSubSlug.'/'.$this->disbursementAuthSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->data;
		
		
	}
	


	/* Method for authorizing bulk transfer */
	public function authorizeBulkTransfer($userParams = []){
		
		$defaultParams = [
		
			'reference' => '', //The unique reference for a single transaction Also to be specified for each transaction in a bulk transaction request.
			'authorizationCode' => '', //The One Time Password sent to the specified email to be used to authenticate the transaction.
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = '';
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disbursementSlug.'/'.$this->disbursementBatchSubSlug.'/'.$this->disbursementAuthSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->data;
		
		
	}

	
	

	/* Method for resending transfer otp */
	public function resendTransferOtp($userParams = []){
		
		$defaultParams = [
		
			'reference' => '', //The unique reference for a single transaction Also to be specified for each transaction in a bulk transaction request.
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = '';
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disbursementSlug.'/'.$this->disbursementSingleSubSlug.'/'.$this->disbursementResendOtpSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->data;
		
		
	}

	
	

	
	/* Method for fetching single transfer details */
	public function getSingleTransferDetails($userParams){
		
		$defaultParams = [
			
			/** PARAMS **/
			'reference' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disbursementSlug.'/'.$this->disbursementSingleSubSlug.'/'.$this->disbursementSummarySubSlug.$qstr;
		
		return json_decode($this->SITE->file_get_contents($curlUrl));
		
	}
	
	

	
	/* Method for fetching bulk transfer details */
	public function getBulkTransferDetails($userParams){
		
		$defaultParams = [
			
			/** PARAMS **/
			'reference' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disbursementSlug.'/'.$this->disbursementBatchSubSlug.'/'.$this->disbursementSummarySubSlug.$qstr;
		
		return json_decode($this->SITE->file_get_contents($curlUrl));
		
	}


	
	/* Method for fetching bulk transfer transaction */
	public function getBulkTransferTransaction($userParams){
		
		$defaultParams = [
			
			/** PARAMS **/
			'batchReference' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disbursementSlug.'/'.$this->disbursementBulkSubSlug.'/'.$this->disbursementTrxSubSlug.$qstr;
		
		return json_decode($this->SITE->file_get_contents($curlUrl));
		
	}

	
	
	/* Method for fetching single transfer list */
	public function getSingleTransferList($userParams){
		
		$defaultParams = [
			
			/** PARAMS **/
			'pageSize' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disbursementSlug.'/'.$this->disbursementSingleSubSlug.'/'.$this->disbursementTrxSubSlug.$qstr;
		
		return json_decode($this->SITE->file_get_contents($curlUrl));
		
	}

	
	
	/* Method for fetching bulk transfer list */
	public function getBulkTransferList($userParams){
		
		$defaultParams = [
			
			/** PARAMS **/
			'pageSize' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disbursementSlug.'/'.$this->disbursementBulkSubSlug.'/'.$this->disbursementTrxSubSlug.$qstr;
		
		return json_decode($this->SITE->file_get_contents($curlUrl));
		
	}

	
	
	/* Method for fetching disbursement wallet balance */
	public function getWalletBalance($userParams){
		
		$defaultParams = [
			
			/** PARAMS **/
			'walletId' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disbursementSlug.'/'.$this->disbursementWalletSubSlug.$qstr;
		
		return json_decode($this->SITE->file_get_contents($curlUrl));
		
	}
	
	
	/* Method for validating bank account details */
	public function validateBankAccountDetails($userParams){
		
		$defaultParams = [
			
			/** PARAMS **/
			'accountNumber' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disbursementSlug.'/'.$this->disbursementAccountSubSlug.'/'.$this->validateSubSlug.$qstr;
		
		return json_decode($this->SITE->file_get_contents($curlUrl));
		
	}
	
	
	
	
	

	



	/**********************************
	LIMIT TRANSACTION API METHODS
	***********************************/
	
	
	/* Method for creating a limit profile */
	public function createLimitProfile($userParams){
		
		$defaultParams = [
		
			'limitProfileName' => '', //The name of the Limit Profile
			'singleTransactionValue' => '', //The maximum amount that can be allowed per transaction on the reserved accounts.
			'dailyTransactionVolume' => '', //The maximum number of transaction count per day allowed on the reserved accounts
			'dailyTransactionValue' => '', //The maximum amount per day in all transactions that can be allowed on the reserved accounts
			
			//optional
			'limitProfileCode' => '', //This Limit Profile code is the unique identifier for the Limit Profile used to reference the Limit Profile
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->limitProfileSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	
	
	
	
	/* Method for listing your integration limit profiles */
	public function getLimitProfileList($userParams = []){
		
		$defaultParams = [
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->limitProfileSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}





	/* Method for updating a limit profile */
	public function updateLimitProfile($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //limitProfileCode;
			'limitProfileName' => '', //The name of the Limit Profile
			'singleTransactionValue' => '', //The maximum amount that can be allowed per transaction on the reserved accounts.
			'dailyTransactionVolume' => '', //The maximum number of transaction count per day allowed on the reserved accounts
			'dailyTransactionValue' => '', //The maximum amount per day in all transactions that can be allowed on the reserved accounts
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->limitProfileSlug.'/'.$this->updateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}





	
	//JAVASCRIPT POPUP IMPLEMENTATION
	/*
	<script type="text/javascript" src="https://sandbox.sdk.monnify.com/plugin/monnify.js"></script>
	<button onclick="payWithMonnify()">Pay with Monnify</button>
				 
	 
	<script type="text/javascript">
		function payWithMonnify() {
			MonnifySDK.initialize({
				amount: 5000,
				currency: "NGN",
				reference: '' + Math.floor((Math.random() * 1000000000) + 1),
				customerFullName: "John Doe",
				customerEmail: "monnify@monnify.com",
				customerMobileNumber: "08121281921",
				apiKey: "MK_TEST_SAF7HR5F3F",
				contractCode: "4934121693",
				paymentDescription: "Test Pay",
				isTestMode: true,
				incomeSplitConfig:  [
					{
						"subAccountCode": "MFY_SUB_342113621921",
						"feePercentage": 50,
						"splitAmount": 1900,
						"feeBearer": true
					},
					{
						"subAccountCode": "MFY_SUB_342113621922",
						"feePercentage": 50,
						"splitAmount": 2100,
						"feeBearer": true
					}
				],
				onComplete: function(response){
					//Implement what happens when transaction is completed.
					console.log(response);
				},
				onClose: function(data){
					//Implement what should happen when the modal is closed here
					console.log(data);
				}
			});
		}
	</script>
  */
	


}





?>
