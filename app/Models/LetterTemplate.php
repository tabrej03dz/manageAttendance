<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LetterTemplate extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'hr_document_type_id');
    }

    public function employeeLetters()
    {
        return $this->hasMany(EmployeeLetter::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
