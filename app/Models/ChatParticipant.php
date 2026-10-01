<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['last_read_at' => 'datetime', 'last_read_message_id' => 'integer'];

    public function conversation() { return $this->belongsTo(ChatConversation::class, 'conversation_id'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
}
