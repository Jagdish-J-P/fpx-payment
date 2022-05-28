# Very short description of the package

This package provides laravel implementations for Paynet FPX services.

## Installation

You can install the package via composer:

```bash
composer require jagdish-j-p/fpx-payment
```

Then run the publish command to publish the config files and support controller

```bash
php artisan fpx:publish
```

This will generate the following files

- The config file with default setup for you to override `fpx.php`
- The controller that will receive payment response and any host-to-host events `Http/Controllers/FPX/Controller.php`
- The assets in public directory.
- The view file with default html for you to override `payment.blade.php`. Note do not change form action URL `fpx.payment.auth.request`.

## Setup

1. Add your redirect urls and your Seller and Exchange Id to the `.env` file.

```php
FPX_INDIRECT_URL=https://app.test/payments/fpx/callback
FPX_INDIRECT_PATH=payments/fpx/callback
FPX_DIRECT_URL=https://app.test/payments/fpx/direct-callback
FPX_DIRECT_PATH=payments/fpx/direct-callback

FPX_EXCHANGE_ID=
FPX_SELLER_ID=
```

2. You can skip this steps, if you have already generated CSR. Visit `fpx/csr/request` path in browser to generate CSR.
   
	 `http://app.test/fpx/csr/request`
   
	 Fill the form and click on `GENERATE`. On right side textarea will be generated with openSSL code.
   Download openSSL from `https://www.openssl.org/` if you don't have installed it.
   Run openssl code to generate CSR. Submit this CSR to FPX service provider to get the Exchange Certificates.

3. After generating your certificates add them to your app. By default, we look for the certificates inside the following directives. 
	 Create `fpx/uat` and `fpx/prod` directories in `storage/app/public` directory and paste your certificates there. You can find UAT certificate in `uat certificate/fpxuat_current.cur` rename it with your Exchange ID and place it in mentioned UAT directory.

```php
'certificates' => [
	'uat' => [
		'disk' => 'local', // S3 or Local. Don't put your certificate in public disk
		'dir' => '/public/fpx/uat',
	],
	'production' => [
		'disk' => 'local', // S3 or Local. Don't put your certificate in public disk
		'dir' => '/public/fpx/prod',
	]
],
```

You can override the defaults by updating the config file.

3. Run migration to add the banks and fpx_transactions table

```bash
php artisan migrate
```

## Usage

1. First run the following command to seed the banks list.

```bash
php artisan fpx:banks
```

you should schedule the fpx:banks Artisan command to run daily:

```php
$schedule->command('fpx:banks')->daily();
```

2. Add one the `x-fpx-payment` component with the following attributes

```php
 <x-fpx-payment
		:reference-id="$invoice->id"
		:datetime="$invoice->created_at->format('Ymdhms')"
		:amount="$invoice->total"
		:customer-name="$company->name"
		:customer-email="$company->owner->email"
		:product-description="Salary Invoice"
		:class="css class name for styling button">
```

During testing, you can use the `test-mode` attribute to override the provided amount to 'MYR 1.00'

```php
 <x-fpx-payment
		:reference-id="$invoice->id"
		:datetime="$invoice->created_at->format('Ymdhms')"
		:amount="$invoice->total"
		:customer-name="$company->name"
		:customer-email="$company->owner->email"
		:product-description="Salary Invoice"
		:class="css class name for styling button"
		test-mode>
```

3. Handle the payment response in `Http/Controllers/FPX/Controller.php`

```php

	/**
	 * This will be called after the user approve the payment
	 * on the bank side
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function callback(Request $request) {
		$response = $request->handle();

		// Update your order status
	}

	/**
	 * This will handle any direct call from FPX
	 *
	 * @param Request $request
	 * @return string
	 */
	public function webhook(Request $request) {
		$response = $request->handle();

		// Update your order status

		return 'OK';
	}
```

4. Check Status of all pending transactions using command

```bash
php artisan fpx:payment-status
```

5. Check Status of specific transaction using command pass comma saperated order reference ids.

```bash
php artisan fpx:payment-status reference_id1,reference_id2,reference_id3
```

6. Check transaction status and Bank list from Controller

```php

use JagdishJP/FpxPayment/Fpx;

/**
 * Returns status of transaction
 * 
 * @param string $reference_id reference order id
 * @return array
 */
$status = Fpx::getTransactionStatus($reference_id);


/**
 * returns collection of bank_id and name 
 * 
 * @param bool $getLatest (optional) pass true to get latest banks 
 * @return \Illuminate\Support\Collection
 */
$banks = Fpx::getBankList(true);

```

7. API for transaction status 

```
http://app.test/api/fpx/transaction/status/$reference_id
```

## Web Integration

You can visit <a href='http://app.test/fpx/initiate/payment'>http://app.test/fpx/initiate/payment</a> for the payment flow demo of web integration.

## Mobile App Integration

- Append `app` parameter in the URL to check the demo. [http://app.test/fpx/initiate/payment/app](http://app.test/fpx/initiate/payment/app) 
- This will print JSON response after completion of transaction to integrate with mobile app.

Follow these steps to integrate in mobile application.

### Request Details
Open [http://app.test/fpx/initiate/payment/app](http://app.test/fpx/initiate/payment/app) in web view with POST method and POST below parameters.

```
response_format = "JSON"
reference_id = unique order reference id
customer_name = name of the buyer/customer
amount = amount to be charged
customer_email = email id of customer
remark = remarks for the transaction
additional_params = any additional parameters you want to pass
```

### Response 
You must use `response` field to display receipt. `fpx_response` is added if you need any extra details.

`response.status` will be succeeded, failed or pending.

```php
{
  "response": {
    "status": "succeeded/failed/pending",
    "message": "Payment is successfull",
    "transaction_id": "",
    "reference_id": "",
    "amount": "",
    "transaction_timestamp": "",
    "buyer_bank_name": "",
    "response_format": "JSON",
    "additional_params": "type=123"
  },
  "fpx_response": {
    "fpx_debitAuthCode": "",
    "fpx_debitAuthNo": "",
    "fpx_sellerExId": "",
    "fpx_creditAuthNo": "",
    "fpx_buyerName": "",
    "fpx_buyerId": null,
    "fpx_sellerTxnTime": "",
    "fpx_sellerExOrderNo": "",
    "fpx_makerName": "",
    "fpx_buyerBankBranch": "",
    "fpx_buyerBankId": "",
    "fpx_msgToken": "",
    "fpx_creditAuthCode": "",
    "fpx_sellerId": "",
    "fpx_fpxTxnTime": "",
    "fpx_buyerIban": null,
    "fpx_sellerOrderNo": "",
    "fpx_txnAmount": "",
    "fpx_fpxTxnId": "",
    "fpx_checkSum": "",
    "fpx_msgType": "",
    "fpx_txnCurrency": "",
  }
}
```

You can also override `payment.blade.php` with your custom design to integrate with your layout. but do not change `name` attribute of html controls and `action` URL of form.

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email jagdish.j.ptl@gmail.com instead of using the issue tracker.

## Credits

- [Jagdish-J-P](https://github.com/jagdish-j-p)
- [AIMEN.S.A.SASI](https://github.com/aimensasi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
