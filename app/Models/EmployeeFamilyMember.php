<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeFamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'relation',
        'occupation',
        'age',
    ];

    protected $casts = [
        'age' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}