<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEducationalQualification extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Fillable Fields
    |--------------------------------------------------------------------------
    */

    protected $guarded = [
        'id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'passing_year' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Employee Relationship
    |--------------------------------------------------------------------------
    */

    public function employee(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}