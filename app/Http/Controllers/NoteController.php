<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\NoteUser;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;



class NoteController extends Controller
{
    public function index(){

            $notes = Note::all();

        return view('dashboard.note.index', compact('notes'));
    }

    public function create(){

        if (auth()->user()->hasRole('super_admin')){
            $offices = Office::all();
        }elseif(auth()->user()->hasRole('owner')){
            $officeNames = Office::where('owner_id', auth()->user()->id)->pluck('name');
//            $roles = Role::where(function ($query) use ($officeNames) {
//                foreach ($officeNames as $officeName) {
//                    $query->orWhere('name', 'like', $officeName . '%');
//                }
//            })->get();
            $offices = auth()->user()->offices;
        }else{
//            $roles = Role::where('name', 'like', auth()->user()->office->name.'%')->get();
            $offices = Office::find(auth()->user()->office_id);
        }


//        $employees = HomeController::employeeList();
        return view('dashboard.note.create', compact( 'offices'));
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'office_id' => 'required',
            'role' => 'required_without:employees',
            'employees' => 'required_without:role',
        ]);

        $note = Note::create([
            'title' => $request->title,
            'description' => $request->description,
            'office_id' => $request->office_id,
        ]);

        if($request->role){
            $users = User::role($request->role)->where('office_id', $request->office_id)->get();
            foreach ($users as $user){
                NoteUser::create(['note_id' => $note->id, 'user_id' => $user->id]);
            }
        }
        if($request->employees){
            if ($request->employees[0] == 'all'){
                $office = Office::find($request->office_id);
                foreach ($office->users as $user){
                    NoteUser::create(['note_id' => $note->id, 'user_id' => $user->id]);
                }
            }else{
                foreach ($request->employees as $key=>$value){
                    NoteUser::create(['note_id' => $note->id, 'user_id' => $value]);
                }
            }
        }
        return redirect('note')->with('success', 'Note created successfully');
    }

    // OfficeController.php
    public function getOfficeRoleAndEmployee($id)
    {
        $office = Office::find($id);
        $employees = User::where('office_id', $office->id)->get();
        $roles = Role::where('name', 'like', $office->name.'%')->orwhereIn('name', ['admin', 'team_leader', 'employee'])->get();

//        dd($employees, $roles);

        return response()->json([
            'employees' => $employees,
            'roles' => $roles,
        ]);

    }

    public function status(Note $note){
        if ($note->status == '1'){
            $note->update(['status' => '0']);
        }else{
            $note->update(['status' => '1']);
        }
        return back()->with('success', 'status changed successfully');
    }

    public function delete(Note $note){
        $note->delete();
        return back()->with('success', 'Note deleted successfully');
    }
}
