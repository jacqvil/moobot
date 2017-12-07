<?php namespace Moo\MessengerManager;

class Conversation
{
    /**
     * @var Sender
     */
    protected $sender;

    /**
     * @var array
     */
    protected $messages;

    /**
     * Conversation constructor.
     *
     * @param Sender|null $sender
     */
    public function __construct(Sender $sender = null)
    {
        if ($sender instanceof Sender) {
            $this->sender = $sender;
        }
    }

    /**
     * @return Sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param Sender $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    public function load($senderId)
    {

    }

    public function processMessage(Message $message)
    {

    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    public function addMessage(Message $message)
    {
        array_push($this->messages, $message);
    }
}
