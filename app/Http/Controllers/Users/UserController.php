<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\AuthDataController;
use App\Models\User;
use App\Http\Requests\Users\IndexUserRequest;
use App\Utils\LogUtils;
use Inertia\Inertia;

class UserController extends AuthDataController
{
    public function index(IndexUserRequest $request)
    {
        try {
            $result = User::
                when(isset($request['id_usuario']), fn ($q) => $q->where('id_usuario', $request['id_usuario']))
                ->when(isset($request['name']), fn ($q) => $q->where('name', 'like', '%' . $request['name'] . '%'))
                ->when(isset($request['email']), fn ($q) => $q->where('email', 'like', '%' . $request['email'] . '%'))
                ->when(isset($request['role']), fn ($q) => $q->where('role', 'like', '%' . $request['role'] . '%'))
                ->when(
                    $request->boolean('paginated', true),
                    fn ($q) => $q->paginate($request['per_page'] ?? 10),
                    fn ($q) => $q->get(),
                );
            \Log::info('Listado de usuarios registrados');
            return response()->json($result, 200);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            return response()->json(
                [
                    'message' => 'Error al obtener el listado de usuarios registrados',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

}
