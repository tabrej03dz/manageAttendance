<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'premise_details',
        'street_road',
        'locality_area',
        'landmark',
        'city',
        'district',
        'state',
        'pin_code',
    ];
}