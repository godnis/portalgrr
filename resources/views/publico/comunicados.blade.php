<!DOCTYPE html>
<html lang="pt-br" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRR • Comunicados Oficiais</title>
    <meta name="description" content="Central de comunicados oficiais da GRR com avisos institucionais, diretrizes operacionais e informativos do portal.">
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

@php
    $comunicados = [
        [
            'id' => 'COM-001',
            'titulo' => 'Operação Rodoviária — Intensificação de Blitz Urbana',
            'categoria' => 'operacoes',
            'prioridade' => 'alta',
            'data' => '2026-01-14',
            'resumo' => 'Ação estratégica com reforço de abordagem padrão e foco em segurança viária e condutas incompatíveis com RP.',
            'conteudo' => 'A GRR informa que será realizada operação de intensificação de blitz em vias urbanas e rodovias estratégicas. O foco é prevenir acidentes, reduzir direção perigosa e manter a ordem no RP. As equipes deverão priorizar abordagem segura, checagem de documentação e orientações ao cidadão. Em caso de resistência ou tentativa de fuga sem motivação plausível, o procedimento seguirá protocolo institucional, com registro da ocorrência e medidas cabíveis.'
        ],
        [
            'id' => 'COM-002',
            'titulo' => 'Fiscalização de Velocidade em Vias Públicas',
            'categoria' => 'blitz',
            'prioridade' => 'media',
            'data' => '2026-01-13',
            'resumo' => 'Fiscalização preventiva com caráter educativo e operacional para reduzir risco e preservar a experiência RP.',
            'conteudo' => 'A GRR realizará fiscalização preventiva de velocidade e condução perigosa em corredores viários e pontos de alto fluxo. A iniciativa possui caráter educativo e operacional, com orientações rápidas durante a abordagem, reforçando comportamento seguro e respeito às sinalizações. Veículos com conduta de risco poderão ser direcionados para checagem mais detalhada. A cooperação do cidadão durante a abordagem contribui para a fluidez do procedimento e para a segurança de todos.'
        ],
        [
            'id' => 'COM-003',
            'titulo' => 'Operação Especial — Combate a Corridas Ilegais',
            'categoria' => 'operacoes-especiais',
            'prioridade' => 'alta',
            'data' => '2026-01-13',
            'resumo' => 'Atuação contínua com pontos de bloqueio, cerco e patrulhamento em áreas críticas contra “rachas” e manobras de risco.',
            'conteudo' => 'Está em vigor operação contínua de combate a corridas ilegais e manobras de alto risco (rachas). A GRR atuará com pontos de bloqueio, cerco e patrulhamento em áreas críticas, priorizando prevenção de acidentes e proteção de terceiros. Em caso de dispersão do evento, o efetivo deverá manter padrão de rádio, identificação de envolvidos e relatório de dinâmica. A população é orientada a evitar proximidade de locais com aglomeração de veículos e a acionar a unidade por canal oficial quando identificar movimentação suspeita.'
        ],
        [
            'id' => 'COM-004',
            'titulo' => 'Orientações em Abordagens Policiais',
            'categoria' => 'orientacao-civil',
            'prioridade' => 'media',
            'data' => '2026-01-12',
            'resumo' => 'Boas práticas para abordagem segura: cooperação, comunicação clara e apresentação de documentos quando solicitado.',
            'conteudo' => 'Durante uma abordagem, mantenha o veículo imobilizado, aguarde instruções e apresente documentos quando solicitado. Evite movimentos bruscos, mantenha as mãos visíveis quando possível e responda de forma objetiva. Caso esteja armado legalmente, informe antes de qualquer ação. A abordagem é um procedimento de segurança e deve ocorrer com respeito mútuo. Atitudes que dificultem o procedimento (provocações, recusa injustificada, tentativa de fuga sem motivo) podem gerar encaminhamentos e registros administrativos.'
        ],
        [
            'id' => 'COM-005',
            'titulo' => 'Blitz Educativa — Uso de Capacete e Segurança em Motocicletas',
            'categoria' => 'blitz',
            'prioridade' => 'baixa',
            'data' => '2026-01-12',
            'resumo' => 'Ação educativa para reduzir mortes e quedas: capacete, prudência e condução responsável no RP.',
            'conteudo' => 'Será realizada blitz educativa voltada ao uso de capacete e práticas seguras na condução de motocicletas. A ação reforça que quedas e colisões são frequentes em vias de grande fluxo, e o uso de capacete reduz danos e melhora o RP. Durante a abordagem, a equipe fará orientações rápidas e verificações básicas. Condutores são orientados a evitar alta velocidade em perímetros urbanos e a respeitar as abordagens para manter a segurança e a coerência da simulação.'
        ],
        [
            'id' => 'COM-006',
            'titulo' => 'Fiscalização de Veículos Irregulares e Modificações Incompatíveis',
            'categoria' => 'operacoes',
            'prioridade' => 'media',
            'data' => '2026-01-11',
            'resumo' => 'Checagem de inconsistências documentais, suspeita de clonagem e modificações que impactam a segurança e o RP.',
            'conteudo' => 'A GRR intensificará a fiscalização de veículos com inconsistências documentais, suspeita de clonagem e modificações incompatíveis com o RP. O foco é reduzir fraudes, aumentar a segurança viária e garantir padrão institucional nas ações. Em caso de irregularidades, o procedimento seguirá com verificação detalhada, orientação ao condutor e registro. Motoristas devem manter documentação atualizada e evitar alterações que comprometam controle do veículo e a convivência nas vias públicas.'
        ],
        [
            'id' => 'COM-007',
            'titulo' => 'Omissão de Socorro — Aviso à Comunidade',
            'categoria' => 'orientacao-civil',
            'prioridade' => 'alta',
            'data' => '2026-01-11',
            'resumo' => 'Reforço de conduta RP: acionar emergência e prestar apoio inicial quando seguro; omissão compromete vidas.',
            'conteudo' => 'Em acidentes ou situações de emergência, a orientação é acionar o serviço competente e, quando seguro, prestar apoio inicial (sinalizar via, manter distância segura, informar localização). A omissão de socorro compromete a vida de terceiros e fere princípios básicos de convivência no RP. Quando houver indícios de abandono deliberado, comunicações poderão ser analisadas para providências administrativas e operacionais. Preserve vidas, preserve o RP.'
        ],
        [
            'id' => 'COM-008',
            'titulo' => 'Operação Integrada — Segurança em Eventos e Áreas de Grande Fluxo',
            'categoria' => 'operacoes-especiais',
            'prioridade' => 'media',
            'data' => '2026-01-10',
            'resumo' => 'Reforço de presença e patrulhamento para garantir ordem, fluidez do trânsito e prevenção de tumultos.',
            'conteudo' => 'Durante eventos públicos e áreas de grande fluxo, haverá reforço de presença e patrulhamento para garantir ordem e segurança. A operação prioriza prevenção de tumultos, fluidez de trânsito e suporte institucional. Em situações de risco, siga orientações dos agentes, evite aglomeração em pontos de conflito e mantenha comportamento compatível com o RP. A unidade atuará de forma proporcional e com registro das ocorrências relevantes.'
        ],
        [
            'id' => 'COM-009',
            'titulo' => 'Condutas Incompatíveis com o Roleplay — Alerta Operacional',
            'categoria' => 'informativo',
            'prioridade' => 'media',
            'data' => '2026-01-10',
            'resumo' => 'Aviso: atitudes anti-RP, fuga sem motivação e desrespeito em abordagem prejudicam a cidade e podem gerar medidas.',
            'conteudo' => 'Reforçamos que condutas anti-RP, fuga sem motivação plausível e desrespeito a abordagens prejudicam a cidade e podem gerar medidas administrativas. A GRR atuará com registro e análise de ocorrências, priorizando transparência, proporcionalidade e padrão institucional. A orientação é simples: coopere na abordagem, mantenha postura respeitosa e conduza de forma segura. Isso mantém o ambiente equilibrado e melhora a experiência para todos.'
        ],
    ];

    $totalComunicados = count($comunicados);
    $totalAlta = count(array_filter($comunicados, fn($c) => $c['prioridade'] === 'alta'));
    $totalMedia = count(array_filter($comunicados, fn($c) => $c['prioridade'] === 'media'));
    $totalBaixa = count(array_filter($comunicados, fn($c) => $c['prioridade'] === 'baixa'));
    $totalOperacoes = count(array_filter($comunicados, fn($c) => in_array($c['categoria'], ['operacoes', 'operacoes-especiais'])));
    $totalOrientacao = count(array_filter($comunicados, fn($c) => in_array($c['categoria'], ['orientacao-civil', 'informativo', 'blitz'])));
