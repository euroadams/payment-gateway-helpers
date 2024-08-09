<?php


class PaymentGateway{
	
	/*********************
	NOTES:
	
	bin => first six characters/digits of a card
	
	*********************/
	
	/*** Generic member variables ***/
	
	const PAGE_ID = 1;
	const PER_PAGE = 30;
	
	protected $DBM;
	protected $ACCOUNT;
	protected $SESS;
	protected $ENGINE;
	protected $SITE;
	protected $INT_HASHER;
	
	
	protected $paymentGatewayName = '';
	protected $successAlertPrefix = '<span class="alert alert-success has-close-btn">';
	protected $errorAlertPrefix = '<span class="alert alert-danger has-close-btn">';
	protected static $paymentFormContextClass = 'payment-forms';
	protected $echoResponse = false;
	protected $minAcceptablePayAmount;
	protected $amountNormalizationVal = 100;
	protected $preferredPayOptions = 'card';
	protected $preferredPayCurrency = '';
	protected $preferredPayCurrencies = array('Naira' => 'NGN', /*'US Dollar' => 'USD'*/);
	protected $gatewayResponseKey = 'payment_gateway_response';
	protected $gatewayResponseDisplayId = 'PaymentGatewayResponse';
	protected $basePaymentGatewayUrl = 'payment-gateway/';
	protected $paymentFormUrl;
	protected $domainPrefixedBasePaymentGatewayUrl;
	protected $cancelTrxSlug = 'cancel-transaction';
	protected $webhookGatewaySlug = 'payment-webhooks';
	protected $cancelActionCallbackUrl;
	protected $internalRdrKey = 'internal_rdr';
	protected $trxMetaKey = 'initialized_transaction_metas';
	protected $payFormSubmitKey = 'process_payment';
	protected $payCardDetailsKey = 'payment_card_details';
	protected $savePayCardKey = 'save_payment_card';
	protected $amountKey = 'amount';
	protected $currencyKey = 'currency';
	protected $trxCustomKey = 'transaction_customization';
	protected $trxCustomizations;
	protected $cardNumberKey = 'card_number';
	protected $cardCvvKey = 'card_cvv';
	protected $cardExpiryMonthKey = 'card_expiry_month';
	protected $cardExpiryYearKey = 'card_expiry_year';
	protected $trxOtpKey = 'trx_otp';
	protected $trxPhoneKey = 'trx_phone';
	protected $trxDobKey = 'trx_dob';
	
	protected $forgetSavedPayCardSlug = 'forget-saved-payment-card';
	
	protected $verifyTrxGiveValSlug = 'verify-transaction-give-value';
	
	protected $createSubSlug = 'create';
	protected $listSubSlug = 'list';
	protected $fetchSubSlug = 'fetch';
	protected $updateSubSlug = 'update';
	protected $verifySubSlug = 'verify';
	protected $initializeSubSlug = 'initialize';
	protected $validateSubSlug = 'validate';
	protected $resolveSubSlug = 'resolve';
	protected $addSubSlug = 'add';
	protected $removeSubSlug = 'remove';
	protected $activateSubSlug = 'activate';
	protected $deactivateSubSlug = 'deactivate';
	protected $enableSubSlug = 'enable';
	protected $disableSubSlug = 'disable';
	protected $pauseSubSlug = 'pause';
	protected $resumeSubSlug = 'resume';
	protected $cancelSubSlug = 'cancel';
	protected $acceptSubSlug = 'accept';
	protected $declineSubSlug = 'decline';
	
	
	
	/*** Constructor ***/
	public function __construct($paymentGatewayName, $paymentFormUrl, $trxCustomizations, $minAcceptablePayAmount){
		
		global $dbm, $ACCOUNT, $ENGINE, $SITE, $INT_HASHER;
		
		$defaultMinAmount = 1000;
		$this->DBM = $dbm;
		$this->ACCOUNT = $ACCOUNT;
		$this->SESS = $ACCOUNT->SESS;
		$this->ENGINE = $ENGINE;
		$this->SITE = $SITE;
		$this->INT_HASHER = $INT_HASHER;
		$this->minAcceptablePayAmount = (($minAcceptablePayAmount > $defaultMinAmount)? $minAcceptablePayAmount : $defaultMinAmount);
		$this->trxCustomizations = $trxCustomizations;
		$this->paymentGatewayName = $paymentGatewayName;
		$this->gatewayResponseKey = $this->paymentGatewayName.'_'.$this->gatewayResponseKey;
		$this->gatewayResponseDisplayId = $this->paymentGatewayName.$this->gatewayResponseDisplayId;
		$this->basePaymentGatewayUrl = $this->paymentGatewayName.'-'.$this->basePaymentGatewayUrl;
		$this->paymentFormUrl = $this->basePaymentGatewayUrl.$paymentFormUrl;
		$this->domainPrefixedBasePaymentGatewayUrl = $this->ENGINE->get_domain().'/'.$this->basePaymentGatewayUrl;
		$this->cancelActionCallbackUrl = $this->domainPrefixedBasePaymentGatewayUrl.$this->cancelTrxSlug;
		
	}
	
	
	
