<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Stripe API key from .env
        // Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function processPayment(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:card,bank,mobile,cod',
            
            // Conditional validation based on payment method
            'card_name' => 'required_if:payment_method,card',
            'card_number' => 'required_if:payment_method,card',
            'expiry_date' => 'required_if:payment_method,card',
            'cvv' => 'required_if:payment_method,card',
            
            'bank_name' => 'required_if:payment_method,bank',
            'account_number' => 'required_if:payment_method,bank',
            'account_name' => 'required_if:payment_method,bank',
            
            'mobile_provider' => 'required_if:payment_method,mobile',
            'mobile_number' => 'required_if:payment_method,mobile',
            'transaction_id' => 'required_if:payment_method,mobile',
            'coupon_code' => 'nullable|string',
        ]);

        try {
            // Find the booking
            $booking = Booking::findOrFail($validatedData['booking_id']);
            
            // Ensure the booking belongs to the authenticated user
            if ($booking->customer_id !== Auth::id()) {
                return view('payment.error', [
                    'message' => 'Unauthorized payment attempt',
                    'booking_id' => $validatedData['booking_id']
                ]);
            }

            // Initialize payment variables
            $originalAmount = $booking->total_price;
            $finalAmount = $originalAmount;
            $discountAmount = 0;
            $couponApplied = false;

            // Check coupon logic if coupon code is provided
            $finalAmount = $originalAmount;
            $discountAmount = 0;
            $couponApplied = false;

            if (!empty($validatedData['coupon_code']) && strtolower($validatedData['coupon_code']) === 'hehe') {
                // Find existing coupon usage record
                $couponUsage = \DB::table('user_coupon_usage')
                    ->where('user_id', Auth::id())
                    ->where('coupon_code', 'hehe')
                    ->first();

                // If coupon usage has reached max, return error
                if ($couponUsage && $couponUsage->current_usage >= $couponUsage->max_usage) {
                    return view('payment.error', [
                        'message' => 'You have used all available "hehe" coupon discounts.',
                        'booking_id' => $booking->id
                    ]);
                }

                // Apply 50% discount
                $discountAmount = $originalAmount * 0.5;
                $finalAmount = $originalAmount - $discountAmount;
                $couponApplied = true;

                // Update or create coupon usage record
                if (!$couponUsage) {
                    \DB::table('user_coupon_usage')->insert([
                        'user_id' => Auth::id(),
                        'coupon_code' => 'hehe',
                        'current_usage' => 1,
                        'max_usage' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'booking_id' => $booking->id
                    ]);
                } else {
                    \DB::table('user_coupon_usage')
                        ->where('user_id', Auth::id())
                        ->where('coupon_code', 'hehe')
                        ->update([
                            'current_usage' => \DB::raw('current_usage + 1'),
                            'updated_at' => now()
                        ]);
                }

                // Insert coupon usage record for this booking
                \DB::table('user_coupon_usage')->insert([
                    'user_id' => Auth::id(),
                    'coupon_code' => 'hehe',
                    'booking_id' => $booking->id,
                    'discount_amount' => $discountAmount,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Validate booking total price matches with more tolerance
            $submittedAmount = $validatedData['amount'];
            
            // Use original amount for validation, but final amount for payment
            $validatedData['amount'] = $finalAmount;

            Log::info('Payment Amount Validation', [
                'original_amount' => $originalAmount,
                'final_amount' => $finalAmount,
                'submitted_amount' => $submittedAmount,
                'amount_difference' => abs($finalAmount - $submittedAmount),
                'coupon_applied' => $couponApplied
            ]);

            // Create payment record
            $paymentDetails = [
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'amount' => $finalAmount,
                'original_amount' => $originalAmount,
                'status' => Payment::STATUS_COMPLETED,
                'payment_method' => $validatedData['payment_method'],
                'coupon_code' => $validatedData['coupon_code'] ?? null,
                'coupon_applied' => $couponApplied ?? false
            ];

            // Create payment
            $payment = Payment::create($paymentDetails);

            // Add method-specific details
            switch ($validatedData['payment_method']) {
                case 'card':
                    $paymentDetails['card_last4'] = substr($validatedData['card_number'], -4);
                    break;
                case 'bank':
                    $paymentDetails['bank_name'] = $validatedData['bank_name'];
                    $paymentDetails['account_number'] = $validatedData['account_number'];
                    break;
                case 'mobile':
                    $paymentDetails['mobile_provider'] = $validatedData['mobile_provider'];
                    $paymentDetails['mobile_number'] = $validatedData['mobile_number'];
                    $paymentDetails['transaction_id'] = $validatedData['transaction_id'];
                    break;
                case 'cod':
                    $paymentDetails['status'] = Payment::STATUS_PENDING; // COD will be marked as completed later
                    break;
            }

            // Update payment record
            $payment->update($paymentDetails);

            // Update booking status 
            // For COD, status remains pending until service is completed
            $booking->status = $validatedData['payment_method'] === 'cod' ? 'accepted' : 'completed';
            $booking->save();

            // Fetch service details
            $service = \DB::table('services')
                ->where('id', $booking->service_id)
                ->first();

            // Fetch service provider details
            $serviceProvider = \DB::table('users')
                ->where('id', $booking->service_provider_id)
                ->first();

            // Return payment success view with all necessary data
            return view('payment.success', [
                'booking' => $booking,
                'payment' => $payment,
                'booking_id' => $booking->id,
                'booking_date' => $booking->created_at,
                'payment_method' => $validatedData['payment_method'],
                'total_payment' => $originalAmount,
                'amount' => $finalAmount,
                'discounted_payment' => $discountAmount,
                'coupon_applied' => $couponApplied,
                'customer_name' => Auth::user()->name ?? 'N/A',
                'customer_id' => Auth::id() ?? 'N/A',
                'service_name' => $service ? $service->name : 'N/A',
                'service_provider_name' => $serviceProvider ? $serviceProvider->name : 'N/A',
                'service_provider_id' => $booking->service_provider_id ?? 'N/A'
            ]);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Payment Processing Error', [
                'booking_id' => $validatedData['booking_id'] ?? null,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            // Return error view
            return view('payment.error', [
                'message' => 'Payment processing failed: ' . $e->getMessage(),
                'booking_id' => $validatedData['booking_id'] ?? null
            ]);
        }
    }

    public function paymentSuccess($bookingId)
    {
        try {
            // Fetch payment details using direct database queries
            $payment = \DB::table('payments')
                ->where('booking_id', $bookingId)
                ->latest('created_at')
                ->first();

            // Fetch booking details
            $booking = \DB::table('bookings')
                ->where('id', $bookingId)
                ->first();

            // Fetch service details
            $service = $booking && $booking->service_id 
                ? \DB::table('services')
                    ->where('id', $booking->service_id)
                    ->first() 
                : null;

            // Fetch customer details
            $customer = $booking && $booking->customer_id
                ? \DB::table('users')
                    ->where('id', $booking->customer_id)
                    ->first()
                : null;

            // Fetch service provider details
            $serviceProvider = $booking && $booking->service_provider_id
                ? \DB::table('users')
                    ->where('id', $booking->service_provider_id)
                    ->first()
                : null;

            // Prepare view data with explicit null checks and default values
            $viewData = [
                'booking_id' => $bookingId ?? 'N/A',
                'payment_method' => $payment ? $payment->payment_method : 'N/A',
                'amount' => $payment ? $payment->amount : 0,
                'service_name' => $service ? $service->name : 'N/A',
                'customer_name' => $customer ? $customer->name : 'N/A',
                'customer_id' => $customer ? $customer->id : 'N/A',
                'service_provider_name' => $serviceProvider ? $serviceProvider->name : 'N/A',
                'service_provider_id' => $serviceProvider ? $serviceProvider->id : 'N/A',
                'booking_date' => $booking ? $booking->created_at : null,
                'original_amount' => $booking ? $booking->total_price : 0,
                'debug_info' => [
                    'payment' => $payment,
                    'booking' => $booking,
                    'service' => $service,
                    'customer' => $customer,
                    'service_provider' => $serviceProvider
                ]
            ];

            // Extensive logging for debugging
            \Log::info('Payment Success Data Retrieval', [
                'booking_id' => $bookingId,
                'payment_exists' => $payment ? 'Yes' : 'No',
                'booking_exists' => $booking ? 'Yes' : 'No',
                'service_exists' => $service ? 'Yes' : 'No',
                'customer_exists' => $customer ? 'Yes' : 'No',
                'service_provider_exists' => $serviceProvider ? 'Yes' : 'No'
            ]);

            return view('payment.success', $viewData);

        } catch (\Exception $e) {
            // Comprehensive error logging
            \Log::error('Payment Success Error', [
                'booking_id' => $bookingId,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return view('payment.success', [
                'error' => 'An unexpected error occurred: ' . $e->getMessage(),
                'booking_id' => $bookingId,
                'payment_method' => 'N/A',
                'amount' => 0,
                'service_name' => 'N/A',
                'customer_name' => 'N/A',
                'customer_id' => 'N/A',
                'service_provider_name' => 'N/A',
                'service_provider_id' => 'N/A',
                'booking_date' => null,
                'original_amount' => 0
            ]);
        }
    }

    public function paymentCancel($bookingId)
    {
        $payment = Payment::where('booking_id', $bookingId)
            ->where('user_id', Auth::id())
            ->first();

        if ($payment) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
            ]);
        }

        return redirect()->route('dashboard')->with('error', 'Payment was cancelled.');
    }

    public function generatePaymentForm($bookingId)
    {
        // Find the booking
        $booking = Booking::findOrFail($bookingId);
        
        // Ensure the booking belongs to the authenticated user
        if ($booking->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Return a comprehensive payment form
        return response()->make('
            <!DOCTYPE html>
            <html>
            <head>
                <title>Payment for Booking #' . $bookingId . '</title>
                <style>
                    body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
                    .payment-options { display: flex; justify-content: space-between; margin-bottom: 20px; }
                    .payment-option { 
                        flex: 1; 
                        border: 2px solid #ddd; 
                        padding: 15px; 
                        margin: 0 10px; 
                        text-align: center; 
                        cursor: pointer;
                        transition: all 0.3s ease;
                    }
                    .payment-option:hover, .payment-option.selected { 
                        border-color: #007bff; 
                        background-color: #f0f0f0; 
                    }
                    .payment-form { 
                        background: #f4f4f4; 
                        padding: 20px; 
                        border-radius: 5px; 
                        display: none; 
                    }
                    .payment-form.active { display: block; }
                    input, select { width: 100%; padding: 10px; margin: 10px 0; }
                    button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; }
                </style>
            </head>
            <body>
                <form id="paymentForm" action="/booking/' . $bookingId . '/pay" method="POST">
                    ' . csrf_field() . '
                    <h2>Choose Payment Method</h2>
                    
                    <div class="payment-options">
                        <div class="payment-option" data-method="card">
                            <h3>Credit/Debit Card</h3>
                            <p>Visa, MasterCard, American Express</p>
                        </div>
                        <div class="payment-option" data-method="bank">
                            <h3>Bank Transfer</h3>
                            <p>Direct bank account payment</p>
                        </div>
                        <div class="payment-option" data-method="mobile">
                            <h3>Mobile Banking</h3>
                            <p>bKash, Nagad, Rocket</p>
                        </div>
                        <div class="payment-option" data-method="cod">
                            <h3>Cash on Delivery</h3>
                            <p>Pay at time of service</p>
                        </div>
                    </div>

                    <input type="hidden" name="booking_id" value="' . $bookingId . '">
                    <input type="hidden" name="amount" value="' . $booking->total_price . '">
                    <input type="hidden" id="selectedMethod" name="payment_method" required>

                    <div id="cardForm" class="payment-form">
                        <h3>Card Payment Details</h3>
                        <input type="text" name="card_name" placeholder="Name on Card">
                        <input type="text" name="card_number" placeholder="Card Number">
                        <div style="display: flex;">
                            <input type="text" name="expiry_date" placeholder="MM/YY" style="width: 50%; margin-right: 10px;">
                            <input type="text" name="cvv" placeholder="CVV" style="width: 50%;">
                        </div>
                    </div>

                    <div id="bankForm" class="payment-form">
                        <h3>Bank Transfer</h3>
                        <select name="bank_name">
                            <option value="">Select Bank</option>
                            <option value="city_bank">City Bank</option>
                            <option value="brac_bank">BRAC Bank</option>
                            <option value="dutch_bangla">Dutch-Bangla Bank</option>
                            <option value="standard_chartered">Standard Chartered</option>
                        </select>
                        <input type="text" name="account_number" placeholder="Account Number">
                        <input type="text" name="account_name" placeholder="Account Name">
                    </div>

                    <div id="mobileForm" class="payment-form">
                        <h3>Mobile Banking</h3>
                        <select name="mobile_provider">
                            <option value="">Select Provider</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="rocket">Rocket</option>
                        </select>
                        <input type="text" name="mobile_number" placeholder="Mobile Number">
                        <input type="text" name="transaction_id" placeholder="Transaction ID">
                    </div>

                    <div id="codForm" class="payment-form">
                        <h3>Cash on Delivery</h3>
                        <p>Pay at time of service</p>
                    </div>

                    <div style="margin-top: 20px;">
                        <h3>Have a Coupon?</h3>
                        <input type="text" name="coupon_code" placeholder="Enter Coupon Code (Optional)">
                        <p style="color: #888; font-size: 0.9em; margin-top: 5px;">
                            &#x1F4A1; Use coupon code "hehe" on your first 2 services to get 50% off
                        </p>
                    </div>

                    <button type="submit">Proceed to Payment</button>
                </form>

                <script>
                    document.querySelectorAll(\'.payment-option\').forEach(option => {
                        option.addEventListener(\'click\', function() {
                            // Remove selected class from all options
                            document.querySelectorAll(\'.payment-option\').forEach(opt => opt.classList.remove(\'selected\'));
                            
                            // Hide all forms
                            document.querySelectorAll(\'.payment-form\').forEach(form => form.classList.remove(\'active\'));
                            
                            // Add selected class to clicked option
                            this.classList.add(\'selected\');
                            
                            // Show corresponding form
                            const method = this.getAttribute(\'data-method\');
                            document.getElementById(method + \'Form\').classList.add(\'active\');
                            
                            // Set selected method in hidden input
                            document.getElementById(\'selectedMethod\').value = method;
                        });
                    });
                </script>
            </body>
            </html>
        ', 200, ['Content-Type' => 'text/html']);
    }

    public function downloadInvoice($bookingId)
    {
        try {
            // Fetch payment details
            $payment = \DB::table('payments')
                ->where('booking_id', $bookingId)
                ->latest('created_at')
                ->first();

            // Fetch booking details
            $booking = \DB::table('bookings')
                ->where('id', $bookingId)
                ->first();

            // Fetch service details
            $service = $booking && $booking->service_id 
                ? \DB::table('services')
                    ->where('id', $booking->service_id)
                    ->first() 
                : null;

            // Fetch customer details
            $customer = $booking && $booking->customer_id
                ? \DB::table('users')
                    ->where('id', $booking->customer_id)
                    ->first()
                : null;

            // Fetch service provider details
            $serviceProvider = $booking && $booking->service_provider_id
                ? \DB::table('users')
                    ->where('id', $booking->service_provider_id)
                    ->first()
                : null;

            // Fetch coupon details if applied
            $couponUsage = \DB::table('user_coupon_usage')
                ->where('booking_id', $bookingId)
                ->first();

            // Generate PDF
            $pdf = \PDF::loadView('invoices.payment', [
                'booking_id' => $bookingId,
                'payment_method' => $payment ? $payment->payment_method : 'N/A',
                'amount' => $payment ? $payment->amount : 0,
                'service_name' => $service ? $service->name : 'N/A',
                'customer_name' => $customer ? $customer->name : 'N/A',
                'customer_email' => $customer ? $customer->email : 'N/A',
                'service_provider_name' => $serviceProvider ? $serviceProvider->name : 'N/A',
                'booking_date' => $booking ? $booking->created_at : null,
                'original_amount' => $booking ? $booking->total_price : 0,
                'total_payment' => $booking ? $booking->total_price : 0,
                'discounted_payment' => $couponUsage ? $booking->total_price - $couponUsage->discount_amount : $booking->total_price,
                'discount_amount' => $couponUsage ? $couponUsage->discount_amount : 0,
                'coupon_applied' => $couponUsage ? true : false
            ]);

            // Download PDF
            return $pdf->download("invoice_{$bookingId}.pdf");

        } catch (\Exception $e) {
            \Log::error('Invoice Download Error', [
                'booking_id' => $bookingId,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }
}
