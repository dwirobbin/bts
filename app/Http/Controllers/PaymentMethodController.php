<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PaymentMethod;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use App\Http\Requests\PaymentMethodRequest;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return view('pages.payment_methods.index', [
            'title' => 'Metode Pembayaran',
            'show_sidebar' => true,
        ]);
    }

    public function getData()
    {
        if (request()->ajax()) {
            $query = PaymentMethod::select('id', 'method', 'target_account', 'created_by')
                ->with(['createdBy:id,name']);

            if (auth()->user()->role->name === 'owner') {
                $query->whereCreatedBy(auth()->user()->id);
            }

            return DataTables::of($query->orderByDesc('updated_at')->get())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if (!preg_match('[customer|driver]', auth()->user()->role->name)) {
                        $btnAction = "<button type='button' id='edit-paymentmethod' data-id='$row->id' class='btn btn-xs btn-warning'>Edit</button>";
                        $btnAction .= "<button type='button' id='delete-paymentmethod' data-id='$row->id' data-delete='$row->method' class='btn btn-xs btn-danger ml-1'>Hapus</button>";
                    }

                    return $btnAction ?? '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create()
    {
        if (request()->ajax() && auth()->user()->role->name === 'admin') {
            return response()->json([
                'owners' => User::whereHas('role', fn ($q) => $q->whereName('owner'))->get(),
            ]);
        }
    }

    public function store(PaymentMethodRequest $request)
    {
        if (request()->ajax()) {
            $request->validated();

            try {
                PaymentMethod::create([
                    'method' => $request['method'],
                    'target_account' => $request['target_account'],
                    'created_by' => $request['owner_id'] ?? auth()->user()->id,
                ]);

                return response()->json(
                    ['success' => 'Metode pembayaran berhasil ditambahkan!']
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['errors' => 'Terjadi suatu kesalahan!']
                );
            }
        }
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        if (request()->ajax()) {
            return response()->json([
                'payment_method' => $paymentMethod,
                'owners' => User::whereHas('role', fn ($q) => $q->whereName('owner'))->get(),
            ]);
        }
    }

    public function update(PaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        if (request()->ajax()) {
            $request->validated();

            try {
                $paymentMethod->update([
                    'method' => $request['method'],
                    'target_account' => $request['target_account'],
                    'created_by' => $request['owner_id'] ?? $paymentMethod->created_by,
                ]);

                return response()->json(['success' => 'Berhasil diubah.']);
            } catch (\Exception $ex) {
                return response()->json(['errors' => 'Terjadi suatu kesalahan.']);
            }
        }
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if (request()->ajax()) {
            try {
                $paymentMethod->delete();

                return response()->json(
                    ['success' => 'Berhasil dihapus.'],
                    Response::HTTP_OK
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['error' => "Error: Metode Pembayaran $paymentMethod->method sedang digunakan."],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }
}