	/*** Destructor ***/
	public function __destruct(){
		
		
	}

	
	
	/************************************************************************************/
	/************************************************************************************
									SITE METHODS
	/************************************************************************************
	/************************************************************************************/
		

	//Method for ensuring object keys are unique
	public function keyUnique($mainKey){

		return $this->paymentGatewayName.'_'.$mainKey;

	}



	/* Method for fetching preferred payment currency select list */
	public function getPreferredPayCurrencyList(){
		
		$currList = '';
		
		foreach($this->preferredPayCurrencies as $currKey => $curr){
			
			$currList .= '<option value="'.$curr.'" >'.$currKey.'</option>';			
			
		}
		
		return '<select class="field" required="required" name="'.$this->currencyKey.'" >
					'.$currList.'
				</select>';
		
	}	


	/* Method for fetching success msg */
	public function getSuccessMsg($trxAmountFmtd, $trxCurrency, $storeType, $msgType='success'){
		
		switch(strtolower($msgType)){
			
			case 'completed': 
			$msg = $this->successAlertPrefix.'
						This transaction was successfully completed and
						the purchased service value has already been delivered.
						<br/>Thank you for your patronage.
					</span>'; break;
			
			default: //'success'
			$msg = $this->successAlertPrefix.'
						<h4>From '.$storeType.'</h4>
						The payment of '.$trxAmountFmtd.' '.$trxCurrency.' was successful. 
						<br/>The purchased service value will be delivered shortly.
						<br/>Thank you for your patronage.
					</span>';
			
		}
		
		return $msg;
		
	}
		


	/* Method for giving values for verified transactions */
	public function giveVerifiedTrxVal($trxAmount, $storeType, $U){
		
		$trxAmountFmtd = $trxAmount;
		
		$adsCampaignStore = strtolower(AdsCampaign::$storeName);
		
		switch(strtolower($storeType)){
			
			case $adsCampaignStore:
			$adsCampaign = new AdsCampaign();
			$trxAmountFmtd = $adsCampaign->creditOrDebitAdvertiser($U->getUsername(), $trxAmount, 'credit', true);
			break;
			
			
		}
		
		return $trxAmountFmtd;
		
	}


	/* Method for setting preferred payment options or channels */
	public function setPreferredPayOptions($payOptions){
		
		$payOptions = (array)$payOptions;
		
		$this->preferredPayOptions = implode(',', $payOptions);
		
	}




	/* Method for setting preferred payment currency */
	public function setPreferredPayCurrency($payCurrency){
		
		$this->preferredPayCurrency = $payCurrency;
		
	}


	/* Method for redirecting back to the page the user was on before initializing payment */
	protected function returnToPaymentCallingPage($alertUser=''){
		
		if($this->echoResponse){
			
			echo $alertUser; 
			exit();
			
		}
		
		$_SESSION[$this->keyUnique($this->gatewayResponseKey)] = $alertUser? $alertUser : '';
		
		if(isset($_SESSION[$this->keyUnique($this->internalRdrKey)])){
			
			$internalRdr = $_SESSION[$this->keyUnique($this->internalRdrKey)];
			 //DO NOT UNSET to ensure customers are always redirected back to the payment gateway calling page
			//unset($_SESSION[$this->keyUnique($this->internalRdrKey)]);
			
		}else
			$internalRdr = '/';
		
		//Free up the transaction metas saved to session before returning to the payment calling page 
		if(isset($_SESSION[$this->keyUnique($this->trxMetaKey)])){
			
			unset($_SESSION[$this->keyUnique($this->trxMetaKey)]);
			
		}
		
		header("Location:".$internalRdr);
		exit();
		

	}
	
	
	
	

