<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            background-color: #f0f0f0;
        }
        .success-container {
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
        }
        .success-icon {
            color: green;
            font-size: 72px;
            margin-bottom: 20px;
        }
        .payment-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .payment-details-table th, 
        .payment-details-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .payment-details-table th {
            background-color: #f4f4f4;
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
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
        .btn svg {
            margin-right: 5px;
        }
        .error-message {
            background-color: #f44336;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">&#10004;</div>
        <h1>Payment Successful</h1>
        
        @if(isset($error))
            <div class="error-message">
                <p>{{ $error }}</p>
            </div>
        @else
            <div class="invoice-details">
                <table class="payment-details-table">
                    <tr>
                        <th>Booking ID</th>
                        <td>{{ $booking->id }}</td>
                    </tr>
                    <tr>
                        <th>Payment Method</th>
                        <td>{{ strtoupper($payment_method) }}</td>
                    </tr>
                    <tr>
                        <th>Service Name</th>
                        <td>{{ $service_name }}</td>
                    </tr>
                    <tr>
                        <th>Service Provider</th>
                        <td>{{ $service_provider_name }} (ID: {{ $service_provider_id }})</td>
                    </tr>
                    <tr>
                        <th>Booking Date</th>
                        <td>
                            @if(isset($booking->created_at) && $booking->created_at)
                                {{ $booking->created_at->format('Y-m-d H:i:s') }}
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Original Amount</td>
                        <td>{{ number_format($total_payment, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Discounted Payment</td>
                        <td>{{ number_format($discounted_payment, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Final Amount</td>
                        <td>{{ number_format($amount, 2) }}</td>
                    </tr>
                </table>
            </div>

            <div class="total">
                <strong>Total Paid: ${{ number_format($amount, 2) }}</strong>
            </div>
        @endif

        <div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px;">
            <a href="{{ route('customer.dashboard') }}" class="btn">Return to Dashboard</a>
            <a href="{{ route('invoice.download', ['booking_id' => $booking->id]) }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                </svg>
                Download Invoice
            </a>
        </div>
    </div>
</body>
</html>
