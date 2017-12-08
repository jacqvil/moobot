<?php namespace Moo\Repositories;

use Moo\Traits\Findable;
use MooBot\Conversation;

/**
 * Created by PhpStorm.
 * User: jacquesviljoen
 * Date: 2017/12/08
 * Time: 12:29 AM
 */

class ConversationRepository
{
    use Findable;
    /**
     * @var Conversation
     */
    protected $model;
    /**
     * ConversationRepository constructor.
     *
     * @param Conversation $model
     */
    public function __construct(Conversation $model)
    {
        $this->model = $model;
    }

    public function findBySenderId($senderId)
    {
        return $this->model->whereSenderId($senderId)->whereIsComplete(0)->latest()->first();
    }
}