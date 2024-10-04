<!DOCTYPE html>
<html>
<head>
    <title>Response By {{$leaveResponse->responsesBy->name}}</title>
</head>
<body>
<h1>Your Leave Request Status</h1>
<p>Dear {{ $leaveResponse->user->name }},</p>
<p>Your leave request for {{ $leaveResponse->start_date }} to {{ $leaveResponse->end_date }} has been <strong>{{$leaveResponse->status }}</strong>.</p>

@if($leaveResponse->status === 'approved')
    <p>Enjoy your time off!</p>
@else
    <p>Unfortunately, your leave request has been denied.</p>
@endif
</body>
</html>
