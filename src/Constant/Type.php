<?php

namespace JagdishJP\FpxPayment\Constant;

/**
 * This class contains some code and message types used by FPX.
 * We try to include a defenition for each message, so you don't have to keep going
 * throw the documentation everytime you need something.
 *
 *
 * Not every application will require every message type, so make sure you know
 * which one to use.
 *
 *
 * Direct = Host to Host request type i.e Webhook event
 * Indirect = host to Browser request type i.e redirect event
 */
class Type {

	public const FLOW_B2C = '01';
	public const FLOW_B2B1 = '02';
	public const FLOW_B2B2 = '03';

	public const CODE_AR = 'AR';
	public const AUTHORIZATION_REQUEST = 'Authorization Request';

	public const CODE_DIRECT_AC = 'Direct AC';
	public const DIRECT_AUTHORIZATION_CONFIRM = 'Direct Authorization Confirmation';
	public const CODE_INDIRCT_AC = 'Indirect AC';
	public const INDIRCT_AUTHORIZATION_CONFIRM = 'Indirect Authorization Confirmation';

	public const CODE_AE = 'AE';
	public const AUTHORIZATION_ENQUIRY = 'Authorization Enquiry';

	public const CODE_BE = 'BE';
	public const BANK_LIST_ENQUIRY = 'Bank List Enquiry';

	public const CODE_BC = 'BC';
	public const BANK_LIST_CONFIRMATION = 'Bank List Confirmation';

	public const CODE_AD = 'AD';
	public const AUTHORIZATION_FOR_E_MANDATE = 'Authorization for E-Mandate';

	public const CODE_AF = 'AF';
	public const AUTHORIZATION_REQUEST_FOR_BULK_REFUND = 'Authorization Request For Bulk Refund';

	public const CODE_DIRECT_AB = 'Direct AB';
	public const DIRECT_BULK_REFUND_AUTHORIZATION = 'Direct Bulk Refund Authorization';
	public const CODE_INDIRECT_AB = 'Indirect AB';
	public const INDIRCT_BULK_REFUND_AUTHORIZATION = 'Indirect Bulk Refund Authorization';

	public const CODE_AQ = 'AQ';
	public const AUTHORIZATION_ENQUIRY_FOR_BULK_REFUND = 'Authorization Enquiry For Bulk Refund';

	public const CODE_AX = 'AX';
	public const AUTHORIZATION_REQUEST_FOR_TRANSACTION_CANCELATION_BY_MERCHANT = 'Authorization Request For Transaction Cancelation By Merchant';

	public const CODE_AXA = 'AXA';
	public const AUTHORIZATION_CONFIRMATION_FOR_TRANSACTION_CANCELATION_BY_MERCHANT = 'Authorization Confirmation For Transaction Cancelation By Merchant';


	public const TYPES_LIST = [
		self::CODE_AR => self::AUTHORIZATION_REQUEST,
		self::CODE_DIRECT_AC => self::DIRECT_AUTHORIZATION_CONFIRM,
		self::CODE_INDIRCT_AC => self::INDIRCT_AUTHORIZATION_CONFIRM,
		self::CODE_AE => self::AUTHORIZATION_ENQUIRY,
		self::CODE_BE => self::BANK_LIST_ENQUIRY,
		self::CODE_BC => self::BANK_LIST_CONFIRMATION,
		self::CODE_AD => self::AUTHORIZATION_FOR_E_MANDATE,
		self::CODE_AF => self::AUTHORIZATION_REQUEST_FOR_BULK_REFUND,
		self::CODE_DIRECT_AB => self::DIRECT_BULK_REFUND_AUTHORIZATION,
		self::CODE_INDIRECT_AB => self::INDIRCT_BULK_REFUND_AUTHORIZATION,
		self::CODE_AQ => self::AUTHORIZATION_ENQUIRY_FOR_BULK_REFUND,
		self::CODE_AX => self::AUTHORIZATION_REQUEST_FOR_TRANSACTION_CANCELATION_BY_MERCHANT,
		self::CODE_AXA => self::AUTHORIZATION_CONFIRMATION_FOR_TRANSACTION_CANCELATION_BY_MERCHANT,
	];
}
