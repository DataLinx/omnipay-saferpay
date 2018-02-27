<?php

namespace Omnipay\SaferPay\Message;

use Omnipay\Tests\TestCase;

class AuthorizeResponseTest extends TestCase
{
    public function testRedirect()
    {
		$httpResponse = $this->getMockHttpResponse('AuthorizeSuccess.txt');
        $response = new AuthorizeResponse($this->getMockRequest(), $httpResponse->json());

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
		$this->assertSame('https://test.saferpay.com/vt2/api/PaymentPage/243155/17886671/6qioac91i6xtw4tphjs4wm5in', $response->getRedirectUrl());
    }
}
