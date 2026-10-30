<?php
/**
 * Informa às páginas HTML se há um usuário conectado, para o menu
 * mostrar "Entrar" ou "Minha conta". Não cria sessão para visitantes.
 */
declare(strict_types=1);

require __DIR__ . '/../config/app.php';
require __DIR__ . '/../includes/funcoes.php';
require __DIR__ . '/../includes/sessao.php';
require __DIR__ . '/../includes/csrf.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

sessao_iniciar(true);
$usuario = usuario_atual();

echo json_encode(
    $usuario
        ? ['autenticado' => true, 'nome' => $usuario['nome']]
        : ['autenticado' => false],
    JSON_UNESCAPED_UNICODE
);
