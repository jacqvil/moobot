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
     * @param array $customerData
     */
    public function setCustomerData($customerData)
    {
        $this->customerData = $customerData;
    }

    public function getCustomerData($key)
    {
        return $this->customerData->{$key} ?? '';
    }

    public function getFullname()
    {
        return $this->getCustomerData('first_name') . ' ' . $this->getCustomerData('surname');
    }

}