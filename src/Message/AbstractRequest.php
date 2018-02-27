<?php
namespace Omnipay\SaferPay\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const LIVE_ENDPOINT = 'https://www.saferpay.com/api';
    const TEST_ENDPOINT = 'https://test.saferpay.com/api';
	const API_VERSION = 'v1';
	const SPEC_VERSION = '1.8';

	private $retry = 0;

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

    public function getRequestId()
    {
        return $this->getParameter('requestId');
    }

    public function setRequestId($value)
    {
        return $this->setParameter('requestId', $value);
    }

	public function getTerminalId()
    {
        return $this->getParameter('terminalId');
    }

    public function setTerminalId($value)
    {
        return $this->setParameter('terminalId', $value);
    }

	public function getData()
	{
		$this->validate('customerId', 'requestId');

		return array(
			'RequestHeader' => array(
				'SpecVersion' => self::SPEC_VERSION,
				'CustomerId' => $this->getCustomerId(),
				'RequestId' => $this->getRequestId()
			),
		);
	}

	public function sendData($data)
    {
		$data['RequestHeader']['RetryIndicator'] = $this->retry++;

		$httpRequest = $this->httpClient->post($this->getEndpoint(), array(
			'Content-Type' => 'application/json; charset=utf-8',
			'Accept' => 'application/json',
		));
		
		$httpRequest->setBody(json_encode($data));

		$httpRequest->setAuth($this->getUsername(), $this->getPassword());

        return $this->createResponse($httpRequest->send()->json());
    }

    protected function createResponse($data)
    {
		return $this->response = new Response($this, $data);
    }

    protected function getEndpoint()
    {
        return ($this->getTestMode() ? self::TEST_ENDPOINT : self::LIVE_ENDPOINT) .'/';
    }
}
