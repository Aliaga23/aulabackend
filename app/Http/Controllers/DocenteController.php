<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class DocenteController extends Controller
{
    private $docenteModel;
    private $userModel;
    
    public function __construct()
    {
        $this->docenteModel = new Docente();
        $this->userModel = new User();
    }
    
    /**
     * Crear docente completo (usuario + docente)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            // Validar campos requeridos
            $required = ['nombre', 'apellido', 'correo', 'ci', 'contrasena', 'fechacontrato'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return response()->json([
                        'success' => false,
                        'message' => "El campo $field es requerido"
                    ], 400);
                }
            }
            
            // Validar email único
            if ($this->userModel->existsByEmail($data['correo'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El email ya está registrado'
                ], 400);
            }
            
            // Validar CI único
            if ($this->userModel->existsByCI($data['ci'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El CI ya está registrado'
                ], 400);
            }
            
            // Preparar datos
            $userData = [
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'correo' => $data['correo'],
                'ci' => $data['ci'],
                'contrasena' => $data['contrasena'],
                'telefono' => $data['telefono'] ?? null,
                'sexo' => $data['sexo'] ?? null,
                'direccion' => $data['direccion'] ?? null
            ];
            
            $docenteData = [
                'especialidad' => $data['especialidad'] ?? null,
                'fechacontrato' => $data['fechacontrato']
            ];
            
            // Crear docente
            $result = $this->docenteModel->create($userData, $docenteData);
            
            // Responder solo con el payload de datos, sin envoltura
            return response()->json($result);
            
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Obtener todos los docentes
     */
    public function index(): JsonResponse
    {
        try {
            $docentes = $this->docenteModel->getAll();
            // Solo data
            return response()->json($docentes);
            
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Obtener docente específico
     */
    public function show($id): JsonResponse
    {
        try {
            $docente = $this->docenteModel->findById($id);
            
            if (!$docente) {
                return response()->noContent(404);
            }
            
            // Solo data
            return response()->json($docente);
            
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Actualizar docente
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->all();
            
            // Preparar datos
            $userData = [
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'telefono' => $data['telefono'] ?? null,
                'sexo' => $data['sexo'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'activo' => $data['activo'] ?? true
            ];
            
            $docenteData = [
                'especialidad' => $data['especialidad'] ?? null,
                'fecha_contrato' => $data['fecha_contrato']
            ];
            
            // Actualizar docente
            $this->docenteModel->update($id, $userData, $docenteData);
            
            // Éxito sin cuerpo
            return response()->noContent();
            
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Eliminar docente (desactivar)
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->docenteModel->delete($id);
            
            // Éxito sin cuerpo
            return response()->noContent();
            
        } catch (Exception $e) {
            return response()->noContent(500);
        }
    }
}
