<?php

namespace Omnipay\SaferPay;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
	/** @var Omnipay\SaferPay\Gateway */
	public $gateway;

	/** @var array */
	public $authorize_options;

    public function setUp()
    {
        parent::setUp();

		$dotenv = new \Dotenv\Dotenv(__DIR__);
		$dotenv->load();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
		$this->gateway->initialize(array(
			'username' => getenv('SP_USERNAME'),
			'password' => getenv('SP_PASSWORD'),
			'testMode' => TRUE,
			'customerId' => getenv('CUSTOMER_ID'),
        ));

		$this->authorize_options = array(
			'terminalId' => getenv('TERMINAL_ID'),
			'requestId' => 'b6858e14f66ac5e6ef2c5804572de9fb',
			'amount' => '10.00',
			'currency' => 'EUR',
            'description' => 'Unit test transaction',
			'returnUrl' => 'https://www.example.com/success',
			'failureUrl' => 'https://www.example.com/failure',
		);
    }

    public function testAuthorizeSuccess()
    {
		$this->setMockHttpResponse('AuthorizeSuccess.txt');
        $response = $this->gateway->authorize($this->authorize_options)->send();

        $this->assertInstanceOf('\Omnipay\SaferPay\Message\AuthorizeResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
		$this->assertFalse($response->isCancelled());
		$this->assertEmpty($response->getMessage());
		$this->assertEmpty($response->getCode());
        $this->assertNotEmpty($response->getTransactionReference());
		$this->assertSame('https://test.saferpay.com/vt2/api/PaymentPage/243155/17886671/6qioac91i6xtw4tphjs4wm5in', $response->getRedirectUrl());
    }

	public function testAssertSuccess()
	{
		$this->setMockHttpResponse('AssertSuccess.txt');
		$response = $this->gateway->assertTransaction(array(
			'requestId' => 'b6858e14f66ac5e6ef2c5804572de9fb',
			'transactionReference' => '6qioac91i6xtw4tphjs4wm5in'
		))->send();

		$this->assertInstanceOf('\Omnipay\SaferPay\Message\AssertResponse', $response);
		$this->assertTrue($response->isSuccessful());
		$this->assertSame('3792ndA0An79SAGjvM63bG9vpUpb', $response->getAuthorizationId());
		$this->assertSame(Message\AssertResponse::STATUS_AUTHORIZED, $response->getTransactionStatus());

		$data = $response->getData();

		$this->assertArrayHasKey('PaymentMeans', $data);
		$this->assertArrayHasKey('Brand', $data['PaymentMeans']);
		$this->assertArrayHasKey('PaymentMethod', $data['PaymentMeans']['Brand']);
		$this->assertSame('VISA', $data['PaymentMeans']['Brand']['PaymentMethod']);
	}

	public function testCaptureSuccess()
	{
		$this->setMockHttpResponse('CaptureSuccess.txt');
		$response = $this->gateway->capture(array(
			'requestId' => 'b6858e14f66ac5e6ef2c5804572de9fb',
			'transactionReference' => '3792ndA0An79SAGjvM63bG9vpUpb' // This is the Authorization ID!
		))->send();

		$this->assertInstanceOf('\Omnipay\SaferPay\Message\CaptureResponse', $response);
		$this->assertTrue($response->isSuccessful());
		$this->assertSame(Message\CaptureResponse::STATUS_CAPTURED, $response->getCaptureStatus());
	}

	public function testVoidSuccess()
	{
		$this->setMockHttpResponse('VoidSuccess.txt');
		$response = $this->gateway->void(array(
			'requestId' => 'b6858e14f66ac5e6ef2c5804572de9fb',
			'transactionReference' => 'U0472EbxhGAbUAInQpMMAjnx9d8b' // This is the Authorization ID!
		))->send();

		$this->assertInstanceOf('\Omnipay\SaferPay\Message\Response', $response);
		$this->assertTrue($response->isSuccessful());
	}
}
