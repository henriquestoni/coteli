<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;
use Exception;

class PregaoDuplicadoException extends Exception
{
}

class PregaoModel extends BaseModel
{
    public function getListaPregoes(): array
    {
        $sql = <<<SQL
            SELECT
                b.id_base_pregoes,
                tp.sigla_tipos_pregao AS tipo_sigla,
                b.ano_pregao,
                b.id_pregao,
                b.id_pregao_repeticao,
                b.data_pregao,
                b.hora_pregao,
                op.unidade_origem,
                b.processo_sei,
                b.objeto_licitado,
                b.data_do,
                sp.nome_status_pregao,
                b.lancado_site_uerj,
                u1.nome_completo AS nome_pregoeiro,
                u2.nome_completo AS nome_responsavel_coteli
            FROM base_pregoes b
            LEFT JOIN tipos_pregao    tp ON tp.id_tipos_pregao   = b.id_tipo_pregao
            LEFT JOIN origens_pedido  op ON op.id_origens_pedido = b.id_origem_pedido
            LEFT JOIN status_pregao   sp ON sp.id_status_pregao  = b.id_status
            LEFT JOIN usuarios        u1 ON u1.id_usuarios       = b.id_pregoeiro
            LEFT JOIN usuarios        u2 ON u2.id_usuarios       = b.id_responsavel_coteli
            ORDER BY
                b.ano_pregao DESC,
                b.id_pregao  DESC,
                b.id_pregao_repeticao ASC
            LIMIT 50
        SQL;

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getTiposPregao(): array
    {
        $stmt = $this->db->query('SELECT id_tipos_pregao, nome_tipos_pregao, sigla_tipos_pregao FROM tipos_pregao ORDER BY id_tipos_pregao');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getOrigensPedido(): array
    {
        $stmt = $this->db->query('SELECT id_origens_pedido, unidade_origem FROM origens_pedido ORDER BY unidade_origem');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getStatusPregao(): array
    {
        $stmt = $this->db->query('SELECT id_status_pregao, nome_status_pregao FROM status_pregao ORDER BY id_status_pregao');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getPregoeiros(): array
    {
        $stmt = $this->db->query("SELECT id_usuarios, nome_completo FROM usuarios WHERE is_pregoeiro = 1 AND ativo = 1 ORDER BY nome_completo");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getResponsaveisCoteli(): array
    {
        $stmt = $this->db->query("SELECT id_usuarios, nome_completo FROM usuarios WHERE is_responsavel_coteli = 1 AND ativo = 1 ORDER BY nome_completo");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getBasesR0(): array
    {
        $sql = <<<SQL
            SELECT
                b.id_base_pregoes,
                b.id_tipo_pregao,
                b.ano_pregao,
                b.id_pregao,
                tp.sigla_tipos_pregao,
                tp.nome_tipos_pregao
            FROM base_pregoes b
            LEFT JOIN tipos_pregao tp ON tp.id_tipos_pregao = b.id_tipo_pregao
            WHERE b.id_pregao_repeticao = 0
            ORDER BY b.ano_pregao DESC, b.id_pregao DESC
        SQL;
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getBaseById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM base_pregoes WHERE id_base_pregoes = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getProximaRepeticao(int $idTiposPregao, int $anoPregao, string $idPregao): int
    {
        $stmt = $this->db->prepare('SELECT MAX(id_pregao_repeticao) AS max_rep FROM base_pregoes WHERE id_tipo_pregao = :tipo AND ano_pregao = :ano AND id_pregao = :num');
        $stmt->execute([
            'tipo' => $idTiposPregao,
            'ano'  => $anoPregao,
            'num'  => $idPregao,
        ]);
        $max = (int)($stmt->fetchColumn() ?: 0);
        return $max + 1;
    }

    public function createBaseR0(array $dados): int
    {
        $dados['id_pregao_repeticao'] = 0;

        if ($this->existeDuplicado($dados)) {
            throw new PregaoDuplicadoException('Já existe um pregão com este tipo, ano, número e repetição.');
        }

        $sql = <<<SQL
            INSERT INTO base_pregoes (
                id_tipo_pregao,
                ano_pregao,
                id_pregao,
                id_pregao_repeticao,
                data_pregao,
                hora_pregao,
                id_status,
                id_origem_pedido,
                processo_sei,
                objeto_licitado,
                data_do,
                id_pregoeiro,
                id_responsavel_coteli,
                lancado_site_uerj,
                created_at,
                updated_at
            ) VALUES (
                :id_tipo_pregao,
                :ano_pregao,
                :id_pregao,
                :id_pregao_repeticao,
                :data_pregao,
                :hora_pregao,
                :id_status,
                :id_origem_pedido,
                :processo_sei,
                :objeto_licitado,
                :data_do,
                :id_pregoeiro,
                :id_responsavel_coteli,
                :lancado_site_uerj,
                NOW(),
                NOW()
            )
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_tipo_pregao'        => $dados['id_tipo_pregao'],
            'ano_pregao'            => $dados['ano_pregao'],
            'id_pregao'             => $dados['id_pregao'],
            'id_pregao_repeticao'   => 0,
            'data_pregao'           => $dados['data_pregao'] ?: null,
            'hora_pregao'           => $dados['hora_pregao'] ?: null,
            'id_status'             => $dados['id_status'],
            'id_origem_pedido'      => $dados['id_origem_pedido'],
            'processo_sei'          => $dados['processo_sei'],
            'objeto_licitado'       => $dados['objeto_licitado'],
            'data_do'               => $dados['data_do'] ?: null,
            'id_pregoeiro'          => $this->filtrarPessoaParaFk($dados['id_pregoeiro'] ?? null, 'pregoeiros'),
            'id_responsavel_coteli' => $this->filtrarPessoaParaFk($dados['id_responsavel_coteli'] ?? null, 'responsaveis_coteli'),
            'lancado_site_uerj'     => $dados['lancado_site_uerj'] ?? 0,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function createRepeticao(array $dados): int
    {
        $dados['id_pregao_repeticao'] = max(1, (int)($dados['id_pregao_repeticao'] ?? 1));

        if ($this->existeDuplicado($dados)) {
            throw new PregaoDuplicadoException('Já existe uma repetição com este tipo, ano, número e repetição.');
        }

        $sql = <<<SQL
            INSERT INTO base_pregoes (
                id_tipo_pregao,
                ano_pregao,
                id_pregao,
                id_pregao_repeticao,
                data_pregao,
                hora_pregao,
                id_status,
                id_origem_pedido,
                processo_sei,
                objeto_licitado,
                data_do,
                id_pregoeiro,
                id_responsavel_coteli,
                lancado_site_uerj,
                created_at,
                updated_at
            ) VALUES (
                :id_tipo_pregao,
                :ano_pregao,
                :id_pregao,
                :id_pregao_repeticao,
                :data_pregao,
                :hora_pregao,
                :id_status,
                :id_origem_pedido,
                :processo_sei,
                :objeto_licitado,
                :data_do,
                :id_pregoeiro,
                :id_responsavel_coteli,
                :lancado_site_uerj,
                NOW(),
                NOW()
            )
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_tipo_pregao'        => $dados['id_tipo_pregao'],
            'ano_pregao'            => $dados['ano_pregao'],
            'id_pregao'             => $dados['id_pregao'],
            'id_pregao_repeticao'   => $dados['id_pregao_repeticao'],
            'data_pregao'           => $dados['data_pregao'] ?: null,
            'hora_pregao'           => $dados['hora_pregao'] ?: null,
            'id_status'             => $dados['id_status'],
            'id_origem_pedido'      => $dados['id_origem_pedido'],
            'processo_sei'          => $dados['processo_sei'],
            'objeto_licitado'       => $dados['objeto_licitado'],
            'data_do'               => $dados['data_do'] ?: null,
            'id_pregoeiro'          => $this->filtrarPessoaParaFk($dados['id_pregoeiro'] ?? null, 'pregoeiros'),
            'id_responsavel_coteli' => $this->filtrarPessoaParaFk($dados['id_responsavel_coteli'] ?? null, 'responsaveis_coteli'),
            'lancado_site_uerj'     => $dados['lancado_site_uerj'] ?? 0,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updatePregao(int $id, array $dados): void
    {
        if ($this->existeDuplicado($dados, $id)) {
            throw new PregaoDuplicadoException('Já existe pregão com esta combinação de tipo, ano, número e repetição.');
        }

        $sql = <<<SQL
            UPDATE base_pregoes SET
                id_tipo_pregao        = :id_tipo_pregao,
                ano_pregao            = :ano_pregao,
                id_pregao             = :id_pregao,
                id_pregao_repeticao   = :id_pregao_repeticao,
                data_pregao           = :data_pregao,
                hora_pregao           = :hora_pregao,
                id_status             = :id_status,
                id_origem_pedido      = :id_origem_pedido,
                processo_sei          = :processo_sei,
                objeto_licitado       = :objeto_licitado,
                data_do               = :data_do,
                id_pregoeiro          = :id_pregoeiro,
                id_responsavel_coteli = :id_responsavel_coteli,
                lancado_site_uerj     = :lancado_site_uerj,
                updated_at            = NOW()
            WHERE id_base_pregoes = :id
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_tipo_pregao'        => $dados['id_tipo_pregao'],
            'ano_pregao'            => $dados['ano_pregao'],
            'id_pregao'             => $dados['id_pregao'],
            'id_pregao_repeticao'   => $dados['id_pregao_repeticao'],
            'data_pregao'           => $dados['data_pregao'] ?: null,
            'hora_pregao'           => $dados['hora_pregao'] ?: null,
            'id_status'             => $dados['id_status'],
            'id_origem_pedido'      => $dados['id_origem_pedido'],
            'processo_sei'          => $dados['processo_sei'],
            'objeto_licitado'       => $dados['objeto_licitado'],
            'data_do'               => $dados['data_do'] ?: null,
            'id_pregoeiro'          => $this->filtrarPessoaParaFk($dados['id_pregoeiro'] ?? null, 'pregoeiros'),
            'id_responsavel_coteli' => $this->filtrarPessoaParaFk($dados['id_responsavel_coteli'] ?? null, 'responsaveis_coteli'),
            'lancado_site_uerj'     => $dados['lancado_site_uerj'] ?? 0,
            'id'                    => $id,
        ]);
    }

    public function existeDuplicado(array $dados, ?int $ignoreId = null): bool
    {
        return $this->buscarPorChave($dados, $ignoreId) !== null;
    }

    public function buscarPorChave(array $dados, ?int $ignoreId = null): ?array
    {
        $sql = <<<SQL
            SELECT
                b.*,
                tp.sigla_tipos_pregao
            FROM base_pregoes b
            LEFT JOIN tipos_pregao tp ON tp.id_tipos_pregao = b.id_tipo_pregao
            WHERE b.id_tipo_pregao = :tipo
              AND b.ano_pregao = :ano
              AND b.id_pregao = :num
              AND b.id_pregao_repeticao = :rep
        SQL;
        $params = [
            'tipo' => $dados['id_tipo_pregao'],
            'ano'  => $dados['ano_pregao'],
            'num'  => $dados['id_pregao'],
            'rep'  => $dados['id_pregao_repeticao'],
        ];

        if ($ignoreId !== null) {
            $sql .= ' AND b.id_base_pregoes <> :ignore';
            $params['ignore'] = $ignoreId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getMaxNumeroBase(int $idTipoPregao, int $anoPregao): int
    {
        $stmt = $this->db->prepare('SELECT MAX(id_pregao) FROM base_pregoes WHERE id_tipo_pregao = :tipo AND ano_pregao = :ano AND id_pregao_repeticao = 0');
        $stmt->execute(['tipo' => $idTipoPregao, 'ano' => $anoPregao]);
        return (int)($stmt->fetchColumn() ?: 0);
    }

    public function getMaxRepeticao(int $idTipoPregao, int $anoPregao, string $idPregao): int
    {
        $stmt = $this->db->prepare('SELECT MAX(id_pregao_repeticao) FROM base_pregoes WHERE id_tipo_pregao = :tipo AND ano_pregao = :ano AND id_pregao = :num AND id_pregao_repeticao > 0');
        $stmt->execute(['tipo' => $idTipoPregao, 'ano' => $anoPregao, 'num' => $idPregao]);
        return (int)($stmt->fetchColumn() ?: 0);
    }

    public function getUltimosBaseR0(int $limite = 5): array
    {
        $sql = <<<SQL
            SELECT
                b.id_base_pregoes,
                b.id_pregao,
                b.ano_pregao,
                b.data_pregao,
                b.hora_pregao,
                tp.sigla_tipos_pregao,
                op.unidade_origem,
                sp.nome_status_pregao
            FROM base_pregoes b
            LEFT JOIN tipos_pregao   tp ON tp.id_tipos_pregao   = b.id_tipo_pregao
            LEFT JOIN origens_pedido op ON op.id_origens_pedido = b.id_origem_pedido
            LEFT JOIN status_pregao  sp ON sp.id_status_pregao  = b.id_status
            WHERE b.id_pregao_repeticao = 0
            ORDER BY b.id_base_pregoes DESC
            LIMIT :lim
        SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    
    public function getAgendaProximos(int $limite = 8, int $userId = 0, bool $apenasDoUsuario = true): array
    {
        // Busca prioritariamente nos proximos 7 dias; se vazio, devolve os 5 proximos.
        $params = [];
        $whereUsuario = '';
        if ($apenasDoUsuario && $userId > 0) {
            $whereUsuario = ' AND (b.id_pregoeiro = :uid OR b.id_responsavel_coteli = :uid)';
            $params['uid'] = $userId;
        }

        $sqlBase = <<<SQL
            SELECT
                b.id_base_pregoes,
                b.id_tipo_pregao,
                b.ano_pregao,
                b.id_pregao,
                b.id_pregao_repeticao,
                b.data_pregao,
                b.hora_pregao,
                b.objeto_licitado,
                b.processo_sei,
                tp.sigla_tipos_pregao AS tipo_sigla,
                u1.nome_completo AS nome_pregoeiro,
                u2.nome_completo AS nome_responsavel
            FROM base_pregoes b
            LEFT JOIN tipos_pregao tp ON tp.id_tipos_pregao = b.id_tipo_pregao
            LEFT JOIN usuarios u1 ON u1.id_usuarios = b.id_pregoeiro
            LEFT JOIN usuarios u2 ON u2.id_usuarios = b.id_responsavel_coteli
            WHERE 1=1
        SQL;

        // Primeiro: proximos 7 dias
        $sql7 = $sqlBase . " AND b.data_pregao BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) {$whereUsuario} ORDER BY b.data_pregao ASC, b.hora_pregao ASC LIMIT :lim";
        $stmt = $this->db->prepare($sql7);
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v, PDO::PARAM_INT);
        }
        $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        if (!empty($result)) {
            return $result;
        }

        // Fallback: proximos N (padrao 5)
        $fallbackLimite = min($limite, 5);
        $sqlNext = $sqlBase . " AND b.data_pregao >= CURDATE() {$whereUsuario} ORDER BY b.data_pregao ASC, b.hora_pregao ASC LIMIT :lim";
        $stmt = $this->db->prepare($sqlNext);
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v, PDO::PARAM_INT);
        }
        $stmt->bindValue(':lim', $fallbackLimite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    /**
     * Compatibilidade: se o banco ainda estiver com FK apontando para tabelas pregoeiros/responsaveis_coteli,
     * validamos a existência; se não existir ou tabela não estiver presente, retornamos null para evitar erro de FK.
     */
    private function filtrarPessoaParaFk($id, string $tabela): ?int
    {
        $id = (int)$id;
        if ($id <= 0) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$tabela} WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $existe = (int)$stmt->fetchColumn() > 0;
            return $existe ? $id : null;
        } catch (\Throwable $e) {
            // Se a tabela não existir (cenário atualizado para usar usuarios), mantemos o ID.
            return $id;
        }
    }
}



