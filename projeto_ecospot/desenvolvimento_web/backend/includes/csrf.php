<?php
/**
 * Proteção CSRF: um token aleatório por sessão, enviado em campo oculto
 * e conferido em todo POST.
 */
declare(strict_types=1);

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_renovar(): void
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/** Campo oculto para incluir dentro de cada <form method="post">. */
function csrf_campo(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_valido(): bool
{
    $enviado = $_POST['csrf_token'] ?? '';
    return is_string($enviado)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $enviado);
}
