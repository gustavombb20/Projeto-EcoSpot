/* ==========================================================================
   EcoSpot + EcoAlert — scripts globais
   Carregado em todas as páginas. Cada módulo verifica se os elementos
   existem antes de agir, então o mesmo arquivo serve para todas as páginas.
   ========================================================================== */
(function () {
  "use strict";

  /* ---------- Menu mobile ---------- */
  function initMobileNav() {
    var toggle = document.querySelector(".nav-toggle");
    var nav = document.getElementById("main-nav");
    if (!toggle || !nav) return;

    function setOpen(open) {
      toggle.setAttribute("aria-expanded", String(open));
      toggle.setAttribute("aria-label", open ? "Fechar menu" : "Abrir menu");
      nav.classList.toggle("is-open", open);
    }

    toggle.addEventListener("click", function () {
      setOpen(toggle.getAttribute("aria-expanded") !== "true");
    });

    // Fecha com Esc e devolve o foco ao botão
    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && nav.classList.contains("is-open")) {
        setOpen(false);
        toggle.focus();
      }
    });

    // Fecha ao clicar fora do cabeçalho
    document.addEventListener("click", function (event) {
      if (nav.classList.contains("is-open") && !event.target.closest(".site-header")) {
        setOpen(false);
      }
    });

    // Fecha ao escolher um link (inclusive âncoras na mesma página)
    nav.addEventListener("click", function (event) {
      if (event.target.closest("a")) setOpen(false);
    });

    // Garante estado consistente ao voltar para o layout desktop
    var desktop = window.matchMedia("(min-width: 761px)");
    var onChange = function (mq) { if (mq.matches) setOpen(false); };
    if (desktop.addEventListener) desktop.addEventListener("change", onChange);
    else if (desktop.addListener) desktop.addListener(onChange);
  }

  /* ---------- Link ativo na navegação ---------- */
  // As páginas já trazem aria-current no HTML; isto é uma garantia extra
  // caso os arquivos sejam renomeados (ex.: .html -> .php no XAMPP).
  function markCurrentLink() {
    var page = document.body.getAttribute("data-page");
    if (!page) return;
    document.querySelectorAll(".main-nav__link[data-nav]").forEach(function (link) {
      if (link.getAttribute("data-nav") === page) {
        link.setAttribute("aria-current", "page");
      } else {
        link.removeAttribute("aria-current");
      }
    });
  }

  /* ---------- Ano atual no rodapé ---------- */
  function setFooterYear() {
    document.querySelectorAll("[data-current-year]").forEach(function (el) {
      el.textContent = String(new Date().getFullYear());
    });
  }

  /* ---------- Botão "voltar ao topo" ---------- */
  function initBackToTop() {
    var button = document.querySelector(".back-to-top");
    if (!button) return;

    function update() {
      button.classList.toggle("is-visible", window.scrollY > 400);
    }

    window.addEventListener("scroll", update, { passive: true });
    update();

    button.addEventListener("click", function () {
      window.scrollTo({ top: 0 });
      var target = document.getElementById("conteudo");
      if (target) target.focus({ preventScroll: true });
    });
  }

  /* ---------- Estado da conta no menu (páginas .html) ---------- */
  // As páginas PHP já trazem o link certo; nas páginas HTML o servidor
  // informa se há alguém conectado. Sem servidor PHP, o link fica "Entrar".
  function initAuthLink() {
    var link = document.querySelector("[data-auth-link]");
    if (!link || !window.fetch || location.protocol === "file:") return;

    fetch("backend/api/sessao.php", { credentials: "same-origin", headers: { Accept: "application/json" } })
      .then(function (resposta) { return resposta.ok ? resposta.json() : null; })
      .then(function (dados) {
        if (dados && dados.autenticado) {
          link.textContent = "Minha conta";
          link.href = "minha-conta.php";
        }
      })
      .catch(function () { /* mantém "Entrar" */ });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initMobileNav();
    markCurrentLink();
    setFooterYear();
    initBackToTop();
    initAuthLink();
  });
})();
