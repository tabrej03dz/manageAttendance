<!DOCTYPE html>
<html>
<head>
    <title>New Leave Request</title>
</head>
<body>
<h1>New Leave Request Submitted</h1>
<p>{{ $leaveRequest->user->name }} has requested leave.</p>
<p>Leave Type: {{ $leaveRequest->leave_type }}</p>
<p>Leave Start Date: {{ $leaveRequest->start_date }}</p>
<p>Leave End Date: {{ $leaveRequest->end_date }}</p>
<p>Cause: {{ $leaveRequest->reason}}</p>
</body>
</html>
