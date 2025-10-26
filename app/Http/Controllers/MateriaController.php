<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class MateriaController extends Controller
{
    private $materiaModel;

    public function __construct()
    {
        $this->materiaModel = new Materia();
    }

    // Crear materia
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $required = ['idcarrera', 'sigla', 'nombre'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || $data[$field] === '') {
                    return response()->noContent(400);
                }
            }

            $materia = $this->materiaModel->create($data);
            return response()->json($materia);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Listar materias
    public function index(): JsonResponse
    {
        try {
            $materias = $this->materiaModel->getAll();
            return response()->json($materias);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Obtener materia
    public function show($idCarrera, $sigla): JsonResponse
    {
        try {
            $materia = $this->materiaModel->findByKey($idCarrera, $sigla);
            if (!$materia) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
            return response()->json($materia);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Actualizar materia
    public function update(Request $request, $idCarrera, $sigla): JsonResponse
    {
        try {
            $data = $request->all();
            if (!isset($data['nombre']) || $data['nombre'] === '') {
                return response()->noContent(400);
            }
            $this->materiaModel->update($idCarrera, $sigla, ['nombre' => $data['nombre']]);
            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Eliminar materia
    public function destroy($idCarrera, $sigla): JsonResponse
    {
        try {
            $this->materiaModel->delete($idCarrera, $sigla);
            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
}
