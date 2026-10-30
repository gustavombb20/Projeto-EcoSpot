<?php
/**
 * Conexão PDO com o MySQL (uma por requisição).
 */
declare(strict_types=1);

/** Lê backend/config/database.local.php. */
function db_config(): array
{
    static $config = null;
    if ($config === null) {
        $arquivo = __DIR__ . '/../config/database.local.php';
        if (!is_file($arquivo)) {
            throw new RuntimeException(
                'Arquivo backend/config/database.local.php não encontrado. '
                . 'Copie database.example.php para database.local.php.'
            );
        }
        $config = require $arquivo;
    }
    return $config;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $cfg = db_config();
    if (strcasecmp((string) $cfg['banco'], BANCO_ORIGINAL) === 0) {
        throw new RuntimeException(
            'A configuração aponta para o banco compartilhado ' . BANCO_ORIGINAL
            . '. Use o banco de desenvolvimento ecospot_dev.'
        );
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $cfg['host'],
        (int) $cfg['porta'],
        $cfg['banco']
    );

    $pdo = new PDO($dsn, (string) $cfg['usuario'], (string) $cfg['senha'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

/**
 * Mensagem para o usuário quando o banco falha. Registra o erro no log
 * do PHP e só mostra detalhes com 'debug' => true na configuração local.
 */
function db_mensagem_erro(Throwable $erro): string
{
    error_log('[EcoSpot] ' . $erro->getMessage());
    $mensagem = 'Não foi possível concluir a operação agora. Tente novamente em instantes.';
    try {
        if (!empty(db_config()['debug'])) {
            $mensagem .= ' Detalhe: ' . $erro->getMessage();
        }
    } catch (Throwable $semConfig) {
        $mensagem .= ' Detalhe: ' . $semConfig->getMessage();
    }
    return $mensagem;
}