	/* Method for fetching input fields */
	public function getInputField($type = 'card'){
		
		switch(strtolower($type)){
			
			case 'otp':
			
				$field = '<div class="field-ctrl">
							<label for="'.($K = 'trx_otp').'">OTP</label>
							<input id="'.$K.'" class="field" type="text" name="'.$this->trxOtpKey.'" />
						</div>';
				break;
				
			case 'phone':
			
				$field = '<div class="field-ctrl">
							<label for="'.($K = 'trx_phone').'">Phone</label>
							<input id="'.$K.'" class="field" type="text" name="'.$this->trxPhoneKey.'" />
						</div>';
				break;
				
			case 'dob':
			
				$field = '<div class="field-ctrl">
							<label for="'.($K = 'trx_dob').'">DOB</label>
							<input id="'.$K.'" class="field" type="text" name="'.$this->trxDobKey.'" />
						</div>';
				break;
			
			default:
			
				$expiryMonthOpt = $expiryYearOpt = '';
			
				for($m = 1; $m <= 12; $m++){
					
					$expiryMonthOpt .= '<option>'.((mb_strlen($m) < 2)? '0'.$m : $m).'</option>';
					
				}
				
				$twoDigitCuurentYear = $this->ENGINE->get_date_safe('', 'y');
				$nYear = ($twoDigitCuurentYear + 30);
				
				for($y = $twoDigitCuurentYear; $y <= $nYear; $y++){
					
					$expiryYearOpt .= '<option>'.$y.'</option>';
					
				}
				
				$field = '<div>
							<div class="field-ctrl">
								<label for="'.($K = 'trx_card_number').'">Card Number</label>
								<input id="'.$K.'" class="field" type="number" name="'.$this->cardNumberKey.'" />
							</div>	
							<div class="align-l"><label>Expiry Date</label></div>
							<div class="row cols-mg field-ctrl">
								<select class="field col-w-3-pull" name="'.$this->cardExpiryMonthKey.'" /><option disabled="disabled">MM</option>'.$expiryMonthOpt.'</select>
								<select class="field col-w-3-pull" name="'.$this->cardExpiryYearKey.'" /><option disabled="disabled">YY</option>'.$expiryYearOpt.'</select>
								<input class="field col-w-3-pull" type="number" name="'.$this->cardCvvKey.'" placeholder="CVV" />
							</div>	
						</div>';
					
		}
		
		return $field;
		
	}
	
	
	
	/* Method for fetching payment gateway buttons */
	public static function getPaymentBtns($optArr=array()){
		
		$type = isset($optArr[$K='type'])? $optArr[$K] : '';
		$renderType = isset($optArr[$K='renderType'])? $optArr[$K] : 'smart';
		$uid = isset($optArr[$K='uid'])? $optArr[$K] : 0;
		
		$customMeta = 'title::'.AdsCampaign::$storeName.',desc::Ads Campaign Credit Purchase';
		$customHeader = 'purchase credits';
		$minAmount = MIN_AD_DEPOSIT;

		$getPaystack = $getFlutterwave = $getMonnify = $getAll = $renderSlide = $renderTab = $renderSmart = false;
		
		switch(strtolower($type)){
			
			case 'paystack' : $getPaystack = true; break;
			case 'flutterwave' : $getFlutterwave = true; break;
			case 'monnify' : $getMonnify = true; break;		
			case 'all' :
			default: $getAll = true;	
			
		}

		switch(strtolower($renderType)){

			case 'tab': $renderTab = true; break;
			case 'slide': $renderSlide = true; break;
			case 'smart':
			default: $renderSmart = true;

		}
		
		$metaArr = array('header' => $customHeader, 'inline' => true, 'renderType' => $renderType);
		
		$paystackPaymentGateway = new PaystackPaymentGateway($customMeta, $minAmount);
		$flutterwavePaymentGateway = new FlutterwavePaymentGateway($customMeta, $minAmount);
		$monnifyPaymentGateway = new MonnifyPaymentGateway($customMeta, $minAmount);
		list($paystackPaymentForm, $paystackPaymentFormToggleBtn, $paystackAlertBox) = $paystackPaymentGateway->getPaymentGatewayForm($uid, array_merge($metaArr, array($K='toggleBtnTxt' => "Pay with Paystack")));
		list($flutterwavePaymentForm, $flutterwavePaymentFormToggleBtn, $flutterwaveAlertBox) = $flutterwavePaymentGateway->getPaymentGatewayForm($uid, array_merge($metaArr, array($K => "Pay with Flutterwave")));
		list($monnifyPaymentForm, $monnifyPaymentFormToggleBtn, $monnifyAlertBox) = $monnifyPaymentGateway->getPaymentGatewayForm($uid, array_merge($metaArr, array($K => "Pay with Monnify")));
		
		if($getAll){
			
			$allPayBtn = '<div '.($K='class="inline-block"').'>'.$paystackPaymentFormToggleBtn.'</div>
						<div '.$K.'>'.$flutterwavePaymentFormToggleBtn.'</div>
						<div '.$K.'>'.$monnifyPaymentFormToggleBtn.'</div>';

			$slideRender = '<div class="'.self::$paymentFormContextClass.'">
								<div data-slide-show-external-pager-target="'.($K='payment-forms-slide').'">
									'.$allPayBtn.'
								</div>
								<div id="'.$K.'" class="slide-show" hidden data-has-external-pager="true" data-scale-full="true" data-pager-numbers="false" data-animate="slideInLeft">
									<div>'.$paystackPaymentForm.'</div>
									<div>'.$flutterwavePaymentForm.'</div>
									<div>'.$monnifyPaymentForm.'</div>
								</div>'.$paystackAlertBox.$flutterwaveAlertBox.$monnifyAlertBox.'	
							</div>';

			$tabRender = '<nav class="nav-base">
								<ul class="nav nav-tabs justified-center justified">			
									<li><a '.($K = 'data-toggle="tab-tab"').'>'.$paystackPaymentFormToggleBtn.'</a></li>
									<li><a '.$K.'>'.$flutterwavePaymentFormToggleBtn.'</a></li>	
									<li><a '.$K.'>'.$monnifyPaymentFormToggleBtn.'</a></li>
								</ul>
								<div class="tab-contents has-tab-close">
									<div class="'.($K = 'tab-content').'">'.$paystackPaymentForm.'</div>
									<div class="'.$K.'">'.$flutterwavePaymentForm.'</div>
									<div class="'.$K.'">'.$monnifyPaymentForm.'</div>
								</div>'.$paystackAlertBox.$flutterwaveAlertBox.$monnifyAlertBox.'
							</nav>';

						
			if($renderSlide)
				$res = $slideRender;

			elseif($renderTab)
				$res = $tabRender;

			elseif($renderSmart)
				$res = '<div class="dsk-platform-dpn">'.$tabRender.'</div><div class="mob-platform-dpn">'.$slideRender.'</div>';

			else			
				$res = '<div class="'.self::$paymentFormContextClass.'">'.
							$allPayBtn.$paystackPaymentForm.$flutterwavePaymentForm.$monnifyPaymentForm.'
						</div>';

		}else{

			if($getPaystack)
				$res = $paystackPaymentForm;

			if($getFlutterwave)
				$res = $flutterwavePaymentForm;

			if($getMonnify)
				$res = $monnifyPaymentForm;

		}
					
		return $res;
		
	}
		
		
	
	
	
