<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\AuthDataController;
use App\Models\User;
use App\Http\Requests\Users\IndexUserRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Utils\LogUtils;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends AuthDataController
{
    public function index(IndexUserRequest $request)
    {
        try {
            $result = User::
                when(isset($request['id_usuario']), fn ($q) => $q->where('id', $request['id_usuario']))
                ->when(isset($request['name']), fn ($q) => $q->where('name', 'like', '%' . $request['name'] . '%'))
                ->when(isset($request['email']), fn ($q) => $q->where('email', 'like', '%' . $request['email'] . '%'))
                ->when(isset($request['role']), fn ($q) => $q->where('role', 'like', '%' . $request['role'] . '%'))
                ->when(isset($request['estado']), fn ($q) => $q->where('estado', $request['status'])
                )
                ->when(
                    $request->boolean('paginated', true),
                    fn ($q) => $q->paginate($request['per_page'] ?? 10),
                    fn ($q) => $q->get(),
                );
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

    public function store(StoreUserRequest $request)
    {
        \Log::info($request->all());
        try {
            \DB::beginTransaction();
            if (in_array($request->role, ['admin', 'empleado'])) {
                if (!Auth::user() || !Auth::user()->hasRole(['admin', 'empleado'])) {
                    throw new \Exception('No tienes permisos para crear este usuario');
                }
                \Log::info('User has permission to create admin or empleado');
            } else {
                // Assign the 'cliente' role to the user by default
                $request->merge(['role' => 'cliente']);
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'username' => substr(explode(' ', trim($request->name))[0], 0, 14),
                'role' => $request->role,
            ]);
            
            $user->assignRole($request->role);
            \DB::commit();
            return response()->json([
                'message' => ucfirst($request->role) . ' creado exitosamente',
            ], 201);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            \DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error al crear el usuario',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function update(Request $request){
        $validator = \Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error al actualizar el usuario',
                'error' => $validator->errors()
            ], 422);
        }
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin'])) {
                throw new \Exception('No tienes permisos para modificar este usuario');
            }
            $user = User::find($request->id);
            if (!$user) {
                throw new \Exception('Usuario no encontrado');
            }
            $userWithSameEmail = User::where('email', $request->email)->where('id', '!=', $request->id)->first();
            if ($userWithSameEmail) {
                throw new \Exception('El email ya está registrado');
            }
            $user->name = $request->name;
            $user->email = $request->email;
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            if (!empty($request->password)){
                $user->password = Hash::make($request->password);
            }
            $user->username = substr(explode(' ', trim($request->name))[0], 0, 14);
            $user->save();
            \DB::commit();
            return response()->json([
                'message' => 'Usuario actualizado exitosamente',
            ], 200);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            \DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error al actualizar el usuario',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
        
    }

    public function activateUser($id_usuario) {
        try {
            if (!Auth::user() || !Auth::user()->hasRole(['admin'])) {
                throw new \Exception('No tienes permisos para modificar este usuario');
            }
            \DB::beginTransaction();
            $user = User::find($id_usuario);
            if (!$user) {
                throw new \Exception('Usuario no encontrado');
            }
            $user->estado = 1;
            $user->save();
            \DB::commit();
            return response()->json([
                'message' => 'Usuario activado exitosamente',
            ], 200);
        } catch (\Throwable $th) {
            \DB::rollBack();
            LogUtils::error($th);
            return response()->json(
                [
                    'message' => 'Error al activar el usuario',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function destroy($id_usuario){
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin'])) {
                throw new \Exception('No tienes permisos para modificar este usuario');
            }
            $user = User::find($id_usuario);
            if (!$user) {
                throw new \Exception('Usuario no encontrado');
            }
            $user->estado = 0;
            $user->save();
            \DB::commit();
            return response()->json([
                'message' => 'Usuario eliminado exitosamente',
            ], 200);
        } catch (\Throwable $th) {
            \DB::rollBack();
            LogUtils::error($th);
            return response()->json(
                [
                    'message' => 'Error al eliminar el usuario',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

}
