<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\AuthDataController;
use App\Models\User;
use App\Models\Cliente;
use App\Utils\LogUtils;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClienteController extends AuthDataController
{
    public function index(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'sometimes|integer|nullable',
            'id_usuario' => 'sometimes|integer|nullable',
            'nombre' => 'sometimes|string|nullable',
            'apellido' => 'sometimes|string|nullable',
            'email' => 'sometimes|string|nullable',
            'telefono' => 'sometimes|string|nullable',
            'direccion' => 'sometimes|string|nullable',
            'estado' => 'sometimes|boolean',
            'with_usuario' => 'sometimes|boolean',
            'paginated' => 'sometimes|boolean',
            'per_page' => 'sometimes|integer|min:5|max:100|exclude_if:paginated,false',
        ]);
        if ($validator->fails()) {
            return response()->json([
                    'message' => 'Error al crear el cliente',
                    'error' => $validator->errors()->all()
                ], 422);
        }
        try {
            $result = Cliente::
                when(isset($request['with_usuario']), fn ($q) => $q->with('usuario:id,username,email'))
                ->when(isset($request['id']), fn ($q) => $q->where('id', $request['id']))
                ->when(isset($request['id_usuario']), fn ($q) => $q->where('id', $request['id_usuario']))
                ->when(isset($request['nombre']), fn ($q) => $q->where('nombre', 'like', '%' . $request['nombre'] . '%'))
                ->when(isset($request['apellido']), fn ($q) => $q->where('apellido', 'like', '%' . $request['apellido'] . '%'))
                ->when(isset($request['email']), fn ($q) => $q->where('email', 'like', '%' . $request['email'] . '%'))
                ->when(isset($request['telefono']), fn ($q) => $q->where('telefono', 'like', '%' . $request['telefono'] . '%'))
                ->when(isset($request['direccion']), fn ($q) => $q->where('direccion', 'like', '%' . $request['direccion'] . '%'))
                ->when(isset($request['estado']), fn ($q) => $q->where('estado', $request['estado'])
                )
                ->when(
                    $request->boolean('paginated', true),
                    fn ($q) => $q->paginate($request['per_page'] ?? 10),
                    fn ($q) => $q->get(),
                );
            $result->makeHidden(['created_at', 'updated_at', 'email_verified_at', 'password']);
            return response()->json($result, 200);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            return response()->json(
                [
                    'message' => 'Error al obtener el listado de clientes registrados',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function store(Request $request) {
        $validator = \Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'telefono' => 'required|string|max:15|min:7',
            'direccion' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        if ($validator->fails()) {
            return response()->json([
                    'message' => 'Error al crear el cliente',
                    'error' => $validator->errors()->all()
                ], 422);
        }
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin', 'empleado'])) {
                    throw new \Exception('No tienes permisos para crear este usuario');
            }
            $userWithSameEmail = User::where('email', $request->email)->first();
            if ($userWithSameEmail) {
                throw new \Exception('El email ya está registrado');
            }
            $user = User::create([
                'name' => mb_strtolower($request->nombre . ' ' . $request->apellido, 'UTF-8'),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'username' => mb_strtolower(str_replace(' ', '_', substr(trim($request->name), 0, 14)), 'UTF-8'),
                'role' => 'cliente',
                'estado' => 1,
            ]);
            $user->assignRole('cliente');
            $cliente = Cliente::create([
                'nombre' => mb_strtolower($request->nombre, 'UTF-8'),
                'apellido' => mb_strtolower($request->apellido, 'UTF-8'),
                'email' => $user->email, 'UTF-8',
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'id_usuario' => $user->id,
            ]);
            \DB::commit();
            return response()->json($user, 201);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            \DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error al crear el cliente',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function update(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required|integer',
            'nombre' => 'sometimes|string|max:255',
            'apellido' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|lowercase|email|max:255',
            'telefono' => 'sometimes|string|max:15|min:7',
            'direccion' => 'sometimes|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                    'message' => 'Error al crear el cliente',
                    'error' => $validator->errors()->all()
                ], 422);
        }
        \Log::info($request->all());
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin', 'empleado'])) {
                throw new \Exception('No tienes permisos para crear este usuario');
            }
            $cliente = Cliente::find($request->id);
            if (!$cliente) {
                throw new \Exception('Cliente no encontrado');
            }
            $user = User::find($cliente->id_usuario);
            if (!$user) {
                Auth::logout();
                throw new \Exception('Usuario no encontrado');                
            }
            $userWithSameEmail = User::where('email', $request->email)->where('id', '!=', $user->id)->first();
            if ($userWithSameEmail) {
                \Log::info($userWithSameEmail);
                throw new \Exception('El email ya está registrado');
            }
            $cliente->nombre = $request->has('nombre') ? $request->nombre : $cliente->nombre;
            $cliente->apellido = $request->has('apellido') ? $request->apellido : $cliente->apellido;
            $cliente->email = $request->has('email') ? $request->email : $cliente->email;
            $cliente->telefono = $request->has('telefono') ? $request->telefono : $cliente->telefono;
            $cliente->direccion = $request->has('direccion') ? $request->direccion : $cliente->direccion;
            $cliente->nombre = mb_strtolower($cliente->nombre, 'UTF-8');
            $cliente->apellido = mb_strtolower($cliente->apellido, 'UTF-8');
            $cliente->save();
            $user->name = $cliente->nombre . ' ' . $cliente->apellido;
            $user->email = $cliente->email;
            $user->username = mb_strtolower(str_replace(' ', '_', substr(trim($user->name), 0, 14)), 'UTF-8');
            $user->save();
            \DB::commit();
            return response()->json($cliente, 200);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            \DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error al actualizar el cliente',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }
    public function activateCliente($id_cliente)
    {
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin', 'empleado'])) {
                throw new \Exception('No tienes permisos para crear este usuario');
            }
            $cliente = Cliente::find($id_cliente);
            if (!$cliente) {
                throw new \Exception('Cliente no encontrado');
            }
            $user = User::find($cliente->id_usuario);
            if (!$user) {
                Auth::logout();
                throw new \Exception('Usuario no encontrado');                
            }
            $user->estado = 1;
            $user->save();
            \DB::commit();
            return response()->json($cliente, 200);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            \DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error al activar el cliente',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function destroy($id_cliente)
    {
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin', 'empleado'])) {
                throw new \Exception('No tienes permisos para crear este usuario');
            }
            $cliente = Cliente::find($id_cliente);
            if (!$cliente) {
                throw new \Exception('Cliente no encontrado');
            }
            $user = User::find($cliente->id_usuario);
            if (!$user) {
                Auth::logout();
                throw new \Exception('Usuario no encontrado');                
            }
            $user->estado = 0;
            $user->save();
            \DB::commit();
            return response()->json($cliente, 200);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            \DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error al eliminar el cliente',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }
}
