<?php namespace Moo\MessengerManager;

class Sender
{

    /**
     * @var int
     */
    protected $senderId;

    /**
     * @var array
     */
    protected $customerData;
    /**
     * Sender constructor.
     *
     * @param int $senderId
     */
    public function __construct($senderId)
    {
       $this->senderId = $senderId;
    }

    /**
     * @return mixed
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * @param mixed $senderId
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;
    }

    /**
     * @return array
     */
    public function getCustomerData()
    {
        return $this->customerData;
    }

    /**
     * @param array $customerData
     */
    public function setCustomerData($customerData)
    {
        $this->customerData = $customerData;
    }

}