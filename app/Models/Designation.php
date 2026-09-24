<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'office_id',
        'department_id',
        'name',
        'code',
        'description',
        'level',
        'sort_order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
        'sort_order' => 'integer',
    ];

    public function employees()
    {
        return $this->hasMany(
            User::class,
            'designation_id'
        );
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}