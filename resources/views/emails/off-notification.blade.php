<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Off Notification</title>
</head>
<body>

    <h2>Office Off Notification</h2>

    <p>Hello,</p>

    <p>
        This is to inform you about the following office off/holiday:
    </p>

    @if(isset($off->title))
        <p>
            <strong>Title:</strong>
            {{ $off->title }}
        </p>
    @endif

    @if(isset($off->date))
        <p>
            <strong>Date:</strong>
            {{ $off->date }}
        </p>
    @endif

    @if(isset($off->description))
        <p>
            <strong>Description:</strong>
            {{ $off->description }}
        </p>
    @endif

    <p>Regards,<br>Management</p>

</body>
</html>