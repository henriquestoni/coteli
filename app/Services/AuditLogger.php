<?php

namespace App\Services;

use App\Core\BaseModel;
use PDOException;

/**
 * Serviço de auditoria para registrar ações sensíveis (CRUD e leituras relevantes).
 */
class AuditLogger extends BaseModel
{
    public function fetchLatest(int $limit = 50): array
    {
        try {
            $stmt = $this->db->prepare('SELECT a.*, u.nome_completo FROM auditoria a LEFT JOIN usuarios u ON u.id_usuarios = a.id_usuario ORDER BY a.criado_em DESC LIMIT :lim');
            $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function logAction(
        ?int $idUsuario,
        string $acao,
        string $entidade,
        $idEntidade = null,
        $dadosAntes = null,
        $dadosDepois = null
    ): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO auditoria (
                    id_usuario, acao, entidade, id_entidade, campos_alterados, dados_anteriores, dados_novos, ip, user_agent
                ) VALUES (
                    :id_usuario, :acao, :entidade, :id_entidade, :campos, :antes, :depois, :ip, :ua
                )'
            );

            $stmt->execute([
                'id_usuario' => $idUsuario,
                'acao' => $acao,
                'entidade' => $entidade,
                'id_entidade' => $idEntidade,
                'campos' => $this->diffCampos($dadosAntes, $dadosDepois),
                'antes' => $dadosAntes ? json_encode($dadosAntes) : null,
                'depois' => $dadosDepois ? json_encode($dadosDepois) : null,
                'ip' => $ip,
                'ua' => $ua,
            ]);
        } catch (PDOException $e) {
            // Em produção, registrar em arquivo de log (storage/logs)
        }
    }

    /**
     * Retorna somente os campos alterados entre antes/depois.
     */
    private function diffCampos($antes, $depois): ?string
    {
        if (!$antes || !$depois || !is_array($antes) || !is_array($depois)) {
            return null;
        }
        $alterados = [];
        foreach ($depois as $k => $v) {
            if (!array_key_exists($k, $antes)) {
                $alterados[] = $k;
                continue;
            }
            if ($antes[$k] !== $v) {
                $alterados[] = $k;
            }
        }
        return $alterados ? json_encode($alterados) : null;
    }
}
