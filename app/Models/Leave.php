<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function responsesBy(){
        return $this->belongsTo(User::class, 'responses_by');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images(){
        return $this->hasMany(LeaveImage::class);
    }

}
