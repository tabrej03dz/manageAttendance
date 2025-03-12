<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveImage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function leave(){
        return $this->belongsTo(Leave::class, 'leave_id');
    }
}
