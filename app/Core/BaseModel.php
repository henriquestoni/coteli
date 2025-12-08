<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Modelo base responsável por entregar a conexão PDO.
 * Para criar um novo modelo:
 * - Crie a classe em app/Models/NomeModel.php estendendo BaseModel.
 * - Utilize $this->db para executar queries preparadas.
 */
class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $config = require BASE_PATH . '/config/database.php';
        $dsn = sprintf(
            '%s:host=%s;dbname=%s;charset=%s',
            $config['driver'],
            $config['host'],
            $config['database'],
            $config['charset']
        );

        try {
            $this->db = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            // Registre em log real em produção
            exit('Erro ao conectar ao banco: ' . $e->getMessage());
        }
    }
}
