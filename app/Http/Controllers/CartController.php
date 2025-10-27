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
            // Card payment logic here
        } elseif ($request->mode == 'khqr') {
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = 'pending';
            $transaction->save();

            // Generate KHQR for this order
            Session::put('pending_order_id', $order->id);
            return response()->json([
                'success' => true,
                'order_id' => $order->id
            ]);

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

    public function generateKHQR($orderId)
    {
        $order = Order::findOrFail($orderId);
        $total = floatval(str_replace(',', '', $order->total));

        $individualInfo = new IndividualInfo(
            bakongAccountID: 'eng_phirom@aclb',
            merchantName: 'Eng Phirom',
            merchantCity: 'PHNOM PENH',
            currency: KHQRData::CURRENCY_USD,
            amount: $total
        );

        $response = BakongKHQR::generateIndividual($individualInfo);
        return response()->json($response);
    }

    public function checkKHQRPayment(Request $request)
    {
        $md5 = $request->md5;
        $orderId = $request->order_id;

        try {
            $bakongKhqr = new BakongKHQR('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiMjIwMWU1MzM1YzI5NGU4NSJ9LCJpYXQiOjE3NjE1NTE5OTIsImV4cCI6MTc2OTMyNzk5Mn0.Brg5cGWprDH00kvyINX4LX_eudud_ghwF81Qh_Dcgow');
            $response = $bakongKhqr->checkTransactionByMD5($md5);

            Log::info('KHQR Payment Check Response:', ['response' => $response, 'md5' => $md5, 'order_id' => $orderId]);

            // Check if payment is successful
            // The response structure might vary, so let's check multiple possible formats
            $isPaid = false;

            if (isset($response['data']['status']) && strtoupper($response['data']['status']) === 'PAID') {
                $isPaid = true;
            } elseif (isset($response['status']) && strtoupper($response['status']) === 'PAID') {
                $isPaid = true;
            } elseif (isset($response['data']['responseCode']) && $response['data']['responseCode'] === '00') {
                $isPaid = true;
            }

            if ($isPaid) {
                $order = Order::findOrFail($orderId);
                $transaction = Transaction::where('order_id', $orderId)->first();

                if ($transaction && $transaction->status !== 'approved') {
                    // Update transaction status
                    $transaction->status = 'approved';
                    $transaction->save();

                    Log::info('Transaction updated to approved:', ['transaction_id' => $transaction->id]);

                    // Get address
                    $address = Address::where('user_id', $order->user_id)->where('isdefault', 1)->first();

                    // Send Telegram notification
                    Log::info('Sending Telegram notification for KHQR payment');
                    $this->sendTelegramNotification($order, $address, 'KHQR');

                    // Clear cart and session
                    Cart::instance('cart')->destroy();
                    Session::forget('checkout');
                    Session::forget('coupon');
                    Session::forget('discount');
                    Session::forget('pending_order_id');
                    Session::put('order_id', $order->id);

                    Log::info('Payment process completed successfully');

                    return response()->json([
                        'success' => true,
                        'paid' => true,
                        'message' => 'Payment confirmed successfully'
                    ]);
                } else {
                    Log::info('Transaction already approved or not found');
                    return response()->json([
                        'success' => true,
                        'paid' => true,
                        'message' => 'Payment already processed'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'paid' => false,
                'message' => 'Payment pending'
            ]);

        } catch (\Exception $e) {
            Log::error('KHQR payment check failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    private function sendTelegramNotification($order, $address, $paymentMethod = 'COD')
    {
        Log::info('Starting Telegram notification', ['order_id' => $order->id, 'payment_method' => $paymentMethod]);

        try {
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
            $chatId = "@rom_notification";

            $telegramData = [
                "text" => "🔔 <b>New Order</b>\n\n" . $customer_info,
                "parse_mode" => "HTML",
                "disable_web_page_preview" => false,
                "disable_notification" => false,
                "chat_id" => $chatId
            ];

            Log::info('Sending to Telegram', ['chat_id' => $chatId, 'order_id' => $order->id]);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("https://api.telegram.org/bot{$token}/sendMessage", $telegramData);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['ok']) && $responseData['ok'] === true) {
                Log::info('Telegram notification sent successfully', [
                    'order_id' => $order->id,
                    'response' => $responseData
                ]);
            } else {
                Log::error('Telegram notification failed', [
                    'order_id' => $order->id,
                    'status' => $response->status(),
                    'response' => $responseData
                ]);
            }

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
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
