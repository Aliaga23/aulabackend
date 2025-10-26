<?php

namespace App\Http\Controllers;

use App\Models\Gestion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class GestionController extends Controller
{
    private $gestionModel;

    public function __construct()
    {
        $this->gestionModel = new Gestion();
    }

    // Crear gestión
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $required = ['anio','periodo','fechainicio','fechafin'];
            foreach ($required as $f) {
                if (!isset($data[$f]) || $data[$f] === '') {
                    return response()->noContent(400);
                }
            }
            $gestion = $this->gestionModel->create($data);
            return response()->json($gestion);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Listar gestiones
    public function index(): JsonResponse
    {
        try {
            $gestiones = $this->gestionModel->getAll();
            return response()->json($gestiones);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Obtener gestión
    public function show($id): JsonResponse
    {
        try {
            $gestion = $this->gestionModel->findById($id);
            if (!$gestion) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
            return response()->json($gestion);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Actualizar gestión
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->all();
            $required = ['anio','periodo','fechainicio','fechafin'];
            foreach ($required as $f) {
                if (!isset($data[$f]) || $data[$f] === '') {
                    return response()->noContent(400);
                }
            }
            $this->gestionModel->update($id, $data);
            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    // Eliminar gestión
    public function destroy($id): JsonResponse
    {
        try {
            $this->gestionModel->delete($id);
            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
}
