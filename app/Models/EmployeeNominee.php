<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeNominee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'relationship',
        'phone',
        'dob',
        'aadhaar_number',
        'address',
    ];

    protected $casts = [
        'dob' => 'date',
    ];
}