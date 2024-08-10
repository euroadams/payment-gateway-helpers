# payment-gateway-helpers
A PHP based class for handling all endpoint calls for paystack, flutterwave and monnify API
<div>
<a href="https://www.paystack.com/" target="_blank" rel="noreferrer"><img src="/logos/paystack.svg" title="Paystack" alt="Paystack logo" width="20" height="20"/></a>&nbsp;&nbsp;
<a href="https://www.flutterwave.com/ng/" target="_blank" rel="noreferrer"><img src="/logos/flutterwave.svg" title="Flutterwave" alt="Flutterwave logo" width="20" height="20"/></a>&nbsp;&nbsp;
<a href="https://monnify.com/" target="_blank" rel="noreferrer"><img src="/logos/monnify.svg" title="Monnify" alt="Monnify logo" width="20" height="20"/></a> 
</div>

## Sample For Paystack :
<p>
<a href="https://www.paystack.com/" target="_blank" rel="noreferrer"><img src="/logos/paystack-2.svg" title="Paystack" alt="Paystack logo" width="100" height="auto"/></a> 
</p>
<img src="https://github.com/euroadams/euroadams/blob/master/assets/public/work-samples/paystack.jpg" alt="Paystack Sample" width="auto" height="auto"/>

:point_right: Read the complete usage guide [here][l1] 

## Sample For Flutterwave :
<p>
<a href="https://www.flutterwave.com/ng/" target="_blank" rel="noreferrer"><img src="/logos/flutterwave-2.svg" title="Flutterwave" alt="Flutterwave logo" width="100" height="auto"/></a> 
</p>
<img src="https://github.com/euroadams/euroadams/blob/master/assets/public/work-samples/flutterwave.jpg" alt="Flutterwave Sample" width="auto" height="auto"/>

:point_right: Read the complete usage guide [here][l2] 

## Sample For Monnify :
<p>
<a href="https://monnify.com/" target="_blank" rel="noreferrer"><img src="/logos/monnify-2.svg" title="Monnify" alt="Monnify logo" width="100" height="auto"/></a> 
</p>
<img src="https://github.com/euroadams/euroadams/blob/master/assets/public/work-samples/monnify.jpg" alt="Monnify Sample" width="auto" height="auto"/>

:point_right: Read the complete usage guide [here][l3] 

## Invoking the Gateway Forms 
The collective payment gateway forms can be invoked by calling the static method `getPaymentBtns()` as shown below

```php

<?php

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
   
[link-author]: https://linkedin.com/in/adiagwai-godswill
[l1]: <https://github.com/euroadams/payment-gateway-helpers/tree/main/paystack/README.md>

[l2]: <https://github.com/euroadams/payment-gateway-helpers/tree/main/flutterwave/README.md>

[l3]: <https://github.com/euroadams/payment-gateway-helpers/tree/main/monnify/README.md>



