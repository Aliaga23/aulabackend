<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class AdministradorController extends Controller
{
    private $administradorModel;
    private $userModel;
    
    public function __construct()
    {
        $this->administradorModel = new Administrador();
        $this->userModel = new User();
    }
    
    /**
     * Crear administrador completo (usuario + administrador)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            // Validar campos requeridos
            $required = ['nombre', 'apellido', 'correo', 'ci', 'contrasena', 'fecha_contrato'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return response()->noContent(400);
                }
            }
            
            // Validar email único
            if ($this->userModel->existsByEmail($data['correo'])) {
                return response()->noContent(400);
            }
            
            // Validar CI único
            if ($this->userModel->existsByCI($data['ci'])) {
                return response()->noContent(400);
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
            
            // Aceptar tanto fecha_contrato como fechacontrato
            $fechaContrato = $data['fecha_contrato'] ?? $data['fechacontrato'] ?? date('Y-m-d');
            
            $adminData = [
                'fecha_contrato' => $fechaContrato
            ];
            
            // Crear administrador
            $result = $this->administradorModel->create($userData, $adminData);
            
            return response()->json($result);
            
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Obtener todos los administradores
     */
    public function index(): JsonResponse
    {
        try {
            $administradores = $this->administradorModel->getAll();
            
            return response()->json($administradores);
            
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Obtener administrador específico
     */
    public function show($id): JsonResponse
    {
        try {
            $administrador = $this->administradorModel->findById($id);
            
            if (!$administrador) {
                return response()->noContent(404);
            }
            
            return response()->json($administrador);
            
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Actualizar administrador
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->all();
            
            // Validar campos requeridos
            $required = ['nombre', 'apellido'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return response()->json(['message' => "El campo {$field} es requerido"], 400);
                }
            }
            
            // Preparar datos
            $userData = [
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'telefono' => $data['telefono'] ?? null,
                'sexo' => $data['sexo'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'activo' => $data['activo'] ?? true
            ];
            
            // Aceptar tanto fecha_contrato como fechacontrato
            $fechaContrato = $data['fecha_contrato'] ?? $data['fechacontrato'] ?? date('Y-m-d');
            
            $adminData = [
                'fecha_contrato' => $fechaContrato
            ];
            
            // Actualizar administrador
            $this->administradorModel->update($id, $userData, $adminData);
            
            // Obtener el administrador actualizado
            $updated = $this->administradorModel->findById($id);
            return response()->json($updated);
            
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Eliminar administrador (desactivar)
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->administradorModel->delete($id);
            return response()->json(['message' => 'Administrador eliminado correctamente']);
            
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
}
