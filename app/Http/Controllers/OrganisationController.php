<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganisationRequest;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganisationController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $organisation = Organisation::query();

        if (!empty($keyword)) {
            $organisation->where('owner_name', 'like', "%$keyword%");
        }

        // Order records by the latest created_at
        $organisationData = $organisation->orderBy('created_at', 'desc')->paginate(15);

        return view('organisation.index', compact('organisationData'));
    }

    public function create()
    {
        return view('organisation.form');
    }
    public function store(OrganisationRequest $request)
    {
        // dd($request->all());
        $data = $request->validated();

        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('logos', 'public');
        }

        Organisation::create($data);

        return redirect()->route('organisation.index')->with('success', 'Organisation created successfully!');
    }

    public function edit(Organisation $organisation)
    {
        return view('organisation.form', compact('organisation'));
    }

    public function update(OrganisationRequest $request, Organisation $organisation)
    {
        $data = $request->validated();

        if ($request->hasFile('company_logo')) {
            if ($organisation->company_logo) {
                Storage::disk('public')->delete($organisation->company_logo);
            }
            $data['company_logo'] = $request->file('company_logo')->store('logos', 'public');
        }

        $organisation->update($data);

        return redirect()->route('organisation.index')->with('success', 'Organisation updated successfully!');
    }


    public function destroy(Organisation $organisation)
    {
        $organisation->delete(); // Delete the record
        return redirect()->back()->with('success', 'Record deleted successfully.');
    }
}
