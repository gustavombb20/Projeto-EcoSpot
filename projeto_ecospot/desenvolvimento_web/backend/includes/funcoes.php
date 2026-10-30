<?php
/**
 * Funções auxiliares usadas pelas páginas PHP.
 */
declare(strict_types=1);

/** Escapa texto para uso seguro em HTML. */
function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Redireciona para um caminho relativo do próprio site e encerra. */
function redirecionar(string $caminho): void
{
    header('Location: ' . $caminho, true, 303);
    exit;
}

/** Guarda uma mensagem para ser exibida na próxima página. */
function flash_definir(string $tipo, string $texto): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

/** Retorna e remove a mensagem pendente, se houver. */
function flash_obter(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

/** Valor de um campo enviado por POST, como texto. */
function post_texto(string $campo): string
{
    $valor = $_POST[$campo] ?? '';
    return is_string($valor) ? $valor : '';
}
