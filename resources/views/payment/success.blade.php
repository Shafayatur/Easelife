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
            max-width: 500px;
            width: 100%;
        }
        .success-icon {
            color: green;
            font-size: 72px;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">&#10004;</div>
        <h1>Payment Successful</h1>
        <p>Your payment for Booking #{{ $booking_id }} has been processed.</p>
        <p>Payment Method: {{ strtoupper($payment_method) }}</p>
        <p>Amount: ${{ number_format($amount, 2) }}</p>
        <a href="{{ route('customer.dashboard') }}" class="btn">Return to Dashboard</a>
    </div>
</body>
</html>
