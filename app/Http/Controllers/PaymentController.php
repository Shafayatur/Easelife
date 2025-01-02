<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            'transaction_id' => 'required_if:payment_method,mobile'
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

            // Validate booking total price matches
            if (abs($booking->total_price - $validatedData['amount']) > 0.01) {
                return view('payment.error', [
                    'message' => 'Payment amount does not match booking total',
                    'booking_id' => $booking->id
                ]);
            }

            // Create payment record with method-specific details
            $paymentDetails = [
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'amount' => $validatedData['amount'],
                'status' => 'completed',
                'payment_method' => $validatedData['payment_method']
            ];

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
                    $paymentDetails['status'] = 'pending'; // COD will be marked as completed later
                    break;
            }

            // Create payment record
            $payment = Payment::create($paymentDetails);

            // Update booking status 
            // For COD, status remains pending until service is completed
            $booking->status = $validatedData['payment_method'] === 'cod' ? 'accepted' : 'completed';
            $booking->save();

            // Return success view
            return view('payment.success', [
                'booking_id' => $booking->id,
                'amount' => $validatedData['amount'],
                'payment_method' => $validatedData['payment_method']
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
        $payment = Payment::where('booking_id', $bookingId)
            ->where('user_id', Auth::id())
            ->first();

        if ($payment) {
            $payment->update([
                'status' => 'completed',
                'transaction_id' => uniqid('stripe_'),
            ]);

            // Update booking status if needed
            $booking = Booking::find($bookingId);
            $booking->update(['status' => 'completed']);

            return redirect()->route('dashboard')->with('success', 'Payment completed successfully!');
        }

        return redirect()->route('dashboard')->with('error', 'Payment verification failed.');
    }

    public function paymentCancel($bookingId)
    {
        $payment = Payment::where('booking_id', $bookingId)
            ->where('user_id', Auth::id())
            ->first();

        if ($payment) {
            $payment->update([
                'status' => 'failed',
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
                        <p>You will pay the total amount of $' . number_format($booking->total_price, 2) . ' at the time of service.</p>
                    </div>

                    <button type="submit" id="submitButton" disabled>Proceed with Payment</button>
                </form>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const paymentOptions = document.querySelectorAll(".payment-option");
                        const paymentForms = document.querySelectorAll(".payment-form");
                        const selectedMethodInput = document.getElementById("selectedMethod");
                        const submitButton = document.getElementById("submitButton");

                        paymentOptions.forEach(option => {
                            option.addEventListener("click", function() {
                                // Remove selected from all options
                                paymentOptions.forEach(opt => opt.classList.remove("selected"));
                                // Hide all forms
                                paymentForms.forEach(form => form.classList.remove("active"));

                                // Select current option
                                this.classList.add("selected");
                                
                                // Show corresponding form
                                const method = this.dataset.method;
                                document.getElementById(method + "Form").classList.add("active");
                                
                                // Set selected method
                                selectedMethodInput.value = method;

                                // Enable submit button
                                submitButton.disabled = false;
                            });
                        });
                    });
                </script>
            </body>
            </html>
        ', 200, ['Content-Type' => 'text/html']);
    }
}
