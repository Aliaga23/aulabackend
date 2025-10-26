<?php

namespace App\Models;

use App\Services\DatabaseService;
use Exception;

class Asignacion
{
    private $db;

    public function __construct()
    {
        $this->db = new DatabaseService();
    }

    // Asignar un docente a una materia y grupo en una gestión
    public function asignarDocenteMateria(array $data)
    {
        try {
            // Validaciones mínimas
            $required = ['coddocente','idgrupo','idcarrera','sigla','idgestion'];
            foreach ($required as $f) {
                if (!isset($data[$f])) {
                    throw new Exception('Faltan datos requeridos');
                }
            }

            // Asegurar que exista entrada en grupomateria
            $sqlGrupoMat = "INSERT INTO grupomateria (idgrupo, idcarrera, sigla) VALUES ($1, $2, $3)
                             ON CONFLICT (idgrupo, idcarrera, sigla) DO NOTHING";
            $this->db->query($sqlGrupoMat, [ $data['idgrupo'], $data['idcarrera'], $data['sigla'] ]);

            // Insertar asignación en docentegrupomateria
            $sqlAsign = "INSERT INTO docentegrupomateria (coddocente, idgrupo, idcarrera, sigla, idgestion, idinfraestructura, id)
                          VALUES ($1, $2, $3, $4, $5, $6, $7)
                          ON CONFLICT (coddocente, idgrupo, idcarrera, sigla, idgestion) DO NOTHING
                          RETURNING coddocente, idgrupo, idcarrera, sigla, idgestion, idinfraestructura, id";

            $params = [
                $data['coddocente'],
                $data['idgrupo'],
                $data['idcarrera'],
                $data['sigla'],
                $data['idgestion'],
                $data['idinfraestructura'] ?? null,
                $data['idhorario'] ?? null
            ];

            $result = $this->db->query($sqlAsign, $params);
            $row = $this->db->fetchOne($result);

            // Si no hay RETURNING por conflicto, devolver los datos enviados
            if (!$row) {
                $row = [
                    'coddocente' => (string)$data['coddocente'],
                    'idgrupo' => (string)$data['idgrupo'],
                    'idcarrera' => (string)$data['idcarrera'],
                    'sigla' => $data['sigla'],
                    'idgestion' => (string)$data['idgestion'],
                    'idinfraestructura' => isset($data['idinfraestructura']) ? (string)$data['idinfraestructura'] : null,
                    'id' => isset($data['idhorario']) ? (string)$data['idhorario'] : null
                ];
            }

            return $row;
        } catch (Exception $e) {
            throw new Exception('Error al asignar docente a materia: ' . $e->getMessage());
        }
    }
}
