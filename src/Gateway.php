<?php

namespace Omnipay\SaferPay;

use Omnipay\Common\AbstractGateway;
use Omnipay\SaferPay\Message\BatchCloseRequest;

/**
 * SaferPay Gateway
 *
 * This gateway is useful for testing. It simply authorizes any payment made using a valid
 * credit card number and expiry.
 *
 * Any card number which passes the Luhn algorithm and ends in an even number is authorized,
 * for example: 4242424242424242
 *
 * Any card number which passes the Luhn algorithm and ends in an odd number is declined,
 * for example: 4111111111111111
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
 * // Do a purchase transaction on the gateway
 * $transaction = $gateway->purchase(array(
 *     'amount'                   => '10.00',
 *     'currency'                 => 'AUD',
 *     'card'                     => $card,
 * ));
 * $response = $transaction->send();
 * if ($response->isSuccessful()) {
 *     echo "Purchase transaction was successful!\n";
 *     $sale_id = $response->getTransactionReference();
 *     echo "Transaction reference = " . $sale_id . "\n";
 * }
 * </code>
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'SaferPay';
    }

    public function getDefaultParameters()
    {
        return array(
			'username'		=> '',
            'password'		=> '',
            'testMode'		=> FALSE,
			'customerId'	=> ''
		);
    }
	
	public function getUsername()
	{
		return $this->getParameter('username');
	}

	public function setUsername($value)
	{
		return $this->setParameter('username', $value);
	}

	public function getPassword()
	{
		return $this->getParameter('password');
	}

	public function setPassword($value)
	{
		return $this->setParameter('password', $value);
	}

	public function getCustomerId()
	{
		return $this->getParameter('customerId');
	}

	public function setCustomerId($value)
	{
		return $this->setParameter('customerId', $value);
	}

    /**
     * Create an authorize request.
     *
     * @param array $parameters
     * @return \Omnipay\SaferPay\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
		return $this->createRequest('\Omnipay\SaferPay\Message\AuthorizeRequest', $parameters);
    }

	/**
	 * Create an assertion request.
	 *
	 * Call this function to safely check the status of the transaction from your server. Depending on the payment provider,
	 * the resulting transaction may either be an authorization or may already be captured (meaning the financial flow was already triggered).
	 * This will be visible in the status of the transaction container returned in the response.
	 *
	 * If the transaction failed (the payer was redirected to the Fail url or he manipulated the return url), an error response with an http
	 * status code 400 or higher containing an error message will be returned providing some information on the transaction failure.
	 *
	 * @param array $parameters
	 * @return \Omnipay\SaferPay\Message\AssertRequest
	 */
	public function assertTransaction(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\SaferPay\Message\AssertRequest', $parameters);
	}

	/**
     * Capture an authorization.
     *
     * Use this resource to capture and process a previously created authorization.
     *
     * @link https://saferpay.github.io/jsonapi/index.html#Payment_v1_Transaction_Capture
     * @param array $parameters
     * @return \Omnipay\SaferPay\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\SaferPay\Message\CaptureRequest', $parameters);
    }

	/**
     * Void an authorization.
     *
     * To to void a previously authorized payment.
     *
     * @link https://saferpay.github.io/jsonapi/index.html#Payment_v1_Transaction_Cancel
     * @param array $parameters
     * @return \Omnipay\SaferPay\Message\VoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\SaferPay\Message\VoidRequest', $parameters);
    }

    /**
     * Batch close transactions.
     *
     * @link https://saferpay.github.io/jsonapi/#Payment_v1_Batch_Close
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest|BatchCloseRequest
     */
    public function batchClose(array $parameters = array())
    {
        return $this->createRequest(BatchCloseRequest::class, $parameters);
    }
}
