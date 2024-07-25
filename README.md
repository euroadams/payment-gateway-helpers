# payment-gateway-helpers
A PHP based class for handling all endpoint calls for paystack, flutterwave and monnify API

> NOTE: All sample example codes are for demonstration purpose only. It's recommended to optimize it before using in production environment.


#### How to Use For Paystack &nbsp;<a href="https://www.paystack.com/" target="_blank" rel="noreferrer"><img src="/logos/paystack.svg" title="Paystack" alt="Paystack logo" width="20" height="20"/></a> :
See [paystack/README.md][l1]
First define your paystack API keys in an associative array with variable `$PSTK_API_KEYS` and key/value pairs as follows 

```
$PSTK_API_KEYS = array(

'test_sk' => 'your paystack test secret key',
'test_pk' => 'your paystack test public key', 
		
'live_sk' => 'your paystack live secret key',
'live_pk' => 'your paystack live public key',
		
);

```

#### How to Use For Flutterwave &nbsp;<a href="https://www.paystack.com/" target="_blank" rel="noreferrer"><img src="/logos/flutterwave.svg" title="Flutterwave" alt="Flutterwave logo" width="20" height="20"/></a> :

#### How to Use For Monnify &nbsp;<a href="https://www.paystack.com/" target="_blank" rel="noreferrer"><img src="/logos/monnify.svg" title="Monnify" alt="Monnify logo" width="20" height="20"/></a> :
   
[link-author]: https://linkedin.com/in/adoagwai-godswill
[l1]: <https://github.com/euroadams/payment-gateway-helpers/tree/master/paystack/README.md>
