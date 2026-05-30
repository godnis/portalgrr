<!DOCTYPE html>
<html lang="pt-br" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRR • Governo da Cidade</title>
    <meta name="description" content="Portal institucional do Governo da Cidade de Brasil Capital. Serviços, regras oficiais, contatos e diretrizes públicas do ambiente RP.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root{
            --gov-primary: #0b2a4a;
            --gov-primary-2: #123b64;
            --gov-accent: #caa65a;
            --gov-soft: #eef4fa;
            --gov-border: #d9e5f1;
            --gov-text: #1e293b;
            --gov-muted: #64748b;
            --gov-success: #0f766e;
            --gov-shadow: 0 14px 34px rgba(15, 23, 42, .08);
            --gov-radius-xl: 22px;
            --gov-radius-lg: 18px;
            --gov-radius-md: 14px;
        }

        [data-theme="dark"]{
            --gov-primary: #dbeafe;
            --gov-primary-2: #bfdbfe;
            --gov-accent: #e6c16e;
            --gov-soft: rgba(255,255,255,.03);
            --gov-border: rgba(255,255,255,.08);
            --gov-text: #e5edf7;
            --gov-muted: #9fb0c6;
            --gov-success: #34d399;
            --gov-shadow: 0 14px 34px rgba(0, 0, 0, .25);
        }

        .gov-page{
            color: var(--gov-text);
        }

        .gov-section{
            margin-bottom: 18px;
        }

        .gov-shell-card{
            background: linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.92));
            border: 1px solid var(--gov-border);
            border-radius: var(--gov-radius-xl);
            box-shadow: var(--gov-shadow);
        }

        [data-theme="dark"] .gov-shell-card{
            background: linear-gradient(180deg, rgba(16,23,34,.88), rgba(11,18,32,.82));
        }

        .gov-hero-wrap{
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            min-height: 380px;
            background:
                linear-gradient(110deg, rgba(5,18,36,.88) 0%, rgba(9,33,58,.72) 48%, rgba(9,33,58,.35) 100%),
                var(--portal-hero-image) center/cover no-repeat;
            box-shadow: 0 22px 50px rgba(15, 23, 42, .16);
        }

        .gov-hero-wrap::before{
            content: "";
            position: absolute;
            inset: auto -80px -80px auto;
            width: 260px;
            height: 260px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(202,166,90,.18), transparent 70%);
            pointer-events: none;
        }

        .gov-hero-content{
            position: relative;
            z-index: 2;
            padding: 42px;
            min-height: 380px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #fff;
        }

        .gov-kicker{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.16);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            width: fit-content;
            margin-bottom: 14px;
            backdrop-filter: blur(8px);
        }

        .gov-hero-title{
            font-size: clamp(2rem, 4vw, 3.35rem);
            line-height: 1.05;
            font-weight: 900;
            margin-bottom: 14px;
            max-width: 720px;
        }

        .gov-hero-text{
            max-width: 680px;
            font-size: 1.02rem;
            line-height: 1.7;
            color: rgba(255,255,255,.92);
            margin-bottom: 24px;
        }

        .gov-hero-actions{
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .gov-btn-main,
        .gov-btn-soft{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 46px;
            border-radius: 14px;
            padding: 0 18px;
            font-weight: 800;
            text-decoration: none;
            transition: .2s ease;
        }

        .gov-btn-main{
            background: #fff;
            color: #0b2a4a;
            border: 1px solid rgba(255,255,255,.18);
        }

        .gov-btn-main:hover{
            transform: translateY(-1px);
            color: #0b2a4a;
        }

        .gov-btn-soft{
            color: #fff;
            border: 1px solid rgba(255,255,255,.18);
            background: rgba(255,255,255,.08);
            backdrop-filter: blur(8px);
        }

        .gov-btn-soft:hover{
            color: #fff;
            transform: translateY(-1px);
        }

        .gov-hero-stats{
            margin-top: 26px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            max-width: 760px;
        }

        .gov-stat{
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 16px;
            padding: 14px 16px;
            backdrop-filter: blur(8px);
        }

        .gov-stat strong{
            display: block;
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .gov-stat span{
            font-size: .9rem;
            color: rgba(255,255,255,.84);
        }

        .gov-anchorbar{
            margin-top: 16px;
            padding: 14px;
            border-radius: 18px;
            border: 1px solid var(--gov-border);
            background: rgba(255,255,255,.85);
            box-shadow: var(--gov-shadow);
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        [data-theme="dark"] .gov-anchorbar{
            background: rgba(10,15,25,.78);
        }

        .gov-anchor{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--gov-primary);
            background: var(--gov-soft);
            border: 1px solid var(--gov-border);
            border-radius: 999px;
            padding: 10px 14px;
            font-weight: 700;
            transition: .2s ease;
        }

        .gov-anchor:hover{
            transform: translateY(-1px);
            color: var(--gov-primary);
            border-color: rgba(202,166,90,.55);
        }

        .gov-head{
            margin-bottom: 16px;
        }

        .gov-head-top{
            display: flex;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            align-items: end;
            margin-bottom: 12px;
        }

        .gov-eyebrow{
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--gov-accent);
            margin-bottom: 6px;
        }

        .gov-title{
            font-size: clamp(1.5rem, 2.2vw, 2rem);
            font-weight: 900;
            color: var(--gov-primary);
            margin: 0;
        }

        .gov-subtitle{
            color: var(--gov-muted);
            margin: 6px 0 0;
            max-width: 720px;
        }

        .gov-alert{
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid var(--gov-border);
            background: linear-gradient(180deg, rgba(238,244,250,.85), rgba(238,244,250,.5));
        }

        [data-theme="dark"] .gov-alert{
            background: rgba(255,255,255,.03);
        }

        .gov-alert-ico{
            flex: 0 0 auto;
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: rgba(202,166,90,.16);
            font-size: 18px;
        }

        .gov-grid-card{
            height: 100%;
            border-radius: 18px;
            border: 1px solid var(--gov-border);
            background: linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.88));
            box-shadow: var(--gov-shadow);
            padding: 20px;
            transition: .2s ease;
        }

        .gov-grid-card:hover{
            transform: translateY(-3px);
        }

        [data-theme="dark"] .gov-grid-card{
            background: linear-gradient(180deg, rgba(16,23,34,.86), rgba(11,18,32,.80));
        }

        .gov-card-icon{
            width: 50px;
            height: 50px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: rgba(11,42,74,.08);
            border: 1px solid var(--gov-border);
            font-size: 24px;
            margin-bottom: 14px;
        }

        .gov-grid-card h2{
            font-size: 1.05rem;
            font-weight: 900;
            color: var(--gov-primary);
            margin-bottom: 8px;
        }

        .gov-grid-card p{
            color: var(--gov-muted);
            margin-bottom: 12px;
        }

        .gov-grid-card ul{
            padding-left: 18px;
            margin: 0;
        }

        .gov-grid-card ul li{
            margin-bottom: 8px;
        }

        .rules-toolbar{
            padding: 18px;
            border-radius: 18px;
            border: 1px solid var(--gov-border);
            background: linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.86));
            box-shadow: var(--gov-shadow);
        }

        [data-theme="dark"] .rules-toolbar{
            background: linear-gradient(180deg, rgba(16,23,34,.86), rgba(11,18,32,.80));
        }

        .rules-toolbar__top{
            display:grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 14px;
            align-items: start;
        }

        .rules-field{
            display:flex;
            flex-direction:column;
            height:100%;
        }

        .rules-field--filter{
            padding-top: 2px;
        }

        .rules-toolbar__bottom{
            margin-top: 12px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .rules-label{
            font-weight: 900;
            color: var(--gov-primary);
            margin-bottom: 7px;
            display:block;
            line-height: 1.1;
        }

        .rules-hint{
            display:block;
            margin-top: 7px;
            color: var(--gov-muted);
            font-size: 12px;
            line-height: 1.4;
        }

        .rules-toolbar .form-control,
        .rules-toolbar .form-select{
            border-radius: 12px;
            min-height: 46px;
            border-color: var(--gov-border);
            box-shadow: none;
        }

        .rules-toolbar .form-control:focus,
        .rules-toolbar .form-select:focus{
            border-color: rgba(202,166,90,.75);
            box-shadow: 0 0 0 .2rem rgba(202,166,90,.12);
        }

        .rules-toolbar .input-group-text{
            border-radius: 12px 0 0 12px;
            font-weight: 800;
            min-height: 46px;
            display:flex;
            align-items:center;
            border-color: var(--gov-border);
            background: var(--gov-soft);
        }

        .rules-toolbar .btn{
            border-radius: 0 12px 12px 0;
            min-height: 46px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight: 700;
        }

        .gov-rule-card{
            border-radius: 18px;
            border: 1px solid var(--gov-border);
            background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.90));
            box-shadow: var(--gov-shadow);
            padding: 22px;
            height: 100%;
        }

        [data-theme="dark"] .gov-rule-card{
            background: linear-gradient(180deg, rgba(16,23,34,.88), rgba(11,18,32,.82));
        }

        .gov-rule-head{
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .gov-rule-badge{
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: rgba(11,42,74,.08);
            border: 1px solid var(--gov-border);
            font-size: 20px;
            flex: 0 0 auto;
        }

        .gov-rule-head h3{
            margin: 0;
            font-size: 1.15rem;
            font-weight: 900;
            color: var(--gov-primary);
        }

        .gov-rule-card aside{
            margin: 0;
            padding: 14px 16px;
            border-radius: 14px;
            background: var(--gov-soft);
            border: 1px solid var(--gov-border);
            color: var(--gov-text);
        }

        .gov-rule-card ol{
            margin-top: 16px;
            padding-left: 18px;
        }

        .gov-rule-card ol li{
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .gov-rule-card ul{
            margin-top: 8px;
        }

        .gov-contact-card{
            height: 100%;
            padding: 22px;
            border-radius: 18px;
            border: 1px solid var(--gov-border);
            background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.90));
            box-shadow: var(--gov-shadow);
        }

        [data-theme="dark"] .gov-contact-card{
            background: linear-gradient(180deg, rgba(16,23,34,.88), rgba(11,18,32,.82));
        }

        .gov-contact-card h2{
            font-size: 1.08rem;
            font-weight: 900;
            color: var(--gov-primary);
            margin-bottom: 8px;
        }

        .gov-contact-card .btn{
            min-height: 44px;
            border-radius: 12px;
            font-weight: 700;
        }

        .gov-footer-note{
            margin-top: 8px;
            color: var(--gov-muted);
            font-size: .95rem;
        }

        @media (max-width: 991px){
            .rules-toolbar__top{
                grid-template-columns: 1fr;
            }

            .rules-field--filter{
                padding-top: 0;
            }

            .gov-hero-content{
                padding: 28px;
                min-height: 340px;
            }

            .gov-hero-stats{
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px){
            .gov-anchorbar{
                padding: 12px;
            }

            .gov-hero-title{
                font-size: 2rem;
            }

            .gov-hero-text{
                font-size: .95rem;
            }

            .gov-head-top{
                align-items: start;
            }
        }
    </style>
</head>

<body class="portal-body gov-page">

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
                    <a href="{{ route('comunicados') }}" class="portal-toplink">Comunicados oficiais</a>
                    <a href="{{ route('legislacao') }}" class="portal-toplink">Legislação</a>
                    <a href="{{ route('recrutamento') }}" class="portal-toplink">Recrutamento</a>
                    <a
                        href="{{ route('governo') }}"
                        class="portal-toplink {{ request()->routeIs('governo') ? 'is-active' : '' }}"
                        @if(request()->routeIs('governo'))
                            aria-current="page"
                            tabindex="-1"
                            style="pointer-events:none; opacity:.65;"
                        @endif
                    >
                        Governo da Cidade
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
                    Governo da Cidade • Brasil Capital
                </div>
            </div>
        </div>

        <div class="portal-services-bar portal-services-bar--gov">
            <div class="portal-services-row portal-services-row--gov">
                <a href="{{ url('/') }}" class="portal-pill portal-pill--gov text-decoration-none">
                    <span class="portal-pill-ico">🏠</span>
                    Página inicial
                    <span class="portal-pill-caret">▾</span>
                </a>

                <a href="#servicos" class="portal-pill portal-pill--gov text-decoration-none">
                    <span class="portal-pill-ico">🏛️</span>
                    Serviços & Secretarias
                    <span class="portal-pill-caret">▾</span>
                </a>

                <a href="#regras" class="portal-pill portal-pill--gov text-decoration-none">
                    <span class="portal-pill-ico">📖</span>
                    Regras da Cidade
                    <span class="portal-pill-caret">▾</span>
                </a>

                <a href="#contato" class="portal-pill portal-pill--gov text-decoration-none">
                    <span class="portal-pill-ico">☎️</span>
                    Contatos
                    <span class="portal-pill-caret">▾</span>
                </a>
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

    {{-- HERO --}}
    <section class="portal-hero gov-section">
        <div class="portal-container">

            <div class="gov-hero-wrap"
                 style="--portal-hero-image: url('{{ asset('images/imgs4.png') }}');">
                <div class="gov-hero-content">
                    <span class="gov-kicker">🏛️ Institucional • Brasil Capital</span>

                    <h1 class="gov-hero-title">
                        Governo da Cidade
                    </h1>

                    <p class="gov-hero-text">
                        Diretrizes públicas, serviços essenciais, regras oficiais e canais administrativos do ambiente RP.
                        Um espaço centralizado para orientar jogadores, manter organização e fortalecer a imersão da cidade.
                    </p>

                    <div class="gov-hero-actions">
                        <a href="#servicos" class="gov-btn-main">Ver serviços</a>
                        <a href="#regras" class="gov-btn-soft">Ler regras oficiais</a>
                    </div>

                    <div class="gov-hero-stats">
                        <div class="gov-stat">
                            <strong>Serviços públicos</strong>
                            <span>Painel rápido com áreas essenciais da cidade</span>
                        </div>
                        <div class="gov-stat">
                            <strong>Normas oficiais</strong>
                            <span>Regras públicas centralizadas em um único local</span>
                        </div>
                        <div class="gov-stat">
                            <strong>Canais institucionais</strong>
                            <span>Suporte, comunicados e legislação do portal</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="gov-anchorbar">
                <a href="#servicos" class="gov-anchor">🏛️ Serviços</a>
                <a href="#regras" class="gov-anchor">📖 Regras</a>
                <a href="#contato" class="gov-anchor">☎️ Contatos</a>
                <a href="{{ route('legislacao') }}" class="gov-anchor">📚 Legislação</a>
                <a href="{{ route('comunicados') }}" class="gov-anchor">📢 Comunicados</a>
            </div>

            <div class="portal-dot"></div>
        </div>
    </section>

    {{-- SERVIÇOS --}}
    <section class="portal-legal gov-section" id="servicos">
        <div class="portal-container">

            <div class="gov-head">
                <div class="gov-head-top">
                    <div>
                        <div class="gov-eyebrow">Estrutura pública</div>
                        <h2 class="gov-title">Serviços e Secretarias</h2>
                        <p class="gov-subtitle">
                            Um painel institucional com orientações rápidas sobre áreas essenciais de atendimento, organização e suporte da cidade.
                        </p>
                    </div>
                </div>

                <div class="gov-alert">
                    <div class="gov-alert-ico">ℹ️</div>
                    <div>
                        Aqui você encontra um <b>painel resumido e objetivo</b> sobre o funcionamento de setores públicos e protocolos básicos do Brasil Capital.
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <div class="gov-grid-card">
                        <div class="gov-card-icon">🏥</div>
                        <h2>Saúde (HP)</h2>
                        <p>Fluxo de atendimento hospitalar, prioridade clínica e conduta correta em situações emergenciais.</p>
                        <ul>
                            <li>Informe o ocorrido com clareza no atendimento</li>
                            <li>Evite interferir em procedimentos em andamento</li>
                            <li>Não force atendimento fora do RP</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="gov-grid-card">
                        <div class="gov-card-icon">🚗</div>
                        <h2>Trânsito e Mobilidade</h2>
                        <p>Diretrizes de circulação urbana, respeito às vias e organização do ambiente para manter o RP coerente.</p>
                        <ul>
                            <li>Respeite sinalizações e rotas de eventos</li>
                            <li>Evite manobras anti-RP em áreas centrais</li>
                            <li>Operações especiais podem ocorrer com aviso prévio</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="gov-grid-card">
                        <div class="gov-card-icon">🧰</div>
                        <h2>Serviços Mecânicos</h2>
                        <p>Regras de chamado, atendimento e segurança para evitar abusos e situações que prejudiquem a dinâmica do servidor.</p>
                        <ul>
                            <li>Chamados devem ser legítimos</li>
                            <li>Proibido usar chamados para roubo ou emboscada</li>
                            <li>Respeite a ordem e o RP do serviço</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="gov-grid-card">
                        <div class="gov-card-icon">📰</div>
                        <h2>Comunicação e Imprensa</h2>
                        <p>Boas práticas para clips, matérias, coberturas e exposição pública dentro da proposta RP do servidor.</p>
                        <ul>
                            <li>Todo conteúdo deve respeitar o RP</li>
                            <li>Sem ataques ou exposição indevida de players</li>
                            <li>Uso responsável de voz, imagem e contexto</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="gov-grid-card">
                        <div class="gov-card-icon">🧾</div>
                        <h2>Economia e Propriedades</h2>
                        <p>Critérios de manutenção de residências e estabelecimentos para equilíbrio econômico e rotatividade saudável da cidade.</p>
                        <ul>
                            <li>Residências exigem taxa semanal</li>
                            <li>Estabelecimentos precisam de operação mínima</li>
                            <li>Inatividade pode gerar perda de posse</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="gov-grid-card">
                        <div class="gov-card-icon">🛡️</div>
                        <h2>Suporte e Denúncias</h2>
                        <p>Canais oficiais para bugs, denúncias e solicitações administrativas de forma organizada e segura.</p>
                        <ul>
                            <li>Reporte bugs imediatamente</li>
                            <li>Evite exposição em canais públicos</li>
                            <li>Envie provas sempre que possível</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- REGRAS --}}
    <section class="portal-legal gov-section" id="regras">
        <div class="portal-container">

            <div class="gov-head">
                <div class="gov-head-top">
                    <div>
                        <div class="gov-eyebrow">Diretrizes públicas</div>
                        <h2 class="gov-title">Regras Oficiais do Brasil Capital</h2>
                        <p class="gov-subtitle">
                            Consulte as normas essenciais da cidade. O descumprimento pode resultar em advertências, suspensões ou banimento.
                        </p>
                    </div>
                </div>

                <div class="gov-alert">
                    <div class="gov-alert-ico">✅</div>
                    <div>
                        Leia com atenção e utilize a busca abaixo para encontrar rapidamente temas específicos como <b>VDM</b>, <b>RDM</b>, <b>Discord</b>, <b>PD</b> e <b>propriedades</b>.
                    </div>
                </div>
            </div>

            <div class="rules-toolbar" style="margin-bottom: 14px;">
                <div class="rules-toolbar__top">
                    <div class="rules-field rules-field--search">
                        <label class="rules-label" for="rulesSearch">Buscar nas regras</label>

                        <div class="input-group">
                            <span class="input-group-text">🔎</span>
                            <input
                                id="rulesSearch"
                                class="form-control"
                                type="text"
                                placeholder="Ex.: metagaming, comércio, corrupção, discord, PD..."
                                autocomplete="off"
                            />
                            <button class="btn btn-outline-secondary" type="button" id="rulesClear">
                                Limpar
                            </button>
                        </div>

                        <small class="rules-hint">
                            A busca localiza termos dentro dos blocos e ajuda a navegar rapidamente pelas regras da cidade.
                        </small>
                    </div>

                    <div class="rules-field rules-field--filter">
                        <label class="rules-label" for="rulesFilter">Filtrar seção</label>
                        <select id="rulesFilter" class="form-select">
                            <option value="all">Todas</option>
                            <option value="aceite">Aceite & Direitos</option>
                            <option value="gerais">Regras Gerais</option>
                            <option value="personagens">Regras de Personagens</option>
                            <option value="pd">PD do Personagem</option>
                            <option value="apps">Regras de Aplicativos</option>
                            <option value="propriedades">Manutenção de Casas/Lojas/Postos</option>
                            <option value="discord">Regras do Discord</option>
                        </select>
                    </div>
                </div>

                <div class="rules-toolbar__bottom">
                    <small class="portal-muted">
                        Exibindo <b id="rulesCount">0</b> de <b id="rulesTotal">0</b> blocos
                    </small>
                </div>
            </div>

            <div class="row g-3" id="rulesGrid">

                <div class="col-12 rule-item" data-sec="aceite" data-text="conectar concorda regras concede direito voz imagem vídeos clipes redes sociais campanhas servidor legislações país 18 anos">
                    <div class="gov-rule-card">
                        <div class="gov-rule-head">
                            <div class="gov-rule-badge">⚠️</div>
                            <h3>Aceite e Direitos de Uso</h3>
                        </div>

                        <aside>
                            Ao conectar no Brasil Capital você <b>concorda</b> com as regras deste documento e <b>concede</b> o direito de uso da sua voz e/ou imagem em vídeos, clipes e materiais voltados às redes sociais e campanhas do servidor. Todo jogador responde pelas próprias ações, sem transferência de responsabilidade ao servidor por condutas que violem legislações vigentes. Declara-se também ciência de que a classificação do jogo é para <b>maiores de 18 anos</b>.
                        </aside>
                    </div>
                </div>

                <div class="col-12 rule-item" data-sec="gerais" data-text="regras gerais discriminação preconceito racismo homofobia flaming metagaming rdm vdm combat logging power rp anti rp bug assalto veículos 20h 06h corrupção empregos legais ifood uber máscara reconhecimento por voz nocauteado esquece mods gráficos som windows discord alianças suicídio música golpe staff zonas vermelhas habitação popular empréstimo roubo viaturas médico mecânico bombeiro reembolso id morro roupas fac">
                    <div class="gov-rule-card">
                        <div class="gov-rule-head">
                            <div class="gov-rule-badge">📖</div>
                            <h3>Regras Gerais</h3>
                        </div>

                        <ol>
                            <li>A discriminação é expressamente <b>proibida</b>, incluindo preconceito, abuso, racismo, homofobia, discurso de ódio e qualquer tipo de flaming.</li>
                            <li>Todos os itens do servidor são de propriedade exclusiva da cidade; aquisições <b>não concedem</b> direito de transferência entre players. Em caso de inatividade, saída ou banimento, os bens podem retornar ao servidor sem reembolso.</li>
                            <li>Qualquer comércio, venda ou troca de contas, itens, moeda do jogo ou transações feitas fora do jogo é <b>estritamente proibido</b>.</li>
                            <li>Não é permitido sair do roleplay sob nenhuma circunstância, inclusive usando termos como “prefeitura”, “bíblia”, “regras”, “deuses”, nomes de staff e afins.</li>
                            <li>É proibido Metagaming, RDM, VDM, Combat Logging, Power-RP, falta de amor à vida, Anti-RP, uso de aplicativos externos para vantagem e abuso de bugs.</li>
                            <li>Se descobrir algum bug e não avisar a administração, poderá haver banimento permanente. Em caso de dúvida, reporte imediatamente.</li>
                            <li>O bom senso é a base do roleplay. Antes de agir, pense se aquilo faria sentido também na vida real.</li>
                            <li>O único assalto permitido a jogadores é o de <b>veículos</b>, com disparo permitido apenas se houver fuga <b>após</b> a voz de assalto.</li>
                            <li><b>8.1.</b> Assaltos para roubo de veículo só são permitidos entre <b>20h00 e 06h00</b> (horário do jogo). Furto e sequestro para ações podem ocorrer em qualquer horário.</li>
                            <li><b>8.2.</b> É proibido assaltar veículos oficiais de trabalhadores dos empregos legais.</li>
                            <li>É proibido qualquer tipo de corrupção em empregos legais como Polícia, Saúde, Bombeiros, Mecânica e Jornalismo.</li>
                            <li>Enquanto estiver em emprego legal, é proibido realizar atividades ilegais.</li>
                            <li>É proibido chamar serviços com a intenção de sequestrar, roubar ou emboscar pessoas em serviço.</li>
                            <li>Com máscara, o reconhecimento por voz pode ocorrer. Para não ser reconhecido, o personagem deve estar alterado visualmente e com distorção de voz, sempre respeitando o bom senso.</li>
                            <li>Após ser nocauteado propositalmente por outra pessoa, você esquece automaticamente do ocorrido, mesmo que seja reanimado ou informado por terceiros.</li>
                            <li>É proibido usar modificações visuais ou sonoras que gerem vantagem em relação à configuração padrão.</li>
                            <li>É <b>terminantemente proibido</b> qualquer tipo de aliança entre líderes ou membros de organizações legais ou ilegais.</li>
                            <li>É extremamente proibido qualquer roleplay que remeta a suicídio.</li>
                            <li>É proibido uso de música fora do sistema sonoro oficial da cidade.</li>
                            <li>É proibido aplicar qualquer roleplay de golpe na cidade.</li>
                            <li>Para trabalhar no <b>iFood</b>, é <b>obrigatório</b> o uso de veículo de duas rodas.</li>
                            <li>Para trabalhar no <b>Uber</b>, é <b>obrigatório</b> o uso de veículo de quatro rodas.</li>
                            <li>Jogadores que ocupem cargos administrativos em outros servidores não terão whitelist liberada.</li>
                            <li>Nas zonas vermelhas, o RDM só é permitido no horário de assalto, entre <b>20h00 e 06h00</b>.</li>
                            <li>É <b>terminantemente proibido</b> fugir para dentro da Habitação Popular por configurar powergaming.</li>
                            <li>É <b>proibido</b> roleplay de empréstimo de dinheiro entre players.</li>
                            <li>É <b>proibido</b> o roubo de viaturas em geral.</li>
                            <li>É <b>proibido</b> matar médicos, mecânicos e bombeiros em serviço.</li>
                            <li>O servidor <b>não reembolsa</b> valores enviados ao ID errado.</li>
                            <li>É <b>proibido</b> subir morro com qualquer tipo de veículo; ações no morro devem ser feitas a pé.</li>
                            <li>É <b>proibido</b> usar palavras ou situações inadequadas que possam derrubar lives de players.</li>
                            <li>É <b>proibida</b> a depredação de veículos em geral.</li>
                            <li>É <b>proibido</b> o uso de roupas personalizadas de FACs por quem não pertence à organização.</li>
                        </ol>
                    </div>
                </div>

                <div class="col-12 rule-item" data-sec="personagens" data-text="regras de personagens nomes marcas duplo sentido conotação sexual preconceito personagens extras vínculo advertências suspensões compartilhadas personagem rival facção setado policial fac organização personagem secundário rp lado oposto sem setagem">
                    <div class="gov-rule-card">
                        <div class="gov-rule-head">
                            <div class="gov-rule-badge">🧑🏻</div>
                            <h3>Regras de Personagens</h3>
                        </div>

                        <ol>
                            <li>É proibido usar nome ou sobrenome com marcas, duplo sentido, conotação sexual, preconceito ou elementos fora da realidade.</li>
                            <li>Personagens extras não podem ter vínculo com os demais personagens do mesmo jogador.</li>
                            <li>Advertências e suspensões são compartilhadas entre todos os personagens da conta.</li>
                            <li>É proibido manter personagem secundário em lado rival estando setado com o personagem principal.</li>
                            <li>RP do lado oposto pode ser feito apenas sem setagem em facção ou organização.</li>
                        </ol>
                    </div>
                </div>

                <div class="col-12 rule-item" data-sec="pd" data-text="pd facção pd personagem morte permanente falta de amor à vida quebra grave regra banimento permanente administração advertência esquecer fac pessoas">
                    <div class="gov-rule-card">
                        <div class="gov-rule-head">
                            <div class="gov-rule-badge">☠️</div>
                            <h3>PD do Personagem (Morte Permanente)</h3>
                        </div>

                        <p>Existem dois tipos de PDs:</p>
                        <ol>
                            <li><b>PD Facção:</b> quando alguém da facção em que você está o mata para retirada da facção; é obrigatório esquecer tudo relacionado à facção e às pessoas dela.</li>
                            <li><b>PD Personagem:</b> quando há uma situação gravíssima que leva à perda definitiva do personagem.</li>
                        </ol>

                        <p><b>Motivos que podem levar a PD pela administração:</b></p>
                        <p><b>Falta de amor à vida.</b></p>
                        <p>Caso você cometa antiamor à vida e sobreviva, poderá não receber PD imediato. Porém, se morrer na situação, poderá receber advertência e, se necessário, PD mediante autorização administrativa.</p>
                        <p><b>Quebra grave de regra da cidade.</b></p>
                        <p>Em determinadas situações, a administração pode avaliar a aplicação de PD em vez de banimento permanente.</p>
                    </div>
                </div>

                <div class="col-12 rule-item" data-sec="apps" data-text="regras de aplicativos contas fakes instagram tinder conteúdo externo explícito mensagens excesso travar aplicativos">
                    <div class="gov-rule-card">
                        <div class="gov-rule-head">
                            <div class="gov-rule-badge">📱</div>
                            <h3>Regras de Aplicativos</h3>
                        </div>

                        <ol>
                            <li>É proibida a criação de contas fakes em aplicativos como Instagram, Tinder e similares.</li>
                            <li>São proibidas postagens com pessoas reais, conteúdo externo ao RP ou material explícito.</li>
                            <li>É proibido enviar mensagens em excesso com objetivo de prejudicar ou travar aplicativos.</li>
                        </ol>
                    </div>
                </div>

                <div class="col-12 rule-item" data-sec="propriedades" data-text="regras manutenção casas lojas postos posse imóveis estabelecimentos manutenção ativa pessoal pagamento semanal taxa categoria estoque mínimo 30% 7 dias proprietário inativo 15 dias remover sem aviso reintegradas patrimônio intransferíveis sem reembolso saldo caixa">
                    <div class="gov-rule-card">
                        <div class="gov-rule-head">
                            <div class="gov-rule-badge">🏠</div>
                            <h3>Regras de Manutenção de Casas, Lojas e Postos</h3>
                        </div>

                        <ol>
                            <li>A posse de imóveis e estabelecimentos depende do cumprimento de critérios de <b>manutenção ativa e pessoal</b> pelo proprietário.</li>
                            <li>Residências exigem <b>pagamento semanal da taxa de manutenção</b> com dinheiro do jogo, conforme a categoria.</li>
                            <li>Estabelecimentos devem manter <b>estoque mínimo de 30%</b> em qualquer período de 7 dias consecutivos.</li>
                            <li>A manutenção deve ser feita diretamente pelo <b>player proprietário</b>. Gerência ativa não substitui presença do dono quando houver inatividade prolongada.</li>
                            <li>Propriedades com donos ausentes por mais de 15 dias poderão ser <b>removidas sem aviso prévio</b>.</li>
                            <li>Todas as propriedades são intransferíveis e permanecem como bens do servidor.</li>
                            <li>O descumprimento poderá liberar automaticamente o estabelecimento para outros players, sem reembolso.</li>
                        </ol>
                    </div>
                </div>

                <div class="col-12 rule-item" data-sec="discord" data-text="regras do discord instagram clipes brasil capital rp bate papo brigas provocações flaming proibição divulgação externa outros servidores ataques exposições mutes advertências banimentos">
                    <div class="gov-rule-card">
                        <div class="gov-rule-head">
                            <div class="gov-rule-badge">🧩</div>
                            <h3>Regras do Discord</h3>
                        </div>

                        <p>Para manter o Discord organizado, saudável e seguro para todos, ficam estabelecidas as seguintes regras:</p>

                        <ol>
                            <li><b>Uso dos canais temáticos</b>
                                <ul>
                                    <li><b>#instagram</b> e <b>#clipes</b> são exclusivos para conteúdos do Brasil Capital RP.</li>
                                    <li>Qualquer outro uso indevido poderá ser removido e penalizado.</li>
                                </ul>
                            </li>
                            <li><b>Respeito no #bate-papo</b>
                                <ul>
                                    <li>Discussões, provocações, brigas e flaming não serão tolerados.</li>
                                    <li>Se necessário, utilize os canais adequados de suporte.</li>
                                </ul>
                            </li>
                            <li><b>Proibição de divulgação externa</b>
                                <ul>
                                    <li>É proibido divulgar conteúdos de outros servidores dentro do Discord do BC.</li>
                                    <li>Divulgação externa só poderá ocorrer em espaços oficialmente autorizados.</li>
                                </ul>
                            </li>
                            <li><b>Ataques e exposições</b>
                                <ul>
                                    <li>É proibido postar conteúdo para atacar, ridicularizar ou expor outros players.</li>
                                </ul>
                            </li>
                        </ol>

                        <hr>

                        <p style="margin:0;">
                            A quebra dessas regras pode gerar mutes temporários, restrição permanente de canal, advertências e banimentos.<br>
                            ⚠️ <b>Lembre-se:</b> o Discord também é extensão do roleplay da cidade.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- CONTATOS --}}
    <section class="portal-legal gov-section" id="contato">
        <div class="portal-container">

            <div class="gov-head">
                <div class="gov-head-top">
                    <div>
                        <div class="gov-eyebrow">Canais oficiais</div>
                        <h2 class="gov-title">Contatos Oficiais</h2>
                        <p class="gov-subtitle">
                            Utilize sempre os meios corretos para denúncias, bugs, comunicados e informações normativas.
                        </p>
                    </div>
                </div>

                <div class="gov-alert">
                    <div class="gov-alert-ico">☎️</div>
                    <div>
                        Use os canais oficiais do portal e evite resolver situações administrativas por mensagens privadas fora dos fluxos adequados.
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <div class="gov-contact-card">
                        <h2>🎫 Suporte</h2>
                        <p class="portal-muted">Para denúncias, bugs e solicitações administrativas.</p>

                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge text-bg-primary">Ticket</span>
                            <span class="badge text-bg-light" style="border:1px solid #e6edf5;color:#0B2A4A;">Canais Oficiais</span>
                        </div>

                        <p style="margin-top:12px;margin-bottom:0;">
                            Abra um ticket com prints e/ou vídeos e descreva o problema de forma objetiva.
                            <br><br>
                            🔗 <a href="https://discord.gg/brasilcapital" target="_blank" rel="noopener noreferrer">
                                Acesse o Discord oficial
                            </a>
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="gov-contact-card">
                        <h2>📣 Comunicados</h2>
                        <p class="portal-muted">Atualizações oficiais, avisos públicos e diretrizes operacionais do portal.</p>
                        <a class="btn btn-outline-primary w-100" href="{{ route('comunicados') }}">Ir para Comunicados</a>
                        <div class="gov-footer-note">Consulte esta área para decisões recentes, avisos e notas institucionais.</div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="gov-contact-card">
                        <h2>📜 Legislação</h2>
                        <p class="portal-muted">Regras, documentos, leis internas e conteúdos jurídicos do ambiente RP.</p>
                        <a class="btn btn-outline-primary w-100" href="{{ route('legislacao') }}">Ver Legislação</a>
                        <div class="gov-footer-note">Ideal para consulta normativa e entendimento das bases legais do servidor.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
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

<script>
(function () {
    const q = document.getElementById('rulesSearch');
    const filter = document.getElementById('rulesFilter');
    const clearBtn = document.getElementById('rulesClear');

    const items = Array.from(document.querySelectorAll('.rule-item'));
    const countEl = document.getElementById('rulesCount');
    const totalEl = document.getElementById('rulesTotal');

    function normalize(str) {
        return (str || "")
            .toString()
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");
    }

    function apply() {
        const term = normalize(q?.value || '');
        const sec = filter?.value || 'all';

        let visible = 0;

        items.forEach(el => {
            const text = normalize(el.dataset.text || "");
            const okText = !term || text.includes(term);
            const okSec = (sec === 'all') || (el.dataset.sec === sec);

            const show = okText && okSec;
            el.style.display = show ? '' : 'none';

            if (show) visible++;
        });

        if (totalEl) totalEl.textContent = items.length;
        if (countEl) countEl.textContent = visible;
    }

    function clearFilters() {
        if (q) q.value = '';
        if (filter) filter.value = 'all';
        apply();
        q?.focus();
    }

    q?.addEventListener('input', apply);
    filter?.addEventListener('change', apply);
    clearBtn?.addEventListener('click', clearFilters);

    apply();
})();
</script>

</body>
</html>