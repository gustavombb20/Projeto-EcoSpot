<?php
/**
 * Ponto de entrada comum das páginas PHP.
 * Carrega configuração e funções, envia cabeçalhos de segurança
 * e inicia a sessão.
 */
declare(strict_types=1);

require __DIR__ . '/../config/app.php';
require __DIR__ . '/funcoes.php';
require __DIR__ . '/db.php';
require __DIR__ . '/sessao.php';
require __DIR__ . '/csrf.php';
require __DIR__ . '/usuario.php';
require __DIR__ . '/layout.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

sessao_iniciar();
