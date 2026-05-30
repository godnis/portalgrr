@extends('layouts.app')

@section('content')
@php
    $statusMap = [
        'aberto' => [
            'label' => 'ABERTO',
            'class' => 'is-open',
            'desc'  => 'Registro recebido e aguardando análise inicial.',
        ],
        'em_analise' => [
            'label' => 'EM ANÁLISE',
            'class' => 'is-review',
            'desc'  => 'Manifestação em processo de verificação.',
        ],
        'resolvido' => [
            'label' => 'RESOLVIDO',
            'class' => 'is-done',
            'desc'  => 'Atendimento concluído com tratativa finalizada.',
        ],
        'arquivado' => [
            'label' => 'ARQUIVADO',
            'class' => 'is-archived',
            'desc'  => 'Registro encerrado e armazenado para consulta.',
        ],
    ];

    $tipoMap = [
        'Denúncia'    => 'is-denuncia',
        'Solicitação' => 'is-solicitacao',
        'Sugestão'    => 'is-sugestao',
        'Elogio'      => 'is-elogio',
    ];

    $statusInfo = $statusMap[$atendimento->status] ?? [
        'label' => strtoupper(str_replace('_', ' ', $atendimento->status)),
        'class' => 'is-archived',
        'desc'  => 'Status não identificado.',
    ];

    $tipoClass = $tipoMap[$atendimento->tipo] ?? 'is-solicitacao';

    $canViewTechnicalData = auth()->check() && (int) (auth()->user()->nivel ?? 0) >= 9;
@endphp

