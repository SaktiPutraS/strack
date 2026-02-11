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
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 3px solid #0D9488;
            padding-bottom: 20px;
        }

        .company-info {
            flex: 1;
        }

        .company-logo {
            max-width: 187px;
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
            color: #0D9488;
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
            color: #0D9488;
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
            margin-bottom: 20px;
            font-size: 14px;
        }

        .items-table th {
            background-color: #0D9488;
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

        .description-text {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .total-section {
            float: right;
            width: 350px;
            margin-bottom: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        .total-row.final {
            border-top: 2px solid #0D9488;
            border-bottom: 2px solid #0D9488;
            font-weight: bold;
            font-size: 18px;
            margin-top: 10px;
            padding: 12px 0;
        }

        .terbilang {
            clear: both;
            background-color: #f0fdfa;
            padding: 12px;
            border-left: 4px solid #0D9488;
            margin-bottom: 20px;
            font-style: italic;
            font-size: 14px;
        }

        .terms {
            margin-bottom: 20px;
            font-size: 14px;
        }

        .terms h4 {
            color: #0D9488;
            margin-bottom: 8px;
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
            margin-top: 25px;
            margin-bottom: 20px;
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
            max-width: 97px;
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
            opacity: 0.4;
        }

        .stamp-image {
            max-width: 60px;
            height: auto;
            filter: drop-shadow(1px 1px 2px rgba(0, 0, 0, 0.2));
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
            background-color: #f0fdfa;
            padding: 12px 15px;
            border-radius: 6px;
            border-left: 4px solid #0D9488;
            font-size: 13px;
            margin-top: 20px;
            line-height: 1.5;
        }

        .validity-info h4 {
            color: #0D9488;
            margin-bottom: 6px;
            font-size: 15px;
        }

        .validity-details {
            line-height: 1.5;
        }

        .validity-details strong {
            color: #0D9488;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .quotation-container {
                margin: 0;
                padding: 0;
                width: 210mm;
                height: 297mm;
            }

            @page {
                size: A4;
                margin: 15mm;
            }

            @page {
                margin-top: 0;
                margin-bottom: 0;
            }

            body::before,
            body::after {
                display: none;
            }

            header,
            footer {
                display: none !important;
            }
        }

        .currency {
            font-weight: bold;
        }

        .teal {
            color: #0D9488;
        }

        .teal-bg {
            background-color: #0D9488;
        }

        .teal-light-bg {
            background-color: #f0fdfa;
        }

        .teal-border {
            border-color: #0D9488;
        }
    </style>
</head>

<body>
    <div class="quotation-container">
        <div class="header">
            <div class="company-info">
                <img src="{{ asset('image/Saktify.webp') }}" alt="Saktify Logo" class="company-logo">
                <div class="company-details">
                    Tangerang, Indonesia<br>
                    Email: admin@saktify.com<br>
                    Website: saktify.my.id<br>
                    WhatsApp: 0813-6422-2434
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
                        <div class="description-text">{{ $itemData['description'] }}</div>
                    </td>
                    <td class="text-center">{{ $itemData['qty'] }}</td>
                    <td class="text-right currency">Rp {{ number_format($itemData['unit_price'], 0, ',', '.') }}</td>
                    <td class="text-right currency">Rp {{ number_format($itemData['total'], 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

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

        <div class="terbilang">
            <strong>Terbilang:</strong> {{ ucfirst($terbilang) }}
        </div>

        @if ($project->notes)
            <div class="terms">
                <h4>Catatan Khusus:</h4>
                <p>{{ $project->notes }}</p>
            </div>
        @endif

        <div class="signature-section">
            <div class="signature-box">
                <p style="margin-bottom: 0px">Tangerang, {{ now()->format('d F Y') }}</p>

                <div style="position: relative; height: 80px; margin: 10px 0;">
                    <img src="{{ asset('image/Btools_ttd.png') }}" alt="Signature" class="signature-image">

                    <div class="stamp-overlay">
                        <img src="{{ asset('image/Saktify_Stempel.webp') }}" alt="Company Stamp" class="stamp-image">
                    </div>
                </div>

                <div class="signature-line">
                    Saktify
                </div>
            </div>
        </div>

        <div class="validity-info">
            <h4>Informasi Penting</h4>
            <div class="validity-details">
                Quotation ini berlaku hingga <strong>{{ $project->created_at->addDays(30)->format('d F Y') }}</strong><br><br>
                <strong>Informasi Pembayaran:</strong><br>
                🟦 BCA : 6042125799<br>
                🟧 Seabank : 901551898940<br>
                🟥 CIMB : 760967985900<br>
                a.n Sakti Putra S
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
