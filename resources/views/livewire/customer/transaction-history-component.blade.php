<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                        Return to Dashboard
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        Return to Homepage
                    </a>
                </div>
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Transaction History</h2>
                    
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Service Provider</th>
                                        <th>Payment Method</th>
                                        <th>Service Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                                            <td>{{ number_format($transaction->amount, 2) }} BDT</td>
                                            <td>{{ optional($transaction->booking->serviceProvider)->name ?? 'N/A' }}</td>
                                            <td>{{ ucfirst($transaction->payment_method) }}</td>
                                            <td>{{ optional($transaction->booking->service)->name ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">No transactions found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
