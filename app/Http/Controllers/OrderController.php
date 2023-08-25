<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Passenger;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index()
    {
        $query = Order::query()->with([
            'user:id,name',
            'ticket' => fn ($query) => $query->with([
                'airline:id,name', 'street:id,from_route,to_route'
            ]),
            'complaints',
        ]);

        if (auth()->user()->role->name === 'owner') {
            $query->whereHas('ticket', fn ($q) => $q->whereHas(
                'airline',
                fn ($q) => $q->whereOwnerId(auth()->user()->id)
            ));
        }

        if (auth()->user()->role->name == 'customer') {
            $query->whereUserId(auth()->user()->id);
        }

        return view('pages.orders.index', [
            'title'         => 'Pesanan',
            'show_sidebar'  => true,
            'orders'        => $query->orderByDesc('updated_at')->get()
        ]);
    }

    public function getPaymentMethods(Request $request)
    {
        $airlineId = Ticket::find($request->ticket_id, ['airline_id'])->airline_id;

        $paymentMethod = PaymentMethod::whereHas(
            'createdBy',
            fn ($q) => $q->whereHas(
                'airline',
                fn ($q) => $q->whereId($airlineId)
            )
        )->pluck('method', 'id');

        return response()->json($paymentMethod);
    }

    public function create()
    {
        $tickets = Ticket::select('id', 'airline_id', 'street_id', 'stock')
            ->with(['airline:id,name', 'street:id,from_route,to_route'])
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
        $request->validated();

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id'       => auth()->user()->id,
                'order_code'    => strval(number_format(microtime(true) * 1000, 0, '.', '')),
                'ticket_id'     => $request['ticket_data']['ticket_id'],
                'trip_type'     => $request['ticket_data']['trip_type'],
                'go_date'       => Carbon::createFromTimestamp(strtotime($request['ticket_data']['go_date']))->format('Y-m-d'),
                'back_date'     => !is_null($request['ticket_data']['back_date']) ? Carbon::createFromTimestamp(strtotime($request['ticket_data']['back_date']))->format('Y-m-d') : null,
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

            Passenger::insert($passengerData);

            Transaction::create([
                'order_id' => $order->id,
                'paymentmethod_id' => $request['payment_data']['paymentmethod_id'],
                'name_account' => str($request['payment_data']['senderaccount_name'])->title(),
                'from_account' => str($request['payment_data']['senderaccount_number'])->title(),
                'total' => (int) $request['payment_data']['total_price'],
                'status' => false,
            ]);

            Ticket::whereId($request['ticket_data']['ticket_id'])
                ->decrement('stock', count($request['passenger_data']['name']));

            DB::commit();

            return redirect()->to('dashboard/orders/history/index')->withSuccess('Pesanan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->withError($e->getMessage())->withInput();
        }
    }

    public function destroy(Order $order)
    {
        if (request()->ajax()) {
            try {
                $filePath = 'public/images/' . $order->transaction->image;

                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }

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
