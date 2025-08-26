<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function index()
    {
        return view('user.index');
    }

    public function order()
    {
        $order = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(12);
        return view('user.order', compact('order'));
    }

    public function order_detail($order_id)
    {
        $orders = Order::where('user_id', Auth::user()->id)->where('id', $order_id)->first();
        if ($orders) {
            $orderItem = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
            $transaction = Transaction::where('order_id', $order_id)->first();
            return view('user.order-detail', compact('orders', 'orderItem', 'transaction'));
        } else {
            return redirect()->route('login');
        }
    }

    public function order_cancel(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = 'canceled';
        $order->canceled_date = Carbon::now();
        $order->save();
        return back()->with('status', 'Order canceled has been successfully');
    }
}
