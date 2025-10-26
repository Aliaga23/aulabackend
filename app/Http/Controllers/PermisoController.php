<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class PermisoController extends Controller
{
    private $permisoModel;

    public function __construct()
    {
        $this->permisoModel = new Permiso();
    }

    // Crear permiso
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            if (!isset($data['nombre']) || empty($data['nombre'])) {
                return response()->noContent(400);
            }

            $permiso = $this->permisoModel->create(['nombre' => $data['nombre']]);
            return response()->json($permiso);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Listar permisos
    public function index(): JsonResponse
    {
        try {
            $permisos = $this->permisoModel->getAll();
            return response()->json($permisos);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Obtener permiso por id
    public function show($id): JsonResponse
    {
        try {
            $permiso = $this->permisoModel->findById($id);
            if (!$permiso) {
                return response()->noContent(404);
            }
            return response()->json($permiso);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Actualizar permiso
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->all();
            if (!isset($data['nombre']) || empty($data['nombre'])) {
                return response()->noContent(400);
            }
            $this->permisoModel->update($id, ['nombre' => $data['nombre']]);
            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Eliminar permiso
    public function destroy($id): JsonResponse
    {
        try {
            $this->permisoModel->delete($id);
            return response()->noContent();
        } catch (Exception $e) {
            return response()->noContent(500);
        }
    }
}
