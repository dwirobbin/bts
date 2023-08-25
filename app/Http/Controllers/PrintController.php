<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function __invoke(Request $request)
    {
        $order = Order::where('order_code', $request['order'])->first();

        if (preg_match('[admin|owner]', auth()->user()->role->name)) {
            return view('pages.orders.print', [
                'order' => $order,
                'title' => 'Print',
                'show_sidebar' => false,
            ]);
        } else {
            $transactionCompleted = Order::whereOrderCode($request['order'])->first()->transaction->status;
            if ($transactionCompleted) {
                if (auth()->user()->id != $order->user->id) {
                    return redirect()->to('orders/history/index');
                }

                return view('pages.orders.print', [
                    'order' => $order,
                    'title' => 'Print',
                    'show_sidebar' => false,
                ]);
            } else {
                return redirect('/orders')->withError('Dimohon untuk membayar tiket terlebih dahulu!');
            }
        }
    }
}
