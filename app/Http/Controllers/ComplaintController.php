<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $validate = Validator::make($request->only('body'), [
                'body' => ['required', 'min:3', 'max:50'],
            ], [
                'required' => ':attribute tidak boleh kosong.',
                'min' => ':attribute minimal harus :min karakter.',
                'max' => ':attribute maksimal harus :max karakter.',
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
                Complaint::create([
                    'user_id' => auth()->user()->id,
                    'order_id' => $request['order_id'],
                    'body' => $request['body']
                ]);

                if (preg_match('[admin|owner]', auth()->user()->role->name)) {
                    Complaint::whereOrderId($request['order_id'])->update(['seen' => true]);
                } else {
                    Complaint::whereOrderId($request['order_id'])->update(['seen_for_admin' => true]);
                }

                return response()->json([
                    'success' => 'Keluhan anda berhasil dikirim!'
                ]);
            } catch (\Exception $ex) {
                return response()->json([
                    'errors' => 'Terjadi suatu kesalahan!'
                ]);
            }
        }
    }
}
