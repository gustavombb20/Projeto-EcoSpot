<?php
/**
 * Acesso à tabela usuario (SQL validado + migração 001) e validação
 * dos formulários de cadastro e login. Todas as consultas são preparadas.
 */
declare(strict_types=1);

/** Remove espaços extras e normaliza o nome digitado. */
function normalizar_nome(string $nome): string
{
    return trim((string) preg_replace('/\s+/u', ' ', $nome));
}

function normalizar_email(string $email): string
{
    return mb_strtolower(trim($email), 'UTF-8');
}

/**
 * Valida os dados do cadastro no servidor.
 * Retorna [campo => mensagem]; vazio quando está tudo certo.
 */
function validar_cadastro(string $nome, string $email, string $senha, string $confirmacao, bool $aceite): array
{
    $erros = [];

    $tamanhoNome = mb_strlen($nome, 'UTF-8');
    if ($nome === '') {
        $erros['nome'] = 'Informe seu nome.';
    } elseif ($tamanhoNome < NOME_MIN || $tamanhoNome > NOME_MAX) {
        $erros['nome'] = 'O nome deve ter entre ' . NOME_MIN . ' e ' . NOME_MAX . ' caracteres.';
    } elseif (!preg_match("/^[\\p{L}\\p{M}][\\p{L}\\p{M} .'-]*$/u", $nome)) {
        $erros['nome'] = 'Use apenas letras, espaços, apóstrofo, ponto ou hífen no nome.';
    }

    if ($email === '') {
        $erros['email'] = 'Informe seu e-mail.';
    } elseif (mb_strlen($email, 'UTF-8') > EMAIL_MAX || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $erros['email'] = 'Informe um e-mail válido, por exemplo nome@exemplo.com.';
    }

    if (strlen($senha) < SENHA_MIN) {
        $erros['senha'] = 'A senha deve ter pelo menos ' . SENHA_MIN . ' caracteres.';
    } elseif (strlen($senha) > SENHA_MAX) {
        $erros['senha'] = 'A senha deve ter no máximo ' . SENHA_MAX . ' caracteres.';
    } elseif (!preg_match('/\p{L}/u', $senha) || !preg_match('/\d/', $senha)) {
        $erros['senha'] = 'A senha deve conter letras e números.';
    }

    if (!isset($erros['senha']) && !hash_equals($senha, $confirmacao)) {
        $erros['confirmacao'] = 'A confirmação não é igual à senha.';
    }

    if (!$aceite) {
        $erros['aceite'] = 'Para criar a conta, é necessário aceitar a Política de Privacidade.';
    }

    return $erros;
}

function usuario_email_existe(string $email): bool
{
    $consulta = db()->prepare('SELECT 1 FROM usuario WHERE email = ? LIMIT 1');
    $consulta->execute([$email]);
    return (bool) $consulta->fetchColumn();
}

/**
 * Cria um usuário com papel e situação definidos pelo servidor.
 * Retorna o usuario_id gerado pelo AUTO_INCREMENT.
 */
function usuario_criar(string $nome, string $email, string $senha): int
{
    $sql = 'INSERT INTO usuario
                (nome, email, senha_hash, situacao, papel, data_criacao, aceite_privacidade_em, versao_politica)
            VALUES
                (:nome, :email, :senha_hash, :situacao, :papel, CURDATE(), NOW(), :versao_politica)';
    $consulta = db()->prepare($sql);
    $consulta->execute([
        ':nome'            => $nome,
        ':email'           => $email,
        ':senha_hash'      => password_hash($senha, PASSWORD_DEFAULT),
        ':situacao'        => SITUACAO_ATIVA,
        ':papel'           => PAPEL_PADRAO,
        ':versao_politica' => POLITICA_VERSAO,
    ]);
    return (int) db()->lastInsertId();
}

function usuario_buscar_por_email(string $email): ?array
{
    $consulta = db()->prepare(
        'SELECT usuario_id, nome, email, senha_hash, situacao, papel
           FROM usuario WHERE email = ? LIMIT 1'
    );
    $consulta->execute([$email]);
    $usuario = $consulta->fetch();
    return $usuario ?: null;
}

function usuario_buscar_por_id(int $usuarioId): ?array
{
    $consulta = db()->prepare(
        'SELECT usuario_id, nome, email, situacao, papel, data_criacao,
                aceite_privacidade_em, versao_politica
           FROM usuario WHERE usuario_id = ? LIMIT 1'
    );
    $consulta->execute([$usuarioId]);
    $usuario = $consulta->fetch();
    return $usuario ?: null;
}

/**
 * Confere e-mail e senha. Retorna o usuário ou null, sem revelar
 * qual dos dois estava errado.
 */
function usuario_autenticar(string $email, string $senha): ?array
{
    $usuario = usuario_buscar_por_email($email);

    // Hash fixo usado quando o e-mail não existe, para que o tempo de
    // resposta seja parecido nos dois casos.
    $hash = $usuario['senha_hash'] ?? '$2y$10$.vGA1O9wmRjrwAVXD98HNOgsNpDczlqm3Jq7KnEd1rVAGv3Fykk1a';
    $senhaConfere = password_verify($senha, (string) $hash);

    if ($usuario === null || empty($usuario['senha_hash']) || !$senhaConfere) {
        return null;
    }
    if ($usuario['situacao'] !== SITUACAO_ATIVA) {
        return null;
    }

    if (password_needs_rehash($usuario['senha_hash'], PASSWORD_DEFAULT)) {
        $atualiza = db()->prepare('UPDATE usuario SET senha_hash = ? WHERE usuario_id = ?');
        $atualiza->execute([password_hash($senha, PASSWORD_DEFAULT), $usuario['usuario_id']]);
    }

    unset($usuario['senha_hash']);
    return $usuario;
}
