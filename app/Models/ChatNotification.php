<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ChatNotification extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['read_at' => 'datetime', 'push_sent_at' => 'datetime', 'expires_at' => 'datetime'];
    public function message() { return $this->belongsTo(ChatMessage::class, 'message_id'); }
    public function conversation() { return $this->belongsTo(ChatConversation::class, 'conversation_id'); }
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
}