	/* Method for fetching payment form */
	public function getPaymentGatewayForm($uid, $metaArr=array()){
		
		global $GLOBAL_rdr, $GLOBAL_delBtn;
		 
		$sessDpn = $this->ENGINE->is_assoc_key_set($metaArr, $K = 'sessDpn')? $this->ENGINE->get_assoc_arr($metaArr, $K) : true;		
		$inline = $this->ENGINE->get_assoc_arr($metaArr, 'inline');
		$renderType = $this->ENGINE->get_assoc_arr($metaArr, 'renderType');
		$header = $this->ENGINE->get_assoc_arr($metaArr, 'header');
		$inlineToggleBtnText = $this->ENGINE->get_assoc_arr($metaArr, 'toggleBtnTxt');
		!$inlineToggleBtnText? ($inlineToggleBtnText = 'Pay Now') : '';
		
		$accSavedCards = '';
		$userSavedCards = $this->getUserSavedPaymentCard($uid);
		!$header? ($header = 'MAKE PAYMENT') : '';
		$cardSavable = in_array($this->paymentGatewayName, array('paystack', 'flutterwave'));
		$optWrapData = ' data-toggle="smartToggler" data-class-targets="'.($saveCardFieldCls = 'save-pay-card-opt').'" data-keep-default="true" data-no-outline="true" ';
		$optWrapData2 = $optWrapData.' data-action="hide" ';
		$optWrapData .= ' data-action="show" ';
		
		foreach($userSavedCards as $row){
			
			$authEmail =  $row["AUTHORIZATION_EMAIL"];
			$trxCardToken = unserialize($row["CARD_AUTHORIZATION"]);
			
			switch(strtolower($this->paymentGatewayName)){
				
				case 'paystack': 
				$cardSignature = $trxCardToken->signature;
				$cardType = $trxCardToken->card_type;
				$bin = $trxCardToken->bin;
				$last4Digits = $trxCardToken->last4;
				$cardIssuer = $trxCardToken->bank;
				break;	
				
				case 'flutterwave': 
				$cardSignature = $row["SIGNATURE"];
				$cardType = $trxCardToken->type;
				$bin = $trxCardToken->first_6digits;
				$last4Digits = $trxCardToken->last_4digits;
				$cardIssuer = $trxCardToken->issuer;
				break;
				
				default: 
				$cardSignature = $cardType = $bin = $last4Digits = $cardIssuer = '';
				
			}
			
			switch($systemDigit = $bin[0]){
				
				case 3: $logoUrl = 'travel_n_ent_card'; break;
				case 4: $logoUrl = 'visa_card.png'; break;
				case 5: $logoUrl = 'master_card.png'; break;
				case 6: $logoUrl = 'discover_card'; break;
				
			};
			
			$creditCardIcon = '<div class="col-w-1-pull">'.$this->SITE->getFA("fa-credit-card").'</div>';
			$creditCardDelIcon = '<div class="col-w-1-pull"><a href="#" class="no-hover-bg forget-pay-card" title="forget this payment card" data-toggle="smartToggler" data-id-targets="'.$cardSignature.'" data-card-token="'.$cardSignature.'" data-url="/'.$this->basePaymentGatewayUrl.$this->forgetSavedPayCardSlug.'">'.$GLOBAL_delBtn.'</a></div>';
			$fieldName = $this->payCardDetailsKey;
			$label = strtoupper($cardType).' XXXX '.$last4Digits;
			$title = 'pay with: '.$cardIssuer.' '.$label;
			$cardAuthMeta = 'cardSignature::'.$cardSignature;
			$accSavedCards .= '<div class="row" id="'.$cardSignature.'">
									<div class="col-w-8-pull">'.
										$this->SITE->getHtmlComponent('iconic-radio', array('label'=>$label, 'title'=>$title,
										'fieldName'=>$fieldName, 'wrapData'=>$optWrapData2, 'value'=>$cardAuthMeta, 'label2R'=>true, 'on'=>false)).'
									</div>	
									'.$creditCardIcon.$creditCardDelIcon.'
								</div>';
			
			
		}
		
		if(isset($_SESSION[$this->keyUnique($this->gatewayResponseKey)])){
			
			$paymentGatewayAlert = $_SESSION[$this->keyUnique($this->gatewayResponseKey)];
			unset($_SESSION[$this->keyUnique($this->gatewayResponseKey)]);
			
		}
		
		
		$panelClass = 'bluex';
		$btnClass = 'success';
		
		if(stripos($inlineToggleBtnText, $this->paymentGatewayName) !== false){
			
			$panelClass = $btnClass = $this->paymentGatewayName;
			
		}
		
		
		$formId = $this->paymentGatewayName.'-payment-form';
		
		$gateResponseAlertBox = '<div id="'.$this->gatewayResponseDisplayId.'">'.(isset($paymentGatewayAlert)? $paymentGatewayAlert : '').'</div>';
		
		$toggleBtn = $inline? '<button class="btn btn-sm btn-'.$btnClass.'" '.($renderType? '' : 'data-toggle="smartToggler" data-id-targets="'.$formId.'" data-close-others-in-context="'.self::$paymentFormContextClass.'"').'>'.$inlineToggleBtnText.'</button>' : '';
		
		$form = '<div class="panel panel-'.$panelClass.' '.(($inline && !$renderType)? 'hide has-close-btn' : '').'" id="'.$formId.'">					
					<h1 class="panel-head page-title align-c">'.$header.'</h1>
					<div class="panel-body">					
						<div class="form-ui form-ui-basic">'.
						(($sessDpn && $this->SESS->getUserId())?
							'<form class="form-fields-classic" action="/'.$this->paymentFormUrl.'?_rdr='.$GLOBAL_rdr.'" method="post" '.($inline? /*'target="_blank"'*/ : '').'>
								<fieldset>
									<div class="field-ctrl">
										<label>CURRENCY*:</label>
										'.$this->getPreferredPayCurrencyList().'
									</div>
									<div class="field-ctrl">
										<label>AMOUNT*:</label>
										<input class="field" required="required" type="number" min="'.$this->minAcceptablePayAmount.'" name="'.($payAmtKey=$this->amountKey).'" value="'.(isset($_POST[$payAmtKey])? $_POST[$payAmtKey] : $this->minAcceptablePayAmount).'" />
										<input type="hidden" name="'.$this->trxCustomKey.'" value="'.$this->trxCustomizations.'" />
									</div>
									<div class="field-ctrl">'.
										($accSavedCards? '
										<div class="hr-dividers">
											<label>CARD OPTION:</label>'.
											$accSavedCards.'
											<div class="row">
												<div class="col-w-8-pull">'.
													$this->SITE->getHtmlComponent('iconic-radio', array('label'=>$label='Bank Card', 'title'=>$label,
													'fieldName'=>$fieldName, 'wrapData'=>$optWrapData, 'value'=>'', 'label2R'=>true, 'on'=>true)).'
												</div>'.$creditCardIcon.'
											</div>
										</div>' : ''
										).'
									</div>
									<div class="field-ctrl">
										'.($cardSavable? $this->SITE->getHtmlComponent('switch-slider', array('label'=>'save bank card for future checkouts:', 'fieldName'=>$this->savePayCardKey, 'wrapClass'=>$saveCardFieldCls, 'on'=>false)) : '').'
										<button class="btn btn-sm btn-'.$btnClass.' btn-block" type="submit" name="'.$this->payFormSubmitKey.'">PAY WITH '.strtoupper($this->paymentGatewayName).' NOW</button>
									</div>
								</fieldset>
							</form>' : $this->SITE->getMeta('not-logged-in-alert')
						)		
						.'</div>
					</div>
				</div>'.($renderType? '' : $gateResponseAlertBox);
				
				
		return $inline? array($form, $toggleBtn, $gateResponseAlertBox) : $form;
		
	}
	
	
	
	

