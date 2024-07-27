## Usage Guide For Paystack :
<p><a href="https://www.paystack.com/" target="_blank" rel="noreferrer"><img src="/logos/paystack-2.svg" title="Paystack" alt="Paystack logo" width="100" height="auto"/></a></p>

<img src="https://github.com/euroadams/euroadams/blob/master/assets/public/work-samples/paystack.jpg" alt="Paystack Sample" width="auto" height="auto"/>

> NOTE: All sample example codes are for demonstration purpose only. It's recommended to optimize it before using in production environment.

#### Configuring API Key

First define a constant `PSTK_API_KEY` to hold your paystack API keys and assign it an associative array with key/value pairs as follows 

```php
<?php

    define("PSTK_API_KEYS", array(

        'test_sk' => 'your paystack test secret key',
        'test_pk' => 'your paystack test public key', 
                
        'live_sk' => 'your paystack live secret key',
        'live_pk' => 'your paystack live public key',
                
        )
    );
?>
```

