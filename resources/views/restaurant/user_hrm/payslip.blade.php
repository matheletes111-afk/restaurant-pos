<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $user->name }} - {{ $monthName }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            padding: 30px 15px;
        }
        .payslip-card {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .payslip-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 30px;
        }
        .payslip-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .payslip-period {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
        }
        .table-custom th {
            background: #f8fafc;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 14px;
        }
        .table-custom td {
            font-size: 13px;
            padding: 10px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        .net-pay-banner {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 18px 24px;
        }
        .status-badge-paid {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
            font-weight: 700;
            font-size: 11px;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
        }
        .status-badge-pending {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
            font-weight: 700;
            font-size: 11px;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .payslip-card {
                border: none;
                box-shadow: none;
                max-width: 100%;
                border-radius: 0;
            }
            .d-print-none {
                display: none !important;
            }
            .payslip-header {
                background: #0f172a !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .net-pay-banner {
                background: #f0fdf4 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Print / Back Toolbar -->
    <div class="d-print-none text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary px-4 py-2 me-2" style="border-radius: 8px; font-weight: 600;">
            <i class="fas fa-print me-2"></i> Print / Save as PDF
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 8px; font-weight: 600;">
            <i class="fas fa-times me-2"></i> Close
        </button>
    </div>

    <div class="payslip-card">
        <!-- Header -->
        <div class="payslip-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h3 class="fw-bold mb-1" style="font-size: 24px; letter-spacing: -0.5px;">
                        {{ $restaurant ? $restaurant->name : 'Restaurant POS' }}
                    </h3>
                    <div style="font-size: 12px; color: #cbd5e1; max-width: 420px; line-height: 1.5;">
                        @if($restaurant && $restaurant->address)
                            {{ $restaurant->address }}<br>
                        @endif
                        @if($restaurant && $restaurant->phone)
                            <span>Phone: {{ $restaurant->phone }}</span> &bull;
                        @endif
                        @if($restaurant && $restaurant->email)
                            <span>Email: {{ $restaurant->email }}</span>
                        @endif
                        @if($restaurant && !empty($restaurant->gst_number))
                            <br><span>GSTIN: {{ $restaurant->gst_number }}</span>
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    <div class="payslip-title">PAYSLIP</div>
                    <div class="payslip-period">{{ $monthName }}</div>
                    <div class="mt-2">
                        @if($payroll->is_paid)
                            <span class="status-badge-paid"><i class="fas fa-check-circle me-1"></i> PAID</span>
                        @else
                            <span class="status-badge-pending"><i class="fas fa-clock me-1"></i> UNPAID / PENDING</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Info & Attendance Grid -->
        <div class="p-4 border-bottom bg-light">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="info-label">Employee Name</div>
                    <div class="info-value">{{ $user->name }}</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-label">Employee ID</div>
                    <div class="info-value">#EMP-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-label">Designation / Role</div>
                    <div class="info-value text-capitalize">{{ $user->role_type ?: ($user->role ?: 'Staff') }}</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-label">Contact Phone</div>
                    <div class="info-value">{{ $user->phone ?: '—' }}</div>
                </div>

                <div class="col-12 mt-2">
                    <div class="p-2 px-3 rounded" style="background: #ffffff; border: 1px solid #e2e8f0;">
                        <div class="row text-center">
                            <div class="col-3 border-end">
                                <div class="info-label">Total Days</div>
                                <div class="fw-bold fs-6 text-dark">{{ $payroll->total_days }}</div>
                            </div>
                            <div class="col-3 border-end">
                                <div class="info-label">Present Days</div>
                                <div class="fw-bold fs-6 text-success">{{ (float)$payroll->present_days }}</div>
                            </div>
                            <div class="col-3 border-end">
                                <div class="info-label">Paid Leaves</div>
                                <div class="fw-bold fs-6 text-primary">{{ (float)$payroll->leave_days }}</div>
                            </div>
                            <div class="col-3">
                                <div class="info-label">Absent Days</div>
                                <div class="fw-bold fs-6 text-danger">{{ (float)$payroll->absent_days }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings & Deductions Tables -->
        <div class="p-4">
            <div class="row g-4">
                <!-- Earnings Column -->
                <div class="col-md-6">
                    <div class="border rounded overflow-hidden h-100">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Earnings</th>
                                    <th class="text-end">Amount (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">Base / Monthly Salary</div>
                                        <small class="text-muted">Full monthly fixed rate</small>
                                    </td>
                                    <td class="text-end fw-semibold">₹{{ number_format($payroll->base_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-success">Earned Base Salary</div>
                                        <small class="text-muted">For {{ (float)($payroll->present_days + $payroll->leave_days) }} eligible days</small>
                                    </td>
                                    <td class="text-end fw-semibold text-success">₹{{ number_format($payroll->earned_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">Extra Earnings / Incentives</div>
                                        <small class="text-muted">Overtime, tips, bonuses</small>
                                    </td>
                                    <td class="text-end fw-semibold">₹{{ number_format($payroll->extra_earnings, 2) }}</td>
                                </tr>
                                <tr style="background: #f8fafc; font-weight: 700;">
                                    <td>Gross Earnings (Earned + Extras)</td>
                                    <td class="text-end text-dark">₹{{ number_format($payroll->earned_salary + $payroll->extra_earnings, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Deductions Column -->
                <div class="col-md-6">
                    <div class="border rounded overflow-hidden h-100">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Deductions</th>
                                    <th class="text-end">Amount (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">Absent Deductions</div>
                                        <small class="text-muted">For {{ (float)$payroll->absent_days }} absent days</small>
                                    </td>
                                    <td class="text-end fw-semibold text-danger">₹{{ number_format($payroll->absent_deduction, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">Provident Fund (PF)</div>
                                        <small class="text-muted">Employee contribution</small>
                                    </td>
                                    <td class="text-end fw-semibold">₹{{ number_format($payroll->pf_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">ESIC Contribution</div>
                                        <small class="text-muted">Health insurance</small>
                                    </td>
                                    <td class="text-end fw-semibold">₹{{ number_format($payroll->esi_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">P.Tax & Other Deductions</div>
                                        <small class="text-muted">Statutory & professional tax</small>
                                    </td>
                                    <td class="text-end fw-semibold">₹{{ number_format($payroll->other_deductions, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">Salary Advance Repayment</div>
                                        <small class="text-muted">Monthly recovery</small>
                                    </td>
                                    <td class="text-end fw-semibold text-danger">₹{{ number_format($payroll->advance_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">Penalties / Extra Deductions</div>
                                        <small class="text-muted">Penalties, damages or fines</small>
                                    </td>
                                    <td class="text-end fw-semibold text-danger">₹{{ number_format($payroll->extra_deductions, 2) }}</td>
                                </tr>
                                @php
                                    $totalDeductions = $payroll->pf_amount + $payroll->esi_amount + $payroll->other_deductions + $payroll->advance_amount + $payroll->extra_deductions;
                                @endphp
                                <tr style="background: #f8fafc; font-weight: 700;">
                                    <td>Total Recoveries & Deductions</td>
                                    <td class="text-end text-danger">₹{{ number_format($totalDeductions, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Net Payable Banner -->
            <div class="net-pay-banner mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #166534;">
                            Net Disbursed / Actual Payable
                        </div>
                        <div class="small text-muted mt-1">
                            Calculated Final: ₹{{ number_format($payroll->final_salary, 2) }}
                            @if($payroll->paid_date)
                                &bull; Disbursed On: {{ \Carbon\Carbon::parse($payroll->paid_date)->format('d M, Y') }}
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold" style="font-size: 28px; color: #15803d; letter-spacing: -0.5px;">
                            ₹{{ number_format($payroll->actual_payable, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            @if($payroll->notes)
                <div class="mt-3 p-3 rounded" style="background: #f8fafc; border: 1px dashed #cbd5e1; font-size: 12px; color: #475569;">
                    <strong>Remarks / Notes:</strong> {{ $payroll->notes }}
                </div>
            @endif

            <!-- Signatures Section -->
            <div class="row mt-5 pt-4 text-center">
                <div class="col-4">
                    <div style="border-top: 1px solid #94a3b8; width: 80%; margin: 0 auto; padding-top: 8px;">
                        <span class="info-label">Employee Signature</span>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px solid #94a3b8; width: 80%; margin: 0 auto; padding-top: 8px;">
                        <span class="info-label">Prepared By</span>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px solid #94a3b8; width: 80%; margin: 0 auto; padding-top: 8px;">
                        <span class="info-label">Authorized Signatory</span>
                    </div>
                </div>
            </div>

            <div class="text-center text-muted small mt-4" style="font-size: 10px;">
                This is a computer-generated document from {{ $restaurant ? $restaurant->name : 'Restaurant POS' }} and does not require a physical seal.
            </div>
        </div>
    </div>

</body>
</html>
