<?php

namespace Omnipay\SaferPay\Message;

use Omnipay\Common\Message\AbstractResponse;
//use Omnipay\Common\Message\RequestInterface;

/**
 * SaferPay Response
 *
 * This is the response class for all SaferPay requests.
 *
 * @see \Omnipay\SaferPay\Gateway
 */
class Response extends AbstractResponse
{
	/**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
		return empty($this->data['ErrorName']);
    }

	 /**
     * Is the transaction cancelled by the user?
     *
     * @return boolean
     */
	public function isCancelled()
	{
		return $this->getCode() === 'TRANSACTION_ABORTED';
	}

	/**
     * Response Message, always empty for successful requests.
	 * You can get additional error information with getData().
     *
     * @return null|string A response message from the payment gateway
     */
	public function getMessage()
	{
		return isset($this->data['ErrorMessage']) ? $this->data['ErrorMessage'] : NULL;
	}

	/**
     * Response code - eg. VALIDATION_FAILED, AMOUNT_INVALID, INTERNAL_ERROR...
	 * See https://saferpay.github.io/jsonapi/#errorhandling for full error list
     *
     * @return null|string A response code from the payment gateway
     */
    public function getCode()
    {
        return isset($this->data['ErrorName']) ? $this->data['ErrorName'] : NULL;
    }

	/**
     * Gateway Reference
     *
     * @return null|string A reference provided by the gateway to represent this transaction
     */
	public function getTransactionReference()
    {
		if (isset($this->data['Token']))
		{
			return $this->data['Token'];
		}

		// For 'capture' requests
		if (isset($this->data['TransactionId']))
		{
			return $this->data['TransactionId'];
		}

        return NULL;
    }

}
