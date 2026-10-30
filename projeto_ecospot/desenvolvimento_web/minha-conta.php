<?php
/**
 * Área do usuário autenticado: mostra os dados gravados no banco e permite sair.
 */
declare(strict_types=1);

require __DIR__ . '/backend/includes/bootstrap.php';

$sessao = exigir_login();
$falha = null;
$usuario = null;

try {
    $usuario = usuario_buscar_por_id($sessao['usuario_id']);
    if ($usuario === null) {
        // A conta deixou de existir no banco: encerra a sessão.
        sessao_encerrar();
        sessao_iniciar();
        flash_definir('aviso', 'Sua conta não foi encontrada. Entre novamente.');
        redirecionar('login.php');
    }
} catch (Throwable $erro) {
    $falha = ['tipo' => 'erro', 'texto' => db_mensagem_erro($erro)];
}

/** Formata DATE/DATETIME do MySQL no padrão brasileiro. */
function data_br(?string $valor, bool $comHora = false): string
{
    if (!$valor) {
        return '—';
    }
    $data = date_create($valor);
    return $data ? $data->format($comHora ? 'd/m/Y \à\s H:i' : 'd/m/Y') : '—';
}

$papeis = ['cidadao' => 'Cidadão'];

layout_inicio('Minha conta', 'conta', 'Dados da sua conta no EcoSpot + EcoAlert.');
?>
    <section class="page-hero page-hero--spot" aria-labelledby="page-title">
      <div class="container">
        <nav class="breadcrumb" aria-label="Você está aqui">
          <ol>
            <li><a href="index.html">Início</a></li>
            <li aria-current="page">Minha conta</li>
          </ol>
        </nav>
        <span class="eyebrow">Sua conta</span>
        <h1 id="page-title">Olá, <?= e($sessao['nome']) ?></h1>
        <p>Você está conectado. Quando o registro de ocorrências estiver disponível no EcoAlert, ele poderá ser feito por esta conta.</p>
      </div>
    </section>

    <section class="section auth-section">
      <div class="container">
        <div class="card auth-card">
          <?php layout_alerta($falha ?? flash_obter()); ?>

<?php if ($usuario): ?>
          <h2 class="auth-card__title">Dados da conta</h2>
          <dl class="account-list">
            <dt>Nome</dt>
            <dd><?= e($usuario['nome']) ?></dd>
            <dt>E-mail</dt>
            <dd><?= e($usuario['email']) ?></dd>
            <dt>Perfil</dt>
            <dd><?= e($papeis[$usuario['papel']] ?? $usuario['papel']) ?></dd>
            <dt>Conta criada em</dt>
            <dd><?= e(data_br($usuario['data_criacao'])) ?></dd>
            <dt>Política de Privacidade</dt>
            <dd>Aceita em <?= e(data_br($usuario['aceite_privacidade_em'], true)) ?> (versão <?= e($usuario['versao_politica'] ?? '—') ?>)</dd>
          </dl>
<?php endif; ?>

          <div class="form-actions">
            <form method="post" action="logout.php">
              <?= csrf_campo() ?>
              <button class="btn btn--outline" type="submit">Sair da conta</button>
            </form>
            <a class="btn btn--primary" href="ecospot.html">Consultar o EcoSpot</a>
          </div>
        </div>
      </div>
    </section>
<?php
layout_fim();
