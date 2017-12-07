<?php namespace Moo\Repositories;

use Moo\Traits\Findable;
use MooBot\Message;

/**
 * Created by PhpStorm.
 * User: jacquesviljoen
 * Date: 2017/12/08
 * Time: 12:29 AM
 */

class MessageRepository
{
    use Findable;
    /**
     * @var Message
     */
    protected $model;
    /**
     * ConversationRepository constructor.
     *
     * @param Message $model
     */
    public function __construct(Message $model)
    {
        $this->model = $model;
    }

    public function findByConversationId($conversationId)
    {
        return $this->model->whereConversationId($conversationId)->latest()->get();
    }
}