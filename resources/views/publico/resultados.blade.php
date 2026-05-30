<!DOCTYPE html>
<html lang="pt-br" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRR • Resultados Operacionais — Portal</title>
    <meta name="description" content="Painel público de resultados operacionais da GRR com dados resumidos, indicadores e transparência institucional.">

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="portal-body">

    {{-- TOPBAR --}}
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
                    </nav>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button
                        class="portal-iconbtn portal-iconbtn--gov"
                        type="button"
                        id="toggleTheme"
                        title="Alternar tema"
                        aria-label="Alternar tema escuro"
                    >◐</button>

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
                            aria-label="Abrir menu">
                        ☰
                    </button>

                    <div class="portal-org-title portal-org-title--gov">
                        Resultados Operacionais • GRR
                    </div>
                </div>
            </div>

            {{-- MENU --}}
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

                        <a class="portal-simple-link active" href="{{ route('resultados.publicos') }}">
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
                        Dica: utilize o menu para navegar rapidamente entre as áreas institucionais da GRR.
                    </div>

                </div>
            </div>

            {{-- PÍLULAS --}}
            <div class="portal-services-bar portal-services-bar--gov">
                <div class="portal-services-row portal-services-row--gov">
                    <a href="{{ url('/') }}" class="portal-pill portal-pill--gov text-decoration-none">
                        <span class="portal-pill-ico">🏠</span>
                        Página inicial
                        <span class="portal-pill-caret">▾</span>
                    </a>

                    <button class="portal-pill portal-pill--gov" type="button">
                        <span class="portal-pill-ico">📊</span>
                        Transparência e indicadores operacionais
                        <span class="portal-pill-caret">▾</span>
                    </button>
                </div>
            </div>

        </div>
    </header>

    {{-- CONTEÚDO --}}
    <main class="portal-main">

        {{-- HERO --}}
        <section class="portal-hero">
            <div class="portal-container">
                <div class="portal-hero-banner"
                     style="background-image:
                        linear-gradient(180deg, rgba(2,6,23,.18), rgba(2,6,23,.72)),
                        url('{{ asset('images/imgs6.png') }}');
                        background-size: cover;
                        background-position: center;
                        background-repeat: no-repeat;">

                    <div class="portal-hero-overlay">
                        <div class="portal-hero-badge">PAINEL PÚBLICO • GRR</div>

                        <div class="portal-hero-h1">
                            Resultados Operacionais
                        </div>

                        <div class="portal-hero-p">
                            Acompanhe os principais números públicos da atuação da GRR com foco em
                            <b>transparência institucional</b>, <b>organização operacional</b> e
                            <b>prestação de contas à sociedade</b>.
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="portal-pill portal-pill--gov">
                                <span class="portal-pill-ico">📈</span>
                                Indicadores atualizados
                            </span>

                            <span class="portal-pill portal-pill--gov">
                                <span class="portal-pill-ico">🛡️</span>
                                Dados públicos resumidos
                            </span>

                            <span class="portal-pill portal-pill--gov">
                                <span class="portal-pill-ico">🏛️</span>
                                Transparência institucional
                            </span>
                        </div>
                    </div>
                </div>

                <div class="portal-dot"></div>
            </div>
        </section>

        {{-- PAINEL --}}
        <section class="portal-legal" style="padding-top: 14px; padding-bottom: 28px;">
            <div class="portal-container">

                <div class="portal-legal-head">
                    <h1 class="portal-legal-title d-flex align-items-center gap-2">
                        <span>📊</span>
                        <span>Painel Público de Resultados</span>
                    </h1>

                    <div class="portal-legal-alert">
                        <div class="portal-legal-alert-ico">ℹ️</div>
                        <div class="portal-legal-alert-text">
                            Este ambiente exibe apenas <b>dados públicos e consolidados</b>. Informações estratégicas,
                            operacionais sensíveis e registros internos permanecem restritos ao efetivo autorizado.
                        </div>
                    </div>
                </div>

                {{-- RESUMO --}}
                <div class="portal-legal-card mb-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <div style="font-size: .78rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #64748b;">
                                Visão geral
                            </div>
                            <h2 style="margin: 6px 0 6px; font-size: 1.35rem;">
                                Desempenho operacional da GRR
                            </h2>
                            <p style="margin: 0; color: #64748b;">
                                Indicadores resumidos das ações registradas no período atual, com foco em produtividade,
                                presença operacional e resultados relevantes.
                            </p>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('publico.home') }}" class="btn portal-enter-btn portal-enter-btn--gov">
                                Voltar ao Portal
                            </a>
                        </div>
                    </div>
                </div>

                {{-- KPIs --}}
                <div class="row g-3 mb-4">

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="portal-news h-100" style="padding: 18px;">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <div class="portal-news-title" style="font-size: .82rem; text-transform: uppercase; letter-spacing: .05em;">
                                        Operações no mês
                                    </div>
                                    <div class="portal-news-text" style="font-size: 2rem; font-weight: 900; line-height: 1; margin-top: 10px;">
                                        {{ number_format($kpis['operacoes_mes'] ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div style="font-size: .92rem; color: #64748b; margin-top: 10px;">
                                        Total de operações registradas no período atual.
                                    </div>
                                </div>
                                <div style="font-size: 1.6rem;">🚔</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="portal-news h-100" style="padding: 18px;">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <div class="portal-news-title" style="font-size: .82rem; text-transform: uppercase; letter-spacing: .05em;">
                                        Abordagens no mês
                                    </div>
                                    <div class="portal-news-text" style="font-size: 2rem; font-weight: 900; line-height: 1; margin-top: 10px;">
                                        {{ number_format($kpis['abordagens_mes'] ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div style="font-size: .92rem; color: #64748b; margin-top: 10px;">
                                        Intervenções e verificações realizadas pelas equipes.
                                    </div>
                                </div>
                                <div style="font-size: 1.6rem;">🧍</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="portal-news h-100" style="padding: 18px;">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <div class="portal-news-title" style="font-size: .82rem; text-transform: uppercase; letter-spacing: .05em;">
                                        Apreensões no mês
                                    </div>
                                    <div class="portal-news-text" style="font-size: 2rem; font-weight: 900; line-height: 1; margin-top: 10px;">
                                        {{ number_format($kpis['apreensoes_mes'] ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div style="font-size: .92rem; color: #64748b; margin-top: 10px;">
                                        Materiais e itens retirados de circulação.
                                    </div>
                                </div>
                                <div style="font-size: 1.6rem;">📦</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="portal-news h-100" style="padding: 18px;">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <div class="portal-news-title" style="font-size: .82rem; text-transform: uppercase; letter-spacing: .05em;">
                                        Veículos recuperados
                                    </div>
                                    <div class="portal-news-text" style="font-size: 2rem; font-weight: 900; line-height: 1; margin-top: 10px;">
                                        {{ number_format($kpis['veiculos_recuperados'] ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div style="font-size: .92rem; color: #64748b; margin-top: 10px;">
                                        Ocorrências com recuperação confirmada no período.
                                    </div>
                                </div>
                                <div style="font-size: 1.6rem;">🚘</div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- GRID INFERIOR --}}
                <div class="row g-3">

                    {{-- APREENSÕES --}}
                    <div class="col-12 col-lg-7">
                        <div class="portal-legal-card h-100">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <h2 style="margin: 0;">📌 Apreensões por categoria</h2>
                                <span class="portal-pill portal-pill--gov">
                                    <span class="portal-pill-ico">📊</span>
                                    Consolidado público
                                </span>
                            </div>

                            @if(!empty($apreensoes_por_tipo))
                                <div class="d-flex flex-column gap-2">
                                    @foreach($apreensoes_por_tipo as $item)
                                        <div style="
                                            border: 1px solid rgba(148,163,184,.20);
                                            border-radius: 14px;
                                            padding: 12px 14px;
                                            background: rgba(255,255,255,.02);
                                        ">
                                            <div class="d-flex align-items-center justify-content-between gap-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span style="font-size: 1.1rem;">📦</span>
                                                    <div>
                                                        <div style="font-weight: 800;">
                                                            {{ $item['tipo'] ?? '-' }}
                                                        </div>
                                                        <div style="font-size: .92rem; color: #64748b;">
                                                            Total consolidado registrado nesta categoria
                                                        </div>
                                                    </div>
                                                </div>

                                                <div style="
                                                    min-width: 64px;
                                                    text-align: center;
                                                    padding: 8px 12px;
                                                    border-radius: 999px;
                                                    font-weight: 900;
                                                    background: rgba(15,23,42,.08);
                                                ">
                                                    {{ number_format($item['total'] ?? 0, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="
                                    border: 1px dashed rgba(148,163,184,.35);
                                    border-radius: 16px;
                                    padding: 18px;
                                    color: #64748b;
                                    background: rgba(148,163,184,.05);
                                ">
                                    Nenhum dado público disponível no momento.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- BLOCO LATERAL --}}
                    <div class="col-12 col-lg-5">
                        <div class="portal-legal-card h-100">
                            <h2 style="margin-bottom: 12px;">🛡️ Transparência institucional</h2>

                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <div style="font-weight: 800; margin-bottom: 4px;">Publicação responsável</div>
                                    <div style="color: #64748b;">
                                        Os números apresentados neste painel têm caráter informativo e representam dados públicos consolidados da atuação operacional.
                                    </div>
                                </div>

                                <div>
                                    <div style="font-weight: 800; margin-bottom: 4px;">Proteção de informações</div>
                                    <div style="color: #64748b;">
                                        Dados estratégicos, nomes, relatórios detalhados e elementos sensíveis permanecem protegidos e acessíveis apenas ao efetivo autorizado.
                                    </div>
                                </div>

                                <div>
                                    <div style="font-weight: 800; margin-bottom: 4px;">Objetivo do painel</div>
                                    <div style="color: #64748b;">
                                        Reforçar a transparência, valorizar o trabalho operacional da GRR e demonstrar resultados de forma clara à comunidade.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        {{-- FOOTER --}}
        <footer class="portal-footer" id="contato">
            <div class="portal-container">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>© {{ date('Y') }} GRR — Grupo de Resposta Rápida</div>
                    <div class="portal-muted">FIVEM.BC • Portal Institucional • Resultados Operacionais</div>
                </div>
            </div>
        </footer>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- JS DO TEMA --}}
    <script>
        (function () {
            const STORAGE_KEY = "grr_theme";
            const root = document.documentElement;
            const btn = document.getElementById("toggleTheme");

            function applyIcon(theme) {
                if (!btn) return;
                btn.textContent = theme === "dark" ? "☀️" : "🌙";
                btn.setAttribute("title", theme === "dark" ? "Ativar tema claro" : "Ativar tema escuro");
                btn.setAttribute("aria-label", theme === "dark" ? "Ativar tema claro" : "Ativar tema escuro");
            }

            function setTheme(theme) {
                root.setAttribute("data-theme", theme);
                localStorage.setItem(STORAGE_KEY, theme);
                applyIcon(theme);
            }

            function getPreferredTheme() {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved === "light" || saved === "dark") return saved;

                return (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches)
                    ? "dark"
                    : "light";
            }

            setTheme(getPreferredTheme());

            if (btn) {
                btn.addEventListener("click", () => {
                    const current = root.getAttribute("data-theme") || "light";
                    setTheme(current === "dark" ? "light" : "dark");
                });
            }
        })();
    </script>

</body>
</html>