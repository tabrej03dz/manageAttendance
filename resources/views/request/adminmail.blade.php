<!DOCTYPE html>
<html>
<head>
    <title>New Request Demo Lead</title>
</head>
<body>
    <h1>New Request Demo Lead</h1>
    <p>A new demo request has been submitted. Here are the details:</p>
    <ul>
        <li><strong>Company Name:</strong> {{ $requestDemo->compan_name }}</li>
        <li><strong>Owner Name:</strong> {{ $requestDemo->owner_name }}</li>
        <li><strong>Phone Number:</strong> {{ $requestDemo->number }}</li>
        <li><strong>Email:</strong> {{ $requestDemo->email }}</li>
        <li><strong>Company Address:</strong> {{ $requestDemo->company_address }}</li>
        <li><strong>Employee Size:</strong> {{ $requestDemo->emp_size }}</li>
        <li><strong>Designation:</strong> {{ $requestDemo->designation }}</li>
    </ul>
    <p>Please follow up with this lead as soon as possible.</p>
</body>
</html>
