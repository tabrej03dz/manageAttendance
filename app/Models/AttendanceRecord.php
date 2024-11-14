<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notedBy(){
        return $this->belongsTo(User::class, 'noted_by');
    }

    public function checkInNoteResponsesBy(){
        return $this->belongsTo(User::class, 'check_in_note_response_by');
    }

    public function checkOutNoteResponseBy(){
        return $this->belongsTo(User::class, 'check_out_note_response_by');
    }

    public function checkInBy(){
        return $this->belongsTo(User::class, 'check_in_by');
    }

    public function checkOutBy(){
        return $this->belongsTo(User::class, 'check_out_by');
    }

    public function breaks(){
        return $this->hasMany(LunchBreak::class, 'attendance_record_id');
    }

}
