<?php namespace Moo\MessengerManager;

use Moo\OneApi\OneApiClient;
use Moo\OneApi\OneApiClientInterface;
use Moo\Repositories\ConversationRepository;
use MooBot\Conversation as ConversationModel;

class Conversation
{
    /**
     * @var Sender
     */
    protected $sender;

    protected $id;

    /**
     * @var array
     */
    protected $messages;

    protected $oneApiClient;

    protected $amount;

    protected $selectedRecipient;

    protected $repo;
    /**
     * @param mixed $selectedRecipient
     */
    public function setSelectedRecipient($selectedRecipient)
    {
        $this->selectedRecipient = $selectedRecipient;

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * Conversation constructor.
     *
     * @param OneApiClient $client
     * @param Sender|null $sender
     */
    public function __construct(OneApiClient $client, Sender $sender = null)
    {
        $this->oneApiClient = $client;
        $this->repo = new ConversationRepository(new ConversationModel);

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

    /**
     * @param $senderId
     * @throws \Exception
     * @return boolean
     */
    public function load($senderId)
    {
        $conversation = $this->repo->findBySenderId($senderId);

        if ($conversation === null) {
            return false;
        }

        $this->sender = new Sender($senderId);

        $this->sender->setCustomerData(json_decode($conversation->customer_data));
        $this->sender->setRecipients(json_decode($conversation->recipients));
        $this->setSelectedRecipient($conversation->recipient_id);
        $this->setAmount($conversation->amount);

        return true;

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

    public function getSelectedRecipient()
    {
        return $this->selectedRecipient;
    }

    public function save()
    {
        $conversation = $this->repo->findBySenderId($this->sender->getSenderId());

        if ( ! $conversation) {
            $conversation = new ConversationModel();
            $conversation->sender_id = $this->sender->getSenderId();
            $conversation->customer_data = json_encode($this->sender->getCustomerData()) ?? null;
            $conversation->recipients = json_encode($this->sender->getRecipients()) ?? null;
            $conversation->amount = json_encode($this->getAmount()) ?? null;
            $conversation->recipient_id = json_encode($this->getSelectedrecipient()) ?? null;
            $conversation->save();
        }
        else {
            $conversation->customer_data = $conversation->customer === null ? json_encode($this->sender->getCustomerData()): null;
            $conversation->recipients = $conversation->recipients === null ? json_encode($this->sender->getRecipients()): null;
            $conversation->amount = $conversation->amount === null ? json_encode($this->amount()): null;
            $conversation->recipient_id = $conversation->recipient_id === null ? json_encode($this->getSelectedRecipient()): null;
            $conversation->save();
        }

        $this->id = $conversation->id;
    }

}
