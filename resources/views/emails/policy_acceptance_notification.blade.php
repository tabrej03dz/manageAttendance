<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Policy Accepted</title>
</head>
<body>
    <h2>Policy Accepted Successfully</h2>

    <p>Hello Admin,</p>

    <p>
        User <strong>{{ $user->name ?? 'N/A' }}</strong>
        has accepted the policy.
    </p>

    <p>
        <strong>Email:</strong> {{ $user->email1 ?? $user->email ?? 'N/A' }}
    </p>

    <p>
        <strong>Policy:</strong> {{ $policy->title ?? $policy->name ?? 'Policy' }}
    </p>

    <p>
        <strong>Date:</strong> {{ now()->format('d-m-Y h:i A') }}
    </p>
</body>
</html>