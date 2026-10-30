<?php
/**
 * Cabeçalho e rodapé das páginas PHP, com a mesma marcação das
 * páginas HTML da Sprint 1 (index.html, ecospot.html, ecoalert.html).
 */
declare(strict_types=1);

function layout_inicio(string $titulo, string $pagina, string $descricao): void
{
    $usuario = usuario_atual();
    $linkConta = $usuario
        ? ['href' => 'minha-conta.php', 'texto' => 'Minha conta']
        : ['href' => 'login.php', 'texto' => 'Entrar'];
    $itens = [
        'home'     => ['href' => 'index.html', 'texto' => 'Início'],
        'ecospot'  => ['href' => 'ecospot.html', 'texto' => 'EcoSpot'],
        'ecoalert' => ['href' => 'ecoalert.html', 'texto' => 'EcoAlert'],
        'conta'    => $linkConta,
    ];
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="<?= e($descricao) ?>">
  <meta name="theme-color" content="#1b7a43">
  <title><?= e($titulo) ?> | EcoSpot + EcoAlert</title>
  <link rel="icon" href="assets/img/logo.svg" type="image/svg+xml">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/main.js" defer></script>
</head>
<body data-page="<?= e($pagina) ?>">
  <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>

  <header class="site-header">
    <div class="container site-header__inner">
      <a class="brand" href="index.html">
        <img src="assets/img/logo.svg" alt="" width="36" height="36">
        <span>EcoSpot <span class="brand__plus">+</span> EcoAlert</span>
      </a>
      <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Abrir menu">
        <span class="nav-toggle__bar" aria-hidden="true"></span>
      </button>
      <nav id="main-nav" class="main-nav" aria-label="Navegação principal">
        <ul class="main-nav__list">
<?php foreach ($itens as $chave => $item): ?>
          <li><a class="main-nav__link" data-nav="<?= e($chave) ?>" href="<?= e($item['href']) ?>"<?= $chave === $pagina ? ' aria-current="page"' : '' ?>><?= e($item['texto']) ?></a></li>
<?php endforeach; ?>
        </ul>
      </nav>
    </div>
  </header>

  <main id="conteudo" tabindex="-1">
<?php
}

function layout_fim(): void
{
    ?>
  </main>

  <footer class="site-footer">
    <div class="container">
      <div class="site-footer__grid">
        <div>
          <p><strong>EcoSpot + EcoAlert</strong></p>
          <p>Plataforma de consulta a pontos sustentáveis e registro de problemas ambientais em Curitiba (PR).</p>
        </div>
        <nav class="site-footer__nav" aria-label="Navegação do rodapé">
          <ul>
            <li><a href="index.html">Início</a></li>
            <li><a href="ecospot.html">EcoSpot</a></li>
            <li><a href="ecoalert.html">EcoAlert</a></li>
            <li><a href="politica-privacidade.html">Política de Privacidade</a></li>
          </ul>
        </nav>
      </div>
      <p class="site-footer__bottom">© <span data-current-year><?= date('Y') ?></span> EcoSpot + EcoAlert · Projeto acadêmico desenvolvido por estudantes da PUCPR. Sem vínculo oficial com a Prefeitura de Curitiba.</p>
    </div>
  </footer>

  <button class="back-to-top" type="button" aria-label="Voltar ao topo">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
  </button>
</body>
</html>
<?php
}

/** Mensagem de retorno (sucesso, aviso ou erro) no topo do formulário. */
function layout_alerta(?array $flash): void
{
    if (!$flash) {
        return;
    }
    $tipo = in_array($flash['tipo'], ['sucesso', 'aviso', 'erro'], true) ? $flash['tipo'] : 'aviso';
    $papel = $tipo === 'erro' ? 'alert' : 'status';
    echo '<div class="alert alert--' . $tipo . '" role="' . $papel . '">' . e($flash['texto']) . '</div>';
}

/** Lista de erros de validação, com links para os campos. */
function layout_erros(array $erros): void
{
    if (!$erros) {
        return;
    }
    echo '<div class="alert alert--erro" role="alert" tabindex="-1" id="resumo-erros">';
    echo '<p><strong>Corrija os campos abaixo:</strong></p><ul>';
    foreach ($erros as $campo => $mensagem) {
        echo '<li><a href="#' . e($campo) . '">' . e($mensagem) . '</a></li>';
    }
    echo '</ul></div>';
}

/** Atributos ARIA e mensagem de erro de um campo. */
function campo_invalido(array $erros, string $campo, string $idDica = ''): string
{
    $descricoes = array_filter([$idDica, isset($erros[$campo]) ? $campo . '-erro' : '']);
    $atributos = isset($erros[$campo]) ? ' aria-invalid="true"' : '';
    if ($descricoes) {
        $atributos .= ' aria-describedby="' . e(implode(' ', $descricoes)) . '"';
    }
    return $atributos;
}

function campo_erro(array $erros, string $campo): void
{
    if (isset($erros[$campo])) {
        echo '<p class="form-error" id="' . e($campo) . '-erro">' . e($erros[$campo]) . '</p>';
    }
}
