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
        $response = "I don't know what you need from me, do you want to send money?";

        if ($message->contains('phone_number')) {
            \Log::info('We have phone number');

            $this->oneApiClient->authenticate();
            $mobileNumber = $message->getEntity('phone_number');
            $customers = $this->oneApiClient->customers($mobileNumber);

            if (count($customers) == 0) {
                $response = "Sorry we couldn't find a profile associated with the mobile number, " . $mobileNumber. ". Please make sure the number is correct.";
            }
            else {
                $this->sender->setCustomerData($customers[0]);
                $response = 'Hi ' . $this->sender->getFullname() . ' who would you like to send money to?';
            }

        }

        if ($message->contains('contact') && $this->sender->loaded()) {

        }

        return $response;
    }

}
