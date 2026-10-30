<?php
/**
 * Sessão PHP: configuração segura, login e logout.
 */
declare(strict_types=1);

const SESSAO_NOME = 'ECOSPOTSESSID';

/**
 * Caminho da URL da pasta do projeto (ex.: /ecospot-ecoalert/), para que o
 * cookie de sessão não seja enviado aos outros sites do mesmo servidor.
 * Calculado a partir do script em execução; usa '/' se não for possível.
 */
function sessao_caminho_cookie(): string
{
    $raiz = str_replace('\\', '/', (string) realpath(__DIR__ . '/../..'));
    $arquivo = str_replace('\\', '/', (string) realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')));
    $script = (string) ($_SERVER['SCRIPT_NAME'] ?? '');

    if ($raiz === '' || strncasecmp($arquivo, $raiz . '/', strlen($raiz) + 1) !== 0) {
        return '/';
    }
    // Ex.: /backend/api/sessao.php, que é o final de SCRIPT_NAME.
    $relativo = substr($arquivo, strlen($raiz));
    if (strcasecmp(substr($script, -strlen($relativo)), $relativo) !== 0) {
        return '/';
    }
    return substr($script, 0, -strlen($relativo)) . '/';
}

/**
 * Inicia a sessão com cookie HttpOnly e SameSite=Lax.
 * Com $somenteSeExistir = true, não cria sessão para quem ainda não tem
 * o cookie (usado pela consulta de status das páginas HTML).
 */
function sessao_iniciar(bool $somenteSeExistir = false): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    if ($somenteSeExistir && !isset($_COOKIE[SESSAO_NOME])) {
        return;
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name(SESSAO_NOME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => sessao_caminho_cookie(),
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();

    // Encerra a sessão autenticada após o tempo de inatividade.
    if (isset($_SESSION['usuario_id'])) {
        $ultimo = (int) ($_SESSION['ultimo_acesso'] ?? 0);
        if (time() - $ultimo > SESSAO_INATIVIDADE) {
            sessao_encerrar();
            sessao_iniciar();
            flash_definir('aviso', 'Sua sessão expirou por inatividade. Entre novamente.');
            return;
        }
        $_SESSION['ultimo_acesso'] = time();
    }
}

/** Dados do usuário autenticado ou null. */
function usuario_atual(): ?array
{
    if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['usuario_id'])) {
        return null;
    }
    return [
        'usuario_id' => (int) $_SESSION['usuario_id'],
        'nome'       => (string) $_SESSION['usuario_nome'],
        'papel'      => (string) $_SESSION['usuario_papel'],
    ];
}

/** Marca o usuário como autenticado, trocando o ID da sessão. */
function sessao_autenticar(array $usuario): void
{
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $usuario['usuario_id'];
    $_SESSION['usuario_nome'] = (string) $usuario['nome'];
    $_SESSION['usuario_papel'] = (string) $usuario['papel'];
    $_SESSION['ultimo_acesso'] = time();
    csrf_renovar();
}

/** Apaga os dados e o cookie da sessão. */
function sessao_encerrar(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => $p['samesite'],
        ]);
    }
    session_destroy();
}

/** Exige login; caso contrário, envia para a página de entrada. */
function exigir_login(): array
{
    $usuario = usuario_atual();
    if ($usuario === null) {
        flash_definir('aviso', 'Entre na sua conta para acessar esta página.');
        redirecionar('login.php');
    }
    return $usuario;
}
