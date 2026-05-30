<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PRF • Cursos GRR</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="portal-body">

{{-- TOPBAR GOV (PADRÃO DO SITE) --}}
<div class="portal-topbar portal-topbar--gov">
    <div class="portal-container">
        <div class="portal-topbar-inner portal-topbar-inner--gov">

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="portal-govbrand">
                    <div class="portal-govlogo">fivem<span>.bc</span></div>
                </div>

                <span class="portal-sep">|</span>

                <span class="portal-muted">
                    Ministério da Justiça e Segurança Pública
                </span>

                <nav class="portal-links d-none d-lg-flex">
                    <a href="{{ url('/') }}" class="portal-toplink">Página inicial</a>
                    <a href="{{ route('recrutamento') }}" class="portal-toplink">Recrutamento</a>

                    <a
                        href="{{ route('cursos.prf') }}"
                        class="portal-toplink {{ request()->routeIs('cursos.prf') ? 'is-active' : '' }}"
                        @if(request()->routeIs('cursos.prf'))
                            aria-current="page"
                            tabindex="-1"
                            style="pointer-events:none; opacity:.65;"
                        @endif
                    >
                        Cursos PRF | GRR
                    </a>
                </nav>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button class="portal-iconbtn portal-iconbtn--gov"
                        type="button"
                        title="Alternar tema"
                        id="toggleTheme">◐</button>

                <a href="{{ route('recrutamento') }}" class="btn btn-outline-secondary d-none d-md-inline-flex">
                    Voltar
                </a>

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
                <button class="portal-menu portal-menu--gov" type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#menuPublico"
                        aria-label="Abrir menu">☰</button>

                <div class="portal-org-title portal-org-title--gov">
                    Cursos PRF • GRR
                </div>
            </div>
        </div>

        <div class="portal-services-bar portal-services-bar--gov">
            <div class="portal-services-row portal-services-row--gov">
                <a href="{{ route('recrutamento') }}" class="portal-pill portal-pill--gov text-decoration-none">
                    <span class="portal-pill-ico">📝</span>
                    Recrutamento
                    <span class="portal-pill-caret">▾</span>
                </a>

                <a href="#trilha" class="portal-pill portal-pill--gov text-decoration-none">
                    <span class="portal-pill-ico">🎓</span>
                    Trilha de formação
                    <span class="portal-pill-caret">▾</span>
                </a>

                <a href="#escala-rua" class="portal-pill portal-pill--gov text-decoration-none">
                    <span class="portal-pill-ico">🚓</span>
                    Escala de rua
                    <span class="portal-pill-caret">▾</span>
                </a>
            </div>
        </div>

    </div>
</header>

{{-- OFFCANVAS MENU --}}
<div class="offcanvas offcanvas-start portal-offcanvas"
     tabindex="-1"
     id="menuPublico"
     aria-labelledby="menuPublicoLabel">

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
      <div class="desc">Acesso rápido às páginas do portal.</div>
    </div>

    <nav class="portal-simple-nav" aria-label="Navegação do portal">

      <a class="portal-simple-link" href="{{ url('/') }}">
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
          <span class="sub">Avisos e notas</span>
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
          <span class="sub">Normas e ordenamento</span>
        </span>
      </a>

      <a class="portal-simple-link" href="{{ route('resultados.publicos') }}">
        <span class="ico">📊</span>
        <span class="txt">
          <span class="name">Resultados operacionais</span>
          <span class="sub">Dashboard público</span>
        </span>
      </a>

      <a class="portal-simple-link" href="{{ route('cursos.prf') }}">
        <span class="ico">🎓</span>
        <span class="txt">
          <span class="name">Cursos PRF</span>
          <span class="sub">Trilha de formação</span>
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
      Dica: use o menu para navegar rapidamente.
    </div>

  </div>
</div>

