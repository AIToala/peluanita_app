<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\AuthDataController;
use App\Models\Servicio;
use App\Utils\LogUtils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServicioController extends AuthDataController
{
    public function index(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id_servicio' => 'sometimes|integer|nullable',
            'nombre' => 'sometimes|string|nullable',
            'descripcion' => 'sometimes|string|nullable',
            'costo_base' => 'sometimes|decimal:2|nullable',
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
            $result = Servicio::
                when(isset($request['id_servicio']), fn ($q) => $q->where('id', $request['id_servicio']))
                ->when(isset($request['nombre']), fn ($q) => $q->where('nombre', 'like', '%' . $request['nombre'] . '%'))
                ->when(isset($request['descripcion']), fn ($q) => $q->where('descripcion', 'like', '%' . $request['descripcion'] . '%'))
                ->when(isset($request['costo_base']), fn ($q) => $q->where('costo_base', $request['costo_base'])
                )
                ->when(
                    $request->boolean('paginated', true),
                    fn ($q) => $q->paginate($request['per_page'] ?? 10),
                    fn ($q) => $q->get(),
                );
            $result->makeHidden(['created_at', 'updated_at']);
            return response()->json($result, 200);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            return response()->json(
                [
                    'message' => 'Error al obtener el listado de servicios registrados',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'costo_base' => 'required|decimal:2|between:0,9999.99',
        ]);
        if ($validator->fails()) {
            return response()->json([
                    'message' => 'Error al crear el cliente',
                    'error' => $validator->errors()->all()
                ], 422);
        }
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin'])) {
                throw new \Exception('No tienes permisos para crear este usuario');
            }
            $servicioExists = Servicio::where('nombre', mb_strtoupper($request->nombre, 'UTF-8'))->first();
            if ($servicioExists) {
                return response()->json(['message' => 'El servicio ya existe', 'error' => 'Ya existe un servicio con este nombre'], 409);
            }
            $servicio = new Servicio();
            $servicio->nombre = mb_strtoupper($request->nombre, 'UTF-8');
            $servicio->descripcion = $request->descripcion;
            $servicio->costo_base = $request->costo_base;
            $servicio->save();
            \DB::commit();
            return response()->json($servicio, 201);
        } catch (\Throwable $th) {
            \DB::rollBack();
            LogUtils::error($th);
            return response()->json(
                [
                    'message' => 'Error al crear el servicio',
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
            'descripcion' => 'sometimes|string|max:255',
            'costo_base' => 'sometimes|decimal:2|between:0,9999.99',
        ]);
        if ($validator->fails()) {
            return response()->json([
                    'message' => 'Error al crear el cliente',
                    'error' => $validator->errors()->all()
                ], 422);
        }
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin'])) {
                throw new \Exception('No tienes permisos para crear este usuario');
            }
            $servicio = Servicio::find($request->id);
            if (!$servicio) {
                return response()->json(['message' => 'Servicio no encontrado'], 404);
            }
            $servicio->nombre = $request->has('nombre') ? mb_strtoupper($request->nombre, 'UTF-8') : mb_strtoupper($servicio->nombre, 'UTF-8');
            $servicio->descripcion = $request->has('descripcion') ? $request->descripcion : $servicio->descripcion;
            $servicio->costo_base = $request->has('costo_base') ? $request->costo_base : $servicio->costo_base;
            $servicio->save();
            \DB::commit();
            return response()->json($servicio, 200);
        } catch (\Throwable $th) {
            \DB::rollBack();
            LogUtils::error($th);
            return response()->json(
                [
                    'message' => 'Error al actualizar el servicio',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function activateServicio($id_servicio)
    {
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin'])) {
                throw new \Exception('No tienes permisos para crear este usuario');
            }
            $servicio = Servicio::find($id_servicio);
            if (!$servicio) {
                return response()->json(['message' => 'Servicio no encontrado'], 404);
            }
            $servicio->estado = 1;
            $servicio->save();
            \DB::commit();
            return response()->json(['message' => 'Servicio activado exitosamente'], 200);
        } catch (\Throwable $th) {
            \DB::rollBack();
            LogUtils::error($th);
            return response()->json(
                [
                    'message' => 'Error al activar el servicio',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function destroy($id_servicio)
    {
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin'])) {
                throw new \Exception('No tienes permisos para crear este usuario');
            }
            $servicio = Servicio::find($id_servicio);
            if (!$servicio) {
                return response()->json(['message' => 'Servicio no encontrado'], 404);
            }
            $servicio->estado = 0;
            $servicio->save();
            \DB::commit();
            return response()->json(['message' => 'Servicio eliminado exitosamente'], 200);
        } catch (\Throwable $th) {
            \DB::rollBack();
            LogUtils::error($th);
            return response()->json(
                [
                    'message' => 'Error al eliminar el servicio',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }
}
