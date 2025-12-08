<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;
use PDOException;

class UserModel extends BaseModel
{
    public function listAll(): array
    {
        $sql = 'SELECT * FROM usuarios ORDER BY nome_completo';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE id_usuarios = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findForAuth(string $loginOuEmail): ?array
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE (login = :login OR email = :login) AND senha_hash IS NOT NULL AND ativo = 1 LIMIT 1');
            $stmt->execute(['login' => $loginOuEmail]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && !isset($result['id']) && isset($result['id_usuarios'])) {
                $result['id'] = $result['id_usuarios'];
            }
            return $result ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function findPrecadastradoSemAcesso(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email AND login IS NULL AND senha_hash IS NULL LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createPreCadastro(array $data): int
    {
        $data = $this->normalizarFlags($data);
        $stmt = $this->db->prepare(
            'INSERT INTO usuarios (nome_completo, email, login, senha_hash, nivel_acesso, is_pregoeiro, is_responsavel_coteli, ativo, precisa_trocar_senha, criado_em, atualizado_em)
             VALUES (:nome_completo, :email, NULL, NULL, NULL, :is_pregoeiro, :is_responsavel_coteli, :ativo, 0, NOW(), NOW())'
        );
        $stmt->execute([
            'nome_completo' => $data['nome_completo'],
            'email' => $data['email'],
            'is_pregoeiro' => $data['is_pregoeiro'],
            'is_responsavel_coteli' => $data['is_responsavel_coteli'],
            'ativo' => $data['ativo'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updatePreCadastro(int $id, array $data): void
    {
        $data = $this->normalizarFlags($data);
        $stmt = $this->db->prepare(
            'UPDATE usuarios
             SET nome_completo = :nome_completo,
                 email = :email,
                 is_pregoeiro = :is_pregoeiro,
                 is_responsavel_coteli = :is_responsavel_coteli,
                 ativo = :ativo,
                 atualizado_em = NOW()
             WHERE id_usuarios = :id'
        );
        $stmt->execute([
            'nome_completo' => $data['nome_completo'],
            'email' => $data['email'],
            'is_pregoeiro' => $data['is_pregoeiro'],
            'is_responsavel_coteli' => $data['is_responsavel_coteli'],
            'ativo' => $data['ativo'],
            'id' => $id,
        ]);
    }

    /**
     * Gera acesso para usuário novo ou já pré-cadastrado (login/senha nulos).
     * Retorna array com ['usuario' => array, 'senha_provisoria' => string].
     */
    public function gerarAcesso(array $data): array
    {
        $email = trim((string)($data['email'] ?? ''));
        $nome = trim((string)($data['nome_completo'] ?? ''));
        $nivel = isset($data['nivel_acesso']) ? (int)$data['nivel_acesso'] : null;
        $loginSugestao = trim((string)($data['login'] ?? ''));

        $flags = $this->normalizarFlags($data);
        $flags['ativo'] = 1;

        $usuarioExistente = $this->findPrecadastradoSemAcesso($email);
        $userId = null;

        if ($usuarioExistente) {
            $userId = (int)$usuarioExistente['id_usuarios'];
            $nome = $nome ?: $usuarioExistente['nome_completo'];
        }

        $loginDefinido = $this->gerarLoginUnico($loginSugestao ?: $email, $userId);
        $senhaProvisoria = $this->gerarSenhaProvisoria();
        $senhaHash = password_hash($senhaProvisoria, PASSWORD_DEFAULT);
        $nivelAcesso = $nivel ?? 1;

        if ($userId) {
            $stmt = $this->db->prepare(
                'UPDATE usuarios
                 SET nome_completo = :nome_completo,
                     login = :login,
                     senha_hash = :senha_hash,
                     nivel_acesso = :nivel_acesso,
                     is_pregoeiro = :is_pregoeiro,
                     is_responsavel_coteli = :is_responsavel_coteli,
                     ativo = 1,
                     precisa_trocar_senha = 1,
                     atualizado_em = NOW()
                 WHERE id_usuarios = :id'
            );
            $stmt->execute([
                'nome_completo' => $nome,
                'login' => $loginDefinido,
                'senha_hash' => $senhaHash,
                'nivel_acesso' => $nivelAcesso,
                'is_pregoeiro' => $flags['is_pregoeiro'],
                'is_responsavel_coteli' => $flags['is_responsavel_coteli'],
                'id' => $userId,
            ]);
        } else {
            $stmt = $this->db->prepare(
                'INSERT INTO usuarios (nome_completo, email, login, senha_hash, nivel_acesso, is_pregoeiro, is_responsavel_coteli, ativo, precisa_trocar_senha, criado_em, atualizado_em)
                 VALUES (:nome_completo, :email, :login, :senha_hash, :nivel_acesso, :is_pregoeiro, :is_responsavel_coteli, 1, 1, NOW(), NOW())'
            );
            $stmt->execute([
                'nome_completo' => $nome,
                'email' => $email,
                'login' => $loginDefinido,
                'senha_hash' => $senhaHash,
                'nivel_acesso' => $nivelAcesso,
                'is_pregoeiro' => $flags['is_pregoeiro'],
                'is_responsavel_coteli' => $flags['is_responsavel_coteli'],
            ]);
            $userId = (int)$this->db->lastInsertId();
        }

        $usuario = $this->findById($userId);
        $this->registrarConviteLog($usuario, $loginDefinido, $senhaProvisoria);

        return [
            'usuario' => $usuario,
            'senha_provisoria' => $senhaProvisoria,
        ];
    }

    public function atualizarSenhaDefinitiva(int $id, string $novaSenha, ?string $novoLogin = null): void
    {
        $login = null;
        if ($novoLogin !== null && trim($novoLogin) !== '') {
            $login = $this->gerarLoginUnico(trim($novoLogin), $id);
        }

        $sql = 'UPDATE usuarios SET senha_hash = :senha_hash, precisa_trocar_senha = 0, atualizado_em = NOW()';
        $params = [
            'senha_hash' => password_hash($novaSenha, PASSWORD_DEFAULT),
            'id' => $id,
        ];

        if ($login !== null) {
            $sql .= ', login = :login';
            $params['login'] = $login;
        }

        $sql .= ' WHERE id_usuarios = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function getPregoeiros(): array
    {
        $stmt = $this->db->query('SELECT id_usuarios, nome_completo FROM usuarios WHERE is_pregoeiro = 1 AND ativo = 1 ORDER BY nome_completo');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getResponsaveisCoteli(): array
    {
        $stmt = $this->db->query('SELECT id_usuarios, nome_completo FROM usuarios WHERE is_responsavel_coteli = 1 AND ativo = 1 ORDER BY nome_completo');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function gerarLoginUnico(string $base, ?int $ignoreId = null): string
    {
        $base = strtolower(trim($base));
        $base = $base !== '' ? preg_replace('/[^a-z0-9._-]/', '', explode('@', $base)[0]) : 'user';
        $login = $base ?: 'user';
        $sufixo = 1;

        while ($this->loginExiste($login, $ignoreId)) {
            $login = $base . $sufixo;
            $sufixo++;
        }

        return $login;
    }

    private function loginExiste(string $login, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM usuarios WHERE login = :login';
        $params = ['login' => $login];
        if ($ignoreId !== null) {
            $sql .= ' AND id_usuarios <> :id';
            $params['id'] = $ignoreId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return ((int)$stmt->fetchColumn()) > 0;
    }

    private function gerarSenhaProvisoria(): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($chars);
        $senha = '';
        for ($i = 0; $i < 10; $i++) {
            $senha .= $chars[random_int(0, $len - 1)];
        }
        return $senha;
    }

    private function registrarConviteLog(?array $usuario, string $login, string $senhaProvisoria): void
    {
        $linha = sprintf(
            "[%s] Nome: %s | Email: %s | Login: %s | Senha provisoria: %s%s",
            date('Y-m-d H:i:s'),
            $usuario['nome_completo'] ?? '-',
            $usuario['email'] ?? '-',
            $login,
            $senhaProvisoria,
            PHP_EOL
        );

        $logDir = BASE_PATH . '/storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/convites_usuarios.log';
        @file_put_contents($logFile, $linha, FILE_APPEND | LOCK_EX);
    }

    private function normalizarFlags(array $data): array
    {
        $isResp = !empty($data['is_responsavel_coteli']) ? 1 : 0;
        $isPreg = !empty($data['is_pregoeiro']) ? 1 : 0;
        if ($isResp) {
            $isPreg = 1; // responsável implica pregoeiro
        }
        $data['is_responsavel_coteli'] = $isResp;
        $data['is_pregoeiro'] = $isPreg;
        $data['ativo'] = isset($data['ativo']) ? (int)!empty($data['ativo']) : 1;
        return $data;
    }
}
