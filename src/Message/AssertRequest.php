<?php

namespace Omnipay\SaferPay\Message;

class AssertRequest extends AbstractRequest
{
	public function getData()
    {
        $this->validate('transactionReference');

		$data = array(
			'Token' => $this->getTransactionReference(),
		);

		return array_replace_recursive(parent::getData(), $data);
    }

	protected function getEndpoint()
	{
		return parent::getEndpoint() . 'Payment/'. parent::API_VERSION .'/PaymentPage/Assert';
	}

	protected function createResponse($data)
    {
		return $this->response = new AssertResponse($this, $data);
    }
}