@endphp

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
                    Comunicados Oficiais • GRR
                </div>
            </div>
        </div>

        <div class="comm-shortcuts">
            <a href="{{ url('/') }}" class="comm-shortcut text-decoration-none">
                <span class="comm-shortcut-ico">🏠</span>
                <span>Página inicial</span>
            </a>

            <a href="#lista" class="comm-shortcut text-decoration-none">
                <span class="comm-shortcut-ico">📣</span>
                <span>Central de comunicados</span>
            </a>

            <a href="#filtros" class="comm-shortcut text-decoration-none">
                <span class="comm-shortcut-ico">🔎</span>
                <span>Buscar e filtrar</span>
            </a>
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

    {{-- HERO --}}
    <section class="portal-hero" style="margin-bottom: 8px;">
        <div class="portal-container">
            <div class="portal-hero-banner"
                 style="height: 320px; --portal-hero-image: url('{{ asset('images/imgs3.png') }}');">
                <div class="portal-hero-overlay">
                    <div class="portal-hero-badge">INFORMAÇÕES OFICIAIS • BRASIL CAPITAL</div>
                    <div class="portal-hero-h1" style="font-size: 42px;">Comunicados Oficiais</div>
                    <div class="portal-hero-p">
                        Avisos institucionais, diretrizes operacionais, alertas e atualizações do GRR.<br>
                        Conteúdo destinado ao ambiente de jogo e à organização pública da cidade.
                    </div>

                    <div class="comm-hero-meta">
                        <span class="comm-highlight-badge">📄 {{ $totalComunicados }} comunicados publicados</span>
                        <span class="comm-highlight-badge">🚨 {{ $totalAlta }} em prioridade alta</span>
                    </div>
                </div>
            </div>

            <div class="portal-dot"></div>
        </div>
    </section>

    <section class="portal-legal" id="lista">
        <div class="portal-container">

            <div class="portal-legal-head">
                <h1 class="portal-legal-title">Central de Comunicados</h1>

                <div class="portal-legal-alert">
                    <div class="portal-legal-alert-ico">ℹ️</div>
                    <div class="portal-legal-alert-text">
                        Consulte avisos por <b>tema</b>, <b>prioridade</b>, <b>ordenação</b> e <b>palavra-chave</b>. Os comunicados são voltados ao funcionamento institucional e à experiência RP.
                    </div>
                </div>
            </div>

            {{-- DESTAQUES --}}
            <div class="comm-highlight">
                <div class="comm-panel">
                    <div class="comm-panel-head">
                        <div class="comm-panel-kicker">Destaque Institucional</div>
                        <h2 class="comm-panel-title">Central oficial para orientações, operações e informativos</h2>
                    </div>
                    <div class="comm-panel-body">
                        <p class="comm-panel-text">
                            Esta área reúne os comunicados públicos da GRR em um ambiente único, com foco em clareza, organização e leitura rápida. A proposta da versão 3.0 é tornar o acesso mais intuitivo, profissional e compatível com a identidade institucional do portal.
                        </p>
                        <p class="comm-panel-text" style="margin-top:12px;">
                            Utilize a pesquisa para encontrar palavras específicas, filtre por categoria, refine por prioridade e ordene os comunicados conforme a necessidade operacional.
                        </p>
                    </div>
                </div>

                <div class="comm-panel">
                    <div class="comm-panel-head">
                        <div class="comm-panel-kicker">Prioridades Atuais</div>
                        <h2 class="comm-panel-title">Visão rápida do acervo publicado</h2>
                    </div>
                    <div class="comm-panel-body">
                        <div class="comm-priority-list">
                            <div class="comm-priority-item">
                                <div class="comm-priority-bullet">🚨</div>
                                <div>
                                    <strong>Alta prioridade</strong><br>
                                    <span>{{ $totalAlta }} comunicados com maior atenção operacional.</span>
                                </div>
                            </div>

                            <div class="comm-priority-item">
                                <div class="comm-priority-bullet">⚠️</div>
                                <div>
                                    <strong>Média prioridade</strong><br>
                                    <span>{{ $totalMedia }} comunicados de acompanhamento e orientação.</span>
                                </div>
                            </div>

                            <div class="comm-priority-item">
                                <div class="comm-priority-bullet">📘</div>
                                <div>
                                    <strong>Baixa prioridade</strong><br>
                                    <span>{{ $totalBaixa }} comunicados informativos e educativos.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATS --}}
            <div class="comm-stats">
                <div class="comm-stat">
                    <div class="comm-stat-label">Total publicado</div>
                    <div class="comm-stat-value">{{ $totalComunicados }}</div>
                    <div class="comm-stat-sub">Base completa da central pública.</div>
                </div>

                <div class="comm-stat">
                    <div class="comm-stat-label">Alta prioridade</div>
                    <div class="comm-stat-value">{{ $totalAlta }}</div>
                    <div class="comm-stat-sub">Itens com maior urgência institucional.</div>
                </div>

                <div class="comm-stat">
                    <div class="comm-stat-label">Em operações</div>
                    <div class="comm-stat-value">{{ $totalOperacoes }}</div>
                    <div class="comm-stat-sub">Comunicados voltados à atuação prática.</div>
                </div>

                <div class="comm-stat">
                    <div class="comm-stat-label">Orientação e apoio</div>
                    <div class="comm-stat-value">{{ $totalOrientacao }}</div>
                    <div class="comm-stat-sub">Conteúdos de orientação e prevenção.</div>
                </div>
            </div>

            {{-- TOOLBAR --}}
            <div class="comm-toolbar" id="filtros">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <label class="form-label" for="commSearch">Buscar comunicado</label>
                        <input id="commSearch" class="form-control" type="text" placeholder="Ex.: operação, blitz, corrida ilegal, fiscalização..." autocomplete="off">
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label class="form-label" for="commCat">Categoria</label>
                        <select id="commCat" class="form-select">
                            <option value="all">Todas</option>
                            <option value="operacoes">Operações</option>
                            <option value="blitz">Blitz</option>
                            <option value="operacoes-especiais">Operações especiais</option>
                            <option value="orientacao-civil">Orientação ao civil</option>
                            <option value="informativo">Informativo</option>
                        </select>
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label class="form-label" for="commPri">Prioridade</label>
                        <select id="commPri" class="form-select">
                            <option value="all">Todas</option>
                            <option value="alta">Alta</option>
                            <option value="media">Média</option>
                            <option value="baixa">Baixa</option>
                        </select>
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <label class="form-label" for="commSort">Ordenar por</label>
                        <select id="commSort" class="form-select">
                            <option value="recent">Mais recentes</option>
                            <option value="oldest">Mais antigos</option>
                            <option value="priority">Maior prioridade</option>
                            <option value="title">Título (A-Z)</option>
                        </select>
                    </div>
                </div>

                <div class="comm-toolbar-actions">
                    <small class="portal-muted">
                        Exibindo <b id="commCount">0</b> de <b id="commTotal">{{ $totalComunicados }}</b> comunicados
                    </small>

                    <button id="commClear" class="btn btn-outline-secondary btn-sm" type="button">
                        Limpar filtros
                    </button>
                </div>
            </div>

            {{-- LISTA --}}
            <div class="row g-3" id="commGrid">
                @foreach ($comunicados as $c)
                    @php
                        $badge = match($c['categoria']) {
                            'operacoes' => 'Operações',
                            'blitz' => 'Blitz',
                            'operacoes-especiais' => 'Operações Especiais',
                            'orientacao-civil' => 'Orientação ao Civil',
                            'informativo' => 'Informativo',
                            default => 'Geral'
                        };

                        $pri = match($c['prioridade']) {
                            'alta' => ['label' => 'Alta', 'cls' => 'text-bg-danger', 'order' => 1],
                            'media' => ['label' => 'Média', 'cls' => 'text-bg-warning', 'order' => 2],
                            'baixa' => ['label' => 'Baixa', 'cls' => 'text-bg-secondary', 'order' => 3],
                            default => ['label' => '—', 'cls' => 'text-bg-secondary', 'order' => 99],
                        };

                        $dateBr = \Carbon\Carbon::parse($c['data'])->format('d/m/Y');
                        $textIndex = $c['id'].' '.$c['titulo'].' '.$c['resumo'].' '.$badge.' '.$pri['label'].' Brasil Capital';
                        $readingTime = max(1, ceil(str_word_count(strip_tags($c['conteudo'])) / 180));
                    @endphp

                    <div class="col-md-6 col-xl-4 comm-item"
                         data-cat="{{ $c['categoria'] }}"
                         data-pri="{{ $c['prioridade'] }}"
                         data-title="{{ e($c['titulo']) }}"
                         data-text="{{ e($textIndex) }}"
                         data-date="{{ $c['data'] }}"
                         data-priority-order="{{ $pri['order'] }}">
                        <div class="portal-legal-card comm-card">
                            <div class="comm-card-head">
                                <div>
                                    <div class="comm-meta">{{ $c['id'] }} • {{ $dateBr }}</div>
                                    <h2 class="comm-card-title">{{ $c['titulo'] }}</h2>
                                </div>

                                <span class="badge {{ $pri['cls'] }}" style="margin-top:2px;">
                                    {{ $pri['label'] }}
                                </span>
                            </div>

                            <div class="comm-badges">
                                <span class="badge text-bg-primary">{{ $badge }}</span>
                                <span class="badge text-bg-light" style="border:1px solid #e6edf5;color:#0B2A4A;">Brasil Capital</span>
                            </div>

                            <p>{{ $c['resumo'] }}</p>

                            <div class="comm-footer-row">
                                <span class="comm-readtime">Leitura estimada: {{ $readingTime }} min</span>

                                <button
                                    class="btn btn-outline-primary"
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalComunicado"
                                    data-title="{{ e($c['titulo']) }}"
                                    data-body="{{ e($c['conteudo']) }}"
                                    data-meta="{{ e($c['id'].' • '.$badge.' • '.$dateBr.' • Prioridade: '.$pri['label']) }}"
                                    data-category="{{ e($badge) }}"
                                    data-priority="{{ e($pri['label']) }}"
                                    data-date="{{ e($dateBr) }}"
                                >
                                    Ver comunicado
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- EMPTY --}}
            <div id="commEmpty" class="portal-legal-card comm-empty" style="display:none;">
                <div class="comm-empty-ico">📭</div>
                <h2 style="margin-bottom:8px;">Nenhum comunicado encontrado</h2>
                <p style="margin:0; color:#64748b;">
                    Tente ajustar a busca, mudar a prioridade selecionada ou limpar os filtros atuais.
                </p>
            </div>

            {{-- PAGINAÇÃO --}}
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Paginação de comunicados">
                    <ul class="pagination mb-0" id="commPagination">
                        <li class="page-item">
                            <button class="page-link comm-page-btn" type="button" id="commPrev">Anterior</button>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link" id="commPageInfo">Página 1</span>
                        </li>
                        <li class="page-item">
                            <button class="page-link comm-page-btn" type="button" id="commNext">Próxima</button>
                        </li>
                    </ul>
                </nav>
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

