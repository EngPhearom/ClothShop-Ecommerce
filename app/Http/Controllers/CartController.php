<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;

class CartController extends Controller
{
    public function index()
    {
        $item = Cart::instance('cart')->content();
        return view('cart', compact('item'));
    }

    public function add_to_cart(Request $request)
    {
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)
            ->associate('App\Models\Product');

        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
        $item = Cart::instance('cart')->get($rowId);
        $qty = $item->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
        $item = Cart::instance('cart')->get($rowId);
        $qty = $item->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function clear_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function coupon_apply(Request $request)
    {
        $coupon_code = $request->coupon_code;
        if (isset($coupon_code)) {
            $coupons = Coupon::where('code', $coupon_code)->where('expiry_date', '>=', Carbon::today())
                ->where('cart_value', '<=', Cart::instance('cart')->subtotal())->first();
            if (!$coupons) {
                return redirect()->back()->with('error', 'Invalid coupon code');
            } else {
                Session::put('coupon', [
                    'code' => $coupons->code,
                    'type' => $coupons->type,
                    'value' => $coupons->value,
                    'cart_value' => $coupons->cart_value
                ]);
                $this->calculatorDiscount();
                return redirect()->back()->with('success', 'Coupon applied successfully');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid coupon code');
        }
    }

    public function calculatorDiscount()
    {
        $discount = 0;
        if (Session::has('coupon')) {
            if (Session::get('coupon')['type'] == 'fixed') {
                $discount = Session::get('coupon')['value'];
            } else {
                $discount = (Cart::instance('cart')->subtotal() * Session::get('coupon')['value']) / 100;
            }

            $subTotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDiscount = ($subTotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subTotalAfterDiscount + $taxAfterDiscount;

            Session::put('discount', [
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval($subTotalAfterDiscount), 2, '.', ''),
                'tax' => number_format(floatval($taxAfterDiscount), 2, '.', ''),
                'total' => number_format(floatval($totalAfterDiscount), 2, '.', '')
            ]);
        }
    }

    public function coupon_remove_code()
    {
        Session::forget('coupon');
        Session::forget('discount');
        return back()->with('success', 'Coupon has been deleted');
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $address = Address::where('user_id', Auth::user()->id)->where('isdefault', 1)->first();
        return view('checkout', compact('address'));
    }

    public function place_an_order(Request $request)
    {
        $user_id = Auth::user()->id;
        $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();

        if (!$address) {
            $request->validate([
                'name' => 'required|max:100',
                'phone' => 'required|numeric|digits_between:9,15',
                'zip' => 'required|numeric|digits:6',
                'state' => 'required',
                'city' => 'required',
                'address' => 'required',
                'locality' => 'required',
                'landmark' => 'required',
            ]);

            $address = new Address();
            $address->name = $request->name;
            $address->phone = $request->phone;
            $address->zip = $request->zip;
            $address->state = $request->state;
            $address->city = $request->city;
            $address->address = $request->address;
            $address->locality = $request->locality;
            $address->landmark = $request->landmark;
            $address->country = 'Cambodia';
            $address->user_id = $user_id;
            $address->isdefault = true;
            $address->save();
        }

        $this->setAmountforCheckout();

        $order = new Order();
        $order->user_id = $user_id;
        $order->subtotal = Session::get('checkout')['subtotal'];
        $order->discount = Session::get('checkout')['discount'];
        $order->tax = Session::get('checkout')['tax'];
        $order->total = Session::get('checkout')['total'];
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->city = $address->city;
        $order->state = $address->state;
        $order->country = $address->country;
        $order->landmark = $address->landmark;
        $order->zip = $address->zip;
        $order->save();

        foreach (Cart::instance('cart')->content() as $item) {
            $orderItem = new OrderItem();
            $orderItem->product_id = $item->id;
            $orderItem->order_id = $order->id;
            $orderItem->price = $item->price;
            $orderItem->quantity = $item->qty;
            $orderItem->save();
        }

        if ($request->mode == 'card') {
        } elseif ($request->mode == 'khqr') {
            $khqrData = $this->generateKHQR($order);

            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = 'pending';
            $transaction->save();

            Session::put('khqr_data', $khqrData);
            Session::put('khqr_order_id', $order->id);
            Session::put('khqr_md5', $khqrData['md5']);

            return redirect()->route('cart.checkout')->with('show_khqr_modal', true);
        } elseif ($request->mode == 'cod') {
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = 'pending';
            $transaction->save();

            $this->sendTelegramNotification($order, $address);

            Cart::instance('cart')->destroy();
            Session::forget('checkout');
            Session::forget('coupon');
            Session::forget('discount');
            Session::put('order_id', $order->id);
            return redirect()->route('cart.order.confirmation');
        }
    }

    public function generateKHQR($order)
    {
        try {
            // Convert USD to KHR (approximate rate: 1 USD = 4100 KHR)
            $amountInKHR = floatval(str_replace(',', '', $order->total)) * 4100;

            $individualInfo = new IndividualInfo(
                bakongAccountID: 'eng_phirom@aclb',
                merchantName: 'Eng Phirom',
                merchantCity: 'PHNOM PENH',
                currency: KHQRData::CURRENCY_KHR,
                amount: $amountInKHR
            );

            // Generate KHQR without API (offline generation)
            $khqrString = BakongKHQR::generateIndividual($individualInfo);

            // Log everything for debugging
            Log::info('KHQR Generation:', [
                'type' => gettype($khqrString),
                'length' => is_string($khqrString) ? strlen($khqrString) : 'not string',
                'value' => $khqrString,
                'amount' => $amountInKHR
            ]);

            // KHQR library returns the QR string directly
            $qrCodeData = is_string($khqrString) ? $khqrString : '';

            if (empty($qrCodeData)) {
                Log::error('Empty QR code data generated');
                // Use a test QR code for debugging
                $qrCodeData = "00020101021229190015eng_phirom@aclb52045999530311654031005802KH5910Eng Phirom6010PHNOM PENH9917001317602445102106304BF70";
            }

            return [
                'qr_string' => $qrCodeData,
                'md5' => md5($qrCodeData),
                'amount' => $amountInKHR
            ];
        } catch (\Exception $e) {
            Log::error('KHQR Generation Failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Return test data for debugging
            $testQR = "00020101021229190015eng_phirom@aclb52045999530311654031005802KH5910Eng Phirom6010PHNOM PENH9917001317602445102106304BF70";

            return [
                'qr_string' => $testQR,
                'md5' => md5($testQR),
                'amount' => 0
            ];
        }
    }


    public function checkKHQRPaymentStatus(Request $request)
    {
        $md5 = $request->input('md5');

        try {
            $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiMjIwMWU1MzM1YzI5NGU4NSJ9LCJpYXQiOjE3NjAxNTU5MzYsImV4cCI6MTc2NzkzMTkzNn0.zrC8oOpgB0T8HR9pwSPdT3_DNer1uI_GRD2hpVPoTPE';
            $bakongKhqr = new BakongKHQR($token);
            $response = $bakongKhqr->checkTransactionByMD5($md5);

            Log::info('KHQR Payment Check Response:', [
                'type' => gettype($response),
                'response' => $response
            ]);

            $isSuccess = false;

            if (is_object($response)) {
                $isSuccess = (
                    (isset($response->responseCode) && $response->responseCode === 0) ||
                    (isset($response->response_code) && $response->response_code === 0) ||
                    (isset($response->status) && $response->status === 'success') ||
                    (isset($response->data) && !empty($response->data))
                );
            } elseif (is_array($response)) {
                $isSuccess = (
                    (isset($response['responseCode']) && $response['responseCode'] === 0) ||
                    (isset($response['response_code']) && $response['response_code'] === 0) ||
                    (isset($response['status']) && $response['status'] === 'success') ||
                    (isset($response['data']) && !empty($response['data']))
                );
            }

            if ($isSuccess) {
                $orderId = Session::get('khqr_order_id');
                $order = Order::find($orderId);

                if (!$order) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order not found'
                    ]);
                }

                $address = Address::where('user_id', $order->user_id)->where('isdefault', true)->first();

                $transaction = Transaction::where('order_id', $orderId)->first();
                if ($transaction) {
                    $transaction->status = 'approved';
                    $transaction->save();
                }

                $this->sendTelegramNotification($order, $address, 'KHQR');

                Cart::instance('cart')->destroy();
                Session::forget('checkout');
                Session::forget('coupon');
                Session::forget('discount');
                Session::forget('khqr_data');
                Session::forget('khqr_order_id');
                Session::forget('khqr_md5');
                Session::put('order_id', $orderId);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed',
                    'redirect' => route('cart.order.confirmation')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment not yet confirmed'
            ]);
        } catch (\Exception $e) {
            Log::error('KHQR payment check failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error checking payment status: ' . $e->getMessage()
            ]);
        }
    }

    private function sendTelegramNotification($order, $address, $paymentMethod = 'COD')
    {
        $orderItem = "";
        foreach ($order->orderItem as $item) {
            $orderItem .= "• {$item->product->name} x {$item->quantity} - \${$item->price}\n";
        }

        $customer_info = "📦 <b>Order #" . $order->id . "</b>\n\n";
        $customer_info .= "👤 <b>Customer Details:</b>\n";
        $customer_info .= "Name: <b>{$address->name}</b>\n";
        $customer_info .= "Phone: <b>{$address->phone}</b>\n";
        $customer_info .= "Email: <b>" . Auth::user()->email . "</b>\n\n";

        $customer_info .= "📍 <b>Shipping Address:</b>\n";
        $customer_info .= "<b>{$address->address}</b>\n";
        $customer_info .= "{$address->locality}, {$address->landmark}\n";
        $customer_info .= "{$address->city}, {$address->state}\n";
        $customer_info .= "{$address->country} - {$address->zip}\n\n";

        $customer_info .= "🛍️ <b>Order Items:</b>\n";
        $customer_info .= $orderItem . "\n";

        $customer_info .= "💰 <b>Order Summary:</b>\n";
        $customer_info .= "Subtotal: \${$order->subtotal}\n";
        $customer_info .= "Discount: \${$order->discount}\n";
        $customer_info .= "Tax: \${$order->tax}\n";
        $customer_info .= "Total: <b>\${$order->total}</b>\n\n";

        $paymentMethodText = $paymentMethod === 'KHQR' ? '💳 <b>Payment Method:</b> KHQR (Paid ✅)' : '💳 <b>Payment Method:</b> Cash on Delivery';
        $customer_info .= $paymentMethodText . "\n";
        $customer_info .= "📅 <b>Order Date:</b> " . $order->created_at->format('d M Y, h:i A');

        $token = "7798227033:AAEdag1xP4p3JvDbdOgdPdavhxd6EPFabIg";

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("https://api.telegram.org/bot{$token}/sendMessage", [
                "text" => "🔔 <b>New Order</b>\n\n" . $customer_info,
                "parse_mode" => "HTML",
                "disable_web_page_preview" => false,
                "disable_notification" => false,
                "chat_id" => "@rom_notification"
            ]);

            Log::info('Telegram notification sent', ['response' => $response->json()]);
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification: ' . $e->getMessage());
        }
    }

    public function setAmountforCheckout()
    {
        if (!Cart::instance('cart')->content()->count() > 0) {
            Session::forget('checkout');
            return;
        }

        if (Session::has('coupon')) {
            Session::put('checkout', [
                'discount' => Session::get('discount')['discount'],
                'subtotal' => Session::get('discount')['subtotal'],
                'tax' => Session::get('discount')['tax'],
                'total' => Session::get('discount')['total'],
            ]);
        } else {
            Session::put('checkout', [
                'discount' => 0,
                'subtotal' => Cart::instance('cart')->subtotal(),
                'tax' => Cart::instance('cart')->tax(),
                'total' => Cart::instance('cart')->total(),
            ]);
        }
    }

    public function order_confirmation()
    {
        if (Session::has('order_id')) {
            $order = Order::find(Session::get('order_id'));
            return view('order-confirmation', compact('order'));
        }
        return redirect()->route('cart.index');
    }
}
