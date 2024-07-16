<?php



class FlutterwavePaymentGateway extends PaymentGateway{

	/*** Generic member variables ***/
	
	private $gatewayName = 'flutterwave';
	private $API_BASE_URL = 'https://api.flutterwave.com/v3/';
	
	//Replace this with your own live and test secret key and encryption key
	private $API_DETAILS_ARR = [
	
		'test_sk' => FLWV_API_KEYS['test_sk'], 
		'test_pk' => FLWV_API_KEYS['test_pk'],
		'test_encrypt' => FLWV_API_KEYS['test_encrypt'], 
		
		'live_sk' => FLWV_API_KEYS['live_sk'],
		'live_pk' => FLWV_API_KEYS['live_pk'],
		'live_encrypt' => FLWV_API_KEYS['live_encrypt'],
		
		'secret_hash' => FLWV_API_KEYS['secret_hash'],
		
	];
			
	private $API_SECRET_KEY;
	private $API_ENC_KEY;
	
	
	private $endpointDoc = 'endpoint-documentation';
	
	
	private $paymentSlug = 'payments';
	
	
	private $trxSlug = 'transactions';
	private $trxFeeSubSlug = 'fee';
	private $trxRefundSubSlug = 'refund';
	private $trxHookResendSubSlug = 'resend-hook';
	private $trxTimelineSubSlug = 'events';
	
	
	private $chargeSlug = 'charges';
	private $chargeValidationSubSlug = 'validate-charge';
	private $cardChargeSubSlug = 'card';
	private $ngBnkAccChargeSubSlug = 'debit_ng_account';
	private $ukBnkAccChargeSubSlug = 'debit_uk_account';
	private $bnkTransferChargeSubSlug = 'bank_transfer';
	private $ghMomoChargeSubSlug = 'mobile_money_ghana';
	private $rwMomoChargeSubSlug = 'mobile_money_rwanda';
	private $ugMomoChargeSubSlug = 'mobile_money_uganda';
	private $zaMomoChargeSubSlug = 'mobile_money_zambia';
	private $francoMomoChargeSubSlug = 'mobile_money_franco';
	private $ussdChargeSubSlug = 'ussd';
	private $mpesaChargeSubSlug = 'mpesa';
	private $voucherPayChargeSubSlug = 'voucher_payment';
	
	
	private $preauthChargeSlug = 'preauthorize';
	private $preauthChargeCaptureSubSlug = 'capture';
	private $preauthChargeVoidSubSlug = 'void';
	private $preauthChargeRefundSubSlug = 'refund';
	
	
	private $tokenSlug = 'tokens';
	private $tokenizedChargeSubSlug = 'tokenized-charges';
	private $tokenizedChargeBulkSubSlug = 'bulk-tokenized-charges';
	private $tokenizedChargeBulkTrxSubSlug = 'transactions';
	
	
	private $transferSlug = 'transfers';
	private $transferBulkSubSlug = 'bulk-transfers';
	private $transferFeeSubSlug = 'fee';
	private $transferRateSubSlug = 'rates';
	
	
	private $beneficiarySlug = 'beneficiaries';
	
	
	private $virtualCardSlug = 'virtual-cards';
	private $virtualCardFundingSubSlug = 'fund';
	private $virtualCardWithdrawalSubSlug = 'withdraw';
	private $virtualCardTerminateSubSlug = 'terminate';
	private $virtualCardTrxSubSlug = 'transactions';
	private $virtualCardStatusSubSlug = 'status';
	
	
	private $virtualAccNumSlug = 'virtual-account-numbers';
	private $virtualAccNumBulkSubSlug = 'bulk-virtual-account-numbers';
	
	
	private $planSlug = 'payment-plans';
	
	
	private $subscriptionSlug = 'subscriptions';
	
	
	private $subaccountSlug = 'subaccounts';
	
	
	private $billSlug = 'bills';
	private $billCategSubSlug = 'bill-categories';
	private $billBulkSubSlug = 'bulk-bills';
	
	
	//Remita payments
	private $billerSlug = 'billers';
	private $billerProductSubSlug = 'products';
	private $billerProductAmtSubSlug = 'product-amount';
	private $billerProductOrderSubSlug = 'orders';
	private $billerProductOrderUpdateSubSlug = 'update-order';
	
	
	private $settlementSlug = 'settlements';
	
	
	private $bankSlug = 'banks';
	private $bankBranchSubSlug = 'branches';
	
	
	private $balanceSlug = 'balances';
	private $balancePerCurrencySubSlug = 'currency';
	
	
	//foreign exchange rates
	private $fxRateSlug = 'rates';
	
	
	private $accountSlug = 'accounts';
	
	
	private $kycSlug = 'kyc';
	private $bvnSubSlug = 'bvns';
	
	
	private $cardBinSlug = 'card-bins';
	
	
	private $otpSlug = 'otps';
	
	
	private $chargebackSlug = 'chargebacks';
	private $chargebackAcknowledgementSubSlug = 'acknowledgement';
	
	
	
	
	
	/*** Constructor ***/
	public function __construct($trxCustomizations = 'title::Store,desc::Service Payment,logo::', $minAcceptablePayAmount = 0){
		
		$this->API_SECRET_KEY = (API_TEST_MODE? $this->API_DETAILS_ARR['test_sk'] : $this->API_DETAILS_ARR['live_sk']);
		$this->API_ENC_KEY = (API_TEST_MODE? $this->API_DETAILS_ARR['test_encrypt'] : $this->API_DETAILS_ARR['live_encrypt']);		

		parent::__construct($this->gatewayName, $this->paymentSlug.'/'.$this->initializeSubSlug, $trxCustomizations, $minAcceptablePayAmount);
		
	}
	
	
	
	/*** Destructor ***/
	public function __destruct(){
		
		
	}

	
	
	/************************************************************************************/
	/************************************************************************************
									SITE METHODS
	/************************************************************************************
	/************************************************************************************/
		



