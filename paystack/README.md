## Usage Guide For Paystack :
<p><a href="https://www.paystack.com/" target="_blank" rel="noreferrer"><img src="/logos/paystack-2.svg" title="Paystack" alt="Paystack logo" width="100" height="auto"/></a></p>

<img src="https://github.com/euroadams/euroadams/blob/master/assets/public/work-samples/paystack.jpg" alt="Paystack Sample" width="auto" height="auto"/>

> [!NOTE]
> All sample example codes are for demonstration purpose only. It's recommended to optimize it before using in production environment.

#### Configuring the API Key

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


### Instantiating the Gateway :

The `PaystackPaymentGateway` can be instantiated using the standard PHP syntax as follows

```php

<?php

$paystack = new PaystackPaymentGateway($trxCustomizations = 'title::Store,desc::Service Payment,logo::', $minAcceptablePayAmount = 0);


/****
 *  PARAMETER DEFINITION :
 * 
 *  @param $trxCustomizations => A comma-separated key::value pair string that defines the custom properties of 
 *  your store.
 *  => each property is separated from the other by a comma (,) and keys are separated from values by double-
 *  colon (::)
 *  => The supported property keys are defined as follows: 
 *         1. title => its value defines the title name of your store (default value: Store)
 *         2. desc => its value defines the description of your store (default value: Service Payment)
 *         3. logo => its value defines the url of your store logo (default value: '')
 * 
 *  @param $minAcceptablePayAmount => The minimum acceptable payment amount for your store
 *  => defaults to 0
 *  
 ****/

?>

```

### Invoking the Payment Form :

The payment gateway form can be invoked by calling the static method `getPaymentBtns()` as shown below

```php

<?php

// Invoking Paystack Payment Form
PaymentGateway::getPaymentBtns($optArr = array('type' => 'paystack', 'uid' => 2398) );

// Invoking All The Payment Forms
PaymentGateway::getPaymentBtns($optArr = array('type' => 'all', 'uid' => 2398) );


/****
 *  PARAMETER DEFINITION :
 * 
 *  @param $optArr => An array of key-value options
 *  => The supported option keys are defined as follows: 
 *         1. type => A string that defines the payment form type to fetch. 
 *            => supported values are [ paystack | flutterwave | monnify | all ]
 *            => defaults to 'all'
 *         2. renderType => This option is only useful when 'type' key above is 'all'
 *                       => it defines how all the payment forms are render on the screen 
 *                       => supported values are [ slide | tab | smart]
 *                       => slide: render the forms as slides
 *                       => tab: render the forms as tabs
 *                       => smart: render the forms smartly as slides or tabs depending on platform/screen size
 *                       => defaults to 'smart'                     
 *         3. uid => Unique database id of the session user making the payment
 *  
 ****/

?>

```


### MORE DOCUMENTATION COMING SOON....


