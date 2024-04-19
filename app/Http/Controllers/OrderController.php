<?php

namespace App\Http\Controllers;

use App\Models\{Order, Ticket, Passenger, Transaction, PaymentMethod};
use App\Http\Requests\OrderRequest;
use Carbon\Carbon;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\{DB, Storage};

class OrderController extends Controller
{
    public function index()
    {
        $query = Order::query()
            ->with([
                'user:id,name',
                'ticket' => fn ($query) => $query->with([
                    'speedBoat:id,name', 'street:id,from_route,to_route'
                ]),
                'complaints'
            ]);

        if (auth()->user()->role->name === 'owner') {
            $query->whereHas('ticket', fn ($q) => $q->whereHas(
                'speedBoat',
                fn ($q) => $q->whereOwnerId(auth()->id())
            ));
        }

        if (auth()->user()->role->name == 'customer') {
            $query->whereUserId(auth()->id());
        }

        return view('pages.orders.index', [
            'title'         => 'Pesanan',
            'show_sidebar'  => true,
            'orders'        => $query->orderByDesc('updated_at')->get()
        ]);
    }

    public function getPaymentMethods(Request $request)
    {
        $speedBoatId = Ticket::query()->find($request->ticket_id, ['speedboat_id'])->speedboat_id;

        $paymentMethod = PaymentMethod::query()->whereHas(
            'createdBy',
            fn ($q) => $q->whereHas(
                'speedBoat',
                fn ($q) => $q->whereId($speedBoatId)
            )
        )->pluck('method', 'id');

        return response()->json($paymentMethod, Response::HTTP_OK);
    }

    public function create()
    {
        $tickets = Ticket::query()
            ->select('id', 'speedboat_id', 'street_id', 'stock', 'price')
            ->with(['speedBoat:id,name', 'street:id,from_route,to_route'])
            ->where('stock', '!=', 0)
            ->get();

        return view('pages.orders.create', [
            'title' => 'Buat Pesanan',
            'show_sidebar' => true,
            'tickets' => $tickets,
        ]);
    }

    public function store(OrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $order = Order::query()->create([
                'user_id'       => auth()->id(),
                'order_code'    => strval(number_format(microtime(true) * 1000, 0, '.', '')),
                'ticket_id'     => $request['ticket_data']['ticket_id'],
                'trip_type'     => $request['ticket_data']['trip_type'],
                'go_date'       => Carbon::createFromFormat('d/m/Y', $request['ticket_data']['go_date'])->format('Y-m-d'),
                'back_date'     => !is_null($request['ticket_data']['back_date']) ? Carbon::createFromFormat('d/m/Y', $request['ticket_data']['back_date'])->format('Y-m-d') : null,
                // 'go_date'       => Carbon::createFromTimestamp(strtotime($request['ticket_data']['go_date']))->format('Y-m-d'),
                // 'back_date'     => !is_null($request['ticket_data']['back_date']) ? Carbon::createFromTimestamp(strtotime($request['ticket_data']['back_date']))->format('Y-m-d') : null,
                'amount'        => count($request['passenger_data']['name']),
            ]);

            for ($i = 0; $i < count($request['passenger_data']['name']); $i++) {
                $passengerData[] = [
                    'order_id' => $order->id,
                    'name' => str($request['passenger_data']['name'][$i])->title(),
                    'id_number' => $request['passenger_data']['ktp_number'][$i],
                    'gender' => $request['passenger_data']['gender'][$i],
                ];
            }

            Passenger::query()->insert($passengerData);

            Transaction::query()->create([
                'order_id' => $order->id,
                'paymentmethod_id' => $request['payment_data']['paymentmethod_id'],
                'name_account' => str($request['payment_data']['senderaccount_name'])->title(),
                'from_account' => str($request['payment_data']['senderaccount_number'])->title(),
                'total' => (int) str_replace('.', '', $request['payment_data']['total_price']),
                'status' => false,
            ]);

            Ticket::query()
                ->whereId($request['ticket_data']['ticket_id'])
                ->decrement('stock', count($request['passenger_data']['name']));

            DB::commit();

            return redirect()->to('dashboard/orders/history/index')->withSuccess('Pesanan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->withError('Terjadi Suatu Kesalahan')->withInput();
        }
    }

    public function destroy(Order $order)
    {
        if (request()->ajax()) {
            try {
                $filePath = 'public/images/' . $order->transaction->image;

                if (Storage::exists($filePath)) Storage::delete($filePath);

                $order->transaction()->delete();
                $order->passengers()->delete();
                $order->delete();

                return response()->json(
                    ['success' => 'Berhasil dihapus.'],
                    Response::HTTP_OK
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['error' => 'Terjadi suatu kesalahan.'],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }
}