{{-- MODAL --}}
<div class="modal fade" id="modalComunicado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="commTitle">Comunicado</h5>
                    <div class="text-muted" id="commMeta" style="font-size:12px;"></div>
                    <div class="modal-meta-badges">
                        <span class="badge text-bg-primary" id="commCategoryBadge">Categoria</span>
                        <span class="badge text-bg-secondary" id="commPriorityBadge">Prioridade</span>
                        <span class="badge text-bg-light" style="border:1px solid #e5e7eb; color:#0B2A4A;" id="commDateBadge">Data</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p id="commBody" style="margin:0; white-space: pre-wrap; line-height:1.75;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

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
    const q = document.getElementById('commSearch');
    const cat = document.getElementById('commCat');
    const pri = document.getElementById('commPri');
    const sort = document.getElementById('commSort');
    const clearBtn = document.getElementById('commClear');

    const grid = document.getElementById('commGrid');
    const items = Array.from(document.querySelectorAll('.comm-item'));
    const empty = document.getElementById('commEmpty');
    const countEl = document.getElementById('commCount');
    const totalEl = document.getElementById('commTotal');

    const prevBtn = document.getElementById('commPrev');
    const nextBtn = document.getElementById('commNext');
    const pageInfo = document.getElementById('commPageInfo');

    const perPage = 6;
    let currentPage = 1;
    let filteredItems = [...items];

    function normalize(str) {
        return (str || "")
            .toString()
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");
    }

    function sortItems(list, mode) {
        const priorityRank = { alta: 1, media: 2, baixa: 3 };

        list.sort((a, b) => {
            if (mode === 'oldest') {
                return new Date(a.dataset.date) - new Date(b.dataset.date);
            }

            if (mode === 'priority') {
                const pa = priorityRank[a.dataset.pri] || 99;
                const pb = priorityRank[b.dataset.pri] || 99;
                if (pa !== pb) return pa - pb;
                return new Date(b.dataset.date) - new Date(a.dataset.date);
            }

            if (mode === 'title') {
                return (a.dataset.title || '').localeCompare((b.dataset.title || ''), 'pt-BR');
            }

            return new Date(b.dataset.date) - new Date(a.dataset.date);
        });

        list.forEach(item => grid.appendChild(item));
    }

    function renderPage() {
        const totalPages = Math.max(1, Math.ceil(filteredItems.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;

        items.forEach(el => el.style.display = 'none');
        filteredItems.slice(start, end).forEach(el => el.style.display = '');

        if (countEl) countEl.textContent = filteredItems.length;
        if (totalEl) totalEl.textContent = items.length;

        if (empty) empty.style.display = filteredItems.length ? 'none' : '';
        if (pageInfo) pageInfo.textContent = `Página ${currentPage} de ${totalPages}`;

        if (prevBtn) prevBtn.disabled = currentPage <= 1 || !filteredItems.length;
        if (nextBtn) nextBtn.disabled = currentPage >= totalPages || !filteredItems.length;

        const paginationWrap = document.getElementById('commPagination')?.parentElement?.parentElement;
        if (paginationWrap) {
            paginationWrap.style.display = filteredItems.length ? '' : 'none';
        }
    }

    function apply() {
        const term = normalize(q?.value || '');
        const c = cat?.value || 'all';
        const p = pri?.value || 'all';
        const s = sort?.value || 'recent';

        filteredItems = items.filter(el => {
            const text = normalize(el.dataset.text || "");
            const title = normalize(el.dataset.title || "");
            const okText = !term || text.includes(term) || title.includes(term);
            const okCat = (c === 'all') || (el.dataset.cat === c);
            const okPri = (p === 'all') || (el.dataset.pri === p);
            return okText && okCat && okPri;
        });

        sortItems(filteredItems, s);
        currentPage = 1;
        renderPage();
    }

    function clearFilters() {
        if (q) q.value = '';
        if (cat) cat.value = 'all';
        if (pri) pri.value = 'all';
        if (sort) sort.value = 'recent';
        currentPage = 1;
        apply();
        q?.focus();
    }

    q?.addEventListener('input', apply);
    cat?.addEventListener('change', apply);
    pri?.addEventListener('change', apply);
    sort?.addEventListener('change', apply);
    clearBtn?.addEventListener('click', clearFilters);

    prevBtn?.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderPage();
            document.getElementById('lista')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    nextBtn?.addEventListener('click', () => {
        const totalPages = Math.max(1, Math.ceil(filteredItems.length / perPage));
        if (currentPage < totalPages) {
            currentPage++;
            renderPage();
            document.getElementById('lista')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    apply();

    const modal = document.getElementById('modalComunicado');
    if (modal) {
        modal.addEventListener('show.bs.modal', (ev) => {
            const btn = ev.relatedTarget;
            if (!btn) return;

            const title = btn.getAttribute('data-title') || 'Comunicado';
            const body = btn.getAttribute('data-body') || '';
            const meta = btn.getAttribute('data-meta') || '';
            const category = btn.getAttribute('data-category') || 'Categoria';
            const priority = btn.getAttribute('data-priority') || 'Prioridade';
            const date = btn.getAttribute('data-date') || 'Data';

            const t = document.getElementById('commTitle');
            const b = document.getElementById('commBody');
            const m = document.getElementById('commMeta');
            const c = document.getElementById('commCategoryBadge');
            const p = document.getElementById('commPriorityBadge');
            const d = document.getElementById('commDateBadge');

            if (t) t.textContent = title;
            if (b) b.textContent = body;
            if (m) m.textContent = meta;
            if (c) c.textContent = category;
            if (p) p.textContent = priority;
            if (d) d.textContent = date;
        });
    }
})();
</script>

</body>
</html>