<?php

namespace Omnipay\SaferPay\Message;

class VoidRequest extends AbstractRequest
{
	public function getData()
    {
        $this->validate('transactionReference');

		$data = array(
			'TransactionReference' => array(
				'TransactionId' => $this->getTransactionReference()
			)
		);

		return array_replace_recursive(parent::getData(), $data);
    }

	protected function getEndpoint()
	{
		return parent::getEndpoint() . 'Payment/'. parent::API_VERSION .'/Transaction/Cancel';
	}
}
