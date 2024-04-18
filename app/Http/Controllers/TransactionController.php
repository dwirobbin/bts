<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\{Storage, Validator};
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TransactionController extends Controller
{
    public function index()
    {
        return view('pages.transactions.index', [
            'title' => 'Transaksi',
            'show_sidebar' => true,
        ]);
    }

    public function getData()
    {
        if (request()->ajax()) {
            $query = Transaction::query()
                ->with([
                    'order' => fn ($query) => $query->with([
                        'user:id,name',
                        'ticket' => fn ($query) => $query->with(['speedBoat:id', 'street:id'])
                    ]),
                    'paymentMethod:id,method,target_account',
                ]);

            if (auth()->user()->role->name === 'owner') {
                $query->whereHas('order', fn ($q) => $q->whereHas('ticket', fn ($q) => $q->whereHas('speedBoat', fn ($q) => $q->whereOwnerId(auth()->id()))));
            }

            if (auth()->user()->role->name === 'customer') {
                $query->whereHas('order', fn ($q) => $q->whereUserId(auth()->id()));
            }

            return DataTables::of($query->orderByDesc('updated_at')->get())
                ->addIndexColumn()
                ->editColumn('total', fn ($row) => number_format($row->total, 0, ',', '.'))
                ->editColumn(
                    'status',
                    fn ($row) => $row->status
                        ? '<span class="badge badge-success">Disetujui</span>'
                        : '<span class="badge badge-danger">Menunggu</span>'
                )
                ->editColumn(
                    'image',
                    fn ($row) => is_null($row->image)
                        ? 'Belum diunggah'
                        : Str::after($row->image, '_')
                )
                ->addColumn('action', function ($row) {
                    if (!preg_match('[driver]', auth()->user()->role->name)) {
                        if (auth()->user()->role->name === 'customer' && $row->status != true) {
                            $btnAction = "<button type='button' id='upload-img' data-id='$row->id' class='btn btn-xs btn-primary'>Unggah Bukti Pembayaran</button>";
                        }

                        if (preg_match('[admin|owner]', auth()->user()->role->name)) {
                            $btnAction = "<button type='button' id='update-status' data-id='$row->id' class='btn btn-xs btn-warning'>Perbaharui Status</button>";
                        }
                    }

                    return $btnAction ?? '';
                })
                ->rawColumns(['total', 'status', 'image', 'action'])
                ->make(true);
        }
    }

    public function edit(Transaction $transaction)
    {
        return request()->ajax()
            ? response()->json(
                ['transaction' => $transaction->load('order.passengers')],
                Response::HTTP_OK
            )
            : response()->noContent();
    }

    public function storeImg(Request $request, Transaction $transaction)
    {
        if (request()->ajax()) {
            $validate = Validator::make($request->only('image'), [
                'image' => ['required', 'mimes:jpg,jpeg,png,bmp,gif,svg,webp', 'max:2048']
            ], [
                'required' => ':attribute tidak boleh kosong',
                'max' => ':attribute maksimal harus :max MB',
            ], [
                'image' => 'Gambar',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    ['errors' => $validate->errors()->all()],
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                );
            }

            if ($request->hasFile('image')) {
                if (Storage::exists('public/images/' . $transaction->image)) {
                    Storage::delete('public/images/' . $transaction->image);
                }

                $image = $request->file('image');
                $imageReplace = str_replace('_', '-', $image->getClientOriginalName());
                $imageName = uniqid() . '_' . $imageReplace;

                Storage::putFileAs('public/images', $image, $imageName);

                $transaction->image = $imageName;
            }

            $transaction->save();

            return response()->json(
                ['success' => 'Berhasil diupdate.'],
                Response::HTTP_OK
            );
        }
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        if (request()->ajax()) {
            $transaction->update([
                'status' => $request->status === 'on' ? true : false
            ]);

            return response()->json(
                ['success' => 'Status berhasil diperbaharui.'],
                Response::HTTP_OK
            );
        }
    }
}
