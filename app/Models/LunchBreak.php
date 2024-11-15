<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LunchBreak extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function attendanceRecord(){
        return $this->belongsTo(AttendanceRecord::class, 'attendance_record_id');
    }
}
