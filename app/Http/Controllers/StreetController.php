<?php

namespace App\Http\Controllers;

use App\Http\Requests\StreetRequest;
use App\Models\Street;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;

class StreetController extends Controller
{
    public function index()
    {
        return view('pages.streets.index', [
            'title' => 'Rute',
            'show_sidebar' => true,
        ]);
    }

    public function getData()
    {
        if (request()->ajax()) {
            $data = Street::query()
                ->select('id', 'from_route', 'to_route')
                ->orderByDesc('updated_at')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if (!preg_match('[owner|customer|driver]', auth()->user()->role->name)) {
                        $btnAction = "<button type='button' id='edit-street' data-id='$row->id' class='btn btn-xs btn-warning'>Edit</button>";
                        $btnAction .= "<button type='button' id='delete-street' data-id='$row->id' data-delete='$row->from_route => $row->to_route' class='btn btn-xs btn-danger ml-1'>Hapus</button>";
                    }

                    return $btnAction ?? '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(StreetRequest $request)
    {
        if (request()->ajax()) {
            $validatedData = $request->validated();

            $isExists = Street::query()
                ->whereFromRoute($request['from_route'])
                ->whereToRoute($request['to_route'])
                ->first();

            if ($isExists) {
                return response()->json(
                    ['same_route' => 'Rute tersebut sudah ada di database!'],
                    Response::HTTP_CONFLICT
                );
            }

            try {
                Street::query()->create($validatedData);

                return response()->json(
                    ['success' => 'Rute berhasil ditambahkan!'],
                    Response::HTTP_OK
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['errors' => 'Terjadi suatu kesalahan!'],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }

    public function edit(Street $street)
    {
        return request()->ajax()
            ? response()->json(['street' => $street], Response::HTTP_OK)
            : response()->noContent();
    }

    public function update(StreetRequest $request, Street $street)
    {
        if (request()->ajax()) {
            $validatedData = $request->validated();

            $sameRoute = Street::query()
                ->where('id', '!=', $street->id)
                ->whereFromRoute($request['from_route'])
                ->whereToRoute($request['to_route'])
                ->first();

            if ($sameRoute) {
                return response()->json(
                    ['same_route' => 'Rute tersebut sudah ada di database!'],
                    Response::HTTP_CONFLICT
                );
            }

            try {
                $street->update($validatedData);

                return response()->json(
                    ['success' => 'Berhasil diubah.'],
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

    public function destroy(Street $street)
    {
        if (request()->ajax()) {
            try {
                $street->delete();

                return response()->json(
                    ['success' => 'Berhasil dihapus.'],
                    Response::HTTP_OK
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['error' => "Error: Rute $street->from_route >> $street->to_route sedang digunakan."],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }
}
