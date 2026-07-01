@php
    use App\Models\Office;

    $activeOffice = null;

    if (isset($employeeLetter) && $employeeLetter->office) {
        $activeOffice = $employeeLetter->office;
    }

    if (!$activeOffice && session('active_office_id')) {
        $activeOffice = Office::find(session('active_office_id'));
    }

    if (!$activeOffice && auth()->check() && auth()->user()->office_id) {
        $activeOffice = Office::find(auth()->user()->office_id);
    }

    $logoPath = null;

    if ($activeOffice) {
        $logoPath = $activeOffice->logo
            ?? $activeOffice->logo_path
            ?? $activeOffice->office_logo
            ?? $activeOffice->image
            ?? $activeOffice->photo
            ?? null;
    }

    if ($logoPath) {
        if (filter_var($logoPath, FILTER_VALIDATE_URL)) {
            $logoUrl = $logoPath;
        } else {
            $logoUrl = asset('storage/' . ltrim($logoPath, '/'));
        }
    } else {
        $logoUrl = asset('images/real-victory-logo.png');
    }

    $officeName = $activeOffice->name ?? session('active_office_name') ?? 'Real Victory Groups';

    $officeAddress = $activeOffice->address
        ?? $activeOffice->office_address
        ?? 'Lakhanpur, Kanpur, Uttar Pradesh';

    $city = $activeOffice->city ?? null;
    $state = $activeOffice->state ?? null;

    $fullAddress = $officeAddress;

    if ($city && !str_contains(strtolower($fullAddress), strtolower($city))) {
        $fullAddress .= ', ' . $city;
    }

    if ($state && !str_contains(strtolower($fullAddress), strtolower($state))) {
        $fullAddress .= ', ' . $state;
    }
@endphp

<div class="letter-header">
    <div class="letter-logo-box">
        <img src="{{ $logoUrl }}" alt="{{ $officeName }}" class="letter-logo">
    </div>

    <div class="letter-office-address">
        Corporate Office: {{ $fullAddress }}
    </div>

    <div class="letter-header-line"></div>
</div>