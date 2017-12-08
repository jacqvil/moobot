<?php namespace Moo\MessengerManager;

use Faker\Provider\DateTime;
use Moo\OneApi\OneApiClient;
use Moo\OneApi\OneApiClientInterface;
use Moo\Repositories\ConversationRepository;
use MooBot\Conversation as ConversationModel;

class Conversation
{
    const CORRIDOR_ID = 18;
    const COUNTRY_ID = 239;
    const OPERATOR_ID = 1234;
    /**
     * @var Sender
     */
    protected $sender;

    protected $id;

    protected $quote;

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
        $this->setQuote(json_decode($conversation->quote));

        return true;

    }

    public function markAsComplete()
    {
        $conversation = $this->repo->findBySenderId($this->sender->getSenderId());
        if ($conversation) {
            $conversation->is_complete = 1;
            //$conversation->completed_at = \DateTime::;
            $conversation->save();
        }
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

            if ($message->contains('amount_of_money')) {
                $this->setAmount($message->getEntity('amount_of_money'));
                $this->save();
                return $this->generateQuote();

            }

            return $this->askNextQuestion();
        }

        if ($message->contains('phone_number')) {
            \Log::info('phone_number');
            return $this->fetchCustomer($message->getEntity('phone_number'));
        }

        return $response;
    }

    public function setQuote($quote)
    {
        $this->quote = $quote;
    }

    public function generateQuote()
    {
        $this->oneApiClient->authenticate();
        $quote = $this->oneApiClient->calculate($this->sender->getCustomerData('id'), $this->getSelectedRecipient(), self::CORRIDOR_ID, self::COUNTRY_ID, $this->getAmount());

        if ($quote !== null) {
            $this->setQuote($quote);
            $this->save();
            return 'You will pay ' . $quote->pay_in_amount . ' to send ' . $quote->pay_out_amount . ' to ' . $this->sender->getRecipient($this->getSelectedRecipient())->full_name . '. Enter yes to proceed or enter a different amount.';
        }
        else {
            return "Sorry, we couldn't generate a quote for you";
        }
    }

    public function createOrder()
    {
        $this->oneApiClient->authenticate();
        $quote = $this->getQuote();
        $order = $this->oneApiClient->createOrder($this->sender->getCustomerData('id'), $this->getSelectedRecipient(), self::CORRIDOR_ID, $quote->pay_in_amount, $quote->pay_out_amount,
            $quote->calculation_token, self::OPERATOR_ID, $quote->public_buy_rate, 'ref000001'.time(), 0);

        if ($order !== null) {
            return 'Your order was created successfully. Your order number is: ' . $order->id;
        }
        else {
            return "Sorry, we couldn't generate a quote for you";
        }
    }

    public function fetchRecipients($recipientFullname)
    {
        \Log::info('>>>>>>>>>>>>>>> Going to fetch recipients');
        $this->oneApiClient->authenticate();
        $recipients = $this->oneApiClient->recipients($this->sender->getCustomerData('id'));

        if (count($recipients) == 0) {
            return "Sorry you do not have any existing recipients.";
        }

        \Log::info( (array)$recipients);
        $this->sender->setRecipients((array)$recipients);

        if ($recipient = $this->findRecipientInList($recipientFullname, $recipients)) {
            \Log::info('<<<< recipient data >>>>');
            \Log::info((array) $recipient);
            $this->setSelectedRecipient($recipient->id);
            $this->save();
            return $this->askNextQuestion();
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

        \Log::info((array) $customers);

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
            return 'Hi ' . $this->getSender()->getFullname() . ', who do you want to send to? Please enter the full name of the person you are sending to.';
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

    /**
     * @return mixed
     */
    public function getQuote()
    {
        return $this->quote;
    }

    public function save()
    {
        $conversation = $this->repo->findBySenderId($this->sender->getSenderId());

        \Log::info(get_class($conversation));
        if ( ! $conversation) {
            $conversation = new ConversationModel();
            $conversation->sender_id = $this->sender->getSenderId();
            $conversation->customer_data = json_encode($this->sender->getCustomerData()) ?? null;
            $conversation->recipients = json_encode($this->sender->getRecipients()) ?? null;
            $conversation->amount = $this->getAmount() ?? 0;
            $conversation->recipient_id = $this->getSelectedrecipient() ?? null;
            $conversation->quote = json_encode($this->getQuote()) ?? null;
            $conversation->save();
        }
        else {

            $conversation->customer_data = $conversation->customer === null ? json_encode($this->sender->getCustomerData()): $conversation->customer;
            $conversation->recipients = $conversation->recipients === null ? json_encode($this->sender->getRecipients()): null;
            $conversation->amount = $conversation->amount === 0 ? $this->getAmount(): $conversation->amount;
            $conversation->recipient_id = $conversation->recipient_id === null ? $this->getSelectedRecipient(): $conversation->recipient_id;
            $conversation->quote = $conversation->quote === null ? json_encode($this->getQuote()) : null;
            $conversation->save();

           /* \Log::info('Customer Data');
            \Log::info($conversation->customer_data);

            $conversation->customer_data = json_encode($this->sender->getCustomerData());

            if ($conversation->recipients === null) {
                \Log::info('!!! recipient empty !!!');
                \Log::info(json_encode($this->sender->getRecipients()));
                $conversation->recipients = json_encode($this->sender->getRecipients());
            }

            if ($conversation->recipient_id === null) {
                \Log::info('!!! recipient is empty !!!');
                $conversation->recipient_id = $this->getSelectedRecipient();
            }
            
            if ($conversation->amount == 0) {
                \Log::info('!!! amount empty !!!');
                $conversation->amount = $this->getAmount();
            }


            if ($conversation->quote === null) {
                \Log::info('!!! quote is empty !!!');
                $conversation->quote = json_encode($this->getQuote());
            }

            $conversation->save();*/
        }

        $this->id = $conversation->id;
    }

}
