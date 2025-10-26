<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class AsignacionController extends Controller
{
    private $asignacionModel;

    public function __construct()
    {
        $this->asignacionModel = new Asignacion();
    }

    // Obtener todas las asignaciones
    public function index(): JsonResponse
    {
        try {
            $asignaciones = $this->asignacionModel->obtenerTodasAsignaciones();
            return response()->json($asignaciones);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al obtener asignaciones'], 500);
        }
    }

    // Asignar docente a materia
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $required = ['coddocente','idgrupo','idcarrera','sigla','idgestion'];
            foreach ($required as $f) {
                if (!isset($data[$f]) || $data[$f] === '') {
                    return response()->noContent(400);
                }
            }

            $asignacion = $this->asignacionModel->asignarDocenteMateria($data);
            return response()->json($asignacion);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
}
