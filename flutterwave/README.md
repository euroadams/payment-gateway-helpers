## Usage Guide For Flutterwave :
<p><a href="https://www.flutterwave.com/ng/" target="_blank" rel="noreferrer"><img src="/logos/flutterwave-2.svg" title="Flutterwave" alt="Flutterwave logo" width="100" height="auto"/></a></p>

> NOTE: All sample example codes are for demonstration purpose only. It's recommended to optimize it before using in production environment.

#### Configuring API Key

First define a constant `FLWV_API_KEY` to hold your flutterwave API keys and assign it an associative array with key/value pairs as follows 

```php
<?php

define("FLWV_API_KEYS", array(

'test_sk' => 'your flutterwave test secret key',
'test_pk' => 'your flutterwave test public key',
'test_encrypt' => 'your flutterwave test encrypt'
		
'live_sk' => 'your flutterwave live secret key',
'live_pk' => 'your flutterwave live public key',
'live_encrypt' => 'your flutterwave live encrypt',
'secret_hash' => 'your flutterwave secret hash',
		
));
?>
```


