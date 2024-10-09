<!DOCTYPE html>
<html>
<head>
    <title>New Leave Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #dddddd;
        }
        .email-header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 20px 0;
        }
        .email-body h1 {
            color: #333333;
        }
        .email-body p {
            color: #555555;
            line-height: 1.6;
        }
        .email-footer {
            text-align: center;
            margin-top: 20px;
        }
        .email-footer a {
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
        }
        .email-footer a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <img src="{{ asset('images/logo.png') }}" alt="Company Logo">
        <h2>Your Company Name</h2>
    </div>
    <div class="email-body">
        <h1>New Leave Request Submitted</h1>
        <p><strong>{{ $leaveRequest->user->name }}</strong> has requested leave.</p>
        <p><strong>Leave Type:</strong> {{ $leaveRequest->leave_type }}</p>
        <p><strong>Leave Start Date:</strong> {{ $leaveRequest->start_date }}</p>
        <p><strong>Leave End Date:</strong> {{ $leaveRequest->end_date }}</p>
        <p><strong>Cause:</strong> {{ $leaveRequest->reason }}</p>
    </div>
    <div class="email-footer">
        <a href="{{ url('leave/show/'.$leaveRequest->id) }}">View Leave Request</a>
    </div>
</div>
</body>
</html>

