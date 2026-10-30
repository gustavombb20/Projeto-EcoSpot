<?php
/**
 * Cria o banco de desenvolvimento ecospot_dev a partir do SQL validado
 * e aplica as migrações complementares.
 *
 * Uso (no XAMPP para Windows, dentro da pasta do projeto):
 *   C:\xampp\php\php.exe database\instalar_dev.php
 *   C:\xampp\php\php.exe database\instalar_dev.php --recriar   (apaga e recria o ecospot_dev)
 *
 * O arquivo original não é modificado: ele é lido, as linhas
 * CREATE DATABASE / USE são ignoradas e as tabelas são criadas no
 * banco configurado em backend/config/database.local.php.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../backend/config/app.php';

$raiz = dirname(__DIR__);
$arquivoConfig = $raiz . '/backend/config/database.local.php';
if (!is_file($arquivoConfig)) {
    fwrite(STDERR, "Arquivo backend/config/database.local.php não encontrado.\n"
        . "Copie backend/config/database.example.php para database.local.php e ajuste os dados.\n");
    exit(1);
}
$cfg = require $arquivoConfig;
$banco = (string) $cfg['banco'];

if (!preg_match('/^[A-Za-z0-9_]+$/', $banco)) {
    fwrite(STDERR, "Nome de banco inválido: {$banco}\n");
    exit(1);
}
if (strcasecmp($banco, BANCO_ORIGINAL) === 0) {
    fwrite(STDERR, "Recusado: a configuração aponta para o banco compartilhado " . BANCO_ORIGINAL . ".\n"
        . "Use um banco de desenvolvimento separado (ecospot_dev).\n");
    exit(1);
}

$recriar = in_array('--recriar', $argv, true);

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $cfg['host'], (int) $cfg['porta']),
        (string) $cfg['usuario'],
        (string) $cfg['senha'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    fwrite(STDERR, "Não foi possível conectar ao MySQL: {$e->getMessage()}\n"
        . "Verifique se o MySQL está iniciado no XAMPP Control Panel.\n");
    exit(1);
}

$existe = $pdo->prepare('SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?');
$existe->execute([$banco]);
if ((int) $existe->fetchColumn() > 0) {
    if (!$recriar) {
        fwrite(STDERR, "O banco {$banco} já existe. Nada foi alterado.\n"
            . "Para apagar e recriar, rode novamente com --recriar.\n");
        exit(1);
    }
    $pdo->exec("DROP DATABASE `{$banco}`");
    echo "Banco {$banco} apagado.\n";
}

$pdo->exec("CREATE DATABASE `{$banco}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `{$banco}`");
echo "Banco {$banco} criado (utf8mb4).\n";

/** Divide um script SQL simples em comandos, ignorando comentários de linha. */
function comandos_sql(string $sql): array
{
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);
    return array_values(array_filter(array_map('trim', explode(';', $sql)), 'strlen'));
}

$scripts = [$raiz . '/database/original/MYSQL1609(2).sql'];
$migracoes = glob($raiz . '/database/migrations/*.sql');
sort($migracoes);
$scripts = array_merge($scripts, $migracoes);

foreach ($scripts as $script) {
    $total = 0;
    foreach (comandos_sql((string) file_get_contents($script)) as $comando) {
        // O script original cria e seleciona o próprio banco; aqui usamos o de desenvolvimento.
        if (preg_match('/^(CREATE\s+DATABASE|USE)\b/i', $comando)) {
            continue;
        }
        $pdo->exec($comando);
        $total++;
    }
    echo '  ' . basename($script) . ": {$total} comandos executados.\n";
}

$tabelas = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo 'Tabelas em ' . $banco . ': ' . implode(', ', $tabelas) . "\n";
echo "Pronto.\n";
