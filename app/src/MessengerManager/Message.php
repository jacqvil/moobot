<?php
/**
 * Created by PhpStorm.
 * User: jacquesviljoen
 * Date: 2017/12/07
 * Time: 8:59 PM
 */

namespace Moo\MessengerManager;

use Moo\Repositories\MessageRepository;
use MooBot\Message as MessageModel;

class Message
{
    CONST INTENT_SEND = 'send';

    protected $intent;
    protected $contact;
    protected $location;
    protected $amount;
    protected $mobileNumber;
    protected $entities;
    protected $repo;
    protected $conversationId;

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
        \Log::info($entities);
        $this->entities = $entities;

       /* $this->intent = $entities['intent'][0]['value'] ?? null;
        $this->contact = $entities['contact'][0]['value'] ?? null;
        $this->amount = $entities['amount_of_money'][0]['value'] ?? null;
        $this->mobileNumber = $entities['amount_of_money'][0]['value'] ?? null;*/

    }

    /**
     * Message constructor.
     *
     */
    public function __construct()
    {
        $this->repo = new MessageRepository(new MessageModel);
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
        return $this->entities[$key][0]['value'] ?? null;
    }

    public function contains($key)
    {
        return (isset($this->entities[$key]));
    }

    public function save($in, $out)
    {
        $message = new MessageModel();
        $message->in = $in;
        $message->out = $out;
        $message->conversation_id = $this->getConversationId();
        $message->save();
    }

    /**
     * @return mixed
     */
    public function getConversationId()
    {
        return $this->conversationId;
    }

    /**
     * @param mixed $conversationId
     */
    public function setConversationId($conversationId)
    {
        $this->conversationId = $conversationId;
    }

}