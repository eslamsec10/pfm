<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice - Global Power Services</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        .invoice-box {
            max-width: 850px;
            margin: auto;
            padding: 30px;
            border: 1px solid #000;
            background-color: #fff;
            color: #333;
        }
        /* الهيدر */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .header-logo img {
            max-width: 180px;
        }
        .header-qr img {
            width: 120px;
            border: 1px solid #ccc;
        }
        .header-info {
            text-align: right;
            font-size: 11px;
            line-height: 1.5;
        }
        .header-info h2 {
            margin: 0;
            font-size: 16px;
            color: #000;
        }

        /* عنوان الفاتورة */
        .invoice-title {
            text-align: center;
            border: 2px solid #000;
            margin: 10px 0;
            padding: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        /* بيانات العميل والفاتورة */
        .details-container {
            display: flex;
            border: 1px solid #000;
            margin-bottom: 0;
        }
        .customer-info {
            flex: 1.5;
            padding: 10px;
            border-left: 1px solid #000;
            font-size: 12px;
        }
        .invoice-meta {
            flex: 1;
            font-size: 12px;
        }
        .meta-row {
            display: flex;
            border-bottom: 1px solid #000;
        }
        .meta-row:last-child { border-bottom: none; }
        .meta-label {
            flex: 1;
            padding: 5px;
            border-left: 1px solid #000;
            background-color: #f9f9f9;
        }
        .meta-value {
            flex: 1.5;
            padding: 5px;
            text-align: center;
            font-weight: bold;
        }

        /* جدول الأصناف */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: -1px; /* للدمج مع الإطار العلوي */
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }
        th {
            background-color: #e2effa; /* اللون اللبني في الفاتورة */
            font-weight: bold;
        }
        .item-name { text-align: right; }

        /* التذييل والحسابات */
        .footer-container {
            display: flex;
            border: 1px solid #000;
            border-top: none;
        }
        .amount-words {
            flex: 1.5;
            padding: 10px;
            font-size: 11px;
            border-left: 1px solid #000;
        }
        .totals-table {
            flex: 1;
        }
        .totals-table table {
            margin: 0;
            border: none;
        }
        .totals-table td {
            border: none;
            border-bottom: 1px solid #000;
            padding: 5px;
            text-align: right;
        }
        .totals-table td:last-child {
            border-right: 1px solid #000;
            text-align: center;
            width: 100px;
            font-weight: bold;
        }
        .signature-section {
            padding: 20px;
            text-align: center;
            font-size: 12px;
        }

        @media print {
            body { background: none; padding: 0; }
            .invoice-box { border: none; }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="header">
        <div class="header-logo">
            <h3 style="color: #2c5aa0; margin: 0;">Global Pools</h3>
            <div style="font-size: 12px; color: #2c5aa0;">مؤسسة خدمات القوة العالمية للتجارة</div>
        </div>
        <div class="header-qr">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=Invoice-Data" alt="QR Code">
        </div>
        <div class="header-info">
            <h2>Global Power Services Trading EST</h2>
            Building: 9133, Street: King Fahad Ibn Abdulaziz Saud, 2938 Al<br>
            Bandariyah Dist.. Postal Code: 34424, Al Khobar<br>
            C.R. No. : 2397275443<br>
            Saudi Arabia<br>
            E-Mail: globalpools.ksa@gmail.com<br>
            Phone No. : +966561124775<br>
            <strong>VAT No. : 301269986600003</strong>
        </div>
    </div>

    <div class="invoice-title">TAX INVOICE الفاتورة الضريبية</div>

    <div class="details-container">
        <div class="customer-info">
            <strong>IBRAHIM ALI ALSAWAD FOR MAINTANENCE</strong><br>
            Customer Code : CM-000068<br>
            8428<br>
            32461<br>
            5203, Saudi Arabia<br><br>
            <strong>VAT No. : 300407286200003</strong>
        </div>
        <div class="invoice-meta">
            <div class="meta-row"><div class="meta-label">Invoice No.</div><div class="meta-value">CM-26-K-000031</div></div>
            <div class="meta-row"><div class="meta-label">Date</div><div class="meta-value">10.02.2026</div></div>
            <div class="meta-row"><div class="meta-label">Order No.</div><div class="meta-value"></div></div>
            <div class="meta-row"><div class="meta-label">Due Date</div><div class="meta-value">10.02.2027</div></div>
            <div class="meta-row"><div class="meta-label">Payment Terms</div><div class="meta-value">CASH</div></div>
            <div class="meta-row"><div class="meta-label">Salesman</div><div class="meta-value">Akhil - أخيل</div></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">SL No.</th>
                <th style="width: 15%;">Item Code</th>
                <th style="width: 40%;">Item Name</th>
                <th style="width: 8%;">Qty</th>
                <th style="width: 10%;">Unit Price</th>
                <th style="width: 12%;">Amount Excl.VAT (SAR)</th>
                <th style="width: 5%;">VAT (%)</th>
                <th style="width: 10%;">VAT Amount (SAR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>0030000000139</td>
                <td class="item-name">Krystal light W2007-S135WW - 1.5 inch pool light, AC12V, 8W, Warm white</td>
                <td>1.000</td>
                <td>147.83</td>
                <td>147.83</td>
                <td>15.00</td>
                <td>22.17</td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="3" style="text-align: left;">Total</td>
                <td>1</td>
                <td></td>
                <td>147.83</td>
                <td></td>
                <td>22.17</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-container">
        <div class="amount-words">
            Amount Excl. VAT (in words):<br>
            <strong>SAR. One Hundred Forty Seven And Eight Hundred Thirty halala Only.</strong><br><br>
            VAT Amount (in words):<br>
            <strong>SAR. Twenty Two And Seventeen halala Only.</strong><br><br>
            Net Total Incl. VAT (in words):<br>
            <strong>SAR. One Hundred Seventy Only.</strong>
        </div>
        <div class="totals-table">
            <table>
                <tr><td>Gross Amount</td><td>147.83</td></tr>
                <tr><td>Total Excl. VAT</td><td>147.83</td></tr>
                <tr><td>VAT</td><td>22.17</td></tr>
                <tr style="border-bottom: none;"><td><strong>Total Amount</strong></td><td><strong>170.00</strong></td></tr>
            </table>
            <div class="signature-section">
                <strong>For Global Power Services Trading EST</strong><br><br><br>
                Authorised Signatory
            </div>
        </div>
    </div>
</div>

</body>
</html>