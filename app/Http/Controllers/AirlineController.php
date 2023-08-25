<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Airline;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use App\Http\Requests\AirlineRequest;

class AirlineController extends Controller
{
    public function index()
    {
        return view('pages.airlines.index', [
            'title' => 'SpeedBoat',
            'show_sidebar' => true,
        ]);
    }

    public function getData()
    {
        if (request()->ajax()) {
            $data = Airline::select('id', 'name', 'owner_id', 'status')
                ->with('owner:id,name')
                ->when(auth()->user()->role->name == 'owner', function ($query) {
                    $query->withWhereHas('owner', fn ($query) => $query->whereId(auth()->id()));
                })
                ->orderByDesc('updated_at')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return match ($row->status) {
                        'Ready' => '<span class="badge badge-success">Ready</span>',
                        'Dalam Perjalanan Berangkat' => "<span class='badge badge-info'>$row->status</span>",
                        'Dalam Perjalanan Kembali Pulang' => "<span class='badge badge-info'>$row->status</span>",
                        default => '<span class="badge badge-secondary">Belum di Set</span>'
                    };
                })
                ->addColumn('action', function ($row) {
                    $userLogin = auth()->user();

                    if (!preg_match('[customer|driver]', $userLogin->role->name)) {
                        if ($row->owner_id === $userLogin->id || $userLogin->role->name === 'admin') {
                            $btnAction = "<button type='button' id='edit-airline' data-id='$row->id' class='btn btn-xs btn-warning'>Edit</button>";
                        }

                        if ($userLogin->role->name === 'admin') {
                            $btnAction .= "<button type='button' id='delete-airline' data-id='$row->id' data-delete='$row->name' class='btn btn-xs btn-danger ml-1'>Hapus</button>";
                        }
                    }

                    return $btnAction ?? '';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        if (request()->ajax()) {
            $ownerDoesntHaveAirline = User::select('id', 'name', 'role_id')
                ->with(['airline', 'role'])
                ->doesntHave('airline')
                ->whereHas('role', fn ($query) => $query->whereName('owner'))
                ->get();

            $statuses = ['Ready'];

            return response()->json([
                'owners' => $ownerDoesntHaveAirline,
                'statuses' => $statuses,
            ]);
        }
    }

    public function store(AirlineRequest $request)
    {
        if (request()->ajax()) {
            $request->validated();

            try {
                $owner = User::find($request['owner_id'], ['id']);

                $airline = new Airline();
                $airline->name = Str::title($request['name']);
                $airline->owner()->associate($owner);
                $airline->status = $request['status'] ?? NULL;
                $airline->save();

                return response()->json(
                    ['success' => 'SpeedBoat: ' . $airline->name . ' berhasil ditambahkan.']
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['errors' => 'Terjadi suatu kesalahan.']
                );
            }
        }
    }

    public function edit(Airline $airline)
    {
        if (request()->ajax()) {
            $ownerDoesntHaveAirline = User::select('id', 'name', 'role_id')
                ->with(['airline', 'role'])
                ->whereHas('role', fn ($query) => $query->whereName('owner'))
                ->get();

            $statuses = [
                'Ready',
                'Dalam Perjalanan Berangkat',
                'Dalam Perjalanan Kembali Pulang'
            ];

            return response()->json([
                'owners'  => $ownerDoesntHaveAirline,
                'airline' => $airline,
                'statuses' => $statuses,
            ]);
        }
    }

    public function update(AirlineRequest $request, Airline $airline)
    {
        if (request()->ajax()) {
            $request->validated();

            try {
                $status = match (true) {
                    $request['status'] != null => $request['status'],
                    $request['status'] == null => NULL,
                    default => $airline->status
                };

                $airlineUpdate = Airline::find($airline->id);
                $airlineUpdate->name = Str::title($request['name']);
                $airlineUpdate->status = $status;
                $airlineUpdate->save();

                return response()->json(['success' => 'berhasil diubah.']);
            } catch (\Exception $ex) {
                return response()->json(['errors' => 'Terjadi suatu kesalahan.']);
            }
        }
    }

    public function destroy(Airline $airline)
    {
        if (request()->ajax()) {
            try {
                $airline->delete();

                return response()->json(
                    ['success' => 'Berhasil dihapus.'],
                    Response::HTTP_OK
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['error' => "Error: Speadboat $airline->name mempunyai data."],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }
}
