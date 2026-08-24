<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class OfficeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = $user->hasRole('owner')
            ? $user->offices()
            : Office::query();

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($officeQuery) use ($search) {
                $officeQuery
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('employee_prefix', 'like', '%' . $search . '%')
                    ->orWhere('latitude', 'like', '%' . $search . '%')
                    ->orWhere('longitude', 'like', '%' . $search . '%')
                    ->orWhere('radius', 'like', '%' . $search . '%');
            });
        }

        $status = $request->input('status', 'active');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('under_radius_required')) {
            $query->where(
                'under_radius_required',
                $this->radiusDatabaseValue(
                    $request->input('under_radius_required')
                )
            );
        }

        $offices = $query
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.office.index', compact('offices'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $owners = User::role('owner')
                ->orderBy('name')
                ->get();
        } else {
            $plan = Plan::query()
                ->where('user_id', $user->id)
                ->latest('id')
                ->first();

            if (
                $plan &&
                (int) $plan->number_of_offices <= $user->offices()->count()
            ) {
                return back()->with(
                    'error',
                    'Your office creation limit is exceeded.'
                );
            }

            $owners = null;
        }

        return view('dashboard.office.create', compact('owners'));
    }

    public function store(Request $request)
    {
        $this->normalizeOfficeRequest($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'employee_prefix' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('offices', 'employee_prefix'),
            ],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'min:0'],
            'number_of_employees' => ['nullable', 'integer', 'min:0'],
            'price_per_employee' => ['nullable', 'numeric', 'min:0'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'address' => ['nullable', 'string', 'max:5000'],
            'under_radius_required' => [
                'required',
                'boolean',
            ],
            'otp_enable' => ['required', 'boolean'],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'employee_prefix.regex' =>
                'Employee prefix may contain uppercase letters and numbers only.',
            'employee_prefix.unique' =>
                'This employee prefix is already being used by another office.',
        ]);

        $user = $request->user();

        if (!$user->hasRole('super_admin')) {
            $validated['owner_id'] = $user->id;
        }

        $data = $this->officeData($validated);
        $data['employee_sequence'] = 0;

        $uploadedLogo = null;

        try {
            if ($request->hasFile('logo')) {
                $uploadedLogo = $request
                    ->file('logo')
                    ->store('office-logos', 'public');

                $data['logo'] = $uploadedLogo;
            }

            Office::query()->create($data);
        } catch (Throwable $exception) {
            if (
                $uploadedLogo &&
                Storage::disk('public')->exists($uploadedLogo)
            ) {
                Storage::disk('public')->delete($uploadedLogo);
            }

            report($exception);

            return back()
                ->with('error', 'Office could not be created. Please try again.')
                ->withInput();
        }

        return redirect()
            ->route('office.index')
            ->with('success', 'Office created successfully.');
    }

    public function edit(Office $office)
    {
        $this->authorizeOfficeAccess($office);

        $owners = auth()->user()->hasRole('super_admin')
            ? User::role('owner')->orderBy('name')->get()
            : null;

        $office->loadMissing('owner');

        return view(
            'dashboard.office.edit',
            compact('office', 'owners')
        );
    }

    public function update(Request $request, Office $office)
    {
        $this->authorizeOfficeAccess($office);
        $this->normalizeOfficeRequest($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'employee_prefix' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('offices', 'employee_prefix')
                    ->ignore($office->id),
            ],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'min:0'],
            'number_of_employees' => ['nullable', 'integer', 'min:0'],
            'price_per_employee' => ['nullable', 'numeric', 'min:0'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'address' => ['nullable', 'string', 'max:5000'],
            'under_radius_required' => [
                'required',
                'boolean',
            ],
            'otp_enable' => ['required', 'boolean'],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'employee_prefix.regex' =>
                'Employee prefix may contain uppercase letters and numbers only.',
            'employee_prefix.unique' =>
                'This employee prefix is already being used by another office.',
        ]);

        if ($request->user()->hasRole('super_admin')) {
            /*
             * The compact edit form currently displays the owner in the
             * summary but does not always submit owner_id. Never overwrite the
             * existing owner with null when the field was not submitted.
             */
            $validated['owner_id'] = $request->filled('owner_id')
                ? (int) $validated['owner_id']
                : $office->owner_id;
        } else {
            $validated['owner_id'] = $office->owner_id
                ?: $request->user()->id;
        }

        $data = $this->officeData($validated);

        /*
         * Do not reset employee_sequence during update. Existing employees and
         * the next generated employee ID depend on the saved sequence.
         */

        $oldLogo = $office->logo;
        $newLogo = null;

        try {
            if ($request->hasFile('logo')) {
                $newLogo = $request
                    ->file('logo')
                    ->store('office-logos', 'public');

                $data['logo'] = $newLogo;
            }

            $office->update($data);

            if (
                $newLogo &&
                $oldLogo &&
                $oldLogo !== $newLogo &&
                Storage::disk('public')->exists($oldLogo)
            ) {
                Storage::disk('public')->delete($oldLogo);
            }
        } catch (Throwable $exception) {
            if (
                $newLogo &&
                Storage::disk('public')->exists($newLogo)
            ) {
                Storage::disk('public')->delete($newLogo);
            }

            report($exception);

            $message = config('app.debug')
                ? $exception->getMessage()
                : 'Office could not be updated. Please try again.';

            return back()
                ->with('error', $message)
                ->withInput();
        }

        return redirect()
            ->route('office.index')
            ->with('success', 'Office updated successfully.');
    }

    public function delete(Office $office)
    {
        $this->authorizeOfficeAccess($office);

        $logo = $office->logo;
        $office->delete();

        if (
            $logo &&
            Storage::disk('public')->exists($logo)
        ) {
            Storage::disk('public')->delete($logo);
        }

        return back()->with('success', 'Office deleted successfully.');
    }

    public function status(Office $office)
    {
        $this->authorizeOfficeAccess($office);

        $office->status = $office->status === 'active'
            ? 'inactive'
            : 'active';

        $office->save();

        return back()->with('success', 'Status changed successfully.');
    }

    public function detail(Office $office)
    {
        $this->authorizeOfficeAccess($office);

        $payments = $office->payments;

        return view(
            'dashboard.office.detail',
            compact('payments', 'office')
        );
    }

    private function normalizeOfficeRequest(Request $request): void
    {
        $request->merge([
            'employee_prefix' => strtoupper(
                trim((string) $request->input('employee_prefix'))
            ),
            'under_radius_required' =>
                $request->boolean('under_radius_required'),
            'otp_enable' => $request->boolean('otp_enable'),
        ]);
    }

    private function radiusDatabaseValue($value): bool
    {
        return in_array(
            strtolower(trim((string) $value)),
            ['1', 'true', 'yes', 'on', 'enable', 'enabled', 'required'],
            true
        );
    }

    private function officeData(array $validated): array
    {
        return [
            'name' => trim((string) $validated['name']),
            'employee_prefix' => $validated['employee_prefix'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'radius' => $validated['radius'] ?? null,
            'number_of_employees' =>
                $validated['number_of_employees'] ?? null,
            'price_per_employee' =>
                $validated['price_per_employee'] ?? null,
            'owner_id' => $validated['owner_id'] ?? null,
            'address' => $validated['address'] ?? null,
            'under_radius_required' =>
                (bool) $validated['under_radius_required'],
            'otp_enable' => (bool) $validated['otp_enable'],
        ];
    }

    private function authorizeOfficeAccess(Office $office): void
    {
        $user = auth()->user();

        if (
            $user->hasRole('owner') &&
            (int) $office->owner_id !== (int) $user->id
        ) {
            abort(403, 'You cannot access this office.');
        }
    }
}