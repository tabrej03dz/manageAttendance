@extends('dashboard.layout.root')
@section('content')
    <style>
        .bordered {
            border: 2px solid black;
            padding: 20px;
        }
        .table-bordered td, .table-bordered th {
            border: 2px solid black !important;
        }
        .center-text {
            text-align: center;
        }
    </style>

    <div class="container p-4">
        <button id="downloadPdf" class="btn btn-primary mb-2" onclick="printDivAsPDF()" >Download Salary Slip</button>
        <div id="contentToPrint">
            <div class="row mb-2">
                <table class="table table-bordered text-center">
                    <thead>
                    <tr>
                        <th colspan="2" class="text-center">
                            <h3 class="m-0">{{strtoupper($salary->user->office->name)}}</h3>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="2">
                            {{$salary->user->office->address ?? 'Harsh Nagar, Mahanandpur Near Indira Garden
                            Raebareli Uttar Pradesh - 229001'}}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-center mb-3">
                <h5>MONTHLY SALARY SLIP</h5>
            </div>
            <div class="row mb-3">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <th>Employee ID</th>
                        <td>{{$salary->user->employee_id}}</td>
                        <th>Employee Name</th>
                        <td>{{$salary->user->name}}</td>
                    </tr>
                    <tr>
                        <th>Rank</th>
                        <td>{{$salary->user->designation}}</td>
                        <th>Month</th>
                        <td>{{\Carbon\Carbon::parse($salary->month)->format('M Y')}}</td>
                    </tr>
                    <tr>
                        <th>UAN No.</th>
                        <td>{{$salary->user->uan_number ?? '101776463834' }}</td>
                        <th>Account No.</th>
                        <td>{{$salary->user->account_number ?? '30288793224'}}</td>
                    </tr>
                    <tr>
                        <th>ESIC No.</th>
                        <td>{{$salary->user->account_number ?? '3012950500'}}</td>
                        <th>No of Days</th>
                        <td>{{ \Carbon\Carbon::parse($salary->month)->daysInMonth }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <table class="table table-bordered">
                <thead>
                <tr class="center-text">
                    <th>INCOME</th>
                    <th>AMOUNT</th>
                    <th>DEDUCTION</th>
                    <th>AMOUNT</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Basic Pay</td>
                    <td>{{$userSalary->basic_salary}}</td>
                    <td rowspan="2">Provident Fund 12%</td>
                    <td rowspan="2">{{$salary->provident_fund}}</td>
                </tr>
                <tr>
                    <td>D.A</td>
                    <td>{{$userSalary->dearness_allowance}}</td>
                </tr>
                <tr>
                    <td>Relieving Charge</td>
                    <td>{{$userSalary->relieving_charge}}</td>
                    <td rowspan="2">ESIC 0.75%</td>
                    <td rowspan="2">{{$salary->employee_state_insurance_corporation}}</td>
                </tr>
                <tr>
                    <td>Additional Allowance</td>
                    <td>{{$userSalary->additional_allowance}}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Absence {{$salary->absence}}</td>
                    <td>{{($userSalary->total_salary / 30) * $salary->absence}}</td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td><strong>{{$userSalary->total_salary}}</strong></td>
                    <td><strong>Total</strong></td>
                    <td><strong>{{$salary->provident_fund + $salary->employee_state_insurance_corporation}}</strong></td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Total Salary in Account:</strong></td>
                    <td colspan="2"><strong>{{$salary->total_salary}}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        function printDivAsPDF() {
            const {
                jsPDF
            } = window.jspdf;

            // Get the div element
            var element = document.getElementById('contentToPrint');

            // Use html2canvas to capture the div as an image
            html2canvas(element).then(canvas => {
                var imgData = canvas.toDataURL('image/png');

                var pdf = new jsPDF('p', 'mm', 'a0');
                var pageHeight = pdf.internal.pageSize.getHeight();
                var imgWidth = 841; // A4 width in mm
                var imgHeight = (canvas.height * imgWidth) / canvas.width;

                var heightLeft = imgHeight;
                var position = 0;

                // Add the image to the PDF page by page
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft > 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                // Save the PDF
                pdf.save('{{ $salary->user->name }}.pdf');
            });
        }
    </script>


@endsection
