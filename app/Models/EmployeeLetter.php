<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLetter extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'extra_data' => 'array',
        'joining_date' => 'date',
        'issue_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'hr_document_type_id');
    }

    public function template()
    {
        return $this->belongsTo(LetterTemplate::class, 'hr_letter_template_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
