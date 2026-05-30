@extends('layouts.app')

@section('content')
@php
    $status = (string) ($solicitacao->status ?? 'pendente');

    $badgeClass = match($status){
        'pendente'  => 'is-pending',
        'aprovado'  => 'is-approved',
        'reprovado' => 'is-rejected',
        default     => 'is-neutral',
    };

    $statusText = match($status){
        'pendente'  => 'Pendente',
        'aprovado'  => 'Aprovado',
        'reprovado' => 'Reprovado',
        default     => ucfirst($status),
    };

    $iniciais = mb_strtoupper(
        mb_substr((string)($solicitacao->nome ?? ''), 0, 1) .
        mb_substr((string)($solicitacao->sobrenome ?? ''), 0, 1)
    );
    $iniciais = trim($iniciais) !== '' ? $iniciais : 'SA';

    $criadoEm   = $solicitacao->created_at?->format('d/m/Y H:i') ?? '—';
    $atualizado = $solicitacao->updated_at?->format('d/m/Y H:i') ?? '—';
    $decididoEm = $solicitacao->decidido_em?->format('d/m/Y H:i') ?? '—';

    $nomeCompleto = trim(($solicitacao->nome ?? '') . ' ' . ($solicitacao->sobrenome ?? ''));
@endphp

<div class="container py-4 solicshow-page">

    {{-- HERO --}}
    <div class="solicshow-hero mb-4">
        <div class="solicshow-hero__glow solicshow-hero__glow--blue"></div>
        <div class="solicshow-hero__glow solicshow-hero__glow--gold"></div>

        <div class="solicshow-hero__inner">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="solicshow-avatar">
                        {{ $iniciais }}
                    </div>

                    <div>
                        <div class="solicshow-kicker">DETALHAMENTO DA SOLICITAÇÃO</div>

                        <h2 class="solicshow-title mb-2">
                            {{ $nomeCompleto ?: 'Solicitação sem nome' }}
                        </h2>

                        <div class="d-flex flex-wrap gap-2">
                            <span class="solicshow-badge {{ $badgeClass }}">
                                {{ $statusText }}
                            </span>

                            <span class="solicshow-chip">
                                ID #{{ $solicitacao->id }}
                            </span>

                            @if(!empty($solicitacao->rg))
                                <span class="solicshow-chip font-monospace">
                                    RG {{ $solicitacao->rg }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.solicitacoes.index') }}" class="btn btn-outline-secondary">
                        Voltar
                    </a>

                    @if($status === 'pendente' && Route::has('admin.solicitacoes.edit'))
                        <a href="{{ route('admin.solicitacoes.edit', $solicitacao->id) }}" class="btn btn-primary">
                            Editar solicitação
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if ($errors->any())
        <div class="alert alert-danger solicshow-alert mb-4">
            <div class="solicshow-alert__icon">!</div>
            <div>
                <div class="fw-bold mb-1">Não foi possível concluir a ação</div>
                <div>{{ $errors->first() }}</div>
            </div>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success solicshow-alert mb-4">
            <div class="solicshow-alert__icon">✓</div>
            <div>
                <div class="fw-bold mb-1">Operação realizada com sucesso</div>
                <div>{{ session('status') }}</div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        {{-- COLUNA PRINCIPAL --}}
        <div class="col-12 col-xl-8">

            {{-- DADOS PRINCIPAIS --}}
            <div class="card border-0 solicshow-card mb-4">
                <div class="card-header solicshow-card__head">
                    <div>
                        <h5 class="mb-1 fw-bold">Informações principais</h5>
                        <div class="text-muted small">
                            Dados enviados pelo solicitante para análise administrativa.
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="solicshow-field">
                                <span class="solicshow-field__label">Nome</span>
                                <span class="solicshow-field__value">{{ $solicitacao->nome ?: '—' }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="solicshow-field">
                                <span class="solicshow-field__label">Sobrenome</span>
                                <span class="solicshow-field__value">{{ $solicitacao->sobrenome ?: '—' }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="solicshow-field">
                                <span class="solicshow-field__label">RG</span>
                                <span class="solicshow-field__value font-monospace">{{ $solicitacao->rg ?: '—' }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="solicshow-field">
                                <span class="solicshow-field__label">Status atual</span>
                                <span class="solicshow-field__value">{{ $statusText }}</span>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="solicshow-field">
                                <span class="solicshow-field__label">E-mail</span>
                                <span class="solicshow-field__value font-monospace">{{ $solicitacao->email ?: '—' }}</span>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="solicshow-field">
                                <span class="solicshow-field__label">Discord</span>
                                <span class="solicshow-field__value font-monospace">{{ $solicitacao->discord ?: '—' }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="solicshow-field">
                                <span class="solicshow-field__label">IP de envio</span>
                                <span class="solicshow-field__value font-monospace">{{ $solicitacao->ip ?: '—' }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="solicshow-field">
                                <span class="solicshow-field__label">Identificador</span>
                                <span class="solicshow-field__value">#{{ $solicitacao->id }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MOTIVO / OBSERVAÇÃO --}}
            @if($status === 'reprovado' || !empty($solicitacao->motivo))
                <div class="card border-0 solicshow-card mb-4">
                    <div class="card-header solicshow-card__head">
                        <div>
                            <h5 class="mb-1 fw-bold">Motivo registrado</h5>
                            <div class="text-muted small">
                                Histórico da decisão aplicada à solicitação.
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="solicshow-note {{ $status === 'reprovado' ? 'is-danger' : '' }}">
                            {{ $solicitacao->motivo ?: 'Nenhum motivo informado.' }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- RESUMO TÉCNICO --}}
            <div class="card border-0 solicshow-card">
                <div class="card-header solicshow-card__head">
                    <div>
                        <h5 class="mb-1 fw-bold">Resumo técnico</h5>
                        <div class="text-muted small">
                            Informações rápidas para conferência operacional.
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="solicshow-summary">
                        <div class="solicshow-summary__item">
                            <div class="solicshow-summary__label">Nome completo</div>
                            <div class="solicshow-summary__value">{{ $nomeCompleto ?: '—' }}</div>
                        </div>

                        <div class="solicshow-summary__item">
                            <div class="solicshow-summary__label">Contato</div>
                            <div class="solicshow-summary__value font-monospace">{{ $solicitacao->email ?: '—' }}</div>
                        </div>

                        <div class="solicshow-summary__item">
                            <div class="solicshow-summary__label">Discord</div>
                            <div class="solicshow-summary__value font-monospace">{{ $solicitacao->discord ?: '—' }}</div>
                        </div>

                        <div class="solicshow-summary__item">
                            <div class="solicshow-summary__label">Documento</div>
                            <div class="solicshow-summary__value font-monospace">{{ $solicitacao->rg ?: '—' }}</div>
                        </div>

                        <div class="solicshow-summary__item">
                            <div class="solicshow-summary__label">Situação</div>
                            <div class="solicshow-summary__value">{{ $statusText }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUNA LATERAL --}}
        <div class="col-12 col-xl-4">

            {{-- LINHA DO TEMPO --}}
            <div class="card border-0 solicshow-card mb-4">
                <div class="card-header solicshow-card__head">
                    <div>
                        <h5 class="mb-1 fw-bold">Linha do tempo</h5>
                        <div class="text-muted small">
                            Histórico temporal da solicitação.
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="solicshow-timeline">
                        <div class="solicshow-timeline__item">
                            <span class="solicshow-timeline__label">Criado em</span>
                            <span class="solicshow-timeline__value">{{ $criadoEm }}</span>
                        </div>

                        <div class="solicshow-timeline__item">
                            <span class="solicshow-timeline__label">Atualizado em</span>
                            <span class="solicshow-timeline__value">{{ $atualizado }}</span>
                        </div>

                        <div class="solicshow-timeline__item">
                            <span class="solicshow-timeline__label">Decidido em</span>
                            <span class="solicshow-timeline__value">{{ $decididoEm }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- AÇÕES --}}
            @if($status === 'pendente')
                <div class="card border-0 solicshow-card">
                    <div class="card-header solicshow-card__head">
                        <div>
                            <h5 class="mb-1 fw-bold">Ações rápidas</h5>
                            <div class="text-muted small">
                                Decisão administrativa da solicitação atual.
                            </div>
                        </div>
                    </div>

                    <div class="card-body d-grid gap-2">
                        <form method="POST" action="{{ route('admin.solicitacoes.aprovar', $solicitacao->id) }}">
                            @csrf
                            <button class="btn btn-success w-100" onclick="return confirm('Aprovar e criar usuário?')">
                                Aprovar solicitação
                            </button>
                        </form>

                        @if(Route::has('admin.solicitacoes.edit'))
                            <a href="{{ route('admin.solicitacoes.edit', $solicitacao->id) }}" class="btn btn-outline-secondary">
                                Editar dados
                            </a>
                        @endif

                        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reprovarModal">
                            Reprovar solicitação
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL REPROVAR --}}
    @if($status === 'pendente')
        <div class="modal fade solicshow-modal" id="reprovarModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold mb-1">Reprovar solicitação</h5>
                            <div class="text-muted small">
                                Registre um motivo para manter histórico interno mais claro.
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form method="POST" action="{{ route('admin.solicitacoes.reprovar', $solicitacao->id) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="solicshow-userbox mb-3">
                                <div class="fw-bold">{{ $nomeCompleto ?: 'Solicitante' }}</div>
                                <div class="text-muted small">
                                    RG <span class="font-monospace">{{ $solicitacao->rg ?: '—' }}</span>
                                    •
                                    <span class="font-monospace">{{ $solicitacao->email ?: '—' }}</span>
                                </div>
                            </div>

                            <label class="form-label fw-bold">
                                Motivo <span class="text-muted fw-normal"></span>
                            </label>

                            <textarea
                                name="motivo"
                                class="form-control"
                                rows="4"
                                placeholder="Ex.: divergência de dados, RG inconsistente, cadastro incompleto..."
                                required
                            ></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Confirmar reprovação</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>

<style>
/* =========================================================
   GRR 3.0 — SHOW SOLICITAÇÃO
========================================================= */
.solicshow-page{
    --ss-text:#0f172a;
    --ss-muted:#64748b;
    --ss-line:rgba(15,23,42,.08);
    --ss-line-soft:rgba(15,23,42,.06);
    --ss-soft:rgba(15,23,42,.03);
    --ss-primary:#1d4ed8;
    --ss-primary-2:#2563eb;
    --ss-gold:#d4af37;
}

/* HERO */
.solicshow-hero{
    position:relative;
    overflow:hidden;
    border-radius:24px;
    border:1px solid rgba(15,23,42,.08);
    background:
        radial-gradient(circle at top left, rgba(37,99,235,.12), transparent 34%),
        radial-gradient(circle at top right, rgba(212,175,55,.12), transparent 28%),
        linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    box-shadow:0 20px 60px rgba(15,23,42,.08);
}
.solicshow-hero__inner{
    position:relative;
    z-index:2;
    padding:26px 28px;
}
.solicshow-hero__glow{
    position:absolute;
    border-radius:999px;
    filter:blur(40px);
    opacity:.55;
    pointer-events:none;
}
.solicshow-hero__glow--blue{
    width:240px;
    height:240px;
    top:-90px;
    left:-60px;
    background:rgba(37,99,235,.18);
}
.solicshow-hero__glow--gold{
    width:200px;
    height:200px;
    top:-50px;
    right:-40px;
    background:rgba(212,175,55,.20);
}
.solicshow-kicker{
    font-size:11px;
    font-weight:900;
    letter-spacing:.16em;
    text-transform:uppercase;
    color:var(--ss-primary);
    margin-bottom:8px;
}
.solicshow-title{
    font-size:clamp(1.45rem, 2vw, 2rem);
    font-weight:900;
    letter-spacing:-.02em;
    color:var(--ss-text);
}
.solicshow-avatar{
    width:64px;
    height:64px;
    border-radius:18px;
    display:grid;
    place-items:center;
    flex-shrink:0;
    font-size:1rem;
    font-weight:900;
    color:#fff;
    background:linear-gradient(135deg,#2563eb 0%, #1e3a8a 100%);
    box-shadow:inset 0 1px 0 rgba(255,255,255,.18);
}
.solicshow-chip,
.solicshow-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:34px;
    padding:0 12px;
    border-radius:999px;
    font-size:11.5px;
    font-weight:900;
    letter-spacing:.05em;
    text-transform:uppercase;
}
.solicshow-chip{
    background:rgba(15,23,42,.05);
    color:#334155;
    border:1px solid rgba(15,23,42,.06);
}
.solicshow-badge{
    border:1px solid transparent;
}
.solicshow-badge.is-pending{
    background:rgba(245,159,0,.14);
    color:#9a6700;
    border-color:rgba(245,159,0,.20);
}
.solicshow-badge.is-approved{
    background:rgba(25,135,84,.13);
    color:#0f7a49;
    border-color:rgba(25,135,84,.20);
}
.solicshow-badge.is-rejected{
    background:rgba(220,53,69,.12);
    color:#b42318;
    border-color:rgba(220,53,69,.18);
}
.solicshow-badge.is-neutral{
    background:rgba(100,116,139,.12);
    color:#475569;
    border-color:rgba(100,116,139,.18);
}

/* ALERT */
.solicshow-alert{
    display:flex;
    align-items:flex-start;
    gap:12px;
    border-radius:18px;
    border:1px solid transparent;
    padding:14px 16px;
}
.solicshow-alert__icon{
    width:34px;
    height:34px;
    border-radius:50%;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    flex-shrink:0;
    background:rgba(255,255,255,.22);
}

/* CARD */
.solicshow-card{
    border-radius:22px;
    overflow:hidden;
    border:1px solid rgba(15,23,42,.08) !important;
    background:#fff;
    box-shadow:0 16px 40px rgba(15,23,42,.06);
}
.solicshow-card__head{
    padding:18px 22px;
    background:linear-gradient(180deg, rgba(248,250,252,.96), rgba(255,255,255,1));
    border-bottom:1px solid rgba(15,23,42,.08);
}

/* FIELDS */
.solicshow-field{
    height:100%;
    padding:16px;
    border-radius:16px;
    background:rgba(15,23,42,.03);
    border:1px solid rgba(15,23,42,.08);
}
.solicshow-field__label{
    display:block;
    font-size:11px;
    font-weight:900;
    letter-spacing:.08em;
    text-transform:uppercase;
    color:#64748b;
    margin-bottom:8px;
}
.solicshow-field__value{
    display:block;
    font-size:1rem;
    font-weight:800;
    color:#0f172a;
    word-break:break-word;
}

/* NOTE */
.solicshow-note{
    padding:16px;
    border-radius:16px;
    background:rgba(15,23,42,.03);
    border:1px solid rgba(15,23,42,.08);
    color:#334155;
    line-height:1.65;
}
.solicshow-note.is-danger{
    background:rgba(220,53,69,.06);
    border-color:rgba(220,53,69,.14);
    color:#7f1d1d;
}

/* SUMMARY */
.solicshow-summary{
    display:grid;
    gap:12px;
}
.solicshow-summary__item{
    padding:14px 16px;
    border-radius:16px;
    background:rgba(15,23,42,.03);
    border:1px solid rgba(15,23,42,.08);
}
.solicshow-summary__label{
    font-size:11px;
    font-weight:900;
    letter-spacing:.08em;
    text-transform:uppercase;
    color:#64748b;
    margin-bottom:6px;
}
.solicshow-summary__value{
    font-size:.98rem;
    font-weight:800;
    color:#0f172a;
    word-break:break-word;
}

/* TIMELINE */
.solicshow-timeline{
    display:grid;
    gap:14px;
}
.solicshow-timeline__item{
    padding:14px 16px;
    border-radius:16px;
    background:rgba(15,23,42,.03);
    border:1px solid rgba(15,23,42,.08);
}
.solicshow-timeline__label{
    display:block;
    font-size:11px;
    font-weight:900;
    letter-spacing:.08em;
    text-transform:uppercase;
    color:#64748b;
    margin-bottom:6px;
}
.solicshow-timeline__value{
    display:block;
    font-weight:800;
    color:#0f172a;
}

/* MODAL */
.solicshow-modal .modal-content{
    border-radius:22px;
    border:1px solid rgba(15,23,42,.10);
    overflow:hidden;
    box-shadow:0 28px 80px rgba(15,23,42,.24);
}
.solicshow-modal .modal-header{
    border-bottom:1px solid rgba(15,23,42,.08);
    padding:18px 20px;
    background:
        radial-gradient(circle at top right, rgba(37,99,235,.06), transparent 24%),
        linear-gradient(180deg, rgba(248,250,252,.95), rgba(255,255,255,1));
}
.solicshow-modal .modal-body{
    padding:20px;
}
.solicshow-modal .modal-footer{
    border-top:1px solid rgba(15,23,42,.08);
    padding:16px 20px;
    background:rgba(248,250,252,.62);
}
.solicshow-modal textarea.form-control{
    min-height:120px;
    border-radius:14px;
    border-color:rgba(15,23,42,.12);
    box-shadow:none !important;
}
.solicshow-modal textarea.form-control:focus{
    border-color:rgba(37,99,235,.45);
    box-shadow:0 0 0 .25rem rgba(37,99,235,.10) !important;
}
.solicshow-userbox{
    padding:12px 14px;
    border-radius:14px;
    background:rgba(15,23,42,.03);
    border:1px solid rgba(15,23,42,.08);
}

/* DARK */
html[data-theme="dark"] .solicshow-hero{
    border-color:rgba(255,255,255,.10);
    background:
        radial-gradient(circle at top left, rgba(37,99,235,.18), transparent 34%),
        radial-gradient(circle at top right, rgba(212,175,55,.15), transparent 28%),
        linear-gradient(135deg, rgba(15,20,28,.98) 0%, rgba(17,24,39,.96) 100%);
    box-shadow:0 22px 60px rgba(0,0,0,.40);
}
html[data-theme="dark"] .solicshow-title{
    color:rgba(241,245,249,.96);
}
html[data-theme="dark"] .solicshow-chip{
    background:rgba(148,163,184,.10);
    border-color:rgba(148,163,184,.12);
    color:rgba(241,245,249,.88);
}
html[data-theme="dark"] .solicshow-card{
    background:rgba(15,20,28,.96);
    border-color:rgba(255,255,255,.10) !important;
    box-shadow:0 18px 44px rgba(0,0,0,.34);
}
html[data-theme="dark"] .solicshow-card__head{
    background:linear-gradient(180deg, rgba(20,27,38,.96), rgba(15,20,28,.98));
    border-bottom-color:rgba(148,163,184,.14);
}
html[data-theme="dark"] .solicshow-card .text-muted{
    color:rgba(226,232,240,.62) !important;
}
html[data-theme="dark"] .solicshow-field,
html[data-theme="dark"] .solicshow-summary__item,
html[data-theme="dark"] .solicshow-timeline__item,
html[data-theme="dark"] .solicshow-userbox{
    background:rgba(148,163,184,.06);
    border-color:rgba(148,163,184,.12);
}
html[data-theme="dark"] .solicshow-field__label,
html[data-theme="dark"] .solicshow-summary__label,
html[data-theme="dark"] .solicshow-timeline__label{
    color:rgba(226,232,240,.62);
}
html[data-theme="dark"] .solicshow-field__value,
html[data-theme="dark"] .solicshow-summary__value,
html[data-theme="dark"] .solicshow-timeline__value{
    color:rgba(241,245,249,.94);
}
html[data-theme="dark"] .solicshow-note{
    background:rgba(148,163,184,.06);
    border-color:rgba(148,163,184,.12);
    color:rgba(226,232,240,.84);
}
html[data-theme="dark"] .solicshow-note.is-danger{
    background:rgba(220,53,69,.10);
    border-color:rgba(220,53,69,.18);
    color:rgba(254,226,226,.92);
}
html[data-theme="dark"] .solicshow-modal .modal-content{
    background:rgba(15,20,28,.98) !important;
    color:rgba(241,245,249,.94) !important;
    border-color:rgba(255,255,255,.10);
}
html[data-theme="dark"] .solicshow-modal .modal-header{
    background:
        radial-gradient(circle at top right, rgba(37,99,235,.10), transparent 24%),
        linear-gradient(180deg, rgba(20,27,38,.96), rgba(15,20,28,.98));
    border-bottom-color:rgba(148,163,184,.14);
}
html[data-theme="dark"] .solicshow-modal .modal-footer{
    background:rgba(11,17,26,.78);
    border-top-color:rgba(148,163,184,.14);
}
html[data-theme="dark"] .solicshow-modal .text-muted{
    color:rgba(226,232,240,.62) !important;
}
html[data-theme="dark"] .solicshow-modal textarea.form-control{
    background:rgba(11,17,26,.96) !important;
    border-color:rgba(255,255,255,.12) !important;
    color:rgba(241,245,249,.94) !important;
}
html[data-theme="dark"] .solicshow-modal textarea.form-control::placeholder{
    color:rgba(226,232,240,.38) !important;
}
html[data-theme="dark"] .alert-success{
    background:rgba(25,135,84,.14) !important;
    color:rgba(241,245,249,.94) !important;
    border-color:rgba(25,135,84,.22) !important;
}
html[data-theme="dark"] .alert-danger{
    background:rgba(220,53,69,.14) !important;
    color:rgba(241,245,249,.94) !important;
    border-color:rgba(220,53,69,.22) !important;
}

/* RESPONSIVO */
@media (max-width: 991.98px){
    .solicshow-hero__inner,
    .solicshow-card__head{
        padding:18px;
    }
}
@media (max-width: 767.98px){
    .solicshow-title{
        font-size:1.35rem;
    }
    .solicshow-avatar{
        width:56px;
        height:56px;
        border-radius:16px;
    }
}
</style>
@endsection