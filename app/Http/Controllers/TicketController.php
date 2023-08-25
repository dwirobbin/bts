<?php

namespace App\Http\Controllers;

use App\Models\Street;
use App\Models\Ticket;
use App\Models\Airline;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use App\Http\Requests\TicketRequest;
use Carbon\Carbon;

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
            $query = Ticket::select('id', 'airline_id', 'street_id', 'hours_of_departure', 'price', 'stock')
                ->with(['airline:id,name', 'street:id,from_route,to_route']);

            if (auth()->user()->role->name === 'owner') {
                $query->whereHas('airline', fn ($q) => $q->whereOwnerId(auth()->user()->id));
            }

            return DataTables::of($query->orderByDesc('updated_at')->get())
                ->addIndexColumn()
                ->editColumn(
                    'price',
                    fn ($row) => !is_null($row->price)
                        ? 'Rp. ' . number_format($row->price, 2, ',', '.')
                        : 'Belum di set'
                )
                ->editColumn(
                    'hours_of_departure',
                    fn ($row) => Carbon::parse($row->hours_of_departure)->format('H:i') . ' WIB'
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
                ->rawColumns(['price', 'stock', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        if (request()->ajax()) {
            $airlineQuery = Airline::query();

            if (auth()->user()->role->name === 'owner') {
                $airlineQuery->whereOwnerId(auth()->user()->id);
            }

            return response()->json(
                [
                    'airlines' => $airlineQuery->get(),
                    'streets'  => Street::select('id', 'from_route', 'to_route')->get(),
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
                Ticket::create($validatedData);

                return response()->json(
                    ['success' => 'Tiket berhasil ditambahkan.'],
                    Response::HTTP_OK
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['errors' => 'Terjadi suatu kesalahan.']
                );
            }
        }
    }

    public function edit(Ticket $ticket)
    {
        if (request()->ajax()) {
            return response()->json([
                'ticket'    => $ticket,
                'airlines'  => Airline::select('id', 'name')->get(),
                'streets'   => Street::select('id', 'from_route', 'to_route')->get(),
            ]);
        }
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
            return response()->json(['txt' => 'Pilih tiket terlebih dahulu!']);
        } else {
            $ticketId = $request['ticket_id'];
        }

        if ($request['trip_type'] === 'undefined') {
            return response()->json(['txt' => 'Pilih jenis perjalanan terlebih dahulu!']);
        } else {
            $typeOfTrip = $request['trip_type'];
        }

        $ticketSelected = Ticket::select('id', 'price', 'street_id')
            ->with('street:id,from_route,to_route')
            ->whereId($ticketId)
            ->first();

        if ($typeOfTrip === 'Pergi' && (is_null($ticketSelected->price) || $ticketSelected->price === 0)) {
            return response()->json([
                'txt' => 'Maaf, tiket berangkat tidak tersedia. Mohon pilih tiket yang lain!'
            ]);
        }

        if ($typeOfTrip === 'Pulang-Pergi') {
            $ticketBackTrip = Ticket::with('street:id,from_route,to_route')
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
                ]);
            }

            $priceGoBack = ($ticketSelected->price + $ticketBackTrip->price) * $request['total_passenger'];
        }

        $price = match ($typeOfTrip) {
            'Pergi' => $ticketSelected->price * $request['total_passenger'],
            'Pulang-Pergi' => $priceGoBack,
        };

        return response()->json(['txt' => $price]);
    }
}
