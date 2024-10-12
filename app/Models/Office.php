<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function users(){
        return $this->hasMany(User::class, 'office_id');
    }

    public function policy(){
        return $this->hasOne(Policy::class, 'office_id');
    }
}
