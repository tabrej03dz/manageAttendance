

@extends('dashboard.layout.root')

@section('content')

    <div class="pt-10 pb-20">
        <!-- Card Container with border -->
        <div class="w-full max-w-screen-lg mx-auto p-6 bg-white rounded-lg shadow-lg border border-red-500">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-semibold text-gray-900">New Leave</h2>
            </div>

            <form action="{{route('leave.store')}}" method="post">
            @csrf
            <!-- Leave Type Selection -->
            <div class="mt-4">
                <div>
                    <label for="leaveType" class="mb-2 font-semibold text-gray-700">Leave Type</label>
                </div>
                <select name="leave_type"
                    class="mb-4 form-select bg-white border  rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-500 transition duration-150 ease-in-out hover:bg-gray-50 text-gray-700 py-2 px-3 w-100"
                    id="leaveType" required>
                    <option value="" disabled selected>Select leave type</option>
                    <option value="annual">Annual Leave</option>
                    <option value="sick">Sick Leave</option>
                    <option value="casual">Casual Leave</option>
                    <option value="personal">Personal Leave</option>
                    <option value="others">Others</option>
                </select>
            </div>

                <div class="mt-4">
                    <h2>Request for--</h2>
                    <div>
                        <input type="radio" value="1" id="paid" name="is_paid">
                        <label for="paid" class="mb-2 font-semibold text-gray-700">Paid Leave</label>
                        <input type="radio" value="0" id="unpaid" name="is_paid" checked>
                        <label for="unpaid" class="mb-2 font-semibold text-gray-700">Unpaid Leave</label>
                    </div>

                </div>


            <!-- Date From Field -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-1" for="date-from">From</label>
                <div class="flex items-center border border-gray-300 rounded-md overflow-hidden">
                    <span class="flex items-center justify-center w-10 h-10 bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 000 2v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-2V4a1 1 0 100-2h-2V1a1 1 0 10-2 0v1H6zm2 0h4v1H8V2zM4 7h12v10H4V7zm2 3a1 1 0 100 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input type="text" id="date-from" name="start_date" class="w-full p-2 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Select date">
                </div>
            </div>

            <!-- Date To Field -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-1" for="date-to">To</label>
                <div class="flex items-center border border-gray-300 rounded-md overflow-hidden">
                    <span class="flex items-center justify-center w-10 h-10 bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 000 2v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-2V4a1 1 0 100-2h-2V1a1 1 0 10-2 0v1H6zm2 0h4v1H8V2zM4 7h12v10H4V7zm2 3a1 1 0 100 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input type="text" id="date-to" name="end_date" class="w-full p-2 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Select date">
                </div>
            </div>


                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-1" for="subject">Subject</label>
                    <input id="subject" name="subject" class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Subject"/>
                </div>
            <!-- Reason Field -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-1" for="reason">Reason for applying</label>
                <textarea id="reason" name="reason" class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" rows="4" placeholder="Enter your reason here"></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between">
                <!-- <button class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400 hover:bg-gray-400">Cancel</button> -->
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 hover:bg-red-500">Send for approval</button>
            </div>

            </form>
        </div>
    </div>

    <!-- Add Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize Flatpickr on the date inputs
        flatpickr("#date-from", {
            dateFormat: "Y-m-d",
            // defaultDate: "today"
        });

        flatpickr("#date-to", {
            dateFormat: "Y-m-d",
            // defaultDate: "today"
        });
    </script>
@endsection
