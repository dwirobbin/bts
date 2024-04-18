<?php

namespace App\Http\Controllers;

use App\Models\{User, SpeedBoat};
use App\Http\Requests\SpeedBoatRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class SpeedBoatController extends Controller
{
    public function index()
    {
        return view('pages.speed_boats.index', [
            'title' => 'SpeedBoat',
            'show_sidebar' => true,
        ]);
    }

    public function getData()
    {
        if (request()->ajax()) {
            $data = SpeedBoat::query()
                ->select('id', 'name', 'owner_id', 'status')
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
                            $btnAction = "<button type='button' id='edit-speedboat' data-id='$row->id' class='btn btn-xs btn-warning'>Edit</button>";
                        }

                        if ($userLogin->role->name === 'admin') {
                            $btnAction .= "<button type='button' id='delete-speedboat' data-id='$row->id' data-delete='$row->name' class='btn btn-xs btn-danger ml-1'>Hapus</button>";
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
            $ownerDoesntHaveSpeedBoat = User::query()
                ->select('id', 'name', 'role_id')
                ->with(['speedBoat', 'role'])
                ->doesntHave('speedBoat')
                ->whereHas('role', fn ($query) => $query->whereName('owner'))
                ->get();

            $statuses = ['Ready'];

            return response()->json([
                'owners' => $ownerDoesntHaveSpeedBoat,
                'statuses' => $statuses,
            ], Response::HTTP_OK);
        }
    }

    public function store(SpeedBoatRequest $request)
    {
        if (request()->ajax()) {
            $request->validated();

            try {
                $owner = User::query()->find($request['owner_id'], ['id']);

                $speedBoat = new SpeedBoat();
                $speedBoat->name = Str::title($request['name']);
                $speedBoat->owner()->associate($owner);
                $speedBoat->status = $request['status'] ?? NULL;
                $speedBoat->save();

                return response()->json(
                    ['success' => 'SpeedBoat: ' . $speedBoat->name . ' berhasil ditambahkan.'],
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

    public function edit(SpeedBoat $speedBoat)
    {
        if (request()->ajax()) {
            $ownerDoesntHaveSpeedBoat = User::query()
                ->select('id', 'name', 'role_id')
                ->with(['speedBoat', 'role'])
                ->whereHas('role', fn ($query) => $query->whereName('owner'))
                ->get();

            $statuses = [
                'Ready',
                'Dalam Perjalanan Berangkat',
                'Dalam Perjalanan Kembali Pulang'
            ];

            return response()->json([
                'owners'  => $ownerDoesntHaveSpeedBoat,
                'speedBoat' => $speedBoat,
                'statuses' => $statuses,
            ], Response::HTTP_OK);
        }
    }

    public function update(SpeedBoatRequest $request, SpeedBoat $speedBoat)
    {
        if (request()->ajax()) {
            $request->validated();

            try {
                $status = match (true) {
                    $request['status'] != null => $request['status'],
                    $request['status'] == null => NULL,
                    default => $speedBoat->status
                };

                $speedBoatUpdate = SpeedBoat::query()->find($speedBoat->id);
                $speedBoatUpdate->name = Str::title($request['name']);
                $speedBoatUpdate->status = $status;
                $speedBoatUpdate->save();

                return response()->json(
                    ['success' => 'berhasil diubah.'],
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

    public function destroy(SpeedBoat $speedBoat)
    {
        if (request()->ajax()) {
            try {
                $speedBoat->delete();

                return response()->json(
                    ['success' => 'Berhasil dihapus.'],
                    Response::HTTP_OK
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['error' => "Error: Speadboat $speedBoat->name mempunyai data."],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }
}
