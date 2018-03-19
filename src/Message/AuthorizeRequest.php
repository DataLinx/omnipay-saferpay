<?php

namespace Omnipay\SaferPay\Message;

/**
 * SaferPay Authorize Request
 *
 * ### Example
 *
 * <code>
 * // Create a gateway for the SaferPay Gateway
 * // (routes to GatewayFactory::create)
 * $gateway = Omnipay::create('SaferPay');
 *
 * // Initialise the gateway
 * $gateway->initialize(array(
 *     'testMode' => true, // Doesn't really matter what you use here.
 * ));
 *
 * // Create a credit card object
 * // This card can be used for testing.
 * $card = new CreditCard(array(
 *             'firstName'    => 'Example',
 *             'lastName'     => 'Customer',
 *             'number'       => '4242424242424242',
 *             'expiryMonth'  => '01',
 *             'expiryYear'   => '2020',
 *             'cvv'          => '123',
 * ));
 *
 * // Do an authorize transaction on the gateway
 * $transaction = $gateway->authorize(array(
 *     'amount'                   => '10.00',
 *     'currency'                 => 'AUD',
 *     'card'                     => $card,
 * ));
 * $response = $transaction->send();
 * if ($response->isSuccessful()) {
 *     echo "Authorize transaction was successful!\n";
 *     $sale_id = $response->getTransactionReference();
 *     echo "Transaction reference = " . $sale_id . "\n";
 * }
 * </code>
 */
class AuthorizeRequest extends AbstractRequest
{
	/**
	 * Set Terminal ID
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setTerminalId($value)
    {
        return $this->setParameter('terminalId', $value);
    }
	
	/**
	 * Get Terminal ID
	 * 
	 * @return string
	 */
	public function getTerminalId()
    {
        return $this->getParameter('terminalId');
    }

	/**
	 * Unambiguous order identifier defined by the merchant/ shop. This identifier might be used as reference later on.
	 * Length: 1 to 80.
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setOrderId($value)
	{
		return $this->setParameter('orderId', $value);
	}

	/**
	 * Get Order ID
	 *
	 * @return string
	 */
	public function getOrderId()
	{
		return $this->getParameter('orderId');
	}

	/**
	 * Text which will be printed on payer's debit note. Supported by SIX Acquiring. No guarantee, that it will show up on the payer's debit note, because his bank has to support it too.
	 * Please note, that maximum allowed characters are rarely supported. It's usually around 10-12.
	 * Length: 1 to 50.
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setPayerNote($value)
	{
		return $this->setParameter('payerNote', $value);
	}

	/**
	 * Get Payer Note
	 * 
	 * @return string
	 */
	public function getPayerNote()
	{
		return $this->getParameter('payerNote');
	}

	/**
	 * Mandate reference of the payment. Needed for German direct debits (ELV) only. The value has to be unique.
	 * Length: 1 to 35.
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setMandateId($value)
	{
		return $this->setParameter('mandateId', $value);
	}

	/**
	 * Get Mandate ID
	 *
	 * @return string
	 */
	public function getMandateId()
	{
		return $this->getParameter('mandateId');
	}
	
	/**
	 * Return URL for failed transaction
	 * 
	 * @param string $value
	 * @return $this
	 */
	public function setFailureUrl($value)
	{
		return $this->setParameter('failureUrl', $value);
	}

	/**
	 * Get Failure URL
	 *
	 * @return string
	 */
	public function getFailureUrl()
	{
		return $this->getParameter('failureUrl');
	}

	/**
	 * Optional e-mail address to which a confirmation email will be sent for successful authorizations.
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setMerchantEmail($value)
	{
		return $this->setParameter('merchantEmail', $value);
	}

	/**
	 * Get merchant e-mail address
	 *
	 * @return string
	 */
	public function getMerchantEmail()
	{
		return $this->getParameter('merchantEmail');
	}

	/**
	 * Optional payer e-mail address to which a confirmation email will be sent for successful authorizations.
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setPayerEmail($value)
	{
		return $this->setParameter('payerEmail', $value);
	}

	/**
	 * Get payer e-mail address
	 *
	 * @return string
	 */
	public function getPayerEmail()
	{
		return $this->getParameter('payerEmail');
	}

    public function getData()
    {
        $this->validate('terminalId', 'amount', 'description', 'returnUrl', 'failureUrl');

		$data = array(
			'TerminalId' => $this->getTerminalId(),
			'Payment' => array(
				'Amount' => array(
					'Value' => intval($this->getAmount() * intval('1'. str_repeat('0', $this->getCurrencyDecimalPlaces()))),
					'CurrencyCode' => $this->getCurrency()
				),
				'Description' => $this->getDescription(),
			),
			'ReturnUrls' => array(
				'Success' => $this->getReturnUrl(),
				'Fail' => $this->getFailureUrl(),
				'Abort' => $this->getCancelUrl()
			)
		);
		
		if ($this->getOrderId() !== NULL) {
			$data['Payment']['OrderId'] = $this->getOrderId();
		}

		if ($this->getPayerNote() !== NULL) {
			$data['Payment']['PayerNote'] = $this->getPayerNote();
		}

		if ($this->getMandateId() !== NULL) {
			$data['Payment']['MandateId'] = $this->getMandateId();
		}

		if ($this->getNotifyUrl()) {
			$data['Notification']['NotifyUrl'] = $this->getNotifyUrl();
		}

		if ($this->getMerchantEmail()) {
			$data['Notification']['MerchantEmail'] = $this->getMerchantEmail();
		}

		if ($this->getMerchantEmail()) {
			$data['Notification']['PayerEmail'] = $this->getPayerEmail();
		}

		return array_replace_recursive(parent::getData(), $data);
    }

	protected function getEndpoint()
	{
		return parent::getEndpoint() . 'Payment/'. parent::API_VERSION .'/PaymentPage/Initialize';
	}

	protected function createResponse($data)
    {
		return $this->response = new AuthorizeResponse($this, $data);
    }
}