	/* Method for extracting bank name from id, name and code pair */
	public function extractBankName($bankIdNameCodePair){
	
		return $this->ENGINE->get_assoc_arr($this->ENGINE->str_to_assoc($bankIdNameCodePair), 'name');
	
		
	}


	/* Method for extracting bank code from id, name and code pair */
	public function extractBankCode($bankIdNameCodePair){
	
		return $this->ENGINE->get_assoc_arr($this->ENGINE->str_to_assoc($bankIdNameCodePair), 'code');
	
		
	}


	/* Method for extracting bank id from id, name and code pair */
	public function extractBankId($bankIdNameCodePair){
	
		return $this->ENGINE->get_assoc_arr($this->ENGINE->str_to_assoc($bankIdNameCodePair), 'id');
	
		
	}
	
	




	/* Method for filtering $_POST data before sending to API endpoint */
	protected function filterPostData($normalizedAmount = ''){
	
		$filteredData = [];
		
		foreach($_POST as $k => $v){
			
			if(in_array($k, array('uid', 'path_param')))
				continue;
				
			$filteredData[$k] = $v;
			
		}
		
		$normalizedAmount? ($filteredData['amount'] = $normalizedAmount) : '';
		
		return $filteredData;
	
	}
	
	
	



	/* Method for filtering $_GET data before sending to API endpoint */
	protected function filterGetData(){
	
		$filteredData = [];
		
		foreach($_GET as $k => $v){
			
			if(in_array($k, array('pageUrl', 'requestSlug', 'path_param')))
				continue;
				
			$filteredData[$k] = urlencode($v);
			
		}
		
		return $filteredData;
	
	}





