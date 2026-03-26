<?php

namespace App\Http\Controllers;

use App\Mail\OffNotification;
use App\Models\Off;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendOffNotification;

class OffController extends Controller
{
//     public function index(){
//         $offs = Off::whereDate('date', '>=', Carbon::today())->where('office_id', auth()->user()->office_id)->get();
//         return view('dashboard.off.index', compact('offs'));
//     }
//     public function create(){
//         if (auth()->user()->hasRole('super_admin')){
//             $offices = Office::all();
//         }else{
//             $offices = Office::where('id', auth()->user()->office_id)->get();
//         }
//         return view('dashboard.off.create', compact('offices'));
//     }

//     public function store(Request $request){
//         $request->validate([
//             'title' => 'required',
//             'date' => 'required|date',
//             'description' => '',
//             'office_id' => 'sometimes',
//             'is_off' => 'in:1',
//         ]);

//         $off = Off::create($request->all() + ['office_id' => $request->office_id ?? auth()->user()->office_id]);

//         if ($off) {
//             foreach ($off->office->users as $user){
//                 dispatch(new SendOffNotification($user, $off));
// //                Mail::to($user->email)->send(new OffNotification($off));
//             }
//             request()->session()->flash('success', 'Off Created successfully');
//         }else{
//             request()->session()->flash('error', 'Failed, Try again!');
//         }
//         return redirect('off');
//     }

//     public function edit(Off $off){
//         if (auth()->user()->hasRole('super_admin')){
//             $offices = Office::all();
//         }else{
//             $offices = null;
//         }
//         return view('dashboard.off.edit', compact('offices', 'off'));
//     }

//     public function update(Request $request, Off $off){
//         $request->validate([
//             'title' => 'required',
//             'date' => 'required|date',
//             'description' => '',
//             'office_id' => 'sometimes',
//         ]);
//         $status = $off->update($request->all());
//         if ($status){
//             request()->session()->flash('success', 'Updated successfully');
//         }else{
//             request()->session()->flash('error', 'Failed, Try again!');
//         }
//         return redirect('off');
//     }

//     public function delete(Off $off){
//         $status = $off->delete();
//         if ($status){
//             \request()->session()->flash('success', 'Deleted successfully');
//         }else{
//             \request()->session()->flash('error', 'Failed, Try again!');
//         }
//         return back();
//     }


private function activeOfficeId(Request $request): ?int
    {
        return $request->user()?->activeOfficeId();
    }

    private function allowedOfficeIds(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return [];
        }

        if ($user->hasRole('super_admin')) {
            $officeId = $user->activeOfficeId();
            return $officeId ? [$officeId] : [];
        }

        if ($user->hasRole('owner')) {
            $officeId = $user->activeOfficeId();
            return $officeId ? [$officeId] : [];
        }

        if ($user->hasRole('admin')) {
            return $user->office_id ? [$user->office_id] : [];
        }

        return $user->office_id ? [$user->office_id] : [];
    }

    private function ensureOffInScope(Request $request, Off $off): void
    {
        $allowedOfficeIds = $this->allowedOfficeIds($request);

        if (!in_array((int) $off->office_id, $allowedOfficeIds)) {
            abort(403, 'This off record does not belong to the selected office.');
        }
    }

    public function index(Request $request)
    {
        $allowedOfficeIds = $this->allowedOfficeIds($request);

        $offs = Off::whereDate('date', '>=', Carbon::today())
            ->whereIn('office_id', $allowedOfficeIds)
            ->orderBy('date')
            ->get();

        return view('dashboard.off.index', compact('offs'));
    }

    public function create(Request $request)
    {
        $allowedOfficeIds = $this->allowedOfficeIds($request);

        if (empty($allowedOfficeIds)) {
            return back()->with('error', 'Please select an office first.');
        }

        $offices = Office::whereIn('id', $allowedOfficeIds)
            ->orderBy('name')
            ->get();

        return view('dashboard.off.create', compact('offices'));
    }

    public function store(Request $request)
    {
        $allowedOfficeIds = $this->allowedOfficeIds($request);
        $activeOfficeId = $this->activeOfficeId($request);

        $request->validate([
            'title' => 'required',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'office_id' => 'nullable|exists:offices,id',
            'is_off' => 'nullable|in:1',
        ]);

        if (empty($allowedOfficeIds) || !$activeOfficeId) {
            return back()->with('error', 'Please select an office first.')->withInput();
        }

        $officeId = $request->filled('office_id') ? (int) $request->office_id : (int) $activeOfficeId;

        if (!in_array($officeId, $allowedOfficeIds)) {
            abort(403, 'You are not allowed to create an off record for this office.');
        }

        $off = Off::create([
            'title' => $request->title,
            'date' => $request->date,
            'description' => $request->description,
            'office_id' => $officeId,
            'is_off' => $request->is_off ?? null,
        ]);

        if ($off) {
            $off->load('office.users');

            foreach ($off->office->users as $user) {
                dispatch(new SendOffNotification($user, $off));
            }

            $request->session()->flash('success', 'Off created successfully');
        } else {
            $request->session()->flash('error', 'Failed, try again!');
        }

        return redirect('off');
    }

    public function edit(Request $request, Off $off)
    {
        $this->ensureOffInScope($request, $off);

        $allowedOfficeIds = $this->allowedOfficeIds($request);

        $offices = Office::whereIn('id', $allowedOfficeIds)
            ->orderBy('name')
            ->get();

        return view('dashboard.off.edit', compact('offices', 'off'));
    }

    public function update(Request $request, Off $off)
    {
        $this->ensureOffInScope($request, $off);

        $allowedOfficeIds = $this->allowedOfficeIds($request);
        $activeOfficeId = $this->activeOfficeId($request);

        $request->validate([
            'title' => 'required',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'office_id' => 'nullable|exists:offices,id',
            'is_off' => 'nullable|in:1',
        ]);

        $officeId = $request->filled('office_id') ? (int) $request->office_id : (int) $activeOfficeId;

        if (!in_array($officeId, $allowedOfficeIds)) {
            abort(403, 'You are not allowed to update this off record for that office.');
        }

        $status = $off->update([
            'title' => $request->title,
            'date' => $request->date,
            'description' => $request->description,
            'office_id' => $officeId,
            'is_off' => $request->is_off ?? $off->is_off,
        ]);

        if ($status) {
            $request->session()->flash('success', 'Updated successfully');
        } else {
            $request->session()->flash('error', 'Failed, try again!');
        }

        return redirect('off');
    }

    public function delete(Request $request, Off $off)
    {
        $this->ensureOffInScope($request, $off);

        $status = $off->delete();

        if ($status) {
            $request->session()->flash('success', 'Deleted successfully');
        } else {
            $request->session()->flash('error', 'Failed, try again!');
        }

        return back();
    }
}
