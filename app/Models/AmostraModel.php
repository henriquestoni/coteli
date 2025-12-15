<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class AmostraModel extends BaseModel
{
    public function listarAmostrasPorPregao(int $idBasePregao, array $filtros = []): array
    {
        $sql = <<<SQL
            SELECT a.*, u.nome_completo AS nome_responsavel, e.nome_empresas AS nome_empresa, tp.nome_tipos_parecer
            FROM amostras a
            LEFT JOIN usuarios u ON u.id_usuarios = a.id_responsavel
            LEFT JOIN empresas e ON e.id_empresas = a.id_empresa
            LEFT JOIN tipos_parecer tp ON tp.id_tipos_parecer = a.id_tipos_parecer
            WHERE a.id_base_amostra = :id
            ORDER BY a.item_licitado ASC, a.id_amostras DESC
        SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $idBasePregao]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function inserirAmostra(array $dados): array
    {
        $dup = $this->buscarDuplicidade($dados['id_base_amostra'], $dados['item_licitado'], $dados['id_tipos_parecer']);
        if ($dup) {
            return ['erro' => 'duplicidade', 'existente' => $dup];
        }

        $sql = <<<SQL
            INSERT INTO amostras (
                id_base_amostra, id_momento_cadastro, item_licitado, id_tipos_licitados, total_unidades,
                id_responsavel, id_empresa, observacoes, entregue_coteli, chegada_coteli, saida_coteli,
                id_tipos_parecer, data_parecer, data_cadastro
            ) VALUES (
                :id_base_amostra, :id_momento_cadastro, :item_licitado, :id_tipos_licitados, :total_unidades,
                :id_responsavel, :id_empresa, :observacoes, :entregue_coteli, :chegada_coteli, :saida_coteli,
                :id_tipos_parecer, :data_parecer, NOW()
            )
        SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_base_amostra'     => $dados['id_base_amostra'],
            'id_momento_cadastro' => $dados['id_momento_cadastro'],
            'item_licitado'       => $dados['item_licitado'],
            'id_tipos_licitados'  => $dados['id_tipos_licitados'],
            'total_unidades'      => $dados['total_unidades'],
            'id_responsavel'      => $dados['id_responsavel'],
            'id_empresa'          => $dados['id_empresa'],
            'observacoes'         => $dados['observacoes'],
            'entregue_coteli'     => $dados['entregue_coteli'],
            'chegada_coteli'      => $dados['chegada_coteli'],
            'saida_coteli'        => $dados['saida_coteli'],
            'id_tipos_parecer'    => $dados['id_tipos_parecer'],
            'data_parecer'        => $dados['data_parecer'],
        ]);

        return ['id' => (int)$this->db->lastInsertId()];
    }

    public function atualizarAmostra(int $idBaseAmostras, array $dados): array
    {
        $dup = $this->buscarDuplicidade($dados['id_base_amostra'], $dados['item_licitado'], $dados['id_tipos_parecer'], $idBaseAmostras);
        if ($dup) {
            return ['erro' => 'duplicidade', 'existente' => $dup];
        }

        $sql = <<<SQL
            UPDATE amostras SET
                id_base_amostra = :id_base_amostra,
                item_licitado = :item_licitado,
                id_tipos_licitados = :id_tipos_licitados,
                total_unidades = :total_unidades,
                id_responsavel = :id_responsavel,
                id_empresa = :id_empresa,
                observacoes = :observacoes,
                entregue_coteli = :entregue_coteli,
                chegada_coteli = :chegada_coteli,
                saida_coteli = :saida_coteli,
                id_tipos_parecer = :id_tipos_parecer,
                data_parecer = :data_parecer
            WHERE id_amostras = :id
        SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_base_amostra' => $dados['id_base_amostra'],
            'item_licitado'   => $dados['item_licitado'],
            'id_tipos_licitados' => $dados['id_tipos_licitados'],
            'total_unidades'  => $dados['total_unidades'],
            'id_responsavel'  => $dados['id_responsavel'],
            'id_empresa'      => $dados['id_empresa'],
            'observacoes'     => $dados['observacoes'],
            'entregue_coteli' => $dados['entregue_coteli'],
            'chegada_coteli'  => $dados['chegada_coteli'],
            'saida_coteli'    => $dados['saida_coteli'],
            'id_tipos_parecer' => $dados['id_tipos_parecer'],
            'data_parecer'    => $dados['data_parecer'],
            'id'              => $idBaseAmostras,
        ]);

        return ['id' => $idBaseAmostras];
    }

    public function removerAmostra(int $idBaseAmostras): void
    {
        $stmt = $this->db->prepare('DELETE FROM amostras WHERE id_amostras = :id');
        $stmt->execute(['id' => $idBaseAmostras]);
    }

    public function listarTiposLicitados(): array
    {
        $stmt = $this->db->query('SELECT id_tipos_licitados, nome_tipos_licitados FROM tipos_licitados ORDER BY nome_tipos_licitados');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function listarTiposParecer(): array
    {
        $stmt = $this->db->query('SELECT id_tipos_parecer, nome_tipos_parecer FROM tipos_parecer ORDER BY nome_tipos_parecer');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function listarResponsaveis(): array
    {
        $stmt = $this->db->query('SELECT id_usuarios, nome_completo FROM usuarios WHERE ativo_usuario = 1 ORDER BY nome_completo');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function listarEmpresas(): array
    {
        $stmt = $this->db->query('SELECT id_empresas AS id, nome_empresas AS nome, email_empresas AS email, telefone_empresas AS telefone, cnpj_empresas AS cnpj FROM empresas ORDER BY nome_empresas');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function criarEmpresa(string $nome, string $email, ?string $telefone = null, ?string $cnpj = null): int
    {
        $stmt = $this->db->prepare('INSERT INTO empresas (nome_empresas, email_empresas, telefone_empresas, cnpj_empresas, status_empresas) VALUES (:nome, :email, :telefone, :cnpj, 1)');
        $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
            'cnpj' => $cnpj,
        ]);
        return (int)$this->db->lastInsertId();
    }

    private function buscarDuplicidade(int $idBasePregao, string $item, int $idTipoParecer, ?int $ignoreId = null): ?array
    {
        $sql = 'SELECT * FROM amostras WHERE id_base_amostra = :id_base_amostra AND item_licitado = :item AND id_tipos_parecer = :parecer';
        $params = [
            'id_base_amostra' => $idBasePregao,
            'item' => $item,
            'parecer' => $idTipoParecer,
        ];
        if ($ignoreId !== null) {
            $sql .= ' AND id_amostras <> :id';
            $params['id'] = $ignoreId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
