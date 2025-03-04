<?php

namespace App\Http\Controllers\Agenda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Atencion;
use App\Http\Controllers\Utils\AuthDataController;
use App\Utils\LogUtils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AtencionController extends AuthDataController
{
    public function index(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'sometimes|integer|nullable',
            'id_servicio' => 'sometimes|integer|nullable',
            'id_cliente' => 'sometimes|integer|nullable',
            'id_empleado' => 'sometimes|integer|nullable',
            'fecha' => 'sometimes|date|nullable',
            'hora' => 'sometimes|date_format:H:i|nullable',
            'costo_final' => 'sometimes|decimal:2|nullable',
            'with_servicio' => 'sometimes|boolean|nullable',
            'with_cliente' => 'sometimes|boolean|nullable',
            'with_empleado' => 'sometimes|boolean|nullable',
            'paginated' => 'sometimes|boolean',
            'per_page' => 'sometimes|integer|min:5|max:100|exclude_if:paginated,false',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error al obtener el listado de citas',
                'error' => $validator->errors()->all()
            ], 422);
        }
        try {
            $result = Atencion::
                when(isset($request['with_cliente']), fn ($q) => $q->with('cliente:id,nombre,apellido,telefono,email'))
                ->when(isset($request['with_empleado']), fn ($q) => $q->with('empleado:id,user,username,email'))
                ->when(isset($request['with_servicio']), fn ($q) => $q->with('servicio:id,nombre,descripcion,costo_base'))
                ->when(isset($request['id']), fn ($q) => $q->where('id', $request['id']))
                ->when(isset($request['id_servicio']), fn ($q) => $q->where('id_servicio', $request['id_servicio']))
                ->when(isset($request['id_cliente']), fn ($q) => $q->where('id_cliente', $request['id_cliente']))
                ->when(isset($request['id_empleado']), fn ($q) => $q->where('id_empleado', $request['id_empleado']))
                ->when(isset($request['fecha']), fn ($q) => $q->whereDate('fecha_hora', $request['fecha']))
                ->when(isset($request['hora']), fn ($q) => $q->whereTime('fecha_hora', $request['hora']))
                ->when(isset($request['costo_final']), fn ($q) => $q->where('costo_final', $request['costo_final']))
                ->orderBy('fecha_hora', 'desc')
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
                    'message' => 'Error al obtener el listado de citas',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id_servicio' => 'required|integer',
            'id_cliente' => 'required|integer',
            'id_empleado' => 'required|integer',
            'fecha_hora' => 'required|date_format:Y-m-d H:i:s',
            'costo_final' => 'required|decimal:2|between:0,9999.99',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error al registrar la atención',
                'error' => $validator->errors()->all()
            ], 422);
        }
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin', 'empleado'])) {
                throw new \Exception('No tienes permisos para registrar atenciones');
            }
            if (!Servicio::find($request->id_servicio)->exists()) {
                return response()->json([
                    'message' => 'Error al registrar la atención',
                    'error' => 'El servicio no existe'
                ], 404);
            }
            if (!Cliente::find($request->id_cliente)->exists()) {
                return response()->json([
                    'message' => 'Error al registrar la atención',
                    'error' => 'El cliente no existe'
                ], 404);
            }
            if (!User::find($request->id_empleado)->exists()) {
                return response()->json([
                    'message' => 'Error al registrar la atención',
                    'error' => 'El empleado no existe'
                ], 404);
            }
            $atencion = new Atencion();
            $atencion->id_servicio = $request->id_servicio;
            $atencion->id_cliente = $request->id_cliente;
            $atencion->id_empleado = $request->id_empleado;
            $atencion->fecha_hora = $request->fecha_hora;
            $atencion->costo_final = $request->costo_final;
            $atencion->save();
            \DB::commit();
            return response()->json($atencion, 201);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            \DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error al registrar la cita',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function destroy($id_atencion)
    {
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin', 'empleado'])) {
                throw new \Exception('No tienes permisos para eliminar atenciones');
            }
            $atencion = Atencion::find($id_atencion);
            if (!$atencion) {
                return response()->json(['message' => 'Atención no encontrada'], 404);
            }
            $atencion->delete();
            \DB::commit();
            return response()->json($atencion, 200);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            \DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error al eliminar la atención',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }
}
