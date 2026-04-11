@extends('dashboard.layout.root')
@section('content')

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="p-4 sm:p-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        {{-- Header --}}
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Employee Roster</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Current month roster status update with instant save
                    </p>
                </div>

                <form action="{{ route('rosters.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="month"
                           name="month"
                           value="{{ $month }}"
                           class="h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white font-semibold shadow hover:bg-red-700 transition">
                        <span class="material-icons text-base">calendar_month</span>
                        Load
                    </button>
                </form>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-50 text-green-700 border border-green-200">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> Working
                </span>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-red-700 border border-red-200">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Off
                </span>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">
                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Half Day
                </span>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Leave
                </span>
            </div>
        </div>

        {{-- Table --}}
        <div class="p-4 sm:p-6">
            <div class="overflow-auto rounded-xl border border-gray-200">
                <table class="min-w-max w-full bg-white border-collapse">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="sticky left-0 z-20 bg-gray-100 border border-gray-200 px-4 py-3 text-left text-xs font-bold text-gray-700 min-w-[220px]">
                                Employee
                            </th>

                            @foreach($days as $day)
                                <th class="border border-gray-200 px-3 py-2 text-center min-w-[130px] {{ $day['is_today'] ? 'bg-red-50' : '' }}">
                                    <div class="text-xs font-bold text-gray-800">{{ $day['day'] }}</div>
                                    <div class="text-[11px] text-gray-500">{{ $day['day_name'] }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($rows as $index => $row)
                            <tr class="hover:bg-gray-50/60">
                                <td class="sticky left-0 z-10 bg-white border border-gray-200 px-4 py-3 align-top">
                                    <div class="flex items-center gap-3">
                                        <img
                                            src="{{ $row['employee']->photo ? asset('storage/' . $row['employee']->photo) : 'https://via.placeholder.com/80' }}"
                                            alt="{{ $row['employee']->name }}"
                                            class="w-10 h-10 rounded-full object-cover border border-gray-200"
                                        >
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $row['employee']->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                ID: {{ $row['employee']->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                @foreach($row['items'] as $item)
                                    <td class="border border-gray-200 px-2 py-2 text-center">
                                        <div class="relative">
                                            <select
                                                class="roster-status w-full h-10 rounded-lg border text-sm px-2 focus:outline-none focus:ring-2"
                                                data-employee-id="{{ $row['employee']->id }}"
                                                data-duty-date="{{ $item['date'] }}"
                                            >
                                                <option value="working" {{ $item['status'] === 'working' ? 'selected' : '' }}>Working</option>
                                                <option value="off" {{ $item['status'] === 'off' ? 'selected' : '' }}>Off</option>
                                                <option value="half_day" {{ $item['status'] === 'half_day' ? 'selected' : '' }}>Half Day</option>
                                                <option value="leave" {{ $item['status'] === 'leave' ? 'selected' : '' }}>Leave</option>
                                            </select>

                                            <div class="save-indicator mt-1 text-[11px] hidden"></div>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($days) + 1 }}" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No active employees found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selects = document.querySelectorAll('.roster-status');

    function applyStyle(select) {
        select.classList.remove(
            'border-gray-300','focus:ring-gray-200',
            'border-green-300','bg-green-50','text-green-700','focus:ring-green-200',
            'border-red-300','bg-red-50','text-red-700','focus:ring-red-200',
            'border-yellow-300','bg-yellow-50','text-yellow-700','focus:ring-yellow-200',
            'border-blue-300','bg-blue-50','text-blue-700','focus:ring-blue-200'
        );

        switch (select.value) {
            case 'working':
                select.classList.add('border-green-300','bg-green-50','text-green-700','focus:ring-green-200');
                break;
            case 'off':
                select.classList.add('border-red-300','bg-red-50','text-red-700','focus:ring-red-200');
                break;
            case 'half_day':
                select.classList.add('border-yellow-300','bg-yellow-50','text-yellow-700','focus:ring-yellow-200');
                break;
            case 'leave':
                select.classList.add('border-blue-300','bg-blue-50','text-blue-700','focus:ring-blue-200');
                break;
            default:
                select.classList.add('border-gray-300','focus:ring-gray-200');
        }
    }

    function showIndicator(select, message, type = 'success') {
        const indicator = select.parentElement.querySelector('.save-indicator');
        indicator.classList.remove('hidden', 'text-green-600', 'text-red-600', 'text-gray-500');

        if (type === 'success') {
            indicator.classList.add('text-green-600');
        } else if (type === 'error') {
            indicator.classList.add('text-red-600');
        } else {
            indicator.classList.add('text-gray-500');
        }

        indicator.textContent = message;

        clearTimeout(indicator._timer);
        indicator._timer = setTimeout(() => {
            indicator.classList.add('hidden');
        }, 2000);
    }

    selects.forEach(select => {
        applyStyle(select);

        select.addEventListener('change', function () {
            const employeeId = this.dataset.employeeId;
            const dutyDate = this.dataset.dutyDate;
            const status = this.value;

            applyStyle(this);
            this.disabled = true;
            showIndicator(this, 'Saving...', 'info');

            fetch("{{ route('rosters.ajax-upsert') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    employee_id: employeeId,
                    duty_date: dutyDate,
                    status: status
                })
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw data;
                }
                return data;
            })
            .then(data => {
                showIndicator(this, 'Saved');
            })
            .catch(error => {
                let msg = 'Save failed';
                if (error && error.message) {
                    msg = error.message;
                } else if (error && error.errors) {
                    const firstKey = Object.keys(error.errors)[0];
                    if (firstKey) {
                        msg = error.errors[firstKey][0];
                    }
                }
                showIndicator(this, msg, 'error');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
});
</script>

@endsection