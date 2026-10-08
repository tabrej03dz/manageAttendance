<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $guarded = ['id'];

    public function conversation() { return $this->belongsTo(ChatConversation::class, 'conversation_id'); }
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
    public function replyTo() { return $this->belongsTo(self::class, 'reply_to_id'); }

    public function reads()
    {
        return $this->hasMany(ChatMessageRead::class, 'message_id');
    }
}
