<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\Complaint;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        if (preg_match('[admin|owner|driver]', auth()->user()->role->name)) {
            return view('pages.dashboard', [
                'title' => 'Dashboard',
                'show_sidebar' => true,
                'tickets' => Ticket::all(),
                'orders' => auth()->user()->role->name === 'owner'
                    ? Order::whereHas(
                        'ticket',
                        fn ($q) => $q->whereHas(
                            'airline',
                            fn ($q) => $q->whereOwnerId(auth()->user()->id)
                        )
                    )
                    : Order::all(),
                'transactions' => auth()->user()->role->name === 'owner'
                    ? Transaction::whereStatus(false)->whereHas(
                        'order',
                        fn ($q) => $q->whereHas(
                            'ticket',
                            fn ($q) => $q->whereHas(
                                'airline',
                                fn ($q) => $q->whereOwnerId(auth()->user()->id)
                            )
                        )
                    )->get()
                    : Transaction::whereStatus(false)->get(),
                'complaints' => Complaint::whereSeen(false)
            ]);
        } else {
            $order = Order::whereUserId(auth()->user()->id);

            return view('pages.dashboard', [
                'title' => 'Dashboard',
                'show_sidebar' => true,
                'tickets' => Ticket::all(),
                'orders' => $order->get(),
                'transactions' => Transaction::whereStatus(false)->whereHas(
                    'order',
                    fn ($q) => $q->whereUserId(Auth::id())
                )->get(),
                'complaints' => Complaint::whereHas(
                    'order',
                    fn ($q) => $q->whereUserId(auth()->user()->id)
                )->whereOrderId($order->first()->id ?? null)
                    ->whereSeenForAdmin(false)
                    ->get(),
            ]);
        }
    }
}
