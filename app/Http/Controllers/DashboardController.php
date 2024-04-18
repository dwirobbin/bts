<?php

namespace App\Http\Controllers;

use App\Models\{Order, Ticket, Complaint, Transaction};

class DashboardController extends Controller
{
    public function __invoke()
    {
        if (preg_match('[admin|owner|driver]', auth()->user()->role->name)) {
            return view('pages.dashboard', [
                'title' => 'Dashboard',
                'show_sidebar' => true,
                'tickets' => Ticket::query()->get(),
                'orders' => auth()->user()->role->name === 'owner'
                    ? Order::query()->whereHas(
                        'ticket',
                        fn ($q) => $q->whereHas(
                            'speedBoat',
                            fn ($q) => $q->whereOwnerId(auth()->id())
                        )
                    )
                    : Order::query()->get(),
                'transactions' => auth()->user()->role->name === 'owner'
                    ? Transaction::query()->whereStatus(false)->whereHas(
                        'order',
                        fn ($q) => $q->whereHas(
                            'ticket',
                            fn ($q) => $q->whereHas(
                                'speedBoat',
                                fn ($q) => $q->whereOwnerId(auth()->id())
                            )
                        )
                    )->get()
                    : Transaction::query()->whereStatus(false)->get(),
                'complaints' => Complaint::query()->whereSeen(false)
            ]);
        } else {
            $order = Order::query()->whereUserId(auth()->id());

            return view('pages.dashboard', [
                'title' => 'Dashboard',
                'show_sidebar' => true,
                'tickets' => Ticket::query()->get(),
                'orders' => $order->get(),
                'transactions' => Transaction::query()->whereStatus(false)->whereHas(
                    'order',
                    fn ($q) => $q->whereUserId(auth()->id())
                )->get(),
                'complaints' => Complaint::query()->whereHas(
                    'order',
                    fn ($q) => $q->whereUserId(auth()->id())
                )->whereOrderId($order->first()->id ?? null)
                    ->whereSeenForAdmin(false)
                    ->get(),
            ]);
        }
    }
}
