@extends('layouts.app')
@section('content')
    <style>
        .table> :not(caption)>tr>th {
            padding: 0.625rem 1.5rem .625rem !important;
            background-color: #6a6e51 !important;
        }

        .table>tr>td {
            padding: 0.625rem 1.5rem .625rem !important;
        }

        .table-bordered> :not(caption)>tr>th,
        .table-bordered> :not(caption)>tr>td {
            border-width: 1px 1px;
            border-color: #6a6e51;
        }

        .table> :not(caption)>tr>td {
            padding: .8rem 1rem !important;
        }

        .bg-success {
            background-color: #40c710 !important;
        }

        .bg-danger {
            background-color: #f44032 !important;
        }

        .bg-warning {
            background-color: #f5d700 !important;
            color: #000;
        }

        .table-transaction>tbody>tr:nth-of-type(odd) {
            --bs-table-accent-bg: #fff !important;
        }
    </style>

    <main class="pt-90" style="padding-top: 0px;">
        <section class="my-account container">
            <h2 class="page-title">Order Details</h2>
            <div class="row">
                <div class="col-lg-2">
                    @include('user.account-nav')
                </div>

                <div class="col-lg-10">
                    <div class="wg-box">
                        <div class="flex items-center justify-between gap10 flex-wrap mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <h5>Ordered Details</h5>
                                </div>
                                <div class="col-6 text-right">
                                    <a class="btn btn-sm btn-dark text-white rounded-3"
                                        href="{{ route('user.order') }}">Back</a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            @if (Session::has('status'))
                                <p class="alert alert-success">{{ Session::get('status') }}</p>
                            @endif
                            <table class="table table-bordered table-striped table-transaction">
                                <tr>
                                    <th>Order No</th>
                                    <td>{{ $orders->id }}</td>
                                    <th>Mobile</th>
                                    <td>{{ $orders->phone }}</td>
                                    <th>Zip Code</th>
                                    <td>{{ $orders->zip }}</td>
                                </tr>
                                <tr>
                                    <th>Order Date</th>
                                    <td>{{ $orders->created_at }}</td>
                                    <th>Delivered Date</th>
                                    <td>{{ $orders->delivered_date }}</td>
                                    <th>Canceled Date</th>
                                    <td>{{ $orders->canceled_date }}</td>
                                </tr>
                                <tr>
                                    <th>Order Status</th>
                                    <td colspan="5">
                                        @if ($orders->status == 'delivered')
                                            <span class="badge bg-success">Delivered</span>
                                        @elseif($orders->status == 'canceled')
                                            <span class="badge bg-danger">Canceled</span>
                                        @else
                                            <span class="badge bg-warning">Ordered</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="wg-box">
                            <div class="flex items-center justify-between gap10 flex-wrap">
                                <div class="wg-filter flex-grow">
                                    <h5>Ordered Items</h5>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">SKU</th>
                                            <th class="text-center">Category</th>
                                            <th class="text-center">Brand</th>
                                            <th class="text-center">Options</th>
                                            <th class="text-center">Return Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orderItem as $item)
                                            <tr>
                                                <td class="pname">
                                                    <div class="image">
                                                        <img src="{{ asset('uploads/products/thumbnails') }}/{{ $item->product->image }}"
                                                            alt="{{ $item->product->name }}" class="image">
                                                    </div>
                                                    <div class="name">
                                                        <a href="{{ route('shop.product.details', $item->product->slug) }}"
                                                            target="_blank"
                                                            class="body-title-2">{{ $item->product->name }}</a>
                                                    </div>
                                                </td>
                                                <td class="text-center">${{ $item->price }}</td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-center">{{ $item->product->SKU }}</td>
                                                <td class="text-center">{{ $item->product->category->name }}</td>
                                                <td class="text-center">{{ $item->product->brand->name }}</td>
                                                <td class="text-center">{{ $item->options }}</td>
                                                <td class="text-center">{{ $item->rstatus == 0 ? 'No' : 'Yes' }}</td>
                                                <td class="text-center">
                                                    <div class="list-icon-function view-icon">
                                                        <div class="item eye">
                                                            <i class="icon-eye"></i>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="divider"></div>
                            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">

                            </div>
                        </div>

                        <div class="wg-box mt-5">
                            <h5>Shipping Address</h5>
                            <div class="my-account__address-item col-md-6">
                                <div class="my-account__address-item__detail">
                                    <p>{{ $orders->name }}</p>
                                    <p>{{ $orders->address }}</p>
                                    <p>{{ $orders->locality }}</p>
                                    <p>{{ $orders->city }}, {{ $orders->country }}</p>
                                    <p>{{ $orders->landmark }}</p>
                                    <p>{{ $orders->zip }}</p>
                                    <br>
                                    <p>Mobile : {{ $orders->phone }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="wg-box mt-5">
                            <h5>Transactions</h5>
                            <table class="table table-striped table-bordered table-transaction">
                                <tbody>
                                    <tr>
                                        <th>Subtotal</th>
                                        <td>{{ $orders->subtotal }}</td>
                                        <th>Tax</th>
                                        <td>{{ $orders->tax }}</td>
                                        <th>Discount</th>
                                        <td>{{ $orders->discount }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total</th>
                                        <td>{{ $orders->total }}</td>
                                        <th>Payment Mode</th>
                                        <td>{{ $transaction->mode }}</td>
                                        <th>Status</th>
                                        <td>
                                            @if ($transaction->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($transaction->status == 'declinded')
                                                <span class="badge bg-danger">Declinded</span>
                                            @elseif($transaction->status == 'refunded')
                                                <span class="badge bg-success">Refunded</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @if ($orders->status == 'ordered')
                            <div class="wg-box mt-5 text-right">
                                <form action="{{ route('user.order.cancel') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $orders->id }}">
                                    <button type="submit" class="btn btn-danger cancel-order">Cancel Order</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.cancel-order').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                swal({
                    title: 'Are you sure?',
                    text: "You want to cancel this order?",
                    icon: 'warning',
                    buttons: [
                        'No',
                        'Yes'
                    ],
                    confirmButtonColor: '#3085d6',
                }).then(function(result) {
                    if (result) {
                        form.submit();
                    }
                });
            });
        })
    </script>
@endpush
