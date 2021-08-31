<?php

namespace Omnipay\SaferPay\Message;

class BatchCloseRequest extends AbstractRequest
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

    public function getData()
    {
        $this->validate('terminalId');

        $data = [
            'TerminalId' => $this->getTerminalId(),
        ];

        return array_replace_recursive(parent::getData(), $data);
    }

    protected function getEndpoint()
    {
        return parent::getEndpoint() . 'Payment/'. parent::API_VERSION .'/Batch/Close';
    }
}