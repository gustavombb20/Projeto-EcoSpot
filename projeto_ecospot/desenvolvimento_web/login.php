<?php
/**
 * Login com e-mail e senha (password_verify) e início da sessão.
 */
declare(strict_types=1);

require __DIR__ . '/backend/includes/bootstrap.php';

if (usuario_atual()) {
    redirecionar('minha-conta.php');
}

$falha = null;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = normalizar_email(post_texto('email'));
    $senha = post_texto('senha');

    if (!csrf_valido()) {
        http_response_code(400);
        $falha = ['tipo' => 'erro', 'texto' => 'A página expirou. Tente entrar novamente.'];
    } elseif ($email === '' || $senha === '') {
        http_response_code(422);
        $falha = ['tipo' => 'erro', 'texto' => 'Informe seu e-mail e sua senha.'];
    } else {
        try {
            $usuario = usuario_autenticar($email, $senha);
            if ($usuario) {
                sessao_autenticar($usuario);
                flash_definir('sucesso', 'Você entrou na sua conta.');
                redirecionar('minha-conta.php');
            }
            http_response_code(401);
            $falha = ['tipo' => 'erro', 'texto' => 'E-mail ou senha incorretos.'];
        } catch (Throwable $erro) {
            $falha = ['tipo' => 'erro', 'texto' => db_mensagem_erro($erro)];
        }
    }
}

layout_inicio('Entrar', 'conta', 'Entre na sua conta do EcoSpot + EcoAlert.');
?>
    <section class="page-hero page-hero--spot" aria-labelledby="page-title">
      <div class="container">
        <nav class="breadcrumb" aria-label="Você está aqui">
          <ol>
            <li><a href="index.html">Início</a></li>
            <li aria-current="page">Entrar</li>
          </ol>
        </nav>
        <span class="eyebrow">Sua conta</span>
        <h1 id="page-title">Entrar</h1>
        <p>Acesse sua conta com o e-mail e a senha cadastrados.</p>
      </div>
    </section>

    <section class="section auth-section">
      <div class="container">
        <div class="card auth-card">
          <?php layout_alerta($falha ?? flash_obter()); ?>

          <form class="form" method="post" action="login.php" novalidate>
            <?= csrf_campo() ?>

            <div class="form-field">
              <label for="email">E-mail</label>
              <input class="form-input" type="email" id="email" name="email" value="<?= e($email) ?>"
                     autocomplete="username" maxlength="<?= EMAIL_MAX ?>" required>
            </div>

            <div class="form-field">
              <label for="senha">Senha</label>
              <input class="form-input" type="password" id="senha" name="senha"
                     autocomplete="current-password" maxlength="<?= SENHA_MAX ?>" required>
            </div>

            <div class="form-actions">
              <button class="btn btn--primary" type="submit">Entrar</button>
            </div>
          </form>

          <p class="form-footer">Ainda não tem conta? <a href="cadastro.php">Criar conta</a></p>
        </div>
      </div>
    </section>
<?php
layout_fim();
