<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class RoleController extends Controller
{
    private $roleModel;

    public function __construct()
    {
        $this->roleModel = new Role();
    }

    // Crear rol
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            if (!isset($data['nombre']) || empty($data['nombre'])) {
                return response()->noContent(400);
            }

            $role = $this->roleModel->create(['nombre' => $data['nombre']]);
            return response()->json($role);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Listar roles
    public function index(): JsonResponse
    {
        try {
            $roles = $this->roleModel->getAll();
            return response()->json($roles);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Obtener rol por id
    public function show($id): JsonResponse
    {
        try {
            $role = $this->roleModel->findById($id);
            if (!$role) {
                return response()->noContent(404);
            }
            return response()->json($role);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Actualizar rol
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->all();
            if (!isset($data['nombre']) || empty($data['nombre'])) {
                return response()->noContent(400);
            }
            $this->roleModel->update($id, ['nombre' => $data['nombre']]);
            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Eliminar rol
    public function destroy($id): JsonResponse
    {
        try {
            $this->roleModel->delete($id);
            return response()->noContent();
        } catch (Exception $e) {
            return response()->noContent(500);
        }
    }
}
