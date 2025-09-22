<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation {{ $quotationNumber }} - {{ $project->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: white;
        }

        .quotation-container {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 10mm;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 3px solid #0ea5e9;
            padding-bottom: 20px;
        }

        .company-info {
            flex: 1;
        }

        .company-logo {
            max-width: 250px;
            height: auto;
            margin-bottom: 10px;
        }

        .company-details {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
        }

        .quotation-title {
            text-align: right;
            flex: 1;
        }

        .quotation-title h1 {
            font-size: 32px;
            color: #0ea5e9;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .quotation-details {
            font-size: 14px;
            color: #64748b;
        }

        .client-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .client-to,
        .quotation-info {
            flex: 1;
            margin-right: 20px;
        }

        .client-to h3,
        .quotation-info h3 {
            font-size: 16px;
            color: #0ea5e9;
            margin-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }

        .customer-info,
        .quotation-data {
            font-size: 14px;
            line-height: 1.6;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .items-table th {
            background-color: #0ea5e9;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
        }

        .items-table td {
            padding: 15px 8px;
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
            margin-bottom: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        .total-row.final {
            border-top: 2px solid #0ea5e9;
            border-bottom: 2px solid #0ea5e9;
            font-weight: bold;
            font-size: 18px;
            margin-top: 15px;
            padding: 15px 0;
        }

        .terbilang {
            clear: both;
            background-color: #f0f9ff;
            padding: 15px;
            border-left: 4px solid #0ea5e9;
            margin-bottom: 30px;
            font-style: italic;
            font-size: 14px;
        }

        .terms {
            margin-bottom: 30px;
            font-size: 14px;
        }

        .terms h4 {
            color: #0ea5e9;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .terms ul {
            list-style-type: disc;
            margin-left: 20px;
            line-height: 1.8;
        }

        .signature-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 40px;
            margin-bottom: 30px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
            position: relative;
        }

        .signature-box p {
            margin-bottom: 50px;
            font-size: 14px;
        }

        .signature-image {
            max-width: 130px;
            height: auto;
            margin: 10px 0;
            position: relative;
            z-index: 1;
        }

        .stamp-overlay {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            opacity: 0.8;
        }

        .stamp-image {
            max-width: 100px;
            height: auto;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.3));
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 15px;
            padding-top: 8px;
            font-size: 14px;
            font-weight: bold;
            position: relative;
            z-index: 3;
        }

        .validity-info {
            background-color: #f0f9ff;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #0369a1;
            font-size: 14px;
            margin-top: 25px;
        }

        .validity-info h4 {
            color: #0369a1;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .validity-details {
            line-height: 1.6;
        }

        .validity-details strong {
            color: #0369a1;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .quotation-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                width: 210mm;
                height: 297mm;
            }

            @page {
                size: A4;
                margin: 15mm;
            }
        }

        .currency {
            font-weight: bold;
        }

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
    <div class="quotation-container">
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
            <div class="quotation-title">
                <h1>QUOTATION</h1>
                <div class="quotation-details">
                    <strong>{{ $quotationNumber }}</strong><br>
                    {{ $project->created_at->format('d F Y') }}
                </div>
            </div>
        </div>

        <!-- Client Information -->
        <div class="client-section">
            <div class="client-to">
                <h3>To:</h3>
                <div class="customer-info">
                    <strong>{{ $clientData['name'] }}</strong><br>
                    @if (!empty($clientData['address']))
                        {{ $clientData['address'] }}<br>
                    @endif
                    {{ $clientData['phone'] }}<br>
                    @if (!empty($clientData['email']))
                        {{ $clientData['email'] }}
                    @endif
                </div>
            </div>
            <div class="quotation-info">
                <h3>Quotation Details:</h3>
                <div class="quotation-data">
                    <strong>Quotation Number:</strong> {{ $quotationNumber }}<br>
                    <strong>Date:</strong> {{ $project->created_at->format('d F Y') }}<br>
                    <strong>Valid Until:</strong> {{ $project->created_at->addDays(30)->format('d F Y') }}
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
                        @if ($project->description)
                            <span style="color: #64748b; font-size: 12px;">
                                {{ $project->description }}
                            </span>
                        @endif
                    </td>
                    <td class="text-center">{{ $itemData['qty'] }}</td>
                    <td class="text-right currency">Rp {{ number_format($itemData['unit_price'], 0, ',', '.') }}</td>
                    <td class="text-right currency">Rp {{ number_format($itemData['total'], 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span class="currency">Rp {{ number_format($itemData['total'], 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Tax (0%):</span>
                <span class="currency">Rp 0</span>
            </div>
            <div class="total-row final">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($itemData['total'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Terbilang -->
        <div class="terbilang">
            <strong>Terbilang:</strong> {{ ucfirst($terbilang) }}
        </div>

        @if ($project->notes)
            <!-- Notes -->
            <div class="terms">
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

        <!-- Validity Information -->
        <div class="validity-info">
            <h4>Informasi Penting</h4>
            <div class="validity-details">
                Quotation ini berlaku hingga <strong>{{ $project->created_at->addDays(30)->format('d F Y') }}</strong><br>
                Untuk konfirmasi dan diskusi lebih lanjut, silakan hubungi:<br>
                <strong>WhatsApp: 0857-1008-9494</strong><br>
                <strong>Email: admin@btools.id</strong>
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
