<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use HasFactory, softDeletes;
    protected $guarded = ['id'];

    public function users(){
        return $this->hasMany(User::class, 'office_id');
    }

    public function policy(){
        return $this->hasOne(Policy::class, 'office_id');
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'office_id');
    }

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }


}
