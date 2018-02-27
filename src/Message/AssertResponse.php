<?php
namespace Omnipay\SaferPay\Message;

class AssertResponse extends Response {

	/**
	 * Transaction authorized and ready for capture.
	 */
	const STATUS_AUTHORIZED = 'AUTHORIZED';

	/**
	 * Transaction captured
	 */
	const STATUS_CAPTURED = 'CAPTURED';

	/**
	 * Transaction pending, used only for paydirekt at the moment.
	 */
	const STATUS_PENDING = 'PENDING';

	/**
	 * Was the request successful?
	 *
	 * @return boolean
	 */
	public function isSuccessful()
	{
		return empty($this->getMessage()) AND (
			$this->getTransactionStatus() === self::STATUS_AUTHORIZED OR
			$this->getTransactionStatus() === self::STATUS_CAPTURED OR
			$this->getTransactionStatus() === self::STATUS_PENDING
		);
	}

	/**
	 * Get transaction status, see class constants for possible values.
	 *
	 * @return string
	 */
	public function getTransactionStatus()
	{
		return isset($this->data['Transaction']['Status']) ? $this->data['Transaction']['Status'] : NULL;
	}

	/**
	 * Get Authorization ID, which you need to use as transactionReference when making a Capture request.
	 *
	 * @return string
	 */
	public function getAuthorizationId()
	{
		if ($this->getTransactionStatus() === self::STATUS_AUTHORIZED AND isset($this->data['Transaction']['Id']))
		{
			return $this->data['Transaction']['Id'];
		}

		return NULL;
	}
}
