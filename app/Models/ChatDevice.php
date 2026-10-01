<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ChatDevice extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['fcm_token', 'token_hash'];
}
