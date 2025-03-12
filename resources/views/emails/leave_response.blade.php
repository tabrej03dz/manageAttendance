<!DOCTYPE html>
<html>
<head>
    <title>Response By {{ $leaveResponse->responsesBy->name }}</title>
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
        .status-approved {
            color: #27ae60;
        }
        .status-denied {
            color: #e74c3c;
        }
        .email-footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h2>Real Victory Groups</h2>
    </div>
    <div class="email-body">
        <h1>Your Leave Request Status</h1>
        <p>Dear {{ $leaveResponse->user->name }},</p>
        <p>Your leave request from <strong>{{ $leaveResponse->start_date }}</strong> to <strong>{{ $leaveResponse->end_date }}</strong> has been
            <strong class="{{ $leaveResponse->status === 'approved' ? 'status-approved' : 'status-denied' }}">{{ $leaveResponse->status }}</strong>.</p>

        @if($leaveResponse->status === 'approved')
            <p>Enjoy your time off!</p>
        @else
            <p>Unfortunately, your leave request has been denied.</p>
        @endif

        <p>{{$leaveResponse->response}}</p>
    </div>
    <div class="email-footer">
        <p>If you have any questions, please contact HR.</p>
    </div>
</div>
</body>
</html>
