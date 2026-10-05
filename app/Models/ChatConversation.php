<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    protected $guarded = ['id'];

    public function participants()
    {
        return $this->hasMany(ChatParticipant::class, 'conversation_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_participants', 'conversation_id', 'user_id');
    }
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }
    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latestOfMany();
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
