<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\CorrectionNote;
use Illuminate\Http\Request;

class CorrectionNoteController extends Controller
{
    public function create(AttendanceRecord $record, CorrectionNote $note = null){
        return view('dashboard.correctionNote.create', compact('record', 'note'));
    }

    public function store(Request $request, AttendanceRecord $record, CorrectionNote $note = null){
        $request->validate([
            'note' => 'required',
        ]);
        CorrectionNote::create([
            'user_id' => auth()->user()->id,
            'attendance_record_id' => $record->id,
            'parent_id' => $note ? $note->id : null,
            'note' => $request->note,
        ]);
        return redirect('attendance/index')->with('success', 'Correction record created successfully');
    }
}
