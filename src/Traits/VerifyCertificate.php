<?php

namespace JagdishJP\FpxPayment\Traits;

use JagdishJP\FpxPayment\Exceptions\InvalidCertificateException;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

trait VerifyCertificate {


	/**
	 * Verify that the received data is valid.
	 *
	 * @param string $checkSum
	 * @param string $data
	 *
	 * @throws \JagdishJP\FpxPayment\Exceptions\InvalidCertificateException
	 * @return bool
	 */
	public function verifySign($checkSum, $data): bool {
		list($disk, $dir) = $this->getCertLocation();

		$signature = $this->decodeSignature($checkSum);
		$certificate = Storage::disk($disk)->get($dir . '/' . $this->exchangeId.'.cer');

		if ($this->isExpired($certificate) || !$this->isValid($certificate, $signature, $data)) {
			throw new InvalidCertificateException;
		}

		return true;
	}


	/**
	 * Verify that the certificate is valid
	 *
	 * @param string $certificate
	 * @param string $signature
	 * @param array|string $data
	 *
	 * @return bool
	 */
	public function isValid($certificate, $signature, $data): bool {
		$key = openssl_pkey_get_public($certificate);
		$result = openssl_verify($data, $signature, $key);

		return $result == 1;
	}


	/**
	 * Verify that the certificate is not yet expired.
	 *
	 * @param string $certificate
	 *
	 * @throws \JagdishJP\FpxPayment\Exceptions\InvalidCertificateException
	 * @return bool
	 */
	public function isExpired($certificate): bool {
		$info = openssl_x509_parse($certificate);
		$expiryDate = Carbon::createFromTimestampUTC($info['validTo_time_t']);

		return $expiryDate->isPast();
	}

	/**
	 * Get the certificate public key file
	 *
	 * @return string
	 */
	public function getPublicKeyCert(): string {
		list($disk, $dir) = $this->getCertLocation();

		$filename = $this->exchangeId . '.key';

		return Storage::disk($disk)->get($dir . '/' . $filename);
	}

	/**
	 * Return the location of the certificate in the file system
	 * based on the current environment
	 *
	 * @return array
	 */
	public function getCertLocation(): array {
		$disk = Config::get('fpx.certificates.uat.disk');
		$dir = Config::get('fpx.certificates.uat.dir');


		if (App::environment('production')) {
			$disk = Config::get('fpx.certificates.production.disk');
			$dir = Config::get('fpx.certificates.production.dir');
		}

		return [$disk, $dir];
	}

	/**
	 * Convert the signature binary expression into its binary-string equivalent
	 *
	 * @param string $signature
	 * @return string
	 */
	function decodeSignature($signature): string {
		$length = strlen($signature);
		$result = "";
		$index = 0;

		while ($index < $length) {
			$a = substr($signature, $index, 2);
			$c = pack("H*", $a);

			$index == 0 ? $result = $c : $result .= $c;
			$index += 2;
		}

		return $result;
	}

	/**
	 * encode and sign the request data
	 *
	 * @param string $data
	 * @return string
	 */
	public function getCheckSum($data): string {
		$privateKey = openssl_pkey_get_private($this->getPublicKeyCert());
		openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA1);

		return strtoupper(bin2hex($signature));
	}
}
