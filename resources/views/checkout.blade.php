@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="shop-checkout container">
            <h2 class="page-title">Shipping and Checkout</h2>
            <div class="checkout-steps">
                <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">01</span>
                    <span class="checkout-steps__item-title">
                        <span>Shopping Bag</span>
                        <em>Manage Your Items List</em>
                    </span>
                </a>
                <a href="javascript:void(0)" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">02</span>
                    <span class="checkout-steps__item-title">
                        <span>Shipping and Checkout</span>
                        <em>Checkout Your Items List</em>
                    </span>
                </a>
                <a href="javascript:void(0)" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">03</span>
                    <span class="checkout-steps__item-title">
                        <span>Confirmation</span>
                        <em>Review And Submit Your Order</em>
                    </span>
                </a>
            </div>
            <form name="checkout-form" action="{{ route('cart.place.order') }}" method="POST" id="checkout-form">
                @csrf
                <div class="checkout-form">
                    <div class="billing-info__wrapper">
                        <div class="row">
                            <div class="col-6">
                                <h4>SHIPPING DETAILS</h4>
                            </div>
                            <div class="col-6">
                            </div>
                        </div>
                        @if ($address)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="my-account_address-list">
                                        <div class="my-account_address-list-item">
                                            <div class="my-account_address-item_datail">
                                                <p>{{ $address->name }}</p>
                                                <p>{{ $address->address }}</p>
                                                <p>{{ $address->landmark }}</p>
                                                <p>{{ $address->city }}, {{ $address->state }}, {{ $address->country }}</p>
                                                <p>{{ $address->zip }}</p>
                                                <br>
                                                <p>{{ $address->phone }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row mt-5">
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="name" required=""
                                            value="{{ old('name') }}">
                                        <label for="name">Full Name *</label>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="phone" required=""
                                            value="{{ old('phone') }}">
                                        <label for="phone">Phone Number *</label>
                                        @error('phone')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="zip" required=""
                                            value="{{ old('zip') }}">
                                        <label for="zip">Pincode *</label>
                                        @error('zip')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mt-3 mb-3">
                                        <input type="text" class="form-control" name="state" required=""
                                            value="{{ old('state') }}">
                                        <label for="state">State *</label>
                                        @error('state')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="city" required=""
                                            value="{{ old('city') }}">
                                        <label for="city">Town / City *</label>
                                        @error('city')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="address" required=""
                                            value="{{ old('address') }}">
                                        <label for="address">House no, Building Name *</label>
                                        @error('address')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="locality" required=""
                                            value="{{ old('locality') }}">
                                        <label for="locality">Road Name, Area, Colony *</label>
                                        @error('locality')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="landmark" required=""
                                            value="{{ old('landmark') }}">
                                        <label for="landmark">Landmark *</label>
                                        @error('landmark')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="checkout__totals-wrapper">
                        <div class="sticky-content">
                            <div class="checkout__totals">
                                <h3>Your Order</h3>
                                <table class="checkout-cart-items">
                                    <thead>
                                        <tr>
                                            <th>PRODUCT</th>
                                            <th align="right">SUBTOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (Cart::instance('cart') as $item)
                                            <tr>
                                                <td>
                                                    {{ $item->name }} x {{ $item->qty }}
                                                </td>
                                                <td align="right">
                                                    ${{ $item->subtotal() }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if (Session::has('discount'))
                                    <table class="checkout-totals">
                                        <tbody>
                                            <tr>
                                                <th>Subtotal</th>
                                                <td class="text-right">${{ Cart::instance('cart')->subtotal() }}</td>
                                            </tr>
                                            <tr>
                                                <th>Discount {{ Session::get('coupon')['code'] }}</th>
                                                <td class="text-right">${{ Session::get('discount')['discount'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Subtotal After Discount</th>
                                                <td class="text-right">${{ Session::get('discount')['subtotal'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Shipping</th>
                                                <td class="text-right">Free</td>
                                            </tr>
                                            <tr>
                                                <th>VAT</th>
                                                <td class="text-right">${{ Session::get('discount')['tax'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total</th>
                                                <td class="text-right">${{ Session::get('discount')['total'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @else
                                    <table class="checkout-totals">
                                        <tbody>
                                            <tr>
                                                <th>SUBTOTAL</th>
                                                <td class="text-right">${{ Cart::instance('cart')->subtotal() }}</td>
                                            </tr>
                                            <tr>
                                                <th>SHIPPING</th>
                                                <td class="text-right">Free shipping</td>
                                            </tr>
                                            <tr>
                                                <th>VAT</th>
                                                <td class="text-right">${{ Cart::instance('cart')->tax() }}</td>
                                            </tr>
                                            <tr>
                                                <th>TOTAL</th>
                                                <td class="text-right">${{ Cart::instance('cart')->total() }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                            <div class="checkout__payment-methods">
                                <div class="form-check">
                                    <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                                        id="mode1" value="card">
                                    <label class="form-check-label" for="mode1">
                                        Debit or Credit Card
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                                        id="mode2" value="khqr">
                                    <label class="form-check-label" for="mode2">
                                        KHQR
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                                        id="mode3" value="cod" checked>
                                    <label class="form-check-label" for="mode3">
                                        Cash on delivery
                                    </label>
                                </div>
                                <div class="policy-text">
                                    Your personal data will be used to process your order, support your experience
                                    throughout this website, and for other purposes described in our <a href="terms.html"
                                        target="_blank">privacy policy</a>.
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-checkout">PLACE ORDER</button>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <!-- KHQR Modal -->
        {{-- <div class="modal fade" id="khqrModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="khqrModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="khqrModalLabel">Scan KHQR to Pay</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div id="qrcode-container" class="mb-3 d-flex justify-content-center">
                            <canvas id="qrcode"></canvas>
                        </div>
                        <div class="alert alert-info">
                            <strong>Scan this QR code</strong> with your banking app to complete payment
                        </div>
                        <div id="payment-status" class="mt-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Checking payment...</span>
                            </div>
                            <p class="mt-2">Waiting for payment confirmation...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Replace your KHQR Modal with this updated version -->
        <div class="modal fade" id="khqrModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="khqrModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="khqrModalLabel">Scan KHQR to Pay</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div id="qrcode-container" class="mb-3 d-flex justify-content-center">
                            <canvas id="qrcode"></canvas>
                        </div>
                        <div class="alert alert-info">
                            <strong>Scan this QR code</strong> with your banking app to complete payment
                        </div>

                        <!-- TEST BUTTON - REMOVE IN PRODUCTION -->
                        <button type="button" class="btn btn-warning mb-3" id="test-payment-success">
                            🧪 Test Payment Success (Dev Only)
                        </button>

                        <div id="payment-status" class="mt-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Checking payment...</span>
                            </div>
                            <p class="mt-2">Waiting for payment confirmation...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <script>
        $(document).ready(function() {
            let paymentCheckInterval;
            let khqrMd5;
            let orderId;

            // Handle form submission
            $('#checkout-form').on('submit', function(e) {
                e.preventDefault();

                const selectedMode = $('input[name="mode"]:checked').val();

                if (selectedMode === 'khqr') {
                    // Submit form via AJAX for KHQR
                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            console.log('Order placed:', response);
                            if (response.success) {
                                orderId = response.order_id;
                                // Generate and show KHQR
                                generateAndShowKHQR(orderId);
                            }
                        },
                        error: function(xhr) {
                            console.error('Order placement error:', xhr);
                            showAlert('Error placing order. Please try again.', 'danger');
                        }
                    });
                } else {
                    // Submit form normally for other payment methods
                    this.submit();
                }
            });

            function generateAndShowKHQR(orderId) {
                console.log('Generating KHQR for order:', orderId);
                // Call API to generate KHQR
                $.ajax({
                    url: '/generate-khqr/' + orderId,
                    method: 'GET',
                    success: function(response) {
                        console.log('KHQR Response:', response);
                        if (response.status && response.status.code === 0) {
                            const qrString = response.data.qr;
                            khqrMd5 = response.data.md5;

                            console.log('KHQR MD5:', khqrMd5);

                            // Generate QR code
                            const canvas = document.getElementById('qrcode');
                            QRCode.toCanvas(canvas, qrString, {
                                width: 300,
                                margin: 2
                            }, function(error) {
                                if (error) {
                                    console.error(error);
                                    showAlert('Error generating QR code', 'danger');
                                    return;
                                }
                                console.log('QR Code generated successfully');
                            });

                            // Show modal
                            $('#khqrModal').modal('show');

                            // Start checking for payment
                            startPaymentCheck();
                        } else {
                            console.error('Invalid KHQR response:', response);
                            showAlert('Error generating KHQR. Please try again.', 'danger');
                        }
                    },
                    error: function(xhr) {
                        console.error('KHQR generation error:', xhr);
                        showAlert('Error generating KHQR. Please try again.', 'danger');
                    }
                });
            }

            function startPaymentCheck() {
                console.log('Starting payment check with MD5:', khqrMd5, 'Order ID:', orderId);
                // Check payment status every 3 seconds
                paymentCheckInterval = setInterval(function() {
                    checkPaymentStatus();
                }, 3000);
            }

            function checkPaymentStatus() {
                console.log('Checking payment status...');
                $.ajax({
                    url: '/check-khqr-payment',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        md5: khqrMd5,
                        order_id: orderId
                    },
                    success: function(response) {
                        console.log('Payment check response:', response);
                        console.log('Response success:', response.success);
                        console.log('Response paid:', response.paid);

                        // Check if payment is successful
                        if (response.success === true && response.paid === true) {
                            console.log('Payment confirmed! Closing modal...');
                            handlePaymentSuccess();
                        } else {
                            console.log('Payment still pending...');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error checking payment status:', xhr);
                        console.error('Response text:', xhr.responseText);
                    }
                });
            }

            // Test button click handler
            $(document).on('click', '#test-payment-success', function() {
                console.log('TEST: Simulating successful payment');

                // Stop the payment check interval
                if (paymentCheckInterval) {
                    clearInterval(paymentCheckInterval);
                }

                // Show processing state
                $('#payment-status').html(
                    '<div class="spinner-border text-warning" role="status">' +
                    '<span class="visually-hidden">Processing...</span>' +
                    '</div>' +
                    '<p class="mt-2">Processing test payment...</p>'
                );

                // Manually approve the transaction
                $.ajax({
                    url: '/approve-test-payment',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        order_id: orderId
                    },
                    success: function(response) {
                        console.log('Test payment approved:', response);
                        if (response.success) {
                            handlePaymentSuccess();
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to approve test payment:', xhr);
                        showAlert('Failed to process test payment', 'danger');
                    }
                });
            });

            function handlePaymentSuccess() {
                // Payment successful
                clearInterval(paymentCheckInterval);

                // Update UI in modal
                $('#payment-status').html(
                    '<div class="alert alert-success">' +
                    '<i class="fas fa-check-circle fa-3x mb-3"></i>' +
                    '<h5>Payment Successful!</h5>' +
                    '<p>Your payment has been confirmed.</p>' +
                    '<p>Redirecting to confirmation page...</p>' +
                    '</div>'
                );

                // Show success notification
                showAlert('Payment confirmed successfully! Your order has been placed.', 'success');

                // Close modal and redirect after 2 seconds
                setTimeout(function() {
                    console.log('Hiding modal and redirecting...');
                    $('#khqrModal').modal('hide');
                    // Redirect to order confirmation
                    window.location.href = '/order-confirmation';
                }, 2000);
            }

            // Function to show alert notification
            function showAlert(message, type) {
                console.log('Showing alert:', type, message);
                // Remove any existing alerts
                $('.custom-alert').remove();

                // Create alert element
                const alertHtml =
                    '<div class="custom-alert alert alert-' + type + ' alert-dismissible fade show" role="alert" ' +
                    'style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">' +
                    '<strong>' + (type === 'success' ? 'Success!' : 'Error!') + '</strong> ' + message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>';

                // Append to body
                $('body').append(alertHtml);

                // Auto dismiss after 5 seconds
                setTimeout(function() {
                    $('.custom-alert').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 5000);
            }

            // Clear interval when modal is closed manually
            $('#khqrModal').on('hidden.bs.modal', function() {
                console.log('Modal closed, clearing interval');
                if (paymentCheckInterval) {
                    clearInterval(paymentCheckInterval);
                }
            });
        });
    </script>
@endpush
