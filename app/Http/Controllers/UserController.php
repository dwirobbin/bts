<?php

namespace App\Http\Controllers;

use App\Models\{Role, User};
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function index()
    {
        return view('pages.users.index', [
            'title' => 'Pengguna',
            'show_sidebar' => true,
        ]);
    }

    public function getData()
    {
        if (request()->ajax()) {
            $data = User::query()
                ->select('id', 'name', 'email', 'phone_number', 'gender', 'role_id')
                ->with(['speedBoat', 'role:id,name'])
                ->when(
                    auth()->user()->role->name != 'admin',
                    fn ($query) => $query->whereId(auth()->id())
                )
                ->orderByDesc('updated_at')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn(
                    'phone_number',
                    fn ($row) => $row->phone_number ?? 'Belum di set'
                )
                ->editColumn(
                    'gender',
                    fn ($row) => $row->gender ?? 'Belum di set'
                )
                ->editColumn(
                    'speedboat_name',
                    fn ($row) => $row->speedBoat->name ?? 'Tidak Punya'
                )
                ->addColumn(
                    'action',
                    function ($row) {
                        $btnAction = "<button type='button' id='edit-user' data-id='$row->id' class='btn btn-xs btn-warning'>Edit</button>";

                        if ($row->id != auth()->id()) {
                            $btnAction .= "<button type='button' id='delete-user' data-id='$row->id' data-delete='$row->name' class='btn btn-xs btn-danger ml-1'>Hapus</button>";
                        }

                        return $btnAction;
                    }
                )
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create()
    {
        if (request()->ajax()) {
            $roles = Role::query()
                ->select('id', 'name')
                ->where('name', '!=', 'admin')
                ->get();

            return response()->json(
                ['roles' => $roles],
                Response::HTTP_OK
            );
        }
    }

    public function store(UserRequest $request)
    {
        if (request()->ajax()) {
            $request->validated();

            try {
                $role = Role::query()->find($request['role_id'], ['id']);

                $user = new User();
                $user->name = str($request['name'])->title();
                $user->email = $request['email'];
                $user->gender = $request['gender'];
                $user->phone_number = $request['phone_number'];
                $user->password = $request['password'];
                $user->role()->associate($role);
                $user->save();

                return response()->json(
                    ['success' => $user->name . ' Berhasil didaftarkan.'],
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

    public function edit(User $user)
    {
        if (request()->ajax()) {
            $query = Role::query()->select('id', 'name');

            if (preg_match('[owner]', auth()->user()->role->name)) {
                $query->where('name', '!=', 'admin');
            }

            if ($user->id !== auth()->id()) {
                $query->where('name', '!=', 'admin');
            }

            return response()->json(
                [
                    'user' => $user,
                    'roles' => $query->get(),
                ],
                Response::HTTP_OK
            );
        }
    }

    public function update(UserRequest $request, User $user)
    {
        if (request()->ajax()) {
            $request->validated();

            try {
                $role = Role::query()->find($request['role_id'] ?? $user->role_id, ['id']);

                $user = User::query()->find($user->id);
                $user->name = str($request['name'])->title();
                $user->email = $request['email'];
                $user->gender = $request['gender'];
                $user->phone_number = $request['phone_number'];
                $user->role()->associate($role);
                $user->save();

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

    public function destroy(User $user)
    {
        if (request()->ajax()) {
            try {
                $user->delete();

                return response()->json(
                    ['success' => 'Berhasil dihapus.'],
                    Response::HTTP_OK
                );
            } catch (\Exception $ex) {
                return response()->json(
                    ['error' => "Error: Akun $user->name mempunyai data."],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }
}
