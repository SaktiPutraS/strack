<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoiceNumber }} - {{ $project->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            /* Diperbesar dari 12px */
            line-height: 1.6;
            /* Diperbesar dari 1.4 */
            color: #333;
            background: white;
        }

        .invoice-container {
            max-width: 210mm;
            /* A4 width */
            min-height: 297mm;
            /* A4 height */
            margin: 0 auto;
            padding: 10mm;
            /* Padding lebih besar untuk A4 */
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            /* Diperbesar dari 18px */
            border-bottom: 3px solid #0ea5e9;
            padding-bottom: 20px;
            /* Diperbesar dari 12px */
        }

        .company-info {
            flex: 1;
        }

        .company-logo {
            max-width: 250px;
            /* Diperbesar dari 180px */
            height: auto;
            margin-bottom: 10px;
            /* Diperbesar dari 6px */
        }

        .company-details {
            font-size: 12px;
            /* Diperbesar dari 9px */
            color: #64748b;
            line-height: 1.5;
            /* Diperbesar dari 1.3 */
        }

        .invoice-title {
            text-align: right;
            flex: 1;
        }

        .invoice-title h1 {
            font-size: 32px;
            /* Diperbesar dari 22px */
            color: #0ea5e9;
            font-weight: bold;
            margin-bottom: 8px;
            /* Diperbesar dari 4px */
        }

        .invoice-details {
            font-size: 14px;
            /* Diperbesar dari 10px */
            color: #64748b;
        }

        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            /* Diperbesar dari 18px */
        }

        .bill-to,
        .invoice-info {
            flex: 1;
            margin-right: 20px;
            /* Diperbesar dari 15px */
        }

        .bill-to h3,
        .invoice-info h3 {
            font-size: 16px;
            /* Diperbesar dari 11px */
            color: #0ea5e9;
            margin-bottom: 10px;
            /* Diperbesar dari 6px */
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            /* Diperbesar dari 3px */
        }

        .customer-info,
        .invoice-data {
            font-size: 14px;
            /* Diperbesar dari 10px */
            line-height: 1.6;
            /* Diperbesar dari 1.4 */
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            /* Diperbesar dari 18px */
            font-size: 14px;
            /* Diperbesar dari 10px */
        }

        .items-table th {
            background-color: #0ea5e9;
            color: white;
            padding: 12px 8px;
            /* Diperbesar dari 6px */
            text-align: left;
            font-weight: bold;
            font-size: 14px;
            /* Diperbesar dari 10px */
        }

        .items-table td {
            padding: 15px 8px;
            /* Diperbesar dari 8px 6px */
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            float: right;
            width: 350px;
            /* Diperbesar dari 250px */
            margin-bottom: 20px;
            /* Diperbesar dari 18px */
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            /* Diperbesar dari 6px 0 */
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            /* Diperbesar dari 10px */
        }

        .total-row.final {
            border-top: 2px solid #0ea5e9;
            border-bottom: 2px solid #0ea5e9;
            font-weight: bold;
            font-size: 18px;
            /* Diperbesar dari 12px */
            margin-top: 15px;
            /* Diperbesar dari 8px */
            padding: 15px 0;
            /* Diperbesar dari 8px 0 */
        }

        .terbilang {
            clear: both;
            background-color: #f0f9ff;
            padding: 15px;
            /* Diperbesar dari 8px */
            border-left: 4px solid #0ea5e9;
            margin-bottom: 30px;
            /* Diperbesar dari 18px */
            font-style: italic;
            font-size: 14px;
            /* Diperbesar dari 10px */
        }

        .notes {
            margin-bottom: 30px;
            /* Diperbesar dari 18px */
            font-size: 14px;
            /* Diperbesar dari 10px */
        }

        .notes h4 {
            color: #0ea5e9;
            margin-bottom: 10px;
            /* Diperbesar dari 6px */
            font-size: 16px;
            /* Diperbesar dari 11px */
        }

        .signature-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 40px;
            /* Diperbesar dari 10px */
            margin-bottom: 30px;
            /* Diperbesar dari 10px */
        }

        .signature-box {
            text-align: center;
            width: 200px;
            /* Diperbesar dari 120px */
            position: relative;
        }

        .signature-box p {
            margin-bottom: 50px;
            /* Diperbesar dari 35px */
            font-size: 14px;
            /* Diperbesar dari 10px */
        }

        .signature-image {
            max-width: 130px;
            /* Diperbesar dari 100px */
            height: auto;
            margin: 10px 0;
            /* Diperbesar dari 8px 0 */
            position: relative;
            z-index: 1;
        }

        /* Styling untuk stempel */
        .stamp-overlay {
            position: absolute;
            top: 20px;
            /* Disesuaikan dari 13px */
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            opacity: 0.8;
        }

        .stamp-image {
            max-width: 100px;
            /* Diperbesar dari 80px */
            height: auto;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.3));
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 15px;
            /* Diperbesar dari 8px */
            padding-top: 8px;
            /* Diperbesar dari 4px */
            font-size: 14px;
            /* Diperbesar dari 10px */
            font-weight: bold;
            position: relative;
            z-index: 3;
        }

        .payment-info {
            background-color: #f0f9ff;
            padding: 20px;
            /* Diperbesar dari 12px */
            border-radius: 6px;
            /* Diperbesar dari 4px */
            border-left: 4px solid #0369a1;
            font-size: 14px;
            /* Diperbesar dari 10px */
            margin-top: 25px;
            /* Diperbesar dari 15px */
        }

        .payment-info h4 {
            color: #0369a1;
            margin-bottom: 10px;
            /* Diperbesar dari 6px */
            font-size: 16px;
            /* Diperbesar dari 11px */
        }

        .payment-details {
            line-height: 1.6;
            /* Diperbesar dari 1.5 */
        }

        .payment-details strong {
            color: #0369a1;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                width: 210mm;
                /* A4 width */
                height: 297mm;
                /* A4 height */
            }

            @page {
                size: A4;
                /* Ukuran A4 penuh */
                margin: 15mm;
                /* Margin print */
            }
        }

        .currency {
            font-weight: bold;
        }

        /* Ocean blue theme colors */
        .ocean-blue {
            color: #0ea5e9;
        }

        .ocean-blue-bg {
            background-color: #0ea5e9;
        }

        .ocean-blue-light-bg {
            background-color: #f0f9ff;
        }

        .ocean-blue-border {
            border-color: #0ea5e9;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <img src="{{ asset('image/Btools.png') }}" alt="Btools Logo" class="company-logo">
                <div class="company-details">
                    Jakarta, Indonesia<br>
                    Email: admin@btools.id<br>
                    Website: www.btools.id<br>
                    WhatsApp: 0857-1008-9494
                </div>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <div class="invoice-details">
                    <strong>{{ $invoiceNumber }}</strong><br>
                    {{ $project->deadline->format('d F Y') }}
                </div>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div class="bill-to">
                <h3>Bill To:</h3>
                <div class="customer-info">
                    <strong>{{ $project->client->name }}</strong><br>
                    @if ($project->client->address)
                        {{ $project->client->address }}<br>
                    @endif
                    {{ $project->client->phone }}<br>
                    @if ($project->client->email)
                        {{ $project->client->email }}
                    @endif
                </div>
            </div>
            <div class="invoice-info">
                <h3>Invoice Details:</h3>
                <div class="invoice-data">
                    <strong>Invoice Number:</strong> {{ $invoiceNumber }}<br>
                    <strong>Invoice Date:</strong> {{ $project->deadline->format('d F Y') }}<br>
                    <strong>Due Date:</strong> {{ $project->deadline->format('d F Y') }}
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 20%;">Unit Price</th>
                    <th style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        <strong>{{ $project->title }}</strong><br>
                        <span style="color: #64748b; font-size: 12px;">
                            {{ $project->description ?: 'Layanan pengembangan dan implementasi proyek sesuai spesifikasi yang telah disepakati.' }}
                        </span>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right currency">Rp {{ number_format($project->total_value, 0, ',', '.') }}</td>
                    <td class="text-right currency">Rp {{ number_format($project->total_value, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span class="currency">Rp {{ number_format($project->total_value, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Tax (0%):</span>
                <span class="currency">Rp 0</span>
            </div>
            <div class="total-row final">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($project->total_value, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Terbilang -->
        <div class="terbilang">
            <strong>Terbilang:</strong> {{ ucfirst($terbilang) }}
        </div>

        <!-- Notes -->
        @if ($project->notes)
            <div class="notes">
                <h4>Catatan Khusus:</h4>
                <p>{{ $project->notes }}</p>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <p style="margin-bottom: 0px">Jakarta, {{ now()->format('d F Y') }}</p>

                <!-- Container untuk tanda tangan dan stempel -->
                <div style="position: relative; height: 80px; margin: 10px 0;">
                    <!-- Tanda tangan sebagai background -->
                    <img src="{{ asset('image/Btools_ttd.png') }}" alt="Signature" class="signature-image">

                    <!-- Stempel yang menimpa tanda tangan -->
                    <div class="stamp-overlay">
                        <img src="{{ asset('image/Btools_stempel.png') }}" alt="Company Stamp" class="stamp-image">
                    </div>
                </div>

                <div class="signature-line">
                    Btools
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="payment-info">
            <h4>Informasi Pembayaran</h4>
            <div class="payment-details">
                Pembayaran untuk invoice ini mohon di transfer ke:<br>
                <strong>Bank BCA a.n Niki Dwi Maharani</strong><br>
                <strong>No Rekening: 127-007-3758</strong><br><br>
                Mohon konfirmasi pembayaran ke WhatsApp: 0857-1008-9494
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
