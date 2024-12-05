<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteUser extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user(){
        $this->belongsTo(User::class, 'user_id');
    }

    public function note(){
        return $this->belongsTo(Note::class, 'note_id');
    }
}