	/* Method for merging params to the API endpoint url */
	protected function mergeParams($userParams = [], $defaultParams = [], $buildQstr = true){
	
		return $this->ENGINE->merge_params($userParams, $defaultParams, $buildQstr);
	
	}



	
	
	/* Method for normalizing amount to API payment gateway required format */
	public function normalizeAmount($amount){
	
		return $amount;
		
	}



	/* Method for rolling back API normalized amount */
	public function rollbackNormalizedAmount($normalizedAmount){
	
		return $normalizedAmount;
		
	}

	

	/* Method for building select option list */
	public function buildSelectOptions($dataLists, $metaArr = array()){
		
		$builtList = $dataLists;
		$idKey = $this->ENGINE->is_assoc_key_set($metaArr, ($K='idKey'))? $this->ENGINE->get_assoc_arr($metaArr, $K) : 'id';
		$nameKey = $this->ENGINE->is_assoc_key_set($metaArr, ($K='nameKey'))? $this->ENGINE->get_assoc_arr($metaArr, $K) : 'name';
		$codeKey = $this->ENGINE->is_assoc_key_set($metaArr, ($K='codeKey'))? $this->ENGINE->get_assoc_arr($metaArr, $K) : 'code';
		$fieldName = $this->ENGINE->get_assoc_arr($metaArr, 'fieldName');
		$fieldClass = $this->ENGINE->get_assoc_arr($metaArr, 'fieldClass');
		$meta = $this->ENGINE->get_assoc_arr($metaArr, 'meta');
		
		if(is_object($dataLists)){
			
			switch(strtolower($meta)){
				
				case 'merge_name_code': $mergeNameCode = true; break;
				
			}
			
			$builtList = '';
			$dataLists = $dataLists->data;
			
			foreach($dataLists as $dataList){
				
				$builtList .= '<option '.(isset($mergeNameCode)? 'value="id::'.$dataList->$idKey.',name::'.$dataList->$nameKey.',code::'.$dataList->$codeKey.'"' : '').'>'.$dataList->$nameKey.'</option>';
				
			}
			
			$builtList? ($builtList = '<select class="field '.$fieldClass.'"  name="'.$fieldName.'"><option>select an option</option>'.$builtList.'</select>') : ''; 
			
		}
			
		return $builtList;
		
	}
	





