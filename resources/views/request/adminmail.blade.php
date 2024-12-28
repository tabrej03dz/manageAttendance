<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Request Demo Lead</title>
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
            .mobile-full {
                width: 100% !important;
                display: block !important;
            }
            .mobile-text {
                font-size: 16px !important;
            }
            .mobile-small-text {
                font-size: 14px !important;
            }
            h1 {
                font-size: 20px !important;
            }
            .details-table td {
                display: block;
                width: 100%;
                padding: 4px 0;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f9fafb; font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased;">
    <table role="presentation" class="container" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f9fafb;">
        <tr>
            <td align="center" style="padding: 10px;">
                <table role="presentation" class="main-table" width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; width: 600px;">
                    <!-- Header -->
                    <tr>
                        <td align="center" class="header" style="background-color: #e5e7eb; padding: 30px 20px;">
                            <img src="{{ asset('asset/img/logo.png') }}" alt="Real Victory Groups Logo" style="width: 90%; max-width: 300px; height: auto;">
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td class="content" style="padding: 30px 20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <h1 style="margin: 0; font-size: 22px; font-weight: bold; color: #1f2937;">New Demo Request Lead</h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-bottom: 25px;">
                                        <p class="mobile-text" style="margin: 0; font-size: 16px; color: #4b5563;">A new demo request has been submitted. Below are the details:</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table role="presentation" class="details-table" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                            <tr>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0; width: 140px;">
                                                    <strong>Company Name:</strong>
                                                </td>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    {{ $requestDemo->compan_name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    <strong>Owner Name:</strong>
                                                </td>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    {{ $requestDemo->owner_name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    <strong>Phone Number:</strong>
                                                </td>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    {{ $requestDemo->number }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    <strong>Email:</strong>
                                                </td>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    {{ $requestDemo->email }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    <strong>Company Address:</strong>
                                                </td>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    {{ $requestDemo->company_address }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    <strong>Employee Size:</strong>
                                                </td>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    {{ $requestDemo->emp_size }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    <strong>Designation:</strong>
                                                </td>
                                                <td class="mobile-text" valign="top" style="padding: 8px 0;">
                                                    {{ $requestDemo->designation }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 20px;">
                                        <p class="mobile-text" style="margin: 0; font-size: 16px; font-weight: 500; color: #1f2937;">Please follow up with this lead as soon as possible.</p>
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
                                        <p class="mobile-small-text" style="margin: 0; font-size: 14px; color: #ffffff;">&copy; 2024 Real Victory Groups. All rights reserved.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table role="presentation" cellpadding="4" cellspacing="0" border="0">
                                            <tr>
                                                <td align="center" class="mobile-small-text" style="color: #ffffff; font-size: 14px;">
                                                    <strong>Contact:</strong> +917753800444
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" class="mobile-small-text" style="color: #ffffff; font-size: 14px;">
                                                    <strong>Email:</strong> realvictorygroups@gmail.com
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" class="mobile-small-text" style="color: #ffffff; font-size: 14px;">
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