<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function templates()
    {
        return $this->hasMany(LetterTemplate::class);
    }

    public function employeeLetters()
    {
        return $this->hasMany(EmployeeLetter::class);
    }
}
