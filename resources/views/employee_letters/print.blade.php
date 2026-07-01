<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $employeeLetter->letter_no }}</title>

    <style>
        @page {
            size: A4;
            margin: 14mm 16mm 16mm 16mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            margin: 0;
            background: #f3f4f6;
        }

        .toolbar {
            padding: 12px;
            text-align: center;
            background: #111827;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .toolbar button,
        .toolbar a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #16a34a;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            margin: 0 4px;
        }

        .toolbar a {
            background: #374151;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 16px auto;
            background: white;
            padding: 18mm 16mm 18mm 16mm;
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
        }

        /* Header */
        .letter-header {
            text-align: center;
            margin-bottom: 22px;
        }

        .letter-logo-box {
            text-align: center;
            margin-top: 4px;
        }

        .letter-logo {
            max-width: 180mm;
            width: 95%;
            height: auto;
            display: inline-block;
        }

        .letter-office-address {
            margin-top: 22px;
            font-size: 17px;
            font-weight: 700;
            color: #666666;
            letter-spacing: 0.2px;
        }

        .letter-header-line {
            width: 82%;
            height: 1px;
            background: #222222;
            margin: 24px auto 0 auto;
        }

        .meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 14px;
        }

        .subject {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }

        .content {
            font-size: 14px;
            line-height: 1.7;
        }

        .content p {
            margin: 8px 0;
        }

        .content h1,
        .content h2,
        .content h3 {
            color: #172554;
            line-height: 1.35;
            margin-top: 20px;
            margin-bottom: 8px;
        }

        .content h1 {
            font-size: 24px;
        }

        .content h2 {
            font-size: 20px;
        }

        .content h3 {
            font-size: 17px;
        }

        .content ul,
        .content ol {
            margin-top: 8px;
            margin-bottom: 8px;
            padding-left: 24px;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
        }

        .content table td,
        .content table th {
            vertical-align: top;
        }

        .content img {
            max-width: 100%;
            height: auto;
        }

        @media print {
            body {
                background: white;
            }

            .toolbar {
                display: none;
            }

            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .letter-logo {
                max-width: 180mm;
            }

            .letter-office-address {
                color: #555555;
            }
        }
    </style>
</head>

<body>

<div class="toolbar">
    <a href="{{ route('employee-letters.show', $employeeLetter->id) }}">
        Back
    </a>

    <button onclick="window.print()">
        Print / Save PDF
    </button>
</div>

<div class="page">

    {{-- Dynamic Active Office Header --}}
    @include('employee_letters.partials.letter_header', ['employeeLetter' => $employeeLetter])

    <div class="meta">
        <div>
            Letter No: {{ $employeeLetter->letter_no }}
        </div>

        <div>
            Issue Date: {{ optional($employeeLetter->issue_date)->format('d-m-Y') }}
        </div>
    </div>

    @if($employeeLetter->rendered_subject)
        <div class="subject">
            {{ $employeeLetter->rendered_subject }}
        </div>
    @endif

    <div class="content">
        {!! $employeeLetter->rendered_html !!}
    </div>
</div>

<script>
    window.addEventListener('load', function () {
        setTimeout(function () {
            window.print();
        }, 300);
    });
</script>

</body>
</html>