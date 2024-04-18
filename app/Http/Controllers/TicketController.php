<?php

namespace App\Http\Controllers;

use App\Models\{Street, Ticket, SpeedBoat};
use App\Http\Requests\TicketRequest;
use Carbon\Carbon;
use Illuminate\Http\{Request, Response};
use Yajra\DataTables\DataTables;

class TicketController extends Controller
{
    public function index()
    {
        return view('pages.tickets.index', [
            'title' => 'Daftar Tiket',
            'show_sidebar' => true,
        ]);
    }

    public function getData()
    {
        if (request()->ajax()) {
            $query = Ticket::query()
                ->select('id', 'speedboat_id', 'street_id', 'hours_of_departure', 'price', 'stock')
                ->with(['speedBoat:id,name', 'street:id,from_route,to_route']);

            if (auth()->user()->role->name === 'owner') {
                $query->whereHas('speedBoat', fn ($q) => $q->whereOwnerId(auth()->id()));
            }

            return DataTables::of($query->orderByDesc('updated_at')->get())
                ->addIndexColumn()
                ->editColumn(
                    'hours_of_departure',
                    fn ($row) => Carbon::parse($row->hours_of_departure)->format('H:i') . ' WIB'
                )
                ->editColumn(
                    'price',
                    fn ($row) => !is_null($row->price)
                        ? 'Rp. ' . number_format($row->price, 0, ',', '.')
                        : 'Belum di set'
                )
                ->editColumn(
                    'stock',
                    fn ($row) => !is_null($row->stock) && $row->stock > 0
                        ? $row->stock . ' Tiket'
                        : '<span class="badge badge-danger">Habis</span>'
                )
                ->addColumn('action', function ($row) {
                    if (!preg_match('[customer|driver]', auth()->user()->role->name)) {
                        $btnAction = "<button type='button' id='edit-ticket' data-id='{$row->id}' class='btn btn-xs btn-warning'>Edit</button>";
                        $btnAction .= "<button type='button' id='delete-ticket' data-id='$row->id' class='btn btn-xs btn-danger ml-1'>Hapus</button>";
                    }

                    return $btnAction ?? '';
                })
                ->rawColumns(['hours_of_departure', 'price', 'stock', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        if (request()->ajax()) {

            $speedBoatQuery = SpeedBoat::query();

            if (auth()->user()->role->name === 'owner') {
                $speedBoatQuery->whereOwnerId(auth()->id());
            }

            $streets = Street::query()
                ->select('id', 'from_route', 'to_route')
                ->get();

            return response()->json(
                [
                    'speed_boats' => $speedBoatQuery->get(),
                    'streets'  => $streets,
                ],
                Response::HTTP_OK
            );
        }
    }

    public function store(TicketRequest $request)
    {
        if (request()->ajax()) {
            $validatedData = $request->validated();

            try {
                Ticket::query()->create($validatedData);

                return response()->json(
                    ['success' => 'Tiket berhasil ditambahkan.'],
                    Response::HTTP_OK
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['errors' => 'Terjadi suatu kesalahan.'],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }

    public function edit(Ticket $ticket)
    {
        return request()->ajax()
            ? response()->json([
                'ticket'    => $ticket,
                'speed_boats'  => SpeedBoat::query()->select('id', 'name')->get(),
                'streets'   => Street::query()->select('id', 'from_route', 'to_route')->get(),
            ])
            : response()->noContent();
    }

    public function update(TicketRequest $request, Ticket $ticket)
    {
        if (request()->ajax()) {
            try {
                $ticket->update($request->only(['price', 'hours_of_departure', 'stock']));

                return response()->json(
                    ['success' => 'Tiket berhasil diperbaharui.'],
                    Response::HTTP_OK
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['errors' => 'Terjadi suatu kesalahan.'],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }

    public function destroy(Ticket $ticket)
    {
        if (request()->ajax()) {
            try {
                $ticket->delete();

                return response()->json(
                    ['success' => 'Berhasil dihapus.'],
                    Response::HTTP_OK
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['error' => "Error: Tiket sedang digunakan."],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }

    public function checkPrice(Request $request)
    {
        if (is_null($request['ticket_id'])) {
            return response()->json(
                ['txt' => 'Pilih tiket terlebih dahulu!'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } else {
            $ticketId = $request['ticket_id'];
        }

        if ($request['trip_type'] === 'undefined') {
            return response()->json(
                ['txt' => 'Pilih jenis perjalanan terlebih dahulu!'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } else {
            $typeOfTrip = $request['trip_type'];
        }

        $ticketSelected = Ticket::query()
            ->select('id', 'price', 'street_id')
            ->with('street:id,from_route,to_route')
            ->whereId($ticketId)
            ->first();

        if ($typeOfTrip === 'Pergi' && (is_null($ticketSelected->price) || $ticketSelected->price === 0)) {
            return response()->json([
                'txt' => 'Maaf, tiket berangkat tidak tersedia. Mohon pilih tiket yang lain!'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($typeOfTrip === 'Pulang-Pergi') {
            $ticketBackTrip = Ticket::query()
                ->with('street:id,from_route,to_route')
                ->whereHas('street', function ($q) use ($ticketSelected) {
                    $q->whereFromRoute($ticketSelected->street->to_route)
                        ->whereToRoute($ticketSelected->street->from_route);
                })->first();

            if (
                is_null($ticketBackTrip->price) || $ticketBackTrip->price === 0 ||
                is_null($ticketSelected->price) || $ticketSelected->price === 0
            ) {
                return response()->json([
                    'txt' => 'Maaf, tiket pulang-pergi tidak tersedia. Mohon pilih tiket yang lain!'
                ], Response::HTTP_NOT_FOUND);
            }

            $priceGoBack = ($ticketSelected->price + $ticketBackTrip->price) * $request['total_passenger'];
        }

        $price = match ($typeOfTrip) {
            'Pergi' => $ticketSelected->price * $request['total_passenger'],
            'Pulang-Pergi' => $priceGoBack,
        };

        return response()->json(['txt' => $price], Response::HTTP_OK);
    }
}
