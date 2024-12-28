<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Demo Confirmation</title>
    <style>
        @media screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                padding: 0 !important;
            }
            .main-table {
                width: 100% !important;
            }
            .content {
                padding: 20px 15px !important;
            }
            .header {
                padding: 20px 15px !important;
            }
            .footer {
                padding: 20px 15px !important;
            }
            .detail-row {
                display: block !important;
                text-align: center !important;
                margin-bottom: 8px !important;
            }
            .detail-label {
                display: inline !important;
                margin-right: 5px !important;
            }
            .detail-value {
                display: inline !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f9fafb; font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased;">
    <table role="presentation" class="container" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f9fafb;">
        <tr>
            <td align="center" style="padding: 10px;">
                <table role="presentation" class="main-table" width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; width: 600px; margin: 0 auto;">
                    <!-- Header -->
                    <tr>
                        <td align="center" class="header" style="background-color: #e5e7eb; padding: 30px 20px;">
                            <img src="{{ asset('asset/img/logo.png') }}" alt="Real Victory Groups Logo" style="width: 90%; max-width: 300px; height: auto;">
                        </td>
                    </tr>

                    <!-- Main Content - Now Centered -->
                    <tr>
                        <td class="content" style="padding: 30px 20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 400px; margin: 0 auto;">
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <h1 style="margin: 0; font-size: 22px; font-weight: bold; color: #1f2937;">Hello, {{ $requestDemo->owner_name }}</h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-bottom: 25px;">
                                        <p style="margin: 0; font-size: 16px; color: #4b5563;">Thank you for your demo request. Below are your details:</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                                            <!-- Details rows -->
                                            <tr class="detail-row">
                                                <td align="center" style="padding: 8px 0; color: #4b5563;">
                                                    <strong>Company Name:</strong> {{ $requestDemo->compan_name }}
                                                </td>
                                            </tr>
                                            <tr class="detail-row">
                                                <td align="center" style="padding: 8px 0; color: #4b5563;">
                                                    <strong>Owner Name:</strong> {{ $requestDemo->owner_name }}
                                                </td>
                                            </tr>
                                            <tr class="detail-row">
                                                <td align="center" style="padding: 8px 0; color: #4b5563;">
                                                    <strong>Phone Number:</strong> {{ $requestDemo->number }}
                                                </td>
                                            </tr>
                                            <tr class="detail-row">
                                                <td align="center" style="padding: 8px 0; color: #4b5563;">
                                                    <strong>Email:</strong> {{ $requestDemo->email }}
                                                </td>
                                            </tr>
                                            <tr class="detail-row">
                                                <td align="center" style="padding: 8px 0; color: #4b5563;">
                                                    <strong>Company Address:</strong> {{ $requestDemo->company_address }}
                                                </td>
                                            </tr>
                                            <tr class="detail-row">
                                                <td align="center" style="padding: 8px 0; color: #4b5563;">
                                                    <strong>Employee Size:</strong> {{ $requestDemo->emp_size }}
                                                </td>
                                            </tr>
                                            <tr class="detail-row">
                                                <td align="center" style="padding: 8px 0; color: #4b5563;">
                                                    <strong>Designation:</strong> {{ $requestDemo->designation }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 20px;">
                                        <p style="margin: 0; font-size: 16px; font-weight: 500; color: #1f2937;">We will contact you shortly!</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer" style="background-color: #dc2626; padding: 30px 20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 15px;">
                                        <p style="margin: 0; font-size: 14px; color: #ffffff;">&copy; 2024 Real Victory Groups. All rights reserved.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table role="presentation" cellpadding="4" cellspacing="0" border="0">
                                            <tr>
                                                <td align="center" style="color: #ffffff; font-size: 14px;">
                                                    <strong>Contact:</strong> +917753800444
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="color: #ffffff; font-size: 14px;">
                                                    <strong>Email:</strong> realvictorygroups@gmail.com
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="color: #ffffff; font-size: 14px;">
                                                    <strong>Address:</strong><br>
                                                    73 Basement, Ekta Enclave Society,<br>
                                                    Lakhanpur, Khyora, Kanpur,<br>
                                                    Uttar Pradesh 208024
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>