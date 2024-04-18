<?php

namespace App\Http\Controllers;

use App\Models\{Complaint, Order};
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function show(Order $order)
    {
        $complaints = Complaint::query()
            ->with(['user:id,name'])
            ->whereOrderId($order->id)
            ->get();

        return response()->json([
            'complaints' => $complaints
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        if ($request->ajax()) {
            $validate = Validator::make($request->only('body'), [
                'body' => ['required', 'min:3'],
            ], [
                'required' => ':attribute tidak boleh kosong.',
                'min' => ':attribute minimal harus :min karakter.',
            ], [
                'body' => 'Isi Pesan',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    ['errors' => $validate->errors()->all()],
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                );
            }

            try {
                Complaint::query()->create([
                    'user_id' => auth()->id(),
                    'order_id' => $request['order_id'],
                    'body' => $request['body']
                ]);

                if (preg_match('[admin|owner]', auth()->user()->role->name)) {
                    Complaint::query()
                        ->whereOrderId($request['order_id'])
                        ->update(['seen' => true]);
                } else {
                    Complaint::query()
                        ->whereOrderId($request['order_id'])
                        ->update(['seen_for_admin' => true]);
                }

                return response()->json([
                    'success' => 'Keluhan anda berhasil dikirim!'
                ], Response::HTTP_OK);
            } catch (\Exception $ex) {
                return response()->json([
                    'errors' => 'Terjadi suatu kesalahan!'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
