<?php



class PaystackPaymentGateway extends PaymentGateway{
	
	/*** Generic member variables ***/
		
	private $gatewayName = 'paystack';
	private $API_BASE_URL = 'https://api.paystack.co/';
	
	//Replace this with your own live and test secret key
	private $API_DETAILS_ARR = [
	
		'test_sk' => PSTK_API_KEYS['test_sk'], 
		'test_pk' => PSTK_API_KEYS['test_pk'], 
		
		'live_sk' => PSTK_API_KEYS['live_sk'],
		'live_pk' => PSTK_API_KEYS['live_pk'],
		
	];
	
	private $API_SECRET_KEY;	
	
	private $truecallerResponseSlug = 'truecaller-response';
	
	private $trxSlug = 'transaction';
	private $trxPartialDebitSubSlug = 'partial_debit';
	private $trxTotalsSubSlug = 'totals';
	private $trxTimelineSubSlug = 'timeline';
	private $exportTrxSubSlug = 'export';
	private $checkAuthorizationSubSlug = 'check_authorization';
	
	
	private $transactionSplitSlug = 'split';
	private $transactionSubaccountSubSlug = 'subaccount';
	
	
	private $chargeSlug = 'charge';
	private $submitPinSubSlug = 'submit_pin';
	private $submitOtpSubSlug = 'submit_otp';
	private $submitPhoneSubSlug = 'submit_phone';
	private $submitBirthdaySubSlug = 'submit_birthday';
	private $submitAddressSubSlug = 'submit_address';
	
	
	private $bulkChargeSlug = 'bulkcharge';
	private $batchChargesSubSlug = 'charges';
	
	
	private $balanceSlug = 'balance';
	
	
	private $transferSlug = 'transfer';
	private $transferFinalizeSubSlug = 'finalize_transfer';
	private $resendOtpSubSlug = 'resend_otp';
	private $enableOtpSubSlug = 'enable_otp';
	private $disableOtpSubSlug = 'disable_otp';
	private $disableOtpFinalSubSlug = 'disable_otp_finalize';
	
	
	private $transferRecipientSlug = 'transferrecipient';
	
	
	private $settlementSlug = 'settlement';
	private $settlementSpecificSubSlug = 'transactions';
	
	private $invoiceSlug = 'paymentrequest';
	private $invoiceNotifySubSlug = 'notify';
	private $invoiceTotalsSubSlug = 'totals';
	private $invoiceFinalizeSubSlug = 'finalize';
	private $invoiceArchiveSubSlug = 'archive';
	
	private $paymentPageSlug = 'page';
	private $paymentPageProductSubSlug = 'product';
	private $paymentPageSlugCheckSubSlug = 'check_slug_availability';
	
	private $productSlug = 'product';
	
	
	private $cpanelSlug = 'integration';
	private $paymentSessTimeoutSubSlug = 'payment_session_timeout';
	
	
	private $subaccountSlug = 'subaccount';
	
	
	private $bankSlug = 'bank';
	private $bankListSubSlug = 'bank-list';
	private $bankListNubanSubSlug = 'bank-list-nuban';
	private $bankAccountNumberResolveSubSlug = 'resolve';
	
	
	private $verificationsSlug = 'verifications';
	private $phoneVerificationSubSlug = 'phone';
	
	
	private $bvnSlug = 'bvn';
	private $bvnResolveSubSlug = 'resolve_bvn';
	private $bvnMatchSubSlug = 'match';
	
	
	private $apiDecisionSlug = 'decision';
	private $cardBinSubSlug = 'bin';
	
	
	private $addressVerificationSlug = 'address_verification';
	private $countryListSlug = 'country_list';
	private $stateVerificationSubSlug = 'states';
	
	
	private $customerSlug = 'customer';
	private $customerValidationSubSlug = 'identification';
	private $customerRiskActionSubSlug = 'set_risk_action';
	private $deactivateAuthorizationSubSlug = 'deactivate_authorization';
	
	
	private $dedicatedAccountSlug = 'dedicated_account';
	
	
	private $planSlug = 'plan';
	
	
	private $subscriptionSlug = 'subscription';
	
	
	private $refundSlug = 'refund';
	
	
	private $disputeSlug = 'dispute';
	private $disputeSpecificSubSlug = 'transaction';
	private $disputeEvidenceSubSlug = 'evidence';
	private $disputeUploadUrlSubSlug = 'upload_url';
	private $disputeResolveSubSlug = 'resolve';
	private $disputeExportSubSlug = 'export';
	
	
	
	
	/*** Constructor ***/
	public function __construct($trxCustomizations = 'title::Store,desc::Service Payment,logo::', $minAcceptablePayAmount = 0){
		
		$this->API_SECRET_KEY = (API_TEST_MODE? $this->API_DETAILS_ARR['test_sk'] : $this->API_DETAILS_ARR['live_sk']);		

		parent::__construct($this->gatewayName, $this->trxSlug.'/'.$this->initializeSubSlug, $trxCustomizations, $minAcceptablePayAmount);
		
	}
	
	
	
	/*** Destructor ***/
	public function __destruct(){
		
		
	}

	
	
	/************************************************************************************/
	/************************************************************************************
									SITE METHODS
	/************************************************************************************
	/************************************************************************************/
		
		

	/* Method for linking paystack inline popup JS  */
	private function linkPopupJs($alertUser=''){
		
		return '<script type="text/javascript" src="https://js.paystack.co/v1/inline.js"></script>';
		
	}




