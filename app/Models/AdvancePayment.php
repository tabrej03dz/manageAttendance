<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePayment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paidBy(){
        return $this->belongsTo(User::class, 'paid_by');
    }
}