	/* Method for generating unique transaction reference */
	protected function generateReference($len = 10, $recursiveCall = false){
		
		$generatedRef = $this->ENGINE->generate_token($len, true);
		
		///PDO QUERY////////
		
		$sql = "SELECT REFERENCE FROM transactions_awaiting_verification WHERE REFERENCE = ? LIMIT 1";
		$valArr = array($generatedRef);
		$refExistInTable1 = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();
		
		$sql = "SELECT REFERENCE FROM transactions_completed_reference WHERE REFERENCE = ? LIMIT 1";
		$valArr = array($generatedRef);
		$refExistInTable2 = $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();
		
		if($refExistInTable1 || $refExistInTable2)
			$generatedRef = $this->generateReference($len, true);
	
		return $generatedRef;
		
	
	}








	/* Method for checking if a payment card has been saved to database */
	public function isUserPaymentCardSaved($cardSignature, $authEmail){
	
		///PDO QUERY////////
		
		$sql = "SELECT SIGNATURE FROM transaction_saved_payment_cards WHERE (GATEWAY_NAME = ? AND SIGNATURE = ? AND AUTHORIZATION_EMAIL = ?) LIMIT 1";
		$valArr = array($this->paymentGatewayName, $cardSignature, $authEmail);
		return $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();	
		
	}
	
	
	
	



	/* Method for saving a payment card to database */
	public function saveUserPaymentCard($cardSignature, $uid, $authEmail, $trxCardToken){
		
		//@var $trxCardToken is a complex data structure so we serialize to keep the structure intact
		
		///PDO QUERY////////
		
		$sql = "INSERT INTO transaction_saved_payment_cards (GATEWAY_NAME, SIGNATURE, USER_ID, AUTHORIZATION_EMAIL, CARD_AUTHORIZATION) VALUES(?,?,?,?,?)";
		$valArr = array($this->paymentGatewayName, $cardSignature, $uid, $authEmail, serialize($trxCardToken));
		return $this->DBM->doSecuredQuery($sql, $valArr);
		
	}
	
	


	/* Method for forget a saved payment card */
	public function forgetUserPaymentCard($uid, $cardSignature, $hasDeactivationEndpoint = true, $echoResponse = false){
		
		$rawResponse='';
		
		if($hasDeactivationEndpoint){
			
			list($authorizationCode, $authorizationEmail) = $this->getUserSavedPaymentCard($uid, $cardSignature, true);
			
			$userParams = [
			
				'authorization_code' => $authorizationCode
				
			];
		
			list($rawResponse, $jsonDecodedResponse) = $this->deactivateAuthorization($userParams);
			
			$endpointSucc = (isset($jsonDecodedResponse->status) && $jsonDecodedResponse->status);
			
		}
		
		if((isset($endpointSucc) && $endpointSucc) || !$hasDeactivationEndpoint){
			
			///PDO QUERY////////
			$sql = "DELETE FROM transaction_saved_payment_cards WHERE (GATEWAY_NAME = ? AND USER_ID = ? AND SIGNATURE = ?) LIMIT 1";
			$valArr = array($this->paymentGatewayName, $uid, $cardSignature);
			$dbSucc = $this->DBM->doSecuredQuery($sql, $valArr);
			$rawResponse? '' : ($rawResponse = $dbSucc);
			
		}
		
		if($echoResponse){
			
			echo $rawResponse;
			exit();
			
		}
		
	}
	

	



	/* Method for fetching users saved payment cards from database */
	public function getUserSavedPaymentCard($uid, $cardSignature = '', $retAuthInfoArr = false, $limit = 6){
		
		$unpack = false;
		$subCnd='';
		
		///PDO QUERY////////
		$valArr = array($this->paymentGatewayName, $uid);
		
		if($cardSignature){
			
			$valArr[] = $cardSignature;
			$limit = 1;
			$subCnd = 'AND SIGNATURE = ?';
			$unpack = true;
		
		} 
		
		$sql = "SELECT * FROM transaction_saved_payment_cards WHERE (GATEWAY_NAME = ? AND USER_ID = ? ".$subCnd.") ORDER BY DATE DESC LIMIT ".$limit;
		$stmt = $this->DBM->doSecuredQuery($sql, $valArr);
		$rows = $this->DBM->fetchRows($unpack);
		
		if($retAuthInfoArr){
			
			if(isset($rows["CARD_AUTHORIZATION"])){
				
				$card = unserialize($rows["CARD_AUTHORIZATION"]);
				$authEmail = $rows["AUTHORIZATION_EMAIL"];
				
				switch(strtolower($this->paymentGatewayName)){
					
					case 'paystack': 
					$retArr = array($card->authorization_code, $authEmail); 
					break;	
					
					case 'flutterwave': 
					$retArr = array($card->token, $authEmail, $card->country); 
					break;
					
					default: $retArr = ['', '', ''];	
					
				}
				
			}else
				$retArr = ['', '', ''];
				
			return $retArr;	
			
		}
		
		return $rows;
		
	}
	
	
	



