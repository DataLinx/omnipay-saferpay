<?php
namespace Omnipay\SaferPay\Message;

class CaptureResponse extends Response {

	/**
	 * Transaction captured
	 */
	const STATUS_CAPTURED = 'CAPTURED';

	/**
	 * Transaction pending, used only for paydirekt at the moment.
	 */
	const STATUS_PENDING = 'PENDING';

	/**
	 * Was the capture successful?
	 *
	 * @return boolean
	 */
	public function isSuccessful()
	{
		return empty($this->getMessage()) AND ($this->getCaptureStatus() === self::STATUS_CAPTURED OR $this->getCaptureStatus() === self::STATUS_PENDING);
	}

	/**
	 * Get capture status, see class constants for possible values.
	 *
	 * @return string
	 */
	public function getCaptureStatus()
	{
		return isset($this->data['Status']) ? $this->data['Status'] : NULL;
	}
}
