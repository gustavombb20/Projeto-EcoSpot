<?php
/**
 * Encerra a sessão. Aceita apenas POST com token CSRF válido.
 */
declare(strict_types=1);

require __DIR__ . '/backend/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    redirecionar(usuario_atual() ? 'minha-conta.php' : 'login.php');
}

if (!csrf_valido()) {
    flash_definir('erro', 'Não foi possível sair. Tente novamente.');
    redirecionar(usuario_atual() ? 'minha-conta.php' : 'login.php');
}

sessao_encerrar();
sessao_iniciar();
flash_definir('sucesso', 'Você saiu da sua conta.');
redirecionar('login.php');
