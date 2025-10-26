<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JWTService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class AuthController extends Controller
{
    private $userModel;
    private $jwtService;
    
    public function __construct()
    {
        $this->userModel = new User();
        $this->jwtService = new JWTService();
    }
    
    /**
     * Login de usuario
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $email = $request->input('email');
            $password = $request->input('password');
            
            // Validar campos requeridos
            if (!$email || !$password) {
                return response()->noContent(400);
            }
            
            // Buscar usuario por email
            $user = $this->userModel->findByEmail($email);
            
            if (!$user) {
                return response()->noContent(401);
            }
            
            // Verificar contraseña
            if (!$this->userModel->verifyPassword($password, $user['contrasena'])) {
                return response()->noContent(401);
            }
            
            // Obtener permisos del usuario
            $permissions = $this->userModel->getUserPermissions($user['id']);
            
            // Generar JWT
            $payload = [
                'user_id' => $user['id'],
                'email' => $user['correo'],
                'rol' => $user['rol_nombre'],
                'permissions' => $permissions
            ];
            
            $token = $this->jwtService->generateToken($payload);
            
            // Actualizar último login
            $this->userModel->updateLastLogin($user['id']);
            
            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'nombre' => $user['nombre'],
                    'apellido' => $user['apellido'],
                    'email' => $user['correo'],
                    'rol' => $user['rol_nombre'],
                    'permissions' => $permissions
                ]
            ]);
            
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Registro de usuario
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            // Validar campos requeridos
            $required = ['nombre', 'apellido', 'correo', 'ci', 'contrasena', 'idrol'];
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
            
            // Crear usuario
            $userId = $this->userModel->create($data);
            
            return response()->json(['user_id' => $userId]);
            
        } catch (Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Validar token
     */
    public function validateToken(Request $request): JsonResponse
    {
        try {
            $authHeader = $request->header('Authorization');
            $token = $this->jwtService->extractTokenFromHeader($authHeader);
            $payload = $this->jwtService->validateToken($token);
            
            return response()->json($payload);
            
        } catch (Exception $e) {
            return response()->noContent(401);
        }
    }
    
    /**
     * Refrescar token
     */
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $authHeader = $request->header('Authorization');
            $token = $this->jwtService->extractTokenFromHeader($authHeader);
            $newToken = $this->jwtService->refreshToken($token);
            
            return response()->json(['token' => $newToken]);
            
        } catch (Exception $e) {
            return response()->noContent(401);
        }
    }
    
    /**
     * Logout (invalidar token del lado cliente)
     */
    public function logout(Request $request): JsonResponse
    {
        return response()->noContent();
    }
}
