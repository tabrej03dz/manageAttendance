@extends('dashboard.layout.root')
@section('content')

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

@php
    $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $month);

    $dayMap = [
        'Mon' => 'Mo',
        'Tue' => 'Tu',
        'Wed' => 'We',
        'Thu' => 'Th',
        'Fri' => 'Fr',
        'Sat' => 'Sa',
        'Sun' => 'Su',
    ];

    $statusMap = [
        'working'  => 'P',
        'off'      => 'O',
        'half_day' => 'H',
        'leave'    => 'L',
    ];
@endphp

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

                <div class="flex flex-wrap items-center gap-2">
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

                    <button type="button"
                            id="downloadRosterPdf"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white font-semibold shadow hover:bg-green-700 transition">
                        <span class="material-icons text-base">picture_as_pdf</span>
                        Download PDF
                    </button>
                </div>
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

        {{-- Editable Table --}}
        <div class="p-4 sm:p-6">
            <div class="overflow-auto rounded-xl border border-gray-200">
                <table class="min-w-max w-full bg-white border-collapse">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="sticky left-0 z-20 bg-gray-100 border border-gray-200 px-4 py-3 text-left text-xs font-bold text-gray-700 min-w-[220px]">
                                Employee
                            </th>

                            @foreach($days as $day)
                                @php $isSunday = $day['day_name'] === 'Sun'; @endphp
                                <th class="border border-gray-200 px-3 py-2 text-center min-w-[130px] {{ $day['is_today'] ? 'bg-red-50' : '' }}">
                                    <div class="text-xs font-bold {{ $isSunday ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ $day['day'] }}
                                    </div>
                                    <div class="text-[11px] {{ $isSunday ? 'text-red-500' : 'text-gray-500' }}">
                                        {{ $day['day_name'] }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($rows as $row)
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

{{-- Export Layout --}}
<div id="rosterExportWrap"
     style="
        position: fixed;
        left: -100000px;
        top: 0;
        z-index: -1;
        opacity: 1;
        pointer-events: none;
        background: #ffffff;
        padding: 0;
     ">
    <div id="rosterPoster"
         style="
            width: 1900px;
            background: #f2f2f2;
            border: 3px solid #383838;
            border-radius: 24px;
            padding: 18px;
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
            box-sizing: border-box;
         ">

        {{-- Top Header --}}
        <div style="display:flex; align-items:stretch; border:2px solid #383838; border-radius:18px 18px 0 0; overflow:hidden; background:#efefef;">
            <div style="width:38%; padding:16px 20px; display:flex; align-items:center; gap:14px; border-right:2px solid #383838;">
                <img
                    src="https://www.realvictorygroups.com/asset/img/logo-w-removebg-preview.png"
                    alt="RVG Logo"
                    crossorigin="anonymous"
                    style="height:90px; object-fit:contain;"
                >
                <div>
                    <div style="font-size:28px; font-weight:800; line-height:1.1;">
                        REAL <span style="color:#c83535;">VICTORY</span> GROUPS
                    </div>
                    <div style="font-size:14px; letter-spacing:2px; margin-top:6px;">
                        THINK OUTSIDE THE BOX
                    </div>
                </div>
            </div>

            <div style="width:62%; padding:22px 20px; text-align:center;">
                <div style="font-size:48px; font-weight:900; letter-spacing:1px;">
                    <span style="color:#c83535;">{{ strtoupper($monthDate->format('M')) }}-MONTH</span>
                    DUTY ROSTER
                </div>
            </div>
        </div>

        {{-- Roster Table --}}
        <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-top:0;">
            <thead>
                <tr>
                    <th style="border:2px solid #383838; width:48px; height:48px; background:#f7f7f7;"></th>
                    <th style="border:2px solid #383838; width:190px; background:#f7f7f7;"></th>

                    @foreach($days as $day)
                        @php $isSunday = $day['day_name'] === 'Sun'; @endphp
                        <th style="
                            border:2px solid #383838;
                            height:48px;
                            font-size:20px;
                            font-weight:800;
                            background:#fafafa;
                            color: {{ $isSunday ? '#c83535' : '#111111' }};
                        ">
                            {{ $day['day'] }}
                        </th>
                    @endforeach
                </tr>

                <tr>
                    <th style="border:2px solid #383838; height:54px; font-size:18px; font-weight:800; color:#c83535; background:#f7f7f7;">
                        No.
                    </th>
                    <th style="border:2px solid #383838; font-size:22px; font-weight:800; color:#c83535; background:#f7f7f7;">
                        EMPLOY
                    </th>

                    @foreach($days as $day)
                        @php
                            $isSunday = $day['day_name'] === 'Sun';
                            $shortDay = $dayMap[$day['day_name']] ?? $day['day_name'];
                        @endphp
                        <th style="
                            border:2px solid #383838;
                            height:54px;
                            font-size:14px;
                            font-weight:700;
                            background:#fafafa;
                            color: {{ $isSunday ? '#c83535' : '#444444' }};
                        ">
                            {{ $shortDay }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @forelse($rows as $i => $row)
                    <tr>
                        <td style="
                            border:2px solid #383838;
                            height:58px;
                            text-align:center;
                            font-size:18px;
                            font-weight:800;
                            background:#f7f7f7;
                        ">
                            {{ $i + 1 }}
                        </td>

                        <td style="
                            border:2px solid #383838;
                            padding:0 10px;
                            font-size:20px;
                            font-weight:700;
                            background:#f7f7f7;
                            white-space:nowrap;
                            overflow:hidden;
                            text-overflow:ellipsis;
                        ">
                            {{ $row['employee']->name ?? '' }}
                        </td>

                        @foreach($days as $dayIndex => $day)
                            @php
                                $status = $row['items'][$dayIndex]['status'] ?? 'working';
                                $short  = $statusMap[$status] ?? 'P';
                                $color = $short === 'O' ? '#c83535' : '#222222';
                            @endphp

                            <td style="
                                border:2px solid #383838;
                                text-align:center;
                                font-size:24px;
                                font-weight:700;
                                height:58px;
                                background:#fafafa;
                                color: {{ $color }};
                            ">
                                {{ $short }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($days) + 2 }}" style="
                            border:2px solid #383838;
                            text-align:center;
                            font-size:20px;
                            font-weight:700;
                            height:80px;
                            background:#fafafa;
                            color:#666666;
                        ">
                            No active employees found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selects = document.querySelectorAll('.roster-status');
    const downloadBtn = document.getElementById('downloadRosterPdf');

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

    async function waitForImages(container) {
        const images = Array.from(container.querySelectorAll('img'));
        await Promise.all(images.map(img => {
            if (img.complete) return Promise.resolve();
            return new Promise(resolve => {
                img.onload = resolve;
                img.onerror = resolve;
            });
        }));
    }

    async function waitForFonts() {
        if (document.fonts && document.fonts.ready) {
            await document.fonts.ready;
        }
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
            .then(() => {
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

    downloadBtn.addEventListener('click', async function () {
        const button = this;
        const poster = document.getElementById('rosterPoster');
        const wrap   = document.getElementById('rosterExportWrap');

        try {
            button.disabled = true;
            button.innerHTML = '<span class="material-icons text-base">hourglass_top</span> Preparing...';

            wrap.style.left = '0px';
            wrap.style.top = '0px';

            await waitForFonts();
            await waitForImages(poster);

            const canvas = await html2canvas(poster, {
                scale: 2,
                useCORS: true,
                allowTaint: false,
                backgroundColor: '#f2f2f2',
                logging: false,
                imageTimeout: 0
            });

            if (!canvas || canvas.width === 0 || canvas.height === 0) {
                throw new Error('Canvas render failed.');
            }

            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;

            const pdf = new jsPDF({
                orientation: canvas.width > canvas.height ? 'landscape' : 'portrait',
                unit: 'px',
                format: [canvas.width, canvas.height]
            });

            pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
            pdf.save('roster-{{ $month }}.pdf');

            wrap.style.left = '-100000px';
            button.disabled = false;
            button.innerHTML = '<span class="material-icons text-base">picture_as_pdf</span> Download PDF';
        } catch (error) {
            console.error(error);
            alert('PDF download failed. Check console.');
            wrap.style.left = '-100000px';
            button.disabled = false;
            button.innerHTML = '<span class="material-icons text-base">picture_as_pdf</span> Download PDF';
        }
    });
});
</script>

@endsection