<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeFamilyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'marital_status',
        'spouse_name',
        'spouse_phone',
        'spouse_dob',
        'spouse_occupation',
    ];

    protected $casts = [
        'spouse_dob' => 'date',
    ];
}