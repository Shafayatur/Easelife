<div>
    <h1>Customer Dashboard</h1>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">My Services</h2>
                </div>
                <div class="card-body">
                    @if($serviceRequests->count() > 0 || $confirmedServices->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Provider</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serviceRequests as $request)
                                    <tr>
                                        <td>{{ $request->service->name }}</td>
                                        <td>{{ $request->serviceProvider->name }}</td>
                                        <td><span class="badge bg-secondary">Request</span></td>
                                        <td>
                                            @switch($request->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                    @break
                                                @case('accepted')
                                                    <span class="badge bg-success">Accepted</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @php
                                                $payment = $request->payments()->first();
                                                $completedPayments = $request->payments()->where('status', 'completed')->count();
                                            @endphp
                                            @if($payment)
                                                <span class="badge 
                                                    @if($payment->status == 'completed') badge-success
                                                    @elseif($payment->status == 'pending') badge-warning
                                                    @else badge-danger
                                                    @endif
                                                ">
                                                    {{ ucfirst($payment->status) }} 
                                                    ({{ $payment->payment_method ? ucfirst($payment->payment_method) : 'N/A' }})
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Not Paid</span>
                                            @endif
                                        </td>
                                        <td>{{ $request->scheduled_date->format('d M Y') }}</td>
                                        <td>-</td>
                                    </tr>
                                @endforeach

                                @foreach($confirmedServices as $service)
                                    <tr>
                                        <td>{{ $service->service->name }}</td>
                                        <td>{{ $service->serviceProvider->name }}</td>
                                        <td><span class="badge bg-primary">Booking</span></td>
                                        <td>
                                            @switch($service->status)
                                                @case('accepted')
                                                    <span class="badge bg-info">Accepted</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-success">Completed</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @php
                                                $payment = $service->payments()->first();
                                                $completedPayments = $service->payments()->where('status', 'completed')->count();
                                            @endphp
                                            @if($payment)
                                                <span class="badge 
                                                    @if($payment->status == 'completed') badge-success
                                                    @elseif($payment->status == 'pending') badge-warning
                                                    @else badge-danger
                                                    @endif
                                                ">
                                                    {{ ucfirst($payment->status) }} 
                                                    ({{ $payment->payment_method ? ucfirst($payment->payment_method) : 'N/A' }})
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Not Paid</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($service->date)->format('d M Y') }}</td>
                                        <td>
                                            @if($service->status == 'accepted')
                                                @php
                                                    $completedPayments = $service->payments()->where('status', 'completed')->count();
                                                @endphp
                                                @if($completedPayments == 0)
                                                    <a 
                                                        href="{{ route('payment.form', ['bookingId' => $service->id]) }}" 
                                                        class="btn btn-primary btn-sm"
                                                    >
                                                        Pay Now
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No services or bookings found.</p>
                    @endif
                </div>
            </div>

            @push('scripts')
            <script>
                function showPaymentForm(bookingId, amount) {
                    console.log('Showing payment form for booking:', bookingId, 'Amount:', amount);
                    document.getElementById('bookingId').value = bookingId;
                    document.getElementById('amount').value = amount.toFixed(2);
                    $('#paymentModal').modal('show');
                }

                function processPayment() {
                    const bookingId = document.getElementById('bookingId').value;
                    const amount = document.getElementById('amount').value;
                    const paymentMethod = document.getElementById('selectedMethod').value;

                    const formData = {
                        booking_id: bookingId,
                        amount: amount,
                        payment_method: paymentMethod
                    };

                    // Add method-specific details based on selected method
                    switch(paymentMethod) {
                        case 'card':
                            formData.card_name = document.querySelector('input[name="card_name"]').value;
                            formData.card_number = document.querySelector('input[name="card_number"]').value;
                            formData.expiry_date = document.querySelector('input[name="expiry_date"]').value;
                            formData.cvv = document.querySelector('input[name="cvv"]').value;
                            break;
                        case 'bank':
                            formData.bank_name = document.querySelector('select[name="bank_name"]').value;
                            formData.account_number = document.querySelector('input[name="account_number"]').value;
                            formData.account_name = document.querySelector('input[name="account_name"]').value;
                            break;
                        case 'mobile':
                            formData.mobile_provider = document.querySelector('select[name="mobile_provider"]').value;
                            formData.mobile_number = document.querySelector('input[name="mobile_number"]').value;
                            formData.transaction_id = document.querySelector('input[name="transaction_id"]').value;
                            break;
                    }

                    fetch('/booking/' + bookingId + '/pay', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Create a simple success page
                            document.open();
                            document.write(`
                                <!DOCTYPE html>
                                <html>
                                <head>
                                    <title>Payment Successful</title>
                                    <style>
                                        body { 
                                            font-family: Arial, sans-serif; 
                                            text-align: center; 
                                            padding: 50px; 
                                            background-color: #f0f0f0;
                                        }
                                        .success-container {
                                            background-color: white;
                                            border-radius: 10px;
                                            padding: 30px;
                                            max-width: 500px;
                                            margin: 0 auto;
                                            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
                                        <p>Your payment for Booking #${bookingId} has been processed.</p>
                                        <p>Payment Method: ${data.payment_method.toUpperCase()}</p>
                                        <p>Amount: $${parseFloat(amount).toFixed(2)}</p>
                                        <a href="/customer/dashboard" class="btn">Return to Dashboard</a>
                                    </div>
                                </body>
                                </html>
                            `);
                            document.close();
                        } else {
                            alert('Payment failed: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred during payment');
                    });
                }
            </script>
            @endpush
        </div>
        
        <div class="col-md-4">
            @livewire('customer.customer-notification-component')
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" id="bookingId" name="booking_id">
                    <div class="form-group">
                        <label for="amount">Total Amount</label>
                        <input type="text" class="form-control" id="amount" name="amount" readonly>
                    </div>
                    <div class="form-group">
                        <label for="cardName">Name on Card</label>
                        <input type="text" class="form-control" id="cardName" name="card_name" required>
                    </div>
                    <div class="form-group">
                        <label for="cardNumber">Card Number</label>
                        <input type="text" class="form-control" id="cardNumber" name="card_number" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="expiryDate">Expiry Date</label>
                            <input type="text" class="form-control" id="expiryDate" name="expiry_date" placeholder="MM/YY" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="cvv">CVV</label>
                            <input type="text" class="form-control" id="cvv" name="cvv" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="processPayment()">Submit Payment</button>
            </div>
        </div>
    </div>
</div>
