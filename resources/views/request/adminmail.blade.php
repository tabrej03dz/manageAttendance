<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Request Demo Lead</title>
    <!-- Tailwind CSS -->
    <link href="{{ asset('mainasset/css/style.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for social icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-900">
    <!-- Header -->
    <header class="bg-gray-200 text-white py-8 shadow-lg">
        <div class="flex justify-center items-center">
            <!-- Logo without border, more spacing -->
            <img src="{{ asset('asset/img/logo.png') }}" alt="Real Victory Groups Logo" class="h-32 w-32 rounded-full">
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto my-12 mt-10 p-8 md:p-12 bg-white rounded-xl shadow-lg">
        <h1 class="text-4xl md:text-5xl font-bold text-center text-gray-800 mb-6">New Demo Request Lead</h1>
        <p class="text-lg md:text-xl text-center text-gray-600 mb-8">A new demo request has been submitted. Below are
            the details:</p>
        <ul class="list-disc pl-8 text-lg md:text-xl text-gray-700 space-y-4">
            <li><strong class="font-semibold">Company Name:</strong> {{ $requestDemo->compan_name }}</li>
            <li><strong class="font-semibold">Owner Name:</strong> {{ $requestDemo->owner_name }}</li>
            <li><strong class="font-semibold">Phone Number:</strong> {{ $requestDemo->number }}</li>
            <li><strong class="font-semibold">Email:</strong> {{ $requestDemo->email }}</li>
            <li><strong class="font-semibold">Company Address:</strong> {{ $requestDemo->company_address }}</li>
            <li><strong class="font-semibold">Employee Size:</strong> {{ $requestDemo->emp_size }}</li>
            <li><strong class="font-semibold">Designation:</strong> {{ $requestDemo->designation }}</li>
        </ul>
        <p class="mt-8 text-lg md:text-xl font-medium text-center text-gray-800">Please follow up with this lead as soon
            as possible.</p>
    </main>

    <!-- Footer -->
    <footer class="bg-red-600 text-white py-8 mt-12">
        <div class="container mx-auto text-center space-y-6">
            <p class="text-sm md:text-base">&copy; 2024 Real Victory Groups. All rights reserved.</p>
            <ul class="contact-details text-sm md:text-base space-y-4">
                <li><strong class="font-semibold">Contact Number:</strong> +917753800444</li>
                <li><strong class="font-semibold">Email:</strong> realvictorygroups@gmail.com</li>
                <li><strong class="font-semibold">Address:</strong> 73 Basement, Ekta Enclave Society, Lakhanpur,
                    Khyora, Kanpur, Uttar Pradesh 208024</li>
            </ul>
            <!-- Social Media Icons -->
            <div>
                <h2 class="text-xl md:text-2xl font-semibold text-white">Follow us on:</h2>
                <div class="flex justify-center space-x-8 mt-4">
                    <a href="https://www.instagram.com/realvictorygroups/" target="_blank"
                        class="text-white text-3xl hover:text-gray-300 transform hover:scale-110 transition-all duration-300">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://wa.me/+917753800444" target="_blank"
                        class="text-white text-3xl hover:text-gray-300 transform hover:scale-110 transition-all duration-300">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>

            </div>
        </div>
    </footer>
</body>

</html>
