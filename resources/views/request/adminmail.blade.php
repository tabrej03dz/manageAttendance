<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Request Demo Lead</title>
</head>

<body style="background-color: #f9fafb; color: #1f2937; margin: 0; font-family: Arial, sans-serif;">
    <!-- Header -->
    <header
        style="background-color: #e5e7eb; color: white; padding: 2rem 0; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
        <div style="display: flex; justify-content: center; align-items: center;">
            <img src="{{ asset('asset/img/logo.png') }}" alt="Real Victory Groups Logo"
                style="width: 90%; height: auto;">
        </div>
    </header>

    <!-- Main Content -->
    <main
        style="max-width: 800px; margin: 2.5rem auto; padding: 2rem; background-color: white; border-radius: 1rem; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
        <h1 style="font-size: 2.5rem; font-weight: bold; text-align: center; color: #1f2937; margin-bottom: 1.5rem;">New
            Demo Request Lead</h1>
        <p style="font-size: 1.125rem; text-align: center; color: #4b5563; margin-bottom: 2rem;">A new demo request has
            been submitted. Below are the details:</p>
        <ul style="padding-left: 2rem; font-size: 1.125rem; color: #374151; line-height: 1.8; list-style-type: none;">
            <li><strong style="font-weight: 600;">Company Name:</strong> {{ $requestDemo->compan_name }}</li>
            <li><strong style="font-weight: 600;">Owner Name:</strong> {{ $requestDemo->owner_name }}</li>
            <li><strong style="font-weight: 600;">Phone Number:</strong> {{ $requestDemo->number }}</li>
            <li><strong style="font-weight: 600;">Email:</strong> {{ $requestDemo->email }}</li>
            <li><strong style="font-weight: 600;">Company Address:</strong> {{ $requestDemo->company_address }}</li>
            <li><strong style="font-weight: 600;">Employee Size:</strong> {{ $requestDemo->emp_size }}</li>
            <li><strong style="font-weight: 600;">Designation:</strong> {{ $requestDemo->designation }}</li>
        </ul>
        <p style="margin-top: 2rem; font-size: 1.125rem; font-weight: 500; text-align: center; color: #1f2937;">Please
            follow up with this lead as soon as possible.</p>
    </main>

    <!-- Footer -->
    <footer style="background-color: #dc2626; color: white; padding: 2rem 0; margin-top: 3rem;">
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <p style="font-size: 0.875rem; margin-bottom: 1.5rem;">&copy; 2024 Real Victory Groups. All rights reserved.
            </p>
            <ul style="font-size: 0.875rem; line-height: 1.8; margin-bottom: 1.5rem; list-style-type: none;">
                <li><strong style="font-weight: 600;">Contact Number:</strong> +917753800444</li>
                <li><strong style="font-weight: 600;">Email:</strong> realvictorygroups@gmail.com</li>
                <li><strong style="font-weight: 600;">Address:</strong> 73 Basement, Ekta Enclave Society, Lakhanpur,
                    Khyora, Kanpur, Uttar Pradesh 208024</li>
            </ul>
        </div>
    </footer>
</body>

</html>
