<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white shadow-lg rounded-xl p-8 max-w-lg w-full space-y-6">
            <!-- Title -->
            <h1 class="text-3xl font-extrabold text-gray-800 text-center">Thank You!</h1>
            
            <!-- Message -->
            <p class="text-lg text-gray-600 text-center">Your demo request has been submitted successfully! We will contact you shortly.</p>

            <!-- Button -->
            <div class="flex justify-center">
                <a href="{{ route('mainpage') }}"
                    class="bg-red-500 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-red-600 transition duration-300 transform hover:scale-105">
                    Back to Home
                </a>
            </div>

            <!-- Footer Text -->
            <p class="text-sm text-gray-500 text-center">
                &copy; 2024 Real Victory Groups. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
