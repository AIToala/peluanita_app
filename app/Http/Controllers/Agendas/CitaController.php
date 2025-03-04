<?php

namespace App\Http\Controllers\Agendas;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Atencion;
use App\Models\Servicio;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\AuthDataController;
use App\Utils\LogUtils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitaController extends AuthDataController
{
    public function index(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'sometimes|integer|nullable',
            'id_cliente' => 'sometimes|integer|nullable',
            'fecha' => 'sometimes|date|nullable',
            'hora' => 'sometimes|date_format:H:i|nullable',
            'estado' => 'sometimes|string|nullable',
            'with_cliente' => 'sometimes|boolean',
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
            $result = Cita::
                when(isset($request['with_cliente']), fn ($q) => $q->with('cliente:id,nombre,apellido,telefono,email'))
                ->when(isset($request['id']), fn ($q) => $q->where('id', $request['id']))
                ->when(isset($request['id_cliente']), fn ($q) => $q->where('id_cliente', $request['id_cliente']))
                ->when(isset($request['fecha']), fn ($q) => $q->whereDate('fecha_hora_cita', $request['fecha']))
                ->when(isset($request['hora']), fn ($q) => $q->whereTime('fecha_hora_cita', $request['hora']))
                ->when(isset($request['estado']), fn ($q) => $q->where('estado', $request['estado']))
                ->orderBy('fecha_hora_cita', 'desc')
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
            'id_cliente' => 'required|integer',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'estado' => 'required|string|in:pendiente',
        ]);
        if ($validator->fails()) {
            return response()->json([
                    'message' => 'Error al agendar la cita',
                    'error' => $validator->errors()->all()
                ], 422);
        }
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin', 'empleado', 'cliente'])) {
                throw new \Exception('No tienes permisos para agendar citas');
            }
            $cliente = Cliente::find($request['id_cliente']);
            if (!$cliente) {
                return response()->json([
                    'message' => 'Error al crear la cita',
                    'error' => 'El cliente no existe'
                ], 404);
            }
            if (Cita::where('id_cliente', $request['id_cliente'])
                ->where('fecha_hora_cita', Carbon::createFromFormat('Y-m-d H:i', $request['fecha'] . ' ' . $request['hora'])->format('Y-m-d H:i:s'))
                ->exists()) {
                return response()->json([
                    'message' => 'Error al crear la cita',
                    'error' => 'Ya existe una cita agendada para el cliente en la fecha y hora indicada'
                ], 409);
            }
            if (Cita::where('fecha_hora_cita', Carbon::createFromFormat('Y-m-d H:i', $request['fecha'] . ' ' . $request['hora'])->format('Y-m-d H:i:s'))
                ->exists()) {
                return response()->json([
                    'message' => 'Error al crear la cita',
                    'error' => 'Ya existe una cita agendada para la fecha y hora indicada'
                ], 409);
            }   
            if (Carbon::createFromFormat('Y-m-d H:i', $request['fecha'] . ' ' . $request['hora'])->isPast()) {
                return response()->json([
                    'message' => 'Error al crear la cita',
                    'error' => 'No se pueden agendar citas en el pasado'
                ], 409);
            }
            $cita = new Cita();
            $cita->id_cliente = $request['id_cliente'];
            $cita->fecha_hora_cita = Carbon::createFromFormat('Y-m-d H:i', $request['fecha'] . ' ' . $request['hora'])->format('Y-m-d H:i:s');
            $cita->estado = 'pendiente';
            $cita->save();
            \DB::commit();
            return response()->json($cita, 201);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            \DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error al crear la cita',
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
            'fecha' => 'sometimes|date',
            'hora' => 'sometimes|date_format:H:i',
            'estado' => 'sometimes|string|in:pendiente,confirmada,cancelada,rechazada,finalizada',
            'id_empleado' => 'required_if:estado,finalizada|integer',
            'id_servicio' => 'required_if:estado,finalizada|integer',
            'costo_final' => 'required_if:estado,finalizada|numeric|min:0',
            'fecha_hora' => 'required_if:estado,finalizada|date_format:Y-m-d H:i',
        ]);
        if ($validator->fails()) {
            return response()->json([
                    'message' => 'Error al actualizar la cita',
                    'error' => $validator->errors()->all()
                ], 422);
        }
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin', 'empleado'])) {
                throw new \Exception('No tienes permisos para actualizar citas');
            }
            $cita = Cita::find($request['id']);
            if (!$cita) {
                return response()->json([
                    'message' => 'Error al actualizar la cita',
                    'error' => 'La cita no existe'
                ], 404);
            }
            $cliente = Cliente::find($cita->id_cliente);
            if (!$cliente) {
                return response()->json([
                    'message' => 'Error al actualizar la cita',
                    'error' => 'El cliente no existe'
                ], 404);
            }
            if (isset($request['fecha']) && isset($request['hora']) && in_array($request['estado'], ['pendiente', 'aceptada'])) {
                $cita->fecha_hora_cita = Carbon::createFromFormat('Y-m-d H:i', $request['fecha'] . ' ' . $request['hora'])->format('Y-m-d H:i:s');
            }
            if ($cita->estado === 'finalizada') {
                return response()->json([
                    'message' => 'Error al actualizar la cita',
                    'error' => 'No se pueden realizar cambios en una cita finalizada'
                ], 409);
            }
            if ($cita->estado === 'aceptada' && !in_array($request['estado'], ['cancelada', 'finalizada'])) {
                return response()->json([
                    'message' => 'Error al actualizar la cita',
                    'error' => 'No puedes cambiar el estado de una cita aceptada a pendiente o confirmada'
                ], 409);
            }
            if (in_array($cita->estado, ['rechazada', 'cancelada']) && $request['estado'] !== 'pendiente') {
                return response()->json([
                    'message' => 'Error al actualizar la cita',
                    'error' => 'Solo se puede cambiar el estado de la cita a pendiente si ha sido rechazada o cancelada'
                ], 409);
            }
            if (isset($request['estado'])) {
                $cita->estado = $request['estado'];
            }
            if ($request['estado'] === 'finalizada') {
                $this->verificarEmpleado($request);
                $this->verificarServicio($request);
                Atencion::create(
                    [
                        'id_cliente' => $cita->id_cliente,
                        'id_empleado' => $request['id_empleado'],
                        'id_servicio' => $request['id_servicio'],
                        'costo_final' => $request['costo_final'],
                        'fecha_hora' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]
                );
            }
            $cita->save();
            \DB::commit();
            return response()->json($cita, 200);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            \DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error al actualizar la cita',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function destroy($id_cita)
    {
        try {
            \DB::beginTransaction();
            if (!Auth::user() || !Auth::user()->hasRole(['admin', 'empleado'])) {
                throw new \Exception('No tienes permisos para eliminar citas');
            }
            $cita = Cita::find($request['id']);
            if (!$cita) {
                return response()->json([
                    'message' => 'Error al eliminar la cita',
                    'error' => 'La cita no existe'
                ], 404);
            }
            if ($cita->estado === 'finalizada') {
                return response()->json([
                    'message' => 'Error al eliminar la cita',
                    'error' => 'No se pueden eliminar citas finalizadas'
                ], 409);
            }
            $cita->delete();
            \DB::commit();
            return response()->json($cita, 200);
        } catch (\Throwable $th) {
            LogUtils::error($th);
            \DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error al eliminar la cita',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function verificarEmpleado(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id_empleado' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Error al verificar el empleado');
        }
        try {
            $empleado = User::where('id', $request['id_empleado'])
                ->whereHas('roles', fn ($q) => $q->where('name', 'empleado'))
                ->first();
            if (!$empleado) {
                throw new \Exception('El empleado no existe');
            }
            return true;
        } catch (\Throwable $th) {
            LogUtils::error($th);
            throw new \Exception('Error al verificar el empleado');
        }
    }

    public function verificarServicio(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id_servicio' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Error al verificar el servicio');
        }
        try {
            $servicio = Servicio::where('id', $request['id_servicio'])->first();
            if (!$servicio) {
                throw new \Exception('El servicio no existe');
            }
            return true;
        } catch (\Throwable $th) {
            LogUtils::error($th);
            throw new \Exception('Error al verificar el servicio');
        }
    }
}
