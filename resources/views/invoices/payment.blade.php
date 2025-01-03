<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $booking_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .company-info h1 {
            margin: 0;
            color: #007bff;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details th, .invoice-details td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .invoice-details th {
            background-color: #f4f4f4;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #777;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div class="company-info">
            <h1>Easelife Services</h1>
            <p>Invoice for Booking #{{ $booking_id }}</p>
        </div>
        <div class="customer-info">
            <strong>Customer:</strong> {{ $customer_name }}<br>
            <strong>Email:</strong> {{ $customer_email }}
        </div>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <th>Service Name</th>
                <td>{{ $service_name }}</td>
            </tr>
            <tr>
                <th>Service Provider</th>
                <td>{{ $service_provider_name }}</td>
            </tr>
            <tr>
                <th>Booking Date</th>
                <td>{{ \Carbon\Carbon::parse($booking_date)->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <th>Payment Method</th>
                <td>{{ strtoupper($payment_method) }}</td>
            </tr>
            <tr>
                <td>Original Amount</td>
                <td>{{ number_format($original_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Discounted Payment</td>
                <td>{{ number_format($original_amount - $amount, 2) }}</td>
            </tr>
            <tr>
                <td>Final Amount</td>
                <td>{{ number_format($amount, 2) }}</td>
            </tr>
            @if($coupon_applied)
            <tr>
                <th>Discount Amount</th>
                <td>${{ number_format($discount_amount, 2) }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="total">
        <strong>Total Paid: ${{ number_format($amount, 2) }}</strong>
    </div>

    <div class="footer">
        <p>Thank you for using Easelife Services. This is an automatically generated invoice.</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
