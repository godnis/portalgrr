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

    $nomeCompleto = trim(($solicitacao->nome ?? '') . ' ' . ($solicitacao->sobrenome ?? ''));
@endphp

<div class="container py-4 solicedit-page">

    {{-- HERO --}}
    <div class="solicedit-hero mb-4">
        <div class="solicedit-hero__glow solicedit-hero__glow--blue"></div>
        <div class="solicedit-hero__glow solicedit-hero__glow--gold"></div>

        <div class="solicedit-hero__inner">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="solicedit-avatar">
                        {{ $iniciais }}
                    </div>

                    <div>
                        <div class="solicedit-kicker">EDIÇÃO DA SOLICITAÇÃO</div>

                        <h2 class="solicedit-title mb-2">
                            {{ $nomeCompleto ?: 'Solicitação sem nome' }}
                        </h2>

                        <div class="d-flex flex-wrap gap-2">
                            <span class="solicedit-badge {{ $badgeClass }}">
                                {{ $statusText }}
                            </span>

                            <span class="solicedit-chip">
                                ID #{{ $solicitacao->id }}
                            </span>

                            @if(!empty($solicitacao->rg))
                                <span class="solicedit-chip font-monospace">
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

                    @if(Route::has('admin.solicitacoes.show'))
                        <a href="{{ route('admin.solicitacoes.show', $solicitacao->id) }}" class="btn btn-outline-dark">
                            Ver detalhes
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if ($errors->any())
        <div class="alert alert-danger solicedit-alert mb-4">
            <div class="solicedit-alert__icon">!</div>
            <div>
                <div class="fw-bold mb-1">Não foi possível salvar as alterações</div>
                <div>{{ $errors->first() }}</div>
            </div>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success solicedit-alert mb-4">
            <div class="solicedit-alert__icon">✓</div>
            <div>
                <div class="fw-bold mb-1">Alterações salvas com sucesso</div>
                <div>{{ session('status') }}</div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        {{-- FORMULÁRIO --}}
        <div class="col-12 col-xl-8">
            <div class="card border-0 solicedit-card">
                <div class="card-header solicedit-card__head">
                    <div>
                        <h5 class="mb-1 fw-bold">Editar dados da solicitação</h5>
                        <div class="text-muted small">
                            Ajuste os dados antes da aprovação para manter o cadastro correto no sistema.
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.solicitacoes.update', $solicitacao->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label solicedit-label">Nome</label>
                                <input
                                    type="text"
                                    name="nome"
                                    class="form-control solicedit-input @error('nome') is-invalid @enderror"
                                    value="{{ old('nome', $solicitacao->nome) }}"
                                    maxlength="80"
                                    required
                                >
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label solicedit-label">Sobrenome</label>
                                <input
                                    type="text"
                                    name="sobrenome"
                                    class="form-control solicedit-input @error('sobrenome') is-invalid @enderror"
                                    value="{{ old('sobrenome', $solicitacao->sobrenome) }}"
                                    maxlength="80"
                                    required
                                >
                                @error('sobrenome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label solicedit-label">RG</label>
                                <input
                                    type="text"
                                    name="rg"
                                    class="form-control solicedit-input font-monospace @error('rg') is-invalid @enderror"
                                    value="{{ old('rg', $solicitacao->rg) }}"
                                    maxlength="30"
                                    required
                                >
                                @error('rg')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Pode colar com máscara; o backend pode normalizar automaticamente.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label solicedit-label">E-mail</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control solicedit-input font-monospace @error('email') is-invalid @enderror"
                                    value="{{ old('email', $solicitacao->email) }}"
                                    maxlength="150"
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Ajuste apenas se sua regra permitir edição manual do e-mail.
                                </div>
                            </div>
                        </div>

                        <div class="solicedit-note mt-4">
                            <div class="fw-bold mb-1">Observação importante</div>
                            <div class="small mb-0">
                                O e-mail institucional pode ser recalculado pelo sistema a partir do nome e sobrenome, conforme sua lógica no backend.
                            </div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.solicitacoes.index') }}" class="btn btn-light">
                                Cancelar
                            </a>

                            @if(Route::has('admin.solicitacoes.show'))
                                <a href="{{ route('admin.solicitacoes.show', $solicitacao->id) }}" class="btn btn-outline-secondary">
                                    Ver detalhes
                                </a>
                            @endif

                            <button type="submit" class="btn btn-primary">
                                Salvar alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- LATERAL --}}
        <div class="col-12 col-xl-4">
            <div class="card border-0 solicedit-card mb-4">
                <div class="card-header solicedit-card__head">
                    <div>
                        <h5 class="mb-1 fw-bold">Resumo atual</h5>
                        <div class="text-muted small">
                            Informações principais antes da alteração.
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="solicedit-summary">
                        <div class="solicedit-summary__item">
                            <div class="solicedit-summary__label">Nome completo</div>
                            <div class="solicedit-summary__value">{{ $nomeCompleto ?: '—' }}</div>
                        </div>

                        <div class="solicedit-summary__item">
                            <div class="solicedit-summary__label">RG</div>
                            <div class="solicedit-summary__value font-monospace">{{ $solicitacao->rg ?: '—' }}</div>
                        </div>

                        <div class="solicedit-summary__item">
                            <div class="solicedit-summary__label">E-mail</div>
                            <div class="solicedit-summary__value font-monospace">{{ $solicitacao->email ?: '—' }}</div>
                        </div>

                        <div class="solicedit-summary__item">
                            <div class="solicedit-summary__label">Discord</div>
                            <div class="solicedit-summary__value font-monospace">{{ $solicitacao->discord ?: '—' }}</div>
                        </div>

                        <div class="solicedit-summary__item">
                            <div class="solicedit-summary__label">Status</div>
                            <div class="solicedit-summary__value">{{ $statusText }}</div>
                        </div>

                        <div class="solicedit-summary__item">
                            <div class="solicedit-summary__label">IP de envio</div>
                            <div class="solicedit-summary__value font-monospace">{{ $solicitacao->ip ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($status === 'pendente')
                <div class="card border-0 solicedit-card">
                    <div class="card-header solicedit-card__head">
                        <div>
                            <h5 class="mb-1 fw-bold">Ações rápidas</h5>
                            <div class="text-muted small">
                                Após corrigir os dados, você pode decidir a solicitação.
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
        <div class="modal fade solicedit-modal" id="reprovarModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold mb-1">Reprovar solicitação</h5>
                            <div class="text-muted small">
                                Registre um motivo para manter o histórico interno organizado.
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form method="POST" action="{{ route('admin.solicitacoes.reprovar', $solicitacao->id) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="solicedit-userbox mb-3">
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
   GRR 3.0 — EDIT SOLICITAÇÃO
========================================================= */
.solicedit-page{
    --se-text:#0f172a;
    --se-muted:#64748b;
    --se-line:rgba(15,23,42,.08);
    --se-soft:rgba(15,23,42,.03);
    --se-primary:#1d4ed8;
    --se-gold:#d4af37;
}

/* HERO */
.solicedit-hero{
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
.solicedit-hero__inner{
    position:relative;
    z-index:2;
    padding:26px 28px;
}
.solicedit-hero__glow{
    position:absolute;
    border-radius:999px;
    filter:blur(40px);
    opacity:.55;
    pointer-events:none;
}
.solicedit-hero__glow--blue{
    width:240px;
    height:240px;
    top:-90px;
    left:-60px;
    background:rgba(37,99,235,.18);
}
.solicedit-hero__glow--gold{
    width:200px;
    height:200px;
    top:-50px;
    right:-40px;
    background:rgba(212,175,55,.20);
}
.solicedit-kicker{
    font-size:11px;
    font-weight:900;
    letter-spacing:.16em;
    text-transform:uppercase;
    color:var(--se-primary);
    margin-bottom:8px;
}
.solicedit-title{
    font-size:clamp(1.45rem, 2vw, 2rem);
    font-weight:900;
    letter-spacing:-.02em;
    color:var(--se-text);
}
.solicedit-avatar{
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
.solicedit-chip,
.solicedit-badge{
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
.solicedit-chip{
    background:rgba(15,23,42,.05);
    color:#334155;
    border:1px solid rgba(15,23,42,.06);
}
.solicedit-badge{
    border:1px solid transparent;
}
.solicedit-badge.is-pending{
    background:rgba(245,159,0,.14);
    color:#9a6700;
    border-color:rgba(245,159,0,.20);
}
.solicedit-badge.is-approved{
    background:rgba(25,135,84,.13);
    color:#0f7a49;
    border-color:rgba(25,135,84,.20);
}
.solicedit-badge.is-rejected{
    background:rgba(220,53,69,.12);
    color:#b42318;
    border-color:rgba(220,53,69,.18);
}
.solicedit-badge.is-neutral{
    background:rgba(100,116,139,.12);
    color:#475569;
    border-color:rgba(100,116,139,.18);
}

/* ALERT */
.solicedit-alert{
    display:flex;
    align-items:flex-start;
    gap:12px;
    border-radius:18px;
    border:1px solid transparent;
    padding:14px 16px;
}
.solicedit-alert__icon{
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
.solicedit-card{
    border-radius:22px;
    overflow:hidden;
    border:1px solid rgba(15,23,42,.08) !important;
    background:#fff;
    box-shadow:0 16px 40px rgba(15,23,42,.06);
}
.solicedit-card__head{
    padding:18px 22px;
    background:linear-gradient(180deg, rgba(248,250,252,.96), rgba(255,255,255,1));
    border-bottom:1px solid rgba(15,23,42,.08);
}

/* FORM */
.solicedit-label{
    font-size:12px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:#475569;
    margin-bottom:8px;
}
.solicedit-input{
    min-height:48px;
    border-radius:14px;
    border:1px solid rgba(15,23,42,.10);
    background:#fff;
    box-shadow:none !important;
}
.solicedit-input:focus{
    border-color:rgba(37,99,235,.45);
    box-shadow:0 0 0 .25rem rgba(37,99,235,.10) !important;
}
.solicedit-note{
    padding:16px 18px;
    border-radius:16px;
    background:rgba(15,23,42,.03);
    border:1px solid rgba(15,23,42,.08);
    color:#475569;
}

/* SUMMARY */
.solicedit-summary{
    display:grid;
    gap:12px;
}
.solicedit-summary__item{
    padding:14px 16px;
    border-radius:16px;
    background:rgba(15,23,42,.03);
    border:1px solid rgba(15,23,42,.08);
}
.solicedit-summary__label{
    font-size:11px;
    font-weight:900;
    letter-spacing:.08em;
    text-transform:uppercase;
    color:#64748b;
    margin-bottom:6px;
}
.solicedit-summary__value{
    font-size:.98rem;
    font-weight:800;
    color:#0f172a;
    word-break:break-word;
}

/* MODAL */
.solicedit-modal .modal-content{
    border-radius:22px;
    border:1px solid rgba(15,23,42,.10);
    overflow:hidden;
    box-shadow:0 28px 80px rgba(15,23,42,.24);
}
.solicedit-modal .modal-header{
    border-bottom:1px solid rgba(15,23,42,.08);
    padding:18px 20px;
    background:
        radial-gradient(circle at top right, rgba(37,99,235,.06), transparent 24%),
        linear-gradient(180deg, rgba(248,250,252,.95), rgba(255,255,255,1));
}
.solicedit-modal .modal-body{
    padding:20px;
}
.solicedit-modal .modal-footer{
    border-top:1px solid rgba(15,23,42,.08);
    padding:16px 20px;
    background:rgba(248,250,252,.62);
}
.solicedit-modal textarea.form-control{
    min-height:120px;
    border-radius:14px;
    border-color:rgba(15,23,42,.12);
    box-shadow:none !important;
}
.solicedit-modal textarea.form-control:focus{
    border-color:rgba(37,99,235,.45);
    box-shadow:0 0 0 .25rem rgba(37,99,235,.10) !important;
}
.solicedit-userbox{
    padding:12px 14px;
    border-radius:14px;
    background:rgba(15,23,42,.03);
    border:1px solid rgba(15,23,42,.08);
}

/* DARK */
html[data-theme="dark"] .solicedit-hero{
    border-color:rgba(255,255,255,.10);
    background:
        radial-gradient(circle at top left, rgba(37,99,235,.18), transparent 34%),
        radial-gradient(circle at top right, rgba(212,175,55,.15), transparent 28%),
        linear-gradient(135deg, rgba(15,20,28,.98) 0%, rgba(17,24,39,.96) 100%);
    box-shadow:0 22px 60px rgba(0,0,0,.40);
}
html[data-theme="dark"] .solicedit-title{
    color:rgba(241,245,249,.96);
}
html[data-theme="dark"] .solicedit-chip{
    background:rgba(148,163,184,.10);
    border-color:rgba(148,163,184,.12);
    color:rgba(241,245,249,.88);
}
html[data-theme="dark"] .solicedit-card{
    background:rgba(15,20,28,.96);
    border-color:rgba(255,255,255,.10) !important;
    box-shadow:0 18px 44px rgba(0,0,0,.34);
}
html[data-theme="dark"] .solicedit-card__head{
    background:linear-gradient(180deg, rgba(20,27,38,.96), rgba(15,20,28,.98));
    border-bottom-color:rgba(148,163,184,.14);
}
html[data-theme="dark"] .solicedit-card .text-muted{
    color:rgba(226,232,240,.62) !important;
}
html[data-theme="dark"] .solicedit-label{
    color:rgba(226,232,240,.72);
}
html[data-theme="dark"] .solicedit-input{
    background:rgba(11,17,26,.96) !important;
    border-color:rgba(255,255,255,.12) !important;
    color:rgba(241,245,249,.94) !important;
}
html[data-theme="dark"] .solicedit-input::placeholder{
    color:rgba(226,232,240,.38) !important;
}
html[data-theme="dark"] .solicedit-note,
html[data-theme="dark"] .solicedit-summary__item,
html[data-theme="dark"] .solicedit-userbox{
    background:rgba(148,163,184,.06);
    border-color:rgba(148,163,184,.12);
}
html[data-theme="dark"] .solicedit-note{
    color:rgba(226,232,240,.78);
}
html[data-theme="dark"] .solicedit-summary__label{
    color:rgba(226,232,240,.62);
}
html[data-theme="dark"] .solicedit-summary__value{
    color:rgba(241,245,249,.94);
}
html[data-theme="dark"] .solicedit-modal .modal-content{
    background:rgba(15,20,28,.98) !important;
    color:rgba(241,245,249,.94) !important;
    border-color:rgba(255,255,255,.10);
}
html[data-theme="dark"] .solicedit-modal .modal-header{
    background:
        radial-gradient(circle at top right, rgba(37,99,235,.10), transparent 24%),
        linear-gradient(180deg, rgba(20,27,38,.96), rgba(15,20,28,.98));
    border-bottom-color:rgba(148,163,184,.14);
}
html[data-theme="dark"] .solicedit-modal .modal-footer{
    background:rgba(11,17,26,.78);
    border-top-color:rgba(148,163,184,.14);
}
html[data-theme="dark"] .solicedit-modal .text-muted{
    color:rgba(226,232,240,.62) !important;
}
html[data-theme="dark"] .solicedit-modal textarea.form-control{
    background:rgba(11,17,26,.96) !important;
    border-color:rgba(255,255,255,.12) !important;
    color:rgba(241,245,249,.94) !important;
}
html[data-theme="dark"] .solicedit-modal textarea.form-control::placeholder{
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
    .solicedit-hero__inner,
    .solicedit-card__head{
        padding:18px;
    }
}
@media (max-width: 767.98px){
    .solicedit-title{
        font-size:1.35rem;
    }
    .solicedit-avatar{
        width:56px;
        height:56px;
        border-radius:16px;
    }
}
</style>
@endsection