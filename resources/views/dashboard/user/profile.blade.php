@extends('dashboard.layout.root')
@section('content')
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <div class="pb-24">
        <!-- Profile Details -->
        <div class="bg-gray-100 min-h-screen">
            <div class="container mx-auto px-4 py-8">
                <!-- Full width on web, max width on mobile -->

                <!-- Header -->
                <div class="bg-red-600 text-white font-semibold text-2xl text-center py-3 shadow-lg rounded-t-lg">
                    Profile Details
                </div>

                <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full  mx-auto">
                    <form action="{{ route('profile.update', ['user' => $user->id]) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="p-6">
                            <!-- Profile Picture -->
                            <div class="flex flex-col items-center mb-6">
                                <img id="photoPreview"
                                    src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://via.placeholder.com/100' }}"
                                    alt="Profile Avatar" class="w-24 h-24 rounded-full mb-4 border">
                                <label class="text-gray-700 text-sm font-medium">
                                    Upload Profile Photo
                                </label>
                                <input type="file" name="photo"
                                    class="mt-2 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    onchange="previewImage(event, 'photoPreview')">
                            </div>

                            <!-- Personal Details Section -->
                            <div class="space-y-4">
                                <!-- Name Field -->
                                <div class="flex flex-col">
                                    <label for="name" class="text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" id="name" name="name"
                                        class="text-gray-800 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        value="{{ $user->name }}" disabled>
                                </div>

                                <!-- Designation Field -->
                                <div class="flex flex-col">
                                    <label for="designation" class="text-sm font-medium text-gray-700">Designation</label>
                                    <input type="text" id="designation" name="designation"
                                        class="text-gray-800 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        value="{{ $user->designation }}" disabled>
                                </div>

                                <!-- Responsibility Field -->
                                <div class="flex flex-col">
                                    <label for="responsibility"
                                        class="text-sm font-medium text-gray-700">Responsibility</label>
                                    <input type="text" id="responsibility" name="responsibility"
                                        class="text-gray-800 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        value="{{ $user->responsibility }}">
                                </div>

                                <!-- Joining Date Field -->
                                <div class="flex flex-col">
                                    <label for="joining_date" class="text-sm font-medium text-gray-700">Joining Date</label>
                                    <input type="date" id="joining_date" name="joining_date"
                                        class="text-gray-800 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        value="{{ $user->joining_date }}" disabled>
                                </div>

                                <!-- Email Field -->
                                <div class="flex flex-col">
                                    <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" id="email" name="email"
                                        class="text-gray-800 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        value="{{ $user->email }}" disabled>
                                </div>

                                <!-- Phone Number Field -->
                                <div class="flex flex-col">
                                    <label for="phone" class="text-sm font-medium text-gray-700">Phone</label>
                                    <input type="text" id="phone" name="phone"
                                        class="text-gray-800 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        value="{{ $user->phone }}" disabled>
                                </div>

                                <!-- Salary Field -->
                                <div class="flex flex-col">
                                    <label for="salary" class="text-sm font-medium text-gray-700">Salary</label>
                                    <input type="text" id="salary" name="salary"
                                        class="text-gray-800 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        value="{{ $user->salary }}" disabled>
                                </div>

                                <!-- Upload Pancard -->
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-800">Upload Pancard</label>
                                    <div class="flex items-center space-x-4">
                                        <input type="file" id="panAttachment" name="pan_attachment" class="hidden"
                                            onchange="previewImage(event, 'panPreview')">
                                        <label for="panAttachment"
                                            class="cursor-pointer bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg py-2 px-4 flex items-center space-x-2 transition duration-200 ease-in-out transform hover:scale-105">
                                            <span class="material-icons">cloud_upload</span>
                                            <span class="text-sm">Choose Pancard</span>
                                        </label>
                                        <div class="relative">
                                            <img id="panPreview"
                                                src="{{ $user->pan_attachment ? asset('storage/' . $user->pan_attachment) : '' }}"
                                                alt="Pancard Preview"
                                                class="w-20 h-20 border border-gray-300 rounded-lg cursor-pointer"
                                                onclick="openModal('panPreview')">
                                            <div class="absolute top-0 right-0 text-xs text-gray-500 bg-white px-1 py-0.5 rounded-full hidden"
                                                id="panFileName">
                                                No file selected
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload Aadharcard -->
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-800">Upload Aadharcard</label>
                                    <div class="flex items-center space-x-4">
                                        <input type="file" id="aadharAttachment" name="aadhar_attachment"
                                            class="hidden" onchange="previewImage(event, 'aadharPreview')">
                                        <label for="aadharAttachment"
                                            class="cursor-pointer bg-green-100 hover:bg-green-200 text-green-600 rounded-lg py-2 px-4 flex items-center space-x-2 transition duration-200 ease-in-out transform hover:scale-105">
                                            <span class="material-icons">cloud_upload</span>
                                            <span class="text-sm">Choose Aadharcard</span>
                                        </label>
                                        <div class="relative">
                                            <img id="aadharPreview"
                                                src="{{ $user->aadhar_attachment ? asset('storage/' . $user->aadhar_attachment) : '' }}"
                                                alt="Aadharcard Preview"
                                                class="w-20 h-20 border border-gray-300 rounded-lg cursor-pointer"
                                                onclick="openModal('aadharPreview')">
                                            <div class="absolute top-0 right-0 text-xs text-gray-500 bg-white px-1 py-0.5 rounded-full hidden"
                                                id="aadharFileName">
                                                No file selected
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload Other Documents -->
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-800">Upload Other Document</label>
                                    <div class="flex items-center space-x-4">
                                        <input type="file" id="otherAttachment" name="other_attachment"
                                            class="hidden" onchange="previewImage(event, 'otherPreview')">
                                        <label for="otherAttachment"
                                            class="cursor-pointer bg-yellow-100 hover:bg-yellow-200 text-yellow-600 rounded-lg py-2 px-4 flex items-center space-x-2 transition duration-200 ease-in-out transform hover:scale-105">
                                            <span class="material-icons">cloud_upload</span>
                                            <span class="text-sm">Choose File</span>
                                        </label>
                                        <div class="relative">
                                            <img id="otherPreview"
                                                src="{{ $user->other_attachment ? asset('storage/' . $user->other_attachment) : '' }}"
                                                alt="Other Document Preview"
                                                class="w-20 h-20 border border-gray-300 rounded-lg cursor-pointer"
                                                onclick="openModal('otherPreview')">
                                            <div class="absolute top-0 right-0 text-xs text-gray-500 bg-white px-1 py-0.5 rounded-full hidden"
                                                id="otherFileName">
                                                No file selected
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Full Image Modal -->
                                <div id="imageModal"
                                    class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
                                    <div class="relative bg-white p-4 rounded-lg">
                                        <span onclick="closeModal()"
                                            class="absolute top-2 right-2 text-2xl text-gray-500 cursor-pointer">&times;</span>
                                        <img id="modalImage" class="w-full max-w-2xl max-h-screen" />
                                    </div>
                                </div>


                                <!-- Address Field -->
                                <div class="flex flex-col mt-4">
                                    <label for="address" class="text-sm font-medium text-gray-700">Address</label>
                                    <input type="text" id="address" name="address"
                                        class="text-gray-800 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        value="{{ $user->address }}" placeholder="Enter full address">
                                </div>
                            </div>

                            <!-- Update Button -->
                            <div class="mt-6 text-center">
                                <button type="submit"
                                    class="bg-blue-600 text-white py-2 px-6 rounded-lg shadow hover:bg-blue-700 transition">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                {{-- <script>
                    function previewImage(event, previewId) {
                        const file = event.target.files[0];
                        const preview = document.getElementById(previewId);

                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                </script> --}}
                <script>
                    function openModal(imageId) {
                        // Get the image element by its ID
                        var imgElement = document.getElementById(imageId);
                        // Get the modal and modal image element
                        var modal = document.getElementById('imageModal');
                        var modalImg = document.getElementById('modalImage');

                        // Set the modal image source to the clicked image's source
                        modalImg.src = imgElement.src;

                        // Show the modal
                        modal.classList.remove('hidden');
                    }

                    function closeModal() {
                        // Close the modal
                        var modal = document.getElementById('imageModal');
                        modal.classList.add('hidden');
                    }

                    function previewImage(event, previewId) {
                        const inputFile = event.target;
                        const previewImage = document.getElementById(previewId);
                        const fileNameDisplay = document.getElementById(previewId + 'FileName');

                        if (inputFile.files && inputFile.files[0]) {
                            const reader = new FileReader();

                            reader.onload = function (e) {
                                previewImage.src = e.target.result;
                            };

                            reader.readAsDataURL(inputFile.files[0]);

                            // Show the file name
                            fileNameDisplay.textContent = inputFile.files[0].name;
                            fileNameDisplay.classList.remove('hidden');
                        } else {
                            previewImage.src = '';
                            fileNameDisplay.classList.add('hidden');
                        }
                    }
                </script>

            </div>
        </div>


        <!-- OFFICE TIMING -->
        <div class="bg-gray-100 flex items-center justify-center">
            <div class="container mx-auto px-4 py-8">

                <!-- Card with shadow and rounded corners -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Header with a red background -->
                    <div class="bg-red-600 text-white font-semibold text-2xl text-center py-2 shadow-md rounded-t-lg">
                        Office Timing
                    </div>
                    <!-- Form Section -->
                    <form action="{{ route('profile.update', ['user' => $user->id]) }}" method="post">
                        @csrf
                        <div class="p-6 space-y-8">

                            <!-- Office Start Time -->
                            <div class="flex flex-col space-y-2">
                                <label for="start_time" class="text-lg font-semibold text-gray-600">Office Start
                                    Time</label>
                                <input type="time" id="start_time" name="start_time" value="{{ $user->start_time }}"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                            </div>

                            <!-- Office End Time -->
                            <div class="flex flex-col space-y-2">
                                <label for="end_time" class="text-lg font-semibold text-gray-600">Office End Time</label>
                                <input type="time" id="end_time" name="end_time" value="{{ $user->end_time }}"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                            </div>

                            <!-- Break Time (Optional) -->
                            <div class="flex flex-col space-y-2">
                                <label for="break_time" class="text-lg font-semibold text-gray-600">Break Time</label>
                                <input type="time" id="break_time" name="break_time" value="{{ $user->break_time }}"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-6 flex justify-center">
                                <button type="submit"
                                    class="w-1/2 bg-red-600 text-white py-2 rounded-lg shadow hover:bg-red-700 transition duration-300">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>


        <!-- Secondary Email -->
        <div class="bg-gray-100 flex items-center justify-center">
            <div class="container mx-auto px-4 py-8">
                <!-- Card with shadow and rounded corners -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">

                    <!-- Header with a red background -->
                    <div class="bg-red-600 text-white font-semibold text-2xl text-center py-2 shadow-md rounded-t-lg">
                        Secondary Email
                    </div>

                    <!-- Form Section -->
                    <form action="{{ route('profile.update', ['user' => $user->id]) }}" method="post">
                        @csrf
                        <div class="p-6 space-y-6">

                            <!-- Secondary Email Field -->
                            <div class="flex flex-col space-y-2">
                                <label for="email1" class="text-lg font-semibold text-gray-600">Enter your secondary
                                    email</label>
                                <input type="email" id="email1" name="email1" value="{{ $user->email1 }}"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-6 flex justify-center">
                                <button type="submit"
                                    class="w-1/2 bg-red-600 text-white py-2 rounded-lg shadow hover:bg-red-700 transition duration-300">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Change Password -->
        <div class="bg-gray-100 flex items-center justify-center">
            <div class="container mx-auto px-4 py-8">
                <!-- Card with shadow and rounded corners -->
                <form action="{{ route('userPassword', ['user' => $user->id]) }}" method="POST">
                    @csrf
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full md:w-full mx-auto">

                        <!-- Header with a red background -->
                        <div class="bg-red-600 text-white font-semibold text-2xl text-center py-2 shadow-md rounded-t-lg">
                            Change Password
                        </div>

                        <div class="p-6 space-y-6">

                            <!-- Current Password Field -->
                            <div class="flex flex-col space-y-2">
                                <label for="current-password" class="text-lg font-semibold text-gray-600">Current
                                    Password</label>
                                <input type="password" id="current-password" name="current_password"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                    placeholder="Enter your current password">
                            </div>

                            <!-- New Password Field -->
                            <div class="flex flex-col space-y-2">
                                <label for="new-password" class="text-lg font-semibold text-gray-600">New Password</label>
                                <input type="password" id="newPassword" name="new_password"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                    placeholder="Enter your new password">
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="flex flex-col space-y-2">
                                <label for="confirm-password" class="text-lg font-semibold text-gray-600">Confirm New
                                    Password</label>
                                <input type="password" id="confirm-password" name="confirm_password"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                    placeholder="Confirm your new password">
                            </div>

                            <!-- Divider -->
                            <div class="flex items-center justify-between border-b py-2"></div>

                            <!-- Update Password Button -->
                            <div class="mt-6 flex justify-center">
                                <button type="submit"
                                    class="w-1/2 bg-red-600 text-white py-2 rounded-lg shadow hover:bg-red-700 transition duration-300">
                                    Update Password
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Log out Button -->
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit"
                class="mt-6 w-3/4 mx-auto flex justify-center items-center bg-red-600 text-white py-2 rounded-lg shadow hover:bg-red-700 transition duration-300">
                <!-- Icon on the left -->
                <span class="material-icons text-white text-lg mr-2">exit_to_app</span>
                LogOut
            </button>
        </form>
    </div>
@endsection
