<!DOCTYPE html>
<html lang="pt-br" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRR • Vídeo Explicativo do Sistema</title>
    <meta name="description" content="Vídeo Explicativo do Sistema da GRR.">
    <meta name="theme-color" content="#0b1220">

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root{
            --comm-bg-soft: rgba(255,255,255,.94);
            --comm-border: rgba(148,163,184,.18);
            --comm-shadow: 0 14px 34px rgba(2,8,23,.10);
            --comm-text: #0B2A4A;
            --comm-muted: #5b6b80;
            --comm-panel: linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.98));
            --comm-toolbar-bg: linear-gradient(180deg, rgba(255,255,255,.98), rgba(245,247,250,.98));
            --comm-soft-line: rgba(226,232,240,.9);
        }

        html[data-theme="dark"]{
            --comm-bg-soft: rgba(15,23,42,.90);
            --comm-border: rgba(255,255,255,.08);
            --comm-shadow: 0 18px 40px rgba(0,0,0,.32);
            --comm-text: #e5eef8;
            --comm-muted: #9fb0c8;
            --comm-panel: linear-gradient(180deg, rgba(15,23,42,.95), rgba(18,28,48,.93));
            --comm-toolbar-bg: linear-gradient(180deg, rgba(15,23,42,.97), rgba(18,28,48,.95));
            --comm-soft-line: rgba(255,255,255,.08);
        }

        .portal-header--gov{
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }

        .portal-org-title--gov{
            font-size: clamp(1.45rem, 2.5vw, 2.1rem);
            font-weight: 900;
            letter-spacing: -.02em;
        }

        .comm-shortcuts{
            display:grid;
            grid-template-columns: repeat(3, minmax(0,1fr));
            gap:12px;
            margin-top:18px;
        }

        .comm-shortcut{
            display:flex;
            align-items:center;
            gap:10px;
            min-height:54px;
            padding:0 16px;
            border-radius:16px;
            color:#e8eef7;
            background:linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
            border:1px solid rgba(255,255,255,.08);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.05);
            font-weight:800;
            transition:.2s ease;
        }

        .comm-shortcut:hover{
            transform: translateY(-1px);
            border-color: rgba(255,255,255,.16);
            background:linear-gradient(180deg, rgba(255,255,255,.09), rgba(255,255,255,.05));
            color:#fff;
        }

        .comm-shortcut-ico{
            width:30px;
            height:30px;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:rgba(11,94,215,.16);
            font-size:14px;
            flex-shrink:0;
        }

        .comm-hero-meta{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
            margin-top:14px;
        }

        .comm-highlight-badge{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:8px 14px;
            border-radius:999px;
            font-size:13px;
            font-weight:800;
            color:#fff;
            background:rgba(255,255,255,.14);
            border:1px solid rgba(255,255,255,.18);
            backdrop-filter: blur(8px);
        }

        .portal-legal{
            padding-top:34px;
        }

        .portal-legal-head{
            margin-bottom:22px;
        }

        .portal-legal-title{
            margin:0 0 14px;
            font-size: clamp(2rem, 3vw, 2.8rem);
            line-height:1.05;
            font-weight:900;
            letter-spacing:-.03em;
        }

        .portal-legal-alert{
            border-radius:18px;
            border:1px solid var(--comm-border);
            background: rgba(255,255,255,.04);
            backdrop-filter: blur(8px);
            padding:16px 18px;
        }

        .comm-highlight{
            display:grid;
            grid-template-columns: 1.15fr .85fr;
            gap:16px;
            margin-top:18px;
            margin-bottom:18px;
        }

        .comm-panel,
        .comm-stat,
        .comm-toolbar,
        .portal-legal-card{
            border-radius:24px !important;
            border:1px solid var(--comm-border) !important;
            background: var(--comm-panel) !important;
            box-shadow: var(--comm-shadow) !important;
            color: var(--comm-text);
        }

        .comm-panel{
            overflow:hidden;
        }

        .comm-panel-head{
            padding:20px 22px 0;
        }

        .comm-panel-body{
            padding:18px 22px 22px;
        }

        .comm-panel-kicker{
            font-size:12px;
            letter-spacing:.08em;
            font-weight:800;
            text-transform:uppercase;
            color:#0B5ED7 !important;
            margin-bottom:8px;
        }

        .comm-panel-title{
            margin:0;
            font-size:1.2rem;
            font-weight:900;
            color: var(--comm-text) !important;
        }

        .comm-panel-text{
            margin:10px 0 0;
            color: var(--comm-muted) !important;
            line-height:1.75;
        }

        .comm-priority-list{
            display:grid;
            gap:12px;
        }

        .comm-priority-item{
            display:flex;
            align-items:flex-start;
            gap:12px;
            padding:14px;
            border-radius:18px;
            background: rgba(255,255,255,.04) !important;
            border:1px solid var(--comm-border) !important;
        }

        .comm-priority-bullet{
            width:38px;
            height:38px;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:16px;
            background:#0B2A4A;
            color:#fff;
            flex-shrink:0;
        }

        .comm-priority-item strong{
            color: var(--comm-text);
        }

        .comm-priority-item span{
            color: var(--comm-muted) !important;
        }

        .comm-stats{
            display:grid;
            grid-template-columns: repeat(4, minmax(0,1fr));
            gap:14px;
            margin-bottom:18px;
        }

        .comm-stat{
            padding:18px 16px;
        }

        .comm-stat-label{
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:.08em;
            font-weight:800;
            color: var(--comm-muted) !important;
            margin-bottom:8px;
        }

        .comm-stat-value{
            font-size:2rem;
            line-height:1;
            font-weight:900;
            color: var(--comm-text) !important;
        }

        .comm-stat-sub{
            margin-top:8px;
            font-size:13px;
            color: var(--comm-muted) !important;
        }

        .comm-toolbar{
            padding:20px;
            background: var(--comm-toolbar-bg) !important;
            margin-bottom:16px;
        }

        .comm-toolbar .form-label{
            font-weight:800;
            color: var(--comm-text) !important;
            margin-bottom:6px;
        }

        .comm-toolbar .form-control,
        .comm-toolbar .form-select{
            min-height:50px;
            border-radius:14px;
            border:1px solid rgba(148,163,184,.28);
            box-shadow:none;
            font-weight:600;
        }

        html[data-theme="dark"] .comm-toolbar .form-control,
        html[data-theme="dark"] .comm-toolbar .form-select{
            background: rgba(255,255,255,.06);
            color: #e5eef8;
            border-color: rgba(255,255,255,.08);
        }

        html[data-theme="dark"] .comm-toolbar .form-control::placeholder{
            color:#9fb0c8;
        }

        .comm-toolbar-actions{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
            margin-top:12px;
        }

        .comm-card{
            height:100%;
            display:flex;
            flex-direction:column;
            padding:2px;
        }

        .comm-card-head{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:12px;
            margin-bottom:12px;
        }

        .comm-meta{
            font-size:12px;
            color: var(--comm-muted) !important;
            font-weight:800;
            letter-spacing:.08em;
            text-transform:uppercase;
        }

        .comm-card-title{
            margin:6px 0 0;
            font-size:1.08rem;
            font-weight:900;
            color: var(--comm-text) !important;
            line-height:1.35;
        }

        .comm-card p{
            color: var(--comm-muted) !important;
            line-height:1.7;
            flex-grow:1;
        }

        .comm-badges{
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            margin:0 0 12px;
        }

        .comm-badges .badge{
            border-radius:999px;
            padding:.62em .85em;
            font-weight:700;
        }

        html[data-theme="dark"] .comm-badges .text-bg-light{
            background: rgba(255,255,255,.08) !important;
            color:#e5eef8 !important;
            border-color:rgba(255,255,255,.08) !important;
        }

        .comm-footer-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:10px;
            margin-top:auto;
            padding-top:14px;
            border-top:1px solid var(--comm-soft-line);
        }

        .comm-readtime{
            font-size:12px;
            font-weight:700;
            color: var(--comm-muted) !important;
        }

        .comm-empty{
            text-align:center;
            padding:40px 22px;
        }

        .comm-empty-ico{
            font-size:34px;
            margin-bottom:10px;
        }

        .comm-page-btn[disabled]{
            pointer-events:none;
            opacity:.55;
        }

        .modal-meta-badges{
            display:flex;
            gap:8px;
            flex-wrap:wrap;
            margin-top:8px;
        }

        .modal-meta-badges .badge{
            font-size:.75rem;
            padding:.55em .8em;
        }

        .modal-content{
            border-radius:24px;
            border:1px solid var(--comm-border);
            overflow:hidden;
        }

        html[data-theme="dark"] .modal-content{
            background: linear-gradient(180deg, #0f172a, #111c31);
            color:#e5eef8;
        }

        html[data-theme="dark"] .modal-header,
        html[data-theme="dark"] .modal-footer{
            border-color: rgba(255,255,255,.08);
        }

        html[data-theme="dark"] .modal .text-muted{
            color:#94a3b8 !important;
        }

        html[data-theme="dark"] .btn-close{
            filter: invert(1) grayscale(100%);
        }

        @media (max-width: 991.98px){
            .comm-shortcuts{
                grid-template-columns: 1fr;
            }

            .comm-highlight{
                grid-template-columns: 1fr;
            }

            .comm-stats{
                grid-template-columns: repeat(2, minmax(0,1fr));
            }
        }

        @media (max-width: 575.98px){
            .comm-stats{
                grid-template-columns: 1fr;
            }

            .portal-hero-banner{
                height: 340px !important;
            }

            .portal-legal{
                padding-top:24px;
            }

            .portal-legal-title{
                font-size:1.8rem;
            }

            .comm-footer-row{
                flex-direction:column;
                align-items:stretch;
            }

            .comm-footer-row .btn{
                width:100%;
            }
        }
    </style>
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

                <nav class="portal-links d-none d-lg-flex" aria-label="Navegação principal do portal">
                    <a href="{{ url('/') }}" class="portal-toplink {{ request()->routeIs('portal') ? 'is-active' : '' }}">Página inicial</a>
                    <a href="{{ route('governo') }}" class="portal-toplink {{ request()->routeIs('governo') ? 'is-active' : '' }}">Governo da Cidade</a>
                    <a href="{{ route('legislacao') }}" class="portal-toplink {{ request()->routeIs('legislacao') ? 'is-active' : '' }}">Legislação</a>
                    <a href="{{ route('recrutamento') }}" class="portal-toplink {{ request()->routeIs('recrutamento') ? 'is-active' : '' }}">Recrutamento</a>

                    <a
                        href="{{ route('comunicados') }}"
                        class="portal-toplink {{ request()->routeIs('comunicados') ? 'is-active' : '' }}"
                        @if(request()->routeIs('comunicados'))
                            aria-current="page"
                            tabindex="-1"
                            style="pointer-events:none; opacity:.65;"
                        @endif
                    >
                        Comunicados oficiais
                    </a>
                </nav>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button class="portal-iconbtn portal-iconbtn--gov" type="button" title="Alternar tema" id="toggleTheme" aria-label="Alternar tema">
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
                <button class="portal-menu portal-menu--gov" type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#menuPublico"
                        aria-label="Abrir menu">☰</button>

                <div class="portal-org-title portal-org-title--gov">
                    Vídeo Explicativo do Sistema • GRR
                </div>
            </div>
        </div>

    </div>
</header>

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

            <a class="portal-simple-link" href="{{ route('comunicados') }}" aria-current="page">
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
            Dica: utilize os filtros da central para localizar comunicados mais rapidamente.
        </div>

    </div>
</div>

<main class="portal-main">
    <section class="portal-legal" id="lista">
        <div class="portal-container">

            <div class="portal-legal-head">
                <div class="portal-legal-alert">
                    <div class="portal-legal-alert-text" style="width: 100%; aspect-ratio: 16 / 9;">
                        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/l6C81u-8XmE?si=wnViwGh5PJJAuEcz" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="portal-footer" id="contato">
        <div class="portal-container">
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <div>© {{ date('Y') }} GRR — Grupo de Resposta Rápida</div>
                <div class="portal-muted">FIVEM.BC • Portal Institucional • Comunicados Oficiais</div>
            </div>
        </div>
    </footer>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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