<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="bg-white shadow-md rounded-lg p-6 max-w-md text-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Thank You!</h1>
            <p class="text-gray-600 mb-6">Request demo submitted successfully!</p>
            <a href="{{ route('mainpage') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                Back to Home
            </a>
        </div>
    </div>
</body>
</html>
