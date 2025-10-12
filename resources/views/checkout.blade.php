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
        <div class="modal fade" id="khqrModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="khqrModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="khqrModalLabel">Scan KHQR to Pay</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div id="qrcode-container" class="mb-3">
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
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('show_khqr_modal') && session('khqr_data'))
                const khqrData = @json(session('khqr_data'));
                const md5 = '{{ session('khqr_md5') }}';
                const canvas = document.getElementById('qrcode');

                QRCode.toCanvas(canvas, khqrData.qr_string, {
                    width: 300
                }, function(error) {
                    if (error) console.error(error);
                });

                const modal = new bootstrap.Modal(document.getElementById('khqrModal'));
                modal.show();

                let checkInterval = setInterval(() => {
                    fetch('{{ route('cart.check.khqr.payment') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                md5
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                clearInterval(checkInterval);
                                document.getElementById('payment-status').innerHTML = `
                        <div class="alert alert-success">
                            <strong>✅ Payment Successful!</strong><br>Redirecting...
                        </div>`;
                                setTimeout(() => window.location.href = data.redirect, 2000);
                            }
                        })
                        .catch(err => console.error('Error checking KHQR:', err));
                }, 5000);

                document.getElementById('khqrModal').addEventListener('hidden.bs.modal', () => clearInterval(
                    checkInterval));
            @endif
        });
    </script>
@endpush