	/* Method for processing payment transaction request */
	public function handlePaymentGatewayRequest(){
		
		global $siteDomain, $pageSelf, $rdr;
		
		
		/***************************BEGIN URL CONTROLLER****************************/
		
		$path = $this->ENGINE->get_page_path('page_url', '', true, true);

		$pagePathArr = explode('/', $path);
				
		if(isset($pagePathArr[1]) && 
			in_array(($requestSlug = strtolower($pagePathArr[1])), 
				array(
					$this->truecallerResponseSlug, $this->cancelTrxSlug, $this->verifyTrxGiveValSlug, $this->webhookGatewaySlug, $this->bvnSlug, $this->verificationsSlug,
					$this->trxSlug, $this->transactionSplitSlug, $this->forgetSavedPayCardSlug, $this->bankSlug, $this->countryListSlug, 
					$this->addressVerificationSlug, $this->apiDecisionSlug, $this->customerSlug, $this->dedicatedAccountSlug, 
					$this->planSlug, $this->subscriptionSlug, $this->subaccountSlug, $this->refundSlug, $this->disputeSlug,
					$this->chargeSlug, $this->cpanelSlug, $this->bulkChargeSlug, $this->balanceSlug, $this->transferSlug,
					$this->settlementSlug, $this->transferRecipientSlug, $this->invoiceSlug, $this->paymentPageSlug, $this->productSlug, 
				)
			)
		){
		
			$pathKeysArr = array('pageUrl', 'requestSlug');
			$maxPath = 2;
			
			//Transaction Tab
			
			if($requestSlug == $this->trxSlug){
				
				$subTabsArr = array(
					$this->initializeSubSlug, $this->verifySubSlug, 
					$this->listSubSlug, $this->fetchSubSlug, $this->checkAuthorizationSubSlug,
					$this->trxTotalsSubSlug, $this->trxTimelineSubSlug, 
					$this->exportTrxSubSlug, $this->trxPartialDebitSubSlug,
					
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			//verify transaction and give value Tab
			
			if($requestSlug == $this->verifyTrxGiveValSlug){
				
				
				
			}
			
			
			//Transaction Split Tab
			
			elseif($requestSlug == $this->transactionSplitSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->updateSubSlug,
					$this->addSubSlug, $this->removeSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			//Subaccount Tab
			
			elseif($requestSlug == $this->subaccountSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->updateSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Bank Tab
			
			elseif($requestSlug == $this->bankSlug){
				
				$subTabsArr = array(
					$this->bankListSubSlug, $this->bankListNubanSubSlug, $this->bankAccountNumberResolveSubSlug, 
					$this->bvnResolveSubSlug
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
				
			}
			
			//Verifications Tab
			
			elseif($requestSlug == $this->verificationsSlug){
				
				$subTabsArr = array(
					$this->phoneVerificationSubSlug
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
				
			}
			
			//BVN Tab
			
			elseif($requestSlug == $this->bvnSlug){
				
				$subTabsArr = array(
					$this->bvnMatchSubSlug
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
				
			}
			
			//Address Verification Tabs
			
			elseif($requestSlug == $this->addressVerificationSlug){
				
				$subTabsArr = array(
					$this->stateVerificationSubSlug
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//API Decision Tab
			
			elseif($requestSlug == $this->apiDecisionSlug){
				
				$subTabsArr = array(
					$this->cardBinSubSlug
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Customer Tab
			
			elseif($requestSlug == $this->customerSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->updateSubSlug,
					$this->customerValidationSubSlug, $this->customerRiskActionSubSlug, 
					$this->deactivateAuthorizationSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Dedicated Account Tab
			
			elseif($requestSlug == $this->dedicatedAccountSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->disableSubSlug,
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
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Subscription Tab
			
			elseif($requestSlug == $this->subscriptionSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, 
					$this->enableSubSlug, $this->disableSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Refunds Tab
			
			elseif($requestSlug == $this->refundSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Dispute Tab
			
			elseif($requestSlug == $this->disputeSlug){
				
				$subTabsArr = array(
					$this->listSubSlug, $this->fetchSubSlug, $this->updateSubSlug, $this->disputeExportSubSlug,
					$this->disputeSpecificSubSlug, $this->disputeEvidenceSubSlug, $this->disputeResolveSubSlug,
					$this->disputeUploadUrlSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Charge Tab
			
			elseif($requestSlug == $this->chargeSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->fetchSubSlug, $this->submitPinSubSlug, $this->submitOtpSubSlug,
					$this->submitPhoneSubSlug, $this->submitBirthdaySubSlug, $this->submitAddressSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Bulk Charge Tab
			
			elseif($requestSlug == $this->bulkChargeSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->fetchSubSlug, $this->listSubSlug, $this->batchChargesSubSlug, $this->pauseSubSlug,  
					$this->resumeSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Balance Tab
			
			elseif($requestSlug == $this->balanceSlug){
				
				$subTabsArr = array(
					$this->fetchSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Transfer Tab
			
			elseif($requestSlug == $this->transferSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->verifySubSlug,  $this->bulkSubSlug, 
					$this->enableOtpSubSlug, $this->resendOtpSubSlug, $this->disableOtpSubSlug, $this->disableOtpFinalSubSlug,
					$this->transferFinalizeSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			//Transfer Recipient Tab
			
			elseif($requestSlug == $this->transferRecipientSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->updateSubSlug,
					$this->bulkSubSlug, $this->removeSubSlug, 
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Settlement Tab
			
			elseif($requestSlug == $this->settlementSlug){
				
				$subTabsArr = array(
					$this->listSubSlug, $this->settlementSpecificSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			//Invoice Tab
			
			elseif($requestSlug == $this->invoiceSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->updateSubSlug,
					$this->verifySubSlug, $this->invoiceNotifySubSlug, $this->invoiceTotalsSubSlug, 
					$this->invoiceFinalizeSubSlug, $this->invoiceArchiveSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			//Payment Page Tab
			
			elseif($requestSlug == $this->paymentPageSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->updateSubSlug,
					$this->paymentPageProductSubSlug, $this->paymentPageSlugCheckSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			//Product Tab
			
			elseif($requestSlug == $this->productSlug){
				
				$subTabsArr = array(
					$this->createSubSlug, $this->listSubSlug, $this->fetchSubSlug, $this->updateSubSlug,
				);
				
				if((isset($pagePathArr[2]) && in_array(($tabSubSlug = strtolower($pagePathArr[2])), $subTabsArr)))
					$maxPath = 3;
				else
					$maxPath = 0;
				
			}
			
			
			//Cpanel Tab
			
			elseif($requestSlug == $this->cpanelSlug){
				
				$subTabsArr = array(
					$this->paymentSessTimeoutSubSlug,
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
		
		$isPayInitialization = $isChargeAuthorization = false;
		
		//Grab Request Query Strings Passed For API Calls
		$pathParam = urlencode(isset($_GET[($K="path_param")])? $_GET[$K] : (isset($_POST[($K)])? $_POST[$K] : ''));
		$postedUid = isset($_POST[($K="uid")])? $_POST[$K] : '';
		$postedCardSignature = isset($_POST[($K="cardSignature")])? $_POST[$K] : '';
		
		$postedAmount = isset($_POST[($K=$this->amountKey)])? $_POST[$K] : '';
		$this->preferredPayCurrency = isset($_POST[($K=$this->currencyKey)])? $_POST[$K] : $this->preferredPayCurrency;
		
		//Paystack process amount in the lowest denomination. So we must always normalize our amount
		$normalizedAmount = $this->normalizeAmount($postedAmount);
						
		$postRequestParams = $this->filterPostData($normalizedAmount);
		$getRequestParams = $this->filterGetData();
		$apiRequestQstr = '?'.http_build_query($getRequestParams);
		
		$U = $this->ACCOUNT->loadUser($postedUid? $postedUid : $this->SESS->getUserId());
		
		if(isset($_POST[($K=$this->trxCustomKey)]))
			$_SESSION[$this->keyUnique($K)] = $trxCustomizations = $_POST[$K];
			
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
		
		
		
		//HANDLE TRANSACTION ENDPOINT CALLS
		if($requestSlug == $this->trxSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
			switch($tabSubSlug){
				
				case $this->initializeSubSlug: 
				
				$curlRequestMethod = "POST";
				$isPayInitialization = true;
				$this->echoResponse = false;
				// url to go to after payment
				$callbackUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->verifyTrxGiveValSlug;
				$cancelActionUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->cancelTrxSlug;
			
				if(isset($_POST[$this->payFormSubmitKey])){
					
					$_SESSION[$this->keyUnique($this->internalRdrKey)] = preg_replace("#\#.*#", "", $rdr).'#'.$this->gatewayResponseDisplayId;
					
					if($postedAmount){
						
						$payCardDetails = isset($_POST[($this->payCardDetailsKey)])? $_POST[$this->payCardDetailsKey] : '';
						
						if($payCardDetails){
							
							$isChargeAuthorization = true;
							$payCardDetailsArr = $this->ENGINE->str_to_assoc($payCardDetails);
							$cardSignature = $this->ENGINE->get_assoc_arr($payCardDetailsArr, 'cardSignature');
							list($authorizationCode, $authorizationEmail) = $this->getUserSavedPaymentCard($U->getUserId(), $cardSignature, true);
							
							$curlPostFields['authorization_code'] = $authorizationCode;
							
							//Ensure we use the card authorization email to charge the customer
							$customerEmail = $authorizationEmail;
							
						}
						
						//Keep track of customer's card save option for verification endpoint
						elseif(isset($_POST[$this->savePayCardKey])){
							
							$savePayCard = true;
							
						}
						
						$curlPostFields['email'] = $customerEmail;
						$curlPostFields['callback_url'] = $callbackUrl;
						$curlPostFields['amount'] = $normalizedAmount;
						$curlPostFields['currency'] = $this->preferredPayCurrency;
						//$curlPostFields['channels'] = ['card', 'bank'];
						$curlPostFields['bearer'] = 'subaccount';
						
						/*
							metadata is used to add additional parameters which an endpoint doesn't accept naturally
							Methods used to pass a metadata:
							
							1. Key/value pair: e.g card_id => iu929
								parameters passed this way don't show up on the dashboard but they are returned with
								the API response
								
							2. Custom Fields: The custom_fields key is reserved for an array of custom fields that
								should show on the dashboard when you click on the transaction.
								custom fields have 3 keys: 
								a. display_name (label for the value when displaying)
								b. variable_name
								c. value
								
							3. Cancel Action: The cancel_action key is used to specify a redirect url when payment is
								canceled
								
							4. Custom Filters: The custom_filters key allows you control how a transaction is completed
								a.	Recurring: The recurring key must be set to boolean true if you need to directly debit the
									customer in future. This will ensure we accept only verve cards that support 
									recurring billing and ensures also to force a bank authentication for masterCard
									and VISA
											
								b.	Banks: The banks key is used to specify an array of bank codes if you only want some particular
									bank cards to be accepted for a transaction
											
								c.	Card Brands: The card_brands key is used to specify an array of card brand if you only want some particular
									card brands to be accepted for a transaction
									
								Example:	custom_filters: {
									
												recurring: true,
												banks: ['057', '100'],
												card_brands: ['visa']
									
											}
								
						
						*/
						
						$curlPostFields['metadata'] = [
						
							'cancel_action' => $cancelActionUrl,
							'custom_fields' => [
								[
									'display_name' => 'Full Name',
									'variable_name' => 'FullName',
									'value' => $U->getFullName(),
								],
							
								[
									'display_name' => 'Phone',
									'variable_name' => 'Phone',
									'value' => $U->getPhone(),
								],
							
								[
									'display_name' => 'Purpose',
									'variable_name' => 'purpose',
									'value' => $trxCustomDesc,
								]
							
							],
							
							'custom_filters'  => [
							
								'recurring' => isset($savePayCard),
								//'banks' => [],
								//'card_brands' => [],
							
							
							]
						
						];
						
						
					}else{
						
						$alertUser = $this->errorAlertPrefix.'The amount you want to pay was not specified; please go back to enter the amount and try again</span>';
						
					}
					
				}
				
				$flatSlugQstr = ($isChargeAuthorization? 'charge_authorization' : $this->initializeSubSlug);
				
				break;
				
				
				
				
				case $this->verifySubSlug:
				$flatSlugQstr = $this->verifySubSlug.'/'.$pathParam;
				break;
				
				
				case $this->checkAuthorizationSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->checkAuthorizationSubSlug; 
				break;
				
				
				
				case $this->trxPartialDebitSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->trxPartialDebitSubSlug; 
				break;
				
				
				case $this->trxTimelineSubSlug:
				$flatSlugQstr = $this->trxTimelineSubSlug.'/'.$pathParam; 
				break;
				
				
				
				case $this->exportTrxSubSlug:
				$flatSlugQstr = $this->exportTrxSubSlug; 
				break;
				
				
				
				case $this->trxTotalsSubSlug:
				$flatSlugQstr = $this->trxTotalsSubSlug; 
				break;
				
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam; 
				break;
				
				
				
				default: //List Transaction By Default
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->trxSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			
		}
		
		
				

		//HANDLE VERIFY TRANSACTION AND GIVE VALUES ENDPOINT CALLS
		if($requestSlug == $this->verifyTrxGiveValSlug){
			
			if(isset($_GET[$K="reference"]) && ($reference = $_GET[$K])){
				
				//Verify the transaction and give value
				$this->giveValue($reference);
				
			}else{
				
				$alertUser = $this->errorAlertPrefix.'Sorry we could not verify that transaction as no reference was found</span>';
				$this->returnToPaymentCallingPage($alertUser);
				
			}
		
		}
		
		
				
				
		
		//HANDLE TRANSACTION SPLIT ENDPOINT CALLS
		elseif($requestSlug == $this->transactionSplitSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "POST";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug: 
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$curlRequestMethod = "GET";
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->addSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->transactionSubaccountSubSlug.'/'.$this->addSubSlug; 
				break;
				
				
				
				case $this->removeSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->transactionSubaccountSubSlug.'/'.$this->removeSubSlug; 
				break;
				
				
				default: $curlRequestMethod = "GET";//Get Transaction Split List
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->transactionSplitSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		
		//HANDLE CHARGE ENDPOINT CALLS
		elseif($requestSlug == $this->chargeSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "POST";
			
			switch($tabSubSlug){
				
				case $this->submitPinSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->submitPinSubSlug;
				break;
				
				
				case $this->submitOtpSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->submitOtpSubSlug;
				break;
				
				
				case $this->submitPhoneSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->submitPhoneSubSlug;
				break;
				
				
				case $this->submitBirthdaySubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->submitBirthdaySubSlug;
				break;
				
				
				
				case $this->submitAddressSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->submitAddressSubSlug;
				break;
				
				
				case $this->fetchSubSlug:
				$curlRequestMethod = "GET";
				$flatSlugQstr = $pathParam; 
				break;
				
				
				default: $curlRequestMethod = "GET";//Create A Charge
				$curlPostFields = $postRequestParams; 
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->chargeSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		//HANDLE BULK CHARGE ENDPOINT CALLS
		elseif($requestSlug == $this->bulkChargeSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST"; 
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->batchChargesSubSlug:
				$flatSlugQstr = $pathParam.'/'.$this->batchChargesSubSlug; 
				break;
				
				
				case $this->pauseSubSlug:
				$flatSlugQstr = $this->pauseSubSlug.'/'.$pathParam;
				break;
				
				
				case $this->resumeSubSlug:
				$flatSlugQstr = $this->resumeSubSlug.'/'.$pathParam;
				break;
				
				
				default: //Get Bulk Charge List
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->bulkChargeSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE BALANCE ENDPOINT CALLS
		elseif($requestSlug == $this->balanceSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			
			switch($tabSubSlug){
				
				
				default: //Get Balance
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->balanceSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		
		//HANDLE TRANSFER ENDPOINT CALLS
		elseif($requestSlug == $this->transferSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "POST";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug: 
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$curlRequestMethod = "GET";
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->verifySubSlug:
				$curlRequestMethod = "GET";
				$flatSlugQstr = $this->verifySubSlug.'/'.$pathParam; 
				break;
				
				
				case $this->transferFinalizeSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->transferFinalizeSubSlug;
				break;
				
				
				case $this->bulkSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->bulkSubSlug;
				break;
				
				
				case $this->resendOtpSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->resendOtpSubSlug; 
				break;
				
				
				case $this->enableOtpSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->enableOtpSubSlug;
				break;
				
				
				case $this->disableOtpSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->disableOtpSubSlug;
				break;
				
				
				case $this->disableOtpFinalSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->disableOtpFinalSubSlug;
				break;
				
				
				default: $curlRequestMethod = "GET";//Get Transfer List
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->transferSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
				
		
		
		
		//HANDLE TRANSFER RECIPIENT ENDPOINT CALLS
		elseif($requestSlug == $this->transferRecipientSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST"; 
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->bulkSubSlug:
				$curlRequestMethod = "POST"; 
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->bulkSubSlug;
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
				
				
				default: //Get Transfer Recipient List
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->transferRecipientSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		//HANDLE SETTLEMENT ENDPOINT CALLS
		elseif($requestSlug == $this->settlementSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			
			switch($tabSubSlug){
				
				case $this->listSubSlug: 
				break;
				
				
				case $this->settlementSpecificSubSlug:
				$flatSlugQstr = $pathParam.'/'.$this->settlementSpecificSubSlug;
				break;
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->settlementSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE INVOICE ENDPOINT CALLS
		elseif($requestSlug == $this->invoiceSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "POST";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug: 
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$curlRequestMethod = "GET";
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT"; 
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->verifySubSlug:
				$curlRequestMethod = "GET";
				$flatSlugQstr = $this->verifySubSlug.'/'.$pathParam;
				break;
				
				
				case $this->invoiceNotifySubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->invoiceNotifySubSlug.'/'.$pathParam;
				break;
				
				
				case $this->invoiceTotalsSubSlug:
				$curlRequestMethod = "GET";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->invoiceTotalsSubSlug;
				break;
				
				
				case $this->invoiceFinalizeSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->invoiceFinalizeSubSlug.'/'.$pathParam;
				break;
				
				
				case $this->invoiceArchiveSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->invoiceArchiveSubSlug.'/'.$pathParam;
				break;
				
				
				default: $curlRequestMethod = "GET";//Get Invoice List
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->invoiceSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE PAYMENT PAGE ENDPOINT CALLS
		elseif($requestSlug == $this->paymentPageSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug: 
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT"; 
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam;
				break;
				
				
				case $this->paymentPageProductSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->paymentPageProductSubSlug;
				break;
				
				
				case $this->paymentPageSlugCheckSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->paymentPageSlugCheckSubSlug.'/'.$pathParam;
				break;
				
				
				default: //Get Payment Page List
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->paymentPageSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
			
		
		
		//HANDLE PRODUCT ENDPOINT CALLS
		elseif($requestSlug == $this->productSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug: 
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT"; 
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam;
				break;
				
				
				default: //Get Product List
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->productSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
				
		
		//HANDLE CPANEL ENDPOINT CALLS
		elseif($requestSlug == $this->cpanelSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			
			switch($tabSubSlug){
				
				case $this->paymentSessTimeoutSubSlug:
				if(!empty($_POST)){
					
					$curlRequestMethod = "PUT";
					$curlPostFields = $postRequestParams;
					
				} 
				
				$flatSlugQstr = $this->paymentSessTimeoutSubSlug; 
				break;
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->cpanelSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE SUBACCOUNT ENDPOINT CALLS
		elseif($requestSlug == $this->subaccountSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST"; 
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam; 
				break;
				
				
				default: //Get subaccount List
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->subaccountSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE BANK ENDPOINT CALLS
		elseif($requestSlug == $this->bankSlug){
			
			$flatSlugQstr = '';
			
			switch($tabSubSlug){
				
				case $this->bvnResolveSubSlug: $flatSlugQstr = $this->bvnResolveSubSlug.$pathParam;
				break;
				
				case $this->bankAccountNumberResolveSubSlug: $flatSlugQstr = $this->bankAccountNumberResolveSubSlug; 
				break;
				
				case $this->bankListNubanSubSlug: 
				break;
				
				default: //Get Bank List
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->bankSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
		
		}
		
		
		//HANDLE VERIFICATIONS ENDPOINT CALLS
		elseif($requestSlug == $this->verificationsSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "POST";
			
			switch($tabSubSlug){
				
				default: ;// Phone Number Verification Using True caller API 
				$curlPostFields = $postRequestParams;	
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->verificationsSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE BVN ENDPOINT CALLS
		elseif($requestSlug == $this->bvnSlug){
			
			$flatSlugQstr = '';
			
			switch($tabSubSlug){
				
				default: $flatSlugQstr = $this->bvnMatchSubSlug;
				$apiRequestQstr = '';
				$curlPostFields = $getRequestParams;	
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->bvnSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$curlRequestMethod = "POST";
			$this->echoResponse = true;
			
		
		}
		
		
		//HANDLE DECISION ENDPOINT CALLS
		elseif($requestSlug == $this->apiDecisionSlug){
			
			$flatSlugQstr = '';
			
			switch($tabSubSlug){
				
				default: $flatSlugQstr = $this->cardBinSubSlug.'/'.$pathParam;
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->apiDecisionSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
		
		}
		
		
		//HANDLE ADDRESS VERIFICATION ENDPOINT CALLS
		elseif($requestSlug == $this->addressVerificationSlug){
			
			switch($tabSubSlug){
				
				default: ;
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->addressVerificationSlug.'/'.$this->stateVerificationSubSlug.$apiRequestQstr;
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
		
		}
		
		
		//HANDLE COUNTRY ENDPOINT CALLS
		elseif($requestSlug == $this->countryListSlug){
			
			$curlUrl = $this->API_BASE_URL.'country';
			$curlRequestMethod = "GET";
			$this->echoResponse = true;
			
		
		}
		
		
		//HANDLE CUSTOMER ENDPOINT CALLS
		elseif($requestSlug == $this->customerSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "POST";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug: 
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$curlRequestMethod = "GET";
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->customerValidationSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->customerValidationSubSlug; 
				break;
				
				
				case $this->customerRiskActionSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->customerRiskActionSubSlug; 
				break;
				
				
				case $this->deactivateAuthorizationSubSlug: 
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->deactivateAuthorizationSubSlug;
				break;
				
				
				default: $curlRequestMethod = "GET";//Get Customer List
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->customerSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		//HANDLE DEDICATED ACCOUNT ENDPOINT CALLS
		elseif($requestSlug == $this->dedicatedAccountSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug:
				$curlRequestMethod = "POST"; 
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->disableSubSlug:
				$curlRequestMethod = "DELETE";
				$flatSlugQstr = $pathParam; 
				break;
				
				
				default: //Get Dedicated Account List
				
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->dedicatedAccountSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		//HANDLE PLAN ENDPOINT CALLS
		elseif($requestSlug == $this->planSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug: 
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam; 
				break;
				
				
				default: //Get Plan List
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->planSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE SUBSCRIPTION ENDPOINT CALLS
		elseif($requestSlug == $this->subscriptionSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "POST";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug: 
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$curlRequestMethod = "GET";
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->enableSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->enableSubSlug; 
				break;
				
				
				case $this->disableSubSlug:
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $this->disableSubSlug; 
				break;
				
				
				default: $curlRequestMethod = "GET"; //Get Subscription List
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->subscriptionSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE REFUNDS ENDPOINT CALLS
		elseif($requestSlug == $this->refundSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			
			switch($tabSubSlug){
				
				case $this->createSubSlug: 
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam; 
				break;
				
				
				default: //Get Refund List
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->refundSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		
		
		//HANDLE DISPUTE ENDPOINT CALLS
		elseif($requestSlug == $this->disputeSlug){
			
			$flatSlugQstr = '';
			$curlRequestMethod = "GET";
			
			switch($tabSubSlug){
				
				case $this->updateSubSlug:
				$curlRequestMethod = "PUT";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->fetchSubSlug:
				$flatSlugQstr = $pathParam; 
				break;
				
				
				case $this->disputeSpecificSubSlug:
				$flatSlugQstr = $this->disputeSpecificSubSlug.'/'.$pathParam; 
				break;
				
				
				case $this->disputeEvidenceSubSlug:
				$curlRequestMethod = "POST";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->disputeEvidenceSubSlug; 
				break;
				
				
				case $this->disputeUploadUrlSubSlug:
				$flatSlugQstr = $pathParam.'/'.$this->disputeUploadUrlSubSlug; 
				break;
				
				
				case $this->disputeResolveSubSlug:
				$curlRequestMethod = "PUT";
				$curlPostFields = $postRequestParams;
				$flatSlugQstr = $pathParam.'/'.$this->disputeResolveSubSlug; 
				break;
				
				
				case $this->disputeExportSubSlug:
				$flatSlugQstr = $this->disputeExportSubSlug; 
				break;
				
				
				default: //Get Dispute List
				
			}
			
			$curlUrl = $this->API_BASE_URL.$this->disputeSlug.($flatSlugQstr? '/'.$flatSlugQstr : '').$apiRequestQstr;
			$this->echoResponse = true;
			
		
		}
		
		
		
		
		//HANDLE CANCEL CALLBACK
		elseif($requestSlug == $this->cancelTrxSlug){
			
			$canceledTrxDetails='';
			
			if(isset($_SESSION[$K=$this->keyUnique($this->trxMetaKey)])){
				
				$trxMeta = json_decode($_SESSION[$K]);
				$this->expungeFromPaymentAwaitingVerif($trxMeta->reference, $U->getUserId(), $trxMeta->amount);
				$canceledTrxDetails = '';//'<br/>Details:<br/> amount: '.$trxMeta->amount.', reference: '.$trxMeta->reference.', status: canceled';
				
			}
			
			$alertUser = $this->errorAlertPrefix.'You canceled the payment'.$canceledTrxDetails.'</span>';
			$this->returnToPaymentCallingPage($alertUser);
			
		}
		
		
		//HANDLE FORGET PAYMENT CARD CALLS
		elseif($requestSlug == $this->forgetSavedPayCardSlug){
			
			$this->forgetUserPaymentCard($U->getUserId(), $postedCardSignature, true);
			exit(); //IMPORTANT
			
		}
		
		
		
		//HANDLE TRUECALLER RESPONSE ENDPOINT CALLS
		elseif($requestSlug == $this->truecallerResponseSlug){
			
			//Retrieve the request body 
			$input = $this->SITE->file_get_contents("http://input");
			
			//Log the retrieved event body for reference 
			$this->logWebhookEvent($input);
			
			//For now, only a post with paystack signature header is allowed
			if(!$this->apiSignatureValidated($input)){

				//Respond to webhook calling endpoint with a unauthorized status code
				http_response_code(401);					
				exit();			

			}
				
			//Respond to webhook calling endpoint with a status code, we do this early enough to avoid timeout 
			http_response_code(200);
		
		}
		
		
		
		//HANDLE WEBHOOK CALLBACKS
		elseif($requestSlug == $this->webhookGatewaySlug){
			
			//Retrieve the request body 
			$input = $this->SITE->file_get_contents("php://input");
			
			//Log the retrieved event body for reference 
			$this->logWebhookEvent($input);
			
			//For now, only a post with paystack signature header is allowed
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
					$this->giveValue($webhookBody->data->reference, false);
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
				
				// there was an error contacting the Paystack API
				$alertUser = $this->errorAlertPrefix.'Error: '.$err.'</span>';
				//die('Curl returned error: '.$err);
				//print_r($alertUser);
				
			}else{

				$trx = $jsonDecodedResponse;
				
				//print_r($trx); // uncomment this line for debug
				
				if(!$trx->status){
					
					// there was an error from the API
					$alertUser = $this->errorAlertPrefix.'Error: '.$trx->message.'</span>';
					//print_r('Error: '.$trx->message);
				  
				}

				elseif($isPayInitialization){
					
					$trxData = $trx->data;
					
					/*
						Log the payment reference before going to the payment page so that we can query it later from the webhook endpoint
						to give value if the trx was successful, just in case the customer had network issues that prevented redirection back 
						to the callBackUrl to get value immediately
					
					*/
					
					if(!$this->isPaymentAwaitingVerifLogged($trxData->reference, $U->getUserId(), $postedAmount))
						$this->logPaymentAwaitingVerif($trxCustomTitle, $customerEmail, $trxData->reference, $U->getUserId(), $postedAmount, $this->preferredPayCurrency, isset($savePayCard));
					
					
					//Save the initialized transaction metadatas into session for subsequent retrieval after redirections
					$_SESSION[$this->keyUnique($this->trxMetaKey)] = json_encode(['reference' => $trxData->reference, 'amount' => $postedAmount]);
					
					if($isChargeAuthorization){
						
						//Verify the transaction and give value
						$this->giveValue($trxData->reference);
						
						
					}else{
					
						// redirect to payment page
						header('Location: '.$trxData->authorization_url);
						exit();
						
					}
					
				}elseif($this->echoResponse){
					
					echo $rawResponse;
					exit();
					
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
	private function giveValue($trxReferencePassed, $returnToCaller = true){
	 
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
		
		$jsonDecodedResponse = $this->verifyTransaction(array('path_param' => $trxReferencePassed));
		
		$trxData = $jsonDecodedResponse->data;
		$trxStatus = $trxData->status;
		$trxAmount = $trxAmountFmtd = $this->rollbackNormalizedAmount($trxData->amount); //Remember to present back the real amount paid by user by stripping the padded lowest denomination digit
		$trxCurrency = $trxData->currency;
		$gatewayTrxId = $trxData->id;
		$trxReference = $trxData->reference;
		$trxCardAuthurization = $trxData->authorization;
		$trxSignature = isset($trxCardAuthurization->signature)? $trxCardAuthurization->signature : '';
		$trxReusable = isset($trxCardAuthurization->reusable)? $trxCardAuthurization->reusable : false;
		$trxCustomerEmail = $trxData->customer->email;
		$trxCustomerId = $trxData->customer->id;
		
		
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
		
		if(strtolower($trxStatus) == 'success' && $awaitingValAmount == $trxAmount && $awaitingValTrxRef == $trxReference && 
		$awaitingValCurrency == $trxCurrency && $awaitingValCustomerEmail == $trxCustomerEmail){
			
			// Give value if not already given for this transaction reference
		  
			if(!$paymentReferenceIsLogged){
				
				//Give value to the customer and log the transaction for reference
				
				$trxAmountFmtd = $this->giveVerifiedTrxVal($trxAmount, $storeType, $U);
				
				$this->logPaymentReference($storeType, $trxReference, $gatewayTrxId, $U->getUserId(), $trxCustomerId, $trxCustomerEmail, $trxAmount);

				//Save the card if customer asked for it
				if($savePayCard && $trxReusable && $trxSignature && $trxCustomerEmail){
					
					if(!$this->isUserPaymentCardSaved($trxSignature, $trxCustomerEmail)){
						
						$this->saveUserPaymentCard($trxSignature, $U->getUserId(), $trxCustomerEmail, $trxCardAuthurization);
						
					}
					
				}
				
				//Remove all payments that has been given value from transactions_awaiting_verification table
				$this->expungeFromPaymentAwaitingVerif($trxReferencePassed, $U->getUserId(), $trxAmount);
			  
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





	/* Method for validating request api signature */
	private function apiSignatureValidated($data){
	
		$hashKey = 'HTTP_X_PAYSTACK_SIGNATURE';
		
		if((strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') && isset($_SERVER[$hashKey])){
			
			foreach($this->API_DETAILS_ARR as $indexKey => $apiKey){
				
				return ($_SERVER[$hashKey] === hash_hmac('sha512', $data, $apiKey));
				
			}
			
		}
		
		return false;
		
	}




	
	//Paystack process amount in the lowest denomination(Kobo for NGN, pesewas for GHS, cents for ZAR). 
	//So we must always apply the normalization before sending to the API endpoint
	
	/* Method for normalizing amount to API payment gateway required format */
	public function normalizeAmount($amount){
	
		return ($amount =  ((double)$amount) * $this->amountNormalizationVal);
		
	}



	/* Method for rolling back API normalized amount */
	public function rollbackNormalizedAmount($normalizedAmount){
	
		return ($normalizedAmount /=  $this->amountNormalizationVal);
		
	}

	
	
	/**********************************
	TRANSACTION API METHODS
	***********************************/
	
	/* Method for fetching total transactions carried out on your integration useful for pagination */
	public function getTransactionTotals(){
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->trxTotalsSubSlug;
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		return $jsonDecodedResponse->data->total_transactions;
		
		/*
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		*/
		
	}
	
	
	
	/* Method for listing your integration transaction */
	public function getTransactionList($userParams = []){
		
		$defaultParams = [
		
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}

	
	/* Method for fetching a transaction from your integration */
	public function fetchTransaction($userParams){
		
		$defaultParams = [
		
			'path_param' => '' //transaction id
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}
	
	
	/* Method for fetching a transaction timeline from your integration */
	public function getTransactionTimeline($userParams){
		
		$defaultParams = [
		
			'path_param' => '' //transaction id or reference
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->trxTimelineSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';
		
	}
	
	
	/* Method for exporting transaction from your integration */
	public function exportTransaction($userParams = [], $download = true){
		
		$defaultParams = [
		
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->exportTrxSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if($download){
			
			header("Location:".$jsonDecodedResponse->data->path);
			exit();
			
		}
		
		/*
		echo'<pre>';
		print_r($jsonDecodedResponse->data);
		echo'</pre>';*/
		
	}


	
	/* Method for doing a partial debit transaction on your integration */
	public function doTransactionPartialDebit($userParams){
		
		$defaultParams = [
		
			'amount' => '', //amount
			'authorization' => '', //authorization code
			'currency' => '', //3 letter ISO currency 
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->trxPartialDebitSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		//return $jsonDecodedResponse->data;
		
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		
		
	}
	
	
	
	/* Method for checking authorization of a card against an amount */
	public function checkAuthorization($userParams){
		
		$defaultParams = [
		
			'amount' => '', //amount
			'authorization_code' => '', //authorization code
			'email' => '', //customer email
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->checkAuthorizationSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}

	
	/* Method for verifying transaction */
	public function verifyTransaction($userParams){
		
		$defaultParams = [
			
			/** PATH PARAMS **/
			'path_param' => '', //reference; This is the transaction reference used to initiate the transaction
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->trxSlug.'/'.$this->verifySubSlug.$qstr;
	
		return json_decode($this->SITE->file_get_contents($curlUrl));
		
	}
	
	
	/**********************************
	TRANSACTION SPLIT API METHODS
	***********************************/

	/* Method for creating a transaction split */
	public function createTransactionSplit($userParams){
		
		$defaultParams = [
		
			'name' => '', //name of the transaction split
			'type' => '', //values: percentage | flat
			'currency' => '', //3 letter ISO currency 
			'subaccounts' => '', //array of objects containing subaccount code and number of share
			'bearer_type' => '', //values: subaccount | account | all-proportional | all
			'bearer_subaccount' => '', //subaccount code
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transactionSplitSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for updating a transaction split */
	public function updateTransactionSplit($userParams){
		
		$defaultParams = [
		
			'name' => '', //name of the transaction split
			'active' => '', //boolean value: true | false
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transactionSplitSlug.'/'.$this->updateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}

	

	/* Method for adding a subaccount to a transaction split */
	public function addSubaccountToTransactionSplit($userParams){
		
		$defaultParams = [
		
			'subaccount' => '', //subaccount code
			'share' => '', //transaction share of the subaccount
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transactionSplitSlug.'/'.$this->addSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}


	/* Method for removing a subaccount from a transaction split */
	public function removeSubaccountFromTransactionSplit($userParams){
		
		$defaultParams = [
		
			'subaccount' => '', //subaccount code
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transactionSplitSlug.'/'.$this->removeSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for fetching list of transaction split */
	public function getTransactionSplitList($userParams = []){
		
		$defaultParams = [
			//Optional filters
			'name' => '',
			'active' => '',
			'sort_by' => '',
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			'from' => '',
			'to' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transactionSplitSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching a transaction split */
	public function fetchTransactionSplit($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id of the split
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transactionSplitSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	
	/**********************************
	CHARGE API METHODS
	***********************************/

	/* Method for initiating a charge */
	public function initiateCharge($userParams){
		
		$defaultParams = [
		
			'email' => '', //customer email
			'amount' => '', //amount
			//optional
			'bank' => '', //an object containing bank account to charge(don't send if charging authorization code)
			'authorization_code' => '', //an authorization code to charge(don't send if charging bank account)
			'pin' => '', //4-digit pin(send with a non-reusable authorization code)
			'metadata' => '', //JSON object
			'reference' => '', //transaction reference
			'ussd' => '', //an object of ussd type to charge(don't send if charging an authorization code, bank or card) 
			'mobile_money' => '', //an object of mobile type details(don't send if charging an authorization code, bank or card) 
			'device_id' => '', //unique device identifier used in making payment
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for submitting a charge pin */
	public function submitChargePin($userParams){
		
		$defaultParams = [
		
			'pin' => '', //pin submitted by user
			'reference' => '', //reference for transaction that requested pin
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->submitPinSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for submitting a charge OTP */
	public function submitChargeOtp($userParams){
		
		$defaultParams = [
		
			'otp' => '', //OTP submitted by user
			'reference' => '', //reference for ongoing transaction
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->submitOtpSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}




	/* Method for submitting a charge phone */
	public function submitChargePhone($userParams){
		
		$defaultParams = [
		
			'phone' => '', //phone submitted by user
			'reference' => '', //reference for ongoing transaction
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->submitPhoneSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for submitting a charge birthday */
	public function submitChargeBirthday($userParams){
		
		$defaultParams = [
		
			'birthday' => '', //birthday submitted by user
			'reference' => '', //reference for ongoing transaction
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->submitBirthdaySubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for submitting a charge address */
	public function submitChargeAddress($userParams){
		
		$defaultParams = [
		
			'address' => '', //address submitted by user
			'reference' => '', //reference for ongoing transaction
			'city' => '', //city submitted by user
			'state' => '', //state submitted by user
			'zipcode' => '', //zipcode submitted by user
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->submitAddressSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}




	/* Method for checking a pending charge */
	public function CheckCharge($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //The reference to check
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->chargeSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	

	
	
	/**********************************
	CONTROL PANEL API METHODS
	***********************************/

	/* Method for fetching integration payment session timeout */
	public function fetchPaymentSessionTimeout($userParams = []){
		
		$defaultParams = [
		
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->cpanelSlug.'/'.$this->paymentSessTimeoutSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	


	/* Method for updating integration payment session timeout */
	public function updatePaymentSessionTimeout($userParams){
		
		$defaultParams = [
		
			'timeout' => '', //time before stopping session(in seconds, set to 0 to cancel session timeout)
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->cpanelSlug.'/'.$this->paymentSessTimeoutSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	
	
	
	/**********************************
	BULK CHARGE API METHODS
	***********************************/

	/* Method for initiating a bulk charge */
	public function initiateBulkCharge($userParams){
		
		$defaultParams = [
			
			[
				'authorization' => '', //authorization
				'amount' => '', //amount
				
			],
			
			[
				'authorization' => '', //authorization
				'amount' => '', //amount
				
			]
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bulkChargeSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for fetching list of bulk charge batch */
	public function getBulkChargeBatchList($userParams = []){
		
		$defaultParams = [
		
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			'from' => '',
			'to' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bulkChargeSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching a bulk charge batch */
	public function fetchBulkChargeBatch($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //transaction id
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bulkChargeSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}




	/* Method for fetching charges in a batch */
	public function fetchChargesInBatch($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or code for the batch whose charges you want to retrieve 
			'status' => '', //Values: pending | success | failed
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			//optional
			'from' => '',
			'to' => '',
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bulkChargeSlug.'/'.$this->batchChargesSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	




	/* Method for pausing bulk charge batch */
	public function pauseBulkChargeBatch($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //The batch code for the bulk charge you want to pause
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bulkChargeSlug.'/'.$this->pauseSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	



	/* Method for resuming bulk charge batch */
	public function resumeBulkChargeBatch($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //The batch code for the bulk charge you want to resume
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bulkChargeSlug.'/'.$this->resumeSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	
	
	/**********************************
	TRANSFER API METHODS
	***********************************/

	/* Method for fetching available balance useful for transfer */
	public function getBalance($userParams = []){
		
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
	
	
	
	

	/* Method for initiating a transfer */
	public function initiateTransfer($userParams){
		
		$defaultParams = [
			
			'source' => '', //values: balance | 
			'amount' => '', //amount
			'recipient' => '', //transfer recipient code
			//optional
			'reason' => '', //transfer reason
			'currency' => '', //transfer currency
			'reference' => '', //transfer reference(leave blank to auto generate or pass your own that conforms to required standard) 
			
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
	
	
	

	/* Method for finalizing a transfer */
	public function finalizeTransfer($userParams){
		
		$defaultParams = [
			
			'transfer_code' => '', //the transfer code you want to finalize 
			'ot' => '', //OTP sent to business phone to verify transfer
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->transferFinalizeSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	
	

	/* Method for initiating a bulk transfer */
	public function initiateBulkTransfer($userParams){
		
		$defaultParams = [
			
			'source' => '', //values: balance | 
			'transfers' => [
				
				[
				
					'amount' => '', //amount
					'recipient' => '', //transfer recipient code
					'reference' => '', //transfer reference(leave blank to auto generate or pass your own that conforms to required standard) 
					
				],
				
				[
				
					'amount' => '', //amount
					'recipient' => '', //transfer recipient code
					'reference' => '', //transfer reference(leave blank to auto generate or pass your own that conforms to required standard) 
					
				]
			
			
			]
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->bulkSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	


	/* Method for fetching list of transfer carried out on your integration */
	public function getTransferList($userParams = []){
		
		$defaultParams = [
		
			//Optional filters
			'customer' => '', //customer id
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			'from' => '',
			'to' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	


	/* Method for fetching a transfer carried out on your integration */
	public function fetchTransfer($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or code of transfer you want to fetch 
			
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
	
	
	



	/* Method for verifying a transfer carried out on your integration */
	public function verifyTransfer($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //transfer reference
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->verifySubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	

	/* Method for resending transfer OTP */
	public function resendTransferOtp($userParams){
		
		$defaultParams = [
			
			'transfer_code' => '', //Transfer Code
			'reason' => '', //Values: resend_otp | transfer
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->resendOtpSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}

	

	

	/* Method for enabling transfer OTP */
	public function enableTransferOtp($userParams = []){
		
		$defaultParams = [
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->enableOtpSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}

	


	

	/* Method for disabling transfer OTP */
	public function disableTransferOtp($userParams = []){
		
		$defaultParams = [
			
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->disableOtpSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}

	

	
	

	/* Method to finalize disabling transfer OTP */
	public function disableTransferOtpFinalize($userParams){
		
		$defaultParams = [
			
			'otp' => '', //OTP sent to business phone to verify disabling OTP requirement
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferSlug.'/'.$this->disableOtpFinalSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}

	
	
	
	
	
	
	
	/**********************************
	TRANSFER RECIPIENT API METHODS
	***********************************/

	/* Method for creating a transfer recipient */
	public function createTransferRecipient($userParams){
		
		$defaultParams = [
			
			'type' => '', //values: nuban | 
			'name' => '', //recipient name
			'account_number' => '', //bank account number(required if type attribute is nuban)
			'bank_code' => '', //bank code(required if type attribute is nuban)
			//optional
			'description' => '', //a description
			'currency' => '', //recipient account currency
			'authorization_code' => '', //an authorization code from previous transaction
			'metadate' => '', //JSON object that holds additional information of your recipient
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferRecipientSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	
	
	
	/* Method for creating multiple transfer recipient */
	public function bulkCreateTransferRecipient($userParams){
		
		$defaultParams = [
			
			'batch' => [
				
				'type' => '', //values: nuban | 
				'name' => '', //recipient name
				'bank_code' => '', //bank code(required if type attribute is nuban)
				//optional
				'account_number' => '', //bank account number(required if type attribute is nuban)
				'description' => '', //a description
				'currency' => '', //recipient account currency
				'authorization_code' => '', //an authorization code from previous transaction
				'metadate' => '', //JSON object that holds additional information of your recipient
				
			]
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferRecipientSlug.'/'.$this->bulkSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	
	


	/* Method for updating a transfer recipient */
	public function updateTransferRecipient($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or code of transfer recipient
			'name' => '', //recipient name
			'email' => '', //recipient email address
			//optional
			'description' => '', //a description
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferRecipientSlug.'/'.$this->updateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	
	


	/* Method for fetching list of transfer recipient */
	public function getTransferRecipientList($userParams = []){
		
		$defaultParams = [
		
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			//optional
			'from' => '',
			'to' => '',
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferRecipientSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}




	/* Method for fetching a transfer recipient */
	public function fetchTransferRecipient($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or code of transfer recipient you want to fetch
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferRecipientSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	




	/* Method for deleting a transfer recipient */
	public function deleteTransferRecipient($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or code of transfer recipient you want to delete
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->transferRecipientSlug.'/'.$this->removeSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	


	
	
	/**********************************
	SETTLEMENT API METHODS
	***********************************/

	/* Method for listing settlement on your integration */
	public function getSettlementList($userParams = []){
		
		$defaultParams = [
			
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			//optional
			'subaccount' => '', //subaccount id(set to none to export only transaction for the account)
			'from' => '',
			'to' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->settlementSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching details of settlement for specific transaction */
	public function fetchSpecificSettlement($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //settle id in which you want to fetch its transaction
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			//optional
			'from' => '',
			'to' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->settlementSlug.'/'.$this->settlementSpecificSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}

		

	
	/**********************************
	INVOICE API METHODS
	***********************************/

	/* Method for creating an invoice */
	public function createInvoice($userParams){
		
		$defaultParams = [
		
			'customer' => '', //customer id or code
			'amount' => '', //payment request amount. Only useful if line_items and tax values are ignored 
			'due_date' => '', //request due date(ISO)
			//optional
			'description' => '', //an object that gives a short description of the payment request 
			'line_items' => '', //an array of line items [['name' => 'item1', 'amount' => 2000], ['name' => 'item1', 'amount' => 2000], ]
			'tax' => '', //an array of tax to charge [['name' => 'VAT', 'amount' => 2000], ['name' => 'TAX2', 'amount' => 2000], ]
			'currency' => '', //invoice currency
			'send_notification' => '', //boolean of whether to notify customer via email
			'draft' => '', //boolean of whether to as draft(overrides send_notification)
			'has_invoice' => '', //boolean of whether to create draft invoice(adds auto incremented invoice number if none is provided)
			'invoice_number' => '', //unique numeric invoice number of your choice
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for updating an invoice */
	public function updateInvoice($userParams){
		
		$defaultParams = [
		
			'customer' => '', //customer id or code
			'amount' => '', //payment request amount. Only useful if line_items and tax values are ignored 
			//optional
			'due_date' => '', //request due date(ISO)
			'description' => '', //an object that gives a short description of the payment request 
			'line_items' => '', //an array of line items [['name' => 'item1', 'amount' => 2000], ['name' => 'item1', 'amount' => 2000], ]
			'tax' => '', //an array of tax to charge [['name' => 'VAT', 'amount' => 2000], ['name' => 'TAX2', 'amount' => 2000], ]
			'currency' => '', //invoice currency
			'send_notification' => '', //boolean of whether to notify customer via email
			'draft' => '', //boolean of whether to as draft(overrides send_notification)
			'has_invoice' => '', //boolean of whether to create draft invoice(adds auto incremented invoice number if none is provided)
			'invoice_number' => '', //unique numeric invoice number of your choice
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->updateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}





	/* Method for sending an invoice notification */
	public function sendInvoiceNotification($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //invoice id or code
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->invoiceNotifySubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}






	/* Method for finalizing a draft invoice */
	public function finalizeInvoice($userParams){
		
		$defaultParams = [
			
			'path_param' => '', //invoice id or code
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->invoiceFinalizeSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}





	/* Method for archiving an invoice */
	public function archiveInvoice($userParams){
		
		$defaultParams = [
			
			'path_param' => '', //invoice id or code
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->invoiceArchiveSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}




	/* Method for fetching list of invoice */
	public function getInvoiceList($userParams = []){
		
		$defaultParams = [
			
			//filters
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			'customer' => '', //customer id
			'status' => '', //invoice status
			'currency' => '', //invoice currency
			'include_archive' => '', //show archived invoices
			//optional filters
			'from' => '',
			'to' => '',
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}





	/* Method for fetching invoice metrics for dashboard */
	public function getInvoiceTotals($userParams = []){
		
		$defaultParams = [
			
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->invoiceTotalsSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching an invoice */
	public function fetchInvoice($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or code of the invoice you want to fetch
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	



	/* Method for verifying an invoice */
	public function verifyInvoice($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or code of the invoice you want to verify
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->invoiceSlug.'/'.$this->verifySubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	

	
	/**********************************
	PAYMENT PAGE API METHODS
	***********************************/

	/* Method for creating a payment page */
	public function createPaymentPage($userParams){
		
		$defaultParams = [
		
			'name' => '', //page name
			//optional
			'description' => '', //page description
			'amount' => '', //The default amount to accept via this page. Leave unset to allow customer provide any amount of their choice
			'slug' => '', //your page url slug. The page will become accessible at https://paystack.com/pay/your_page_slug
			'metadata' => '', //object holding extra data to configure your payment page e.g subaccount, logo image, transaction charge
			'redirect_url' => '', //if you would like to be redirected to some other page after successful payment, specify the url here 
			'custom_fields' => '', //if you would like to accept custom fields then specify them here
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->paymentPageSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for updating a payment page */
	public function updatePaymentPage($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //page id or slug
			'name' => '', //page name
			'description' => '', //page description
			//optional
			'amount' => '', //The default amount to accept via this page. Leave unset to allow customer provide any amount of their choice
			'active' => '', //boolean if set to false deactivate the page url 
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->paymentPageSlug.'/'.$this->updateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for adding product to a payment page */
	public function addPaymentPageProduct($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //page id or slug
			'product' => '', //an integer array of all the product ids [id1, id2, ....]
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->paymentPageSlug.'/'.$this->paymentPageProductSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	
	/* Method for checking availability of a payment page url slug */
	public function checkPaymentPageSlugAvailability($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //url slug to confirm
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->paymentPageSlug.'/'.$this->paymentPageSlugCheckSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching list of payment page */
	public function getPaymentPageList($userParams = []){
		
		$defaultParams = [
		
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			'from' => '',
			'to' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->paymentPageSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching a payment page */
	public function fetchPaymentPage($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or slug of the page you want to fetch
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->paymentPageSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	

	

	
	/**********************************
	PRODUCT API METHODS
	***********************************/

	/* Method for creating a product */
	public function createProduct($userParams){
		
		$defaultParams = [
		
			'name' => '', //product name
			'description' => '', //product description
			'price' => '', //The price(amount) of the product
			'currency' => '', //The currency in which price is set
			//optional
			'limited' => '', //boolean which denotes if a product is limited in stock
			'quantity' => '', //number of products in stock(use if limited is true)
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->productSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for updating a product */
	public function updateProduct($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //product id
			'name' => '', //product name
			'description' => '', //product description
			'price' => '', //The price(amount) of the product
			'currency' => '', //The currency in which price is set
			//optional
			'limited' => '', //boolean which denotes if a product is limited in stock
			'quantity' => '', //number of products in stock(use if limited is true)
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->productSlug.'/'.$this->updateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}




	/* Method for fetching list of payment page */
	public function getProductList($userParams = []){
		
		$defaultParams = [
		
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			'from' => '',
			'to' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->productSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching a product */
	public function fetchProduct($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id of the product you want to fetch
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->productSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	

	

	
	
	/**********************************
	SUBACCOUNT API METHODS
	***********************************/

	/* Method for creating a subaccount */
	public function createSubaccount($userParams){
		
		$defaultParams = [
		
			'business_name' => '', //name of business for subaccount
			'settlement_bank' => '', //bank code
			'account_number' => '', //bank account number
			'subaccounts' => '', //array of objects containing subaccount code and number of share
			'percentage_charge' => '', //A floating point value of the percentage charged when receiving on behalf of this subaccount 
			'description' => '', //subaccount description
			'primary_contact_email' => '', //subaccount contact email
			'primary_contact_name' => '', //subaccount contact person name
			'primary_contact_phone' => '', //subaccount contact phone
			'metadata' => '', //stringified JSON object(including custom_fields attribute)
			
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



	/* Method for updating a subaccount */
	public function updateSubaccount($userParams){
		
		$defaultParams = [
		
			'business_name' => '', //name of business for subaccount
			'settlement_bank' => '', //bank code
			
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



	/* Method for fetching list of subaccount */
	public function getSubaccountList($userParams = []){
		
		$defaultParams = [
			//Optional filters
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			'from' => '',
			'to' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subaccountSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching a subaccount */
	public function fetchSubaccount($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or code of the subaccount you want to fetch
			
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
	
	
	
	


	
	
	
	/**********************************
	BVN API METHODS
	***********************************/


	/* Method for checking if a BVN and account number is linked */
	public function matchBvn($userParams, $retDataObj = false){
		
		$defaultParams = [
		
			'bvn' => '', //bvn
			'account_number' => '', //account number
			'bank_code' => '', //bank code
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bvnSlug.'/'.$this->bvnMatchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: [ first_name | last_name | account_number | is_blacklisted ] (booleans), [ bvn ]			
			$responseData = $jsonDecodedResponse->data;
			return ($retDataObj? $responseData : ($responseData->account_number && $responseData->first_name && $responseData->last_name)); 
			
		}
		
		return $jsonDecodedResponse;
		
	}


	


	
	
	/**********************************
	VERIFICATIONS API METHODS
	***********************************/
	
	/* 
		Method for verifying a phone number 
	
	*/
	public function verifyPhone($userParams){
		
		$defaultParams = [
		
			'verification_type' => 'truecaller', //Values: truecaller | 
			'phone' => '', //customer phone number with country code but excluding the + prefix
			'callback_url' => $this->domainPrefixedBasePaymentGatewayUrl.$this->truecallerResponseSlug, //url to receive verification details 
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->verificationsSlug.'/'.$this->phoneVerificationSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}






	
	
	/**********************************
	BANK API METHODS
	***********************************/
	
	/* 
		Method for resolving a bank account BVN (Get customer's infos using BVN), 
		useful for populating fields during registrations 
	
	*/
	public function resolveBvn($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //bvn
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bankSlug.'/'.$this->bvnResolveSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: [ first_name | last_name | dob | mobile | bvn ]
			return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}




	/* Method for resolving a bank account number (Confirm if an account belongs to the right customer) */
	public function resolveBankAccountNumber($userParams, $retDataObj = false){
		
		$defaultParams = [
		
			'account_number' => '', //account number
			'bank_code' => '', //bank code
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bankSlug.'/'.$this->bankAccountNumberResolveSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: [ account_number | account_name ]
			$responseData = $jsonDecodedResponse->data;
			return ($retDataObj? $responseData : ('Account Name: '.$responseData->account_name.' | Account Number: '.$responseData->account_number)); 

		}
		
		return $jsonDecodedResponse;
		
	}
	
	

	/* Method for fetching a payment card information */
	public function resolveCardBin($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //card bin
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->apiDecisionSlug.'/'.$this->cardBinSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: [ bin |  bank | brand | sub_brand | country_code | country_name | card_type | linked_bank_id ]
			return $jsonDecodedResponse->data;
			
		}
		
		return $jsonDecodedResponse;
		
	}




	/* Method for fetching list of banks supported */
	public function getBankList($userParams = [], $fieldName = 'pay_api_bank_list'){
		
		$defaultParams = [
		
			'pay_with_bank' => true, //a boolean with value: true
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			'type' => '', //type of financial channel; for Ghanaian use either mobile_money for mobile money or ghipps for bank channels
			'currency' => '', //Values: NGN | USD | GHS | 
			'country' => '', //The country from which to obtain the list of supported banks. e.g country=ghana or country=nigeria 
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bankSlug.'/'.$this->bankListSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		return $this->buildSelectOptions($jsonDecodedResponse, ['fieldName' => $fieldName, 'meta' => 'merge_name_code']);
		
	}


	/* Method for fetching list of NUBAN banks supported */
	public function getBankListNuban($userParams = [], $fieldName = 'pay_api_bank_list_nuban'){
		
		$defaultParams = [
		
			'pay_with_bank_transfer' => true, //a boolean with value: true
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->bankSlug.'/'.$this->bankListNubanSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		return $this->buildSelectOptions($jsonDecodedResponse, ['fieldName' => $fieldName, 'meta' => 'merge_name_code']);
		
	}
	

	/* Method for fetching list of countries supported */
	public function getCountryList($userParams = [], $fieldName = 'pay_api_country_list'){
		
		$defaultParams = [
		
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->countryListSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		return $this->buildSelectOptions($jsonDecodedResponse, ['fieldName' => $fieldName]);
		
	}
	

	/* Method for fetching address verification state */
	public function getStateVerification($userParams, $forSelect = false, $fieldName = 'pay_api_address_avs_list'){
		
		$defaultParams = [
		
			'country' => '',  //2 letter country code
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->addressVerificationSlug.'/'.$this->stateVerificationSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: [ bin |  bank | brand | sub_brand | country_code | country_name | card_type | linked_bank_id ]
			return ($forSelect? $this->buildSelectOptions($jsonDecodedResponse, ['fieldName' => $fieldName]) : $jsonDecodedResponse->data);
			
		}
		
		return $jsonDecodedResponse;
		
	}
	


	
	
	
	/**********************************
	CUSTOMER API METHODS
	***********************************/
	
	/* Method for creating a customer */
	public function createCustomer($userParams){
		
		$defaultParams = [
		
			'email' => '', //customer email address
			'first_name' => '', //customer first name
			'last_name' => '', //customer last name
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->customerSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for updating a customer */
	public function updateCustomer($userParams){
		
		$defaultParams = [
		
			'first_name' => '', //customer first name
			'last_name' => '', //customer last name
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->customerSlug.'/'.$this->updateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for fetching list of customer */
	public function getCustomerList($userParams = []){
		
		$defaultParams = [
		
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->customerSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching a customer */
	public function fetchCustomer($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //email or code of the customer you want to fetch
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->customerSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	

	

	/* Method for validating a customer */
	public function validateCustomer($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or code of the customer you want to validate
			'first_name' => '', //customer first name
			'last_name' => '', //customer last name
			'type' => '', //values: bvn | 
			'value' => '', //customer id number
			'country' => '', //2 letter country code of ID issuer
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->customerSlug.'/'.$this->customerValidationSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	

	

	/* Method for white/black listing a customer */
	public function setCustomerRiskAction($userParams){
		
		$defaultParams = [
		
			'customer' => '', //customer email address or code
			'risk_action' => '', //values: default | allow | deny /* use allow to white list and deny to black list */ 
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->customerSlug.'/'.$this->customerRiskActionSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	

	/* Method for deactivating card authorization */
	public function deactivateAuthorization($userParams){
		
		$defaultParams = [
		
			'authorization_code' => '',  //authorization code you want to deactivate
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->customerSlug.'/'.$this->deactivateAuthorizationSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return array($rawResponse, $jsonDecodedResponse);
		
	}


	
	
	/**********************************
	DEDICATED NUBAN API METHODS
	***********************************/

	/* Method for creating a dedicated account */
	public function createDedicatedAccount($userParams){
		
		$defaultParams = [
		
			'customer' => '', //customer email address or code
			'preferred_bank' => '', //optional bank name slug
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->dedicatedAccountSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}





	/* Method for fetching list of dedicated account */
	public function getDedicatedAccountList($userParams = []){
		
		$defaultParams = [
			//optional filter params
			'active' => '', //status of dedicated account
			'currency' => '', //3 letter ISO currency 
			'provider_slug' => '', //bank name slug
			'bank_id' => '', //bank id
			'customer' => '', //customer id
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->dedicatedAccountSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching a dedicated account */
	public function fetchDedicatedAccount($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id of the dedicated account
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->dedicatedAccountSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	



	/* Method for deactivating a dedicated account */
	public function deactivateDedicatedAccount($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id of the dedicated account
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->dedicatedAccountSlug.'/'.$this->disableSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	
	/**********************************
	PLAN API METHODS
	***********************************/

	/* Method for creating a plan */
	public function createPlan($userParams){
		
		$defaultParams = [
		
			'name' => '', //name of plan
			'amount' => '', //amount
			'interval' => '', //values: hourly | daily | weekly | monthly | annually | biannually
			
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



	/* Method for updating a plan */
	public function updatePlan($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or code of plan
			'name' => '', //plan code
			'amount' => '', //amount
			'interval' => '', //values: hourly | daily | weekly | monthly | annually | biannually
			
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



	/* Method for fetching list of plan */
	public function getPlanList($userParams = []){
		
		$defaultParams = [
		
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			
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



	/* Method for fetching a plan */
	public function fetchPlan($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or code of plan you want to fetch
			
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
	
	
	
	
	
	/**********************************
	SUBSCRIPTION API METHODS
	***********************************/

	/* Method for creating a subscription */
	public function createSubscription($userParams){
		
		$defaultParams = [
		
			'customer' => '', //customer email or code 
			'plan' => '', //plan code
			'authorization' => '', //customer authorization
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subscriptionSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for enabling subscription on your integration */
	public function enableSubscription($userParams){
		
		$defaultParams = [
		
			'code' => '', //s code
			'token' => '', //email token
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subscriptionSlug.'/'.$this->enableSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for disabling subscription on your integration */
	public function disableSubscription($userParams){
		
		$defaultParams = [
		
			'code' => '', //s code
			'token' => '', //email token
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subscriptionSlug.'/'.$this->disableSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for fetching list of subscription */
	public function getSubscriptionList($userParams = []){
		
		$defaultParams = [
		
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			
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



	/* Method for fetching a subscription */
	public function fetchSubscription($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id or code of subscription you want to fetch
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->subscriptionSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	

	
	
	
	/**********************************
	REFUNDS API METHODS
	***********************************/

	/* Method for initiating a refund */
	public function initiateRefund($userParams){
		
		$defaultParams = [
		
			'transaction' => '', //transaction reference or id
			'amount' => '', //amount
			'currency' => '', //3 letter ISO currency 
			'customer_note' => '', //customer reason
			'merchant_note' => '', //merchant reason
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->refundSlug.'/'.$this->createSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for fetching list of refund */
	public function getRefundList($userParams = []){
		
		$defaultParams = [
		
			'reference' => '', //Identifier for the transaction to be refunded
			'currency' => '', //3 letter ISO currency 
			//optional filters
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			'from' => '',
			'to' => '',
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->refundSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching a refund */
	public function fetchRefund($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //identifier for transaction to be refunded
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->refundSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}
	
	
	
	
	
	
	/**********************************
	DISPUTE API METHODS
	***********************************/

	/* Method for updating a dispute */
	public function updateDispute($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id of dispute
			'reund_amount' => '', //amount to refund
			//optional
			'uploaded_filename' => '', //filename of attachment returned from upload_url
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disputeSlug.'/'.$this->updateSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}



	/* Method for fetching list of dispute */
	public function getDisputeList($userParams = []){
		
		$defaultParams = [
		
			'from' => '', //A timestamp from which to start listing
			'to' => '', //A timestamp at which to stop listing
			//optional
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			'transaction' => '', //Transaction id
			'status' => '', //Dispute status: awaiting-merchant-feedback | awaiting-bank-feedback | pending | resolved
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disputeSlug.'/'.$this->listSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching a dispute */
	public function fetchDispute($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id of dispute you want to fetch
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disputeSlug.'/'.$this->fetchSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for fetching details of dispute for specific transaction */
	public function fetchSpecificDispute($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //transaction id you want to fetch
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disputeSlug.'/'.$this->disputeSpecificSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	/* Method for adding evidence for a dispute */
	public function addDisputeEvidence($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id of dispute
			'customer_email' => '', //customer email
			'customer_name' => '', //customer name
			'customer_phone' => '', //customer phone
			'service_details' => '', //details of service involved
			//optional
			'delivery_address' => '', //delivery address
			'delivery_date' => '', //delivery date ISO representation(YYYY-MM-DD)
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disputeSlug.'/'.$this->disputeEvidenceSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}
	
	
	



	/* Method for fetching dispute evidence upload_url */
	public function getDisputeUploadUrl($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id of dispute
			'upload_filename' => '', //filename with its extension that you want to upload e.g filename.pdf
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disputeSlug.'/'.$this->disputeUploadUrlSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	



	/* Method for resolving a dispute */
	public function resolveDispute($userParams){
		
		$defaultParams = [
		
			'path_param' => '', //id of dispute
			'resolution' => '', //values: merchant-accepted | declined 
			'message' => '', //reason for resolving
			'refund_amount' => '', //amount to refund
			'uploaded_filename' => '', //filename of attachment returned from upload_url
			//optional
			'evidence' => '', //evidence id for fraud claims
			
		];
		
		$curlPostFields = $this->mergeParams($userParams, $defaultParams, false);
		
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disputeSlug.'/'.$this->disputeResolveSubSlug;
		
		list($rawResponse, $jsonDecodedResponse, $err) = $this->SITE->file_get_contents_curl(array('url' => $curlUrl, 'postFields' => $curlPostFields));
		
		return $jsonDecodedResponse->status;
		/*
		echo'<pre>';
		print_r($rawResponse);
		echo'</pre>';
		*/
		
	}




	/* Method for exporting dispute */
	public function exportDispute($userParams = []){
		
		$defaultParams = [
		
			'from' => '', //A timestamp from which to start listing
			'to' => '', //A timestamp at which to stop listing
			//optional
			'perPage' => self::PER_PAGE,
			'page' => self::PAGE_ID,
			'transaction' => '', //Transaction id
			'status' => '', //Dispute status: awaiting-merchant-feedback | awaiting-bank-feedback | pending | resolved
			
		];
		
		$qstr = $this->mergeParams($userParams, $defaultParams);
		$curlUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->disputeSlug.'/'.$this->disputeExportSubSlug.$qstr;
		
		$jsonDecodedResponse = json_decode($this->SITE->file_get_contents($curlUrl));
		
		if(is_object($jsonDecodedResponse)){
			
			//Informations you can retrieve from the data include: 
			//return $jsonDecodedResponse->data;

		}
		
		return $jsonDecodedResponse;
		
	}



	
	
	//JAVASCRIPT POPUP IMPLEMENTATION
	/*
	var paymentForm = document.getElementById('paymentForm');

	paymentForm.addEventListener('submit', payWithPaystack, false);

	function payWithPaystack() {

	  var handler = PaystackPop.setup({

		key: 'YOUR_PUBLIC_KEY', // Replace with your public key

		email: document.getElementById('email-address').value,

		amount: document.getElementById('amount').value * 100, // the amount value is multiplied by 100 to convert to the lowest currency unit

		currency: 'NGN', // Use GHS for Ghana Cedis or USD for US Dollars

		ref: 'YOUR_REFERENCE', // Replace with a reference you generated

		callback: function(response) {

		  //this happens after the payment is completed successfully

		  var reference = response.reference;

		  alert('Payment complete! Reference: ' + reference);

		  // Make an AJAX call to your server with the reference to verify the transaction
		  
		  $.ajax({

			url: 'http://www.yoururl.com/verify_transaction?reference='+ response.reference,

			method: 'get',

			success: function (response) {

			  // verify the transaction status in response.data.status is successful and proceed to give value

			}

		  });

		},

		onClose: function() {

		  alert('Transaction was not completed, window closed.');

		},

	  });

	  handler.openIframe();
  

	}
		
  */
	


}





?>
