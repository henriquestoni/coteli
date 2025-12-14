<?php

namespace App\Core;

/**
 * Helper de autenticação e autorização.
 * Preparado para futuras integrações (Google OAuth, WebAuthn) via métodos/strategies extras.
 */
class Auth
{
    public static function login(array $user): void
    {
        $_SESSION['user'] = $user;
        unset($_SESSION['pending_user']);
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function check(): bool
    {
        return !empty($_SESSION['user']);
    }

    public static function pendingUser(): ?array
    {
        return $_SESSION['pending_user'] ?? null;
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * @param bool $ignorarPrimeiroAcesso Quando true, permite acessar a rota de primeiro acesso sem redirecionar em loop.
     */
    public static function requireLogin(bool $ignorarPrimeiroAcesso = false): void
    {
        if (!self::check()) {
            $pending = self::pendingUser();
            $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
            $primeiroAcessoPath = url('usuarios/primeiro-acesso');
            if ($pending && str_starts_with($currentPath, $primeiroAcessoPath)) {
                // permite acessar apenas a rota de primeiro acesso com pending_user
                return;
            }
            // se tem pending, redireciona para primeiro acesso; senão, para login
            if ($pending) {
                header('Location: ' . $primeiroAcessoPath);
            } else {
                header('Location: ' . url('login'));
            }
            exit;
        }

        $user = self::user();
        // se houver pending_user, força primeiro acesso
        if ($user) {
            $pending = self::pendingUser();
            if ($pending && !$ignorarPrimeiroAcesso) {
                $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
                $primeiroAcessoPath = url('usuarios/primeiro-acesso');
                if (!str_starts_with($currentPath, $primeiroAcessoPath)) {
                    header('Location: ' . $primeiroAcessoPath);
                    exit;
                }
            }
        }

        $needsChange = false;
        if ($user) {
            $needsChange = !empty($user['precisa_trocar_senha'])
                || (!empty($user['trocar_senha']) && (int)$user['trocar_senha'] === 1)
                || empty($user['senha_hash']);
        }

        if (!$ignorarPrimeiroAcesso && $needsChange) {
            $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
            $primeiroAcessoPath = url('usuarios/primeiro-acesso');
            // Evita redirecionar quando já está na rota de primeiro acesso
            if (!str_starts_with($currentPath, $primeiroAcessoPath)) {
                header('Location: ' . $primeiroAcessoPath);
                exit;
            }
        }
    }

    public static function requireLevel(int $nivelMinimo): void
    {
        self::requireLogin();
        $user = self::user();
        if (!$user || (int)$user['nivel_acesso'] < $nivelMinimo) {
            http_response_code(403);
            $view = BASE_PATH . '/app/Views/errors/403.php';
            if (is_file($view)) {
                include $view;
            } else {
                echo 'Acesso negado.';
            }
            exit;
        }
    }

    // Pontos de extensão futura:
    // - login via Google OAuth2: implementar um provider que retorne perfil + email verificado.
    // - biometria/WebAuthn: armazenar chaves públicas associadas ao usuário e validar desafio.
}
