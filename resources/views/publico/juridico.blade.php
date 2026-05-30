<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jurídico — GRR • Brasil Capital</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .portal-body{
            --portal-hero-image: url("/images/juridico.png?v=3");
        }

        :root{
            --law-primary:#1351B4;
            --law-primary-2:#0f3f8c;
            --law-border:#dbe5f1;
            --law-text:#0f172a;
            --law-muted:#64748b;
            --law-soft:#f8fbff;
            --law-card:#ffffff;
            --law-card-2:#f8fafc;
            --law-shadow:0 14px 34px rgba(15,23,42,.08);
            --law-shadow-soft:0 8px 20px rgba(15,23,42,.06);
        }

        [data-theme="dark"]{
            --law-border:rgba(148,163,184,.18);
            --law-text:#e5e7eb;
            --law-muted:rgba(226,232,240,.74);
            --law-soft:#0f172a;
            --law-card:#0b1220;
            --law-card-2:#0f172a;
            --law-shadow:0 16px 38px rgba(0,0,0,.34);
            --law-shadow-soft:0 10px 24px rgba(0,0,0,.28);
        }

        html{
            scroll-behavior:smooth;
        }

        body.portal-body{
            color:var(--law-text);
        }

        .portal-header--gov{
            position:relative;
            z-index:20;
        }

        .law-hero-wrap{
            position:relative;
            z-index:1;
            padding-top:18px;
        }

        .law-wrap{
            padding:20px 0 32px;
        }

        .law-head{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:14px;
            flex-wrap:wrap;
            margin-bottom:4px;
        }

        .law-title{
            display:flex;
            gap:12px;
            align-items:center;
            margin:0;
            font-weight:900;
            letter-spacing:-.6px;
            color:var(--law-text);
        }

        .law-title .ico{
            width:46px;
            height:46px;
            border-radius:14px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:linear-gradient(180deg, rgba(19,81,180,.14), rgba(19,81,180,.08));
            overflow:hidden;
            line-height:1;
            font-size:21px;
            flex:0 0 46px;
            border:1px solid rgba(19,81,180,.12);
        }

        .law-title .ico > *{
            display:block;
            line-height:1;
        }

        .law-sub{
            color:var(--law-muted);
            margin:8px 0 0;
            font-size:14px;
        }

        .law-meta{
            display:flex;
            gap:8px;
            flex-wrap:wrap;
            margin-top:14px;
        }

        .law-badge{
            display:inline-flex;
            gap:8px;
            align-items:center;
            border:1px solid var(--law-border);
            background:var(--law-card);
            padding:9px 13px;
            border-radius:999px;
            font-weight:800;
            font-size:12.5px;
            color:var(--law-text);
            box-shadow:var(--law-shadow-soft);
        }

        .law-grid{
            display:grid;
            grid-template-columns:320px minmax(0, 1fr);
            gap:16px;
            margin-top:16px;
            align-items:start;
        }

        @media (max-width: 992px){
            .law-grid{
                grid-template-columns:1fr;
            }
        }

        .law-card{
            border:1px solid var(--law-border);
            background:var(--law-card);
            border-radius:20px;
            box-shadow:var(--law-shadow);
            overflow:hidden;
        }

        .law-card .hd{
            padding:14px 16px;
            border-bottom:1px solid rgba(148,163,184,.14);
            font-weight:900;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            color:var(--law-text);
            background:linear-gradient(180deg, rgba(248,250,252,.95), rgba(255,255,255,1));
        }

        [data-theme="dark"] .law-card .hd{
            background:linear-gradient(180deg, rgba(15,23,42,.76), rgba(11,18,32,1));
        }

        .law-card .bd{
            padding:14px 16px;
        }

        .law-aside{
            position:sticky;
            top:14px;
        }

        @media (max-width: 992px){
            .law-aside{
                position:static;
            }
        }

        .law-search{
            display:flex;
            gap:10px;
            align-items:center;
            border:1px solid var(--law-border);
            background:var(--law-card);
            padding:11px 13px;
            border-radius:14px;
            transition:.2s ease;
        }

        .law-search:focus-within{
            border-color:rgba(19,81,180,.38);
            box-shadow:0 0 0 4px rgba(19,81,180,.08);
        }

        .law-search .ico{
            font-weight:900;
            opacity:.9;
        }

        .law-search input{
            border:0;
            outline:0;
            width:100%;
            background:transparent;
            font-weight:800;
            color:var(--law-text);
            font-size:14px;
        }

        .law-search input::placeholder{
            color:#94a3b8;
        }

        .law-search .btn-clear{
            border:0;
            background:transparent;
            font-weight:900;
            opacity:.75;
            padding:4px 8px;
            border-radius:10px;
            transition:.2s ease;
            color:var(--law-text);
        }

        .law-search .btn-clear:hover{
            opacity:1;
            background:rgba(15,23,42,.06);
        }

        [data-theme="dark"] .law-search .btn-clear:hover{
            background:rgba(148,163,184,.10);
        }

        .law-results{
            margin-top:12px;
            font-size:12.5px;
            font-weight:800;
            color:var(--law-muted);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
        }

        .law-toc{
            margin-top:12px;
            display:flex;
            flex-direction:column;
            gap:8px;
        }

        .law-toc a{
            display:flex;
            gap:10px;
            align-items:center;
            padding:11px 12px;
            border-radius:14px;
            color:var(--law-text);
            text-decoration:none;
            border:1px solid transparent;
            font-weight:850;
            transition:.2s ease;
        }

        .law-toc a:hover{
            border-color:#cfe0ff;
            background:rgba(19,81,180,.06);
            transform:translateX(2px);
        }

        .law-toc a.is-active{
            border-color:rgba(19,81,180,.25);
            background:rgba(19,81,180,.10);
        }

        .law-toc a .dot{
            width:10px;
            height:10px;
            border-radius:999px;
            background:rgba(19,81,180,.30);
            box-shadow:0 8px 16px rgba(19,81,180,.15);
            flex:0 0 10px;
        }

        .law-toc a.is-active .dot{
            background:var(--law-primary);
        }

        [data-theme="dark"] .law-toc a{
            color:#e5e7eb;
        }

        .law-alert{
            display:flex;
            gap:10px;
            align-items:flex-start;
            border:1px solid var(--law-border);
            background:linear-gradient(180deg, #fffaf0, #fff);
            border-radius:16px;
            padding:12px 14px;
        }

        .law-alert .i{
            width:34px;
            height:34px;
            border-radius:10px;
            display:grid;
            place-items:center;
            background:rgba(245,158,11,.16);
            font-weight:900;
            flex:0 0 34px;
        }

        [data-theme="dark"] .law-alert{
            background:linear-gradient(180deg, rgba(245,158,11,.08), rgba(11,18,32,1));
        }

        .law-doc{
            min-width:0;
        }

        .law-document-shell{
            border:1px solid var(--law-border);
            border-radius:18px;
            background:linear-gradient(180deg, rgba(248,250,252,.92), rgba(255,255,255,1));
            padding:18px;
        }

        [data-theme="dark"] .law-document-shell{
            background:linear-gradient(180deg, rgba(15,23,42,.72), rgba(11,18,32,1));
        }

        .law-document-top{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:14px;
            flex-wrap:wrap;
            margin-bottom:16px;
            padding-bottom:14px;
            border-bottom:1px dashed var(--law-border);
        }

        .law-document-kicker{
            font-size:11px;
            letter-spacing:.16em;
            text-transform:uppercase;
            font-weight:900;
            color:var(--law-primary);
            margin-bottom:6px;
        }

        .law-document-title{
            margin:0;
            font-size:24px;
            font-weight:900;
            letter-spacing:-.5px;
            color:var(--law-text);
        }

        .law-document-sub{
            margin:6px 0 0;
            color:var(--law-muted);
            font-size:13.5px;
            max-width:720px;
        }

        .law-document-status{
            display:inline-flex;
            align-items:center;
            gap:8px;
            border:1px solid rgba(16,185,129,.18);
            background:rgba(16,185,129,.10);
            color:#047857;
            padding:10px 12px;
            border-radius:999px;
            font-size:12px;
            font-weight:900;
            white-space:nowrap;
        }

        [data-theme="dark"] .law-document-status{
            color:#86efac;
            background:rgba(16,185,129,.12);
            border-color:rgba(16,185,129,.18);
        }

        .law-section-title{
            margin:0 0 10px;
            font-size:1.02rem;
            font-weight:900;
            color:var(--law-text);
            letter-spacing:-.2px;
        }

        .law-text{
            white-space:pre-wrap;
            word-break:break-word;
            overflow-wrap:anywhere;
            font-size:14px;
            line-height:1.82;
            margin:0;
            color:var(--law-text);
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        }

        .law-note{
            font-size:13px;
            color:var(--law-muted);
            line-height:1.65;
        }

        .accordion{
            border:1px solid var(--law-border);
            border-radius:18px;
            overflow:hidden;
            background:var(--law-card);
        }

        .accordion-item{
            border:0;
            background:transparent;
        }

        .accordion-item + .accordion-item{
            border-top:1px solid rgba(148,163,184,.14);
        }

        .accordion-button{
            font-weight:900;
            font-size:15px;
            padding:16px 18px;
            box-shadow:none !important;
            background:linear-gradient(180deg, rgba(248,250,252,.9), rgba(255,255,255,1));
            color:var(--law-text);
        }

        .accordion-button:not(.collapsed){
            color:var(--law-primary);
            background:rgba(19,81,180,.06);
        }

        [data-theme="dark"] .accordion-button,
        [data-theme="dark"] .accordion-body{
            background:#0f172a;
            color:#e5e7eb;
        }

        [data-theme="dark"] .accordion-button:not(.collapsed){
            background:rgba(99,102,241,.12);
        }

        [data-theme="dark"] .accordion-button::after{
            filter:invert(1);
        }

        .accordion-body{
            padding:18px;
            background:linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.96));
        }

        [data-theme="dark"] .accordion-body{
            background:linear-gradient(180deg, rgba(15,23,42,.82), rgba(11,18,32,1));
        }

        .law-actions{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            margin-top:18px;
            flex-wrap:wrap;
            padding-top:16px;
            border-top:1px dashed var(--law-border);
        }

        .law-back{
            display:inline-flex;
            align-items:center;
            gap:10px;
            padding:11px 18px;
            border-radius:999px;
            background:linear-gradient(180deg, var(--law-primary), var(--law-primary-2));
            color:#fff;
            text-decoration:none;
            font-weight:900;
            box-shadow:0 14px 26px rgba(19,81,180,.20);
            transition:.2s ease;
        }

        .law-back:hover{
            color:#fff;
            transform:translateY(-1px);
            box-shadow:0 18px 30px rgba(19,81,180,.24);
        }

        .law-mini{
            font-size:12.5px;
            font-weight:800;
            color:var(--law-muted);
        }

        .law-hero{
            margin:0 0 20px;
            max-width:none;
        }

        .law-hero-banner{
            border-radius:10px;
            border:1px solid #e6edf5;
            height:360px;
            position:relative;
            overflow:hidden;
            z-index:1;
            background-image:
                radial-gradient(circle at 50% 35%, rgba(255,255,255,.12), transparent 55%),
                linear-gradient(180deg, rgba(0,0,0,.18), rgba(0,0,0,.68)),
                var(--portal-hero-image);
            background-size:cover;
            background-position:center;
            background-repeat:no-repeat;
            box-shadow:0 16px 40px rgba(15,23,42,.12);
        }

        .law-hero-banner::after{
            content:"";
            position:absolute;
            inset:auto 0 0 0;
            height:100px;
            background:linear-gradient(180deg, transparent, rgba(0,0,0,.22));
            pointer-events:none;
        }

        [data-theme="dark"] .law-hero-banner{
            border-color:rgba(148,163,184,.18);
            background-image:
                radial-gradient(circle at 50% 35%, rgba(255,255,255,.05), transparent 55%),
                linear-gradient(180deg, rgba(0,0,0,.28), rgba(0,0,0,.82)),
                var(--portal-hero-image);
        }

        .law-hero-overlay{
            position:absolute;
            inset:0;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            text-align:center;
            color:#fff;
            padding:22px;
        }

        .law-hero-badge{
            display:inline-flex;
            align-items:center;
            gap:10px;
            padding:9px 14px;
            border-radius:999px;
            border:1px solid rgba(255,255,255,.24);
            background:rgba(15,23,42,.30);
            backdrop-filter:blur(8px);
            font-size:12px;
            letter-spacing:.14em;
            text-transform:uppercase;
            opacity:.98;
            margin-bottom:14px;
            font-weight:800;
        }

        .law-hero-h1{
            font-size:48px;
            font-weight:900;
            letter-spacing:.01em;
            margin-bottom:10px;
            text-shadow:0 10px 30px rgba(0,0,0,.35);
        }

        .law-hero-p{
            max-width:880px;
            color:rgba(255,255,255,.94);
            font-size:15px;
            line-height:1.6;
            margin:0;
            text-shadow:0 6px 18px rgba(0,0,0,.22);
        }

        @media (max-width: 992px){
            .law-hero-banner{ height:300px; }
            .law-hero-h1{ font-size:36px; }
        }

        @media (max-width: 576px){
            .law-hero-wrap{ padding-top:14px; }
            .law-wrap{ padding-top:16px; }
            .law-hero-banner{ height:260px; }
            .law-hero-h1{ font-size:29px; }
            .law-hero-p{ font-size:13.5px; }
            .law-document-title{ font-size:20px; }
            .law-document-shell{ padding:14px; }
            .accordion-body{ padding:14px; }
            .law-text{ font-size:13.2px; line-height:1.72; }
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
                            aria-label="Abrir menu">☰</button>

                    <div class="portal-org-title portal-org-title--gov">
                        ORDENAMENTO JURÍDICO • BC
                    </div>
                </div>
            </div>

            <div class="portal-services-bar portal-services-bar--gov">
                <div class="portal-services-row portal-services-row--gov">
                    <a href="{{ url('/') }}" class="portal-pill portal-pill--gov text-decoration-none">
                        <span class="portal-pill-ico">🏠</span>
                        Página inicial
                        <span class="portal-pill-caret">›</span>
                    </a>

                    <span class="portal-pill portal-pill--gov" style="pointer-events:none; opacity:.95;">
                        <span class="portal-pill-ico">⚖️</span>
                        Jurídico
                    </span>
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

    <main class="portal-main">

        {{-- HERO FORA DO HEADER --}}
        <section class="law-hero-wrap">
            <div class="portal-container">
                <section class="law-hero">
                    <div class="law-hero-banner">
                        <div class="law-hero-overlay">
                            <div class="law-hero-badge">⚖️ Ordenamento jurídico • Brasil Capital</div>
                            <h2 class="law-hero-h1">Jurídico</h2>
                            <p class="law-hero-p">
                                Consulte regras, normas e referências de RP com navegação rápida, leitura organizada e busca inteligente em um ambiente institucional mais moderno.
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </section>

        <section class="law-wrap">
            <div class="portal-container">

                <div class="law-head">
                    <div>
                        <h1 class="law-title">
                            <span class="ico">⚖️</span>
                            Jurídico — Ordenamento de Brasil Capital
                        </h1>

                        <p class="law-sub">
                            Regras, normas e diretrizes aplicadas ao RP da cidade em um ambiente de consulta institucional.
                        </p>

                        <div class="law-meta">
                            <span class="law-badge">VERSÃO: 02.2024</span>
                            <span class="law-badge">PUBLICADO: 26/06/2024 • 21:05</span>
                            <span class="law-badge">ATUALIZADO: 05/12/2024 • 20:25</span>
                        </div>
                    </div>
                </div>

                <div class="law-grid">

                    {{-- SIDEBAR --}}
                    <aside class="law-card law-aside">
                        <div class="hd">
                            <span>📌 Navegação</span>
                            <span class="law-mini" id="lawHint">Use a busca ou clique</span>
                        </div>

                        <div class="bd">
                            <div class="law-search">
                                <span class="ico">🔎</span>
                                <input id="lawSearch" type="text" placeholder="Buscar no documento..." autocomplete="off">
                                <button class="btn-clear" type="button" id="lawClear" title="Limpar">✕</button>
                            </div>

                            <div class="law-results">
                                <span id="lawCount">Mostrando tudo</span>
                                <span class="law-mini d-none d-md-inline" id="lawAutoOpen"></span>
                            </div>

                            <div class="law-toc" id="lawToc">
                                <a href="#aviso" data-link="aviso"><span class="dot"></span><span>⚠️</span> Aviso legal</a>
                                <a href="#agradecimentos" data-link="agradecimentos"><span class="dot"></span><span>🙏</span> Agradecimentos</a>
                                <a href="#constituicao" data-link="constituicao"><span class="dot"></span><span>📜</span> Constituição</a>
                                <a href="#decretos" data-link="decretos"><span class="dot"></span><span>🏛️</span> Decretos-Lei</a>
                                <a href="#jurisprudencias" data-link="jurisprudencias"><span class="dot"></span><span>⚖️</span> Jurisprudências</a>
                                <a href="#sumulas" data-link="sumulas"><span class="dot"></span><span>📌</span> Súmulas vinculantes</a>
                            </div>

                            <hr class="my-3">

                            <div class="law-alert">
                                <div class="i">i</div>
                                <div style="font-size:13px;">
                                    <b>Documento fictício.</b> Este conteúdo não possui vínculo com instituições reais e deve ser utilizado exclusivamente no contexto de RP da cidade.
                                </div>
                            </div>
                        </div>
                    </aside>

                    {{-- CONTEÚDO --}}
                    <section class="law-card">
                        <div class="hd">📚 Documento</div>

                        <div class="bd law-doc">
                            <div class="law-document-shell">
                                <div class="law-document-top">
                                    <div>
                                        <div class="law-document-kicker">Base normativa</div>
                                        <h2 class="law-document-title">Ordenamento Jurídico de Brasil Capital</h2>
                                        <p class="law-document-sub">
                                            Abaixo estão reunidas as diretrizes, fundamentos, decretos e entendimentos utilizados como referência institucional dentro do servidor.
                                        </p>
                                    </div>

                                    <div class="law-document-status">
                                        ● Documento em vigor
                                    </div>
                                </div>

                                @include('publico.partials.juridico-texto')

                                <div class="law-actions">
                                    <div class="law-mini">
                                        Dica: pressione <b>Ctrl + F</b> para utilizar também a busca do navegador.
                                    </div>

                                    <a class="law-back" href="{{ url('/') }}">⬅ Voltar ao Portal</a>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </section>

        <footer class="portal-footer" id="contato">
            <div class="portal-container">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <div>© {{ date('Y') }} GRR — Grupo de Resposta Rápida</div>
                    <div class="portal-muted">FIVEM.BC • Portal Institucional</div>
                </div>
            </div>
        </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- TEMA --}}
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

    {{-- TOC --}}
    <script>
        (function(){
            const toc = document.getElementById('lawToc');
            if(!toc) return;

            const links = Array.from(toc.querySelectorAll('a[href^="#"]'));
            const sections = links
                .map(a => document.querySelector(a.getAttribute('href')))
                .filter(Boolean);

            function setActive(id){
                links.forEach(a => a.classList.toggle('is-active', a.getAttribute('href') === `#${id}`));
            }

            const obs = new IntersectionObserver((entries) => {
                const visible = entries
                    .filter(e => e.isIntersecting)
                    .sort((a,b) => b.intersectionRatio - a.intersectionRatio)[0];

                if(visible?.target?.id) setActive(visible.target.id);
            }, {
                rootMargin: "-20% 0px -70% 0px",
                threshold: [0.05, 0.1, 0.2]
            });

            sections.forEach(sec => obs.observe(sec));

            links.forEach(a => {
                a.addEventListener('click', (e) => {
                    const id = a.getAttribute('href')?.replace('#','');
                    const el = document.getElementById(id);
                    if(!el) return;

                    e.preventDefault();
                    el.scrollIntoView({ behavior:'smooth', block:'start' });
                    history.replaceState(null, "", `#${id}`);
                    setActive(id);
                });
            });
        })();
    </script>

    {{-- BUSCA --}}
    <script>
        (function(){
            const input = document.getElementById('lawSearch');
            const clear = document.getElementById('lawClear');
            const count = document.getElementById('lawCount');
            const autoOpen = document.getElementById('lawAutoOpen');

            const blocks = () => Array.from(document.querySelectorAll('[data-law-block]'));

            function normalize(s){
                return (s || '')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g,'');
            }

            function openAccordionIfNeeded(el){
                const item = el.closest('.accordion-item');
                if(!item) return;

                const collapse = item.querySelector('.accordion-collapse');
                if(!collapse) return;

                const bs = bootstrap.Collapse.getOrCreateInstance(collapse, { toggle:false });
                bs.show();
            }

            function applyFilter(){
                const q = normalize((input?.value || '').trim());
                let shown = 0;
                let openedAny = false;

                blocks().forEach(el => {
                    const hay = normalize(el.innerText);
                    const show = !q || hay.includes(q);
                    el.style.display = show ? '' : 'none';

                    if(show){
                        shown++;
                        if(q && !openedAny){
                            openAccordionIfNeeded(el);
                            openedAny = true;
                        }
                    }
                });

                if(!count) return;

                if(!q){
                    count.textContent = 'Mostrando tudo';
                    if(autoOpen) autoOpen.textContent = '';
                    return;
                }

                count.textContent = `${shown} resultado(s)`;
                if(autoOpen) autoOpen.textContent = openedAny ? 'Abrindo seção com resultado…' : '';

                setTimeout(() => {
                    if(autoOpen) autoOpen.textContent = '';
                }, 1000);
            }

            if(input){
                input.addEventListener('input', applyFilter);
            }

            if(clear){
                clear.addEventListener('click', () => {
                    if(!input) return;
                    input.value = '';
                    input.focus();
                    applyFilter();
                });
            }

            applyFilter();
        })();
    </script>

</body>
</html>