	/* Method for linking Flutterwave inline popup JS  */
	private function linkPopupJs($alertUser=''){
		
		return '<script type="text/javascript" src="https://checkout.flutterwave.com/v3.js"></script>
				<button type="button" onClick="makePayment();">Pay Now</button>';
		
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
					$this->paymentSlug, $this->trxSlug, $this->chargeSlug, $this->preauthChargeSlug, $this->tokenSlug, $this->transferSlug, 
					$this->beneficiarySlug, $this->virtualCardSlug, $this->virtualAccNumSlug, $this->planSlug, $this->cancelTrxSlug,
					$this->subscriptionSlug, $this->subaccountSlug, $this->billSlug, $this->billerSlug, $this->settlementSlug, 
					$this->bankSlug, $this->balanceSlug, $this->fxRateSlug, $this->accountSlug, $this->kycSlug, $this->forgetSavedPayCardSlug,
					$this->cardBinSlug, $this->otpSlug, $this->chargebackSlug, $this->endpointDoc, $this->verifyTrxGiveValSlug, $this->webhookGatewaySlug,
				)
			)
		){
		
			$pathKeysArr = array('pageUrl', 'requestSlug');
			$maxPath = 2;
			
			//Payments Tab
			
			if($requestSlug == $this->paymentSlug){
				
				$subTabsArr = array(
					$this->initializeSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			//Transaction Tab
			
			if($requestSlug == $this->trxSlug){
				
				$subTabsArr = array(
					$this->listSubSlug, $this->verifySubSlug, $this->trxFeeSubSlug, $this->trxRefundSubSlug, $this->trxHookResendSubSlug, $this->trxTimelineSubSlug, 
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			//verify transaction and give value Tab
			
			if($requestSlug == $this->verifyTrxGiveValSlug){
				
				
				
			}
			
			//Charge Tab
			
			elseif($requestSlug == $this->chargeSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->validateSubSlug, 
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			//Preauthorization Tab
			
			elseif($requestSlug == $this->preauthChargeSlug){
				
				$subTabsArr = array(
					$this->preauthChargeCaptureSubSlug, $this->preauthChargeVoidSubSlug, $this->preauthChargeRefundSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Token Tab
			
			elseif($requestSlug == $this->tokenSlug){
				
				$subTabsArr = array(
					$this->tokenizedChargeSubSlug, $this->tokenizedChargeBulkSubSlug, $this->tokenizedChargeBulkTrxSubSlug, 
					$this->updateSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
				
			}
			
			//Transfer Tab
			
			elseif($requestSlug == $this->transferSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->transferBulkSubSlug, $this->transferFeeSubSlug, 
					$this->transferRateSubSlug, 
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
				
			}
			
			//Transfer Beneficiary Tab
			
			elseif($requestSlug == $this->beneficiarySlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->removeSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
				
			}
			
			//Virtual Cards Tabs
			
			elseif($requestSlug == $this->virtualCardSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->virtualCardFundingSubSlug, $this->virtualCardWithdrawalSubSlug, 
					$this->virtualCardTerminateSubSlug, $this->virtualCardStatusSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Virtual Account Numbers Tab
			
			elseif($requestSlug == $this->virtualAccNumSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->fetchSubSlug, $this->virtualAccNumBulkSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Plan Tab
			
			elseif($requestSlug == $this->planSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->updateSubSlug,
					$this->cancelSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Subscription Tab
			
			elseif($requestSlug == $this->subscriptionSlug){
				
				$subTabsArr = array(
					$this->listSubSlug, $this->activateSubSlug, $this->cancelSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Subaccounts Tab
			
			elseif($requestSlug == $this->subaccountSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->updateSubSlug, $this->removeSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Bill Tab
			
			elseif($requestSlug == $this->billSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, 
					$this->validateSubSlug, $this->billCategSubSlug, $this->billBulkSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Biller Tab
			
			elseif($requestSlug == $this->billerSlug){
				
				$subTabsArr = array(
					$this->listSubSlug, $this->billerProductOrderSubSlug, $this->billerProductSubSlug, $this->billerProductAmtSubSlug,
					$this->billerProductOrderUpdateSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Settlements Tab
			
			elseif($requestSlug == $this->settlementSlug){
				
				$subTabsArr = array(
					$this->listSubSlug, $this->fetchSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Bank Tab
			
			elseif($requestSlug == $this->bankSlug){
				
				$subTabsArr = array(
					$this->listSubSlug, $this->bankBranchSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Balance Tab
			
			elseif($requestSlug == $this->balanceSlug){
				
				$subTabsArr = array(
					$this->fetchSubSlug, $this->balancePerCurrencySubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Foreign Exchange Rates Tab
			
			elseif($requestSlug == $this->fxRateSlug){
				
				$subTabsArr = array(
					$this->fetchSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Account Tab
			
			elseif($requestSlug == $this->accountSlug){
				
				$subTabsArr = array(
					$this->resolveSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			//KYC Tab
			
			elseif($requestSlug == $this->kycSlug){
				
				$subTabsArr = array(
					
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//KYC Tab
			
			elseif($requestSlug == $this->kycSlug){
				
				$subTabsArr = array(
					$this->bvnSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			//Card Bin Tab
			
			elseif($requestSlug == $this->cardBinSlug){
				
				$subTabsArr = array(
					$this->resolveSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//OTP Tab
			
			elseif($requestSlug == $this->otpSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->validateSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			//Chargeback Tab
			
			elseif($requestSlug == $this->chargebackSlug){
				
				$subTabsArr = array(
					$this->listSubSlug, $this->fetchSubSlug, $this->chargebackAcknowledgementSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Endpoint Documentation Tab
			
			elseif($requestSlug == $this->endpointDoc){
				
				$subTabsArr = array(
					$this->listSubSlug, $this->fetchSubSlug
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
		
		$isPayInitialization = $isTokenizedCharge = $log2PaymentsAwaitingVerif = false;
		
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
		$trxReference = isset($_POST[($K='tx_ref')])? $_POST[$K] : (isset($_GET[($K)])? $_GET[$K] : '');
		
		if(isset($_POST[($K=$this->trxCustomKey)]))
			$curlPostFields[$K] = $_SESSION[$this->keyUnique($K)] = $trxCustomizations = $_POST[$K];
			
		elseif(isset($_SESSION[$this->keyUnique($K)]))
			$trxCustomizations = $_SESSION[$this->keyUnique($K)];
		
		else
			$trxCustomizations = $this->trxCustomizations;
			
		$trxCustomizations = $this->ENGINE->str_to_assoc($trxCustomizations);
		$trxCustomTitle = $this->ENGINE->get_assoc_arr($trxCustomizations, 'title');
		$trxCustomDesc = $this->ENGINE->get_assoc_arr($trxCustomizations, 'desc');
		$trxCustomLogo = $this->ENGINE->get_assoc_arr($trxCustomizations, 'logo');
		$customerEmail = $U->getEmail();
		
		//HANDLE API REQUESTS CALLS FROM SLUG TYPE
		

		//HANDLE PAYMENTS ENDPOINT CALLS
		if($requestSlug == $this->paymentSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "POST";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->initializeSubSlug: 
				
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
							
							'tx_ref' => '', //Your transaction reference. This MUST be unique for every transaction 
							'amount' => '', //Amount to charge the customer.
							'payment_options' => '', //This specifies the payment options to be displayed e.g - card, mobilemoney, ussd and so on
							'redirect_url' => '', //URL to redirect to when a transaction is completed. This is useful for 3DSecure payments so we can redirect your customer back to a custom page you want to show them.
							'customer' => '', //This is an object that can contains your customer details: e.g - 
												'customer': {
													'email': 'example@example.com',
													'phonenumber': '08012345678',
													'name': 'Takeshi Kovacs'
												}
							'customizations' => '', //This is an object that contains title, logo, and description you want to display on the modal e.g
											{
												'title': 'Pied Piper Payments',
												'description': 'Middleout isn't free. Pay the price',
												'logo': 'https://assets.piedpiper.com/logo.png'
											}
							
							//OPTIONAL
							'currency' => '', //currency to charge in. Defaults to NGN
							'integrity_hash' => '', //This is a sha256 hash of your FlutterwaveCheckout values, it is used for passing secured values to the payment gateway.
							'payment_plan' => '', //This is the payment plan ID used for Recurring billing
							'subaccounts' => '', //This is an array of objects containing the subaccount IDs to split the payment into. Check our Split Payment page for more info 
							'meta' => '', //This is an object that helps you include additional payment information to your request e.g 
											{
												'consumer_id': 23,
												'consumer_mac': '92a3-912ba-1192a'
											}
							
			
						
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
						
						$curlPostFields['tx_ref'] = $trxReference;
						$curlPostFields['amount'] = $normalizedAmount;
						$curlPostFields['currency'] = $this->preferredPayCurrency;
						$curlPostFields['payment_options'] = $this->preferredPayOptions;
						$curlPostFields['redirect_url'] = $callbackUrl;
						
						$curlPostFields['customer'] = [
						
							'email' => $customerEmail,
							'phone_number' => $U->getPhone(),
							'name' => $U->getFullName(),
						
						];
						
						$curlPostFields['customizations'] = [
						
							'title' => $trxCustomTitle,
							'description' => $trxCustomDesc,
							'logo' => $trxCustomLogo,
						
						];
						
						if($isTokenizedCharge){
							
							$jsonDecodedResponseData = $this->chargeWithToken($curlPostFields);
							
							if(isset($jsonDecodedResponseData->tx_ref))
								$this->giveValue($jsonDecodedResponseData->id, $jsonDecodedResponseData->tx_ref);
							
						}
						
						
					}else{
						
						$alertUser = $this->errorAlertPrefix.'The amount you want to pay was not specified; please go back to enter the amount and try again</span>';
						
					}
					
				}
				
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->paymentSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			
		}
		
		
				

		//HANDLE VERIFY TRANSACTION AND GIVE VALUES ENDPOINT CALLS
		if($requestSlug == $this->verifyTrxGiveValSlug){
			
			if(isset($_GET[$K="tx_ref"]) && ($reference = $_GET[$K]) &&  
			isset($_GET[$K="status"]) && ($trxStatus = $_GET[$K])){
				
				$gatewayTrxId = isset($_GET[$K="transaction_id"])? $_GET[$K] : '';
				
				switch(strtolower($trxStatus)){
					
					case 'successful':
						//Verify the transaction and give value
						$this->giveValue($gatewayTrxId, $reference);
						break;
					
					
					case 'cancelled':
						header("Location:".$this->cancelActionCallbackUrl);
						exit();
						break;
						
					
					default:
				}
				
				
			}else{
				
				$alertUser = $this->errorAlertPrefix.'Sorry we could not verify that transaction as no reference was found</span>';
				$this->returnToPaymentCallingPage($alertUser);
				
			}
		
		}
		
		
				

		//HANDLE CHARGE ENDPOINT CALLS
		if($requestSlug == $this->chargeSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "POST";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->chargeValidationSubSlug:
				$curlPostFields = $postRequestParams;
				$apiRequestSlug = $this->chargeValidationSubSlug; 
				break;
				
				
				default: //Create A Charge Using the type passed in apiRequestQstr
				$chargeType = isset($_GET[$K="type"])? $_GET[$K] : '';
				$log2PaymentsAwaitingVerif = true;
				
				switch(strtolower($chargeType)){
					
					case $this->cardChargeSubSlug:
					$postRequestParams = $this->encryptPayload($postRequestParams);
					break;
					
					
				}
				
				$curlPostFields = $postRequestParams;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->chargeSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			
		}
		
		
		
		//HANDLE PREAUTHORIZATION ENDPOINT CALLS
		elseif($requestSlug == $this->preauthChargeSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "POST";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->preauthChargeCaptureSubSlug:
				$log2PaymentsAwaitingVerif = true;
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->preauthChargeCaptureSubSlug;
				break;
				
				
				case $this->preauthChargeVoidSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->preauthChargeVoidSubSlug;
				break;
				
				
				case $this->preauthChargeRefundSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->preauthChargeRefundSubSlug;
				break;
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->chargeSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE TOKEN ENDPOINT CALLS
		elseif($requestSlug == $this->tokenSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "POST";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->tokenizedChargeSubSlug:
				$log2PaymentsAwaitingVerif = true;
				$curlPostFields = $postRequestParams;
				$apiRequestSlug = $this->tokenizedChargeSubSlug;
				break;
				
				
				case $this->tokenizedChargeBulkSubSlug:
				$log2PaymentsAwaitingVerif = true;
				$apiRequestSlug = $this->tokenizedChargeBulkSubSlug;
				
				switch($tabTabSubSlug){
					
					case $this->createSubSlug:
					$log2PaymentsAwaitingVerif = true;
					$curlPostFields = $postRequestParams;
					break;
					
					case $this->fetchSubSlug:
					$curlRequestMethod = "GET";
					$flatSlugQstr = $pathParam;
					break;
					
					case $this->tokenizedChargeBulkTrxSubSlug:
					$curlRequestMethod = "GET";
					$flatSlugQstr = $pathParam.'/'.$this->tokenizedChargeBulkTrxSubSlug;
					break;
					
					
				}
				break;
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->tokenSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE TRANSACTION ENDPOINT CALLS
		elseif($requestSlug == $this->trxSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "POST";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->listSubSlug:
				$curlRequestMethod = "GET";
				break;
				
				
				case $this->verifySubSlug:
				$curlRequestMethod = "GET";
				$flatSlugQstr = $pathParam.'/'.$this->verifySubSlug;
				break;
				
				
				case $this->trxFeeSubSlug:
				$curlRequestMethod = "GET";
				$flatSlugQstr = $this->trxFeeSubSlug;
				break;
				
				
				case $this->trxRefundSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->trxRefundSubSlug;
				break;
				
				
				case $this->trxHookResendSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->trxHookResendSubSlug;
				break;
				
				
				case $this->trxTimelineSubSlug:
				$curlRequestMethod = "GET";
				$flatSlugQstr = $pathParam.'/'.$this->trxTimelineSubSlug;
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->trxSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE TRANSFER ENDPOINT CALLS
		elseif($requestSlug == $this->transferSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$log2PaymentsAwaitingVerif = true;
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->listSubSlug:
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->transferFeeSubSlug:
				$flatSlugQstr = $this->transferFeeSubSlug;
				break;
				
				
				case $this->transferRateSubSlug:
				$flatSlugQstr = $this->transferRateSubSlug;
				break;
				
				
				case $this->transferBulkSubSlug:
				$apiRequestSlug = $this->transferBulkSubSlug;
				
				switch($tabTabSubSlug){
					
					case $this->createSubSlug:
					$log2PaymentsAwaitingVerif = true;
					$curlRequestMethod = "POST";
					$curlPostFields = $postRequestParams;
					break;
					
					case $this->listSubSlug: //Get Bulk Transfers
					$apiRequestSlug = '';
					break;
					
				}
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->transferSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		
		//HANDLE BENEFICIARY ENDPOINT CALLS
		elseif($requestSlug == $this->beneficiarySlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->listSubSlug:
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->removeSubSlug:
				$curlRequestMethod = "DELETE";
				$flatSlugQstr = $pathParam;
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->beneficiarySlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE VIRTUAL CARDS ENDPOINT CALLS
		elseif($requestSlug == $this->virtualCardSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->listSubSlug:
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->virtualCardFundingSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->virtualCardFundingSubSlug;
				break;
				
				
				case $this->virtualCardWithdrawalSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->virtualCardWithdrawalSubSlug;
				break;
				
				
				case $this->virtualCardTerminateSubSlug:
				$curlRequestMethod = "PUT";
				$flatSlugQstr = $pathParam.'/'.$this->virtualCardTerminateSubSlug;
				break;
				
				
				case $this->virtualCardStatusSubSlug:
				$curlRequestMethod = "PUT";
				$flatSlugQstr = $pathParam.'/status/'.$pathParam2;
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->virtualCardSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		
		//HANDLE VIRTUAL ACCOUNT NUMBERS ENDPOINT CALLS
		elseif($requestSlug == $this->virtualAccNumSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->virtualAccNumBulkSubSlug:
				$apiRequestSlug = $this->virtualAccNumBulkSubSlug;
				
				switch($tabTabSubSlug){
					
					case $this->createSubSlug:
					$curlPostFields = $postRequestParams;
					break;
					
					case $this->fetchSubSlug:
					$curlRequestMethod = "GET";
					$flatSlugQstr = $pathParam;
					break;
					
				}
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->virtualAccNumSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		
		//HANDLE PLAN ENDPOINT CALLS
		elseif($requestSlug == $this->planSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->listSubSlug:
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->cancelSubSlug:
				$curlRequestMethod = "PUT";
				$flatSlugQstr = $pathParam.'/cancel';
				break;
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->planSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE SUBSCRIPTION ENDPOINT CALLS
		elseif($requestSlug == $this->subscriptionSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "PUT";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->listSubSlug:
				$curlRequestMethod = "GET";
				break;
				
				
				case $this->activateSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/activate';
				break;
				
				
				case $this->cancelSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/cancel';
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->subscriptionSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE SUBACCOUNTS ENDPOINT CALLS
		elseif($requestSlug == $this->subaccountSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->listSubSlug:
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->removeSubSlug:
				$curlRequestMethod = "DELETE";
				$flatSlugQstr = $pathParam;
				break;
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->subaccountSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
				
		
		//HANDLE BILL ENDPOINT CALLS
		elseif($requestSlug == $this->billSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$log2PaymentsAwaitingVerif = true;
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->listSubSlug:
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->validateSubSlug:
				$apiRequestSlug = 'bill-items';
				$flatSlugQstr = $pathParam.'/validate';
				break;
				
				
				case $this->billCategSubSlug:
				$apiRequestSlug = $this->billCategSubSlug;
				break;
				
				
				case $this->billBulkSubSlug:
				$log2PaymentsAwaitingVerif = true;
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				$apiRequestSlug = $this->billBulkSubSlug;
				break;
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->billSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		
		//HANDLE BILLER/REMITA PAYMENTS ENDPOINT CALLS
		elseif($requestSlug == $this->billerSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->listSubSlug:
				break;
				
				
				case $this->billerProductSubSlug:
				$flatSlugQstr = $pathParam.'/'.$this->billerProductSubSlug;
				break;
				
				
				case $this->billerProductOrderSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->billerProductSubSlug.'/'.$pathParam2.'/'.$this->billerProductOrderSubSlug;
				break;
				
				
				case $this->billerProductAmtSubSlug:
				$flatSlugQstr = $pathParam.'/'.$this->billerProductSubSlug.'/'.$pathParam2;
				break;
				
				
				case $this->billerProductOrderUpdateSubSlug:
				$curlRequestMethod = "PUT";
				$curlPostFields = $postRequestParams;
				$apiRequestSlug = 'product-orders';
				$flatSlugQstr = $pathParam;
				break;
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->billerSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
				
				
		//HANDLE SETTLEMENTS ENDPOINT CALLS
		elseif($requestSlug == $this->settlementSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->listSubSlug:
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam;
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->settlementSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		 
		
		//HANDLE BANKS ENDPOINT CALLS
		elseif($requestSlug == $this->bankSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->listSubSlug:
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->bankBranchSubSlug:
				$flatSlugQstr = $pathParam.'/'.$this->bankBranchSubSlug;
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->bankSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
			
				 
		//HANDLE BALANCES ENDPOINT CALLS
		elseif($requestSlug == $this->balanceSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->fetchSubSlug:
				break;
				
				
				case $this->balancePerCurrencySubSlug:
				$flatSlugQstr = $pathParam;
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->balanceSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
			
				 
		//HANDLE FOREIGN EXCHANGE RATES ENDPOINT CALLS
		elseif($requestSlug == $this->fxRateSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->fetchSubSlug:
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->fxRateSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
				 
		//HANDLE ACCOUNTS ENDPOINT CALLS
		elseif($requestSlug == $this->accountSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "POST";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->resolveSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = 'resolve';
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->accountSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
				 
		//HANDLE KYC ENDPOINT CALLS
		elseif($requestSlug == $this->kycSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->bvnSubSlug:
				
				switch($tabTabSubSlug){
					
					case $this->resolveSubSlug:
					$flatSlugQstr = $this->bvnSubSlug.'/'.$pathParam;
					break;
					
				}
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->kycSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		
				 
		//HANDLE CARD BIN ENDPOINT CALLS
		elseif($requestSlug == $this->cardBinSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->resolveSubSlug:
				$flatSlugQstr = $pathParam;
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->cardBinSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		 
				
		//HANDLE OTP ENDPOINT CALLS
		elseif($requestSlug == $this->otpSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "POST";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->validateSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/validate';
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->otpSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		 
				
		//HANDLE CHARGEBACKS ENDPOINT CALLS
		elseif($requestSlug == $this->chargebackSlug){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->listSubSlug:
				case $this->fetchSubSlug:
				break;
				
				
				case $this->chargebackAcknowledgementSubSlug:
				$flatSlugQstr = $pathParam;
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->chargebackSlug).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		

		
		
		 
				
		//HANDLE ENDPOINT DOCUMENTIONS CALLS
		elseif($requestSlug == $this->endpointDoc){
			
			$apiRequestSlug = $flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->listSubSlug:
				case $this->fetchSubSlug:
				break;
				
				default:
				$apiRequestSlug = 'meta';
				$flatSlugQstr = $pathParam;
				
			}
			
			$curlUrl = $this->API_BASE_URL.($apiRequestSlug? $apiRequestSlug : $this->endpointDoc).($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
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
			
			//For now, only a post with flutterwave signature header is allowed
			if(!$this->apiSignatureValidated($input)){

				//Respond to webhook calling endpoint with a unauthorized status code
				http_response_code(401);					
				exit();			


			}
				
			//Respond to webhook calling endpoint with a status code, we do this early enough to avoid timeout 
			http_response_code(200);
			
			//Parse the webhook body
			$webhookBody = json_decode($input);
			
			//Handle each webhook events
			switch($webhookBody->event){
				
				case 'charge.success':
					//Verify the transaction and give value if not already given
					$this->giveValue($webhookBody->data->id, $webhookBody->data->tx_ref, false);
					break;
				
			}
			
			//For webhook event we stop the script here
			exit(); //IMPORTANT
			
			
		}
		 
		 
		 
		 
		
		
		//POINT ENDPOINT CALLS TO APPROPRAITE API URL
		if(isset($curlUrl)){
			
			$curlHttpHeader = [
				"authorization: Bearer ".$this->API_SECRET_KEY,
				"content-type: application/json",
				"cache-control: no-cache"
			  ];
			  
			!isset($curlPostFields)? ($curlPostFields = '') : '';
			
			list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'method' => $curlRequestMethod, 'header' => $curlHttpHeader, 'postFields' => $curlPostFields, 'jsonData' => true, 'sameOrigin' => false));
			
			if($err){
				
				// there was an error contacting the Flutterwave API
				$alertUser = $this->errorAlertPrefix.'Error: '.$err.'</span>';
				//die('Curl returned error: '.$err);
				//print_r($alertUser);
				
			}else{

				$trx = $jsonDecodedResponse;
				
				//print_r($trx); // uncomment this line for debug
				
				if(strtolower($trx->status) != 'success'){
					
					// there was an error from the API
					$alertUser = $this->errorAlertPrefix.'Error: '.$trx->message.'</span>';
					//print_r('Error: '.$trx->message);
				  
				}

				else{
					
					$trxData = $trx->data;
					
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
						header('Location: '.$trxData->link);
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
	private function giveValue($gatewayTrxIdPassed, $trxReferencePassed, $returnToCaller = true){
	 
		/* 
			After Payment, we call the transaction verification endpoint to verify the status of the transaction
			and if successful, we proceed to give value.
			In giving value, we proceed as follows;
			Query for the transaction with the trxReferencePassed from our payments awaiting verification and if found;
			==> we confirm that the response status from the verification endpoint was a success
			==> match the following details from verification endpoint response against the ones queried from payments awaiting verification
				and if everything checks out, then give value accordingly.
				transaction amount, reference and currency.
			
		*/
		
		
		//Call verification endpoint
		
		$jsonDecodedResponse = $this->verifyTransaction(array('path_param' => $gatewayTrxIdPassed));
		
		$trxData = $jsonDecodedResponse->data;
		$trxStatus = $trxData->status;
		$trxAmount = $trxAmountFmtd = $this->rollbackNormalizedAmount($trxData->amount); //Remember to present back the real amount paid by user by stripping the padded lowest denomination digit
		$trxCurrency = $trxData->currency;
		$gatewayTrxId = $trxData->id;
		$trxReference = $trxData->tx_ref;
		$trxCustomerEmail = $trxData->customer->email;
		$trxCustomerId = $trxData->customer->id;
		$trxCard = isset($trxData->card)? $trxData->card : '';
		$trxCardToken = isset($trxCard->token)? $trxCard->token : '';
		$trxCardBin = isset($trxCard->first_6digits)? $trxCard->first_6digits : '';
		$trxCardLast4Digits = isset($trxCard->last_4digits)? $trxCard->last_4digits : '';
		$trxCardExpiryDate = isset($trxCard->expiry)? $trxCard->expiry : '';
		
		
		//Query from payment awaiting verification
		
		$awaitingValRow = $this->getPaymentAwaitingVerif($trxReferencePassed);
		$awaitingValTrxRef = $awaitingValRow["REFERENCE"];
		$awaitingValCustomerEmail = $awaitingValRow["CUSTOMER_EMAIL"];
		$awaitingValAmount = $awaitingValRow["VALUE_AMOUNT"];
		$awaitingValCurrency = $awaitingValRow["TRANX_CURRENCY"];
		$storeType = $awaitingValRow["STORE_NAME"];
		$savePayCard = $awaitingValRow["SAVE_CARD"];
		$U = $this->ACCOUNT->loadUser($awaitingValRow["USER_ID"]);
		
		$paymentReferenceIsLogged = $this->isPaymentReferenceLogged($U->getUserId(), $trxReference, $trxCustomerEmail);
		
		if(strtolower($trxStatus) == 'successful' && $awaitingValAmount == $trxAmount &&  
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




	/* Method for encrypting a transaction charge payload */
	public function encryptPayload($data){
	
		$encData = openssl_encrypt(json_encode($data), 'DES-EDE3', $this->API_ENC_KEY, OPENSSL_RAW_DATA);
	  
		$encData =  base64_encode($encData);
		
		return $encData; 

		
	}




	/* Method for building payment card signature */
	public function buildCardSignature($cardToken, $cardBin, $CardLast4Digits, $expiryDate){
	
		$data = $cardBin.$cardToken.$CardLast4Digits.'-'.$expiryDate;
		return 'SIG_'.hash('sha256', $data); 

	}



	/* Method for validating request api signature */
	private function apiSignatureValidated($data){
		
		$hashKey = 'HTTP_VERIF_HASH';
		
		if((strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') && isset($_SERVER[$hashKey])){
			
			foreach($this->API_DETAILS_ARR as $indexKey => $apiKey){
				
				return ($_SERVER[$hashKey] === $this->API_DETAILS_ARR['secret_hash']);
				
			}
			
		}
		
		return false;
		
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
	CHARGE API METHODS
	***********************************/

	/* Method for charging a debit or credit card */
	public function chargeCard($userParams = []){
		
		$defaultParams = [
		
			'amount' => '2300', //amount
			'currency' => 'NGN', //3 letter ISO currency 
			'card_number' => '4556052704172643',
			'cvv' => '899',
			'expiry_month' => '01',
			'expiry_year' => '21',
			'email' => 'xper@low.com',
			'tx_ref' => '',
			//optional
			'phone_number' => '',
			'preauthorize' => '', //boolean 
			'redirect_url' => '', 
			'client_ip' => '', 
			'device_fingerprint' => '', 
			'meta' => '', //{'flightID': , 'sideNote':} used for passing extra informations
			'authorization' => '', //{'mode': , 'pin':, 'city':, 'address':, 'state':, 'country':, 'zipcode':} card validation auth model

			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = $this->mergeParams(['type' => $this->cardChargeSubSlug]);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}



	/* Method for charging Nigerian bank accounts */
	public function chargeNgBankAccounts($userParams = []){
		
		$defaultParams = [
		
			'amount' => '', //amount 
			'account_bank' => '', //bank code
			'account_number' => '',
			'email' => '',
			'tx_ref' => '',
			//optional
			'currency' => '', //3 letter ISO currency
			'phone_number' => '',
			'fullname' => '', 
			'redirect_url' => '', 
			'client_ip' => '', 
			'device_fingerprint' => '', 
			'meta' => '', //{'flightID': , 'sideNote':} used for passing extra informations
			'bvn' => '', //This is the customer's BVN number (It is only required for UBA account payment option)
			'passcode' => '', //This is required for Zenith bank account payments, you are required to collect the customer's date of birth and pass it in this format DDMMYYYY as the passcode.

			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = $this->mergeParams(['type' => $this->ngBnkAccChargeSubSlug]);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	
	



	/* Method for charging UK bank accounts */
	public function chargeUkBankAccounts($userParams = []){
		
		$defaultParams = [
		
			'amount' => '', //amount 
			'account_bank' => '', //bank code
			'account_number' => '',
			'email' => '',
			'tx_ref' => '',
			//optional
			'currency' => '', //3 letter ISO currency
			'phone_number' => '',
			'fullname' => '', 
			'redirect_url' => '', 
			'client_ip' => '', 
			'device_fingerprint' => '', 
			'meta' => '', //{'flightID': , 'sideNote':} used for passing extra informations
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = $this->mergeParams(['type' => $this->ukBnkAccChargeSubSlug]);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	
	
	



	/* Method for charging via bank transfer */
	public function chargeViaBankTransfer($userParams = []){
		
		$defaultParams = [
		
			'amount' => '', //amount 
			'email' => '',
			'tx_ref' => '',
			'currency' => '', //3 letter ISO currency
			//optional
			'phone_number' => '',
			'fullname' => '', 
			'redirect_url' => '', 
			'client_ip' => '', 
			'device_fingerprint' => '', 
			'meta' => '', //{'flightID': , 'sideNote':} used for passing extra informations
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = $this->mergeParams(['type' => $this->bnkTransferChargeSubSlug]);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	
	
	



	/* Method for charging via Ghana Mobile Money */
	public function chargeViaGhanaMomo($userParams = []){
		
		$defaultParams = [
		
			'amount' => '', //amount 
			'email' => '',
			'tx_ref' => '',
			'currency' => '', //3 letter ISO currency
			'phone_number' => '', //This is the phone number linked to the customer's mobile money account
			'network' => '', //This is the customer's mobile money network provider (possible values: MTN, VODAFONE, TIGO)
			//optional
			'fullname' => '', 
			'redirect_url' => '', 
			'client_ip' => '', 
			'device_fingerprint' => '', 
			'meta' => '', //{'flightID': , 'sideNote':} used for passing extra informations
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = $this->mergeParams(['type' => $this->ghMomoChargeSubSlug]);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	
	



	/* Method for charging via Rwanda Mobile Money */
	public function chargeViaRwandaMomo($userParams = []){
		
		$defaultParams = [
		
			'amount' => '', //amount 
			'email' => '',
			'tx_ref' => '',
			'currency' => '', //3 letter ISO currency
			'order_id' => '', //Unique ref for the mobilemoney transaction to be provided by the merchant
			//optional
			'phone_number' => '', //This is the phone number linked to the customer's mobile money account
			'fullname' => '', 
			'redirect_url' => '', 
			'client_ip' => '', 
			'device_fingerprint' => '', 
			'meta' => '', //{'flightID': , 'sideNote':} used for passing extra informations
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = $this->mergeParams(['type' => $this->rwMomoChargeSubSlug]);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	
	



	/* Method for charging via Uganda Mobile Money */
	public function chargeViaUgandaMomo($userParams = []){
		
		$defaultParams = [
		
			'amount' => '', //amount 
			'email' => '',
			'tx_ref' => '',
			'currency' => '', //3 letter ISO currency
			//optional
			'order_id' => '', //Unique ref for the mobilemoney transaction to be provided by the merchant
			'phone_number' => '', //This is the phone number linked to the customer's mobile money account
			'network' => '', //This is the customer's mobile money network provider (possible values: MTN, VODAFONE, TIGO)
			'voucher' => '', //This is the voucher code generated by the customer. It is meant to be passed in the initial charge request. (only for Vodafone cash)
			'fullname' => '', 
			'redirect_url' => '', 
			'client_ip' => '', 
			'device_fingerprint' => '', 
			'meta' => '', //{'flightID': , 'sideNote':} used for passing extra informations
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = $this->mergeParams(['type' => $this->ugMomoChargeSubSlug]);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug.$qstr;
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	
	



	/* Method for charging via Zambia Mobile Money */
	public function chargeViaZambiaMomo($userParams = []){
		
		$defaultParams = [
		
			'amount' => '', //amount 
			'email' => '',
			'tx_ref' => '',
			'currency' => '', //3 letter ISO currency
			'network' => '', //This is the customer's mobile money network provider (possible values: MTN, VODAFONE, TIGO)
			//optional
			'order_id' => '', //Unique ref for the mobilemoney transaction to be provided by the merchant
			'phone_number' => '', //This is the phone number linked to the customer's mobile money account
			'fullname' => '', 
			'redirect_url' => '', 
			'client_ip' => '', 
			'device_fingerprint' => '', 
			'meta' => '', //{'flightID': , 'sideNote':} used for passing extra informations
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = $this->mergeParams(['type' => $this->zaMomoChargeSubSlug]);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	



	/* Method for charging via Ussd */
	public function chargeViaUssd($userParams = []){
		
		$defaultParams = [
		
			'amount' => '', //amount 
			'email' => '',
			'tx_ref' => '',
			'currency' => '', //3 letter ISO currency
			'account_bank' => '', //bank numeric code
			//optional
			'phone_number' => '', //This is the phone number linked to the customer's mobile money account
			'fullname' => '', 
			'redirect_url' => '', 
			'client_ip' => '', 
			'device_fingerprint' => '', 
			'meta' => '', //{'flightID': , 'sideNote':} used for passing extra informations
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = $this->mergeParams(['type' => $this->ussdChargeSubSlug]);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	



	/* Method for charging via Mpesa */
	public function chargeViaMpesa($userParams = []){
		
		$defaultParams = [
		
			'amount' => '', //amount 
			'email' => '',
			'tx_ref' => '',
			'currency' => '', //3 letter ISO currency
			'phone_number' => '', //This is the phone number linked to the customer's mobile money account
			//optional
			'fullname' => '', 
			'redirect_url' => '', 
			'client_ip' => '', 
			'device_fingerprint' => '', 
			'meta' => '', //{'flightID': , 'sideNote':} used for passing extra informations
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = $this->mergeParams(['type' => $this->mpesaChargeSubSlug]);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	



	/* Method for charging via Voucher Payment */
	public function chargeViaVoucherPay($userParams = []){
		
		$defaultParams = [
		
			'amount' => '', //amount 
			'email' => '',
			'tx_ref' => '',
			'currency' => '', //3 letter ISO currency
			'pin' => '', //This is the voucher pin given to the user after redemption at the agent location. They would provide this to you as the voucher code.
			//optional
			'phone_number' => '', //This is the phone number linked to the customer's mobile money account
			'country' => '', //Pass your country as US for US ACH payments and ZA for SA ACH payments
			'fullname' => '', 
			'redirect_url' => '', 
			'client_ip' => '', 
			'device_fingerprint' => '', 
			'meta' => '', //{'flightID': , 'sideNote':} used for passing extra informations
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = $this->mergeParams(['type' => $this->voucherPayChargeSubSlug]);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	



	/* Method for charging via Francophone Mobile Money */
	public function chargeViaFrancophoneMomo($userParams = []){
		
		$defaultParams = [
		
			'amount' => '', //amount 
			'email' => '',
			'tx_ref' => '',
			'currency' => '', //3 letter ISO currency
			'phone_number' => '', //This is the phone number linked to the customer's mobile money account
			//optional
			'country' => '', //This is the country code of the francophone country making the mobile money payment. Possible values are CM (Cameroon), SN (Senegal), BF (Burkina Faso) and so on.
			'fullname' => '', 
			'redirect_url' => '', 
			'client_ip' => '', 
			'device_fingerprint' => '', 
			'meta' => '', //{'flightID': , 'sideNote':} used for passing extra informations
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$qstr = $this->mergeParams(['type' => $this->francoMomoChargeSubSlug]);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug.$qstr;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	
	



	/* Method for validating a charge */
	public function validateCharge($userParams = []){
		
		$defaultParams = [
		
			'otp' => '',
			'flw_ref' => '', //This is the reference returned in the initiate charge call as flw_ref
			'type' => '', //This recognises the type of payment you want to validate. Set to account if you want to validate an account transaction and set to card for card transactions
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->chargeValidationSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	



	
	
	/**********************************
	TOKENIZED CHARGE API METHODS
	***********************************/


	/* Method for charging with token */
	public function chargeWithToken($userParams = []){
		
		$defaultParams = [
			
			/** BODY PARAMS **/
			'token' => '', //This is the card token returned from the transaction verification endpoint as data.card.token 
			'amount' => '', //amount 
			'email' => '',
			'tx_ref' => '',
			'currency' => '',
			'country' => '', //This is the country code of the francophone country making the mobile money payment. Possible values are CM (Cameroon), SN (Senegal), BF (Burkina Faso) and so on.
			//optional
			'first_name' => '',
			'last_name' => '', 
			'ip' => '', //IP - Internet Protocol. This represents the IP address of where the transaction is being carried out
			'narration' => '', //This is a custom description added by the merchant
			'device_fingerprint' => '', 
			'payment_plan' => '', //This is the ID of the plan you want to subscribe to. It is returned in the call to create a payment plan as data.id
			'subaccounts' => '', //This is an array of objects containing the subaccount IDs to split the payment into. Subaccount ID's are returned in the call to create a subaccount as data.subaccount_id
			'preauthorize' => '', //Pass this value as true to preauthorize a tokenized charge
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->tokenSlug.'/'.$this->tokenizedChargeSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->data;
		
		
	}
	
	


	/* Method for creating bulk tokenized charge */
	public function bulkTokenizedCharge($userParams = []){
		
		$defaultParams = [
		
			'retry_strategy' => '', //This is an object that defines what should happen when the transaction fails. It contains 3 properties (retry_interval, retry_amount_variable, retry_attempt_variable)
					'retry_interval' => '', //This is the number of mins it should take for the retry to happen
					'retry_amount_variable' => '', //This is the amount that would be retried after the specified number of attempts in percentage
					'retry_attempt_variable' => '', //This is the number of times the retry should happen
			'bulk_data' => '', //An array of objects containing the tokenized bulk charge data. This array contains the same payload you passed to the single tokenize endpoint with multiple different values.
					'currency' => '',
					'country' => '',
					'token' => '',
					'amount' => '',
					'tx_ref' => '',
					'email' => '',
					//optional
					'first_name' => '',
					'last_name' => '',
					'ip' => '',
			'title' => '' //Title of the bulk charge
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->tokenSlug.'/'.$this->tokenizedChargeBulkSubSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	
	
	
	
	/* Method for fetching bulk tokenized charge status */
	public function getBulkTokenizedChargeStatus($userParams){
		
		$defaultParams = [
		
			'path_param' => '' //bulk_id; This is the id returned in the bulk charge response
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->tokenSlug.'/'.$this->tokenizedChargeBulkSubSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}
	
	
	
	
	/* Method for fetching bulk tokenized charge transaction */
	public function getBulkTokenizedChargeTransaction($userParams){
		
		$defaultParams = [
		
			'path_param' => '' //bulk_id; The unique ìd of a bulk charge. It is returned as data.id` in the create bulk tokenized charge call
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->tokenSlug.'/'.$this->tokenizedChargeBulkSubSlug.'/'.$this->tokenizedChargeBulkTrxSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}



	/* Method for updating a token details */
	public function updateTokenDetails($userParams = []){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //token; This is the card token returned from the transaction verification endpoint as data.card.token
			
			/** BODY PARAMS **/
			'email' => '',
			'first_name' => '',
			'last_name' => '',
			'phone_number' => ''
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->tokenSlug.'/'.$this->updateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	

	
	
	/**********************************
	PREAUTHORIZATION API METHODS
	***********************************/
	


	/* 
		Method that allows a merchant to collect the preauthorized funds from the customer 
		i.e. after value or service has been given to the customer a merchant would capture 
		the preauthorized amount 
	*/
	public function capturePreauthCharge($userParams = []){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //flw_ref
			
			/** BODY PARAMS **/
			'amount' => ''
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->preauthChargeSlug.'/'.$this->preauthChargeCaptureSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	

	/* 
		Method that allows the merchant to release the hold on the funds 
		i.e. if value was not given for the service, the merchant would 
		typically be required to void the transaction
	*/
	public function voidPreauthCharge($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //flw_ref
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->preauthChargeSlug.'/'.$this->preauthChargeVoidSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	
	
	

	/* 
		Method that allows the merchant to refund a captured amount.
	*/
	public function refundPreauthCharge($userParams = []){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //flw_ref
			
			/** BODY PARAMS **/
			'amount' => ''
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->preauthChargeSlug.'/'.$this->preauthChargeRefundSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	
	
	
	
	
	
	
	/**********************************
	TRANSACTION API METHODS
	***********************************/
	
	
	/* Method for verifying transaction */
	public function verifyTransaction($userParams){
		
		$defaultParams = [
			
			/** PATH PARAMS **/
			'path_param' => '', //id; This is the transaction unique identifier. It is returned in the initiate transaction call as data.id
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->verifySubSlug.$qstr;
		
		return json_decode($this->SITE->file_get_contents($curlUrl));
		
	}


	
	/* Method for listing your integration transaction */
	public function getTransactionList($userParams = [], $retResponse = true){
		
		$defaultParams = [
			
			//optional filters
			'from' => '',
			'to' => '',
			'tx_ref' => '',
			'customer_email' => '',
			'customer_fullname' => '',
			'currency' => '',
			'page' => self::PAGE_ID, //This is the page number to retrieve e.g. setting 1 retrieves the first page
			'status' => '', //This is the transaction status, can be set to successful, failed etc to filter the listing
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if($retResponse)
			return $jsonDecodedResponse;
		
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}
	
	
	
	
	/* Method for fetching transaction fee */
	public function getTransactionFee($userParams = []){
		
		$defaultParams = [
			
			
			'amount' => '',
			'currency' => '',
			//optional filters
			'payment_type' => '', //This is an optional parameter to be used when getting the transaction fees for different payment types. The expected values are card, debit_ng_account, mobilemoney, bank_transfer, and ach_payment
			'card_first6digits' => '', //This can be used only when the user has entered first 6digits of their card number, it also helps determine international fees on the transaction if the card being used is an international card
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->trxFeeSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}



	/* 
		Method for resending a failed transaction webhook to your server
	*/
	public function resendTransactionHook($userParams = []){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; This is the transaction unique identifier. It is returned in the initiate transaction call as data.id
			
			/** BODY PARAMS **/
			//optional
			'wait' => '' // If this is passed the endpoint would hold for the hook response and return what you respond with as the response. The expected value is 1
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->trxHookResendSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return (isset($jsonDecodedResponse->status)? $jsonDecodedResponse->status : $rawResponse);
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	


	/* 
		Method that allows you to initiate a transaction refund. 
		This service is not publicly available, kindly contact support for it.
	*/
	public function refundTransaction($userParams = []){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; This is the transaction unique identifier. It is returned in the initiate transaction call as data.id
			
			/** BODY PARAMS **/
			//optional
			'amount' => '' // This is an optional parameter and should be sent if you would like to refund a partial amount.
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->trxRefundSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	
	
	
	/* Method for fetching a transaction timeline from your integration */
	public function getTransactionTimeline($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; This is the transaction unique identifier. It is returned in the initiate transaction call as data.id
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->trxTimelineSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}
	
	
	
	
	
	
	
	/**********************************
	TRANSFER API METHODS
	***********************************/
	


	/* 
		Method that allows you to initiate a transfer
	*/
	public function doTransfer($userParams = []){
		
		$defaultParams = [
		
			'account_bank' => '', //Recipient Bank Code
			'account_number' => '', //Recipient Account Number
			'amount' => '',
			'currency' => '', //This can be NGN, GHS, KES, UGX, TZS, USD
			'beneficiary_name' => '', //This is the name of the beneficiary.
			//optional
			'narration' => '', //This is the narration for the transfer e.g. payments for x services provided
			'destination_branch_code' => '', //This code uniquely identifies bank branches for disbursements into Ghana, Uganda and Tanzania. It is returned in the call to fetch bank branches. It is only REQUIRED for GHS, UGX and TZS bank transfers
			'beneficiary' => '', //This is the beneficiary's id. It allows you to initiate a transfer to an existing beneficiary. You can pass this in place of account_bank & account_number. It is returned in the call to fetch a beneficiary as data.id
			'reference' => '', //This is a merchant's unique reference for the transfer, it can be used to query for the status of the transfer.
			'callback_url' => '', //This is a url passed by you the developer, Flutterwave would pass the final transfer response to this callback url. You can use this in place of Webhooks
			'debit_currency' => '', //You can pass this when you want to debit a currency balance and send money in another currency.
			'meta' => '', //This is an object you can use to add any additional payment information you would like to associate with this transfer. Note that this object is REQUIRED for ZAR payouts.
					'first_name' => '', //Recipient
					'last_name' => '', //Recipient
					'email' => '', //Recipient
					'mobile_number' => '', //Recipient
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	


	/* 
		Method that allows you to initiate a bulk transfer
	*/
	public function doBulkTransfer($userParams = []){
		
		$defaultParams = [
		
			'bulk_data' => '', //An array of objects containing the transfer charge data. This array contains the same payload you would passed to create a single transfer with multiple different values.
					'bank_code' => '', //Recipient
					'account_number' => '', //Recipient
					'amount' => '',
					'currency' => '', //This can be NGN, GHS, KES, UGX, TZS, USD
					//optional
					'narration' => '',
					'reference' => '',
			//optional
			'title' => '', //Title of the bulk transfer
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->transferBulkSubSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	
	
	
	
	/* Method for fetching transfer fee */
	public function getTransferFee($userParams = []){
		
		$defaultParams = [
			
			
			'amount' => '',
			//optional filters
			'currency' => '',
			'type' => '', //This is the type of transfer you want to get the fee for. Usual values are mobilemoney or account
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->transferFeeSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}



	
	
	
	
	/* Method for listing your integration transfers */
	public function getTransferList($userParams = []){
		
		$defaultParams = [
			
			//optional filters
			'page' => self::PAGE_ID, //This is the page number to retrieve e.g. setting 1 retrieves the first page
			'status' => '', //This is the transaction status, can be set to successful, failed etc to filter the listing
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}

	
	
	
	
	/* Method for listing your integration bulk transfers */
	public function getBulkTransferList($userParams = []){
		
		$defaultParams = [
			
			'batch_id' => '', //This is the numeric ID of the bulk transfer you want to fetch. It is returned in the call to create a bulk transfer as data.id
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->transferBulkSubSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}



	


	/* Method for fetching a transfer */
	public function fetchTransfer($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id; This is the numeric ID of the transfer you want to fetch. It is returned in the call to create a transfer as data.id
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	


	/* Method for fetching transfer rate */
	public function fetchTransferRate($userParams = []){
		
		$defaultParams = [
		
			'amount' => '',
			'destination_currency' => '', //This is the wallet / currency you are making a transfer to.
			'source_currency' => '', //This is the wallet / currency to be debited for the transfer
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->transferRateSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	
	
	



	/**********************************
	BENEFICIARY API METHODS
	***********************************/
	
	
	/* Method for creating a transfer beneficiary */
	public function createTransferBeneficiary($userParams){
		
		$defaultParams = [
		
			'account_number' => '', //beneficiary
			'account_bank' => '', //beneficiary bank code
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->beneficiarySlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	
	
	
	
	/* Method for listing your integration transfer beneficiaries */
	public function getTransferBeneficiaryList($userParams = []){
		
		$defaultParams = [
			
			//optional filters
			'page' => self::PAGE_ID, //This is the page number to retrieve e.g. setting 1 retrieves the first page
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->beneficiarySlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}



	


	/* Method for fetching a transfer beneficiary */
	public function fetchTransferBeneficiary($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id; This is the numeric ID of the transfer beneficiary you want to fetch. It is returned in the call to create a transfer as data.id
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->beneficiarySlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	

	


	/* Method for removing a transfer beneficiary */
	public function removeTransferBeneficiary($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id; This is the numeric ID of the transfer beneficiary you want to remove. It is returned in the call to create a transfer as data.id
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->beneficiarySlug.'/'.$this->removeSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	


	
	



	/**********************************
	VIRTUAL CARDS API METHODS
	***********************************/
	


	
	/* 
		Method for creating a virtual card 
		NOTE: We charge a card issuance fee of $0.5 for every new virtual card creation on Flutterwave
	
	*/
	public function createVirtualCard($userParams){
		
		$defaultParams = [
		
			'currency' => '',
			'amount' => '',
			'billing_name' => '', //This is the name that will appear on the card
			//optional
			'billing_address' => '', //This is the registered address for the card. e.g. Your house address where you would receive your card statements
			'billing_city' => '', //This is the City / District / Suburb / Town / Village registered for the card
			'billing_state' => '', //This is the State / County / Province / Region. It is a two letter word representing the state in the billing country e.g CA, NY
			'billing_country' => '', //Billing address country code, if provided. (e.g. "NG", "US")
			'billing_postal_code' => '', //Zip or postal code
			'callback_url' => '', //This is a callback endpoint you provide where we send details about any transaction that occurs on the card.
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->virtualCardSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for funding a virtual card */
	public function fundVirtualCard($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; This is the unique id of the particular card of interest. You can get this id from the call to create a virtual card or list virtual cards as data.id
			
			/** BODY PARAMS **/
			'amount' => '',
			'debit_currency' => '', //Use this if you want to debit a different balance on Flutterwave to fund your card e.g. you are funding a USD card but you want to debit your NGN balance to fund the card
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->virtualCardSlug.'/'.$this->virtualCardFundingSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for withdrawing from a virtual card */
	public function withdrawFromVirtualCard($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; This is the unique id of the particular card of interest. You can get this id from the call to create a virtual card or list virtual cards as data.id
			
			/** BODY PARAMS **/
			'amount' => '',
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->virtualCardSlug.'/'.$this->virtualCardWithdrawalSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for terminating a virtual card */
	public function terminateVirtualCard($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id; This is the unique id of the particular card of interest. You can get this id from the call to create a virtual card or list virtual cards as data.id
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->virtualCardSlug.'/'.$this->virtualCardTerminateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for blocking/unblocking a virtual card */
	public function manageVirtualCardStatus($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id; This is the unique id of the particular card of interest. You can get this id from the call to create a virtual card or list virtual cards as data.id
			'path_param2' => '', //status_action; This is the action you want to perform on the virtual card. Can be block or unblock
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->virtualCardSlug.'/'.$this->virtualCardStatusSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


		
	
	
	
	/* Method for listing your integration virtual cards */
	public function getVirtualCardList($userParams = []){
		
		$defaultParams = [
			
			//optional filters
			'page' => self::PAGE_ID, //This is the page number to retrieve e.g. setting 1 retrieves the first page
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->virtualCardSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}



	


	/* Method for fetching a virtual card */
	public function fetchVirtualCard($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id; This is the unique id of the particular card of interest. You can get this id from the call to create a virtual card or list virtual cards as data.id
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->virtualCardSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	


	/* Method for fetching a virtual card transaction */
	public function fetchVirtualCardTransaction($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; This is the unique id of the particular card of interest. You can get this id from the call to create a virtual card or list virtual cards as data.id
			
			/** BODY PARAMS **/
			'from' => '',
			'to' => '',
			'index' => '', //Pass 0 if you want to start from the beginning
			'size' => self::PER_PAGE, //Specify how many transactions you want to retrieve in a single call
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->virtualCardSlug.'/'.$this->virtualCardTrxSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	



	/**********************************
	VIRTUAL ACCOUNT NUMBERS API METHODS
	***********************************/
	

	

	
	/* Method for creating a virtual account number */
	public function createVirtualAccountNumber($userParams){
		
		$defaultParams = [
		
			'email' => '',
			'amount' => '',
			//optional
			'tx_ref' => '',
			'frequency' => '', //This is the number of times a generated account number can receive payments
			'duration' => '', //This is represented in days e.g. Passing 2 means 2 days. It is the expiry date for the account number.
			'is_permanent' => '', //Boolean: This allows you create a static account number i.e. it doesn't expire
			'narration' => '', //This allows you specify the name shown when the account is resolved
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->virtualAccNumSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}

	
	
	/* Method for creating bulk virtual account number */
	public function createBulkVirtualAccountNumber($userParams){
		
		$defaultParams = [
		
			//optional
			'accounts' => '', //This is the number of virtual account numbers you want to generate
			'email' => '',
			'amount' => '',
			'tx_ref' => '',
			'frequency' => '', //This is the number of times a generated account number can receive payments
			'duration' => '', //This is represented in days e.g. Passing 2 means 2 days. It is the expiry date for the account number.
			'is_permanent' => '', //Boolean: This allows you create a static account number i.e. it doesn't expire
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->virtualAccNumBulkSubSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	


	/* Method for fetching a virtual account number */
	public function fetchVirtualAccountNumber($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //order_ref; This is the order reference returned in the virtual account number creation
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->virtualAccNumSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}





	/* Method for fetching bulk virtual account number */
	public function fetchBulkVirtualAccountNumber($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //batch_id; This is the batch ID returned in the bulk virtual account numbers creation
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->virtualAccNumBulkSubSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	
	
	



	/**********************************
	PAYMENT PLANS API METHODS
	***********************************/

	
	
	/* Method for creating a payment plan */
	public function createPaymentPlan($userParams){
		
		$defaultParams = [
		
			'amount' => '', //This is the amount to charge all customers subscribed to this plan
			'name' => '', //This is the name of the payment plan, it will appear on the subscription reminder emails
			'interval' => '', //This will determine the frequency of the charges for this plan. Could be yearly, quarterly, monthly, weekly, daily, etc.
			//optional
			'duration' => '', //This is the frequency, it is numeric, e.g. if set to 5 and intervals is set to monthly you would be charged 5 months, and then the subscription stops
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->planSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	


	/* Method for listing payment plans on your integration */
	public function getPaymentPlanList($userParams){
		
		$defaultParams = [
		
			'from' => '',
			'to' => '',
			'page' => '',
			'amount' => '',
			'currency' => '',
			'interval' => '',
			'status' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->planSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}


	


	/* Method for fetching a payment plan */
	public function fetchPaymentPlan($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id; This is the unique id of the payment plan you want to fetch. It is returned in the call to create a payment plan as data.id
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->planSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}




	/* Method for updating a payment plan */
	public function updatePaymentPlan($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; This is the unique id of the plan of interest. You can get this id from the call to create a virtual card or list virtual cards as data.id
			
			/** BODY PARAMS **/
			'name' => '', //The new name of the payment plan
			'status' => '', //The new status of the payment plan
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->planSlug.'/'.$this->updateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for canceling a payment plan */
	public function cancelPaymentPlan($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; This is the unique id of the plan of interest. You can get this id from the call to create a virtual card or list virtual cards as data.id
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->planSlug.'/'.$this->cancelSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	
	



	/**********************************
	SUBSRIPTIONS API METHODS
	***********************************/



	/* Method for listing subscriptions on your integration */
	public function getSubscriptionList($userParams){
		
		$defaultParams = [
			
			//optional
			'email' => '',
			'transaction_id' => '', //This is the id of the transaction. It is returned in the call to verify a transaction as data.id
			'plan' => '', //This is the ID of the payment plan. It is returned in the call to create a payment plan as data.id
			'subscribed_from' => '', //This is the params to filter from the start date of the subscriptions
			'subscribed_to' => '', //This is the params to filter to the end date of the subscriptions
			'next_due_from' => '', //This is the params to filter from the start date of the next due subscriptions
			'next_due_to' => '', //This is the params to filter to the end date of the next due subscriptions
			'page' => '', //This is the page number to retrieve e.g. setting 1 retrieves the first page
			'status' => '', //This is the params used to filter the list of subscription based on the status which can be either active or cancelled
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subscriptionSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}


	


	/* Method for activating a subscription */
	public function activateSubscription($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; This is the unique id of the subscription of interest. You can get this id from the call to create a virtual card or list virtual cards as data.id
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subscriptionSlug.'/'.$this->activateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	


	/* Method for canceling a subscription */
	public function cancelSubscription($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; This is the unique id of the subscription of interest. You can get this id from the call to create a virtual card or list virtual cards as data.id
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subscriptionSlug.'/'.$this->cancelSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	
	



	/**********************************
	SUBACCOUNTS API METHODS
	***********************************/
	
	
	/* Method for creating a subaccount */
	public function createSubaccount($userParams){
		
		$defaultParams = [
		
			'account_number' => '', //customer
			'account_bank' => '', //bank code
			'business_name' => '', //This is the sub-account business name.
			'country' => '', //This is the ISO country code of the merchant e.g. NG, GH, KE etc.
			'split_value' => '', //This can be a percentage value or flat value depending on what was set on split_type.Note that the % value is in decimal. So 50% is 0.5 and so on.
			'business_mobile' => '', //Primary business contact number
			//optional
			'business_email' => '', //This is the sub-account business email
			'business_contact' => '', //This is the contact person for the sub-account e.g. Richard Hendrix
			'business_contact_mobile' => '', //Business contact number
			'split_type' => '', //This can be set as percentage or flat
			'meta' => '', //This is an array that allows you pass more information about the sub-account. Click the Add button below to see sample properties
					'meta_name' => '', //This allows you pass extra information (key) about the sub-account.
					'meta_value' => '', //This allows you pass extra information (value of meta_name) about the sub-account.
			
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
			
			//optional filters
			'account_bank' => '', //This is the sub-accounts bank ISO code
			'account_number' => '', //This is the account number associated with the subaccount you want to fetch
			'bank_name' => '', //This is the name of the bank associated with the ISO code provided in account_bank field
			'page' => self::PAGE_ID,
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subaccountSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}



	


	/* Method for fetching a subaccount */
	public function fetchSubaccount($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id; This is the unique id of the sub account of interest. It is returned in the call to create a sub account as data.id
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subaccountSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	


	/* Method for updating a subaccount */
	public function updateSubaccount($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; This is the unique id of the subaccount of interest. You can get this id from the call to create a virtual card or list virtual cards as data.id
			
			/** BODY PARAMS **/
			//optional
			'business_name' => '', //This is the sub-account business name
			'business_email' => '', //This is the sub-account business email
			'split_type' => '', //This can be set as percentage or flat
			'split_value' => '', //This can be a percentage value or flat value depending on what was set on split_type
			'account_number' => '', //This is the customer's account number
			
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
		
			'path_param' => '', //id; This is the numeric ID of the subaccount of interest. It is returned in the call to create a transfer as data.id
			
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
	BILLS API METHODS
	***********************************/


	/* Method for fetching a bill category */
	public function getBillCategory($userParams){
		
		$defaultParams = [
		
			'airtime' => '', //Get all AIRTIME bill categories on Flutterwave
			'data_bundle' => '', //Get all data bundle bill categories on Flutterwave
			'power' => '', //Get all power (electricity) bill categories on Flutterwave
			'internet' => '', //Get all internet (Wifi) bill categories on Flutterwave
			'toll' => '', //Get all toll ( toll gate) bill categories on Flutterwave
			'biller_code' => '', //Get all bill categories under a specific biller code
			'cables' => '', //Get all bill categories for cables (DSTV, GoTv etc)
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->billSlug.'/'.$this->billCategSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	
	
	
	
	/* Method for listing your integration subaccounts */
	public function getBillPaymentList($userParams = []){
		
		$defaultParams = [
			
			'from' => '',
			'to' => '',
			//optional filters
			'page' => '',
			'reference' => '', //This is the customer ID, pass this if you want to retrieve bill history for a particular customer ID
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->billSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}




	

	/* Method for fetching the status of a bill payment */
	public function getBillPaymentStatus($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //reference; This is the tx_ref of the bill transaction
			
			/** BODY PARAMS **/
			//optional
			'verbose' => '', //This is an optional parameter you can pass to get more details about the bill. E.g: the status of the bill payment is added to the response when this parameter is passed
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->billSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}

	


	/* Method for validating a bill service */
	public function validateBillService($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //item_code; This is the item_code returned from bills categories endpoint
			
			/** BODY PARAMS **/
			'code' => '', //This is the biller code for the service
			'customer' => '', //This is the customer identifier For airtime, the value must be the customer's phone number. For DSTV, it must be the customer's smartcard number
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->billSlug.'/'.$this->validateSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	
	/* Method for creating a bill payment */
	public function createBillPayment($userParams){
		
		$defaultParams = [
		
			'country' => '', //This is the country attached to the service being bought e.g. if service is Airtime and country is NG it means you are buying airtime in Nigeria.
			'customer' => '', //This is the customer identifier. For airtime, the value must be the customer's phone number. For DSTV, it must be the customer's smartcard number
			'amount' => '', //This is the amount for the service you would like to buy.
			'type' => '', //Pass the following possible values based on the service being bought: AIRTIME, DSTV, DSTV BOX OFFICE. Note that these values are case sensitive. If you are unsure what value to pass here, call our [Bill categories}
			//optional
			'recurrence' => '', //This determines if you are buying a service recurrently or not. ONCE - This is a one time payment, HOURLY - This is an hourly payment, DAILY - This is a daily payment, WEEKLY - This is a weekly payment, MONTHLY - This is a monthly payment. It defaults to ONCE when the value is not provided
			'reference' => '', //This is a unique reference passed by the developer to identify transactions on their end
			'biller_name' => '', //This is the particular biller you're paying to. Only pass this value for Ghana Airtime bills
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->billSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	
	
	
	
	/* Method for creating bulk bill payment */
	public function createBulkBillPayment($userParams){
		
		$defaultParams = [
		
			'bulk_reference' => '', //This is a reference that identifies the batch request being made for bulk requests
			'callback_url' => '', //This is an endpoint supplied by you the developer/merchant so we can send a response when each request in the bulk is completed
			'bulk_data' => '', //This is an array containing each individual requests in the batch.
					'bank_code' => '', //This is the recipients bank code
					'account_number' => '', //This is the recipient account number
					'amount' => '', //This is the amount for the service you would like to buy.
					'currency' => '', //This is the debit currency for the transfer. It can be NGN, GHS, KES, UGX, TZS, or USD
					//optional
					'narration' => '', //This is the narration for the transfer e.g. payments for x services provided
					'reference' => '', //This is a merchant's unique reference for the transfer, it can be used to query for the status of the transfer
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->billSlug.'/'.$this->billBulkSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	
	
	






	/**********************************
	REMITA PAYMENTS API METHODS
	***********************************/


	/* Method for listing bill payment agencies */
	public function getBillerList($userParams){
		
		$defaultParams = [
		
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->billerSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	


	/* Method for fetching bill payment agency products */
	public function getBillerProducts($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //biller_code; This is the biller's code
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->billerSlug.'/'.$this->billerProductSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	


	/* Method for fetching bill payment agency products amount */
	public function getBillerProductAmount($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //biller_code; This is the biller's code
			'path_param2' => '', //product_code; This is the item_code for the particular product
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->billerSlug.'/'.$this->billerProductAmtSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	
	
	/* Method for creating a biller product order */
	public function createBillerProductOrder($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //biller_code; This is the biller's code
			'path_param2' => '', //product_code; This is the id of the product
			
			/** BODY PARAMS **/
			'customer' => '', //This is an object with the name, email and phone number of the customer
					'name' => '', //customer
					'email' => '', //customer
					'phone_number' => '', //customer
					'product_code' => '', //This is the id of the product
			'fields' => '', //This is an array of the id, quatity and value of the order
					//optional
					'id' => '', //This is the order id
					'quantity' => '', //This is the quantity of the order
					'value' => '', //This is the value of the order
			//optional
			'country' => '', //This is the country attached to the service being bought e.g. if Service is Airtime and country is NG it means you are buying airtime in Nigeria
			'amount' => '',
			'reference' => '', //This is a unique reference passed by the developer to identify transactions on their end.
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->billerSlug.'/'.$this->billerProductOrderSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	
	
	
	


	/* Method for updating a biller product order */
	public function updateBillerProductOrder($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //order_id; This is the order id of the order you first created using createBillerProductOrder method
			
			/** BODY PARAMS **/
			'amount' => '', //This is the amount you want to update the order with
			//optional
			'reference' => '', //This is your unique reference for this order

		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->billerSlug.'/'.$this->billerProductOrderUpdateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	

	






	/**********************************
	BANKS API METHODS
	***********************************/
	
	

	/* 
		Method for listing bank names and codesBanks for MOMO

		When making MOMO transfers, you can use these values for the account_bank
		prop in the request payload:

		GH: AIRTEL, VODAFONE, TIGO, MTN

		KE: MPS

		UG: MPS

		ZM: MPS

		RW: MPS 
	
	*/
	
	public function getBankList($userParams, $fieldName = 'pay_api_bank_list'){
		
		$defaultParams = [
		
			'path_param' => '', //country; Pass either NG, GH, KE, UG, ZA or TZ to get list of banks in Nigeria, Ghana, Kenya, Uganda, South Africa or Tanzania respectively
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bankSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		return $this->buildSelectOptions($jsonDecodedResponse, ['fieldName' => $fieldName, 'meta' => 'merge_name_code']);
		
		
	}
	
	
	
	

	/* Method for listing bank branches */
	public function getBankBranchList($userParams, $fieldName = 'pay_api_bank_branch_list'){
		
		$defaultParams = [
		
			'path_param' => '', //id; Unique bank ID, it is returned in the call to fetch banks
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bankSlug.'/'.$this->bankBranchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		return $this->buildSelectOptions($jsonDecodedResponse, ['fieldName' => $fieldName, 'nameKey' => 'branch_name', 'codeKey' => 'branch_code', 'meta' => 'merge_name_code']);
		
		
	}
	
	
	


	


	






	/**********************************
	SETTLEMENTS API METHODS
	***********************************/
	
	
	
	/* Method for listing your integration settlements */
	public function getSettlementList($userParams = []){
		
		$defaultParams = [
			
			//optional filters
			'page' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->settlementSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}




	

	/* Method for fetching a settlement */
	public function fetchSettlement($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; This is a unique identifier for the particular settlement you want to fetch. It is returned in the call to list all settlements as data.id
			
			/** BODY PARAMS **/
			//optional
			'from' => '',
			'to' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->settlementSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}

	
	






	/**********************************
	OTPS API METHODS
	***********************************/
	
	
	
	/* Method for creating a one time password (OTP) */
	public function createOtp($userParams){
		
		$defaultParams = [
		
			'length' => '', //This is Integer length you want for the OTP.
			'customer' => '', //This is an object with the name, email and phone number of the customer.
			'sender' => '', //This is your merchant/business name. It would display when the OTP is sent
			'send' => '', //Boolean: Set to true to send otp to customer
			'medium' => '', //Pass the medium you want your customers to receive the OTP on. Possible values are sms, email and whatsapp you can pass more than one medium in the array"
			//optional
			'expiry' => '', //Pass an integer value represented in minutes for how long you want the OTP to live for before expiring
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->otpSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	
	
	/* Method for validating a one time pin (OTP) */
	public function validateOtp($userParams){
		
		$defaultParams = [
		
			'reference' => '', //This is the reference that was returned in the create OTP response
			'otp' => '', //This is the One time Pin sent to the user. You are meant to collect this from the user for validation
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->otpSlug.'/'.$this->validateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}









	/**********************************
	CHARGEBACKS API METHODS
	***********************************/
	

	
	
	/* Method for listing your integration chargebacks */
	public function getChargebackList($userParams = [], $retResponse = true){
		
		$defaultParams = [
			
			//optional filters
			'page' => '',
			'status' => '', //This specifies the status of the chargebacks you want to fetch. It can be lost, won, initiated, accepted, declined .
			'from' => '',
			'to' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargebackSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if($retResponse)
			return $jsonDecodedResponse;
		
		
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}




	

	/* Method for fetching a chargeback */
	public function fetchChargeback($userParams){
		
		$defaultParams = [
		
			'flw_ref' => '' //This is the flutterwave reference associated with a particular charge back. Pass this value when you want to fetch a single chargeback
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargebackSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}

	
	


	

	/* Method for Accepting/Declining a chargeback */
	public function manageChargebackAcknowledgement($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //id; Unique identifier for the chargeback you want to accept/decline
			
			/** BODY PARAMS **/
			'action' => '', //This is the action you want to perform on the chargeback. It can be accept or decline
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargebackSlug.'/'.$this->chargebackAcknowledgementSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}

	
	





	/**********************************
	MISC API METHODS
	***********************************/
	


	/* Method for fetching wallet balances */
	public function getWalletBalance($userParams){
		
		$defaultParams = [
		
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->balanceSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}


	

	/* Method for fetching wallet balances per currency */
	public function getWalletBalancePerCurrency($userParams){
		
		$defaultParams = [
		
			/** PATH PARAMS **/
			'path_param' => '', //currency; Target wallet currency to fetch balance for
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->balanceSlug.'/'.$this->balancePerCurrencySubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}

	
	

	/* Method for resolving account details */
	public function resolveAccountDetails($userParams){
		
		$defaultParams = [
		
			'account_number' => '', //account number
			'account_bank' => '', //bank code
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->accountSlug.'/'.$this->resolveSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}


	/* Method for resolving BVN details */
	public function resolveBvnDetails($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //bvn; This is a valid BVN number you want to resolve
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->kycSlug.'/'.$this->bvnSubSlug.'/'.$this->resolveSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	

	/* Method for resolving card bins */
	public function resolveCardBin($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //bin; The first 6 six digits on a debit/credit card
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->cardBinSlug.'/'.$this->resolveSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}

	


	

	/* Method for fetching foreign exchange rates */
	public function getFxRates($userParams){
		
		$defaultParams = [
		
			'from' => '', //This is the currency to convert from
			'to' => '', //This is the currency to convert to
			'amount' => '', //This is the amount to convert
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->fxRateSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}

	


	
	
	/**********************************
	ENDPOINT DOCUMENTIONS API METHODS
	***********************************/
	
	


	/* Method for fetching endpoint documentation */
	private function getEndpointDoc($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //endpoint_slug; This is the endpoint url slug
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->endpointDoc.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}

	

	


	
	
	/**********************************
	MISC METHODS
	***********************************/
	
	

	/* Daemon method for resending unverified transaction webhook */
	public function TrxHookResendDaemon(){
	
		$limit = $this->DBM->getMaxRowPerSelect();
		
		for($i = 0 ; ; $i += $limit){
			
			///PDO QUERY////////
			
			$sql = "SELECT REFERENCE FROM transactions_awaiting_verification WHERE (GATEWAY_NAME = ? AND ((DATE + INTERVAL 20 MINUTE) < NOW())) LIMIT ".$i.",".$limit;
			$valArr = array($this->paymentGatewayType);
			$stmt = $this->DBM->doSecuredQuery($sql, $valArr, true);
		
			/////IMPORTANT INFINITE LOOP CONTROL ////
			if(!$this->DBM->getSelectCount())
				break;
				
			while($row = $this->DBM->fetchRow($stmt)){

				$jsonDecodedResponse = $this->getTransactionList(['tx_ref' => $row["REFERENCE"]]);
				
				if(isset($jsonDecodedResponse->data[0]->id))
					$this->resendTransactionHook(['path_param' => $jsonDecodedResponse->data[0]->id]);
					
					
			}
			
		}
		
	}
	

	
	//JAVASCRIPT POPUP IMPLEMENTATION
	/*
	<script>
	  function makePayment() {
		FlutterwaveCheckout({
		  public_key: "FLWPUBK_TEST-31d61a13026483fc38f15f0e90232374-X",
		  tx_ref: "hooli-tx-1920bbtyt",
		  amount: 54600,
		  currency: "NGN",
		  country: "NG",
		  payment_options: "card,mobilemoney,ussd",
		  customer: {
			email: "user@gmail.com",
			phone_number: "08102909304",
			name: "yemi desola",
		  },
		  callback: function (data) { // specified callback function
			console.log(data);
		  },
		  customizations: {
			title: "My store",
			description: "Payment for items in cart",
			logo: "https://assets.piedpiper.com/logo.png",
		  },
		});
	  }
	</script>
  */
	


}





?>