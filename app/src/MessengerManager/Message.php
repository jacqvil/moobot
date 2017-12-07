<?php
/**
 * Created by PhpStorm.
 * User: jacquesviljoen
 * Date: 2017/12/07
 * Time: 8:59 PM
 */

namespace Moo\MessengerManager;

class Message
{
    CONST INTENT_SEND = 'send';

    protected $intent;
    protected $contact;
    protected $location;
    protected $amount;
    protected $mobileNumber;
    protected $entities;

    /**
     * @return mixed
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param mixed $entities
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;

       /* $this->intent = $entities['intent'][0]['value'] ?? null;
        $this->contact = $entities['contact'][0]['value'] ?? null;
        $this->amount = $entities['amount_of_money'][0]['value'] ?? null;
        $this->mobileNumber = $entities['amount_of_money'][0]['value'] ?? null;*/

    }

    /**
     * Message constructor.
     *
     * @param array $data;
     */
    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getIntent()
    {
        return $this->getEntity('intent');
    }

    /**
     * @param mixed $intent
     */
    public function setIntent($intent)
    {
        $this->intent = $intent;
    }

    /**
     * @return mixed
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param mixed $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function response()
    {
        return 'The intent is: ' . $this->intent;
    }

    public function getEntity($key)
    {
        return $entities[$key][0]['value'] ?? null;
    }

    public function contains($key)
    {
        return (isset($this->entities[$key]));
    }
}