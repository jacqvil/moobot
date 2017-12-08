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

    protected $amount = 0;

    protected $selectedRecipient = null;

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
        $response = "I don't know what you need from me, do you want to send money? If yes, please send me your mobile number.";

        if (count($message->getEntities()) == 0) {
            \Log::info('No Entities');
            return $response;
        }

        if ($this->sender->hasCustomerData()) {
            \Log::info('hasCustomerData');

            if ($message->contains('contact')) {
                \Log::info('Contact');
                return $this->fetchRecipients($message->getEntity('contact'));
            }

            return $this->askNextQuestion();
        }

        if ($message->contains('phone_number')) {
            \Log::info('phone_number');
            return $this->fetchCustomer($message->getEntity('phone_number'));
        }

        if ($message->contains('amount_of_money')) {
            $this->setAmount($message->getEntity('amount_of_money'));
            return "We now need to create a quote";
        }

        return $response;
    }


    public function fetchRecipients($recipientFullname)
    {
        \Log::info('>>>>>>>>>>>>>>> Going to fetch recipients');
        $this->oneApiClient->authenticate();
        $recipients = $this->oneApiClient->recipients($this->sender->getCustomerData('id'));

        if (count($recipients) == 0) {
            return "Sorry you do not have any existing recipients.";
        }

        $this->sender->setRecipients($recipients);

        if ($recipient = $this->findRecipientInList($recipientFullname,$recipients)) {
            $this->setSelectedRecipient($recipient->id);
            $this->askNextQuestion();
        }
        else {
            return "We cannot find the recipient in your recipient list. Please check that the name is correct.";
        }
    }

    public function findRecipientInList($recipientFullname, $recipients)
    {
        foreach ($recipients as $recipient) {
            if ($recipient->full_name == $recipientFullname) {
                return $recipient;
            }
        }

        return null;
    }
    public function fetchCustomer($mobileNumber)
    {
        $this->oneApiClient->authenticate();
        $customers = $this->oneApiClient->customers($mobileNumber);

        if (count($customers) == 0) {
            return "Sorry we couldn't find a profile associated with the mobile number, " . $mobileNumber. ". Please make sure the number is correct.";
        }
        else {
            $this->sender->setCustomerData($customers[0]);
            return $this->askNextQuestion();
        }
    }

    public function askNextQuestion()
    {
        if ($this->getSelectedRecipient() === null) {
            return 'Who do you want to send to?';
        }

        if ($this->getAmount() == 0) {
            return 'How much do you want to send?';
        }

        return 'Do you want to send money? If yes, please send me your mobile number.';
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
            $conversation->amount = $this->getAmount() ?? 0;
            $conversation->recipient_id = json_encode($this->getSelectedrecipient()) ?? null;
            $conversation->save();
        }
        else {
            $conversation->customer_data = $conversation->customer === null ? json_encode($this->sender->getCustomerData()): null;
            $conversation->recipients = $conversation->recipients === null ? json_encode($this->sender->getRecipients()): null;
            $conversation->amount = $conversation->amount === 0 ? $this->amount(): 0;
            $conversation->recipient_id = $conversation->recipient_id === null ? json_encode($this->getSelectedRecipient()): null;
            $conversation->save();
        }

        $this->id = $conversation->id;
    }

}
