<?php namespace Moo\ChatBot;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Moo\MessengerManager\Message;

class ChatbotHelper
{
    const WIT_AI = 'witai';

    protected $chatbotAI;
    protected $facebookSend;
    protected $log;
    private $accessToken;
    public $config;
    protected $session;

    public function __construct(array $config, $session)
    {

        $this->config = $config;
        $this->session = $session;
        $this->accessToken = $config['access_token'];
        $this->chatbotAI = new ChatbotAI($this->config);
        $this->facebookSend = new FacebookSend();
        $this->log = new Logger('general');
        $this->log->pushHandler(new StreamHandler('debug.log'));
    }

    /**
     * Get the sender id of the message
     * @param $input
     * @return mixed
     */
    public function getSenderId($input)
    {
        return $input['entry'][0]['messaging'][0]['sender']['id'];
    }

    /**
     * Get the user's message from input
     * @param $input
     * @return mixed
     */
    public function getMessage($input)
    {
        return $input['entry'][0]['messaging'][0]['message']['text'];
    }

    /**
     * Check if the callback is a user message
     * @param $input
     * @return bool
     */
    public function isMessage($input)
    {
        return isset($input['entry'][0]['messaging'][0]['message']['text']) && !isset
        ($input['entry'][0]['messaging'][0]['message']['is_echo']);

    }

    /**
     * Get the answer to a given user's message
     * @param null $api
     * @param string $message
     * @return Message $message;
     */
    public function getAnswer($message, $api = null)
    {

            $response = $this->chatbotAI->getWitAIAnswer($message);
            \Log::info('Wit response');
            \Log::info($response);


            $message = new Message();
            $message->setEntities($response['entities']);
            //$message->setIntent($intent);
            //$message->setContact($contact);
            //$message->setAmount($amountOfMoney);

            return $message;
    }

    /**
     * Send a reply back to Facebook chat
     * @param $senderId
     * @param $replyMessage
     */
    public function send($senderId, string $replyMessage)
    {
        return $this->facebookSend->send($this->accessToken, $senderId, $replyMessage);
    }

    /**
     * Verify Facebook webhook
     * This is only needed when you setup or change the webhook
     * @param $request
     * @return mixed
     */
    public function verifyWebhook($request)
    {
        if (!isset($request['hub_challenge'])) {
            return false;
        };

        $hubVerifyToken = null;
        $hubVerifyToken = $request['hub_verify_token'];
        $hubChallenge = $request['hub_challenge'];

        if (isset($hubChallenge) && $hubVerifyToken == $this->config['webhook_verify_token']) {

            echo $hubChallenge;
        }

    }

    /**
     * @param $senderId
     * @return boolean
     */
    public function isExistingConversation($senderId)
    {
            return $this->session->has($senderId);
    }

    public function createConversation($senderId)
    {
        $this->session->put($senderId, $senderId);
    }
}