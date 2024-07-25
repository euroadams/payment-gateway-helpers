#### Usage Guide For Paystack :

<p><a href="https://www.paystack.com/" target="_blank" rel="noreferrer"><img src="/logos/paystack-2.svg" title="Paystack" alt="Paystack logo" width="200" height="auto"/></a></p>

> NOTE: All sample example codes are for demonstration purpose only. It's recommended to optimize it before using in production environment.

First define your paystack API keys in an associative array with variable `$PSTK_API_KEYS` and key/value pairs as follows 

```
<?php
$PSTK_API_KEYS = array(

'test_sk' => 'your paystack test secret key',
'test_pk' => 'your paystack test public key', 
		
'live_sk' => 'your paystack live secret key',
'live_pk' => 'your paystack live public key',
		
);

?>

```

