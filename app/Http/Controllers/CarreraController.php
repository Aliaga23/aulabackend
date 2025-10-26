<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class CarreraController extends Controller
{
    private $carreraModel;

    public function __construct()
    {
        $this->carreraModel = new Carrera();
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            if (!isset($data['nombre']) || $data['nombre'] === '') {
                return response()->noContent(400);
            }
            $carrera = $this->carreraModel->create(['nombre' => $data['nombre']]);
            return response()->json($carrera);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function index(): JsonResponse
    {
        try {
            $carreras = $this->carreraModel->getAll();
            return response()->json($carreras);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $carrera = $this->carreraModel->findById($id);
            if (!$carrera) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
            return response()->json($carrera);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->all();
            if (!isset($data['nombre']) || $data['nombre'] === '') {
                return response()->json(['message' => 'El nombre es requerido'], 400);
            }
            $this->carreraModel->update($id, ['nombre' => $data['nombre']]);
            $updated = $this->carreraModel->findById($id);
            return response()->json($updated);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->carreraModel->delete($id);
            return response()->json(['message' => 'Carrera eliminada correctamente']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
}
