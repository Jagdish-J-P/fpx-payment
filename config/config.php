<?php

/*
	 * You can place your custom package configuration in here.
	 */
return [
	/**
	 * The Merchant Exchange ID
	 *
	 * You need to contact FPX to request your id.
	 */
	'exchange_id' => env('FPX_EXCHANGE_ID'),

	/**
	 * The Merchant Seller ID
	 */
	'seller_id' => env('FPX_SELLER_ID'),

	/**
	 * indirect url used by FPX to direct the user back to your platform after a transaction is completed
	 *
	 * Example: https://localhost.test/fpx/payment/callback
	 */
	'indirect_url' => env('FPX_INDIRECT_URL'),

	/**
	 * The indirect url path without the domain and scheme
	 *
	 * Example: fpx/payment/callback
	 */
	'indirect_path' => env('FPX_INDIRECT_PATH'),

	/**
	 * Direct event url used by FPX to send direct messages to your app without the need for users actions
	 *
	 * Example: https://localhost.test/fpx/payment/direct-callback
	 */
	'direct_url' => env('FPX_DIRECT_URL'),

	/**
	 * The indirect url path without the domain and scheme
	 *
	 * Example: fpx/payment/direct-callback
	 */
	'direct_path' => env('FPX_DIRECT_PATH'),

	/**
	 * Middleware
	 */
	'middleware' => ['web'],

	/**
	 * Minimum acceptable amount
	 */
	'min_amount' => 1,

	/**
	 * Maximum acceptable amount
	 */
	'max_amount' => 30000,

	/**
	 * FPX Version
	 *
	 * Ensure that you are using the latest version by checking FPX documentation at
	 * https://fpxexchange.myclear.org.my:8443/MerchantIntegrationKit/
	 */
	'version' => env('FPX_VERSION', '7.0'),

	/**
	 * The Default Currency
	 *
	 * set the default currency code used for transaction. You can reach out to FPX to
	 * find out what other currency are supported
	 */
	'currency' => env('FPX_CURRENCY', 'MYR'),

	/**
	 * Merchant Bank Code
	 *
	 * The merchant bank code, the default is '01' but you might need to check with FPX to make sure your account
	 * does not have a different bank code
	 */
	'bank_code' => env('FPX_BANK_CODE', '01'),

	/**
	 *
	 * Optional
	 * Is response received from FPX required to be verified? (true/false)
	 * Ensure that value of should_verify_response must be true in production Environment
	 *
	 */
	'should_verify_response' => env('FPX_VERIFY_RESPONSE', true),

	/**
	 * Certificate Paths
	 *
	 */
	'certificates' => [
		'uat' => [
			'disk' => 'local', // S3 or Local. Don't put your certificate in public disk
			'dir' => '/fpx/uat',
		],
		'production' => [
			'disk' => 'local', // S3 or Local. Don't put your certificate in public disk
			'dir' => '/fpx/prod',
		]
	],

	/**
	 * Urls List
	 *
	 * the list of urls for uat and production
	 *
	 * each url is used for a specific request, please refer to documentation to learn more about when to use
	 * each url.
	 *
	 * https://fpxexchange.myclear.org.my:8443/MerchantIntegrationKit/files/Merchant%20Interface%20Specification%20for%20FPX%20V4.5.pdf
	 *
	 */
	'urls' => [
		'uat' => [
			'auth_request' => 'https://uat.mepsfpx.com.my/FPXMain/seller2DReceiver.jsp',
			'bank_enquiry' => 'https://uat.mepsfpx.com.my/FPXMain/RetrieveBankList',
			'seller_request_cancel' => 'https://uat.mepsfpx.com.my/FPXMain/FPXMain/sellerReqCancel.jsp',
			'auth_request_bulk' => 'https://uat.mepsfpx.com.my/FPXMain/B2B2Payment',
			'auth_enquiry' => 'https://uat.mepsfpx.com.my/FPXMain/sellerNVPTxnStatus.jsp',
		],
		'production' => [
			'auth_request' => 'https://www.mepsfpx.com.my/FPXMain/seller2DReceiver.jsp',
			'bank_enquiry' => 'https://www.mepsfpx.com.my/FPXMain/RetrieveBankList',
			'seller_request_cancel' => 'https://www.mepsfpx.com.my/FPXMain/FPXMain/sellerReqCancel.jsp',
			'auth_request_bulk' => 'https://www.mepsfpx.com.my/FPXMain/B2B2Payment',
			'auth_enquiry' => 'https://www.mepsfpx.com.my/FPXMain/sellerNVPTxnStatus.jsp',
		],
	]
];
