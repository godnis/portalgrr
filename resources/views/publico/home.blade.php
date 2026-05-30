<!DOCTYPE html>
<html lang="pt-br" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GRR • Grupo de Resposta Rápida — Portal</title>
  <meta name="description" content="Portal institucional do Grupo de Resposta Rápida. Informações públicas, comunicados oficiais, legislação, recrutamento e acesso restrito.">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    /* =========================================================
       GRR 3.0 — AJUSTE LOCAL DO MODAL NO TEMA ESCURO
    ========================================================= */
    .portal-modal .modal-content {
      border: 1px solid rgba(15, 23, 42, .10);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(15, 23, 42, .18);
    }

    .portal-modal .modal-header,
    .portal-modal .modal-footer {
      border-color: rgba(15, 23, 42, .08);
    }

    .portal-modal .modal-title {
      font-weight: 900;
      letter-spacing: -.02em;
    }

    .portal-modal .form-label {
      font-weight: 800;
      color: #334155;
      font-size: 13px;
      margin-bottom: 6px;
    }

    .portal-modal .form-control,
    .portal-modal textarea.form-control {
      border-radius: 12px;
      border: 1px solid rgba(15, 23, 42, .10);
      min-height: 44px;
    }

    .portal-modal textarea.form-control {
      min-height: 140px;
      resize: vertical;
    }

    .portal-modal .form-control:focus,
    .portal-modal textarea.form-control:focus {
      border-color: rgba(19, 81, 180, .38);
      box-shadow: 0 0 0 4px rgba(19, 81, 180, .12);
    }

    .portal-modal .form-text {
      color: #64748B;
    }

    .portal-modal .alert {
      border-radius: 14px;
    }

    html[data-theme="dark"] .portal-modal .modal-content {
      background: linear-gradient(180deg, rgba(15, 23, 42, .98), rgba(11, 18, 32, .98));
      border-color: rgba(255, 255, 255, .10);
      box-shadow: 0 26px 70px rgba(0, 0, 0, .55);
    }

    html[data-theme="dark"] .portal-modal .modal-header,
    html[data-theme="dark"] .portal-modal .modal-footer {
      border-color: rgba(255, 255, 255, .10);
      background: transparent;
    }

    html[data-theme="dark"] .portal-modal .modal-title {
      color: rgba(248, 250, 252, .96);
    }

    html[data-theme="dark"] .portal-modal .btn-close {
      filter: invert(1) opacity(.92);
    }

    html[data-theme="dark"] .portal-modal .form-label {
      color: rgba(226, 232, 240, .88);
    }

    html[data-theme="dark"] .portal-modal .form-control,
    html[data-theme="dark"] .portal-modal textarea.form-control {
      background: rgba(255, 255, 255, .04) !important;
      border-color: rgba(255, 255, 255, .12) !important;
      color: rgba(248, 250, 252, .96) !important;
    }

    html[data-theme="dark"] .portal-modal .form-control::placeholder,
    html[data-theme="dark"] .portal-modal textarea.form-control::placeholder {
      color: rgba(226, 232, 240, .52) !important;
      opacity: 1;
    }

    html[data-theme="dark"] .portal-modal .form-control:disabled,
    html[data-theme="dark"] .portal-modal .form-control[disabled] {
      background: rgba(255, 255, 255, .08) !important;
      color: rgba(248, 250, 252, .88) !important;
      opacity: 1;
    }

    html[data-theme="dark"] .portal-modal .form-text {
      color: rgba(226, 232, 240, .66);
    }

    html[data-theme="dark"] .portal-modal .alert-info {
      color: #d7f3ff;
      background: rgba(14, 116, 144, .22);
      border-color: rgba(103, 232, 249, .24);
    }

    html[data-theme="dark"] .portal-modal .btn-outline-secondary {
      color: rgba(226, 232, 240, .92);
      border-color: rgba(255, 255, 255, .16);
      background: rgba(255, 255, 255, .03);
    }

    html[data-theme="dark"] .portal-modal .btn-outline-secondary:hover {
      background: rgba(255, 255, 255, .08);
      border-color: rgba(255, 255, 255, .22);
      color: rgba(248, 250, 252, .98);
    }

    html[data-theme="dark"] .portal-modal .btn-primary {
      background: linear-gradient(180deg, #2563eb, #1d4ed8);
      border-color: #2563eb;
    }

    html[data-theme="dark"] .portal-modal .btn-primary:hover {
      background: linear-gradient(180deg, #1d4ed8, #1e3a8a);
      border-color: #1d4ed8;
    }

    /* =========================================================
       GRR 3.0 — GALERIA OPERACIONAL
    ========================================================= */
    .portal-gallery-section {
      padding: 22px 0 8px;
    }

    .portal-gallery-wrap {
      border: 1px solid rgba(15, 23, 42, .08);
      border-radius: 28px;
      background:
        linear-gradient(180deg, rgba(255,255,255,1), rgba(248,250,252,.96));
      box-shadow: 0 22px 50px rgba(15, 23, 42, .08);
      padding: 28px;
      overflow: hidden;
      position: relative;
    }

    .portal-gallery-wrap::before {
      content: "";
      position: absolute;
      inset: 0;
      background:
        radial-gradient(800px 260px at 15% 0%, rgba(19, 81, 180, .06), transparent 60%),
        radial-gradient(700px 220px at 100% 20%, rgba(249, 176, 0, .08), transparent 50%);
      pointer-events: none;
    }

    .portal-gallery-head {
      position: relative;
      z-index: 1;
      display: grid;
      grid-template-columns: 1.2fr .8fr;
      gap: 22px;
      align-items: end;
      margin-bottom: 22px;
    }

    .portal-gallery-kicker {
      font-size: 11px;
      font-weight: 950;
      letter-spacing: .14em;
      text-transform: uppercase;
      color: #64748B;
      margin-bottom: 8px;
    }

    .portal-gallery-title {
      font-size: clamp(1.8rem, 3vw, 2.7rem);
      line-height: 1.05;
      font-weight: 950;
      letter-spacing: -.03em;
      color: #0F172A;
      margin: 0 0 12px;
    }

    .portal-gallery-text {
      font-size: 15px;
      line-height: 1.7;
      color: #475569;
      margin: 0;
      max-width: 64ch;
    }

    .portal-gallery-quote {
      position: relative;
      z-index: 1;
      border: 1px solid rgba(15, 23, 42, .08);
      border-radius: 20px;
      background: rgba(255,255,255,.88);
      padding: 18px 18px 16px;
      box-shadow: 0 14px 30px rgba(15, 23, 42, .05);
    }

    .portal-gallery-quote strong {
      display: block;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: #0B2A4A;
      margin-bottom: 8px;
    }

    .portal-gallery-quote p {
      margin: 0;
      color: #334155;
      font-size: 14px;
      line-height: 1.65;
      font-weight: 700;
    }

    .portal-gallery-grid {
      position: relative;
      z-index: 1;
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      gap: 16px;
    }

    .portal-gallery-card {
      position: relative;
      overflow: hidden;
      border-radius: 22px;
      min-height: 240px;
      background: #0f172a;
      box-shadow: 0 18px 40px rgba(15, 23, 42, .12);
      border: 1px solid rgba(15, 23, 42, .08);
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .portal-gallery-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 24px 50px rgba(15, 23, 42, .16);
      border-color: rgba(19, 81, 180, .20);
    }

    .portal-gallery-card--lg {
      grid-column: span 6;
      min-height: 320px;
    }

    .portal-gallery-card--md {
      grid-column: span 4;
      min-height: 260px;
    }

    .portal-gallery-card--sm {
      grid-column: span 3;
      min-height: 220px;
    }

    .portal-gallery-img {
      position: absolute;
      inset: 0;
      background-size: cover;
      background-position: center;
      transform: scale(1.01);
      transition: transform .35s ease;
    }

    .portal-gallery-card:hover .portal-gallery-img {
      transform: scale(1.06);
    }

    .portal-gallery-overlay {
      position: absolute;
      inset: 0;
      background:
        linear-gradient(180deg, rgba(2, 6, 23, .10), rgba(2, 6, 23, .70));
      display: flex;
      align-items: flex-end;
      padding: 18px;
    }

    .portal-gallery-meta {
      color: #fff;
      max-width: 90%;
    }

    .portal-gallery-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 10px;
      border-radius: 999px;
      background: rgba(255,255,255,.12);
      border: 1px solid rgba(255,255,255,.16);
      backdrop-filter: blur(8px);
      font-size: 10px;
      font-weight: 900;
      letter-spacing: .12em;
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    .portal-gallery-name {
      font-size: 20px;
      font-weight: 900;
      letter-spacing: -.02em;
      line-height: 1.1;
      margin: 0 0 6px;
    }

    .portal-gallery-desc {
      font-size: 13px;
      line-height: 1.5;
      color: rgba(255,255,255,.88);
      margin: 0;
      font-weight: 600;
    }

    .portal-gallery-foot {
      position: relative;
      z-index: 1;
      margin-top: 22px;
      padding-top: 18px;
      border-top: 1px solid rgba(15, 23, 42, .08);
      display: flex;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
      align-items: center;
    }

    .portal-gallery-foot-text {
      color: #475569;
      font-size: 14px;
      line-height: 1.6;
      font-weight: 700;
      max-width: 70ch;
      margin: 0;
    }

    .portal-gallery-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 14px;
      border-radius: 999px;
      background: rgba(19, 81, 180, .08);
      border: 1px solid rgba(19, 81, 180, .14);
      color: #0B2A4A;
      font-size: 12px;
      font-weight: 900;
      letter-spacing: .03em;
      white-space: nowrap;
    }

    @media (max-width: 991.98px) {
      .portal-gallery-head {
        grid-template-columns: 1fr;
      }

      .portal-gallery-card--lg,
      .portal-gallery-card--md {
        grid-column: span 6;
      }

      .portal-gallery-card--sm {
        grid-column: span 6;
      }
    }

    @media (max-width: 767.98px) {
      .portal-gallery-wrap {
        padding: 20px;
        border-radius: 22px;
      }

      .portal-gallery-grid {
        grid-template-columns: 1fr;
      }

      .portal-gallery-card--lg,
      .portal-gallery-card--md,
      .portal-gallery-card--sm {
        grid-column: auto;
        min-height: 260px;
      }

      .portal-gallery-name {
        font-size: 18px;
      }
    }

    html[data-theme="dark"] .portal-gallery-wrap {
      background:
        linear-gradient(180deg, rgba(15,23,42,.92), rgba(11,18,32,.96));
      border-color: rgba(255,255,255,.10);
      box-shadow: 0 24px 60px rgba(0,0,0,.45);
    }

    html[data-theme="dark"] .portal-gallery-kicker {
      color: rgba(226, 232, 240, .64);
    }

    html[data-theme="dark"] .portal-gallery-title {
      color: rgba(248, 250, 252, .96);
    }

    html[data-theme="dark"] .portal-gallery-text,
    html[data-theme="dark"] .portal-gallery-foot-text {
      color: rgba(226, 232, 240, .78);
    }

    html[data-theme="dark"] .portal-gallery-quote {
      background: rgba(255,255,255,.04);
      border-color: rgba(255,255,255,.10);
      box-shadow: 0 16px 34px rgba(0,0,0,.24);
    }

    html[data-theme="dark"] .portal-gallery-quote strong {
      color: #93c5fd;
    }

    html[data-theme="dark"] .portal-gallery-quote p {
      color: rgba(226, 232, 240, .86);
    }

    html[data-theme="dark"] .portal-gallery-card {
      border-color: rgba(255,255,255,.10);
      box-shadow: 0 18px 40px rgba(0,0,0,.32);
    }

    html[data-theme="dark"] .portal-gallery-foot {
      border-top-color: rgba(255,255,255,.10);
    }

    html[data-theme="dark"] .portal-gallery-pill {
      background: rgba(59,130,246,.14);
      border-color: rgba(59,130,246,.22);
      color: rgba(226,232,240,.94);
    }
  </style>
</head>

<body class="portal-body">

  {{-- TOPBAR --}}
  <div class="portal-topbar portal-topbar--gov">
    <div class="portal-container">
      <div class="portal-topbar-inner portal-topbar-inner--gov">

        <div class="d-flex align-items-center gap-3 flex-wrap">
          <div class="portal-govbrand" aria-label="Identidade institucional">
            <div class="portal-govlogo">fivem<span>.bc</span></div>
          </div>

          <span class="portal-sep d-none d-md-inline">|</span>

          <span class="portal-muted">
            Ministério da Justiça e Segurança Pública
          </span>

          <nav class="portal-links d-none d-lg-flex" aria-label="Links rápidos institucionais">
            <a href="{{ route('comunicados') }}" class="portal-toplink">Comunicados oficiais</a>
            <a href="{{ route('governo') }}" class="portal-toplink">Governo da Cidade</a>
            <a href="{{ route('legislacao') }}" class="portal-toplink">Legislação</a>
            <a href="{{ route('recrutamento') }}" class="portal-toplink">Recrutamento</a>
            <a href="{{ route('publico.hierarquia') }}" class="portal-toplink">Hierarquia</a>
          </nav>
        </div>

        <div class="d-flex align-items-center gap-2">
          <button
            class="portal-iconbtn portal-iconbtn--gov"
            type="button"
            id="toggleTheme"
            title="Alternar tema"
            aria-label="Alternar tema escuro"
          >
            ◐
          </button>

          <a href="{{ route('login') }}" class="btn portal-enter-btn portal-enter-btn--gov">
            Entrar com FIVEM.BC
          </a>
        </div>

      </div>
    </div>
  </div>

  {{-- HEADER --}}
  <header class="portal-header portal-header--gov">
    <div class="portal-container">

      <div class="portal-header-inner portal-header-inner--gov">
        <div class="d-flex align-items-center gap-2">
          <button
            class="portal-menu portal-menu--gov"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#menuPublico"
            aria-label="Abrir menu"
          >
            ☰
          </button>

          <div class="portal-org-title portal-org-title--gov">
            Grupo de Resposta Rápida
          </div>
        </div>

        {{-- BUSCA FUNCIONAL --}}
        <div class="portal-searchbox portal-searchbox--gov" role="search" aria-label="Buscar no portal">
          <input
            type="text"
            id="portalSearchInput"
            class="form-control portal-search-input"
            placeholder="Buscar no portal..."
            aria-label="Buscar no portal"
            autocomplete="off"
          >
          <button
            type="button"
            id="portalSearchClear"
            class="portal-search-clear"
            aria-label="Limpar busca"
            title="Limpar busca"
          >
            ✕
          </button>
        </div>
      </div>

      {{-- RESULTADOS DA BUSCA --}}
      <div id="portalSearchResults" class="portal-search-results" aria-live="polite"></div>

    </div>
  </header>

  {{-- PÍLULAS / ACESSOS RÁPIDOS --}}
  <div class="portal-services-bar portal-services-bar--gov">
    <div class="portal-services-row portal-services-row--gov">

      <div class="dropdown portal-pill-wrap">
        <button
          class="portal-pill portal-pill--gov portal-pill--dropdown"
          type="button"
          data-bs-toggle="dropdown"
          data-bs-auto-close="outside"
          aria-expanded="false"
        >
          <span class="portal-pill-ico">🔥</span>
          Serviços mais acessados do GRR
          <span class="portal-pill-caret">▾</span>
        </button>

        <ul class="dropdown-menu portal-pill-menu">
          <li>
            <a class="dropdown-item" href="{{ route('juridico') }}">
              <span class="ico">⚖️</span>
              <span class="txt">Jurídico</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('comunicados') }}">
              <span class="ico">📢</span>
              <span class="txt">Comunicados oficiais</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('recrutamento') }}">
              <span class="ico">📝</span>
              <span class="txt">Recrutamento</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('resultados.publicos') }}">
              <span class="ico">📊</span>
              <span class="txt">Resultados operacionais</span>
            </a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item" href="#atendimento">
              <span class="ico">💬</span>
              <span class="txt">Canais de atendimento</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="dropdown portal-pill-wrap">
        <button
          class="portal-pill portal-pill--gov portal-pill--dropdown"
          type="button"
          data-bs-toggle="dropdown"
          data-bs-auto-close="outside"
          aria-expanded="false"
        >
          <span class="portal-pill-ico">⭐</span>
          Serviços em destaque do GRR
          <span class="portal-pill-caret">▾</span>
        </button>

        <ul class="dropdown-menu portal-pill-menu">
          <li>
            <a class="dropdown-item" href="{{ route('governo') }}">
              <span class="ico">🏛️</span>
              <span class="txt">Governo da Cidade</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('legislacao') }}">
              <span class="ico">📜</span>
              <span class="txt">Legislação</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="#redes">
              <span class="ico">📣</span>
              <span class="txt">Redes sociais</span>
            </a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item is-strong" href="{{ route('login') }}">
              <span class="ico">🔒</span>
              <span class="txt">Área restrita (login)</span>
            </a>
          </li>
        </ul>
      </div>

    </div>
  </div>

  {{-- MENU LATERAL --}}
  <div class="offcanvas offcanvas-start portal-offcanvas" tabindex="-1" id="menuPublico" aria-labelledby="menuPublicoLabel">
    <div class="offcanvas-header">
      <h5 class="portal-offcanvas-title" id="menuPublicoLabel">
        <span>Menu</span>
        <span class="portal-offcanvas-badge">GRR • Portal</span>
      </h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>

    <div class="offcanvas-body">

      <div class="portal-offcanvas-brand">
        <div class="kicker">FIVEM.BC</div>
        <div class="title">Grupo de Resposta Rápida</div>
        <div class="desc">Acesso rápido às principais páginas do portal.</div>
      </div>

      <nav class="portal-simple-nav" aria-label="Navegação do portal">
        <a class="portal-simple-link" href="{{ route('portal') }}">
          <span class="ico">🏠</span>
          <span class="txt">
            <span class="name">Página inicial</span>
            <span class="sub">Voltar ao portal</span>
          </span>
        </a>

        <a class="portal-simple-link" href="{{ route('comunicados') }}">
          <span class="ico">📢</span>
          <span class="txt">
            <span class="name">Comunicados oficiais</span>
            <span class="sub">Avisos e notas do GRR</span>
          </span>
        </a>

        <a class="portal-simple-link" href="{{ route('governo') }}">
          <span class="ico">🏛️</span>
          <span class="txt">
            <span class="name">Governo da cidade</span>
            <span class="sub">Estrutura e informações</span>
          </span>
        </a>

        <a class="portal-simple-link" href="{{ route('legislacao') }}">
          <span class="ico">📚</span>
          <span class="txt">
            <span class="name">Legislação</span>
            <span class="sub">Leis e documentos</span>
          </span>
        </a>

        <a class="portal-simple-link" href="{{ route('recrutamento') }}">
          <span class="ico">📝</span>
          <span class="txt">
            <span class="name">Recrutamento</span>
            <span class="sub">Inscrições e etapas</span>
          </span>
        </a>

        <a class="portal-simple-link" href="{{ route('juridico') }}">
          <span class="ico">⚖️</span>
          <span class="txt">
            <span class="name">Jurídico</span>
            <span class="sub">Ordenamento e normas</span>
          </span>
        </a>

        <a class="portal-simple-link" href="{{ route('resultados.publicos') }}">
          <span class="ico">📊</span>
          <span class="txt">
            <span class="name">Resultados operacionais</span>
            <span class="sub">Dashboard público</span>
          </span>
        </a>
      </nav>

      <div class="portal-offcanvas-sep"></div>

      <div class="portal-offcanvas-cta">
        <a class="portal-offcanvas-btn" href="{{ route('login') }}">
          🔒 Acesso Operacional
        </a>
      </div>

      <div class="portal-offcanvas-foot">
        Dica: use o menu para navegar rapidamente entre as páginas do portal.
      </div>

    </div>
  </div>

  {{-- CONTEÚDO --}}
  <main class="portal-main">

    {{-- HERO --}}
    <section
      class="portal-hero"
      data-searchable
      data-title="Portal Oficial do GRR"
    >
      <div class="portal-container">
        <div
          class="portal-hero-banner portal-hero-banner--home"
          style="background-image:
            linear-gradient(180deg, rgba(0, 0, 0, .20), rgba(0, 0, 0, .60)),
            url('{{ asset('images/imgs.png') }}');"
        >
          <div class="portal-hero-overlay">
            <div class="portal-hero-badge">PORTAL OFICIAL • GRR</div>
            <h1 class="portal-hero-h1">Grupo de Resposta Rápida</h1>
            <p class="portal-hero-p">
              Portal institucional com acesso a comunicados, legislação, recrutamento,
              serviços públicos e área restrita para o efetivo autorizado.
            </p>

            <div class="portal-hero-actions d-flex flex-wrap gap-2 mt-4">
              <a href="{{ route('login') }}" class="btn portal-enter-btn portal-enter-btn--gov">
                Acessar área restrita
              </a>

              <a href="{{ route('comunicados') }}" class="btn btn-outline-light">
                Ver comunicados
              </a>
            </div>
          </div>
        </div>

        <div class="portal-dot"></div>
      </div>
    </section>

    {{-- ACESSOS RÁPIDOS --}}
    <section
      class="portal-section portal-section--compact"
      data-searchable
      data-title="Acessos rápidos"
    >
      <div class="portal-container">
        <div class="portal-section-head">
          <div>
            <div class="portal-section-kicker">ACESSO RÁPIDO</div>
            <h2 class="portal-section-title">Principais áreas do portal</h2>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('comunicados') }}" class="portal-service-card h-100">
              <div class="portal-service-ico">📢</div>
              <div class="portal-service-text">Comunicados</div>
            </a>
          </div>

          <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('governo') }}" class="portal-service-card h-100">
              <div class="portal-service-ico">🏛️</div>
              <div class="portal-service-text">Governo</div>
            </a>
          </div>

          <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('legislacao') }}" class="portal-service-card h-100">
              <div class="portal-service-ico">📜</div>
              <div class="portal-service-text">Legislação</div>
            </a>
          </div>

          <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('recrutamento') }}" class="portal-service-card h-100">
              <div class="portal-service-ico">📝</div>
              <div class="portal-service-text">Recrutamento</div>
            </a>
          </div>

          <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('juridico') }}" class="portal-service-card h-100">
              <div class="portal-service-ico">⚖️</div>
              <div class="portal-service-text">Jurídico</div>
            </a>
          </div>

          <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('resultados.publicos') }}" class="portal-service-card h-100">
              <div class="portal-service-ico">📊</div>
              <div class="portal-service-text">Resultados</div>
            </a>
          </div>
        </div>
      </div>
    </section>

    {{-- GALERIA OPERACIONAL GRR --}}
    <section
      class="portal-gallery-section"
      data-searchable
      data-title="Galeria Operacional da GRR"
    >
      <div class="portal-container">
        <div class="portal-gallery-wrap">

          <div class="portal-gallery-head">
            <div>
              <div class="portal-gallery-kicker">PRESENÇA • OPERAÇÃO • HONRA</div>
              <h2 class="portal-gallery-title">A força da GRR em cada missão, cada resposta e cada avanço operacional</h2>
              <p class="portal-gallery-text">
                Mais do que imagens, esta galeria representa a identidade da corporação:
                presença firme, resposta rápida, disciplina tática e compromisso absoluto com a missão.
                Cada registro reforça a grandeza do trabalho desenvolvido pela GRR e inspira confiança,
                orgulho e motivação para seguir elevando o nível da operação.
              </p>
            </div>

            <div class="portal-gallery-quote">
              <strong>Espírito operacional</strong>
              <p>
                Onde a maioria vê risco, a GRR vê dever. Onde o tempo pressiona,
                a GRR responde com estratégia, coragem e presença.
                Aqui, cada operação conta uma história de preparo, honra e determinação.
              </p>
            </div>
          </div>

          <div class="portal-gallery-grid">
            <article class="portal-gallery-card portal-gallery-card--lg">
              <div class="portal-gallery-img" style="background-image: url('{{ asset('images/GRR.png') }}');"></div>
              <div class="portal-gallery-overlay">
                <div class="portal-gallery-meta">
                  <div class="portal-gallery-badge">Linha de frente</div>
                  <h3 class="portal-gallery-name">Presença que impõe respeito</h3>
                  <p class="portal-gallery-desc">
                    A GRR atua com postura, controle e capacidade de resposta nos cenários mais críticos.
                  </p>
                </div>
              </div>
            </article>

            <article class="portal-gallery-card portal-gallery-card--lg">
              <div class="portal-gallery-img" style="background-image: url('{{ asset('images/grr-4.png') }}');"></div>
              <div class="portal-gallery-overlay">
                <div class="portal-gallery-meta">
                  <div class="portal-gallery-badge">Aéreo tático</div>
                  <h3 class="portal-gallery-name">Operação sem pausa, mesmo sob tempestade</h3>
                  <p class="portal-gallery-desc">
                    Apoio aéreo, coordenação e ação contínua em qualquer condição operacional.
                  </p>
                </div>
              </div>
            </article>

            <article class="portal-gallery-card portal-gallery-card--md">
              <div class="portal-gallery-img" style="background-image: url('{{ asset('images/grr-5.png') }}');"></div>
              <div class="portal-gallery-overlay">
                <div class="portal-gallery-meta">
                  <div class="portal-gallery-badge">Patrulha noturna</div>
                  <h3 class="portal-gallery-name">Vigilância total durante a noite</h3>
                  <p class="portal-gallery-desc">
                    Mobilidade, cobertura e presença aérea ampliando a capacidade de controle territorial.
                  </p>
                </div>
              </div>
            </article>

            <article class="portal-gallery-card portal-gallery-card--md">
              <div class="portal-gallery-img" style="background-image: url('{{ asset('images/grr-6.png') }}');"></div>
              <div class="portal-gallery-overlay">
                <div class="portal-gallery-meta">
                  <div class="portal-gallery-badge">Patrulhamento</div>
                  <h3 class="portal-gallery-name">A estrada também sente a força da GRR</h3>
                  <p class="portal-gallery-desc">
                    Presença ostensiva e mobilidade operacional para garantir resposta rápida em campo.
                  </p>
                </div>
              </div>
            </article>

            <article class="portal-gallery-card portal-gallery-card--md">
              <div class="portal-gallery-img" style="background-image: url('{{ asset('images/grr-7.png') }}');"></div>
              <div class="portal-gallery-overlay">
                <div class="portal-gallery-meta">
                  <div class="portal-gallery-badge">Pronto emprego</div>
                  <h3 class="portal-gallery-name">Preparo, precisão e cobertura</h3>
                  <p class="portal-gallery-desc">
                    Cada operador representa técnica, controle emocional e eficiência tática.
                  </p>
                </div>
              </div>
            </article>

            <article class="portal-gallery-card portal-gallery-card--sm">
              <div class="portal-gallery-img" style="background-image: url('{{ asset('images/grr-2.png') }}');"></div>
              <div class="portal-gallery-overlay">
                <div class="portal-gallery-meta">
                  <div class="portal-gallery-badge">Aviação</div>
                  <h3 class="portal-gallery-name">Coordenação aérea</h3>
                </div>
              </div>
            </article>

            <article class="portal-gallery-card portal-gallery-card--sm">
              <div class="portal-gallery-img" style="background-image: url('{{ asset('images/grr-3.png') }}');"></div>
              <div class="portal-gallery-overlay">
                <div class="portal-gallery-meta">
                  <div class="portal-gallery-badge">Entrada tática</div>
                  <h3 class="portal-gallery-name">Sincronia operacional</h3>
                </div>
              </div>
            </article>

            <article class="portal-gallery-card portal-gallery-card--sm">
              <div class="portal-gallery-img" style="background-image: url('{{ asset('images/grr-8.png') }}');"></div>
              <div class="portal-gallery-overlay">
                <div class="portal-gallery-meta">
                  <div class="portal-gallery-badge">Equipe</div>
                  <h3 class="portal-gallery-name">Cobertura e progressão</h3>
                </div>
              </div>
            </article>

            <article class="portal-gallery-card portal-gallery-card--sm">
              <div class="portal-gallery-img" style="background-image: url('{{ asset('images/grr-9.png') }}');"></div>
              <div class="portal-gallery-overlay">
                <div class="portal-gallery-meta">
                  <div class="portal-gallery-badge">Resposta imediata</div>
                  <h3 class="portal-gallery-name">Ação no terreno urbano</h3>
                </div>
              </div>
            </article>
          </div>

          <div class="portal-gallery-foot">
            <p class="portal-gallery-foot-text">
              A GRR representa mobilização, disciplina e superação. Cada imagem reforça que o compromisso da corporação
              não está apenas no discurso, mas na prática diária de quem entra em campo para manter a ordem,
              proteger a operação e elevar o nome da instituição.
            </p>

            <div class="portal-gallery-pill">
              ⚡ GRR 2.0 • Mais presença, mais identidade, mais impacto visual
            </div>
          </div>

        </div>
      </div>
    </section>

    {{-- BLOCO INSTITUCIONAL --}}
    <section
      class="portal-section"
      data-searchable
      data-title="Institucional do GRR"
    >
      <div class="portal-container">
        <div class="row g-4 align-items-stretch">
          <div class="col-lg-7">
            <div class="portal-panel-card h-100">
              <div class="portal-section-kicker">INSTITUCIONAL</div>
              <h2 class="portal-section-title mb-3">Compromisso com resposta rápida, organização e presença operacional</h2>
              <p class="mb-3">
                O Grupo de Resposta Rápida atua com foco em coordenação, presença institucional,
                apoio operacional e organização estratégica das atividades do portal e da corporação.
              </p>
              <p class="mb-0">
                Nesta plataforma, o cidadão e o efetivo autorizado encontram informações públicas,
                comunicados, canais de atendimento, acesso a legislação e recursos internos de gestão.
              </p>
            </div>
          </div>

          <div class="col-lg-5">
            <div class="portal-panel-card h-100">
              <div class="portal-section-kicker">DIRETRIZES</div>
              <ul class="portal-check-list mb-0">
                <li>Padronização institucional das informações</li>
                <li>Centralização de serviços e acessos rápidos</li>
                <li>Melhoria contínua da experiência do portal</li>
                <li>Ambiente preparado para expansão da versão 2.0</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- INDICADORES --}}
    <section
      class="portal-section portal-section--compact"
      data-searchable
      data-title="Indicadores institucionais"
    >
      <div class="portal-container">
        <div class="portal-section-head">
          <div>
            <div class="portal-section-kicker">VISÃO GERAL</div>
            <h2 class="portal-section-title">Indicadores do portal</h2>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-6 col-lg-3">
            <div class="portal-stat-card">
              <div class="portal-stat-value">24/7</div>
              <div class="portal-stat-label">Disponibilidade institucional</div>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <div class="portal-stat-card">
              <div class="portal-stat-value">6+</div>
              <div class="portal-stat-label">Áreas principais no portal</div>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <div class="portal-stat-card">
              <div class="portal-stat-value">100%</div>
              <div class="portal-stat-label">Foco em padronização</div>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <div class="portal-stat-card">
              <div class="portal-stat-value">2.0</div>
              <div class="portal-stat-label">Nova fase do projeto GRR</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- SERVIÇOS DO GRR --}}
    <section
      class="portal-prf-services-wrap"
      id="servicos"
      data-searchable
      data-title="Serviços do GRR"
    >
      <div class="portal-container">
        <div class="portal-actions-row">
          <a href="{{ route('comunicados') }}" class="portal-action-btn">
            Ver todos os comunicados
          </a>
        </div>

        <div class="portal-section-head">
          <div>
            <div class="portal-section-kicker">SERVIÇOS</div>
            <h2 class="portal-prf-title">Serviços do GRR</h2>
          </div>
        </div>

        <div class="portal-services-grid">
          <a href="{{ route('juridico') }}" class="portal-service-card">
            <div class="portal-service-ico">⚖️</div>
            <div class="portal-service-text">Jurídico</div>
          </a>

          <a href="{{ route('resultados.publicos') }}" class="portal-service-card">
            <div class="portal-service-ico">📊</div>
            <div class="portal-service-text">Resultados operacionais</div>
          </a>

          <a href="{{ route('governo') }}" class="portal-service-card">
            <div class="portal-service-ico">🏛️</div>
            <div class="portal-service-text">Governo da cidade</div>
          </a>

          <a href="{{ route('recrutamento') }}" class="portal-service-card">
            <div class="portal-service-ico">📝</div>
            <div class="portal-service-text">Recrutamento</div>
          </a>
        </div>
      </div>
    </section>

    {{-- COMUNICADOS / DESTAQUES --}}
    <section
      class="portal-grid"
      id="comunicados"
      data-searchable
      data-title="Comunicados e destaques"
    >
      <div class="portal-container">

        <div class="portal-section-head mb-3">
          <div>
            <div class="portal-section-kicker">DESTAQUES</div>
            <h2 class="portal-section-title">Comunicados e áreas em evidência</h2>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-4">
            <a href="{{ route('comunicados') }}" class="portal-news text-decoration-none">
              <div class="portal-news-img" style="background-image: url('{{ asset('images/Comunicadosoficiais.png') }}');"></div>
              <div class="portal-news-title">Comunicados oficiais</div>
              <div class="portal-news-text">Avisos institucionais, notas internas e informações de interesse geral do GRR.</div>
            </a>
          </div>

          <div class="col-md-4">
            <a href="{{ route('resultados.publicos') }}" class="portal-news text-decoration-none">
              <div class="portal-news-img" style="background-image: url('{{ asset('images/Operaçõesemandamento.png') }}');"></div>
              <div class="portal-news-title">Resultados operacionais</div>
              <div class="portal-news-text">Acompanhe painéis públicos, números e resumos operacionais do grupo.</div>
            </a>
          </div>

          <div class="col-md-4">
            <a href="{{ route('login') }}" class="portal-news text-decoration-none">
              <div class="portal-news-img" style="background-image: url('{{ asset('images/arearestrita.png') }}');"></div>
              <div class="portal-news-title">Área restrita</div>
              <div class="portal-news-text">Acesso interno para relatórios, auditoria, gestão institucional e recursos operacionais.</div>
            </a>
          </div>
        </div>
      </div>
    </section>

    {{-- REDES SOCIAIS --}}
    <section
      class="portal-section"
      id="redes"
      data-searchable
      data-title="Redes sociais"
    >
      <div class="portal-container">
        <div class="portal-section-head">
          <div>
            <div class="portal-section-kicker">CANAIS OFICIAIS</div>
            <h2 class="portal-section-title">Redes Sociais</h2>
          </div>
        </div>

        <div class="portal-mini-grid">
          <a href="#" class="portal-mini-card" aria-label="X (Twitter)">
            <span class="portal-mini-info" title="Informações">i</span>
            <div class="portal-mini-ico">𝕏</div>
            <div class="portal-mini-label">X (Twitter)</div>
          </a>

          <a href="#" class="portal-mini-card" aria-label="YouTube">
            <span class="portal-mini-info" title="Informações">i</span>
            <div class="portal-mini-ico">▶</div>
            <div class="portal-mini-label">YouTube</div>
          </a>

          <a href="#" class="portal-mini-card" aria-label="Facebook">
            <span class="portal-mini-info" title="Informações">i</span>
            <div class="portal-mini-ico">f</div>
            <div class="portal-mini-label">Facebook</div>
          </a>

          <a
            href="https://www.instagram.com/grr_roleplay/"
            class="portal-mini-card"
            aria-label="Instagram"
            target="_blank"
            rel="noopener noreferrer"
          >
            <span class="portal-mini-info" title="Informações">i</span>
            <div class="portal-mini-ico">◎</div>
            <div class="portal-mini-label">Instagram GRR</div>
          </a>
        </div>
      </div>
    </section>

    {{-- CANAIS DE ATENDIMENTO --}}
    <section
      class="portal-section"
      id="atendimento"
      data-searchable
      data-title="Canais de Atendimento"
    >
      <div class="portal-container">
        <div class="portal-section-head">
          <div>
            <div class="portal-section-kicker">ATENDIMENTO</div>
            <h2 class="portal-section-title">Canais de Atendimento</h2>
          </div>
        </div>

        <div class="portal-mini-grid portal-mini-grid--solid">
          <button
            type="button"
            class="portal-mini-card portal-mini-card--solid"
            data-bs-toggle="modal"
            data-bs-target="#modalAtendimento"
            data-tipo="Denúncia"
            aria-label="Denúncia"
          >
            <div class="portal-mini-ico">📣</div>
            <div class="portal-mini-label portal-mini-label--upper">DENÚNCIA</div>
          </button>

          <button
            type="button"
            class="portal-mini-card portal-mini-card--solid"
            data-bs-toggle="modal"
            data-bs-target="#modalAtendimento"
            data-tipo="Solicitação"
            aria-label="Solicitação"
          >
            <div class="portal-mini-ico">💬</div>
            <div class="portal-mini-label portal-mini-label--upper">SOLICITAÇÃO</div>
          </button>

          <button
            type="button"
            class="portal-mini-card portal-mini-card--solid"
            data-bs-toggle="modal"
            data-bs-target="#modalAtendimento"
            data-tipo="Sugestão"
            aria-label="Sugestão"
          >
            <div class="portal-mini-ico">🗨</div>
            <div class="portal-mini-label portal-mini-label--upper">SUGESTÃO</div>
          </button>

          <button
            type="button"
            class="portal-mini-card portal-mini-card--solid"
            data-bs-toggle="modal"
            data-bs-target="#modalAtendimento"
            data-tipo="Elogio"
            aria-label="Elogio"
          >
            <div class="portal-mini-ico">👍</div>
            <div class="portal-mini-label portal-mini-label--upper">ELOGIO</div>
          </button>
        </div>
      </div>
    </section>

    {{-- MODAL DE ATENDIMENTO --}}
    <div class="modal fade portal-modal" id="modalAtendimento" tabindex="-1" aria-labelledby="modalAtendimentoLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAtendimentoLabel">Canal de Atendimento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>

          <form method="POST" action="{{ route('atendimento.enviar') }}" id="formAtendimento">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

            <div class="modal-body">
              <div class="alert alert-info mb-3">
                Se possível, descreva com clareza e inclua informações úteis como local, envolvidos, horário e evidências.
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Tipo</label>
                  <input type="text" class="form-control" id="at_tipo_label" value="-" disabled>
                  <input type="hidden" name="tipo" id="at_tipo" value="">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Seu nome (opcional)</label>
                  <input type="text" class="form-control" name="nome" placeholder="Ex.: João Silva">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Contato (opcional)</label>
                  <input type="text" class="form-control" name="contato" placeholder="Ex.: Discord / telefone">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Assunto</label>
                  <input
                    type="text"
                    class="form-control"
                    name="assunto"
                    required
                    maxlength="80"
                    placeholder="Ex.: Atendimento no pedágio / Sugestão de melhoria"
                  >
                </div>

                <div class="col-12">
                  <label class="form-label">Mensagem</label>
                  <textarea
                    class="form-control"
                    name="mensagem"
                    required
                    rows="5"
                    maxlength="1500"
                    placeholder="Descreva o ocorrido ou sua solicitação..."
                  ></textarea>
                  <div class="form-text">Máximo de 1500 caracteres.</div>
                </div>

                <div class="col-12">
                  <label class="form-label">Link de prova (opcional)</label>
                  <input
                    type="url"
                    class="form-control"
                    name="prova_url"
                    placeholder="Ex.: link do vídeo ou print (imgur, streamable, etc.)"
                  >
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- FOOTER --}}
    <footer class="portal-footer" id="contato" data-searchable data-title="Rodapé e contato">
      <div class="portal-container">
        <div class="row g-4 align-items-start">
          <div class="col-lg-5">
            <div class="portal-footer-brand">
              <div class="portal-footer-title">GRR — Grupo de Resposta Rápida</div>
              <div class="portal-muted">
                Portal institucional destinado à centralização de serviços, comunicados,
                legislação e recursos operacionais da corporação.
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="portal-footer-col-title">Navegação</div>
            <div class="portal-footer-links d-flex flex-column gap-2">
              <a href="{{ route('portal') }}">Página inicial</a>
              <a href="{{ route('comunicados') }}">Comunicados</a>
              <a href="{{ route('legislacao') }}">Legislação</a>
              <a href="{{ route('recrutamento') }}">Recrutamento</a>
            </div>
          </div>

          <div class="col-md-6 col-lg-4">
            <div class="portal-footer-col-title">Acesso</div>
            <div class="portal-footer-links d-flex flex-column gap-2">
              <a href="{{ route('login') }}">Área restrita</a>
              <a href="#atendimento">Canais de atendimento</a>
              <a href="#redes">Redes sociais</a>
            </div>
          </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-between flex-wrap gap-2">
          <div>© {{ date('Y') }} GRR — Grupo de Resposta Rápida</div>
          <div class="portal-muted">FIVEM.BC • Portal Institucional • Versão 2.0</div>
        </div>
      </div>
    </footer>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- TEMA --}}
  <script>
    (function () {
      const STORAGE_KEY = 'grr_theme';
      const root = document.documentElement;
      const btn = document.getElementById('toggleTheme');

      function applyIcon(theme) {
        if (!btn) return;
        btn.textContent = theme === 'dark' ? '☀️' : '🌙';
        btn.setAttribute('title', theme === 'dark' ? 'Ativar tema claro' : 'Ativar tema escuro');
        btn.setAttribute('aria-label', theme === 'dark' ? 'Ativar tema claro' : 'Ativar tema escuro');
      }

      function setTheme(theme) {
        root.setAttribute('data-theme', theme);
        localStorage.setItem(STORAGE_KEY, theme);
        applyIcon(theme);
      }

      function getPreferredTheme() {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved === 'light' || saved === 'dark') return saved;

        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
          ? 'dark'
          : 'light';
      }

      setTheme(getPreferredTheme());

      if (btn) {
        btn.addEventListener('click', () => {
          const current = root.getAttribute('data-theme') || 'light';
          setTheme(current === 'dark' ? 'light' : 'dark');
        });
      }
    })();
  </script>

  {{-- MODAL DE ATENDIMENTO --}}
  <script>
    (function () {
      const modal = document.getElementById('modalAtendimento');
      if (!modal) return;

      const tipoHidden = document.getElementById('at_tipo');
      const tipoLabel = document.getElementById('at_tipo_label');
      const titulo = document.getElementById('modalAtendimentoLabel');

      modal.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        const tipo = trigger?.getAttribute('data-tipo') || 'Atendimento';

        if (tipoHidden) tipoHidden.value = tipo;
        if (tipoLabel) tipoLabel.value = tipo;
        if (titulo) titulo.textContent = `Canal de Atendimento — ${tipo}`;
      });
    })();
  </script>

  {{-- BUSCA DO PORTAL --}}
  <script>
    (() => {
      const MAX_RESULTS = 20;

      const normalize = (text) => (text || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/\s+/g, ' ')
        .trim();

      const escapeHTML = (text) =>
        (text || '').replace(/[&<>"']/g, (char) => ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;'
        }[char]));

      const debounce = (fn, wait = 120) => {
        let timeout = null;
        return (...args) => {
          clearTimeout(timeout);
          timeout = setTimeout(() => fn(...args), wait);
        };
      };

      const highlightHTML = (text, query) => {
        const safe = escapeHTML(text || '');
        if (!query) return safe;

        try {
          const re = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'ig');
          return safe.replace(re, '<mark class="portal-hl">$1</mark>');
        } catch {
          return safe;
        }
      };

      const nodes = Array.from(document.querySelectorAll('[data-searchable]'));

      const index = nodes.map((el, i) => {
        const title =
          el.getAttribute('data-title') ||
          (el.querySelector('h1,h2,h3,h4,.title,.name')?.innerText ?? 'Seção');

        const text = el.innerText || el.textContent || '';

        return {
          id: i,
          el,
          title: title.trim(),
          titleNorm: normalize(title),
          textRaw: text,
          textNorm: normalize(text)
        };
      });

      function doSearch(query) {
        const q = normalize(query);
        if (!q) return [];

        const results = [];
        for (const row of index) {
          if (row.titleNorm.includes(q) || row.textNorm.includes(q)) {
            results.push(row);
            if (results.length >= MAX_RESULTS) break;
          }
        }

        return results;
      }

      function focusHit(hitId) {
        const row = index.find(item => String(item.id) === String(hitId));
        if (!row) return;

        document.querySelectorAll('.portal-search-hit').forEach(el => {
          el.classList.remove('portal-search-hit');
        });

        row.el.classList.add('portal-search-hit');
        row.el.scrollIntoView({ behavior: 'smooth', block: 'start' });

        setTimeout(() => row.el.classList.remove('portal-search-hit'), 3500);
      }

      function closeResults(container) {
        container.classList.remove('is-open');
        container.innerHTML = '';
      }

      function render(container, results, query) {
        const q = normalize(query);

        if (!q) {
          closeResults(container);
          return;
        }

        container.classList.add('is-open');

        const header = `
          <div class="portal-searchresults-head">
            ${results.length ? `${results.length} resultado(s)` : 'Nenhum resultado encontrado'}
          </div>
        `;

        if (!results.length) {
          container.innerHTML = header + `
            <div class="portal-searchnone">
              🚫 Nenhum resultado encontrado. Tente outra palavra.
            </div>
          `;
          return;
        }

        const items = results.map((result) => {
          const raw = (result.textRaw || '').replace(/\s+/g, ' ').trim();
          const pos = normalize(raw).indexOf(q);

          let snippet = raw;

          if (pos > -1) {
            const start = Math.max(0, pos - 60);
            const end = Math.min(raw.length, pos + 90);
            snippet = (start > 0 ? '…' : '') + raw.slice(start, end) + (end < raw.length ? '…' : '');
          } else {
            snippet = raw.slice(0, 140) + (raw.length > 140 ? '…' : '');
          }

          return `
            <div class="portal-searchitem" role="button" tabindex="0" data-hit="${result.id}">
              <div class="tag">🔎</div>
              <div class="meta">
                <div class="title">${highlightHTML(result.title, q)}</div>
                <div class="snippet">${highlightHTML(snippet, q)}</div>
              </div>
            </div>
          `;
        }).join('');

        container.innerHTML = header + `<div class="portal-searchresults-list">${items}</div>`;
      }

      function setup() {
        const input = document.getElementById('portalSearchInput');
        const results = document.getElementById('portalSearchResults');
        const clear = document.getElementById('portalSearchClear');

        if (!input || !results) return;

        const run = debounce(() => {
          render(results, doSearch(input.value), input.value);
        }, 120);

        input.addEventListener('input', run);
        input.addEventListener('focus', run);

        results.addEventListener('click', (e) => {
          const item = e.target.closest('.portal-searchitem');
          if (!item) return;
          focusHit(item.getAttribute('data-hit'));
        });

        results.addEventListener('keydown', (e) => {
          if (e.key !== 'Enter') return;
          const item = e.target.closest('.portal-searchitem');
          if (!item) return;
          focusHit(item.getAttribute('data-hit'));
        });

        document.addEventListener('click', (e) => {
          const searchBox = document.querySelector('.portal-searchbox--gov');
          if (searchBox && searchBox.contains(e.target)) return;
          if (results.contains(e.target)) return;
          closeResults(results);
        });

        if (clear) {
          clear.addEventListener('click', () => {
            input.value = '';
            closeResults(results);
            input.focus();
          });
        }
      }

      setup();
    })();
  </script>

</body>
</html>