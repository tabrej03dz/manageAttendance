<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FrontController extends Controller
{
    public function mainpage()
    {
        return view('mainpage.index');
    }

    public function employeeForm()
    {
        return view('mainpage.employee_form');
    }

    public function employeeRegister(Request $request){
        $data = $request->validate([
            // required
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255', Rule::unique('users','email')],
            'password' => ['required','string','min:4','max:255'],

            // optional
            'email1'          => ['nullable','email','max:255'],
            'phone'           => ['nullable','string','max:255'],
            'address'         => ['nullable','string'],
            'joining_date'    => ['nullable','date'],
            'designation'     => ['nullable','string','max:255'],
            'responsibility'  => ['nullable','string'],
            'salary'          => ['nullable','numeric','min:0'],

            'check_in_time'   => ['nullable','date_format:H:i'],
            'check_out_time'  => ['nullable','date_format:H:i'],
            'office_time'     => ['nullable','integer','min:0'],
            'break'           => ['nullable','string','max:255'],

            'team_leader_id'  => ['nullable','integer'],   // optionally exists:employees,id
            'office_id'       => ['nullable','integer'],   // optionally exists:offices,id
            'department_id'   => ['nullable','integer'],   // optionally exists:departments,id

            'employee_id'     => ['nullable','string','max:255'],
            'uan_number'      => ['nullable','string','max:255'],
            'esic_number'     => ['nullable','string','max:255'],
            'account_number'  => ['nullable','string','max:255'],

            'location_required' => ['nullable'],
            'is_accepted'       => ['nullable'],
            'status'            => ['nullable'],

            // files
            'photo'            => ['nullable','image','max:4096'], // 4MB
            'aadhar_attachment'=> ['nullable','file','max:10240'], // 10MB
            'pan_attachment'   => ['nullable','file','max:10240'],
            'other_attachment' => ['nullable','file','max:10240'],
        ]);

        // ✅ hash password
        $data['password'] = Hash::make($data['password']);

        // ✅ files upload (public disk)
        // NOTE: run `php artisan storage:link`
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employees/photos', 'public');
        }

        if ($request->hasFile('aadhar_attachment')) {
            $data['aadhar_attachment'] = $request->file('aadhar_attachment')->store('employees/docs/aadhar', 'public');
        }

        if ($request->hasFile('pan_attachment')) {
            $data['pan_attachment'] = $request->file('pan_attachment')->store('employees/docs/pan', 'public');
        }

        if ($request->hasFile('other_attachment')) {
            $data['other_attachment'] = $request->file('other_attachment')->store('employees/docs/other', 'public');
        }

        // ✅ DB insert
        $employee = User::create($data + ['status' => '0']);

        return back()
            ->with('success', 'Employee created successfully!');
    }

    public function blogs()
    {
        return view('mainpage.blog');
    }

    public function blogDetailsPage()
    {
        return view('mainpage.blogDetailsPage');
    }

    public function reqDemo()
    {
        return view('mainpage.reqaDemo');
    }
    public function thankyou()
    {
        return view('mainpage.thankyou');
    }


}
