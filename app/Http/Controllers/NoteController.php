<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Office;
use Illuminate\Http\Request;


class NoteController extends Controller
{
    public function index(){

            $notes = Note::all();

        return view('dashboard.note.index', compact('notes'));
    }

    public function create(){

        return view('dashboard.note.create');
    }
}
