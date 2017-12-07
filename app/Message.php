<?php namespace MooBot;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
