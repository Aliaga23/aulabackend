<?php

namespace App\Models;

use App\Services\DatabaseService;
use Exception;

class Role
{
    private $db;

    public function __construct()
    {
        $this->db = new DatabaseService();
    }

    public function create(array $data)
    {
        try {
            $sql = "INSERT INTO roles (nombre) VALUES ($1) RETURNING idrol, nombre";
            $params = [ $data['nombre'] ];
            $result = $this->db->query($sql, $params);
            return $this->db->fetchOne($result);
        } catch (Exception $e) {
            throw new Exception('Error al crear rol: ' . $e->getMessage());
        }
    }

    public function getAll()
    {
        $sql = "SELECT idrol, nombre FROM roles ORDER BY idrol";
        $result = $this->db->query($sql);
        return $this->db->fetchAll($result);
    }

    public function findById($id)
    {
        $sql = "SELECT idrol, nombre FROM roles WHERE idrol = $1";
        $result = $this->db->query($sql, [ $id ]);
        return $this->db->fetchOne($result);
    }

    public function update($id, array $data)
    {
        try {
            $sql = "UPDATE roles SET nombre = $1 WHERE idrol = $2";
            $this->db->query($sql, [ $data['nombre'], $id ]);
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al actualizar rol: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $sql = "DELETE FROM roles WHERE idrol = $1";
            $this->db->query($sql, [ $id ]);
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al eliminar rol: ' . $e->getMessage());
        }
    }
}
