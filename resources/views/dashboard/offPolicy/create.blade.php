@extends('dashboard.layout.root')
@section('content')
    <div class="container">
        <h2>Create Leave Policy</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('off_policy.store') }}" method="POST">
            @csrf

            <!-- Office Selection -->
            <div class="mb-3">
                <label for="office_id" class="form-label">Select Office:</label>
                <select name="office_id" id="office_id" class="form-control" required>
                    <option value="">-- Select Office --</option>
                    @foreach(\App\Models\Office::all() as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Weekly Off Policy -->
            @php
                $weeks = ['first_week' => 'First Week', 'second_week' => 'Second Week', 'third_week' => 'Third Week', 'fourth_week' => 'Fourth Week'];
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            @endphp

            @foreach($weeks as $week_key => $week_name)
                <div class="mb-3 border p-3">
                    <label class="form-label">{{ $week_name }} - Number of Offs:</label>
                    <input type="number" name="weekly_off_policy[{{ $week_key }}][off_count]" class="form-control mb-2" min="0" max="7" value="0">

                    <label class="form-label">Select Off Days:</label>
                    <div class="d-flex flex-wrap">
                        @foreach($days as $day)
                            <div class="form-check me-2">
                                <input type="checkbox" class="form-check-input" name="weekly_off_policy[{{ $week_key }}][off_days][]" value="{{ $day }}">
                                <label class="form-check-label">{{ $day }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <button type="submit" class="btn btn-primary">Save Policy</button>
        </form>
    </div>
@endsection