<style>
    /* =========================================================
       GRR 3.0 — ATENDIMENTO SHOW
       ========================================================= */
    .att-show-page{
        --att-bg: linear-gradient(180deg, rgba(248,250,252,1) 0%, rgba(241,245,249,1) 100%);
        --att-card: rgba(255,255,255,.88);
        --att-card-border: rgba(15,23,42,.08);
        --att-text: #0f172a;
        --att-muted: #64748b;
        --att-soft: #e2e8f0;
        --att-primary: #2563eb;
        --att-primary-2: #1d4ed8;
        --att-gold: #d4af37;
        --att-success: #16a34a;
        --att-warning: #f59e0b;
        --att-danger: #dc2626;
        --att-dark: #334155;
        --att-shadow: 0 20px 60px rgba(15,23,42,.08);
        --att-radius-xl: 24px;
        --att-radius-lg: 18px;
        --att-radius-md: 14px;
        color: var(--att-text);
    }

    body.theme-dark .att-show-page,
    html.theme-dark .att-show-page,
    [data-theme="dark"] .att-show-page,
    body.dark .att-show-page,
    html.dark .att-show-page{
        --att-bg: radial-gradient(circle at top left, rgba(37,99,235,.14), transparent 28%),
                  radial-gradient(circle at top right, rgba(212,175,55,.10), transparent 24%),
                  linear-gradient(180deg, #07101d 0%, #0b1220 100%);
        --att-card: rgba(2,6,23,.70);
        --att-card-border: rgba(148,163,184,.16);
        --att-text: #e2e8f0;
        --att-muted: rgba(226,232,240,.68);
        --att-soft: rgba(148,163,184,.18);
        --att-shadow: 0 25px 70px rgba(0,0,0,.35);
    }

    .att-show-page{
        background: var(--att-bg);
        border-radius: 28px;
        padding: 8px;
    }

    .att-show-wrap{
        max-width: 1280px;
        margin: 0 auto;
    }

    .att-show-kicker{
        display: inline-flex;
        align-items: center;
        gap: 8px;
        letter-spacing: .16em;
        text-transform: uppercase;
        font-weight: 900;
        font-size: .74rem;
        color: var(--att-primary);
        margin-bottom: 10px;
    }

    .att-show-kicker::before{
        content: "";
        width: 30px;
        height: 2px;
        border-radius: 999px;
        background: currentColor;
        opacity: .9;
    }

    .att-show-hero{
        position: relative;
        overflow: hidden;
        background: var(--att-card);
        border: 1px solid var(--att-card-border);
        box-shadow: var(--att-shadow);
        border-radius: var(--att-radius-xl);
        padding: 24px;
        margin-bottom: 20px;
        backdrop-filter: blur(14px);
    }

    .att-show-hero::before{
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 12% 18%, rgba(37,99,235,.12), transparent 26%),
            radial-gradient(circle at 88% 10%, rgba(212,175,55,.10), transparent 22%);
        pointer-events: none;
    }

    .att-show-hero__inner{
        position: relative;
        z-index: 1;
    }

    .att-show-title{
        font-size: clamp(1.45rem, 2.2vw, 2.2rem);
        font-weight: 900;
        line-height: 1.05;
        margin-bottom: 8px;
        color: var(--att-text);
    }

    .att-show-sub{
        color: var(--att-muted);
        font-size: .98rem;
        margin-bottom: 0;
    }

    .att-show-badges{
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
    }

    .att-badge{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: .76rem;
        font-weight: 900;
        line-height: 1;
        border: 1px solid transparent;
        text-transform: uppercase;
        letter-spacing: .04em;
        white-space: nowrap;
    }

    .att-badge--tipo.is-denuncia{
        background: rgba(220,38,38,.10);
        color: #b91c1c;
        border-color: rgba(220,38,38,.14);
    }
    .att-badge--tipo.is-solicitacao{
        background: rgba(37,99,235,.10);
        color: #1d4ed8;
        border-color: rgba(37,99,235,.14);
    }
    .att-badge--tipo.is-sugestao{
        background: rgba(245,158,11,.12);
        color: #b45309;
        border-color: rgba(245,158,11,.18);
    }
    .att-badge--tipo.is-elogio{
        background: rgba(22,163,74,.10);
        color: #15803d;
        border-color: rgba(22,163,74,.14);
    }

    .att-badge--status.is-open{
        background: rgba(220,38,38,.10);
        color: #b91c1c;
        border-color: rgba(220,38,38,.16);
    }
    .att-badge--status.is-review{
        background: rgba(245,158,11,.12);
        color: #b45309;
        border-color: rgba(245,158,11,.18);
    }
    .att-badge--status.is-done{
        background: rgba(22,163,74,.10);
        color: #15803d;
        border-color: rgba(22,163,74,.14);
    }
    .att-badge--status.is-archived{
        background: rgba(100,116,139,.14);
        color: #475569;
        border-color: rgba(100,116,139,.18);
    }

    body.theme-dark .att-badge--tipo.is-denuncia,
    html.theme-dark .att-badge--tipo.is-denuncia,
    [data-theme="dark"] .att-badge--tipo.is-denuncia,
    body.dark .att-badge--tipo.is-denuncia,
    html.dark .att-badge--tipo.is-denuncia{
        background: rgba(239,68,68,.14);
        color: #fecaca;
        border-color: rgba(239,68,68,.16);
    }
    body.theme-dark .att-badge--tipo.is-solicitacao,
    html.theme-dark .att-badge--tipo.is-solicitacao,
    [data-theme="dark"] .att-badge--tipo.is-solicitacao,
    body.dark .att-badge--tipo.is-solicitacao,
    html.dark .att-badge--tipo.is-solicitacao{
        background: rgba(59,130,246,.16);
        color: #bfdbfe;
        border-color: rgba(96,165,250,.18);
    }
    body.theme-dark .att-badge--tipo.is-sugestao,
    html.theme-dark .att-badge--tipo.is-sugestao,
    [data-theme="dark"] .att-badge--tipo.is-sugestao,
    body.dark .att-badge--tipo.is-sugestao,
    html.dark .att-badge--tipo.is-sugestao{
        background: rgba(245,158,11,.16);
        color: #fde68a;
        border-color: rgba(245,158,11,.18);
    }
    body.theme-dark .att-badge--tipo.is-elogio,
    html.theme-dark .att-badge--tipo.is-elogio,
    [data-theme="dark"] .att-badge--tipo.is-elogio,
    body.dark .att-badge--tipo.is-elogio,
    html.dark .att-badge--tipo.is-elogio{
        background: rgba(34,197,94,.16);
        color: #bbf7d0;
        border-color: rgba(34,197,94,.18);
    }

    body.theme-dark .att-badge--status.is-open,
    html.theme-dark .att-badge--status.is-open,
    [data-theme="dark"] .att-badge--status.is-open,
    body.dark .att-badge--status.is-open,
    html.dark .att-badge--status.is-open{
        background: rgba(239,68,68,.14);
        color: #fecaca;
        border-color: rgba(239,68,68,.16);
    }
    body.theme-dark .att-badge--status.is-review,
    html.theme-dark .att-badge--status.is-review,
    [data-theme="dark"] .att-badge--status.is-review,
    body.dark .att-badge--status.is-review,
    html.dark .att-badge--status.is-review{
        background: rgba(245,158,11,.16);
        color: #fde68a;
        border-color: rgba(245,158,11,.18);
    }
    body.theme-dark .att-badge--status.is-done,
    html.theme-dark .att-badge--status.is-done,
    [data-theme="dark"] .att-badge--status.is-done,
    body.dark .att-badge--status.is-done,
    html.dark .att-badge--status.is-done{
        background: rgba(34,197,94,.16);
        color: #bbf7d0;
        border-color: rgba(34,197,94,.18);
    }
    body.theme-dark .att-badge--status.is-archived,
    html.theme-dark .att-badge--status.is-archived,
    [data-theme="dark"] .att-badge--status.is-archived,
    body.dark .att-badge--status.is-archived,
    html.dark .att-badge--status.is-archived{
        background: rgba(148,163,184,.14);
        color: #cbd5e1;
        border-color: rgba(148,163,184,.18);
    }

    .att-show-panel{
        background: var(--att-card);
        border: 1px solid var(--att-card-border);
        box-shadow: var(--att-shadow);
        border-radius: var(--att-radius-xl);
        backdrop-filter: blur(14px);
    }

    .att-show-panel__head{
        padding: 20px 22px 0;
    }

    .att-show-panel__title{
        font-size: 1.02rem;
        font-weight: 900;
        margin: 0;
        color: var(--att-text);
    }

    .att-show-panel__desc{
        margin-top: 4px;
        color: var(--att-muted);
        font-size: .92rem;
    }

    .att-show-panel__body{
        padding: 20px 22px 22px;
    }

    .att-info-grid{
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 14px;
    }

    .att-info-card{
        grid-column: span 6;
        background: rgba(148,163,184,.06);
        border: 1px solid var(--att-card-border);
        border-radius: 18px;
        padding: 16px;
    }

    .att-info-card--full{
        grid-column: 1 / -1;
    }

    body.theme-dark .att-info-card,
    html.theme-dark .att-info-card,
    [data-theme="dark"] .att-info-card,
    body.dark .att-info-card,
    html.dark .att-info-card{
        background: rgba(15,23,42,.42);
    }

    .att-label{
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        font-weight: 900;
        color: var(--att-muted);
        margin-bottom: 8px;
    }

    .att-value{
        color: var(--att-text);
        font-weight: 700;
        line-height: 1.45;
        word-break: break-word;
    }

    .att-value--soft{
        color: var(--att-muted);
        font-weight: 600;
    }

    .att-message-box{
        background: rgba(15,23,42,.03);
        border: 1px solid var(--att-card-border);
        border-radius: 18px;
        padding: 18px;
        white-space: pre-wrap;
        line-height: 1.6;
        color: var(--att-text);
        min-height: 120px;
    }

    body.theme-dark .att-message-box,
    html.theme-dark .att-message-box,
    [data-theme="dark"] .att-message-box,
    body.dark .att-message-box,
    html.dark .att-message-box{
        background: rgba(15,23,42,.42);
    }

    .att-link-box{
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding: 14px 16px;
        border-radius: 16px;
        border: 1px dashed rgba(37,99,235,.24);
        background: rgba(37,99,235,.06);
    }

    body.theme-dark .att-link-box,
    html.theme-dark .att-link-box,
    [data-theme="dark"] .att-link-box,
    body.dark .att-link-box,
    html.dark .att-link-box{
        background: rgba(37,99,235,.10);
        border-color: rgba(96,165,250,.20);
    }

    .att-link{
        color: var(--att-primary);
        font-weight: 700;
        text-decoration: none;
        word-break: break-all;
    }

    .att-link:hover{
        text-decoration: underline;
    }

    .att-show-page .form-select{
        min-height: 48px;
        border-radius: 14px;
        border: 1px solid var(--att-card-border);
        background: rgba(255,255,255,.84);
        color: var(--att-text);
        box-shadow: none !important;
    }

    body.theme-dark .att-show-page .form-select,
    html.theme-dark .att-show-page .form-select,
    [data-theme="dark"] .att-show-page .form-select,
    body.dark .att-show-page .form-select,
    html.dark .att-show-page .form-select{
        background: rgba(15,23,42,.68);
        border-color: rgba(148,163,184,.22);
        color: #e2e8f0;
    }

    body.theme-dark .att-show-page .form-select option,
    html.theme-dark .att-show-page .form-select option,
    [data-theme="dark"] .att-show-page .form-select option,
    body.dark .att-show-page .form-select option,
    html.dark .att-show-page .form-select option{
        background: #0b1220;
        color: #e2e8f0;
    }

    .att-show-page .btn{
        min-height: 46px;
        border-radius: 14px;
        font-weight: 900;
        letter-spacing: .01em;
    }

    .att-show-page .btn-primary{
        background: linear-gradient(135deg, var(--att-primary), var(--att-primary-2));
        border: none;
        box-shadow: 0 14px 28px rgba(37,99,235,.24);
    }

    .att-show-page .btn-outline-secondary{
        border-color: rgba(148,163,184,.35);
    }

    body.theme-dark .att-show-page .btn-outline-secondary,
    html.theme-dark .att-show-page .btn-outline-secondary,
    [data-theme="dark"] .att-show-page .btn-outline-secondary,
    body.dark .att-show-page .btn-outline-secondary,
    html.dark .att-show-page .btn-outline-secondary{
        border-color: rgba(148,163,184,.25);
        color: #e2e8f0;
        background: rgba(15,23,42,.35);
    }

    .att-status-box{
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 18px;
        padding: 16px;
        border-radius: 18px;
        border: 1px solid var(--att-card-border);
        background: rgba(148,163,184,.06);
    }

    body.theme-dark .att-status-box,
    html.theme-dark .att-status-box,
    [data-theme="dark"] .att-status-box,
    body.dark .att-status-box,
    html.dark .att-status-box{
        background: rgba(15,23,42,.42);
    }

    .att-status-box__title{
        font-weight: 900;
        color: var(--att-text);
        margin-bottom: 4px;
    }

    .att-status-box__text{
        color: var(--att-muted);
        margin: 0;
        font-size: .93rem;
    }

    .att-show-page .alert{
        border-radius: 18px;
        border: 1px solid transparent;
        box-shadow: 0 10px 30px rgba(15,23,42,.06);
    }

    @media (max-width: 991.98px){
        .att-info-card{
            grid-column: span 12;
        }
    }

    @media (max-width: 767.98px){
        .att-show-page{
            border-radius: 22px;
            padding: 4px;
        }

        .att-show-hero{
            padding: 20px;
        }

        .att-show-panel__head,
        .att-show-panel__body{
            padding-left: 16px;
            padding-right: 16px;
        }
    }