	/* Method for checking if a successful payment transaction reference has been given a value and logged to database */
	public function isPaymentReferenceLogged($uid, $reference, $customerEmail){
	
		///PDO QUERY////////
		
		$sql = "SELECT REFERENCE FROM transactions_completed_reference WHERE (GATEWAY_NAME = ? AND REFERENCE = ? ".($uid? "AND USER_ID = ? AND CUSTOMER_EMAIL = ?" : "").") LIMIT 1";
		$valArr = array($this->paymentGatewayName, $reference);
		if($uid){
			
			$valArr[] = $uid;
			$valArr[] = $customerEmail;
			
		}
		
		return $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();	
		
	}
	
	
	
	




	/* Method for logging a payment transaction reference to database */
	public function logPaymentReference($storeType, $reference, $gatewayTrxId, $uid, $customerId, $customerEmail, $amount){
	
		///PDO QUERY////////
		
		$sql = "INSERT INTO transactions_completed_reference (GATEWAY_NAME, STORE_NAME, REFERENCE, GATEWAY_TRANX_ID, USER_ID, CUSTOMER_ID, CUSTOMER_EMAIL, AMOUNT) VALUES(?,?,?,?,?,?,?,?)";
		$valArr = array($this->paymentGatewayName, $storeType, $reference, $gatewayTrxId, $uid, $customerId, $customerEmail, $amount);
		return $this->DBM->doSecuredQuery($sql, $valArr);
		
	}
	
	


	

	/* Method for checking if a payment transaction awaiting verification and value has already been logged to database */
	public function isPaymentAwaitingVerifLogged($trxReference, $uid, $valueAmount){
	
		///PDO QUERY////////
		
		$sql = "SELECT REFERENCE FROM transactions_awaiting_verification WHERE (GATEWAY_NAME = ? AND REFERENCE = ? AND USER_ID = ? AND VALUE_AMOUNT = ?) LIMIT 1";
		$valArr = array($this->paymentGatewayName, $trxReference, $uid, $valueAmount);
		return $this->DBM->doSecuredQuery($sql, $valArr)->fetchColumn();	
		
	}
	
	

	/* Method for logging a payment transaction that is yet to be verified and given value */
	public function logPaymentAwaitingVerif($trxCustomTitle, $customerEmail, $trxReference, $uid, $valueAmount, $trxCurrency, $saveCard = false){
	
		///PDO QUERY////////
		
		$sql = "INSERT INTO transactions_awaiting_verification (GATEWAY_NAME, STORE_NAME, CUSTOMER_EMAIL, REFERENCE, USER_ID, VALUE_AMOUNT, TRANX_CURRENCY, SAVE_CARD) VALUES(?,?,?,?,?,?,?,?)";
		$valArr = array($this->paymentGatewayName, $trxCustomTitle, $customerEmail, $trxReference, $uid, $valueAmount, $trxCurrency, intval($saveCard));
		return $this->DBM->doSecuredQuery($sql, $valArr);
		
	}
	

	/* Method for expunging a payment transaction that has been verified and given value */
	public function expungeFromPaymentAwaitingVerif($trxReference, $uid, $valueAmount){
	
		///PDO QUERY////////
		
		$sql = "DELETE FROM transactions_awaiting_verification WHERE (GATEWAY_NAME = ? AND REFERENCE = ? AND USER_ID = ? AND VALUE_AMOUNT = ?) LIMIT 1";
		$valArr = array($this->paymentGatewayName, $trxReference, $uid, $valueAmount);
		return $this->DBM->doSecuredQuery($sql, $valArr);
		
	}
	
	
	

	/* Method for fetching from database by reference meta data of a payment awaiting verification */
	public function getPaymentAwaitingVerif($trxReference){
	
		///PDO QUERY////////
		
		$sql = "SELECT * FROM transactions_awaiting_verification WHERE (GATEWAY_NAME = ? AND REFERENCE = ?) LIMIT 1";
		$valArr = array($this->paymentGatewayName, $trxReference);
		return $this->DBM->doSecuredQuery($sql, $valArr, 'chain')->fetchRow();
		
	}
	
	
	
	

	/* Method for logging webhook events to database */
	public function logWebhookEvent($rawBody){
	
		///PDO QUERY////////
		
		$sql = "INSERT INTO webhook_events_log (OWNER, RAW_BODY) VALUES(?,?)";
		$valArr = array($this->paymentGatewayName, $rawBody);
		return $this->DBM->doSecuredQuery($sql, $valArr);
		
	}
	
	
	


}


?>