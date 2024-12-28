<!DOCTYPE html>
<html>
<head>
    <title>Request Demo Confirmation</title>
</head>
<body>
    <h1>Hello, {{ $requestDemo->owner_name }}</h1>
    <p>Thank you for your demo request. Here are your details:</p>
    <ul>
        <li>Company Name: {{ $requestDemo->compan_name }}</li>
        <li>Owner Name: {{ $requestDemo->owner_name }}</li>
        <li>Phone Number: {{ $requestDemo->number }}</li>
        <li>Email: {{ $requestDemo->email }}</li>
        <li>Company Address: {{ $requestDemo->company_address }}</li>
        <li>Employee Size: {{ $requestDemo->emp_size }}</li>
        <li>Designation: {{ $requestDemo->designation }}</li>
    </ul>
    <p>We will contact you shortly!</p>
</body>
</html>