</style>

<div class="container-fluid py-3 att-show-page">
    <div class="att-show-wrap">

        {{-- HERO --}}
        <div class="att-show-hero">
            <div class="att-show-hero__inner d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                <div>
                    <div class="att-show-kicker">GRR • PRF</div>
                    <h1 class="att-show-title">Atendimento #{{ $atendimento->id }}</h1>
                    <p class="att-show-sub">
                        {{ $atendimento->tipo }} • Enviado em {{ optional($atendimento->created_at)->format('d/m/Y H:i') }}
                    </p>

                    <div class="att-show-badges">
                        <span class="att-badge att-badge--tipo {{ $tipoClass }}">
                            {{ $atendimento->tipo }}
                        </span>

                        <span class="att-badge att-badge--status {{ $statusInfo['class'] }}">
                            {{ $statusInfo['label'] }}
                        </span>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('atendimentos.index') }}" class="btn btn-outline-secondary px-4">
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        {{-- ALERTAS --}}
        @if (session('success'))
            <div class="alert alert-success d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">{{ session('success') }}</div>
                <button class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <div class="fw-bold mb-2">Ops! Verifique os campos abaixo:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- DETALHES --}}
            <div class="col-12 col-xl-8">
                <div class="att-show-panel h-100">
                    <div class="att-show-panel__head">
                        <h2 class="att-show-panel__title">Detalhes do atendimento</h2>
                        <div class="att-show-panel__desc">
                            Informações completas da manifestação registrada no sistema.
                        </div>
                    </div>

                    <div class="att-show-panel__body">
                        <div class="att-info-grid">
                            <div class="att-info-card">
                                <div class="att-label">Assunto</div>
                                <div class="att-value">{{ $atendimento->assunto }}</div>
                            </div>

                            <div class="att-info-card">
                                <div class="att-label">Status atual</div>
                                <div class="att-value">
                                    <span class="att-badge att-badge--status {{ $statusInfo['class'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="att-info-card">
                                <div class="att-label">Nome informado</div>
                                <div class="att-value">{{ $atendimento->nome ?? '—' }}</div>
                            </div>

                            <div class="att-info-card">
                                <div class="att-label">Contato informado</div>
                                <div class="att-value">{{ $atendimento->contato ?? '—' }}</div>
                            </div>

                            <div class="att-info-card att-info-card--full">
                                <div class="att-label">Mensagem</div>
                                <div class="att-message-box">{{ $atendimento->mensagem }}</div>
                            </div>

                            <div class="att-info-card att-info-card--full">
                                <div class="att-label">Prova / link enviado</div>

                                @if($atendimento->prova_url)
                                    <div class="att-link-box">
                                        <a href="{{ $atendimento->prova_url }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="att-link">
                                            {{ $atendimento->prova_url }}
                                        </a>
                                    </div>
                                @else
                                    <div class="att-value att-value--soft">Nenhum link informado.</div>
                                @endif
                            </div>

                            @if($canViewTechnicalData)
                                <div class="att-info-card att-info-card--full">
                                    <div class="att-label">Dados técnicos</div>
                                    <div class="att-value att-value--soft">
                                        IP: {{ $atendimento->ip ?? '—' }}
                                        <span class="mx-2">•</span>
                                        UA: {{ $atendimento->user_agent ?? '—' }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATUS --}}
            <div class="col-12 col-xl-4">
                <div class="att-show-panel">
                    <div class="att-show-panel__head">
                        <h2 class="att-show-panel__title">Atualizar status</h2>
                        <div class="att-show-panel__desc">
                            Gerencie a situação atual deste atendimento.
                        </div>
                    </div>

                    <div class="att-show-panel__body">
                        <div class="att-status-box">
                            <div>
                                <div class="att-status-box__title">Situação atual</div>
                                <p class="att-status-box__text">{{ $statusInfo['desc'] }}</p>
                            </div>

                            <span class="att-badge att-badge--status {{ $statusInfo['class'] }}">
                                {{ $statusInfo['label'] }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('atendimentos.status', $atendimento) }}" class="row g-3">
                            @csrf
                            @method('PATCH')

                            <div class="col-12">
                                <label class="att-label">Novo status</label>
                                <select name="status" class="form-select" required>
                                    <option value="aberto" @selected($atendimento->status === 'aberto')>Aberto</option>
                                    <option value="em_analise" @selected($atendimento->status === 'em_analise')>Em análise</option>
                                    <option value="resolvido" @selected($atendimento->status === 'resolvido')>Resolvido</option>
                                    <option value="arquivado" @selected($atendimento->status === 'arquivado')>Arquivado</option>
                                </select>
                            </div>

                            <div class="col-12 d-grid">
                                <button class="btn btn-primary">
                                    Salvar atualização
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection