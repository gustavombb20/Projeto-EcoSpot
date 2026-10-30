<?php
/**
 * Cadastro de usuário: nome, e-mail, senha e aceite da política de privacidade.
 */
declare(strict_types=1);

require __DIR__ . '/backend/includes/bootstrap.php';

if (usuario_atual()) {
    redirecionar('minha-conta.php');
}

$erros = [];
$falha = null;
$nome = '';
$email = '';
$aceite = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = normalizar_nome(post_texto('nome'));
    $email = normalizar_email(post_texto('email'));
    $senha = post_texto('senha');
    $confirmacao = post_texto('confirmacao');
    $aceite = post_texto('aceite') === '1';

    if (!csrf_valido()) {
        http_response_code(400);
        $falha = ['tipo' => 'erro', 'texto' => 'A página expirou. Confira os dados e envie o formulário novamente.'];
    } else {
        $erros = validar_cadastro($nome, $email, $senha, $confirmacao, $aceite);

        if (!$erros) {
            try {
                if (usuario_email_existe($email)) {
                    $erros['email'] = 'Já existe uma conta com este e-mail. Entre na sua conta ou use outro e-mail.';
                } else {
                    usuario_criar($nome, $email, $senha);
                    flash_definir('sucesso', 'Conta criada com sucesso. Entre com seu e-mail e senha.');
                    redirecionar('login.php');
                }
            } catch (PDOException $erro) {
                // 23000: violação de UNIQUE (dois cadastros simultâneos com o mesmo e-mail).
                if ($erro->getCode() === '23000') {
                    $erros['email'] = 'Já existe uma conta com este e-mail. Entre na sua conta ou use outro e-mail.';
                } else {
                    $falha = ['tipo' => 'erro', 'texto' => db_mensagem_erro($erro)];
                }
            } catch (Throwable $erro) {
                $falha = ['tipo' => 'erro', 'texto' => db_mensagem_erro($erro)];
            }
        }
        if ($erros) {
            http_response_code(422);
        }
    }
}

layout_inicio('Criar conta', 'conta', 'Crie sua conta no EcoSpot + EcoAlert.');
?>
    <section class="page-hero page-hero--spot" aria-labelledby="page-title">
      <div class="container">
        <nav class="breadcrumb" aria-label="Você está aqui">
          <ol>
            <li><a href="index.html">Início</a></li>
            <li aria-current="page">Criar conta</li>
          </ol>
        </nav>
        <span class="eyebrow">Sua conta</span>
        <h1 id="page-title">Criar conta</h1>
        <p>Com uma conta, você poderá registrar ocorrências ambientais no EcoAlert quando o registro estiver disponível. A consulta ao EcoSpot continua aberta a todos.</p>
      </div>
    </section>

    <section class="section auth-section">
      <div class="container">
        <div class="card auth-card">
          <?php layout_alerta($falha ?? flash_obter()); ?>
          <?php layout_erros($erros); ?>

          <form class="form" method="post" action="cadastro.php" novalidate>
            <?= csrf_campo() ?>

            <div class="form-field">
              <label for="nome">Nome completo</label>
              <input class="form-input" type="text" id="nome" name="nome" value="<?= e($nome) ?>"
                     autocomplete="name" maxlength="<?= NOME_MAX ?>" required<?= campo_invalido($erros, 'nome') ?>>
              <?php campo_erro($erros, 'nome'); ?>
            </div>

            <div class="form-field">
              <label for="email">E-mail</label>
              <input class="form-input" type="email" id="email" name="email" value="<?= e($email) ?>"
                     autocomplete="email" maxlength="<?= EMAIL_MAX ?>" required<?= campo_invalido($erros, 'email') ?>>
              <?php campo_erro($erros, 'email'); ?>
            </div>

            <div class="form-field">
              <label for="senha">Senha</label>
              <input class="form-input" type="password" id="senha" name="senha"
                     autocomplete="new-password" minlength="<?= SENHA_MIN ?>" maxlength="<?= SENHA_MAX ?>" required<?= campo_invalido($erros, 'senha', 'senha-dica') ?>>
              <p class="form-hint" id="senha-dica">Mínimo de <?= SENHA_MIN ?> caracteres, com letras e números.</p>
              <?php campo_erro($erros, 'senha'); ?>
            </div>

            <div class="form-field">
              <label for="confirmacao">Confirme a senha</label>
              <input class="form-input" type="password" id="confirmacao" name="confirmacao"
                     autocomplete="new-password" maxlength="<?= SENHA_MAX ?>" required<?= campo_invalido($erros, 'confirmacao') ?>>
              <?php campo_erro($erros, 'confirmacao'); ?>
            </div>

            <div class="form-field">
              <div class="form-check">
                <input type="checkbox" id="aceite" name="aceite" value="1"<?= $aceite ? ' checked' : '' ?> required<?= campo_invalido($erros, 'aceite') ?>>
                <label for="aceite">Li e aceito a <a href="politica-privacidade.html" target="_blank" rel="noopener">Política de Privacidade<span class="visually-hidden"> (abre em nova aba)</span></a>.</label>
              </div>
              <?php campo_erro($erros, 'aceite'); ?>
            </div>

            <div class="form-actions">
              <button class="btn btn--primary" type="submit">Criar conta</button>
            </div>
          </form>

          <p class="form-footer">Já tem uma conta? <a href="login.php">Entrar</a></p>
        </div>
      </div>
    </section>
<?php
layout_fim();
