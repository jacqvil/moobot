<?php namespace Moo\MessengerManager;

use Moo\OneApi\OneApiClient;

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

    protected $oneApiClient;

    /**
     * Conversation constructor.
     *
     * @param OneApiClient $client
     * @param Sender|null $sender
     */
    public function __construct(OneApiClient $client, Sender $sender = null)
    {
        $this->oneApiClient = $client;

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

    public function helpImConfused(Message $message)
    {
        if ($message->contains('phone_number')) {
            \Log::info('We have phone number');
            \Log::info($this->oneApiClient);
            $response = $this->oneApiClient->customers($message->getEntity('phone_number'));
            \Log::info('Customer lookup response:');
            \Log::info($response);

        }
    }
}
