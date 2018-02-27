<?php

namespace Omnipay\SaferPay\Message;

use Omnipay\Tests\TestCase;

class AuthorizeRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array(
            'username' => getenv('SP_USERNAME'),
			'password' => getenv('SP_PASSWORD'),
			'customerId' => getenv('CUSTOMER_ID'),
			'terminalId' => getenv('TERMINAL_ID'),

            'amount' => '10.00',
			'currency' => 'EUR',
            'description' => 'Unit test transaction',
			'returnUrl' => 'https://www.example.com/success',
			'failureUrl' => 'https://www.example.com/failure',
        ));
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertSame(1000, $data['Payment']['Amount']['Value']);
		$this->assertSame('EUR', $data['Payment']['Amount']['CurrencyCode']);
    }
}
