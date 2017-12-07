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

    protected $recipients = [];

    /**
     * @return array
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @param array $recipients
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
    }

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

    public function hasCustomerData()
    {
      return count($this->customerData) > 0;
    }

    public function getCustomerData($key = null)
    {
        // return all data
        if ($key === null) {
            return $this->customerData;
        }

        return $this->customerData->{$key} ?? '';
    }

    public function getFullname()
    {
        return $this->getCustomerData('first_name') . ' ' . $this->getCustomerData('surname');
    }

    public function loaded()
    {
        return count($this->customerData) > 0;
    }

}