<main class="portal-main">

    {{-- HERO / APRESENTAÇÃO --}}
    <section class="portal-hero" style="margin-bottom:8px;">
        <div class="portal-container">
            <div class="portal-hero-banner" style="height:300px; --portal-hero-image:url('{{ asset('images/imgs5.png') }}');">
                <div class="portal-hero-overlay">
                    <div class="portal-hero-badge">FORMAÇÃO OPERACIONAL • PRF GRR</div>
                    <div class="portal-hero-h1" style="font-size:40px;">Trilha de Cursos da GRR</div>
                    <div class="portal-hero-p">
                        Formação institucional, técnica e operacional para padronizar postura,
                        reduzir erro de procedimento e elevar o nível do patrulhamento.
                    </div>
                </div>
            </div>
            <div class="portal-dot"></div>
        </div>
    </section>

    <section class="portal-legal" id="trilha">
        <div class="portal-container">

            <div class="portal-legal-head">
                <h1 class="portal-legal-title">🎓 Trilha de Formação — GRR</h1>
                <div class="portal-legal-alert">
                    <div class="portal-legal-alert-ico">ℹ️</div>
                    <div class="portal-legal-alert-text">
                        Esta página organiza a jornada de capacitação dos agentes da GRR, reunindo a trilha base,
                        os cursos obrigatórios para atuação em rua e os requisitos mínimos por função operacional.
                    </div>
                </div>
            </div>

            {{-- VISÃO GERAL --}}
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="portal-legal-card h-100">
                        <h2>📌 Objetivo</h2>
                        <p class="portal-muted mb-0">
                            Garantir padronização institucional, segurança na atuação, domínio procedimental
                            e melhor desempenho operacional em serviço.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="portal-legal-card h-100">
                        <h2>🧭 Estrutura</h2>
                        <p class="portal-muted mb-0">
                            A formação está dividida entre trilha principal, cursos obrigatórios para patrulhamento
                            e exigências específicas conforme a função exercida na viatura.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="portal-legal-card h-100">
                        <h2>✅ Resultado Esperado</h2>
                        <p class="portal-muted mb-0">
                            Formar agentes mais preparados, disciplinados e alinhados com o padrão técnico e
                            institucional exigido pela GRR.
                        </p>
                    </div>
                </div>
            </div>

            {{-- TRILHA PRINCIPAL --}}
            <div class="portal-legal-card" style="margin-top:14px;">
                <h2>🛣️ Etapas da Trilha Principal</h2>
                <p class="portal-muted">
                    A progressão abaixo representa a base ideal de formação para o(a) policial,
                    desde a integração até a validação final de aptidão operacional.
                </p>

                <div class="row g-3 mt-1">
                    <div class="col-md-6 col-lg-4">
                        <div class="portal-legal-card h-100">
                            <div class="portal-muted mb-1"><b>ETAPA 01</b></div>
                            <h2>1) Integração & Postura</h2>
                            <p class="portal-muted">Primeiro contato com a identidade institucional e o padrão esperado em serviço.</p>
                            <ul style="margin:0;">
                                <li>Conduta, respeito e disciplina</li>
                                <li>Hierarquia e postura institucional</li>
                                <li>Comunicação operacional e QRA</li>
                                <li>Normas internas e alinhamento inicial</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="portal-legal-card h-100">
                            <div class="portal-muted mb-1"><b>ETAPA 02</b></div>
                            <h2>2) Abordagem & Condução</h2>
                            <p class="portal-muted">Capacitação para controle de cena, segurança da equipe e condução correta da ocorrência.</p>
                            <ul style="margin:0;">
                                <li>Abordagem pessoal e veicular</li>
                                <li>Revista, verbalização e contenção</li>
                                <li>Condução segura e procedural</li>
                                <li>Registro e narrativa coerente</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="portal-legal-card h-100">
                            <div class="portal-muted mb-1"><b>ETAPA 03</b></div>
                            <h2>3) Trânsito & Operações</h2>
                            <p class="portal-muted">Aplicação prática em fiscalização, organização de fluxo e atuação em cenário rodoviário.</p>
                            <ul style="margin:0;">
                                <li>Blitz e checkpoints</li>
                                <li>Fiscalização e regularização</li>
                                <li>Bloqueios e controle de fluxo</li>
                                <li>Coordenação operacional de rua</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="portal-legal-card h-100">
                            <div class="portal-muted mb-1"><b>ETAPA 04</b></div>
                            <h2>4) Acompanhamento & Uso Progressivo</h2>
                            <p class="portal-muted">Formação sobre perseguição, cerco, interceptação e tomada de decisão com progressão de força.</p>
                            <ul style="margin:0;">
                                <li>Técnicas de acompanhamento</li>
                                <li>Cerco e contenção</li>
                                <li>Condições permitidas de intervenção</li>
                                <li>Autorização e responsabilidade operacional</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="portal-legal-card h-100">
                            <div class="portal-muted mb-1"><b>ETAPA 05</b></div>
                            <h2>5) Armamento & Regras RDPRF</h2>
                            <p class="portal-muted">Consolidação técnica e disciplinar voltada ao uso correto do material institucional.</p>
                            <ul style="margin:0;">
                                <li>Armamento autorizado</li>
                                <li>Limite de munições</li>
                                <li>Responsabilidade no uso do material</li>
                                <li>Sanções, desvios e enquadramentos</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="portal-legal-card h-100">
                            <div class="portal-muted mb-1"><b>ETAPA 06</b></div>
                            <h2>6) Avaliação Final</h2>
                            <p class="portal-muted">Etapa de verificação do preparo técnico, comportamental e institucional do agente.</p>
                            <ul style="margin:0;">
                                <li>Teste de procedimento</li>
                                <li>Validação de postura e disciplina</li>
                                <li>Análise de conduta operacional</li>
                                <li>Aprovação, reforço ou reciclagem</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CURSOS OBRIGATÓRIOS --}}
            <div class="portal-legal-card" style="margin-top:14px;">
                <h2>🚓 Cursos Obrigatórios para Patrulhamento</h2>

                <p class="portal-muted">
                    Para atuar em patrulhamento operacional, o agente deverá concluir os cursos abaixo,
                    garantindo preparo técnico, jurídico, comunicacional e procedimental.
                </p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="portal-legal-card h-100">
                            <h2>📚 Formação Base</h2>
                            <ul style="margin:0;">
                                <li><b>POP / Carceragem</b><br>Procedimento Operacional Padrão aplicado às rotinas da unidade e conduções.</li>
                                <li><b>Modulação, BO e TCO</b><br>Comunicação via rádio, linguagem operacional, elaboração de registro e formalização.</li>
                                <li><b>Abordagem e Posicionamento</b><br>Técnicas de abordagem pessoal e veicular, leitura de risco e segurança da equipe.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="portal-legal-card h-100">
                            <h2>🎯 Formação Técnica</h2>
                            <ul style="margin:0;">
                                <li><b>Tiro Básico</b><br>Fundamentos do manuseio seguro do armamento, disciplina e regras essenciais.</li>
                                <li><b>Tiro Avançado</b><br>Progressão de força, tomada de decisão, precisão e responsabilidade operacional.</li>
                                <li><b>CLT — Curso de Legislação de Trânsito</b><br>Normas, infrações, enquadramentos e procedimentos de fiscalização.</li>
                                <li><b>SAT-B — Sistema Avaliativo de Trânsito (Nível B)</b><br>Validação prática e teórica para atuação em trânsito e condução operacional.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="portal-legal-alert mt-3">
                    <div class="portal-legal-alert-ico">✅</div>
                    <div class="portal-legal-alert-text">
                        A conclusão desses cursos fortalece a padronização da equipe e reduz falhas de procedimento,
                        comunicação e tomada de decisão em rua.
                    </div>
                </div>
            </div>

            {{-- ESCALA DE RUA --}}
            <div class="portal-legal-card" id="escala-rua" style="margin-top:14px;">
                <h2>📢 Informativos Operacionais — Escala de Rua</h2>

                <p class="portal-muted">
                    Para atuação em patrulhamento operacional, torna-se obrigatória a conclusão
                    dos cursos abaixo, conforme a função exercida na viatura.
                </p>

                <div class="row g-3 mt-1">
                    <div class="col-md-6 col-xl-3">
                        <div class="portal-legal-card h-100">
                            <h2>🚗 P1 — Motorista</h2>
                            <p class="portal-muted">Responsável pela condução, mobilidade tática e segurança da viatura.</p>
                            <ul style="margin:0;">
                                <li><b>SAT-B</b></li>
                                <li><b>Abordagem e Posicionamento</b></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="portal-legal-card h-100">
                            <h2>🎯 P2 — Chefe de Barca</h2>
                            <p class="portal-muted">Responsável pela liderança da guarnição, decisões táticas e condução da ocorrência.</p>
                            <ul style="margin:0;">
                                <li><b>Tiro Básico</b></li>
                                <li><b>SAT-B</b></li>
                                <li><b>Modulação, BO e TCO</b></li>
                                <li><b>POP / Carceragem</b></li>
                                <li><b>Abordagem e Posicionamento</b></li>
                                <li><b>CLT</b></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="portal-legal-card h-100">
                            <h2>🧍 P3 — Auxiliar</h2>
                            <p class="portal-muted">Atuação de apoio na abordagem, segurança da cena e acompanhamento do procedimento.</p>
                            <ul style="margin:0;">
                                <li><b>POP / Carceragem</b></li>
                                <li><b>Abordagem e Posicionamento</b></li>
                                <li><b>CLT</b></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="portal-legal-card h-100">
                            <h2>🧍‍♂️ P4 — Auxiliar do P3</h2>
                            <p class="portal-muted">Suporte complementar da composição, reforçando segurança e execução procedural.</p>
                            <ul style="margin:0;">
                                <li><b>POP / Carceragem</b></li>
                                <li><b>Abordagem e Posicionamento</b></li>
                                <li><b>CLT</b></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="portal-legal-alert mt-3">
                    <div class="portal-legal-alert-ico">⚠️</div>
                    <div class="portal-legal-alert-text">
                        Para exercer a função de <b>Chefe de Barca (P2)</b>, o agente deverá possuir,
                        obrigatoriamente, no mínimo a patente de <b>Agente de 3º Classe</b>.
                    </div>
                </div>

                <div class="portal-legal-alert mt-3">
                    <div class="portal-legal-alert-ico">⚠️</div>
                    <div class="portal-legal-alert-text">
                        Ressalta-se que <b>não é obrigatória</b> a composição de viaturas com quatro integrantes.
                        Em escalas reduzidas, será necessária autorização prévia da
                        <b>Diretoria, Coordenadores, Vice-Diretor e Diretor</b>.
                    </div>
                </div>

                <p class="portal-muted mt-3 mb-0">
                    Recomenda-se que o(a) aluno(a) busque a realização dos cursos o quanto antes,
                    evitando impedimentos para atuação operacional. As solicitações devem ser realizadas no
                    <b>Discord Oficial SSP — Brasil Capital</b>, no canal <b>🧨 | SOLICITAR CURSOS</b>.
                    Fique atento também ao canal <b>CURSOS-HORÁRIOS</b>.
                </p>
            </div>

            {{-- ORIENTAÇÃO FINAL --}}
            <div class="portal-legal-card" style="margin-top:14px;">
                <h2>📌 Orientações Importantes</h2>
                <ul style="margin:0;">
                    <li>Busque concluir os cursos com antecedência para evitar bloqueios na escala operacional.</li>
                    <li>Mantenha-se atento aos comunicados, horários e convocações divulgadas nos canais oficiais.</li>
                    <li>Em caso de dúvida sobre requisito, liberação ou função na viatura, consulte previamente a supervisão responsável.</li>
                    <li>A formação não deve ser vista apenas como exigência, mas como preparação real para atuação correta em serviço.</li>
                </ul>
            </div>

            {{-- DÚVIDAS --}}
            <div class="portal-legal-card" style="margin-top:14px;">
                <h2>❓ Dúvidas?</h2>
                <p style="margin:0;">
                    Abra um ticket via Discord com prints e/ou vídeo, explicando a situação de forma clara e objetiva.<br>
                    🔗 <a href="https://discord.gg/brasilcapital" target="_blank" rel="noopener noreferrer">Acessar Discord oficial</a>
                </p>
            </div>

        </div>
    </section>

    <footer class="portal-footer">
        <div class="portal-container">
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <div>© {{ date('Y') }} GRR — Grupo de Resposta Rápida</div>
                <div class="portal-muted">FIVEM.BC • Portal Institucional</div>
            </div>
        </div>
    </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- THEME (GLOBAL PADRÃO DO SITE) --}}
<script>
(function () {
    const STORAGE_KEY = "grr_theme";
    const root = document.documentElement;
    const btn = document.getElementById("toggleTheme");

    function applyIcon(theme) {
        if (!btn) return;
        btn.textContent = theme === "dark" ? "☀️" : "🌙";
    }

    function setTheme(theme) {
        root.setAttribute("data-theme", theme);
        try { localStorage.setItem(STORAGE_KEY, theme); } catch(e) {}
        applyIcon(theme);
    }

    function getPreferredTheme() {
        let saved = null;
        try { saved = localStorage.getItem(STORAGE_KEY); } catch(e) {}
        if (saved === "light" || saved === "dark") return saved;
        return (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches) ? "dark" : "light";
    }

    setTheme(getPreferredTheme());

    btn?.addEventListener("click", () => {
        const current = root.getAttribute("data-theme") || "light";
        setTheme(current === "dark" ? "light" : "dark");
    });
})();
</script>

</body>
</html>