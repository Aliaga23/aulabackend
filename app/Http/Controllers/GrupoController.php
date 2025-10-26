<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class GrupoController extends Controller
{
    private $grupoModel;

    public function __construct()
    {
        $this->grupoModel = new Grupo();
    }

    // Crear grupo
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            if (!isset($data['nombre']) || $data['nombre'] === '') {
                return response()->noContent(400);
            }
            $grupo = $this->grupoModel->create(['nombre' => $data['nombre']]);
            return response()->json($grupo);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Listar grupos
    public function index(): JsonResponse
    {
        try {
            $grupos = $this->grupoModel->getAll();
            return response()->json($grupos);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Obtener grupo
    public function show($id): JsonResponse
    {
        try {
            $grupo = $this->grupoModel->findById($id);
            if (!$grupo) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
            return response()->json($grupo);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Actualizar grupo
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->all();
            if (!isset($data['nombre']) || $data['nombre'] === '') {
                return response()->noContent(400);
            }
            $this->grupoModel->update($id, ['nombre' => $data['nombre']]);
            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Eliminar grupo
    public function destroy($id): JsonResponse
    {
        try {
            $this->grupoModel->delete($id);
            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
}
